<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi QRIS.
 */
class QrisTransactionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'pagination', 'form_validation']);
        $this->load->model(['Model_user', 'Mutation_model', 'Chanel', 'Qris', 'QRISDynamic', 'QRISRecurring']);
        is_logged_in();

        global $internalUrlHit, $externalUrlHit;
        $this->internalUrlHit = $internalUrlHit;
        $this->externalUrlHit = $externalUrlHit;
    }

    private function _parseRawJson()
    {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
                    $_POST[$k] = $v;
                }
            }
        }
    }

    private function _isApiRequest()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        return $this->input->is_ajax_request()
            || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
            || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
            || strpos($accept, 'json') !== false
            || strtolower($this->input->method()) === 'post'
            || $is_swagger;
    }

    public function qris()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetqris(false);
        }

        $data['title'] = 'QRIS';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_qris_name'            => 'search_name_qris',
            'search_qris_date1'           => 'search_date_qris',
            'search_qris_date2'           => 'search_date_qris_to',
            'search_qris_date_settlement' => 'search_date_qris_settlement',
            'search_qris_invoice_no'      => 'search_invoice_no',
            'search_qris_rrn'             => 'search_rrn',
            'search_qris_transid'         => 'search_transactionid_ht',
        ];
        $get_fallback = [
            'search_qris_name'            => 'merchant',
            'search_qris_date1'           => 'date_from',
            'search_qris_date2'           => 'date_to',
            'search_qris_date_settlement' => 'settlement',
            'search_qris_invoice_no'      => 'invoice',
            'search_qris_rrn'             => 'rrn',
            'search_qris_transid'         => 'transid',
        ];

        $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
        if ($merchant_post !== NULL) $_POST['search_name_qris'] = $merchant_post;
        $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->get('date_from'));
        if ($date_from_post !== NULL) $_POST['search_date_qris'] = $date_from_post;
        $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
        if ($date_to_post !== NULL) $_POST['search_date_qris_to'] = $date_to_post;

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('rrn');
        if ($active_search) $this->session->set_userdata('last_dt_search_qris', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_qris');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_qris', 'search_qris_invoice_no', 'search_qris_transid', 'search_qris_rrn']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_qris_invoice_no', $dtSearch);
                    $this->session->set_userdata('last_dt_search_qris', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_qris'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->post('search_date_qris'));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_qris_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'   => $merchant_val ?: null,
                    'date_from'  => $date_from_val ?: null,
                    'date_to'    => $date_to_val ?: null,
                    'settlement' => $this->input->post('settlement') ?: ($this->input->post('search_date_qris_settlement') ?: null),
                    'rrn'        => $this->input->post('rrn') ?: ($this->input->post('search_rrn') ?: null),
                    'invoice'    => $this->input->post('invoice') ?: ($this->input->post('search_invoice_no') ?: null),
                    'transid'    => $this->input->post('transid') ?: ($this->input->post('search_transactionid_ht') ?: null)
                ];
                $out = $this->Qris->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'QRIS AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['qriss'] = [];
        $data['start'] = 0;
        $data['pagination'] = '';
        $data['merchants'] = $this->Qris->get_merchant();
        $this->load->view('qris/list', $data);
    }

    public function resetqris($redirect = true)
    {
        $this->session->unset_userdata(['search_qris_name', 'search_qris_date1', 'search_qris_date2', 'search_qris_date_settlement', 'search_qris_invoice_no', 'search_qris_rrn', 'search_qris_transid', 'last_dt_search_qris']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'QRIS filters reset successfully.']));
            return;
        }
        if ($redirect) redirect('finance/qris');
    }

    public function qris_detail($id = NULL)
    {
        if (!$id) redirect('finance/qris');

        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['title'] = 'Detail QRIS';
        $data['qris_data'] = $this->Qris->qris_detail($id);

        $displayId = '#' . $id;
        if (!empty($data['qris_data'])) {
            $row = $data['qris_data'][0];
            $displayId = '#' . $row['c_invoiceNo'];
            $data['external_log'] = $this->Qris->get_external_payment_log($id, $row['ref_cashinExternalId']);
            $data['create_log'] = null;
            if ($row['c_type'] == 'Dynamic' && $row['dynamic_create_log_id']) {
                $data['create_log'] = $this->QRISDynamic->getDataQrisDynamicChannelExternal($row['ref_cashinExternalId'], $row['dynamic_create_log_id'], $row['ref_cashinDynamicQrisMpmId']);
            } elseif ($row['c_type'] == 'Recurring' && $row['recurring_create_log_id']) {
                $data['create_log'] = $this->QRISRecurring->getDataQrisRecurringChannelExternal($row['ref_cashinExternalId'], $row['recurring_create_log_id'], $row['ref_cashinRecurringQrisMpmId']);
            }
        }
        $data['breadcrumb_replace'] = [$id => $displayId];

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'QRIS detail retrieved successfully',
                'data' => [
                    'qris_data' => $data['qris_data'],
                    'external_log' => $data['external_log'] ?? null,
                    'create_log' => $data['create_log'] ?? null
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }

        $this->load->view('qris/detail_qris', $data);
    }

    public function download_qris()
    {
        $this->_parseRawJson();
        $is_api = $this->_isApiRequest();

        $search_date_qris = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_qris_date1') ?: $this->session->userdata('search_qris_date1')));
        $search_name_qris = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_qris_name') ?: $this->session->userdata('search_qris_name')));
        $search_date_qris_to = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_qris_date2') ?: $this->session->userdata('search_qris_date2')));
        $search_date_qris_settlement = $this->input->post('settlement') ?: ($this->input->get('search_qris_date_settlement') ?: $this->session->userdata('search_qris_date_settlement'));

        $user = $this->Model_user->view_user()->row_array();
        $adminID = $user['id'] ?? 1;
        $additionalFilter = $search_name_qris . '|' . $search_date_qris . '|' . $search_date_qris_settlement;
        
        $data = [
            'ref_adminId'        => $adminID,
            'c_datetime'         => date('Y-m-d H:i:s'),
            'c_additionalFilter' => $additionalFilter,
            'c_type'             => 'Qris',
            'c_status'           => 'Pending',
            'c_filename'         => '',
        ];

        if ($is_api) {
            $inserted = $this->db->insert('admin_download', $data);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => (bool)$inserted,
                'message' => $inserted ? 'QRIS download request submitted.' : 'Failed to submit download request.',
                'data'    => $inserted ? ['download_id' => $this->db->insert_id(), 'type' => 'Qris', 'filter' => $additionalFilter] : null
            ]));
        }

        if (empty($search_name_qris) && empty($search_date_qris) && empty($search_date_qris_settlement)) {
            $this->session->set_flashdata('error_message', 'Please fill search fields before downloading.');
            redirect('finance/qris');
            return;
        }

        if ($this->db->insert('admin_download', $data)) {
            $this->session->set_flashdata('success', 'Your request is being processed. Go to Download Report.');
        } else {
            $this->session->set_flashdata('error', 'Failed request download');
        }
        redirect('finance/qris');
    }

    public function qris_dynamic_list()
    {
        $this->_parseRawJson();
        try {
            $out = $this->QRISDynamic->get_datatables_handler();
            $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
        } catch (\Throwable $e) {
            log_message('error', 'QRIS Dynamic List AJAX error: ' . $e->getMessage());
            $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
        }
    }

    public function qris_dynamic()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetqris_dynamic(false);
        }

        $data['title'] = 'QRIS Dynamic';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_qrisdynamic_name'     => 'search_name_qd',
            'search_qrisdynamic_date1'    => 'search_date_qd',
            'search_qrisdynamic_date2'    => 'search_date_qd_to',
            'search_qrisdynamic_status'   => 'search_status_transaction_qd',
            'search_qrisdynamic_reff'     => 'search_reff_label',
            'search_qrisdynamic_transid'  => 'search_transid_qd',
            'search_qrisdynamic_channel'  => 'search_channel_qrisdynamic',
            'search_qrisdynamic_external' => 'search_external_qrisdynamic',
        ];
        $get_fallback = [
            'search_qrisdynamic_name'     => 'merchant',
            'search_qrisdynamic_transid'  => 'transid',
            'search_qrisdynamic_channel'  => 'channel',
            'search_qrisdynamic_external' => 'external',
        ];

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_qrisdynamic', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_qrisdynamic');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_qrisdynamic', 'search_qrisdynamic_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_qrisdynamic_transid', $dtSearch);
                    $this->session->set_userdata('last_dt_search_qrisdynamic', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_qd'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_qd')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_qd_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'         => $merchant_val ?: null,
                    'date'             => $date_from_val ?: null,
                    'date_to'          => $date_to_val ?: null,
                    'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_qd') ?: null),
                    'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_qd') ?: null),
                    'reff'             => $this->input->post('reff') ?: ($this->input->post('search_reff_label') ?: null),
                    'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_qrisdynamic') ?: null),
                    'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_qrisdynamic') ?: null)
                ];
                $out = $this->QRISDynamic->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'QRIS Dynamic AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['merchants'] = $this->QRISDynamic->get_merchant();
        $data['search_reff_label'] = $this->session->userdata('search_qrisdynamic_reff');
        $data['internal_channels'] = $this->Qris->get_internal_channels();
        $data['external_channels'] = $this->Qris->get_external_channels();
        $this->load->view('qris/qrisdynamic', $data);
    }

    public function resetqris_dynamic($redirect = true)
    {
        $this->session->unset_userdata(['search_qrisdynamic_name', 'search_qrisdynamic_date1', 'search_qrisdynamic_date2', 'search_qrisdynamic_status', 'search_qrisdynamic_reff', 'search_qrisdynamic_transid', 'search_qrisdynamic_channel', 'search_qrisdynamic_external', 'last_dt_search_qrisdynamic']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Dynamic QRIS search filters reset.']));
            return;
        }
        if ($redirect) redirect('qris/dynamic');
    }

    public function qris_recurring()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetqris_recurring(false);
        }

        $data['title'] = 'QRIS Recurring';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_qrisrecurring_name'        => 'search_name_qr',
            'search_qrisrecurring_date1'       => 'search_date_qr',
            'search_qrisrecurring_date2'       => 'search_date_qr_to',
            'search_qrisrecurring_submerchant' => 'search_submerchant_qr',
            'search_qrisrecurring_status'      => 'search_status_transaction_qr',
            'search_qrisrecurring_transid'     => 'search_transid_qr',
            'search_qrisrecurring_channel'     => 'search_channel_qrisrecurring',
            'search_qrisrecurring_external'    => 'search_external_qrisrecurring',
        ];
        $get_fallback = [
            'search_qrisrecurring_name'        => 'merchant',
            'search_qrisrecurring_transid'     => 'transid',
            'search_qrisrecurring_channel'     => 'channel',
            'search_qrisrecurring_external'    => 'external',
        ];

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_qrisrecurring', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_qrisrecurring');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_qrisrecurring', 'search_qrisrecurring_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_qrisrecurring_transid', $dtSearch);
                    $this->session->set_userdata('last_dt_search_qrisrecurring', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_qr'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_qr')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_qr_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'         => $merchant_val ?: null,
                    'date'             => $date_from_val ?: null,
                    'date_to'          => $date_to_val ?: null,
                    'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_qr') ?: null),
                    'submerchant'      => $this->input->post('submerchant') ?: ($this->input->post('search_submerchant_qr') ?: null),
                    'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_qr') ?: null),
                    'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_qrisrecurring') ?: null),
                    'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_qrisrecurring') ?: null)
                ];
                $out = $this->QRISRecurring->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'QRIS Recurring AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['merchants'] = $this->QRISRecurring->get_merchant();
        $data['internal_channels'] = $this->Qris->get_internal_channels();
        $data['external_channels'] = $this->Qris->get_external_channels();
        $this->load->view('qris/qrisrecurring', $data);
    }

    public function resetqris_recurring($redirect = true)
    {
        $this->session->unset_userdata(['search_qrisrecurring_name', 'search_qrisrecurring_date1', 'search_qrisrecurring_date2', 'search_qrisrecurring_submerchant', 'search_qrisrecurring_status', 'search_qrisrecurring_transid', 'search_qrisrecurring_channel', 'search_qrisrecurring_external', 'last_dt_search_qrisrecurring']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Recurring QRIS filters reset.']));
            return;
        }
        if ($redirect) redirect('qris/recurring');
    }

    public function SendnotifikasiQRIS($ref_cashinPaymentQrisMpmId = NULL, $refMerchantId = NULL)
    {
        $is_api = $this->_isApiRequest();
        if (!$ref_cashinPaymentQrisMpmId) {
            if ($is_api) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
                return;
            }
            $this->session->set_flashdata('error', 'Transaction ID not found.');
            redirect('finance/qris');
            return;
        }

        $internalRequestBody = [
            "msgType" => "consumer_notification_qris",
            "msgInfo" => ["ref_cashinPaymentQrisMpmId" => $ref_cashinPaymentQrisMpmId, "merchantId" => $refMerchantId]
        ];

        $internalUrlHit = ($this->internalUrlHit ?: 'http://127.0.0.1/gatewayservice') . "/Rabbitmq/createQueue";
        $internalCurl = curl_init();
        curl_setopt_array($internalCurl, [
            CURLOPT_URL => $internalUrlHit,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);
        $curlRes = curl_exec($internalCurl);
        if (curl_errno($internalCurl)) {
            log_message('error', 'QRIS Resend Notif cURL Error: ' . curl_error($internalCurl));
        }
        curl_close($internalCurl);

        if ($is_api) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'QRIS notification queued successfully.']));
            return;
        }
        $this->session->set_flashdata('success', 'Notification has been resent.');
        redirect('finance/qris');
    }

    public function getDetailQrisDynamicChannelExternal()
    {
        if (!$this->session->userdata('c_email')) redirect('auth');
        $this->_parseRawJson();

        $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
        $parentId = $this->input->post('parentId') ?: 1;
        $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate') ?: 1;

        $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
        $this->output->set_content_type('application/json')->set_output(json_encode($detailData ?: []));
    }

    public function getDetailQrisRecurringChannelExternal()
    {
        if (!$this->session->userdata('c_email')) redirect('auth');
        $this->_parseRawJson();

        $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
        $parentId = $this->input->post('parentId') ?: 1;
        $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate') ?: 1;

        $detailData = $this->QRISRecurring->getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
        $this->output->set_content_type('application/json')->set_output(json_encode($detailData ?: []));
    }
}
