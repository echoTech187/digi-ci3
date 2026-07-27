<?php defined('BASEPATH') or exit('No direct script access allowed');

class MerchantFeeController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('Merchant');
      is_logged_in();
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function settingcashinfee()
   {
      $data['title'] = 'Setting Cashin Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }
      $merchant = $this->Merchant->get_merchant_by_id($data['merchant_id']);
      $data['merchant_name'] = $merchant ? $merchant['c_name'] : 'Unknown';

      if ($this->input->is_ajax_request()) {
         try {
            return $this->Merchant->get_fee_datatables_handler('cashin', $data['merchant_id']);
         } catch (Throwable $e) {
            log_message('error', 'Cashin Fee AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => "Error"
            ]);
            return;
         }
      }

      $data['breadcrumb_replace'] = [$data['merchant_id'] => $data['merchant_name']];
      $data['cashin_channel_x_merchant'] = $this->Merchant->get_cashin_channel_x_merchant_by_merchant_id($data['merchant_id']);
      $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
      $data['channel_ids'] = $this->Chanel->get_cashin_chanel_id();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
      
      $data['total_channels'] = count($data['cashin_channel_x_merchant']);
      $data['active_channels'] = 0; $data['inactive_channels'] = 0;
      foreach ($data['cashin_channel_x_merchant'] as $row) {
         if ($row->c_status == 'Active') $data['active_channels']++;
         else $data['inactive_channels']++;
      }
      $this->load->view('merchant/setting-fee', $data);
   }

   public function createSettingCashinFee()
   {
      $rules = [
         ['field' => 'ref_cashinChannelId', 'label' => 'Channel ID', 'rules' => 'required'],
         ['field' => 'c_cashinChannelGroup', 'label' => 'Channel Group', 'rules' => 'required'],
         ['field' => 'c_externalIdDefault', 'label' => 'External Default', 'rules' => 'required'],
         ['field' => 'c_feeType', 'label' => 'Fee Type', 'rules' => 'required'],
         ['field' => 'c_fee', 'label' => 'Fee', 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange', 'label' => 'Fee Percentage', 'rules' => 'required|numeric'],
         ['field' => 'c_settlementInterval', 'label' => 'Settlement Interval', 'rules' => 'required|numeric'],
         ['field' => 'c_amountMin', 'label' => 'Amount Min', 'rules' => 'required|numeric'],
         ['field' => 'c_amountMax', 'label' => 'Amount Max', 'rules' => 'required|numeric'],
         ['field' => 'c_status', 'label' => 'Status', 'rules' => 'required'],
      ];
      $this->form_validation->set_rules($rules);

      $merchant_id = $this->input->post('ref_merchantId');
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      if ($this->form_validation->run() == FALSE) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => validation_errors()]);
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      } else {
         $data = array(
            'ref_merchantId' => $merchant_id,
            'c_cashinChannelGroup' => $this->input->post('c_cashinChannelGroup'),
            'ref_cashinChannelId' => $this->input->post('ref_cashinChannelId'),
            'c_externalIdDefault' => $this->input->post('c_externalIdDefault'),
            'c_feeType' => $this->input->post('c_feeType'),
            'c_fee' => $this->input->post('c_fee'),
            'c_feePercetange' => $this->input->post('c_feePercetange'),
            'c_settlementInterval' => $this->input->post('c_settlementInterval'),
            'c_amountMin' => $this->input->post('c_amountMin'),
            'c_amountMax' => $this->input->post('c_amountMax'),
            'c_status' => $this->input->post('c_status'),
         );
         $result = $this->Chanel->createCashinChannelXMerchant($data);
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Data successfully inserted']);
                return;
            }
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to insert data due to a system constraint. Please verify your input or contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to add cashin fee settings.';
            } elseif ($code == 1062) {
               $msg = 'Failed to insert data: A fee configuration for this channel already exists.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      }
   }

   public function settingcashoutfee()
   {
      $data['title'] = 'Setting Cashout Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }
      $merchant = $this->Merchant->get_merchant_by_id($data['merchant_id']);
      $data['merchant_name'] = $merchant ? $merchant['c_name'] : 'Unknown';

      if ($this->input->is_ajax_request()) {
         try {
            return $this->Merchant->get_fee_datatables_handler('cashout', $data['merchant_id']);
         } catch (Throwable $e) {
            log_message('error', 'Cashout Fee AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => "Error"
            ]);
            return;
         }
      }

      $data['breadcrumb_replace'] = [$data['merchant_id'] => $data['merchant_name']];
      $data['cashout_channel_x_merchant'] = $this->Merchant->get_cashout_channel_x_merchant_by_merchant_id($data['merchant_id']);
      $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
      $data['channel_ids'] = $this->Chanel->get_cashout_chanel_id();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();

      $data['total_channels'] = count($data['cashout_channel_x_merchant']);
      $data['active_channels'] = 0; $data['inactive_channels'] = 0;
      foreach ($data['cashout_channel_x_merchant'] as $row) {
         if ($row->c_status == 'Active') $data['active_channels']++;
      }
      $this->load->view('merchant/setting-cashout-fee', $data);
   }


   public function bulkCreateSettingCashinFee($merchant_id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      $rules = [
         ['field' => 'c_cashinChannelGroup',    'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
         ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
         ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
         ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
         ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
         ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      } else {
         $channelGroups = $this->Chanel->get_cashin_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashinChannelGroup'));
         
         if (empty($channelGroups)) {
             $msg = 'Tidak ada channel yang ditemukan untuk grup dan provider ini.';
             if ($this->input->is_ajax_request()) {
                 echo json_encode(['status' => 'error', 'message' => $msg]);
                 return;
             }
             $this->session->set_flashdata('error', $msg);
             redirect('merchant/setting-cashin-fee/' . $merchant_id);
             return;
         }

         // Check for existing duplicates
         $channelIds = array_column($channelGroups, 'id');
         $existing = $this->db->where('ref_merchantId', $merchant_id)
                              ->where_in('ref_cashinChannelId', $channelIds)
                              ->get('cashin_channel_x_merchant')
                              ->result_array();
                              
         if (!empty($existing)) {
             $existingIds = array_unique(array_column($existing, 'ref_cashinChannelId'));
             $duplicates = [];
             foreach ($existingIds as $eid) {
                 $duplicates[] = "<strong>{$eid}</strong>";
             }
             $msg = 'Gagal menyimpan pengaturan Bulk. Channel berikut telah terdaftar untuk merchant ini:<br> • ' . implode('<br> • ', $duplicates) . '<br><br>Mohon gunakan opsi <em>Edit Mapping</em> jika ingin memperbarui data yang sudah ada.';
             
             if ($this->input->is_ajax_request()) {
                 echo json_encode(['status' => 'error', 'message' => $msg]);
                 return;
             }
             $this->session->set_flashdata('error', $msg);
             redirect('merchant/setting-cashin-fee/' . $merchant_id);
             return;
         }

         $data = [];
         foreach ($channelGroups as $row) {
            $data []= [
               'ref_merchantId'           => $merchant_id,
               'c_cashinChannelGroup'     => $this->input->post('c_cashinChannelGroup'),
               'ref_cashinChannelId'      => $row->id,
               'c_externalIdDefault'      => $this->input->post('c_externalIdDefault'),
               'c_feeType'                => $this->input->post('c_feeType'),
               'c_fee'                    => $this->input->post('c_fee'),
               'c_feePercetange'          => $this->input->post('c_feePercetange'),
               'c_settlementInterval'     => $this->input->post('c_settlementInterval') !== null && $this->input->post('c_settlementInterval') !== '' ? $this->input->post('c_settlementInterval') : 0,
               'c_amountMin'              => $this->input->post('c_amountMin') !== null && $this->input->post('c_amountMin') !== '' ? $this->input->post('c_amountMin') : 1000,
               'c_amountMax'              => $this->input->post('c_amountMax') !== null && $this->input->post('c_amountMax') !== '' ? $this->input->post('c_amountMax') : 10000000,
               'c_status'                 => $this->input->post('c_status') !== null && $this->input->post('c_status') !== '' ? $this->input->post('c_status') : 'Active',
            ];
         }

         $result = $this->Chanel->bulkCreateCashinChannelXMerchant($data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
               $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to perform bulk cashin fee settings.');
            } else {
               $this->session->set_flashdata('error', 'Unable to complete bulk insertion due to a system constraint. Please contact technical support.');
            }
         }

         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      }
   }

   public function getCashinChannelGroups()
   {
      $rules = [
         ['field' => 'c_cashinChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         echo json_encode($errors);
         return;
      } 

      $channelGroups = $this->Chanel->get_cashin_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashinChannelGroup'));
      echo json_encode($channelGroups);
   }

   public function editSettingCashinFee($merchant_id = NULL, $id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);
      
      $rules = [
         ['field' => 'ref_cashinChannelId',      'label' => 'Channel ID',          'rules' => 'required'],
         ['field' => 'c_cashinChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
         ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
         ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
         ['field' => 'c_settlementInterval',     'label' => 'Settlement Interval', 'rules' => 'required|numeric'],
         ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
         ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
         ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      } else {
         $data = array(
               'c_cashinChannelGroup'  => $this->input->post('c_cashinChannelGroup'),
               'ref_cashinChannelId'   => $this->input->post('ref_cashinChannelId'),
               'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
               'c_feeType'             => $this->input->post('c_feeType'),
               'c_fee'                 => $this->input->post('c_fee'),
               'c_feePercetange'       => $this->input->post('c_feePercetange'),
               'c_settlementInterval'  => $this->input->post('c_settlementInterval'),
               'c_amountMin'           => $this->input->post('c_amountMin'),
               'c_amountMax'           => $this->input->post('c_amountMax'),
               'c_status'              => $this->input->post('c_status'),
         );

         $result = $this->Chanel->updateCashinChannelXMerchant($id, $data);
         if ($result === true) {
               $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
               $code = isset($result['code']) ? $result['code'] : 0;
               if ($code == 1142) {
                  $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to update cashin fee settings.');
               } elseif ($code == 1062) {
                  $this->session->set_flashdata('error', 'Failed to update data: A fee configuration for this channel already exists.');
               } else {
                  $this->session->set_flashdata('error', 'Unable to update data due to a system constraint. Please verify your input or contact technical support.');
               }
         }

         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      }
   }

   public function deleteSettingCashinFee($merchant_id = NULL, $id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      $result = $this->Chanel->deleteCashinChannelXMerchant($id);

      if ($result === true) {
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete cashin fee settings.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this setting because it is currently linked to existing transaction records.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete setting due to a system constraint. Please contact technical support.');
         }
      }

      redirect('merchant/setting-cashin-fee/' . $merchant_id);
   }

   public function createSettingCashoutFee()
   {
      $merchant_id = $this->input->post('ref_merchantId');
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      $rules = [
         ['field' => 'ref_cashoutChannelId',      'label' => 'Channel ID',          'rules' => 'required'],
         ['field' => 'c_cashoutChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
         ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
         ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
         ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
         ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
         ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => validation_errors()]);
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      } else {
         $data = array(
            'ref_merchantId'           => $merchant_id,
            'c_cashoutChannelGroup'    => $this->input->post('c_cashoutChannelGroup'),
            'ref_cashoutChannelId'     => $this->input->post('ref_cashoutChannelId'),
            'c_externalIdDefault'      => $this->input->post('c_externalIdDefault'),
            'c_feeType'                => $this->input->post('c_feeType'),
            'c_fee'                    => $this->input->post('c_fee'),
            'c_feePercetange'          => $this->input->post('c_feePercetange'),
            'c_amountMin'              => $this->input->post('c_amountMin'),
            'c_amountMax'              => $this->input->post('c_amountMax'),
            'c_status'                 => $this->input->post('c_status'),
         );

         $result = $this->Chanel->createCashoutChannelXMerchant($data);
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Data successfully inserted']);
                return;
            }
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to insert data due to a system constraint. Please verify your input or contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to add cashout fee settings.';
            } elseif ($code == 1062) {
               $msg = 'Failed to insert data: A fee configuration for this channel already exists.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function bulkCreateSettingCashoutFee($merchant_id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      $rules = [
         ['field' => 'c_cashoutChannelGroup',    'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
         ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
         ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
         ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
         ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
         ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => validation_errors()]);
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      } else {
         $channelGroups = $this->Chanel->get_cashout_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashoutChannelGroup'));
         
         if (empty($channelGroups)) {
             if ($this->input->is_ajax_request()) {
                 echo json_encode(['status' => 'error', 'message' => 'Tidak ada channel yang ditemukan untuk grup dan provider ini.']);
                 return;
             }
             $this->session->set_flashdata('error', 'Tidak ada channel yang ditemukan untuk grup dan provider ini.');
             redirect('merchant/setting-cashout-fee/' . $merchant_id);
             return;
         }

         // Check for existing duplicates
         $channelIds = array_column($channelGroups, 'id');
         $existing = $this->db->where('ref_merchantId', $merchant_id)
                              ->where_in('ref_cashoutChannelId', $channelIds)
                              ->get('cashout_channel_x_merchant')
                              ->result_array();
                              
         if (!empty($existing)) {
             $existingIds = array_unique(array_column($existing, 'ref_cashoutChannelId'));
             $duplicates = [];
             foreach ($existingIds as $eid) {
                 $duplicates[] = "<strong>{$eid}</strong>";
             }
             $msg = 'Gagal menyimpan pengaturan Bulk. Channel berikut telah terdaftar untuk merchant ini:<br> • ' . implode('<br> • ', $duplicates) . '<br><br>Mohon gunakan opsi <em>Edit Mapping</em> jika ingin memperbarui data yang sudah ada.';
             
             if ($this->input->is_ajax_request()) {
                 echo json_encode(['status' => 'error', 'message' => $msg]);
                 return;
             }
             $this->session->set_flashdata('error', $msg);
             redirect('merchant/setting-cashout-fee/' . $merchant_id);
             return;
         }

         $data = [];
         foreach ($channelGroups as $row) {
            $data []= [
               'ref_merchantId'           => $merchant_id,
               'c_cashoutChannelGroup'    => $this->input->post('c_cashoutChannelGroup'),
               'ref_cashoutChannelId'     => $row->id,
               'c_externalIdDefault'      => $this->input->post('c_externalIdDefault'),
               'c_feeType'                => $this->input->post('c_feeType'),
               'c_fee'                    => $this->input->post('c_fee'),
               'c_feePercetange'          => $this->input->post('c_feePercetange'),
               'c_amountMin'              => $this->input->post('c_amountMin'),
               'c_amountMax'              => $this->input->post('c_amountMax'),
               'c_status'                 => $this->input->post('c_status'),     
            ];
         }

         $result = $this->Chanel->bulkCreateCashoutChannelXMerchant($data);
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Data successfully inserted']);
                return;
            }
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to complete bulk insertion due to a system constraint. Please contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to perform bulk cashout fee settings.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function getCashoutChannelGroups()
   {
      $rules = [
         ['field' => 'c_cashoutChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         echo json_encode($errors);
         return;
      } 

      $channelGroups = $this->Chanel->get_cashout_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashoutChannelGroup'));
      echo json_encode($channelGroups);
   }

   public function editSettingCashoutFee($merchant_id = NULL, $id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      $rules = [
         ['field' => 'ref_cashoutChannelId',      'label' => 'Channel ID',          'rules' => 'required'],
         ['field' => 'c_cashoutChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
         ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
         ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
         ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
         ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
         ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
         ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      } else {
         $data = array(
               'c_cashoutChannelGroup'  => $this->input->post('c_cashoutChannelGroup'),
               'ref_cashoutChannelId'   => $this->input->post('ref_cashoutChannelId'),
               'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
               'c_feeType'             => $this->input->post('c_feeType'),
               'c_fee'                 => $this->input->post('c_fee'),
               'c_feePercetange'       => $this->input->post('c_feePercetange'),
               'c_amountMin'           => $this->input->post('c_amountMin'),
               'c_amountMax'           => $this->input->post('c_amountMax'),
               'c_status'              => $this->input->post('c_status'),
         );

         $result = $this->Chanel->updateCashoutChannelXMerchant($id, $data);
         if ($result === true) {
               $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
               $code = isset($result['code']) ? $result['code'] : 0;
               if ($code == 1142) {
                  $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to update cashout fee settings.');
               } elseif ($code == 1062) {
                  $this->session->set_flashdata('error', 'Failed to update data: A fee configuration for this channel already exists.');
               } else {
                  $this->session->set_flashdata('error', 'Unable to update data due to a system constraint. Please verify your input or contact technical support.');
               }
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function deleteSettingCashoutFee($merchant_id = NULL, $id = NULL)
   {
      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      $result = $this->Chanel->deleteCashoutChannelXMerchant($id);

      if ($result === true) {
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete cashout fee settings.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this setting because it is currently linked to existing transaction records.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete setting due to a system constraint. Please contact technical support.');
         }
      }

      redirect('merchant/setting-cashout-fee/' . $merchant_id);
   }

}
