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

      if ($this->input->is_ajax_request()) {
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

            $filters = [
               'date' => $this->session->userdata('search_history_date1'),
               'merchant' => $this->session->userdata('search_history_name'),
               'invoice' => $this->session->userdata('search_history_invoice'),
               'status' => $this->session->userdata('search_history_status'),
            ];
            return $this->History->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'History AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving history data: " . $e->getMessage()
            ));
         }
         return;
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
      if ($redirect) redirect('finance/history');
   }

   public function download_history()
   {
      $search_merchant_purchase = $this->input->get('search_history_name') ?: '';
      $search_date_purchase = $this->input->get('search_history_date1') ?: '';
      $search_status_purchase = $this->input->get('search_history_status') ?: '';

      if (empty($search_date_purchase) && empty($search_merchant_purchase) && empty($search_status_purchase)) {
         $this->session->set_flashdata('error_message', 'Please select at least one filter before downloading.');
         redirect('finance/history');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];
      $additionalFilter = $search_date_purchase . '|' . $search_merchant_purchase . '|' . $search_status_purchase;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'PPOB',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('report/download') . '">Download Report</a> menu to retrieve the file.');
      } else {
         $this->session->set_flashdata('error', 'Failed to request download.');
      }

      redirect('finance/history');
   }
}
