<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani riwayat transaksi PPOB (History).
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class HistoryController extends CI_Controller
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
      $this->load->model('History');
      
      // Pastikan user sudah login
      is_logged_in();
   }

   public function index()
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
         $this->resetHistory(false);
      }

      $data['title'] = 'Purchase History';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_history_name'      => 'search_merchant_purchase',
         'search_history_date1'     => 'search_date_purchase',
         'search_history_invoice'   => 'search_invoice_ppob',
         'search_history_status'    => 'search_status_purchase',
      ];

      $get_fallback = [
         'search_history_name'      => 'merchant',
         'search_history_date1'     => 'date',
         'search_history_invoice'   => 'invoice',
         'search_history_status'    => 'status',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('phone');
      if ($active_search) {
         $this->session->set_userdata('last_dt_search_history', $active_search);
         $this->session->set_userdata('search_history_invoice', $active_search);
      }

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
            $oldSearch = $this->session->userdata('last_dt_search_history');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_history', 'search_history_invoice']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_history_invoice', $dtSearch);
               $this->session->set_userdata('last_dt_search_history', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_merchant_purchase'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_purchase')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_purchase_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'date' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'merchant' => $merchant_val ?: null,
               'invoice' => $this->input->post('invoice') ?: ($this->input->post('search_invoice_ppob') ?: null),
               'status' => $this->input->post('status') ?: ($this->input->post('search_status_purchase') ?: null),
            ];
            $out = $this->History->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'History AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving history data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->History->get_merchant();
      $this->load->view('history/list', $data);
   }

   public function resetHistory($redirect = true)
   {
      $this->session->unset_userdata([
         'search_history_name',
         'search_history_date1',
         'search_history_invoice',
         'last_dt_search_history',
         'search_history_status',
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Transaction history search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('finance/history');
   }

   public function download_history()
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

      $search_merchant_purchase = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_history_name') ?: $this->session->userdata('search_history_name')));
      $search_date_purchase = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_history_date1') ?: $this->session->userdata('search_history_date1')));
      $search_status_purchase = $this->input->post('status') ?: ($this->input->get('search_history_status') ?: $this->session->userdata('search_history_status'));

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'] ?? 1;
      $additionalFilter = $search_date_purchase . '|' . $search_merchant_purchase . '|' . $search_status_purchase;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'PPOB',
      );

      if ($is_api_request) {
         if ($this->db->insert('admin_download', $data)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Your PPOB History download request has been submitted successfully. Please check the Download Report menu to download the generated file.',
               'data' => [
                  'download_id' => $this->db->insert_id(),
                  'type' => 'PPOB',
                  'filter' => $additionalFilter
               ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         } else {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Failed to submit PPOB History download request.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         }
      }

      if (empty($search_date_purchase) && empty($search_merchant_purchase) && empty($search_status_purchase)) {
         $this->session->set_flashdata('error_message', 'Please select at least one filter before downloading.');
         redirect('finance/history');
      }

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('report/download') . '">Download Report</a> menu to retrieve the file.');
      } else {
         $this->session->set_flashdata('error', 'Failed to request download.');
      }

      redirect('finance/history');
   }
}
