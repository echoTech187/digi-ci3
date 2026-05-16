<?php defined('BASEPATH') or exit('No direct script access allowed');

class ReportController extends CI_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('BalanceLogModel');
      is_logged_in();
   }

   public function balance_log()
   {
      is_logged_in();

      if ($this->input->is_ajax_request()) {
         try {
            return $this->BalanceLogModel->get_datatables_handler();
         } catch (Throwable $e) {
            log_message('error', 'Balance Log AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving balance log data: " . $e->getMessage()
            ));
         }
      }

      $data['title'] = 'Balance Log';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['balance_logs'] = [];

      // Summary data for KPI cards
      $summary = $this->BalanceLogModel->get_summary();
      $data['total_logs']      = $summary->total_logs;
      $data['total_merchants'] = $summary->total_merchants;
      $data['total_settled']   = $summary->total_settled;
      $data['avg_settled']     = $summary->avg_settled;

      $this->load->view('admin/balance_log', $data);
   }
   
   public function report()
   {
      $this->load->model('AdminDownload');
      is_logged_in();

      $search_date = $this->input->post('search_date');
      if (!$search_date && !$this->input->is_ajax_request()) {
         $search_date = $this->session->userdata('search_date');
      }

      if ($this->input->is_ajax_request()) {
         try {
            $filters = [
               'date' => $this->session->userdata('search_date') ?: $this->input->post('search_date')
            ];
            return $this->AdminDownload->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'Report AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving report data: " . $e->getMessage()
            ));
         }
      }

      if ($search_date) {
         $this->session->set_userdata('search_date', $search_date);
      }

      $data['title'] = 'Report';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['search_date'] = $search_date;
      $data['downloads'] = [];
      $data['pagination'] = '';
      $data['start'] = 0;

      $this->load->view('report/index', $data);
   }

   public function resetdownload()
   {
      $this->session->unset_userdata('search_date');
      redirect('admin/report');
   }

   public function download()
   {
      $filename = $this->input->get('filename');

      if (!empty($filename)) {
         // Standard report download path
         $filepath = '/var/www/download_report/' . $filename;

         if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            readfile($filepath);
         } else {
            echo 'File not found.';
         }
      } else {
         echo 'Filename parameter is missing.';
      }
   }
}
