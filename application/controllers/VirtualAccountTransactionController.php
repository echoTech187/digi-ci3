<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi Virtual Account.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class VirtualAccountTransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Load model dasar
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('VirtualAccount');
      $this->load->model('VADynamic');
      $this->load->model('VARecurring');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function virtual_account()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVA(false);
      }

      $data['title'] = 'Virtual Account';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
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

      // Alias parameters support for API / Swagger
      $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
      if ($merchant_post !== NULL) $_POST['search_name_va'] = $merchant_post;

      $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->get('date_from')));
      if ($date_from_post !== NULL) $_POST['search_date_va'] = $date_from_post;

      $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
      if ($date_to_post !== NULL) $_POST['search_date_va_to'] = $date_to_post;

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_va', $active_search);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos($accept, 'json') !== false
         || strtolower($this->input->method()) === 'post'
         || $is_swagger;

      if ($is_api) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_va');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_va', 'search_va_invoice_no', 'search_va_transid', 'search_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_va_invoice_no', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_va', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_va'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_va')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_va_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'date' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'merchant' => $merchant_val ?: null,
               'settlement' => $this->input->post('settlement') ?: ($this->input->post('search_date_va_settlement') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel_va') ?: null),
               'va_number' => $this->input->post('va_number') ?: ($this->input->post('search_va_number') ?: null),
               'transid' => $this->input->post('transid') ?: ($this->input->post('search_va_transid') ?: null),
               'invoice_no' => $this->input->post('invoice') ?: ($this->input->post('search_invoice_no') ?: null)
            ];
            $out = $this->VirtualAccount->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'VA AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving VA data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->VirtualAccount->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $this->load->view('virtualaccount/list', $data);
   }

   public function resetVA($redirect = true)
   {
      $this->session->unset_userdata([
         'search_va_date1',
         'search_va_date2',
         'search_va_date_settlement',
         'search_va_name',
         'search_va_channel',
         'search_va_number',
         'search_va_transid',
         'search_va_invoice_no',
         'last_dt_search_va'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Virtual Account search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('finance/virtual-account');
   }

   public function VA_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/virtual-account');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail VA';
      $data['va_data'] = $this->VirtualAccount->va_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['va_data'])) {
         $displayId = '#' . $data['va_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode([
                  'status' => true,
                  'message' => 'Virtual Account detail data retrieved successfully',
                  'data' => [
                      'va_data' => $data['va_data']
                  ]
              ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
          return;
      }

      $this->load->view('virtualaccount/detail_va', $data);
   }

   public function download_VA()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || strtolower($this->input->method()) === 'post' || $is_swagger;

      $search_date_va = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_va_date1') ?: $this->session->userdata('search_va_date1')));
      $search_date_va_to = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_va_date2') ?: $this->session->userdata('search_va_date2')));
      $search_name_va = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_va_name') ?: $this->session->userdata('search_va_name')));
      $search_date_va_settlement = $this->input->post('settlement') ?: ($this->input->get('search_va_date_settlement') ?: $this->session->userdata('search_va_date_settlement'));

      if ($is_api_request) {
         $user = $this->Model_user->view_user()->row_array();
         $adminID = $user['id'] ?? 1;

         $additionalFilter = $search_name_va . '|' . $search_date_va . '|' . $search_date_va_to . '|' . $search_date_va_settlement;
         $data = array(
            'ref_adminId' => $adminID,
            'c_datetime' => date('Y-m-d H:i:s'),
            'c_additionalFilter' => $additionalFilter,
            'c_type' => 'Va',
         );

         if ($this->db->insert('admin_download', $data)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Your Virtual Account download request has been submitted successfully. Please check the Download Report menu to download the generated file.',
               'data' => [
                  'download_id' => $this->db->insert_id(),
                  'type' => 'Va',
                  'filter' => $additionalFilter
               ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         } else {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Failed to submit Virtual Account download request.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         }
      }

      if (empty($search_name_va) && (empty($search_date_va) || empty($search_date_va_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('finance/virtual-account');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_name_va . '|' . $search_date_va . '|' . $search_date_va_to . '|' . $search_date_va_settlement;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Va',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('finance/virtual-account');
   }

   public function Va_dynamic()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVa_dynamic(false);
      }

      $data['title'] = 'VA Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_vadynamic_name'      => 'search_name_vad',
         'search_vadynamic_date1'     => 'search_date_vad',
         'search_vadynamic_date2'     => 'search_date_vad_to',
         'search_vadynamic_status'    => 'search_status_transaction_vad',
         'search_vadynamic_transid'   => 'search_transid_vad',
         'search_vadynamic_va_number' => 'search_va_number',
         'search_vadynamic_channel'   => 'search_channel_vadynamic',
         'search_vadynamic_external'  => 'search_external_vadynamic'
      ];

      $get_fallback = [
         'search_vadynamic_name'      => 'merchant',
         'search_vadynamic_transid'   => 'transid',
         'search_vadynamic_va_number' => 'va_number'
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_vadynamic', $active_search);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos($accept, 'json') !== false
         || strtolower($this->input->method()) === 'post'
         || $is_swagger;

      if ($is_api) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_vadynamic');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_vadynamic', 'search_vadynamic_transid', 'search_vadynamic_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_vadynamic_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_vadynamic_transid', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_vadynamic', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_vad'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_vad')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_vad_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'merchant' => $merchant_val ?: null,
               'date' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'va_number' => $this->input->post('va_number') ?: ($this->input->post('search_va_number') ?: null),
               'merchant_trxid' => $this->input->post('transid') ?: ($this->input->post('search_transid_vad') ?: null),
               'status' => $this->input->post('status') ?: ($this->input->post('search_status_transaction_vad') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel_vadynamic') ?: null),
               'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_vadynamic') ?: null)
            ];
            $out = $this->VADynamic->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'VA Dynamic AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving VA Dynamic data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->VADynamic->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $data['external_channels'] = $this->VirtualAccount->get_external_channels();
      $this->load->view('virtualaccount/vadynamic', $data);
   }

   public function resetVa_dynamic($redirect = true)
   {
      $this->session->unset_userdata([
         'search_vadynamic_name',
         'search_vadynamic_date1',
         'search_vadynamic_date2',
         'search_vadynamic_status',
         'search_vadynamic_transid',
         'search_vadynamic_va_number',
         'search_vadynamic_channel',
         'search_vadynamic_external',
         'last_dt_search_vadynamic'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Dynamic Virtual Account search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('virtual-account/dynamic');
   }

   public function VA_recurring()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVa_recurring(false);
      }

      $data['title'] = 'VA Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_varecurring_name'        => 'search_name_var',
         'search_varecurring_date1'       => 'search_date_var',
         'search_varecurring_date2'       => 'search_date_var_to',
         'search_varecurring_submerchant' => 'search_submerchant_var',
         'search_varecurring_transid'     => 'search_transid_var',
         'search_varecurring_va_number'   => 'search_va_number_var',
         'search_varecurring_status'      => 'search_status_transaction_var',
         'search_varecurring_channel'     => 'search_channel_varecurring',
         'search_varecurring_external'    => 'search_external_varecurring'
      ];

      $get_fallback = [
         'search_varecurring_name'        => 'merchant',
         'search_varecurring_transid'     => 'transid',
         'search_varecurring_va_number'   => 'va_number'
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_varecurring', $active_search);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos($accept, 'json') !== false
         || strtolower($this->input->method()) === 'post'
         || $is_swagger;

      if ($is_api) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_varecurring');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_varecurring', 'search_varecurring_transid', 'search_varecurring_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_varecurring_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_varecurring_transid', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_varecurring', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_var'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_var')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_var_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'merchant' => $merchant_val ?: null,
               'date' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'submerchant' => $this->input->post('submerchant') ?: ($this->input->post('search_submerchant_var') ?: null),
               'transid' => $this->input->post('transid') ?: ($this->input->post('search_transid_var') ?: null),
               'va_number' => $this->input->post('va_number') ?: ($this->input->post('search_va_number_var') ?: null),
               'status' => $this->input->post('status') ?: ($this->input->post('search_status_transaction_var') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel_varecurring') ?: null),
               'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_varecurring') ?: null)
            ];
            $out = $this->VARecurring->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'VA Recurring AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving VA Recurring data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->VARecurring->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $data['external_channels'] = $this->VirtualAccount->get_external_channels();
      $this->load->view('virtualaccount/varecurring', $data);
   }

   public function resetVa_recurring($redirect = true)
   {
      $this->session->unset_userdata([
         'search_varecurring_name',
         'search_varecurring_date1',
         'search_varecurring_date2',
         'search_varecurring_submerchant',
         'search_varecurring_transid',
         'search_varecurring_va_number',
         'search_varecurring_status',
         'search_varecurring_channel',
         'search_varecurring_external',
         'last_dt_search_varecurring'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Recurring Virtual Account search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('virtual-account/recurring');
   }

   public function SendnotifikasiVA($ref_cashinPaymentVaId = NULL, $refMerchantId = NULL)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$ref_cashinPaymentVaId) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
            return;
         }
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/virtual-account');
         return;
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_va",
         "msgInfo" => array(
            "ref_cashinPaymentVaId" => $ref_cashinPaymentVaId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = (property_exists($this, 'internalUrlHit') ? $this->internalUrlHit : 'http://127.0.0.1/gatewayservice') . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Virtual Account notification resend queue request submitted successfully.',
            'data' => ['id' => $ref_cashinPaymentVaId, 'merchantId' => $refMerchantId]
         ]));
         return;
      }

      $this->session->set_flashdata('success', 'Notification has resend');
      redirect('finance/virtual-account');
   }

   public function getDetailVaDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'ifp';
      $parentId = $this->input->post('parentId') ?: 1;
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate') ?: 1;

      $detailData = $this->VADynamic->getDataVaDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
      echo json_encode($detailData ?: []);
   }

   public function getDetailVaRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'ifp';
      $parentId = $this->input->post('parentId') ?: 1;
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate') ?: 1;

      $detailData = $this->VARecurring->getDataVaRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
      echo json_encode($detailData ?: []);
   }
}
