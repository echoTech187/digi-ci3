<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk pengelolaan Merchant, Supervisor, dan Delegation.
 * Bagian dari refactoring AdminMerchant untuk mengikuti standar OOP yang lebih modular.
 */
class MerchantManagementController extends CI_Controller
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
      $this->load->model('Merchant');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function merchant()
   {
      if ($this->input->is_ajax_request()) {
         try {
            $this->load->library('datatables');
            $where = ['m.c_merchantLevel' => 0];
            $ref_entity = $this->session->userdata('ref_entity');
            if (!empty($ref_entity)) {
               $where['m.ref_entity'] = $ref_entity;
            }

            $role_id = $this->session->userdata('role');
            $hasBalancePermission = $this->rbac->has_permission($role_id, 'balance_merchant_module');

            return $this->Merchant->getMerchantDataTable($where, $hasBalancePermission);
         } catch (Throwable $e) {
            log_message('error', 'Merchant AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => $e->getMessage()
            ]);
            return;
         }
      }

      $data['title'] = 'Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_merchant = $this->input->get('search_merchant') ?: $this->input->post('search_merchant');
      if ($search_merchant !== null) {
         $this->session->set_userdata('search_merchant', $search_merchant);
      } else {
         $search_merchant = $this->session->userdata('search_merchant');
      }

      $data['cashin_channels'] = $this->Merchant->get_cashin_channel();
      $data['cashout_channels'] = $this->Merchant->get_cashout_channel();

      $this->db->select('SUM(c_balanceTotal) as total_balance, SUM(c_balanceHold) as total_hold, COUNT(id) as total_merchants');
      $this->db->from('merchant');
      $this->db->where('c_status', 'Active');
      $this->db->where('c_merchantLevel', 0);
      $ref_entity = $this->session->userdata('ref_entity');
      if (!empty($ref_entity)) {
         $this->db->where('ref_entity', $ref_entity);
      }
      if (!empty($search_merchant)) {
         $this->db->group_start();
         $this->db->like('c_name', $search_merchant);
         $this->db->or_like('id', $search_merchant);
         $this->db->or_like('c_email', $search_merchant);
         $this->db->group_end();
      }

      $summary = $this->db->get()->row();
      $data['total_balance'] = (float)($summary->total_balance ?? 0);
      $data['total_hold'] = (float)($summary->total_hold ?? 0);
      $data['total_available'] = $data['total_balance'] - $data['total_hold'];
      $data['total_merchants'] = (int)($summary->total_merchants ?? 0);

      $this->load->view('merchant/index', $data);
   }

   public function resetMerchant()
   {
      $this->session->unset_userdata('search_merchant');
      redirect("admin/merchant");
   }

   public function merchant_spv()
   {
      if ($this->input->is_ajax_request()) {
         try {
            return $this->Merchant->get_merchant_spv_handler();
         } catch (Exception $e) {
            log_message('error', 'Supervisor AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => $e->getMessage()
            ]);
            return;
         }
      }

      $data['title'] = 'Merchant Supervisor';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['total_merchants_assigned'] = $this->db->where('c_refSupervisor IS NOT NULL')->count_all_results('merchant');

      $this->load->view('merchantspv/index', $data);
   }

   public function listMerchants($supervisorId)
   {
      if (!$supervisorId) {
         $this->session->set_flashdata('error', 'Supervisor ID not found.');
         redirect('admin/merchant_spv');
      }

      if ($this->input->is_ajax_request()) {
         try {
            $role_id = $this->session->userdata('role');
            $hasBalancePermission = $this->rbac->has_permission($role_id, 'balance_merchant_module');
            return $this->Merchant->get_merchants_by_supervisor_handler($supervisorId, $hasBalancePermission);
         } catch (Exception $e) {
            log_message('error', 'Supervisor Assigned Merchants AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => []
            ]);
            return;
         }
      }

      $data['title'] = 'Merchant Supervisor - List Merchants';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['supervisor_id'] = $supervisorId;
      $data['merchants'] = $this->db->get_where('merchant', ['c_refSupervisor' => $supervisorId])->result_array();

      $supervisor = $this->db->get_where('merchant_supervisor', ['id' => $supervisorId])->row_array();
      $supervisor_name = $supervisor ? $supervisor['c_name'] : 'Supervisor';
      $data['breadcrumb_replace'] = [$supervisorId => $supervisor_name];

      $this->load->view('merchantspv/list', $data);
   }

   public function addMerchant()
   {
      $data['title'] = 'Register New Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $this->load->view('merchant/add-merchant', $data);
   }

   public function registerMerchant()
   {
      $formValidationRules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_phoneNumber', 'label' => 'Merchant Phone', 'rules' => 'trim'],
         ['field' => 'c_password', 'label' => 'Merchant Password', 'rules' => 'trim|required'],
         ['field' => 'c_confirmPassword', 'label' => 'Merchant Confirm Password', 'rules' => 'trim|required|matches[c_password]'],
      ];

      $optionalFields = [
         'c_openapiUrlCallbackEwallet', 'c_openapiUrlCallbackVa', 'c_openapiStatus',
         'c_gvconnectBusinessId', 'c_gvconnectBusinessName',
         'c_openapiChannelVaDynamicCreate', 'c_openapiChannelVaDynamicQuery', 'c_openapiChannelVaDynamicCancel',
         'c_openapiChannelVaRecurringCreate', 'c_openapiChannelVaRecurringCancel',
         'c_openapiChannelQrisMpmDynamicCreate', 'c_openapiChannelQrisMpmDynamicQuery', 'c_openapiChannelQrisMpmDynamicCancel',
         'c_openapiChannelQrisMpmRecurringCreate', 'c_openapiChannelQrisMpmRecurringCancel',
         'c_openapiChannelTransferToBifast', 'c_openapiChannelTransferToBifastQuery', 'c_openapiChannelTransferToBifastCancel',
         'c_openapiChannelTransferToRealtimeOnline', 'c_openapiChannelTransferToRealtimeOnlineQuery', 'c_openapiChannelTransferToRealtimeOnlineCancel',
         'c_openApiChannelTransferToEmoney', 'c_openApiChannelTransferToEmoneyQuery', 'c_openApiChannelTransferToEmoneyCancel',
         'c_allowTransferFromDashboard',
      ];

      $this->form_validation->set_rules($formValidationRules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/addMerchant');
      } else {
         $this->load->library('MerchantRegistrationService', null, 'MerchantRegistrationService');
         try {
            $this->MerchantRegistrationService->registerMerchant($this->input->post(), $formValidationRules, $optionalFields);
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
         }
         redirect('admin/merchant');
      }
   }

   public function editMerchant($merchant_id)
   {
      $data['title'] = 'Edit Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Merchant->get_merchant_by_id($merchant_id);

      if (empty($data['merchant'])) {
         $this->session->set_flashdata('error', 'Merchant tidak ditemukan.');
         redirect('admin/merchant');
         return;
      }

      $data['breadcrumb_replace'] = [$merchant_id => $data['merchant']['c_name']];
      $this->load->view('merchant/edit-merchant', $data);
   }

   public function updateMerchant($merchant_id)
   {
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      $rules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_phoneNumber', 'label' => 'Merchant Phone', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackQrisMpm', 'label' => 'URL Callback Qris Mpm', 'rules' => 'valid_url'],
         ['field' => 'c_openapiUrlCallbackVa',      'label' => 'URL Callback VA',       'rules' => 'valid_url'],
         ['field' => 'c_openapiUrlCallbackEwallet', 'label' => 'URL Callback Ewallet',  'rules' => 'valid_url'],
         ['field' => 'c_openapiStatus',             'label' => 'OpenAPI Status',        'rules' => 'required'],
      ];
      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/editMerchant/' . $merchant_id);
      } else {
         $data = [
            'c_name' => $this->input->post('c_name'),
            'c_email' => $this->input->post('c_email'),
            'c_phoneNumber' => $this->input->post('c_phoneNumber'),
            'c_openapiUrlCallbackQrisMpm' => $this->input->post('c_openapiUrlCallbackQrisMpm'),
            'c_openapiUrlCallbackVa' => $this->input->post('c_openapiUrlCallbackVa'),
            'c_openapiUrlCallbackEwallet' => $this->input->post('c_openapiUrlCallbackEwallet'),
            'c_openapiIPAllow' => $this->input->post('c_openapiIPAllow'),
            'c_openapiStatus' => $this->input->post('c_openapiStatus'),
            'c_openapiChannelVaDynamicCreate' => $this->input->post('c_openapiChannelVaDynamicCreate') ? '1' : '0',
            'c_openapiChannelVaDynamicQuery' => $this->input->post('c_openapiChannelVaDynamicQuery') ? '1' : '0',
            'c_openapiChannelVaDynamicCancel' => $this->input->post('c_openapiChannelVaDynamicCancel') ? '1' : '0',
            'c_openapiChannelVaRecurringCreate' => $this->input->post('c_openapiChannelVaRecurringCreate') ? '1' : '0',
            'c_openapiChannelVaRecurringCancel' => $this->input->post('c_openapiChannelVaRecurringCancel') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicCreate' => $this->input->post('c_openapiChannelQrisMpmDynamicCreate') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicQuery' => $this->input->post('c_openapiChannelQrisMpmDynamicQuery') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicCancel' => $this->input->post('c_openapiChannelQrisMpmDynamicCancel') ? '1' : '0',
            'c_openapiChannelEwalletDynamicCreate' => $this->input->post('c_openapiChannelEwalletDynamicCreate') ? '1' : '0',
            'c_openapiChannelEwalletDynamicQuery' => $this->input->post('c_openapiChannelEwalletDynamicQuery') ? '1' : '0',
            'c_openapiChannelEwalletDynamicCancel' => $this->input->post('c_openapiChannelEwalletDynamicCancel') ? '1' : '0',
            'c_openapiChannelTransferToBifast' => $this->input->post('c_openapiChannelTransferToBifast') ? '1' : '0',
            'c_openapiChannelTransferToRealtimeOnline' => $this->input->post('c_openapiChannelTransferToRealtimeOnline') ? '1' : '0',
            'c_allowTransferFromDashboard' => $this->input->post('c_allowTransferFromDashboard') ? '1' : '0',
         ];

         $data['c_openapiSecurityType'] = !empty($data['c_openapiIPAllow']) ? 'Whitelist IP' : 'Not Both';

         $this->db->trans_begin();
         $this->Merchant->update_merchant($merchant_id, $data);
         
         $gvId = $this->input->post('c_gvconnectBusinessId');
         $gvName = $this->input->post('c_gvconnectBusinessName');
         if ($gvId !== null || $gvName !== null) {
            $updSub = [];
            if ($gvId !== null) $updSub['c_gvconnectBusinessId'] = $gvId;
            if ($gvName !== null) $updSub['c_gvconnectBusinessName'] = $gvName;
            $this->db->where('ref_merchantId', $merchant_id);
            $this->db->update('submerchant', $updSub);
         }

         if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Failed to update merchant.');
         } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Merchant successfully updated.');
         }
         redirect('admin/editMerchant/' . $merchant_id);
      }
   }

   public function registerMerchantSpv() 
   {
      $this->load->library('MerchantRegistrationService', null, 'MerchantRegistrationService');
      try {
         $this->MerchantRegistrationService->registerSupervisor($this->input->post());
         $this->session->set_flashdata('success', 'Merchant Supervisor Added Successfully.');
         redirect('admin/merchant_spv');
      } catch (Exception $e) {
         $this->session->set_flashdata('error', $e->getMessage());
         if ($e->getMessage() === 'Password not match') {
            redirect('admin/supervisorForm');
         } else {
            redirect('admin/merchant_spv');
         }
      }
   }

   public function deleteMerchantSpv($id)
   {
      if (!$id) {
         $this->session->set_flashdata('error', 'Supervisor ID missing.');
         redirect('admin/merchant_spv');
      }

      $this->db->trans_start();
      $this->db->where('c_refSupervisor', $id);
      $this->db->update('merchant', ['c_refSupervisor' => NULL]);
      $this->db->where('id', $id);
      $this->db->delete('merchant_supervisor');
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
         $this->session->set_flashdata('error', 'Failed to delete supervisor.');
      } else {
         $this->session->set_flashdata('success', 'Supervisor deleted successfully.');
      }
      redirect('admin/merchant_spv');
   }

   public function settingcashinfee()
   {
      $data['title'] = 'Setting Cashin Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
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

      $merchant_id = $this->uri->segment(3);
      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/settingcashinfee/' . $merchant_id);
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
         if ($this->Chanel->createCashinChannelXMerchant($data)) $this->session->set_flashdata('success', 'Success');
         else $this->session->set_flashdata('error', 'Failed');
         redirect('admin/settingcashinfee/' . $merchant_id);
      }
   }

   public function settingcashoutfee()
   {
      $data['title'] = 'Setting Cashout Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
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
         else $data['inactive_channels']++;
      }
      $this->load->view('merchant/setting-cashout-fee', $data);
   }

   public function searchMerchants()
   {
      if (ob_get_length()) ob_clean();
      $q = $this->input->get('q');
      $this->db->select('m.id, m.c_name, s.c_name as supervisor_name');
      $this->db->from('merchant m');
      $this->db->join('merchant_supervisor s', 'm.c_refSupervisor = s.id', 'left');
      $this->db->where('m.c_refSupervisor IS NULL');
      if (!empty($q)) {
         $this->db->group_start();
         $this->db->like('m.c_name', $q);
         $this->db->or_like('m.id', $q);
         $this->db->group_end();
      }
      $this->db->limit(30); 
      $results = $this->db->get()->result();
      $data = [];
      foreach ($results as $row) {
         $data[] = ['id' => $row->id, 'name' => $row->c_name];
      }
      $this->output->set_content_type('application/json')->set_output(json_encode($data));
   }

   public function fetchMerchantPermissions($merchantId)
   {
      if (!$merchantId) {
         echo json_encode(['status' => 'error', 'message' => 'ID missing']);
         return;
      }
      $all_permissions = $this->Merchant->get_rbac_permissions();
      $explicit_grants = $this->Merchant->get_merchant_explicit_grants($merchantId);
      $grants_map = [];
      foreach ($explicit_grants as $g) $grants_map[$g->ref_permissionId] = $g->c_isAllowed;
      $data = [];
      foreach ($all_permissions as $p) {
         $status = 'Deny';
         if (isset($grants_map[$p->id])) $status = ($grants_map[$p->id] == 1) ? 'Grant' : 'Deny';
         $data[] = ['id' => $p->id, 'name' => $p->c_code, 'label' => $p->c_name, 'status' => $status];
      }
      $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
   }

   public function saveDelegation($merchantId)
   {
      if (!$merchantId) {
         echo json_encode(['status' => 'error', 'message' => 'ID missing']);
         return;
      }
      $permissions = $this->input->post('permissions');
      if (!empty($permissions) && is_array($permissions)) {
         $successCount = 0;
         foreach ($permissions as $permId => $action) {
            if ($this->Merchant->save_merchant_delegation($merchantId, $permId, $action)) $successCount++;
         }
         echo json_encode(['status' => 'success', 'message' => "$successCount updated"]);
      } else {
         echo json_encode(['status' => 'error', 'message' => 'No data']);
      }
   }
   public function createCreditBalance()
   {
      is_logged_in();
      $isAjax = $this->input->is_ajax_request();

      $merchantId  = $this->input->post('merchantId');
      $channelId   = $this->input->post('channelId');
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountCredit');

      if (empty($merchantId) || empty($channelId) || empty($description) || empty($amount)) {
         $errorMessage = 'All fields are required.';
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
            return;
         }
         $this->session->set_flashdata('error_message', $errorMessage);
         redirect('admin/merchant');
         return;
      }

      $internalRequestBody = [
         "merchantId" => $merchantId,
         "channelId"  => $channelId,
         'description' => $description,
         'amount'      => $amount
      ];

      $internalUrlHit = $this->internalUrlHit . "/Merchant/creditBalance";
      $response = $this->_internalCurl($internalUrlHit, $internalRequestBody);

      if ($isAjax) {
         header('Content-Type: application/json');
         if ($response !== false) {
            echo json_encode(['status' => 'success', 'message' => 'Credit balance successfully added.']);
         } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to reach internal service.']);
         }
         return;
      }

      if ($response !== false) {
         $this->session->set_flashdata('success', 'Credit Balance Success.');
      } else {
         $this->session->set_flashdata('error', 'Failed to send data.');
      }
      redirect('admin/merchant');
   }

   public function createDebitBalance()
   {
      is_logged_in();
      $isAjax = $this->input->is_ajax_request();

      $merchantId  = $this->input->post('merchantIdDebit');
      $channelId   = $this->input->post('channelId');
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountDebit');

      if (empty($merchantId) || empty($channelId) || empty($description) || empty($amount)) {
         $errorMessage = 'All fields are required.';
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
            return;
         }
         $this->session->set_flashdata('error_message', $errorMessage);
         redirect('admin/merchant');
         return;
      }

      $internalRequestBody = [
         "merchantId" => $merchantId,
         "channelId"  => $channelId,
         'description' => $description,
         'amount'      => $amount
      ];

      $internalUrlHit = $this->internalUrlHit . "/Merchant/debitBalance";
      $response = $this->_internalCurl($internalUrlHit, $internalRequestBody);

      if ($isAjax) {
         header('Content-Type: application/json');
         if ($response !== false) {
            echo json_encode(['status' => 'success', 'message' => 'Debit balance successfully processed.']);
         } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to reach internal service.']);
         }
         return;
      }

      if ($response !== false) {
         $this->session->set_flashdata('success', 'Debit Balance Success.');
      } else {
         $this->session->set_flashdata('error', 'Failed to send data.');
      }
      redirect('admin/merchant');
   }

   /**
    * Helper for internal CURL requests
    */
   private function _internalCurl($url, $data)
   {
      $ch = curl_init();
      curl_setopt_array($ch, [
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($data),
         CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
      ]);
      $response = curl_exec($ch);
      curl_close($ch);
      return $response;
   }
}
