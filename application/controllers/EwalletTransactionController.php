<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi E-Wallet.
 */
class EwalletTransactionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'pagination', 'form_validation']);
        $this->load->model(['Model_user', 'Mutation_model', 'Chanel', 'Ewallet', 'EwalletDynamic']);
        is_logged_in();

        global $internalUrlHit, $externalUrlHit;
        $this->internalUrlHit = $internalUrlHit;
        $this->externalUrlHit = $externalUrlHit;
    }

    private function _parseRawJson()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) {
            foreach ($raw as $k => $v) {
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

    public function ewallet()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetewallet(false);
        }

        $data['title'] = 'Ewallet';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_ewallet_name'            => 'search_name_ewallet',
            'search_ewallet_date1'           => 'search_date_ewallet',
            'search_ewallet_date2'           => 'search_date_ewallet_to',
            'search_ewallet_date_settlement' => 'search_date_ewallet_settlement',
            'search_ewallet_invoice_no'      => 'search_invoice_no',
            'search_ewallet_transid'         => 'search_transid_ewallet',
            'search_ewallet_channel'         => 'search_channel_ewallet',
        ];
        $get_fallback = [
            'search_ewallet_name'            => 'merchant',
            'search_ewallet_date1'           => 'date_from',
            'search_ewallet_date2'           => 'date_to',
            'search_ewallet_date_settlement' => 'settlement',
            'search_ewallet_invoice_no'      => 'invoice',
            'search_ewallet_transid'         => 'transid',
            'search_ewallet_channel'         => 'channel',
        ];

        $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
        if ($merchant_post !== NULL) $_POST['search_name_ewallet'] = $merchant_post;
        $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->get('date_from')));
        if ($date_from_post !== NULL) $_POST['search_date_ewallet'] = $date_from_post;
        $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
        if ($date_to_post !== NULL) $_POST['search_date_ewallet_to'] = $date_to_post;

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_ewallet', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_ewallet');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_ewallet', 'search_ewallet_invoice_no', 'search_ewallet_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_ewallet_invoice_no', $dtSearch);
                    $this->session->set_userdata('last_dt_search_ewallet', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_ewallet'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_ewallet')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_ewallet_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'   => $merchant_val ?: null,
                    'date_from'  => $date_from_val ?: null,
                    'date_to'    => $date_to_val ?: null,
                    'settlement' => $this->input->post('settlement') ?: ($this->input->post('search_date_ewallet_settlement') ?: null),
                    'channel'    => $this->input->post('channel') ?: ($this->input->post('search_channel_ewallet') ?: null),
                    'invoice'    => $this->input->post('invoice') ?: ($this->input->post('search_invoice_no') ?: null),
                    'transid'    => $this->input->post('transid') ?: ($this->input->post('search_transid_ewallet') ?: null)
                ];
                $out = $this->Ewallet->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'Ewallet AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['ewallets'] = [];
        $data['start'] = 0;
        $data['pagination'] = '';
        $data['merchants'] = $this->Ewallet->get_merchant();
        $this->load->view('ewallet/list', $data);
    }

    public function resetewallet($redirect = true)
    {
        $this->session->unset_userdata(['search_ewallet_name', 'search_ewallet_date1', 'search_ewallet_date2', 'search_ewallet_date_settlement', 'search_ewallet_invoice_no', 'search_ewallet_transid', 'search_ewallet_channel', 'last_dt_search_ewallet']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'E-Wallet search filters reset.']));
            return;
        }
        if ($redirect) redirect('finance/e-wallet');
    }

    public function ewallet_detail($id = NULL)
    {
        if (!$id) redirect('finance/e-wallet');

        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['title'] = 'Detail E-Wallet';
        $data['ewallet_data'] = $this->Ewallet->ewallet_detail($id);

        $displayId = '#' . $id;
        if (!empty($data['ewallet_data'])) {
            $row = $data['ewallet_data'][0];
            $displayId = '#' . $row['c_invoiceNo'];
            $data['external_log'] = $this->Ewallet->get_external_payment_log($id, $row['ref_cashinExternalId']);
            $data['create_log'] = null;
            if ($row['c_type'] == 'Dynamic' && $row['dynamic_create_log_id']) {
                $data['create_log'] = $this->EwalletDynamic->getDataEwalletDynamicChannelExternal($row['ref_cashinExternalId'], $row['dynamic_create_log_id'], $row['ref_cashinDynamicEwalletId']);
            }
        }
        $data['breadcrumb_replace'] = [$id => $displayId];

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'E-Wallet detail retrieved successfully',
                'data' => [
                    'ewallet_data' => $data['ewallet_data'],
                    'external_log' => $data['external_log'] ?? null,
                    'create_log'   => $data['create_log'] ?? null
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }

        $this->load->view('ewallet/detail_ewallet', $data);
    }

    public function download_ewallet()
    {
        $this->_parseRawJson();
        $is_api = $this->_isApiRequest();

        $search_date_ewallet = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_ewallet_date1') ?: $this->session->userdata('search_ewallet_date1')));
        $search_name_ewallet = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_ewallet_name') ?: $this->session->userdata('search_ewallet_name')));
        $search_date_ewallet_to = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_ewallet_date2') ?: $this->session->userdata('search_ewallet_date2')));
        $search_date_ewallet_settlement = $this->input->post('settlement') ?: ($this->input->get('search_ewallet_date_settlement') ?: $this->session->userdata('search_ewallet_date_settlement'));

        $user = $this->Model_user->view_user()->row_array();
        $adminID = $user['id'] ?? 1;
        $additionalFilter = $search_name_ewallet . '|' . $search_date_ewallet . '|' . $search_date_ewallet_settlement;
        
        $data = [
            'ref_adminId'        => $adminID,
            'c_datetime'         => date('Y-m-d H:i:s'),
            'c_additionalFilter' => $additionalFilter,
            'c_type'             => 'Ewallet',
            'c_status'           => 'Pending',
            'c_filename'         => '',
        ];

        if ($is_api) {
            $inserted = $this->db->insert('admin_download', $data);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => (bool)$inserted,
                'message' => $inserted ? 'Ewallet download request submitted.' : 'Failed to submit download request.',
                'data'    => $inserted ? ['download_id' => $this->db->insert_id(), 'type' => 'Ewallet', 'filter' => $additionalFilter] : null
            ]));
        }

        if (empty($search_name_ewallet) && empty($search_date_ewallet) && empty($search_date_ewallet_settlement)) {
            $this->session->set_flashdata('error_message', 'Please fill search fields before downloading.');
            redirect('finance/e-wallet');
            return;
        }

        if ($this->db->insert('admin_download', $data)) {
            $this->session->set_flashdata('success', 'Your request is being processed. Go to Download Report.');
        } else {
            $this->session->set_flashdata('error', 'Failed request download');
        }
        redirect('finance/e-wallet');
    }

    public function ewallet_dynamic_list()
    {
        $this->_parseRawJson();
        try {
            $out = $this->EwalletDynamic->get_datatables_handler();
            $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
        } catch (\Throwable $e) {
            log_message('error', 'E-Wallet Dynamic List AJAX error: ' . $e->getMessage());
            $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
        }
    }

    public function ewallet_dynamic()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetewallet_dynamic(false);
        }

        $data['title'] = 'E-Wallet Dynamic';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_ewalletdynamic_name'     => 'search_name_ewd',
            'search_ewalletdynamic_date1'    => 'search_date_ewd',
            'search_ewalletdynamic_date2'    => 'search_date_ewd_to',
            'search_ewalletdynamic_status'   => 'search_status_transaction_ewd',
            'search_ewalletdynamic_reff'     => 'search_reff_label_ewallet',
            'search_ewalletdynamic_transid'  => 'search_transid_ewd',
            'search_ewalletdynamic_channel'  => 'search_channel_ewalletdynamic',
            'search_ewalletdynamic_external' => 'search_external_ewalletdynamic',
        ];
        $get_fallback = [
            'search_ewalletdynamic_name'     => 'merchant',
            'search_ewalletdynamic_transid'  => 'transid',
            'search_ewalletdynamic_channel'  => 'channel',
            'search_ewalletdynamic_external' => 'external',
        ];

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_ewalletdynamic', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_ewalletdynamic');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_ewalletdynamic', 'search_ewalletdynamic_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_ewalletdynamic_transid', $dtSearch);
                    $this->session->set_userdata('last_dt_search_ewalletdynamic', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_ewd'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_ewd')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_ewd_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'         => $merchant_val ?: null,
                    'date'             => $date_from_val ?: null,
                    'date_to'          => $date_to_val ?: null,
                    'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_ewd') ?: null),
                    'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_ewd') ?: null),
                    'reff'             => $this->input->post('reff') ?: ($this->input->post('search_reff_label_ewallet') ?: null),
                    'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_ewalletdynamic') ?: null),
                    'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_ewalletdynamic') ?: null)
                ];
                $out = $this->EwalletDynamic->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'E-Wallet Dynamic AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['merchants'] = $this->EwalletDynamic->get_merchant();
        $data['search_reff_label'] = $this->session->userdata('search_ewalletdynamic_reff');
        $data['internal_channels'] = $this->Ewallet->get_internal_channels();
        $data['external_channels'] = $this->Ewallet->get_external_channels();
        $this->load->view('ewallet/ewalletdynamic', $data);
    }

    public function resetewallet_dynamic($redirect = true)
    {
        $this->session->unset_userdata(['search_ewalletdynamic_name', 'search_ewalletdynamic_date1', 'search_ewalletdynamic_date2', 'search_ewalletdynamic_status', 'search_ewalletdynamic_reff', 'search_ewalletdynamic_transid', 'search_ewalletdynamic_channel', 'search_ewalletdynamic_external', 'last_dt_search_ewalletdynamic']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Dynamic E-Wallet search filters reset.']));
            return;
        }
        if ($redirect) redirect('ewallet/dynamic');
    }

    public function SendnotifikasiEwallet($ref_cashinPaymentEwalletId = NULL, $refMerchantId = NULL)
    {
        $is_api = $this->_isApiRequest();
        if (!$ref_cashinPaymentEwalletId) {
            if ($is_api) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
                return;
            }
            $this->session->set_flashdata('error', 'Transaction ID not found.');
            redirect('finance/e-wallet');
            return;
        }

        $internalRequestBody = [
            "msgType" => "consumer_notification_ewallet",
            "msgInfo" => ["ref_cashinPaymentEwalletId" => $ref_cashinPaymentEwalletId, "merchantId" => $refMerchantId]
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
            log_message('error', 'E-Wallet Resend Notif cURL Error: ' . curl_error($internalCurl));
        }
        curl_close($internalCurl);

        if ($is_api) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'E-Wallet notification queued successfully.']));
            return;
        }
        $this->session->set_flashdata('success', 'Notification has been resent.');
        redirect('finance/e-wallet');
    }

    public function getDetailEwalletDynamicChannelExternal()
    {
        if (!$this->session->userdata('c_email')) redirect('auth');
        $this->_parseRawJson();

        $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
        $parentId = $this->input->post('parentId') ?: 1;
        $ref_cashinExternalLogEwalletIdCreate = $this->input->post('ref_cashinExternalLogEwalletIdCreate') ?: 1;

        $detailData = $this->EwalletDynamic->getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate, $parentId);
        $this->output->set_content_type('application/json')->set_output(json_encode($detailData ?: []));
    }
}
