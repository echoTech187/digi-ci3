<?php defined('BASEPATH') or exit('No direct script access allowed');

global $internalUrlHit;
global $externalUrlHit;

class AdminMerchant extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();

      global $internalUrlHit;
      global $externalUrlHit;

      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->library('pagination');
      $this->load->library('rbac');
      $this->load->helper('cstring');

      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }
   public function merchant()
   {
      $serviceName = "view_merchants";
      $this->load->model('Merchant');

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $role_id = $this->session->userdata('role');
      if (!$this->rbac->has_permission($role_id, $serviceName)) {
         show_error('Access Denied', 403);
      }

      // Server-side DataTables Handler
      if ($this->input->is_ajax_request()) {
         $table = 'merchant m';
         $column_order = array(null, 'm.id', 'm.c_name', 'm.c_balanceTotal', 'm.c_status', null);
         $column_search = array('m.id', 'm.c_name', 'm.c_email');
         $order = array('m.id' => 'desc');
         
         $where = [];
         $where['m.c_merchantLevel'] = 0; // Filter to show only level 0 merchants here
         $ref_entity = $this->session->userdata('ref_entity');
         if (!empty($ref_entity)) {
             $where['m.ref_entity'] = $ref_entity;
         }

         $list = $this->Merchant->get_datatables($table, $column_order, $column_search, $order, $where);
         $dataItems = array();
         $no = $this->input->post('start');
         
         // Pre-check permission outside the loop to avoid N+1 queries
         $hasBalancePermission = $this->rbac->has_permission($role_id, 'balance_merchant_module');

         foreach ($list as $m) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['id'] = '<span class="fw-bold text-dark">#' . $m->id . '</span>';
            
            // Info Column
            $row['info'] = '
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark">' . $m->c_name . '</span>
                    <span class="text-muted small">' . $m->c_email . '</span>
                </div>';

            // Balance Column
            $available = (float)$m->c_balanceTotal - (float)$m->c_balanceHold;
            $row['balance'] = '
                <div class="d-flex flex-column" style="min-width: 150px;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Total:</span>
                        <span class="fw-bold text-dark">Rp ' . number_format($m->c_balanceTotal, 0, ',', '.') . '</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Hold:</span>
                        <span class="text-warning fw-bold">Rp ' . number_format($m->c_balanceHold, 0, ',', '.') . '</span>
                    </div>
                    <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                        <span class="text-muted">Available:</span>
                        <span class="text-success fw-bold">Rp ' . number_format($available, 0, ',', '.') . '</span>
                    </div>
                </div>';

            // Status Column
            $status_class = ($m->c_status == 'Active') ? 'bg-success' : 'bg-secondary';
            $openapi_class = ($m->c_openapiStatus == 'Active') ? 'text-success' : 'text-muted';
            $row['status'] = '
                <div class="d-flex flex-column">
                    <span class="mb-2 badge ' . $status_class . '-soft text-' . str_replace('-soft', '', $status_class) . ' rounded-pill px-3 py-1" style="width: fit-content;">
                        ' . $m->c_status . '
                    </span>
                    <span class="small ' . $openapi_class . ' d-flex align-items-center gap-1">
                        <i class="fas fa-plug me-1"></i>OpenAPI: ' . $m->c_openapiStatus . '
                    </span>
                </div>';

            // Action Column (Standardized Premium Style)
            $action_html = '
                <div class="dropdown">
                    <button class="btn btn-white btn-sm rounded shadow-none dropdown-toggle border px-3" type="button" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                        <i class="fas fa-ellipsis-v text-muted mr-2"></i>Actions
                    </button>
                    <div class="dropdown-menu dropdown-menu-right border-0 shadow-lg p-2" style="min-width: 200px;">
                        <a class="dropdown-item rounded-2 py-2" href="' . base_url('admin/editMerchant/' . $m->id) . '">
                            <i class="fas fa-edit mr-2 text-info" style="width: 20px;"></i>Edit Merchant
                        </a>
                        <a class="dropdown-item rounded-2 py-2" href="' . base_url('admin/mutation/' . $m->id) . '">
                            <i class="fas fa-exchange-alt mr-2 text-primary" style="width: 20px;"></i>Mutation Log
                        </a>
                        <a class="dropdown-item rounded-2 py-2" href="' . base_url('admin/submerchant/' . $m->id) . '">
                            <i class="fas fa-users mr-2 text-success" style="width: 20px;"></i>Sub Accounts
                        </a>';

            if (isset($m->c_merchantLevel) && $m->c_merchantLevel == 0) {
                $action_html .= '
                        <button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#delegateModal" onClick="openDelegateModal(' . $m->id . ', \'' . addslashes($m->c_name) . '\')">
                            <i class="fas fa-key mr-2 text-warning" style="width: 20px;"></i>Delegate
                        </button>';
            }
            
            if ($hasBalancePermission) {
                $action_html .= '
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#creditBalanceModal" onClick="detail(' . $m->id . ', \'' . addslashes($m->c_name) . '\')">
                            <i class="fas fa-plus-circle mr-2 text-success" style="width: 20px;"></i>Credit Balance
                        </button>
                        <button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#debitBalanceModal" onClick="detaildebit(' . $m->id . ', \'' . addslashes($m->c_name) . '\')">
                            <i class="fas fa-minus-circle mr-2 text-danger" style="width: 20px;"></i>Debit Balance
                        </button>';
            }

            $action_html .= '
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rounded-2 py-2" href="' . base_url('admin/settingcashinfee/' . $m->id) . '">
                            <i class="fas fa-cog mr-2 text-secondary" style="width: 20px;"></i>Cashin Fee Settings
                        </a>
                        <a class="dropdown-item rounded-2 py-2" href="' . base_url('admin/settingcashoutfee/' . $m->id) . '">
                            <i class="fas fa-cog mr-2 text-secondary" style="width: 20px;"></i>Cashout Fee Settings
                        </a>
                    </div>
                </div>';
            
            $row['action'] = $action_html;
            $dataItems[] = $row;
         }

         $output = array(
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => $this->Merchant->count_all_dt($table, $where),
            "recordsFiltered" => $this->Merchant->count_filtered($table, $column_order, $column_search, $order, $where),
            "data" => $dataItems,
         );
         echo json_encode($output);
         exit;
      }

      $data['title'] = 'Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_merchant = $this->input->post('search_merchant');
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
      $this->db->where('c_merchantLevel', 0); // Only Level 0 for summary
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

      $data['submerchants'] = []; // Not used by server-side table
      $data['start'] = 0;
      $data['pagination'] = '';

      // Load views
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('merchant/index', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function merchant_spv()
   {
      $serviceName = "view_merchants";
      $this->load->model('Merchant');

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $role_id = $this->session->userdata('role');
      if (!$this->rbac->has_permission($role_id, $serviceName)) {
         show_error('Access Denied', 403);
      }

      $data['title'] = 'Merchant Supervisor';
      $data['user'] = $this->Model_user->view_user()->row_array();


      $this->db->select('*');
      $this->db->from('merchant_supervisor');
      $this->db->where('c_status', 'Active');

      $query = $this->db->get();
      $data['merchant_spv'] = $query->result();

      // Summary data for KPI cards
      $data['total_supervisors'] = count($data['merchant_spv']);
      $data['total_merchants_assigned'] = $this->db->where('c_refSupervisor IS NOT NULL')->count_all_results('merchant');

      // Load views
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('merchantspv/index', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

    public function listMerchants($supervisorId)
    {
      if (!$supervisorId) {
         $this->session->set_flashdata('error', 'Supervisor ID not found.');
         redirect('admin/merchant_spv');
      }
      $this->load->model('Merchant');
      $data['title'] = 'Merchant Supervisor - List Merchants';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchants'] = $this->Merchant->getMerchantsBySupervisor($supervisorId);
      $data['supervisor_id'] = $supervisorId;

      // Breadcrumb override: Replace ID with Supervisor Name
      // We'll fetch the supervisor details
      $supervisor = $this->db->get_where('merchant_supervisor', ['id' => $supervisorId])->row_array();
      $supervisor_name = $supervisor ? $supervisor['c_name'] : 'Supervisor';
      $data['breadcrumb_replace'] = [
         $supervisorId => $supervisor_name
      ];

      // Load views
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('merchantspv/list', $data);
      $this->load->view('templates/user_footer.php', $data);
    }   


   public function registerMerchant()
   {
      $formValidationRules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_phoneNumber', 'label' => 'Merchant Phone', 'rules' => 'trim'],
         ['field' => 'c_openapiIPAllow', 'label' => 'Whitelist IP', 'rules' => 'trim|required'],
         ['field' => 'c_openapiUrlCallbackQrisMpm', 'label' => 'OpenAPI Callback URL QRIS MPM', 'rules' => 'trim|required'],
         ['field' => 'c_openapiUrlCallbackEwallet', 'label' => 'OpenAPI Callback URL E-wallet', 'rules' => 'trim|required'],
         ['field' => 'c_openapiUrlCallbackVa', 'label' => 'OpenAPI Callback URL VA', 'rules' => 'trim|required'],
         ['field' => 'c_openapiStatus', 'label' => 'OpenAPI Status', 'rules' => 'trim|required'],
         ['field' => 'c_password', 'label' => 'Merchant Password', 'rules' => 'trim|required'],
         ['field' => 'c_confirmPassword', 'label' => 'Merchant Confirm Password', 'rules' => 'trim|required|matches[c_password]'],
         ['field' => 'c_gvconnectBusinessId', 'label' => 'GVConnect Business ID', 'rules' => 'trim'],
         ['field' => 'c_gvconnectBusinessName', 'label' => 'GVConnect Business Name', 'rules' => 'trim'],
      ];

      $optionalFields = [
         'c_openapiChannelVaDynamicCreate',
         'c_openapiChannelVaDynamicQuery',
         'c_openapiChannelVaDynamicCancel',
         'c_openapiChannelVaRecurringCreate',
         'c_openapiChannelVaRecurringCancel',
         'c_openapiChannelQrisMpmDynamicCreate',
         'c_openapiChannelQrisMpmDynamicQuery',
         'c_openapiChannelQrisMpmDynamicCancel',
         'c_openapiChannelQrisMpmRecurringCreate',
         'c_openapiChannelQrisMpmRecurringCancel',
         // 'c_openapiChannelEwalletDynamicCreate',
         // 'c_openapiChannelEwalletDynamicQuery',
         // 'c_openapiChannelEwalletDynamicCancel',
         'c_openapiChannelTransferToBifast',
         'c_openapiChannelTransferToBifastQuery',
         'c_openapiChannelTransferToBifastCancel',
         'c_openapiChannelTransferToRealtimeOnline',
         'c_openapiChannelTransferToRealtimeOnlineQuery',
         'c_openapiChannelTransferToRealtimeOnlineCancel',
         'c_openApiChannelTransferToEmoney',
         'c_openApiChannelTransferToEmoneyQuery',
         'c_openApiChannelTransferToEmoneyCancel',
         'c_allowTransferFromDashboard',
      ];

      $this->form_validation->set_rules($formValidationRules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/merchant');
      } else {
         $this->load->library('MerchantRegistrationService');
         
         try {
             $this->MerchantRegistrationService->registerMerchant(
                 $this->input->post(),
                 $formValidationRules,
                 $optionalFields
             );
             
             $this->session->set_flashdata('success', 'Data successfully inserted');
         } catch (Exception $e) {
             $this->session->set_flashdata('error', $e->getMessage());
         }

         redirect('admin/merchant');
      }
   }

   public function registerMerchantSpv() 
   {
      $this->load->library('MerchantRegistrationService');

      try {
          // Lemparkan seluruh data POST ke service layer
          $this->MerchantRegistrationService->registerSupervisor($this->input->post());
          
          $this->session->set_flashdata('success', 'Merchant Supervisor Added Successfully.');
          redirect('admin/merchant_spv');

      } catch (Exception $e) {
          // Jika terjadi error (validasi password, merchant sudah diassign, db gagal, dll)
          $this->session->set_flashdata('error', $e->getMessage());
          
          if ($e->getMessage() === 'Password not match') {
              redirect('admin/supervisorForm'); // Menyesuaikan logic asli
          } else {
              redirect('admin/merchant_spv');
          }
      }
   }

   public function editMerchant($merchant_id)
   {
      $this->load->library('form_validation');
      $this->load->model('Merchant');

      // Cek session
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $data['title'] = 'Edit Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Merchant->get_merchant_by_id($merchant_id);

      // Jika merchant tidak ditemukan
      if (empty($data['merchant'])) {
         $this->session->set_flashdata('error', 'Merchant tidak ditemukan.');
         redirect('admin/merchant');
         return;
      }

      // Breadcrumb override: Replace ID with Merchant Name
      $data['breadcrumb_replace'] = [
         $merchant_id => $data['merchant']['c_name']
      ];

      // Load views
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('merchant/edit-merchant', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function updateMerchant($merchant_id)
   {
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }
      $this->load->model('Merchant');
      $this->load->library('form_validation');

      // Rules validasi
      $rules = [
         ['field' => 'c_openapiUrlCallbackQrisMpm', 'label' => 'URL Callback Qris Mpm', 'rules' => 'required|valid_url'],
         ['field' => 'c_openapiUrlCallbackVa',      'label' => 'URL Callback VA',       'rules' => 'required|valid_url'],
         ['field' => 'c_openapiUrlCallbackEwallet', 'label' => 'URL Callback Ewallet',  'rules' => 'required|valid_url'],
         ['field' => 'c_openapiStatus',             'label' => 'OpenAPI Status',        'rules' => 'required'],
      ];
      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/editMerchant/' . $merchant_id);
      } else {
         $data = [
            'c_openapiUrlCallbackQrisMpm' => $this->input->post('c_openapiUrlCallbackQrisMpm'),
            'c_openapiUrlCallbackVa'      => $this->input->post('c_openapiUrlCallbackVa'),
            'c_openapiUrlCallbackEwallet' => $this->input->post('c_openapiUrlCallbackEwallet'),
            'c_openapiIPAllow'            => $this->input->post('c_openapiIPAllow'),
            'c_openapiSecurityType'       => $this->input->post('c_openapiSecurityType'),
            'c_openapiStatus'             => $this->input->post('c_openapiStatus'),
         ];

         $result = $this->Merchant->update_merchant($merchant_id, $data);

         if ($result) {
            $this->session->set_flashdata('success', 'Merchant berhasil diupdate.');
         } else {
            $this->session->set_flashdata('error', 'Gagal mengupdate merchant.');
         }

         redirect('admin/editMerchant/' . $merchant_id);
      }
   }

   public function settingcashinfee()
   {
      $this->load->model('Merchant');

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $data['title'] = 'Setting Cashin Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }
      $merchant = $this->Merchant->get_merchant_by_id($data['merchant_id']);
      $data['merchant_name'] = $merchant ? $merchant['c_name'] : 'Unknown';

      // Breadcrumb override: Replace ID with Merchant Name
      $data['breadcrumb_replace'] = [
         $data['merchant_id'] => $data['merchant_name']
      ];

      $data['cashin_channel_x_merchant'] = $this->Merchant->get_cashin_channel_x_merchant_by_merchant_id($data['merchant_id']);
      $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
      $data['channel_ids'] = $this->Chanel->get_cashin_chanel_id();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
      
      // Calculate Summary
      $data['total_channels'] = count($data['cashin_channel_x_merchant']);
      $data['active_channels'] = 0;
      $data['inactive_channels'] = 0;
      foreach ($data['cashin_channel_x_merchant'] as $row) {
         if ($row->c_status == 'Active') {
            $data['active_channels']++;
         } else {
            $data['inactive_channels']++;
         }
      }
   
       // Load views
       $this->load->view('templates/user_header.php', $data);
       $this->load->view('templates/user_sidebar.php', $data);
       $this->load->view('templates/user_topbar.php', $data);
       $this->load->view('merchant/setting-fee', $data);
       $this->load->view('templates/user_footer.php', $data);
   }

   public function createSettingCashinFee()
   {
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

      $merchant_id = $this->uri->segment(3);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/settingcashinfee/' . $merchant_id);
      } else {
         $data = array(
            'ref_merchantId'        => $merchant_id,
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

         $result = $this->Chanel->createCashinChannelXMerchant($data);
         if ($result == true) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data:' . json_encode($result));
         }

         redirect('admin/settingcashinfee/' . $merchant_id);
      }
   }

   public function bulkCreateSettingCashinFee()
   {
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

      $merchant_id = $this->uri->segment(3);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/settingcashinfee/' . $merchant_id);
      } else {
         $channelGroups = $this->Chanel->get_cashin_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashinChannelGroup'));
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
               'c_amountMin'              => $this->input->post('c_amountMin'),
               'c_amountMax'              => $this->input->post('c_amountMax'),
               'c_status'                 => $this->input->post('c_status'),
            ];
         }

         $result = $this->Chanel->bulkCreateCashinChannelXMerchant($data);
         if ($result === true) {
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data:' . json_encode($result));
         }

         redirect('admin/settingcashinfee/' . $merchant_id);
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

   public function editSettingCashinFee()
   {
      $merchant_id = $this->uri->segment(3);
      $id = $this->uri->segment(4);
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
         redirect('admin/settingcashinfee/' . $merchant_id);
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
         if ($result == true) {
               $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
               $this->session->set_flashdata('error', 'Failed to update data: ' . json_encode($result));
         }

         redirect('admin/settingcashinfee/' . $merchant_id);
      }
   }

   public function deleteSettingCashinFee($id)
   {
      $merchant_id = $this->uri->segment(3);
      $id = $this->uri->segment(4);

      $result = $this->Chanel->deleteCashinChannelXMerchant($id);

      if ($result === true) {
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown DB error';
         $this->session->set_flashdata('error', 'Failed to delete data: ' . $errorMsg);
      }

      redirect('admin/settingcashinfee/' . $merchant_id);
   }

   // start setting cashout fee

   public function settingcashoutfee()
   {
      $this->load->model('Merchant');

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $data['title'] = 'Setting Cashout Fee';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant_id'] = $this->uri->segment(3);
      if (!$data['merchant_id']) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }
      $merchant = $this->Merchant->get_merchant_by_id($data['merchant_id']);
      $data['merchant_name'] = $merchant ? $merchant['c_name'] : 'Unknown';

      // Breadcrumb override: Replace ID with Merchant Name
      $data['breadcrumb_replace'] = [
         $data['merchant_id'] => $data['merchant_name']
      ];

      $data['cashout_channel_x_merchant'] = $this->Merchant->get_cashout_channel_x_merchant_by_merchant_id($data['merchant_id']);
      $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
      $data['channel_ids'] = $this->Chanel->get_cashout_chanel_id();
      $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();

      // Calculate Summary
      $data['total_channels'] = count($data['cashout_channel_x_merchant']);
      $data['active_channels'] = 0;
      $data['inactive_channels'] = 0;
      foreach ($data['cashout_channel_x_merchant'] as $row) {
         if ($row->c_status == 'Active') {
            $data['active_channels']++;
         } else {
            $data['inactive_channels']++;
         }
      }
   
       // Load views
       $this->load->view('templates/user_header.php', $data);
       $this->load->view('templates/user_sidebar.php', $data);
       $this->load->view('templates/user_topbar.php', $data);
       $this->load->view('merchant/setting-cashout-fee', $data);
       $this->load->view('templates/user_footer.php', $data);
   }

   public function createSettingCashoutFee()
   {
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

      $merchant_id = $this->uri->segment(3);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/settingcashoutfee/' . $merchant_id);
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
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data:' . json_encode($result));
         }

         redirect('admin/settingcashoutfee/' . $merchant_id);
      }
   }

   public function bulkCreateSettingCashoutFee()
   {
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

      $merchant_id = $this->uri->segment(3);
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/settingcashoutfee/' . $merchant_id);
      } else {
         $channelGroups = $this->Chanel->get_cashout_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashoutChannelGroup'));
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
            $this->session->set_flashdata('success', 'Data successfully inserted');
         } else {
            $this->session->set_flashdata('error', 'Failed to insert data:' . json_encode($result));
         }

         redirect('admin/settingcashoutfee/' . $merchant_id);
      }
   }

   public function getCashoutChannelGroups()
   {
      $rules = [
         ['field' => 'c_cashoutChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
         ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
      ];

      $this->form_validation->set_rules($rules);

      $merchant_id = $this->uri->segment(3);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         echo json_encode($errors);
         return;
      } 

      $channelGroups = $this->Chanel->get_cashout_channels($this->input->post('c_externalIdDefault'), $this->input->post('c_cashoutChannelGroup'));
      echo json_encode($channelGroups);
   }

   public function editSettingCashoutFee()
   {
      $merchant_id = $this->uri->segment(3);
      $id = $this->uri->segment(4);
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
         redirect('admin/settingcashoutfee/' . $merchant_id);
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
         if ($result == true) {
               $this->session->set_flashdata('success', 'Data successfully updated');
         } else {
               $this->session->set_flashdata('error', 'Failed to update data: ' . json_encode($result));
         }

         redirect('admin/settingcashoutfee/' . $merchant_id);
      }
   }

   public function deleteSettingCashoutFee($id)
   {
      $merchant_id = $this->uri->segment(3);
      $id = $this->uri->segment(4);

      $result = $this->Chanel->deleteCashoutChannelXMerchant($id);

      if ($result === true) {
         $this->session->set_flashdata('success', 'Data successfully deleted');
      } else {
         $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown DB error';
         $this->session->set_flashdata('error', 'Failed to delete data: ' . $errorMsg);
      }

      redirect('admin/settingcashoutfee/' . $merchant_id);
   }

   // end setting cashout fee
   
   public function resetMerchant()
   {
      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_merchant');
      redirect("admin/merchant");
   }

   public function searchMerchants()
   {
      $q = $this->input->get('q');
      
      $this->db->select('id, c_name');
      $this->db->from('merchant');
      $this->db->like('c_name', $q);
      $this->db->where('(c_refSupervisor IS NULL OR c_refSupervisor = "" OR c_refSupervisor = 0)');
      $this->db->limit(10); // optional: batasi hasil
      $query = $this->db->get();
      $results = $query->result();

      $data = [];
      foreach ($results as $row) {
         $data[] = [
               'id' => $row->id,
               'name' => $row->c_name // <- pakai key 'name'
         ];
      }

      echo json_encode($data);
   }

   /**
    * AJAX Methods for Merchant Delegation
    */
   public function fetchMerchantPermissions($merchantId)
   {
      if (!$merchantId) {
          header('Content-Type: application/json');
          echo json_encode(['status' => 'error', 'message' => 'Merchant ID missing']);
          return;
      }
      
      $this->load->model('Merchant');
      $all_permissions = $this->Merchant->get_rbac_permissions();
      $explicit_grants = $this->Merchant->get_merchant_explicit_grants($merchantId);
      
      // Map grants for easy lookup
      $grants_map = [];
      foreach ($explicit_grants as $g) {
         $grants_map[$g->ref_permissionId] = $g->c_isAllowed;
      }
      
      $data = [];
      foreach ($all_permissions as $p) {
         $status = 'Deny';
         if (isset($grants_map[$p->id])) {
            // Flexible check for 1/0 or booleans
            $status = ($grants_map[$p->id] == 1 || $grants_map[$p->id] === true) ? 'Grant' : 'Deny';
         }
         
         $data[] = [
            'id' => $p->id,
            'name' => $p->c_code,
            'label' => $p->c_name,
            'status' => $status
         ];
      }
      
      header('Content-Type: application/json');
      echo json_encode(['status' => 'success', 'data' => $data]);
   }

   public function saveDelegation($merchantId)
   {
      if (!$merchantId) {
          header('Content-Type: application/json');
          echo json_encode(['status' => 'error', 'message' => 'Merchant ID missing']);
          return;
      }
      
      $this->load->model('Merchant');
      $permissions = $this->input->post('permissions');
      
      if (!empty($permissions) && is_array($permissions)) {
         $successCount = 0;
         foreach ($permissions as $permId => $action) {
            if ($this->Merchant->save_merchant_delegation($merchantId, $permId, $action)) {
               $successCount++;
            }
         }
         
         if ($successCount > 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "$successCount permissions updated successfully"]);
         } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to save permissions. Please check database connectivity.']);
         }
      } else {
         header('Content-Type: application/json');
         echo json_encode(['status' => 'error', 'message' => 'No permissions data received']);
      }
   }
}

