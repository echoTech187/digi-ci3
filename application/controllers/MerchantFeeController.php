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
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';



      $merchant_id = $this->input->post('ref_merchantId');
      if (!$merchant_id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID is required.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
         return;
      }

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

      if ($this->form_validation->run() == FALSE) {
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => $clean_error ?: 'Validation failed.'
             ]));
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
         return;
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => true,
                   'message' => 'Data successfully inserted'
                ]));
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => false,
                   'message' => $msg
                ]));
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
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      // Parameter Key Aliasing
      if (isset($_POST['channel_group']) && empty($_POST['c_cashinChannelGroup'])) $_POST['c_cashinChannelGroup'] = $_POST['channel_group'];
      if (isset($_POST['external_default']) && empty($_POST['c_externalIdDefault'])) $_POST['c_externalIdDefault'] = $_POST['external_default'];
      if (isset($_POST['fee_type']) && empty($_POST['c_feeType'])) $_POST['c_feeType'] = $_POST['fee_type'];
      if (isset($_POST['fee']) && empty($_POST['c_fee'])) $_POST['c_fee'] = $_POST['fee'];
      if (isset($_POST['fee_percentage']) && empty($_POST['c_feePercetange'])) $_POST['c_feePercetange'] = $_POST['fee_percentage'];
      if (isset($_POST['amount_min']) && empty($_POST['c_amountMin'])) $_POST['c_amountMin'] = $_POST['amount_min'];
      if (isset($_POST['amount_max']) && empty($_POST['c_amountMax'])) $_POST['c_amountMax'] = $_POST['amount_max'];
      if (isset($_POST['status']) && empty($_POST['c_status'])) $_POST['c_status'] = $_POST['status'];

      if (!$merchant_id) $merchant_id = $this->input->post('ref_merchantId') ?: $this->uri->segment(4);
      if (!$merchant_id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID is required.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
         return;
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
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => $clean_error ?: 'Validation failed.'
            ]));
            return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
         return;
      } else {
         $channelGroups = $this->Chanel->get_cashin_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashinChannelGroup'));
         
         if (empty($channelGroups)) {
             $msg = 'No channels found for the selected group and provider.';
             if ($is_api_request) {
                 $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
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
             $msg = 'Failed to bulk insert: Channels already configured for this merchant (' . implode(', ', $existingIds) . ').';
             
             if ($is_api_request) {
                 $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => true,
                   'message' => 'Bulk cashin fee settings successfully created.'
                ]));
                return;
            }
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to complete bulk insertion due to a system constraint.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to perform bulk cashin fee settings.';
            }
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => false,
                   'message' => $msg
                ]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      }
   }

   public function getCashinChannelGroups()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $external_id = $this->input->post('c_externalIdDefault') ?: $this->input->get('external_id');
      $channel_group = $this->input->post('c_cashinChannelGroup') ?: $this->input->get('channel_group');

      if ($external_id && $channel_group) {
         $channels = $this->Chanel->get_cashin_channels($external_id, $channel_group);
         return $this->output->set_content_type('application/json')->set_output(json_encode($channels));
      }

      $groups = $this->Chanel->get_cashin_chanel_group();
      return $this->output->set_content_type('application/json')->set_output(json_encode($groups));
   }

   public function editSettingCashinFee($merchant_id = NULL, $id = NULL)
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      if (!$merchant_id || !$id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID or Channel ID missing.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID or Channel ID missing.');
         redirect('merchant/manage');
         return;
      }

      // Resolve $id if string channel code (e.g. va_bni)
      $existing = null;
      if (is_numeric($id)) {
         $existing = $this->db->get_where('cashin_channel_x_merchant', ['id' => $id])->row_array();
      }
      if (!$existing && $merchant_id) {
         $existing = $this->db->get_where('cashin_channel_x_merchant', [
            'ref_merchantId' => $merchant_id,
            'ref_cashinChannelId' => $id
         ])->row_array();
      }
      if ($existing) {
         $id = $existing['id'];
         $fields_to_fill = ['ref_cashinChannelId', 'c_cashinChannelGroup', 'c_externalIdDefault', 'c_feeType', 'c_fee', 'c_feePercetange', 'c_settlementInterval', 'c_amountMin', 'c_amountMax', 'c_status'];
         foreach ($fields_to_fill as $f) {
            if ($this->input->post($f) === null && isset($existing[$f])) {
               $_POST[$f] = $existing[$f];
            }
         }
      } else if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => false,
            'message' => 'Cashin fee setting for channel (' . $id . ') not found for merchant (' . $merchant_id . ').'
         ]));
         return;
      }




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
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => $clean_error ?: 'Validation failed.'
            ]));
            return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashin-fee/' . $merchant_id);
         return;
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
            if ($is_api_request) {
               $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => true,
                  'message' => 'Cashin fee setting updated successfully.'
               ]));
               return;
            }
            $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to update data due to a system constraint.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to update cashin fee settings.';
            } elseif ($code == 1062) {
               $msg = 'Failed to update data: A fee configuration for this channel already exists.';
            }
            if ($is_api_request) {
               $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => false,
                  'message' => $msg
               ]));
               return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashin-fee/' . $merchant_id);
      }
   }

   public function deleteSettingCashinFee($merchant_id = NULL, $id = NULL)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      if (!is_numeric($id) && $merchant_id) {
         $row = $this->db->get_where('cashin_channel_x_merchant', [
            'ref_merchantId' => $merchant_id,
            'ref_cashinChannelId' => $id
         ])->row_array();
         if ($row) {
            $id = $row['id'];
         }
      }

      $result = $this->Chanel->deleteCashinChannelXMerchant($id);

      if ($result === true) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Data successfully deleted.'
            ]));
            return;
         }
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to delete setting due to a system constraint.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to delete cashin fee settings.';
         } elseif ($code == 1451) {
            $msg = 'Cannot delete this setting because it is currently linked to existing transaction records.';
         }
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => $msg
            ]));
            return;
         }
         $this->session->set_flashdata('error', $msg);
      }

      redirect('merchant/setting-cashin-fee/' . $merchant_id);
   }

   public function createSettingCashoutFee()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';




      $merchant_id = $this->input->post('ref_merchantId');
      if (!$merchant_id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID is required.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
         return;
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
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => $clean_error ?: 'Validation failed.'
             ]));
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
         return;
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => true,
                   'message' => 'Data successfully inserted'
                ]));
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => false,
                   'message' => $msg
                ]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function bulkCreateSettingCashoutFee($merchant_id = NULL)
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$merchant_id) $merchant_id = $this->input->post('ref_merchantId') ?: $this->uri->segment(4);
      if (!$merchant_id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID is required.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
         return;
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
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => $clean_error ?: 'Validation failed.'
             ]));
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
         return;
      } else {
         $channelGroups = $this->Chanel->get_cashout_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashoutChannelGroup'));
         
         if (empty($channelGroups)) {
             $msg = 'No channels found for the selected group and provider.';
             if ($is_api_request) {
                 $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
                 return;
             }
             $this->session->set_flashdata('error', $msg);
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
             $msg = 'Failed to bulk insert: Channels already configured for this merchant (' . implode(', ', $existingIds) . ').';
             
             if ($is_api_request) {
                 $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
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
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => true,
                   'message' => 'Bulk cashout fee settings successfully created.'
                ]));
                return;
            }
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to complete bulk insertion due to a system constraint.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to perform bulk cashout fee settings.';
            }
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                   'status' => false,
                   'message' => $msg
                ]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function getCashoutChannelGroups()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $external_id = $this->input->post('c_externalIdDefault') ?: $this->input->get('external_id');
      $channel_group = $this->input->post('c_cashoutChannelGroup') ?: $this->input->get('channel_group');

      if ($external_id && $channel_group) {
         $channels = $this->Chanel->get_cashout_channels($external_id, $channel_group);
         return $this->output->set_content_type('application/json')->set_output(json_encode($channels));
      }

      $groups = $this->Chanel->get_cashout_chanel_group();
      return $this->output->set_content_type('application/json')->set_output(json_encode($groups));
   }

   public function editSettingCashoutFee($merchant_id = NULL, $id = NULL)
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      if (!$merchant_id || !$id) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Merchant ID or Channel ID missing.'
            ]));
            return;
         }
         $this->session->set_flashdata('error', 'Merchant ID or Channel ID missing.');
         redirect('merchant/manage');
         return;
      }

      // Resolve $id if string channel code (e.g. bifast_bca)
      $existing = null;
      if (is_numeric($id)) {
         $existing = $this->db->get_where('cashout_channel_x_merchant', ['id' => $id])->row_array();
      }
      if (!$existing && $merchant_id) {
         $existing = $this->db->get_where('cashout_channel_x_merchant', [
            'ref_merchantId' => $merchant_id,
            'ref_cashoutChannelId' => $id
         ])->row_array();
      }
      if ($existing) {
         $id = $existing['id'];
         $fields_to_fill = ['ref_cashoutChannelId', 'c_cashoutChannelGroup', 'c_externalIdDefault', 'c_feeType', 'c_fee', 'c_feePercetange', 'c_amountMin', 'c_amountMax', 'c_status'];
         foreach ($fields_to_fill as $f) {
            if ($this->input->post($f) === null && isset($existing[$f])) {
               $_POST[$f] = $existing[$f];
            }
         }
      } else if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => false,
            'message' => 'Cashout fee setting for channel (' . $id . ') not found for merchant (' . $merchant_id . ').'
         ]));
         return;
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
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => $clean_error ?: 'Validation failed.'
            ]));
            return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/setting-cashout-fee/' . $merchant_id);
         return;
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
            if ($is_api_request) {
               $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => true,
                  'message' => 'Cashout fee setting updated successfully.'
               ]));
               return;
            }
            $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to update data due to a system constraint.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to update cashout fee settings.';
            } elseif ($code == 1062) {
               $msg = 'Failed to update data: A fee configuration for this channel already exists.';
            }
            if ($is_api_request) {
               $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => false,
                  'message' => $msg
               ]));
               return;
            }
            $this->session->set_flashdata('error', $msg);
         }

         redirect('merchant/setting-cashout-fee/' . $merchant_id);
      }
   }

   public function deleteSettingCashoutFee($merchant_id = NULL, $id = NULL)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$merchant_id) $merchant_id = $this->uri->segment(4);
      if (!$id) $id = $this->uri->segment(5);

      // Resolve $id if string channel code (e.g. bifast_bca)
      if (!is_numeric($id) && $merchant_id) {
         $row = $this->db->get_where('cashout_channel_x_merchant', [
            'ref_merchantId' => $merchant_id,
            'ref_cashoutChannelId' => $id
         ])->row_array();
         if ($row) {
            $id = $row['id'];
         }
      }

      $result = $this->Chanel->deleteCashoutChannelXMerchant($id);

      if ($result === true) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Data successfully deleted.'
            ]));
            return;
         }
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to delete setting due to a system constraint.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to delete cashout fee settings.';
         } elseif ($code == 1451) {
            $msg = 'Cannot delete this setting because it is currently linked to existing transaction records.';
         }
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => $msg
            ]));
            return;
         }
         $this->session->set_flashdata('error', $msg);
      }

      redirect('merchant/setting-cashout-fee/' . $merchant_id);
   }

}
