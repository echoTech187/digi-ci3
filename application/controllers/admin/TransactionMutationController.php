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
         redirect('admin/merchant');
      }

      $data['title'] = 'Mutation';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Mutation_model->get_merchant($id);

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [$id => $merchant_name];

      $search_date_mutation = $this->input->post('search_date_mutation')
         != NULL ? $this->input->post('search_date_mutation') : $this->session->userdata('search_date_mutation');

      $search_date_mutation_to = $this->input->post('search_date_mutation_to')
         != NULL ? $this->input->post('search_date_mutation_to') : $this->session->userdata('search_date_mutation_to');

      $search_position = $this->input->post('search_position')
         != NULL ? $this->input->post('search_position') : $this->session->userdata('search_position');

      $search_channel = $this->input->post('search_channel')
         != NULL ? $this->input->post('search_channel') : $this->session->userdata('search_channel');

      // Sync Session
      $this->session->set_userdata([
         'search_date_mutation' => $search_date_mutation,
         'search_date_mutation_to' => $search_date_mutation_to,
         'search_position' => $search_position,
         'search_channel' => $search_channel
      ]);

      if ($this->input->is_ajax_request()) {
         try {
            $filters = [
               'date' => $search_date_mutation,
               'date_to' => $search_date_mutation_to,
               'position' => $search_position,
               'channel' => $search_channel
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
      if ($search_position == 'Credit')
         $data['channels'] = $this->Mutation_model->get_cashin_channels($id);
      elseif ($search_position == 'Debit')
         $data['channels'] = $this->Mutation_model->get_cashout_channels($id);

      $this->load->view('mutation/list', $data);
   }

   public function resetMutation()
   {
      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_date_mutation');
      $this->session->unset_userdata('search_date_mutation_to');
      $this->session->unset_userdata('search_position');
      $this->session->unset_userdata('search_channel');
      redirect("admin/mutation/$id");
   }

   public function download_mutation()
   {
      $search_date_mutation = isset($_GET['search_date_mutation']) ? $_GET['search_date_mutation'] : '';
      $search_date_mutation_to = isset($_GET['search_date_mutation_to']) ? $_GET['search_date_mutation_to'] : '';
      $id = isset($_GET['id']) ? $_GET['id'] : '';

      if (empty($search_date_mutation) || empty($search_date_mutation_to)) {
         $this->session->set_flashdata('error_message', 'Please select both from and to dates before downloading.');
         redirect("admin/mutation/$id");
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

      redirect("admin/mutation/$id");
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
