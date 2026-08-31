<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani mutasi transaksi merchant.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class TransactionMutationController extends CI_Controller
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
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function mutation($id = NULL)
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
      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos($accept, 'json') !== false
         || strtolower($this->input->method()) === 'post'
         || $is_swagger;

      if (!$id) {
         $id = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->uri->segment(3)));
      }

      if (!$id && !$is_api) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
         return;
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$is_api && empty($this->input->get()) && !$this->input->post()) {
         $this->resetMutation($id, false);
      }

      $data['title'] = 'Mutation';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $id ? $this->Mutation_model->get_merchant($id) : [];

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      if ($id) $data['breadcrumb_replace'] = [$id => $merchant_name];

      // Sync from GET/POST to Session
      $field_map = [
         'search_mutation_date1'    => 'search_date_mutation',
         'search_mutation_date2'    => 'search_date_mutation_to',
         'search_mutation_position' => 'search_position',
         'search_mutation_channel'  => 'search_channel',
         'search_mutation_transid'  => 'search_transactionid_mutation',
      ];

      $get_fallback = [
         'search_mutation_date1'    => 'date_from',
         'search_mutation_date2'    => 'date_to',
         'search_mutation_position' => 'position',
         'search_mutation_channel'  => 'channel',
         'search_mutation_transid'  => 'transid',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
      if ($active_search) {
         $this->session->set_userdata('last_dt_search_mutation', $active_search);
         $this->session->set_userdata('search_mutation_transid', $active_search);
      }

      if ($is_api) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_mutation');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_mutation', 'search_mutation_transid']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_mutation_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_mutation', $dtSearch);
            }

            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->post('date') ?: $this->input->post('search_date_mutation')));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_mutation_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'date' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'position' => $this->input->post('position') ?: ($this->input->post('search_position') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel') ?: null),
               'transid' => $this->input->post('transid') ?: ($this->input->post('search_transactionid_mutation') ?: null)
            ];
            $out = $this->Mutation_model->get_datatables_handler($id, $filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'Mutation AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving mutation data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['channels'] = [];
      $search_position = $this->session->userdata('search_mutation_position');
      if ($search_position == 'Credit')
         $data['channels'] = $this->Mutation_model->get_cashin_channels($id);
      elseif ($search_position == 'Debit')
         $data['channels'] = $this->Mutation_model->get_cashout_channels($id);

      $this->load->view('mutation/list', $data);
   }

   public function resetMutation($id = NULL, $redirect = true)
   {
      if (!$id) $id = $this->uri->segment(4);
      $this->session->unset_userdata([
         'search_mutation_date1',
         'search_mutation_date2',
         'search_mutation_position',
         'search_mutation_channel',
         'search_mutation_transid',
         'last_dt_search_mutation'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Transaction mutation search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect("finance/mutation/$id");
   }

   public function download_mutation()
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

      $search_date_mutation = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_mutation_date1') ?: $this->session->userdata('search_mutation_date1')));
      $search_date_mutation_to = $this->input->post('date_to') ?: ($this->input->post('date2') ?: ($this->input->get('search_mutation_date2') ?: $this->session->userdata('search_mutation_date2')));
      $id = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->post('id') ?: ($this->input->get('id') ?: $this->session->userdata('search_mutation_merchant_id'))));

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'] ?? 1;

      $additionalFilter = $search_date_mutation . '|' . $search_date_mutation_to . '|' . $id;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Mutation',
      );

      if ($is_api_request) {
         if ($this->db->insert('admin_download', $data)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Your Mutation download request has been submitted successfully. Please check the Download Report menu to download the generated file.',
               'data' => [
                  'download_id' => $this->db->insert_id(),
                  'type' => 'Mutation',
                  'filter' => $additionalFilter
               ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         } else {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Failed to submit Mutation download request.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         }
      }

      if (empty($search_date_mutation) || empty($search_date_mutation_to)) {
         $this->session->set_flashdata('error_message', 'Please select both from and to dates before downloading.');
         redirect("finance/mutation/$id");
      }

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect("finance/mutation/$id");
   }
   
   public function getChannelsByPosition()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
          foreach ($raw_json as $k => $v) {
              if ($this->input->post($k) === null) {
                  $_POST[$k] = $v;
              }
          }
      }

      $position = $this->input->post('position');
      $merchant_id = $this->input->post('merchant_id');

      if (empty($merchant_id)) {
          $merchant_id = $this->session->userdata('merchant_id');
      }

      $this->load->model('Mutation_model');
      if ($position === 'Debit' || $position == '2') {
         $channels = $this->Mutation_model->get_cashout_channels($merchant_id);
      } else {
         $channels = $this->Mutation_model->get_cashin_channels($merchant_id);
      }

      $this->output
          ->set_content_type('application/json')
          ->set_output(json_encode($channels, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
   }
}
