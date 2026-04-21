<?php defined('BASEPATH') or exit('No direct script access allowed');
ini_set('display_errors', 1); error_reporting(E_ALL);

class ChannelController extends CI_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('Chanel');
      is_logged_in();
   }

   public function cashin()
   {
      is_logged_in();

      $table = 'cashin_channel cc';
      $column_order = array(null, 'cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault', 'cc.c_feeType', 'cc.c_fee', null);
      $column_search = array('cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault');
      $order = array('cc.id' => 'asc');

      if ($this->input->is_ajax_request()) {
         try {
            return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order);
         } catch (Throwable $e) {
            log_message('error', 'Cashin Channel AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving cashin channel data: " . $e->getMessage()
            ));
         }
      }

      $summary = $this->Chanel->get_cashin_summary();
      $data['qty']        = $summary->qty;
      $data['groups']     = $summary->total_groups;
      $data['providers']  = $summary->providers;
      $data['avg_fee']    = $summary->avg_fee;

      $data['title'] = 'Cash In Channels ';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['cashin_chanel'] = [];
      $data['pagination'] = '';
      $data['start'] = 0;

      $this->load->view('chanel/cashin', $data);
   }

   public function cashout()
   {
      is_logged_in();

      $table = 'cashout_channel cc';
      $column_order = array(null, 'cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault', 'cc.c_feeType', 'cc.c_fee', null);
      $column_search = array('cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault');
      $order = array('cc.id' => 'asc');
      $where = array('cc.c_channelGroup !=' => 'ppob');

      if ($this->input->is_ajax_request()) {
         try {
            return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order, $where);
         } catch (Throwable $e) {
            log_message('error', 'Cashout Channel AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving cashout channel data: " . $e->getMessage()
            ));
         }
      }

      $summary = $this->Chanel->get_cashout_summary();
      $data['qty']           = $summary->qty;
      $data['groups']        = $summary->total_groups;
      $data['providers']     = $summary->providers;
      $data['avg_fee']       = $summary->avg_fee;

      $data['title'] = 'Cash Out Channels';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['cashout_chanel'] = [];
      $data['pagination'] = '';
      $data['start'] = 0;

      // For the modal select
      $data['cashout_channels'] = $this->Chanel->get_cashout_channels_all(); // Need to verify this method exist

      $this->load->view('chanel/cashout', $data);
   }

   public function createCashinChanel()
   {
      $this->form_validation->set_rules('id', 'Id', 'required');
      $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
      $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
      $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
      $this->form_validation->set_rules('settlementinterval', 'Settlement Interval', 'required|numeric');
      $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
      $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

      if ($this->form_validation->run() == FALSE) {
         $this->load->view('admin/cashin');
      } else {
         $data = array(
            'id' => $this->input->post('id'),
            'c_channelGroup' => $this->input->post('chanelgroup'),
            'c_description' => $this->input->post('description'),
            'c_externalIdDefault' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_settlementInterval' => $this->input->post('settlementinterval'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         if ($this->Chanel->createCashinChannel($data)) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
            redirect('admin/cashin');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data');
            redirect('admin/cashin');
         }
      }
   }

   public function createCashOutChanel()
   {
      $this->form_validation->set_rules('id', 'Id', 'required');
      $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
      $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
      $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
      $this->form_validation->set_rules('settlementinterval', 'Settlement Interval', 'required|numeric');
      $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
      $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

      if ($this->form_validation->run() == FALSE) {
         $this->load->view('admin/cashout');
      } else {
         $data = array(
            'id' => $this->input->post('id'),
            'c_channelGroup' => $this->input->post('chanelgroup'),
            'c_description' => $this->input->post('description'),
            'c_externalIdDefault' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_settlementInterval' => $this->input->post('settlementinterval'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         if ($this->Chanel->createCashoutChannel($data)) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
            redirect('admin/cashout');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data');
            redirect('admin/cashout');
         }
      }
   }
}
