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
      if (!$this->input->is_ajax_request()) {
          $search_invoice_ppob = $this->input->get('invoice') ?: ($this->input->get('transid') ?: ($this->input->get('phone') ?: ($this->input->post('search_invoice_ppob') ?: '')));
          
          if ($this->input->get('invoice') || $this->input->get('transid') || $this->input->get('phone')) {
              $this->session->set_userdata('last_search_ppob', $search_invoice_ppob);
          }

          if (!$this->input->get('invoice') && !$this->input->get('transid') && !$this->input->get('phone') && !$this->input->post('search_invoice_ppob')) {
              $this->session->unset_userdata('last_search_ppob');
          }
      } else {
          $search_invoice_ppob = $this->session->userdata('search_invoice_ppob');
      }

      $search_merchant_purchase = $this->input->post('search_merchant_purchase');
      $search_date_purchase = $this->input->post('search_date_purchase');

      if ($search_date_purchase) {
         $this->session->set_userdata('search_date_purchase', $search_date_purchase);
      } else {
         $search_date_purchase = $this->session->userdata('search_date_purchase');
      }

      if ($search_merchant_purchase) {
         $this->session->set_userdata('search_merchant_purchase', $search_merchant_purchase);
      } else {
         $search_merchant_purchase = $this->session->userdata('search_merchant_purchase');
      }

      $this->session->set_userdata('search_invoice_ppob', $search_invoice_ppob);

      // Handle DataTables AJAX Request
      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_ppob');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                $this->session->unset_userdata('search_date_purchase');
                $this->session->unset_userdata('search_merchant_purchase');
                $this->session->unset_userdata('search_invoice_ppob');
            }

            if ($dtSearch !== '') {
                $this->session->set_userdata('search_invoice_ppob', $dtSearch);
                $this->session->set_userdata('last_search_ppob', $dtSearch);
            }

            $filters = [
               'date' => $this->session->userdata('search_date_purchase'),
               'merchant' => $this->session->userdata('search_merchant_purchase'),
               'invoice' => $this->session->userdata('search_invoice_ppob')
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

      $data['title'] = 'Purchase History';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchants'] = $this->History->get_merchant();
      $this->load->view('history/list', $data);
   }

   public function resetHistory()
   {
      $this->session->unset_userdata('search_date_purchase');
      $this->session->unset_userdata('search_merchant_purchase');
      $this->session->unset_userdata('search_invoice_ppob');
      $this->session->unset_userdata('last_search_ppob');
      redirect('finance/history');
   }

   public function download_history()
   {
      $search_date_purchase = $this->input->get('search_date_purchase') ?: '';
      $search_merchant_purchase = $this->input->get('search_merchant_purchase') ?: '';

      if (empty($search_date_purchase) && empty($search_merchant_purchase)) {
         $this->session->set_flashdata('error_message', 'Please select date and merchant before downloading.');
         redirect('finance/history');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];
      $additionalFilter = $search_date_purchase . '|' . $search_merchant_purchase;

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
