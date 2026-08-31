<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi E-Wallet.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class EwalletTransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar yang dibutuhkan
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Load model dasar
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('Ewallet');
      $this->load->model('EwalletDynamic');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit (Internal/External)
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   /**
    * Menampilkan halaman daftar transaksi E-Wallet dan menangani request DataTables.
    */
   public function ewallet()
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
         $this->resetewallet(false);
      }

      $data['title'] = 'Ewallet';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
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

      // Alias parameters support for API / Swagger
      $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
      if ($merchant_post !== NULL) $_POST['search_name_ewallet'] = $merchant_post;

      $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->get('date_from'));
      if ($date_from_post !== NULL) $_POST['search_date_ewallet'] = $date_from_post;

      $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
      if ($date_to_post !== NULL) $_POST['search_date_ewallet_to'] = $date_to_post;

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_ewallet', $active_search);

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
            $oldSearch = $this->session->userdata('last_dt_search_ewallet');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_ewallet', 'search_ewallet_invoice_no', 'search_ewallet_transid']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_ewallet_invoice_no', $dtSearch);
               $this->session->set_userdata('last_dt_search_ewallet', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_ewallet'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->post('search_date_ewallet'));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_ewallet_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'merchant' => $merchant_val ?: null,
               'date_from' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'settlement' => $this->input->post('settlement') ?: ($this->input->post('search_date_ewallet_settlement') ?: null),
               'invoice' => $this->input->post('invoice') ?: ($this->input->post('search_invoice_no') ?: null),
               'transid' => $this->input->post('transid') ?: ($this->input->post('search_transid_ewallet') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel_ewallet') ?: null)
            ];
            $out = $this->Ewallet->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'E-Wallet AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving E-Wallet data: " . $e->getMessage()
               )));
            return;
         }
      }    
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['ewallets'] = [];
      $data['merchants'] = $this->Ewallet->get_merchant();
      $data['internal_channels'] = $this->Ewallet->get_internal_channels();

      $this->load->view('ewallet/ewallet_list', $data);
   }

   public function resetewallet($redirect = true)
   {
      $this->session->unset_userdata([
         'search_ewallet_name',
         'search_ewallet_date1',
         'search_ewallet_date2',
         'search_ewallet_date_settlement',
         'search_ewallet_invoice_no',
         'search_ewallet_transid',
         'search_ewallet_channel',
         'last_dt_search_ewallet'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'E-Wallet search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('finance/e-wallet');
   }

   public function download_ewallet()
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

      $search_date_ewallet = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_ewallet_date1') ?: $this->session->userdata('search_ewallet_date1')));
      $search_date_to_ewallet = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_ewallet_date2') ?: $this->session->userdata('search_ewallet_date2')));
      $search_name_ewallet = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_ewallet_name') ?: $this->session->userdata('search_ewallet_name')));
      $search_date_ewallet_settlement = $this->input->post('settlement') ?: ($this->input->get('search_date_ewallet_settlement') ?: $this->session->userdata('search_date_ewallet_settlement'));

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'] ?? 1;
      $additionalFilter = $search_name_ewallet . '|' . $search_date_ewallet . '|' . $search_date_to_ewallet . '|' . $search_date_ewallet_settlement;
      
      $data = [
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Ewallet',
      ];

      if ($is_api_request) {
         if ($this->db->insert('admin_download', $data)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Your E-Wallet download request has been submitted successfully. Please check the Download Report menu to download the generated file.',
               'data' => [
                  'download_id' => $this->db->insert_id(),
                  'type' => 'Ewallet',
                  'filter' => $additionalFilter
               ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         } else {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Failed to submit E-Wallet download request.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         }
      }

      if (
         empty($search_name_ewallet) &&
         empty($search_date_ewallet) &&
         empty($search_date_to_ewallet) &&
         empty($search_date_ewallet_settlement)
      ) {
         redirect('finance/e-wallet');
      }

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Request download has been sent, please check in report menu');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('finance/e-wallet');
   }

   public function ewallet_dynamic()
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
         $this->resetewallet_dynamic(false);
      }

      $data['title'] = 'E-Wallet Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_ewalletdynamic_name'     => 'search_name_qd',
         'search_ewalletdynamic_date1'    => 'search_date_qd',
         'search_ewalletdynamic_date2'    => 'search_date_qd_to',
         'search_ewalletdynamic_status'   => 'search_status_transaction_qd',
         'search_ewalletdynamic_transid'  => 'search_transid_qd',
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
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_ewalletdynamic', $active_search);

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
            $oldSearch = $this->session->userdata('last_dt_search_ewalletdynamic');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_ewalletdynamic', 'search_ewalletdynamic_transid']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_ewalletdynamic_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_ewalletdynamic', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_qd'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_qd')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_qd_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'merchant'         => $merchant_val ?: null,
               'date'             => $date_from_val ?: null,
               'date_to'          => $date_to_val ?: null,
               'transid'          => $this->input->post('transid') ?: ($this->input->post('search_transid_qd') ?: null),
               'status'           => $this->input->post('status') ?: ($this->input->post('search_status_transaction_qd') ?: null),
               'channel'          => $this->input->post('channel') ?: ($this->input->post('search_channel_ewalletdynamic') ?: null),
               'external_channel' => $this->input->post('external') ?: ($this->input->post('search_external_ewalletdynamic') ?: null)
            ];
            $out = $this->EwalletDynamic->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'E-Wallet Dynamic AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving E-Wallet Dynamic data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->EwalletDynamic->get_merchant();
      $data['internal_channels'] = $this->Ewallet->get_internal_channels();
      $data['external_channels'] = $this->Ewallet->get_external_channels();
      $this->load->view('ewallet/ewallet_dynamic', $data);
   }

   public function ewallet_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/e-wallet');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail Ewallet';
      $data['saldo'] = $this->Model_user->saldo();
      $data['ewallet_data'] = $this->Ewallet->ewallet_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['ewallet_data'])) {
         $displayId = '#' . $data['ewallet_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode([
                  'status' => true,
                  'message' => 'E-Wallet detail data retrieved successfully',
                  'data' => [
                      'ewallet_data' => $data['ewallet_data']
                  ]
              ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
          return;
      }

      $this->load->view('ewallet/ewallet_detail', $data);
   }

   public function resetewallet_dynamic($redirect = true)
   {
      $this->session->unset_userdata([
         'search_ewalletdynamic_name',
         'search_ewalletdynamic_date1',
         'search_ewalletdynamic_date2',
         'search_ewalletdynamic_status',
         'search_ewalletdynamic_transid',
         'search_ewalletdynamic_channel',
         'search_ewalletdynamic_external',
         'last_dt_search_ewalletdynamic'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Dynamic E-Wallet search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('e-wallet/dynamic');
   }

   public function Sendnotifikasiewallet($ref_cashinPaymentEwalletId = NULL, $refMerchantId = NULL)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$ref_cashinPaymentEwalletId) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
            return;
         }
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/e-wallet');
         return;
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_ewallet",
         "msgInfo" => array(
            "ref_cashinPaymentEwalletId" => $ref_cashinPaymentEwalletId,
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
            'message' => 'E-Wallet notification resend queue request submitted successfully.',
            'data' => ['id' => $ref_cashinPaymentEwalletId, 'merchantId' => $refMerchantId]
         ]));
         return;
      }

      $this->session->set_flashdata('success', 'Notification has resend');
      redirect('finance/e-wallet');
   }

   public function getDetailEwalletDynamicChannelExternal()
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
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
      $ref_cashinExternalLogEwalletIdCreate = $this->input->post('ref_cashinExternalLogEwalletIdCreate') ?: 1;

      $detailData = $this->EwalletDynamic->getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate);
      echo json_encode($detailData ?: []);
   }

   public function getDetailEwalletChannelExternal()
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
      $this->load->model('QRISDynamic');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId') ?: 'paylabs';
      $parentId = $this->input->post('parentId') ?: 1;
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate') ?: 1;

      $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
      echo json_encode($detailData ?: []);
   }
}
