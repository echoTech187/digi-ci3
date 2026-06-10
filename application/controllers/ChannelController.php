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

      $table = 'cashin_external_x_channel cc';
      $column_order = array(null, 'cc.ref_cashinChannelId', 'cc.c_cashinChannelGroup', 'cc.c_cashinChannelGroup2', 'cc.c_cashinExternalId', 'cc.c_feeType', 'cc.c_fee', null);
      $column_search = array('cc.ref_cashinChannelId', 'cc.c_cashinChannelGroup', 'cc.c_cashinChannelGroup2', 'cc.c_cashinExternalId');
      $order = array('cc.id' => 'desc');
      $where = [];

      if ($this->input->post('channel_group')) {
          $where['cc.c_cashinChannelGroup'] = $this->input->post('channel_group');
      }
      if ($this->input->post('external_id')) {
          $where['cc.c_cashinExternalId'] = $this->input->post('external_id');
      }

      // Clear session if direct access (not ajax) without parameters
      if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
         $this->session->unset_userdata('search_channel');
      }

      $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
      if ($search_channel !== null) {
         $this->session->set_userdata('search_channel', $search_channel);
      } else {
         $search_channel = $this->session->userdata('search_channel');
      }

      if ($search_channel) {
         $where['custom_search'] = $search_channel;
      }

      if ($this->input->is_ajax_request()) {
         try {
            return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order, $where);
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

      $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();

      $this->load->view('chanel/cashin', $data);
   }

   public function cashout()
   {
      is_logged_in();

      $table = 'cashout_external_x_channel cc';
      $column_order = array(null, 'cc.ref_cashoutChannelId', 'cc.c_cashoutChannelGroup', 'cc.c_cashoutChannelGroup2', 'cc.c_cashoutExternalId', 'cc.c_feeType', 'cc.c_fee', null);
      $column_search = array('cc.ref_cashoutChannelId', 'cc.c_cashoutChannelGroup', 'cc.c_cashoutChannelGroup2', 'cc.c_cashoutExternalId');
      $order = array('cc.id' => 'desc');
      $where = array('cc.c_cashoutChannelGroup !=' => 'ppob');

      if ($this->input->post('channel_group')) {
          $where['cc.c_cashoutChannelGroup'] = $this->input->post('channel_group');
      }
      if ($this->input->post('external_id')) {
          $where['cc.c_cashoutExternalId'] = $this->input->post('external_id');
      }

      // Clear session if direct access (not ajax) without parameters
      if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
         $this->session->unset_userdata('search_channel_out');
      }

      $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
      if ($search_channel !== null) {
         $this->session->set_userdata('search_channel_out', $search_channel);
      } else {
         $search_channel = $this->session->userdata('search_channel_out');
      }

      if ($search_channel) {
         $where['custom_search'] = $search_channel;
      }

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

      $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();

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
         $this->session->set_flashdata('error', validation_errors());
         redirect('channel/cashin');
      } else {
         $data = array(
            'ref_cashinChannelId' => $this->input->post('id'),
            'c_cashinChannelGroup' => $this->input->post('chanelgroup'),
            'c_cashinChannelGroup2' => $this->input->post('description'),
            'c_cashinExternalId' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_settlementInterval' => $this->input->post('settlementinterval'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         $result = $this->Chanel->createCashinChannel($data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
            redirect('channel/cashin');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
               $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to create cashin channels.');
            } elseif ($code == 1062) {
               $this->session->set_flashdata('error', 'Failed to insert data: A channel with this ID or configuration already exists.');
            } else {
               $this->session->set_flashdata('error', 'Unable to insert channel due to a system constraint. Please verify your input or contact technical support.');
            }
            redirect('channel/cashin');
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
      $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
      $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

      if ($this->form_validation->run() == FALSE) {
         $this->session->set_flashdata('error', validation_errors());
         redirect('channel/cashout');
      } else {
         $data = array(
            'ref_cashoutChannelId' => $this->input->post('id'),
            'c_cashoutChannelGroup' => $this->input->post('chanelgroup'),
            'c_cashoutChannelGroup2' => $this->input->post('description'),
            'c_cashoutExternalId' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         $result = $this->Chanel->createCashoutChannel($data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
            redirect('channel/cashout');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
               $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to create cashout channels.');
            } elseif ($code == 1062) {
               $this->session->set_flashdata('error', 'Failed to insert data: A channel with this ID or configuration already exists.');
            } else {
               $this->session->set_flashdata('error', 'Unable to insert channel due to a system constraint. Please verify your input or contact technical support.');
            }
            redirect('channel/cashout');
         }
      }
   }

   public function updateCashinChanel()
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
         $this->session->set_flashdata('error', validation_errors());
         redirect('channel/cashin');
      } else {
         $id = $this->input->post('pk_id');
         $data = array(
            'ref_cashinChannelId' => $this->input->post('id'),
            'c_cashinChannelGroup' => $this->input->post('chanelgroup'),
            'c_cashinChannelGroup2' => $this->input->post('description'),
            'c_cashinExternalId' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_settlementInterval' => $this->input->post('settlementinterval'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         $result = $this->Chanel->updateCashinChannel($id, $data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully updated');
            redirect('channel/cashin');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
               $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to update cashin channels.');
            } elseif ($code == 1062) {
               $this->session->set_flashdata('error', 'Failed to update data: A channel with this configuration already exists.');
            } else {
               $this->session->set_flashdata('error', 'Unable to update channel due to a system constraint. Please verify your input or contact technical support.');
            }
            redirect('channel/cashin');
         }
      }
   }

   public function updateCashOutChanel()
   {
      $this->form_validation->set_rules('id', 'Id', 'required');
      $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
      $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
      $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
      $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
      $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

      if ($this->form_validation->run() == FALSE) {
         $this->session->set_flashdata('error', validation_errors());
         redirect('channel/cashout');
      } else {
         $id = $this->input->post('pk_id');
         $data = array(
            'ref_cashoutChannelId' => $this->input->post('id'),
            'c_cashoutChannelGroup' => $this->input->post('chanelgroup'),
            'c_cashoutChannelGroup2' => $this->input->post('description'),
            'c_cashoutExternalId' => $this->input->post('externaldefault'),
            'c_feeType' => $this->input->post('feetype'),
            'c_fee' => $this->input->post('fee'),
            'c_amountMin' => $this->input->post('amountmin'),
            'c_amountMax' => $this->input->post('amountmax')
         );

         $result = $this->Chanel->updateCashoutChannel($id, $data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully updated');
            redirect('channel/cashout');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
               $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to update cashout channels.');
            } elseif ($code == 1062) {
               $this->session->set_flashdata('error', 'Failed to update data: A channel with this configuration already exists.');
            } else {
               $this->session->set_flashdata('error', 'Unable to update channel due to a system constraint. Please verify your input or contact technical support.');
            }
            redirect('channel/cashout');
         }
      }
   }

   public function deleteCashInChanel($id = null)
   {
      is_logged_in();
      if (!$id) {
         $this->session->set_flashdata('error', 'Invalid ID');
         redirect('channel/cashin');
      }

      $result = $this->Chanel->deleteCashinChannel($id);
      if ($result === true) {
         $this->session->set_flashdata('success', 'Channel successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete cashin channels.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this channel because it is currently linked to existing merchant fee configurations or transactions.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete channel due to a system constraint. Please contact technical support.');
         }
      }
      redirect('channel/cashin');
   }

   public function deleteCashOutChanel($id = null)
   {
      is_logged_in();
      if (!$id) {
         $this->session->set_flashdata('error', 'Invalid ID');
         redirect('channel/cashout');
      }

      $result = $this->Chanel->deleteCashoutChannel($id);
      if ($result === true) {
         $this->session->set_flashdata('success', 'Channel successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete cashout channels.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this channel because it is currently linked to existing merchant fee configurations or transactions.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete channel due to a system constraint. Please contact technical support.');
         }
      }
      redirect('channel/cashout');
   }

   public function get_master_filter_options() {
       if (!$this->input->is_ajax_request()) return;
       $type = $this->input->post('type'); // 'cashin' or 'cashout'
       $group = $this->input->post('group');

       $table = ($type === 'cashin') ? 'cashin_external_x_channel' : 'cashout_external_x_channel';
       $col_id = ($type === 'cashin') ? 'c_cashinExternalId' : 'c_cashoutExternalId';
       $col_group = ($type === 'cashin') ? 'c_cashinChannelGroup' : 'c_cashoutChannelGroup';

       $this->db->select("$col_id as provider");
       $this->db->from($table);
       if (!empty($group)) {
           $this->db->where($col_group, $group);
       }
       // Don't include empty providers
       $this->db->where("$col_id !=", '');
       $this->db->where("$col_id IS NOT NULL", null, false);
       $this->db->group_by($col_id);
       $providers = $this->db->get()->result_array();

       return $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode([
               'providers' => array_column($providers, 'provider')
           ]));
   }
}
