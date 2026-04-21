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
         $draw = intval($this->input->post("draw"));
         $start = intval($this->input->post("start"));
         $length = intval($this->input->post("length"));

         $list = $this->BalanceLogModel->get_datatables();
         $dataItems = array();
         $no = $start;

         foreach ($list as $items) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['created_at'] = $items->created_at;
            $row['merchant_id'] = $items->merchant_id;
            $row['merchant_name'] = $items->merchant_name;
            $row['add_to_available'] = $items->add_to_available;

            $dataItems[] = $row;
         }

         $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->BalanceLogModel->count_all_dt(),
            "recordsFiltered" => $this->BalanceLogModel->count_filtered(),
            "data" => $dataItems,
         );
         echo json_encode($output);
         exit;
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
}
