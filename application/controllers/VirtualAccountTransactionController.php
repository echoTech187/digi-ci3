<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi Virtual Account.
 */
class VirtualAccountTransactionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'pagination', 'form_validation']);
        $this->load->model(['Model_user', 'Mutation_model', 'Chanel', 'VirtualAccount', 'VADynamic', 'VARecurring']);
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

    public function virtual_account()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetVA(false);
        }

        $data['title'] = 'Virtual Account';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_va_name'            => 'search_name_va',
            'search_va_date1'           => 'search_date_va',
            'search_va_date2'           => 'search_date_va_to',
            'search_va_date_settlement' => 'search_date_va_settlement',
            'search_va_channel'         => 'search_channel_va',
            'search_va_number'          => 'search_va_number',
            'search_va_transid'         => 'search_va_transid',
            'search_va_invoice_no'      => 'search_invoice_no',
        ];
        $get_fallback = [
            'search_va_name'            => 'merchant',
            'search_va_date1'           => 'date',
            'search_va_date2'           => 'date_to',
            'search_va_date_settlement' => 'settlement',
            'search_va_channel'         => 'channel',
            'search_va_number'          => 'va_number',
            'search_va_transid'         => 'transid',
            'search_va_invoice_no'      => 'invoice',
        ];

        $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
        if ($merchant_post !== NULL) $_POST['search_name_va'] = $merchant_post;
        $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->get('date_from')));
        if ($date_from_post !== NULL) $_POST['search_date_va'] = $date_from_post;
        $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
        if ($date_to_post !== NULL) $_POST['search_date_va_to'] = $date_to_post;

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('va_number');
        if ($active_search) $this->session->set_userdata('last_dt_search_va', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_va');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_va', 'search_va_invoice_no', 'search_va_transid', 'search_va_number']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_va_invoice_no', $dtSearch);
                    $this->session->set_userdata('last_dt_search_va', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_va'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_va')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_va_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'   => $merchant_val ?: null,
                    'date_from'  => $date_from_val ?: null,
                    'date_to'    => $date_to_val ?: null,
                    'settlement' => $this->input->post('settlement') ?: ($this->input->post('search_date_va_settlement') ?: null),
                    'channel'    => $this->input->post('channel') ?: ($this->input->post('search_channel_va') ?: null),
                    'va_number'  => $this->input->post('va_number') ?: ($this->input->post('search_va_number') ?: null),
                    'invoice'    => $this->input->post('invoice') ?: ($this->input->post('search_invoice_no') ?: null),
                    'transid'    => $this->input->post('transid') ?: ($this->input->post('search_va_transid') ?: null)
                ];
                $out = $this->VirtualAccount->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'VA AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['virtual_accounts'] = [];
        $data['start'] = 0;
        $data['pagination'] = '';
        $data['merchants'] = $this->VirtualAccount->get_merchant();
        $this->load->view('virtual_account/list', $data);
    }

    public function resetVA($redirect = true)
    {
        $this->session->unset_userdata(['search_va_name', 'search_va_date1', 'search_va_date2', 'search_va_date_settlement', 'search_va_channel', 'search_va_number', 'search_va_transid', 'search_va_invoice_no', 'last_dt_search_va']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Virtual Account search filters reset.']));
            return;
        }
        if ($redirect) redirect('finance/virtual-account');
    }

    public function virtual_account_detail($id = NULL)
    {
        if (!$id) redirect('finance/virtual-account');

        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['title'] = 'Detail Virtual Account';
        $data['va_data'] = $this->VirtualAccount->virtual_account_detail($id);

        $displayId = '#' . $id;
        if (!empty($data['va_data'])) {
            $row = $data['va_data'][0];
            $displayId = '#' . $row['c_invoiceNo'];
            $data['external_log'] = $this->VirtualAccount->get_external_payment_log($id, $row['ref_cashinExternalId']);
            $data['create_log'] = null;
            if ($row['c_type'] == 'Dynamic' && $row['dynamic_create_log_id']) {
                $data['create_log'] = $this->VADynamic->getDataVADynamicChannelExternal($row['ref_cashinExternalId'], $row['dynamic_create_log_id'], $row['ref_cashinDynamicVaId']);
            } elseif ($row['c_type'] == 'Recurring' && $row['recurring_create_log_id']) {
                $data['create_log'] = $this->VARecurring->getDataVARecurringChannelExternal($row['ref_cashinExternalId'], $row['recurring_create_log_id'], $row['ref_cashinRecurringVaId']);
            }
        }
        $data['breadcrumb_replace'] = [$id => $displayId];

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'VA detail data retrieved successfully',
                'data' => [
                    'va_data' => $data['va_data'],
                    'external_log' => $data['external_log'] ?? null,
                    'create_log' => $data['create_log'] ?? null
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }

        $this->load->view('virtual_account/detail_va', $data);
    }

    public function download_va()
    {
        $this->_parseRawJson();
        $is_api = $this->_isApiRequest();

        $search_date_va = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_va_date1') ?: $this->session->userdata('search_va_date1')));
        $search_name_va = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_va_name') ?: $this->session->userdata('search_va_name')));
        $search_date_va_to = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_va_date2') ?: $this->session->userdata('search_va_date2')));
        $search_date_va_settlement = $this->input->post('settlement') ?: ($this->input->get('search_va_date_settlement') ?: $this->session->userdata('search_va_date_settlement'));

        $user = $this->Model_user->view_user()->row_array();
        $adminID = $user['id'] ?? 1;
        $additionalFilter = $search_name_va . '|' . $search_date_va . '|' . $search_date_va_settlement;
        
        $data = [
            'ref_adminId'        => $adminID,
            'c_datetime'         => date('Y-m-d H:i:s'),
            'c_additionalFilter' => $additionalFilter,
            'c_type'             => 'VA',
            'c_status'           => 'Pending',
            'c_filename'         => '',
        ];

        if ($is_api) {
            $inserted = $this->db->insert('admin_download', $data);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => (bool)$inserted,
                'message' => $inserted ? 'VA download request submitted.' : 'Failed to submit download request.',
                'data'    => $inserted ? ['download_id' => $this->db->insert_id(), 'type' => 'VA', 'filter' => $additionalFilter] : null
            ]));
        }

        if (empty($search_name_va) && empty($search_date_va) && empty($search_date_va_settlement)) {
            $this->session->set_flashdata('error_message', 'Please fill search fields before downloading.');
            redirect('finance/virtual-account');
            return;
        }

        if ($this->db->insert('admin_download', $data)) {
            $this->session->set_flashdata('success', 'Your request is being processed. Go to Download Report.');
        } else {
            $this->session->set_flashdata('error', 'Failed request download');
        }
        redirect('finance/virtual-account');
    }

    public function va_dynamic_list()
    {
        $this->_parseRawJson();
        try {
            $out = $this->VADynamic->get_datatables_handler();
            $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
        } catch (\Throwable $e) {
            log_message('error', 'VA Dynamic List AJAX error: ' . $e->getMessage());
            $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
        }
    }

    public function va_dynamic()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetva_dynamic(false);
        }

        $data['title'] = 'Virtual Account Dynamic';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_vadynamic_name'     => 'search_name_vad',
            'search_vadynamic_date1'    => 'search_date_vad',
            'search_vadynamic_date2'    => 'search_date_vad_to',
            'search_vadynamic_status'   => 'search_status_transaction_vad',
            'search_vadynamic_reff'     => 'search_reff_label_va',
            'search_vadynamic_transid'  => 'search_transid_vad',
            'search_vadynamic_channel'  => 'search_channel_vadynamic',
            'search_vadynamic_external' => 'search_external_vadynamic',
        ];
        $get_fallback = [
            'search_vadynamic_name'     => 'merchant',
            'search_vadynamic_transid'  => 'transid',
            'search_vadynamic_channel'  => 'channel',
            'search_vadynamic_external' => 'external',
        ];

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_vadynamic', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_vadynamic');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_vadynamic', 'search_vadynamic_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_vadynamic_transid', $dtSearch);
                    $this->session->set_userdata('last_dt_search_vadynamic', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_vad'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_vad')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_vad_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'         => $merchant_val ?: null,
                    'date'             => $date_from_val ?: null,
                    'date_to'          => $date_to_val ?: null,
                    'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_vad') ?: null),
                    'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_vad') ?: null),
                    'reff'             => $this->input->post('reff') ?: ($this->input->post('search_reff_label_va') ?: null),
                    'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_vadynamic') ?: null),
                    'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_vadynamic') ?: null)
                ];
                $out = $this->VADynamic->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'VA Dynamic AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['merchants'] = $this->VADynamic->get_merchant();
        $data['search_reff_label'] = $this->session->userdata('search_vadynamic_reff');
        $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
        $data['external_channels'] = $this->VirtualAccount->get_external_channels();
        $this->load->view('virtual_account/vadynamic', $data);
    }

    public function resetva_dynamic($redirect = true)
    {
        $this->session->unset_userdata(['search_vadynamic_name', 'search_vadynamic_date1', 'search_vadynamic_date2', 'search_vadynamic_status', 'search_vadynamic_reff', 'search_vadynamic_transid', 'search_vadynamic_channel', 'search_vadynamic_external', 'last_dt_search_vadynamic']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Dynamic VA search filters reset.']));
            return;
        }
        if ($redirect) redirect('virtual-account/dynamic');
    }

    public function va_recurring()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
            $this->resetva_recurring(false);
        }

        $data['title'] = 'Virtual Account Recurring';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $field_map = [
            'search_varecurring_name'        => 'search_name_var',
            'search_varecurring_date1'       => 'search_date_var',
            'search_varecurring_date2'       => 'search_date_var_to',
            'search_varecurring_submerchant' => 'search_submerchant_var',
            'search_varecurring_status'      => 'search_status_transaction_var',
            'search_varecurring_transid'     => 'search_transid_var',
            'search_varecurring_channel'     => 'search_channel_varecurring',
            'search_varecurring_external'    => 'search_external_varecurring',
        ];
        $get_fallback = [
            'search_varecurring_name'        => 'merchant',
            'search_varecurring_transid'     => 'transid',
            'search_varecurring_channel'     => 'channel',
            'search_varecurring_external'    => 'external',
        ];

        foreach ($field_map as $session_key => $post_key) {
            $val = $this->input->post($post_key);
            if ($val === NULL && isset($get_fallback[$session_key])) $val = $this->input->get($get_fallback[$session_key]);
            if ($val !== NULL) $this->session->set_userdata($session_key, $val);
        }

        $active_search = $this->input->get('q') ?: $this->input->get('transid');
        if ($active_search) $this->session->set_userdata('last_dt_search_varecurring', $active_search);

        if ($this->_isApiRequest()) {
            try {
                $dtSearch = $this->input->post('search')['value'] ?? '';
                $oldSearch = $this->session->userdata('last_dt_search_varecurring');

                if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                    $this->session->unset_userdata(['last_dt_search_varecurring', 'search_varecurring_transid']);
                }
                if ($dtSearch !== '') {
                    $this->session->set_userdata('search_varecurring_transid', $dtSearch);
                    $this->session->set_userdata('last_dt_search_varecurring', $dtSearch);
                }

                $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_var'));
                $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_var')));
                $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_var_to'));
                if ($date_to_val && strlen(trim($date_to_val)) === 10) $date_to_val = trim($date_to_val) . ' 23:59:59';

                $filters = [
                    'merchant'         => $merchant_val ?: null,
                    'date'             => $date_from_val ?: null,
                    'date_to'          => $date_to_val ?: null,
                    'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_var') ?: null),
                    'submerchant'      => $this->input->post('submerchant') ?: ($this->input->post('search_submerchant_var') ?: null),
                    'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_var') ?: null),
                    'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_varecurring') ?: null),
                    'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_varecurring') ?: null)
                ];
                $out = $this->VARecurring->get_datatables_handler($filters);
                $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
                return;
            } catch (\Throwable $e) {
                log_message('error', 'VA Recurring AJAX error: ' . $e->getMessage());
                $this->output->set_content_type('application/json')->set_output(json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]));
                return;
            }
        }

        $data['merchants'] = $this->VARecurring->get_merchant();
        $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
        $data['external_channels'] = $this->VirtualAccount->get_external_channels();
        $this->load->view('virtual_account/varecurring', $data);
    }

    public function resetva_recurring($redirect = true)
    {
        $this->session->unset_userdata(['search_varecurring_name', 'search_varecurring_date1', 'search_varecurring_date2', 'search_varecurring_submerchant', 'search_varecurring_status', 'search_varecurring_transid', 'search_varecurring_channel', 'search_varecurring_external', 'last_dt_search_varecurring']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Recurring VA filters reset.']));
            return;
        }
        if ($redirect) redirect('virtual-account/recurring');
    }

    public function SendnotifikasiVA($ref_cashinPaymentVaId = NULL, $refMerchantId = NULL)
    {
        $is_api = $this->_isApiRequest();
        if (!$ref_cashinPaymentVaId) {
            if ($is_api) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
                return;
            }
            $this->session->set_flashdata('error', 'Transaction ID not found.');
            redirect('finance/virtual-account');
            return;
        }

        $internalRequestBody = [
            "msgType" => "consumer_notification_va",
            "msgInfo" => ["ref_cashinPaymentVaId" => $ref_cashinPaymentVaId, "merchantId" => $refMerchantId]
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
            log_message('error', 'VA Resend Notif cURL Error: ' . curl_error($internalCurl));
        }
        curl_close($internalCurl);

        if ($is_api) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'VA notification queued successfully.']));
            return;
        }
        $this->session->set_flashdata('success', 'Notification has been resent.');
        redirect('finance/virtual-account');
    }

    public function getDetailVaDynamicChannelExternal()
    {
        if (!$this->session->userdata('c_email')) redirect('auth');
        $this->_parseRawJson();

        $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
        $parentId = $this->input->post('parentId') ?: 1;
        $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate') ?: 1;

        $detailData = $this->VADynamic->getDataVADynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
        $this->output->set_content_type('application/json')->set_output(json_encode($detailData ?: []));
    }

    public function getDetailVaRecurringChannelExternal()
    {
        if (!$this->session->userdata('c_email')) redirect('auth');
        $this->_parseRawJson();

        $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
        $parentId = $this->input->post('parentId') ?: 1;
        $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate') ?: 1;

        $detailData = $this->VARecurring->getDataVARecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
        $this->output->set_content_type('application/json')->set_output(json_encode($detailData ?: []));
    }
}
