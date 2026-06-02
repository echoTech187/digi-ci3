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
      if (!$id) $id = $this->uri->segment(3);
      if (!$id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetMutation($id, false);
      }

      $data['title'] = 'Mutation';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Mutation_model->get_merchant($id);

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [$id => $merchant_name];

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

      if ($this->input->is_ajax_request()) {
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

            $filters = [
               'date' => $this->session->userdata('search_mutation_date1'),
               'date_to' => $this->session->userdata('search_mutation_date2'),
               'position' => $this->session->userdata('search_mutation_position'),
               'channel' => $this->session->userdata('search_mutation_channel'),
               'transid' => $this->session->userdata('search_mutation_transid')
            ];
            return $this->Mutation_model->get_datatables_handler($id, $filters);
         } catch (Throwable $e) {
            log_message('error', 'Mutation AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving mutation data: " . $e->getMessage()
            ));
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
      if ($redirect) redirect("finance/mutation/$id");
   }

   public function download_mutation()
   {
      $search_date_mutation = isset($_GET['search_mutation_date1']) ? $_GET['search_mutation_date1'] : '';
      $search_date_mutation_to = isset($_GET['search_mutation_date2']) ? $_GET['search_mutation_date2'] : '';
      $id = isset($_GET['id']) ? $_GET['id'] : '';

      if (empty($search_date_mutation) || empty($search_date_mutation_to)) {
         $this->session->set_flashdata('error_message', 'Please select both from and to dates before downloading.');
         redirect("finance/mutation/$id");
      }
      
      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_date_mutation . '|' . $search_date_mutation_to . '|' . $id;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Mutation',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect("finance/mutation/$id");
   }
   
   public function getChannelsByPosition()
   {
      $position = $this->input->post('position');
      $merchant_id = $this->input->post('merchant_id');
      
      if (empty($position) || empty($merchant_id)) {
         echo json_encode([]);
         return;
      }

      $this->load->model('Mutation_model');
      if ($position === 'Credit') {
         $channels = $this->Mutation_model->get_cashin_channels($merchant_id);
      } else {
         $channels = $this->Mutation_model->get_cashout_channels($merchant_id);
      }

      echo json_encode($channels);
   }
}
