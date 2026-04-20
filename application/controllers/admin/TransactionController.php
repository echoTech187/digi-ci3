<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransactionController extends CI_Controller
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
      is_logged_in();
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function Submerchant($id = NULL)
   {
      if (!$id) $id = $this->uri->segment(3);
      if (!$id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }
      $this->load->model('SubMerchant');
      is_logged_in();

      $role_id = $this->session->userdata('role');

      $data['title'] = 'Sub Account';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $data['merchant'] = $this->Mutation_model->get_merchant($id);
      $data['total_submerchants'] = $this->SubMerchant->count_all_dt($id);

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [
         $id => $merchant_name
      ];

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->SubMerchant->get_datatables($id);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_name'] = '<div>' . htmlspecialchars($items->c_name) . '</div><small class="text-muted">ID: ' . $items->id . '</small>';
               $row['c_email'] = $items->c_email;
               $row['c_gvconnectBusinessId'] = (isset($items->c_gvconnectBusinessId) && $items->c_gvconnectBusinessId) ? $items->c_gvconnectBusinessId : '-';

               $status_class = ($items->c_status == 'Active') ? 'success' : 'secondary';
               $row['c_status'] = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';

               $row['action'] = '
                  <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-toggle="dropdown">
                           Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                           <a class="dropdown-item py-2" href="' . base_url('admin/submerchant/' . $items->id) . '">
                              <i class="fas fa-users mr-2 text-success"></i>Sub Accounts
                           </a>
                           <div class="dropdown-divider"></div>
                           <button type="button" class="dropdown-item py-2 edit-sub-btn" 
                              data-toggle="modal" data-target="#subMerchantModal"
                              data-id="' . $items->id . '"
                              data-name="' . htmlspecialchars($items->c_name) . '"
                              data-email="' . htmlspecialchars($items->c_email) . '"
                              data-merchantid="' . $items->parent_merchant_id . '"
                              data-businessname="' . htmlspecialchars(isset($items->c_gvconnectBusinessName) ? $items->c_gvconnectBusinessName : '') . '"
                              data-businessid="' . htmlspecialchars(isset($items->c_gvconnectBusinessId) ? $items->c_gvconnectBusinessId : '') . '"
                              data-key="' . htmlspecialchars(isset($items->c_gvconnectGVConnectKey) ? $items->c_gvconnectGVConnectKey : '') . '"
                              data-qris="' . htmlspecialchars(isset($items->c_gvconnectStaticQrisRaw) ? $items->c_gvconnectStaticQrisRaw : '') . '"
                              data-bni="' . htmlspecialchars(isset($items->c_gvconnectStaticVaBni) ? $items->c_gvconnectStaticVaBni : '') . '"
                              data-bca="' . htmlspecialchars(isset($items->c_gvconnectStaticVaBca) ? $items->c_gvconnectStaticVaBca : '') . '"
                              data-cimb="' . htmlspecialchars(isset($items->c_gvconnectStaticVaCimb) ? $items->c_gvconnectStaticVaCimb : '') . '"
                              data-permata="' . htmlspecialchars(isset($items->c_gvconnectStaticVaPermata) ? $items->c_gvconnectStaticVaPermata : '') . '"
                              data-status="' . $items->c_status . '">
                              <i class="fas fa-edit mr-2 text-info"></i>Edit Details
                           </button>
                           <a class="dropdown-item py-2" href="' . base_url('admin/mutation/' . $items->id) . '">
                              <i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations
                           </a>
                        </div>
                  </div>';

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->SubMerchant->count_all_dt($id),
               "recordsFiltered" => $this->SubMerchant->count_filtered($id),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving submerchant data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['merchant'] = $this->Mutation_model->get_merchant($id);
      $data['total_submerchants'] = $this->SubMerchant->count_all_dt($id);

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('submerchant/index', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetsubmerchant()
   {
      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_submerchant');
      redirect("admin/submerchant/$id");
   }
   public function mutation($id = NULL)
   {
      if (!$id) $id = $this->uri->segment(3);
      if (!$id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }
      is_logged_in();

      $role_id = $this->session->userdata('role');

      $data['title'] = 'Mutation';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Mutation_model->get_merchant($id);

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [
         $id => $merchant_name
      ];

      $search_date_mutation = $this->input->post('search_date_mutation')
         != NULL ? $this->input->post('search_date_mutation') : $this->session->userdata('search_date_mutation');

      $search_date_mutation_to = $this->input->post('search_date_mutation_to')
         != NULL ? $this->input->post('search_date_mutation_to') : $this->session->userdata('search_date_mutation_to');

      $search_position = $this->input->post('search_position')
         != NULL ? $this->input->post('search_position') : $this->session->userdata('search_position');

      $search_channel = $this->input->post('search_channel')
         != NULL ? $this->input->post('search_channel') : $this->session->userdata('search_channel');

      // Sync Session
      $this->session->set_userdata([
         'search_date_mutation' => $search_date_mutation,
         'search_date_mutation_to' => $search_date_mutation_to,
         'search_position' => $search_position,
         'search_channel' => $search_channel
      ]);

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->Mutation_model->get_datatables($id, $search_date_mutation, $search_position, $search_channel, $search_date_mutation_to);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetime'] = $items->c_datetime;

               $pos_class = ($items->c_potition == 'Credit') ? 'success' : 'danger';
               $row['c_potition'] = '<span class="badge badge-' . $pos_class . '">' . $items->c_potition . '</span>';

               $row['channelName'] = $items->channelName ?: '-';
               $row['description'] = $items->description ?: '-';

               $amount_class = ($items->c_potition == 'Credit') ? 'text-success' : 'text-danger';
               $row['c_amount'] = '<span class="' . $amount_class . ' font-weight-bold">' . number_format($items->c_amount, 2) . '</span>';
               $row['c_BalanceAfter'] = number_format($items->c_BalanceAfter, 2);
               
               // Raw values for premium client-side rendering
               $row['c_amount_raw']   = $items->c_amount;
               $row['c_balance_raw']  = $items->c_BalanceAfter;
               $row['c_position_raw'] = $items->c_potition;

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->Mutation_model->count_all_dt($id),
               "recordsFiltered" => $this->Mutation_model->count_filtered($id, $search_date_mutation, $search_position, $search_channel, $search_date_mutation_to),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving mutation data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['channels'] = [];
      if ($search_position == 'Credit')
         $data['channels'] = $this->Mutation_model->get_cashin_channels($id);
      elseif ($search_position == 'Debit')
         $data['channels'] = $this->Mutation_model->get_cashout_channels($id);

      $data['merchant'] = $this->Mutation_model->get_merchant($id);
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('mutation/list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }
   public function registersubMerchant()
   {
      $formValidationRules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'ref_merchantId', 'label' => 'Merchant Id', 'rules' => 'trim|required'],
         ['field' => 'c_gvconnectBusinessName', 'label' => 'GVConnect Business Name', 'rules' => 'trim|required'],
         ['field' => 'c_gvconnectBusinessId', 'label' => 'GVConnect Business ID', 'rules' => 'trim|required'],
         ['field' => 'c_gvconnectGVConnectKey', 'label' => 'GVConnect Key', 'rules' => 'trim'],
         ['field' => 'c_gvconnectStaticQrisRaw', 'label' => 'GVConnect Static Qris Raw', 'rules' => 'trim'],
         ['field' => 'c_gvconnectStaticVaBni', 'label' => 'GVConnect Static VA BNI', 'rules' => 'trim'],
         ['field' => 'c_gvconnectStaticVaBca', 'label' => 'GVConnect Static VA BCA', 'rules' => 'trim'],
         ['field' => 'c_gvconnectStaticVaCimb', 'label' => 'GVConnect Static VA CIMB', 'rules' => 'trim'],
         ['field' => 'c_gvconnectStaticVaPermata', 'label' => 'GVConnect Static VA PERMATA', 'rules' => 'trim'],
         ['field' => 'c_status', 'label' => 'Status', 'rules' => 'trim|required']
      ];

      $this->form_validation->set_rules($formValidationRules);

      if ($this->form_validation->run() == FALSE) {
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('admin/submerchant/' . $this->input->post('ref_merchantId'));
      }
      else {
         $data = [];
         foreach ($formValidationRules as $rule) {
            $field = $rule['field'];
            $data[$field] = $this->input->post($field);
         }

         // Map ref_merchantId to parent_merchant_id and add level
         $parent_id = $data['ref_merchantId'];
         unset($data['ref_merchantId']);
         $data['parent_merchant_id'] = $parent_id;
         
         // Get parent level to set current level
         $this->load->model('Mutation_model');
         $parent = $this->Mutation_model->get_merchant($parent_id);
         $parent_level = isset($parent[0]->c_merchantLevel) ? $parent[0]->c_merchantLevel : 0;
         $data['c_merchantLevel'] = $parent_level + 1;
         
         $data['id'] = rand(111111, 999999); // Use longer ID
         $data['c_status'] = $this->input->post('c_status') != NULL ? $this->input->post('c_status') : 'Active';

         $this->load->model('SubMerchant');
         $result = $this->SubMerchant->create_submerchant($data);

         if ($result === true) {
            $this->session->set_flashdata('success', 'Submerchant successfully registered');
         }
         else {
            $this->session->set_flashdata('error', 'Failed to register: ' . json_encode($result));
         }

         redirect('admin/submerchant/' . $parent_id);
      }
   }

   public function edit_submerchant($id = null)
   {
      $this->load->model('SubMerchant');
      $this->load->library('form_validation');
      $this->form_validation->set_rules('c_name', 'Nama', 'required');
      $this->form_validation->set_rules('c_email', 'Email', 'required|valid_email');

      if ($this->form_validation->run() == FALSE) {
         $this->session->set_flashdata('error', validation_errors());
         redirect($_SERVER['HTTP_REFERER']);
      }

      $data = [
         'c_name' => $this->input->post('c_name', true),
         'c_email' => $this->input->post('c_email', true),
         'c_gvconnectBusinessId' => $this->input->post('c_gvconnectBusinessId', true),
         'c_gvconnectBusinessName' => $this->input->post('c_gvconnectBusinessName', true),
         'c_gvconnectGVConnectKey' => $this->input->post('c_gvconnectGVConnectKey', true),
         'c_gvconnectStaticQrisRaw' => $this->input->post('c_gvconnectStaticQrisRaw', true),
         'c_gvconnectStaticVaBni' => $this->input->post('c_gvconnectStaticVaBni', true),
         'c_gvconnectStaticVaBca' => $this->input->post('c_gvconnectStaticVaBca', true),
         'c_gvconnectStaticVaCimb' => $this->input->post('c_gvconnectStaticVaCimb', true),
         'c_gvconnectStaticVaPermata' => $this->input->post('c_gvconnectStaticVaPermata', true),
         'c_status' => $this->input->post('c_status', true),
      ];

      $updated = $this->SubMerchant->update_submerchant($id, $data);

      if ($updated) {
         $this->session->set_flashdata('success', 'SubMerchant was successfully updated.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed to update SubMerchant.');
      }

      $refMerchantId = $this->input->post('ref_merchantId');
      redirect('admin/submerchant/' . $refMerchantId);
   }


   public function resetMutation()
   {

      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_date_mutation');
      $this->session->unset_userdata('search_date_mutation_to');
      $this->session->unset_userdata('search_position');
      $this->session->unset_userdata('search_channel');
      redirect("admin/mutation/$id");

   }

   public function download_mutation()
   {
      $role_id = $this->session->userdata('role');


      $search_date_mutation = isset($_GET['search_date_mutation']) ? $_GET['search_date_mutation'] : '';
      $search_date_mutation_to = isset($_GET['search_date_mutation_to']) ? $_GET['search_date_mutation_to'] : '';

      $id = isset($_GET['id']) ? $_GET['id'] : '';
      // var_dump($search_date_mutation);
      if (empty($search_date_mutation) || empty($search_date_mutation_to)) {
         $this->session->set_flashdata('error_message', 'Please select both from and to dates before downloading.');
         redirect("admin/mutation/$id");
      }
      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];
      // var_dump($adminID);

      $additionalFilter = $search_date_mutation . '|' . $search_date_mutation_to . '|' . $id;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Mutation',

      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect("admin/mutation/$id");
   }

   public function history()
   {
      $this->load->model('History');
      is_logged_in();

      $search_merchant_purchase = $this->input->post('search_merchant_purchase');
      $search_date_purchase = $this->input->post('search_date_purchase');

      if ($search_date_purchase) {
         $this->session->set_userdata('search_date_purchase', $search_date_purchase);
      }
      else {
         $search_date_purchase = $this->session->userdata('search_date_purchase');
      }
      if ($search_merchant_purchase) {
         $this->session->set_userdata('search_merchant_purchase', $search_merchant_purchase);
      }
      else {
         $search_merchant_purchase = $this->session->userdata('search_merchant_purchase');
      }

      // Intercept AJAX request for DataTables
      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $list = $this->History->get_datatables($search_date_purchase, $search_merchant_purchase);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $data = array();
            $no = isset($_POST['start']) ? $_POST['start'] : 0;
            foreach ($list as $history) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['name_merchant'] = $history->name_merchant;
               $row['c_datetime'] = $history->c_datetime;
               $row['ref_cashoutChannelId'] = $history->ref_cashoutChannelId;
               $row['c_invoiceNo'] = $history->c_invoiceNo;
               $row['c_phone'] = $history->c_phone;
               $row['c_amount'] = $history->c_amount;
               $row['c_status'] = $history->c_status;

               $data[] = $row;
            }

            $output = array(
               "draw" => isset($_POST['draw']) ? $_POST['draw'] : null,
               "recordsTotal" => $this->History->count_all_dt($search_date_purchase, $search_merchant_purchase),
               "recordsFiltered" => $this->History->count_filtered($search_date_purchase, $search_merchant_purchase),
               "data" => $data,
            );

            $this->output->set_content_type('application/json')->set_output(json_encode($output));
         } catch (Throwable $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
               "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving history data: " . $e->getMessage()
            )));
         }
         return;
      }

      $data['title'] = 'Purchase';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchants'] = $this->History->get_merchant();
      // Load views
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('history/list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }
   public function resetHistory()
   {

      $this->session->unset_userdata('search_date_purchase');
      $this->session->unset_userdata('search_merchant_purchase');
      redirect('admin/history');

   }

   public function download_history()
   {

      $search_date_purchase = isset($_GET['search_date_purchase']) ? $_GET['search_date_purchase'] : '';
      $search_merchant_purchase = isset($_GET['search_merchant_purchase']) ? $_GET['search_merchant_purchase'] : '';
      // var_dump($search_merchant_purchase);
      if (empty($search_date_purchase) && empty($search_merchant_purchase)) {
         $this->session->set_flashdata('error_message', 'Please fill date and search before to continue download.');
         redirect('admin/history');
      }
      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];
      // var_dump($adminID);

      $additionalFilter = $search_date_purchase . '|' . $search_merchant_purchase;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'PPOB',
      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/history');
   }

   public function virtual_account()
   {

      $this->load->model('VirtualAccount');

      is_logged_in();

      $role_id = $this->session->userdata('role');


      $data['title'] = 'Virtual Account';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $filters = ['search_date_va', 'search_date_va_to', 'search_name_va', 'search_va_number', 'search_date_va_settlement', 'search_va_transid'];
      foreach ($filters as $filter) {
         $val = $this->input->post($filter);
         if ($val !== null) {
            $this->session->set_userdata($filter, $val);
            $data[$filter] = $val;
         }
         else {
            $data[$filter] = $this->session->userdata($filter);
         }
      }

      if ($this->input->post() && empty($data['search_name_va']) && ($data['search_date_va'] || $data['search_date_va_settlement'])) {
         $this->session->set_flashdata('error_message', 'Tanggal pencarian, Tanggal Settlement harus diisi');
         redirect('admin/virtual_account');
      }

      // Cek apakah pencarian dilakukan
      $search_performed = !empty($data['search_date_va']) || !empty($data['search_name_va']) || !empty($data['search_va_number']) || !empty($data['search_date_va_settlement']) || !empty($data['search_va_transid']);


      // Handle DataTables AJAX Request
      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            // Get filter values from session (set by the Search button reload)
            $search_date = $this->session->userdata('search_date_va');
            $search_date_to = $this->session->userdata('search_date_va_to');
            $search_merchant = $this->session->userdata('search_name_va');
            $search_settlement = $this->session->userdata('search_date_va_settlement');
            $search_va = $this->session->userdata('search_va_number');
            $search_transid = $this->session->userdata('search_va_transid');

            $list = $this->VirtualAccount->get_datatables($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $data = array();
            $no = $start;
            foreach ($list as $va) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetime'] = $va->c_datetime;
               $row['merchant_name'] = $va->merchant_name;
               $row['c_invoiceNo'] = $va->c_invoiceNo;
               $row['ref_cashinChannelId'] = $va->ref_cashinChannelId;
               $row['c_type'] = $va->c_type;
               $row['c_vaNumber'] = $va->c_vaNumber;
               $row['c_custom'] = $va->c_custom;
               $row['c_amount'] = number_format($va->c_amount, 2);
               $row['c_fee'] = number_format($va->c_fee, 2);
               $row['c_isSettlementRealtime'] = ($va->c_isSettlementRealtime == 1) ? 'Yes' : 'No';
               $row['c_datetimeSettlement'] = ($va->c_isSettlementRealtime == 1) ? 'Realtime' : $va->c_datetimeSettlement;
               $row['Merchant_Transaction_Id'] = $va->Merchant_Transaction_Id;

               $action = '<a href="' . base_url('admin/VA_detail/' . $va->id) . '" class="btn btn-action-detail"><i class="fas fa-eye mr-2"></i>Detail</a> ';
               $action .= '<a onclick="javascript: return confirm(\'Are you sure, want to resend notification again ??\')" href="' . base_url('admin/SendnotifikasiVA/' . $va->id . '/' . $va->ref_merchantId) . '" class="btn btn-action-resend"><i class="fas fa-paper-plane mr-2"></i>Resend</a>';
               $row['action'] = $action;

               $data[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->VirtualAccount->count_all_dt($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid),
               "recordsFiltered" => $this->VirtualAccount->count_filtered($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid),
               "data" => $data,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving VA data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['Vas'] = [];
      $data['pagination'] = '';
      $data['start'] = 0;
      $data['merchants'] = $this->VirtualAccount->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('virtualaccount/list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetVA()
   {
      $role_id = $this->session->userdata('role');


      $this->session->unset_userdata('search_date_va');
      $this->session->unset_userdata('search_date_va_to');
      $this->session->unset_userdata('search_name_va');
      $this->session->unset_userdata('search_date_va_settlement');
      $this->session->unset_userdata('search_va_number');
      $this->session->unset_userdata('search_va_transid');
      redirect('admin/virtual_account');

   }
   public function VA_detail($id = NULL)
   {
      $role_id = $this->session->userdata('role');
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/virtual_account');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail VA';

      $this->load->model('VirtualAccount');
      $data['va_data'] = $this->VirtualAccount->va_detail($id);

      // Breadcrumb override: Mask numeric ID with Invoice No
      $displayId = '#' . $id;
      if (!empty($data['va_data'])) {
         $displayId = '#' . $data['va_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('virtualaccount/detail_va', $data);
      $this->load->view('templates/user_footer.php', $data);
   }
   public function download_VA()
   {
      $search_date_va = isset($_GET['search_date_va']) ? $_GET['search_date_va'] : '';
      $search_date_va_to = isset($_GET['search_date_va_to']) ? $_GET['search_date_va_to'] : '';
      $search_name_va = isset($_GET['search_name_va']) ? $_GET['search_name_va'] : '';
      $search_date_va_settlement = isset($_GET['search_date_va_settlement']) ? $_GET['search_date_va_settlement'] : '';

      if (empty($search_name_va) && (empty($search_date_va) || empty($search_date_va_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('admin/virtual_account');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];

      $additionalFilter = $search_name_va . '|' . $search_date_va . '|' . $search_date_va_to . '|' . $search_date_va_settlement;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Va',
      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/virtual_account');
   }

   public function qris()
   {
      $this->load->model('Qris');

      is_logged_in();

      $role_id = $this->session->userdata('role');


      $data['title'] = 'QRIS';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Get & Store filters
      $search_rrn = $this->input->post('search_rrn');
      $search_name_qris = $this->input->post('search_name_qris');
      $search_date_qris = $this->input->post('search_date_qris');
      $search_date_qris_to = $this->input->post('search_date_qris_to');
      $search_date_qris_settlement = $this->input->post('search_date_qris_settlement');
      $search_invoice_no = $this->input->post('search_invoice_no');
      $search_transid_qriss = $this->input->post('search_transactionid_ht');


      if ($search_transid_qriss !== null)
         $this->session->set_userdata('search_transactionid_ht', $search_transid_qriss);
      else
         $search_transid_qriss = $this->session->userdata('search_transactionid_ht');

      if ($search_rrn !== null)
         $this->session->set_userdata('search_rrn', $search_rrn);
      else
         $search_rrn = $this->session->userdata('search_rrn');

      if ($search_name_qris !== null)
         $this->session->set_userdata('search_name_qris', $search_name_qris);
      else
         $search_name_qris = $this->session->userdata('search_name_qris');

      if ($search_date_qris !== null)
         $this->session->set_userdata('search_date_qris', $search_date_qris);
      else
         $search_date_qris = $this->session->userdata('search_date_qris');

      if ($search_date_qris_to !== null)
         $this->session->set_userdata('search_date_qris_to', $search_date_qris_to);
      else
         $search_date_qris_to = $this->session->userdata('search_date_qris_to');

      if ($search_date_qris_settlement !== null)
         $this->session->set_userdata('search_date_qris_settlement', $search_date_qris_settlement);
      else
         $search_date_qris_settlement = $this->session->userdata('search_date_qris_settlement');

      if ($search_invoice_no !== null)
         $this->session->set_userdata('search_invoice_no', $search_invoice_no);
      else
         $search_invoice_no = $this->session->userdata('search_invoice_no');

      // Summary defaults
      $qty = $total_trx = $total_fee = $total_fee_ext = $profit = 0;
      $date_from = $date_to = null;

      $search_performed = !empty($search_name_qris) || !empty($search_date_qris) || !empty($search_date_qris_to)
         || !empty($search_date_qris_settlement) || !empty($search_rrn) || !empty($search_invoice_no) || !empty($search_transid_qriss);

      // Pagination config
      // Handle DataTables AJAX Request
      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            // Get filter values from session (set by the Search button reload)
            $search_rrn = $this->session->userdata('search_rrn');
            $search_name = $this->session->userdata('search_name_qris');
            $search_date_from = $this->session->userdata('search_date_qris');
            $search_date_to = $this->session->userdata('search_date_qris_to');
            $search_settlement = $this->session->userdata('search_date_qris_settlement');
            $search_invoice = $this->session->userdata('search_invoice_no');
            $search_transid = $this->session->userdata('search_transactionid_ht');

            // Format dates for query if they exist
            $date_from_query = null;
            $date_to_query = null;
            if (!empty($search_date_from) && !empty($search_date_to)) {
               $date_from_query = date('Ymd', strtotime($search_date_from)) . "000001";
               $date_to_query = date('Ymd', strtotime($search_date_to)) . "235959";
            }

            $list = $this->Qris->get_datatables($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $data = array();
            $no = $start;
            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetime'] = $items->c_datetime;
               $row['merchant_info'] = ' [' . $items->ref_merchantId . '] - ' . $items->name_merchant;
               $row['submerchant_info'] = ' [' . $items->ref_subMerchantId . '] - ' . $items->name_submerchant;
               $row['c_invoiceNo'] = $items->c_invoiceNo;
               $row['c_type'] = $items->c_type;
               $row['c_amount'] = number_format($items->c_amount, 2);
               $row['c_mdr'] = $items->c_mdr;
               $row['c_fee'] = number_format($items->c_fee, 2);
               $row['c_issuerRrn'] = $items->c_issuerRrn;
               $row['c_isSettlementRealtime'] = ($items->c_isSettlementRealtime == 1) ? 'Yes' : 'No';
               $row['c_datetimeSettlement'] = ($items->c_isSettlementRealtime == 1) ? 'Realtime' : $items->c_datetimeSettlement;
               $row['Merchant_Transaction_Id'] = $items->Merchant_Transaction_Id;

               $action = '<a href="' . base_url('admin/qris_detail/' . $items->id) . '" class="btn btn-action-detail"><i class="fas fa-eye mr-2"></i>Detail</a> ';
               $action .= '<a onclick="javascript: return confirm(\'Are you sure, want to resend notification again ??\')" href="' . base_url('admin/SendnotifikasiQRIS/' . $items->id . '/' . $items->ref_merchantId) . '" class="btn btn-action-resend"><i class="fas fa-paper-plane mr-2"></i>Resend</a>';
               $row['action'] = $action;

               $data[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->Qris->count_all_dt($search_name, $date_from_query, $date_to_query),
               "recordsFiltered" => $this->Qris->count_filtered($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid),
               "data" => $data,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            $db_error = $this->db->error();
            $error_msg = $e->getMessage();
            if (!empty($db_error['message'])) {
               $error_msg .= " (DB Error: " . $db_error['message'] . ")";
            }
            log_message('error', 'QRIS AJAX error: ' . $error_msg);
            $this->session->set_flashdata('error', 'Error retrieving QRIS data: ' . $error_msg);
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "redirect" => base_url('admin/qris')
            ));
         }
         exit;
      }

      $data['qriss'] = [];
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['search_rrn'] = $search_rrn;
      $data['search_transid_qriss'] = $search_transid_qriss;
      $data['merchants'] = $this->Qris->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('qris/list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetqris()
   {

      $this->session->unset_userdata('search_date_qris');
      $this->session->unset_userdata('search_date_qris_to');
      $this->session->unset_userdata('search_name_qris');
      $this->session->unset_userdata('search_date_qris_settlement');
      $this->session->unset_userdata('search_invoice_no');
      $this->session->unset_userdata('search_rrn');
      $this->session->unset_userdata('search_transactionid_ht');
      redirect('admin/qris');
   }
   public function qris_detail($id = NULL)
   {
      $role_id = $this->session->userdata('role');
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/qris');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail QRIS';

      $this->load->model('Qris');
      $data['qris_data'] = $this->Qris->qris_detail($id);

      // Breadcrumb override: Mask numeric ID with Invoice No
      $displayId = '#' . $id;
      if (!empty($data['qris_data'])) {
         $displayId = '#' . $data['qris_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('qris/detail_qris', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function download_qris()
   {

      $search_date_qris = isset($_GET['search_date_qris']) ? $_GET['search_date_qris'] : '';
      $search_name_qris = isset($_GET['search_name_qris']) ? $_GET['search_name_qris'] : '';
      $search_date_qris_to = isset($_GET['search_date_qris_to']) ? $_GET['search_date_qris_to'] : '';
      $search_date_qris_settlement = isset($_GET['search_date_qris_settlement']) ? $_GET['search_date_qris_settlement'] : '';

      if (empty($search_name_qris) && (empty($search_date_qris) && empty($search_date_qris_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('admin/qris');
      }
      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];

      $additionalFilter = $search_name_qris . '|' . $search_date_qris . '|' . $search_date_qris_settlement;
      // var_dump($additionalFilter);

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Qris',
         'c_status' => 'Pending',
         'c_filename' => '',
      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/qris');
   }

   public function ewallet()
   {


      $this->load->model('Ewallet');

      is_logged_in();

      $role_id = $this->session->userdata('role');



      $data['title'] = 'Ewallet';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_name_ewallet = $this->input->post('search_name_ewallet');
      $search_date_ewallet = $this->input->post('search_date_ewallet');
      $search_date_ewallet_to = $this->input->post('search_date_ewallet_to');
      $search_date_ewallet_settlement = $this->input->post('search_date_ewallet_settlement');
      $search_invoice_no = $this->input->post('search_invoice_no');

      $total_trx = 0;
      $total_fee = 0;
      $total_fee_ext = 0;
      $qty = 0;
      $profit = 0;


      if ($search_name_ewallet) {
         $this->session->set_userdata('search_name_ewallet', $search_name_ewallet);
      }
      else {
         $search_name_ewallet = $this->session->userdata('search_name_ewallet');
      }

      if ($search_date_ewallet) {
         $this->session->set_userdata('search_date_ewallet', $search_date_ewallet);
      }
      else {
         $search_date_ewallet = $this->session->userdata('search_date_ewallet');
      }

      if ($search_date_ewallet_to) {
         $this->session->set_userdata('search_date_ewallet_to', $search_date_ewallet_to);
      }
      else {
         $search_date_ewallet_to = $this->session->userdata('search_date_ewallet_to');
      }

      if ($search_date_ewallet_settlement) {
         $this->session->set_userdata('search_date_ewallet_settlement', $search_date_ewallet_settlement);
      }
      else {
         $search_date_ewallet_settlement = $this->session->userdata('search_date_ewallet_settlement');
      }

      if ($search_invoice_no) {
         $this->session->set_userdata('search_invoice_no', $search_invoice_no);
      }
      else {
         $search_invoice_no = $this->session->userdata('search_invoice_no');
      }


      $date_from = !empty($search_date_ewallet) && !empty($search_date_ewallet_to) ? 
         date('Ymd', strtotime($search_date_ewallet)) . "000001" : null;
      $date_to = !empty($search_date_ewallet) && !empty($search_date_ewallet_to) ? 
         date('Ymd', strtotime($search_date_ewallet_to)) . "235959" : null;

      $search_performed = !empty($search_name_ewallet) || !empty($date_from) || !empty($date_to) || !empty($search_date_ewallet_settlement) || !empty($search_invoice_no);


      $config['base_url'] = base_url('admin/ewallet');

      $config['per_page'] = 10;
      $config['uri_segment'] = 3;

      $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
      $config['full_tag_close'] = '</ul>';

      $config['first_link'] = 'First';
      $config['last_link'] = 'Last';

      $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['first_tag_close'] = '</span></li>';

      $config['prev_link'] = '&laquo';
      $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['prev_tag_close'] = '</span></li>';

      $config['next_link'] = '&raquo';
      $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['next_tag_close'] = '</span></li>';

      $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['last_tag_close'] = '</span></li>';

      $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
      $config['cur_tag_close'] = '</a></li>';

      $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['num_tag_close'] = '</span></li>';

      $this->pagination->initialize($config);

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $search_name = $this->session->userdata('search_name_ewallet');
            $date_from_query = !empty($this->session->userdata('search_date_ewallet')) && !empty($this->session->userdata('search_date_ewallet_to')) ? 
               date('Ymd', strtotime($this->session->userdata('search_date_ewallet'))) . "000001" : null;
            $date_to_query = !empty($this->session->userdata('search_date_ewallet')) && !empty($this->session->userdata('search_date_ewallet_to')) ? 
               date('Ymd', strtotime($this->session->userdata('search_date_ewallet_to'))) . "235959" : null;
            $search_date_settlement = $this->session->userdata('search_date_ewallet_settlement');
            $search_invoice_no_ajax = $this->session->userdata('search_invoice_no');

            $list = $this->Ewallet->get_datatables($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no_ajax);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $data = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetime'] = $items->c_datetime;
               $row['submerchant_info'] = ' [' . $items->ref_subMerchantId . '] - ' . $items->name_submerchant;
               $row['c_invoiceNo'] = $items->c_invoiceNo;
               $row['c_type'] = $items->c_type;
               $row['ref_cashinChannelId'] = $items->ref_cashinChannelId;
               $row['c_amount'] = number_format($items->c_amount, 2);
               $row['c_mdr'] = $items->c_mdr;
               $row['c_fee'] = number_format($items->c_fee, 2);
               $row['settlement_info'] = ($items->c_isSettlementRealtime == 1) ? 'Realtime' : ($items->c_datetimeSettlement ?: '-');
               $row['Merchant_Transaction_Id'] = $items->Merchant_Transaction_Id ?: '-';

               $action = '<a href="' . base_url('admin/ewallet_detail/' . $items->id) . '" class="btn btn-action-detail"><i class="fas fa-eye mr-2"></i>Detail</a> ';
               $action .= '<a onclick="javascript: return confirm(\'Are you sure, want to resend notification again ??\')" href="' . base_url('admin/Sendnotifikasiewallet/' . $items->id) . '" class="btn btn-action-resend"><i class="fas fa-paper-plane mr-2"></i>Resend</a>';
               $row['action'] = $action;

               $data[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->Ewallet->count_all_dt($search_name, $date_from_query, $date_to_query),
               "recordsFiltered" => $this->Ewallet->count_filtered($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no_ajax),
               "data" => $data,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving E-Wallet data: " . $e->getMessage()
            ));
         }
         exit;
      }      
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['ewallets'] = [];

      $data['merchants'] = $this->Ewallet->get_merchant();


      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('ewallet/ewallet_list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetewallet()
   {

      $this->session->unset_userdata('search_date_ewallet');
      $this->session->unset_userdata('search_date_ewallet_to');
      $this->session->unset_userdata('search_name_ewallet');
      $this->session->unset_userdata('search_date_ewallet_settlement');
      $this->session->unset_userdata('search_invoice_no');
      redirect('admin/ewallet');
   }

   public function download_ewallet()
   {
      $search_date_ewallet = isset($_GET['search_date_ewallet']) ? $_GET['search_date_ewallet'] : '';
      $search_date_to_ewallet = isset($_GET['search_date_to_ewallet']) ? $_GET['search_date_to_ewallet'] : '';
      $search_name_ewallet = isset($_GET['search_name_ewallet']) ? $_GET['search_name_ewallet'] : '';
      $search_date_ewallet_settlement = isset($_GET['search_date_ewallet_settlement']) ? $_GET['search_date_ewallet_settlement'] : '';

      if (
      empty($search_name_ewallet) &&
      empty($search_date_ewallet) &&
      empty($search_date_to_ewallet) &&
      empty($search_date_ewallet_settlement)
      ) {
         $this->session->set_flashdata('error_message', 'Please fill at least one filter before downloading.');
         redirect('admin/ewallet');
      }
      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];

      $additionalFilter = $search_name_ewallet . '|' . $search_date_ewallet . '|' . $search_date_to_ewallet . '|' . $search_date_ewallet_settlement;
      // var_dump($additionalFilter);

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'ewallet',
      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/ewallet');
   }

   public function bi_fast()
   {
      $this->load->model('BiFast');

      is_logged_in();

      $role_id = $this->session->userdata('role');


      $data['title'] = 'Disbursement';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Read all search filters
      $search_name_bifast = $this->input->post('search_name_bifast') != NULL ? $this->input->post('search_name_bifast') : $this->session->userdata('search_name_bifast');
      $search_date_bifast = $this->input->post('search_date_bifast') != NULL ? $this->input->post('search_date_bifast') : $this->session->userdata('search_date_bifast');
      $search_date_bifast_to = $this->input->post('search_date_bifast_to') != NULL ? $this->input->post('search_date_bifast_to') : $this->session->userdata('search_date_bifast_to');
      $search_transid_bifast = $this->input->post('search_transid_bifast') != NULL ? $this->input->post('search_transid_bifast') : $this->session->userdata('search_transid_bifast');
      $search_external_reff_id = $this->input->post('search_external_reff_id') != NULL ? $this->input->post('search_external_reff_id') : $this->session->userdata('search_external_reff_id');
      $search_channel_bifast = $this->input->post('search_channel_bifast') != NULL ? $this->input->post('search_channel_bifast') : $this->session->userdata('search_channel_bifast');
      $search_status_transaction_bifast = $this->input->post('search_status_transaction_bifast') != NULL ? $this->input->post('search_status_transaction_bifast') : $this->session->userdata('search_status_transaction_bifast');

      // Debug logging
      log_message('debug', 'BI-FAST Filters - POST: ' . json_encode($this->input->post()));
      log_message('debug', 'BI-FAST Filters - SESSION: external_reff=' . $this->session->userdata('search_external_reff_id') . ', channel=' . $this->session->userdata('search_channel_bifast'));
      log_message('debug', 'BI-FAST Filters - FINAL: external_reff=' . $search_external_reff_id . ', channel=' . $search_channel_bifast);

      // Validation: External Reff ID requires External Channel to be selected
      if (!empty($search_external_reff_id) && (empty($search_channel_bifast) || $search_channel_bifast === '' || $search_channel_bifast === null)) {
         log_message('error', 'BI-FAST Validation: External Reff ID provided without Channel. Reff ID: ' . $search_external_reff_id . ', Channel: ' . $search_channel_bifast);
         $this->session->set_flashdata('error', 'Silakan pilih "External Channel" terlebih dahulu sebelum memasukan "External Reff ID"');
         redirect('admin/bi_fast');
      }

      // Store filters in session
      $this->session->set_userdata([
         'search_name_bifast' => $search_name_bifast,
         'search_date_bifast' => $search_date_bifast,
         'search_date_bifast_to' => $search_date_bifast_to,
         'search_transid_bifast' => $search_transid_bifast,
         'search_external_reff_id' => $search_external_reff_id,
         'search_channel_bifast' => $search_channel_bifast,
         'search_status_transaction_bifast' => $search_status_transaction_bifast,
      ]);

      // Convert dates
      $date_from = !empty($search_date_bifast) ? date('Ymd', strtotime($search_date_bifast)) . "000001" : null;
      $date_to = !empty($search_date_bifast_to) ? date('Ymd', strtotime($search_date_bifast_to)) . "235959" : null;


      $search_performed = !empty($date_from) || !empty($date_to) || !empty($search_name_bifast) || !empty($search_transid_bifast) || !empty($search_external_reff_id) || !empty($search_channel_bifast) || !empty($search_status_transaction_bifast);

      // Handle DataTables AJAX Request
      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            // Get filter values from session
            $search_name = $this->session->userdata('search_name_bifast');
            $search_date_from = $this->session->userdata('search_date_bifast');
            $search_date_to = $this->session->userdata('search_date_bifast_to');
            $search_transid = $this->session->userdata('search_transid_bifast');
            $search_external_reff = $this->session->userdata('search_external_reff_id');
            $search_channel = $this->session->userdata('search_channel_bifast');
            $search_status = $this->session->userdata('search_status_transaction_bifast');

            // Format dates for query
            $date_from_query = !empty($search_date_from) ? date('Ymd', strtotime($search_date_from)) . "000001" : null;
            $date_to_query = !empty($search_date_to) ? date('Ymd', strtotime($search_date_to)) . "235959" : null;

            $list = $this->BiFast->get_datatables($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $data = array();
            $no = $start;
            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['merchant_info'] = ' [' . $items->ref_merchantId . '] - ' . $items->name_merchant;
               $row['c_datetime'] = $items->c_datetime;
               $row['c_invoiceNo'] = $items->c_invoiceNo;
               $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;
               $row['ref_cashoutChannelId'] = $items->ref_cashoutChannelId;
               $row['c_accountNo'] = $items->c_accountNo;
               $row['c_beneficiaryAccountName'] = $items->c_beneficiaryAccountName;
               $row['c_amount'] = number_format($items->c_amount, 2);
               $row['c_fee'] = number_format($items->c_fee, 2);
               $row['c_status'] = $items->c_status;

               // Parsed Response Logic
               $responseBody = json_decode($items->c_responseBody, true);
               $row['parsedResponse'] = isset($responseBody['responseMessage']) ? $responseBody['responseMessage'] : (isset($responseBody['message']) ? $responseBody['message'] : '-');

               $action = '<a href="' . base_url('admin/bi_fast_detail/' . $items->id) . '" class="btn btn-action-detail"><i class="fas fa-eye mr-2"></i>Detail</a> ';
               $action .= '<a class="btn btn-action-resend btn-info-request" href="#" data-merchantTransactionId="' . $items->c_merchantTransactionId . '" data-ref_cashoutExternalId="' . $items->ref_cashoutExternalId . '" data-ref_cashoutExternalLogBifastId="' . $items->ref_cashoutExternalLogBifastId . '"><i class="fas fa-info-circle mr-2"></i>Info Request</a>';
               $row['action'] = $action;

               $data[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->BiFast->count_all_dt($search_name, $date_from_query, $date_to_query),
               "recordsFiltered" => $this->BiFast->count_filtered($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status),
               "data" => $data,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving BI-FAST data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['start'] = 0;
      $data['pagination'] = '';
      $data['bifasts'] = [];
      $data['qty'] = 0;
      $data['total_trx'] = 0;
      $data['total_fee'] = 0;
      $data['total_fee_ext'] = 0;
      $data['profit'] = 0;
      $data['search_status_transaction_bifast'] = isset($search_status_transaction_bifast) ? $search_status_transaction_bifast : '';

      if ($search_performed) {
         // Parse responseBody
         foreach ($data['bifasts'] as $key => $row) {
            $responseBody = $row->c_responseBody != NULL ? $row->c_responseBody : null;
            if (!empty($responseBody)) {
               $decoded = json_decode($responseBody, true);
               $data['bifasts'][$key]->parsedResponse = $decoded['responseMessage'] != NULL ? $decoded['responseMessage'] : '-';
            }
            else {
               $data['bifasts'][$key]->parsedResponse = '-';
            }
         }

         // Final vars
         $data['qty'] = 0;
         $data['total_trx'] = 0;
         $data['total_fee'] = 0;
         $data['total_fee_ext'] = 0;
         $data['profit'] = 0;
         $data['search_status_transaction_bifast'] = $search_status_transaction_bifast;
      }



      $data['merchants'] = $this->BiFast->get_merchant();
      $data['channels'] = $this->BiFast->get_channels();


      // Render view
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('bifast/list', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetbi_fast()
   {

      $this->session->unset_userdata('search_date_bifast');
      $this->session->unset_userdata('search_date_bifast_to');
      $this->session->unset_userdata('search_name_bifast');
      $this->session->unset_userdata('search_transid_bifast');
      $this->session->unset_userdata('search_status_transaction_bifast');
      $this->session->unset_userdata('search_external_reff_id');
      $this->session->unset_userdata('search_channel_bifast');

      redirect('admin/bi_fast');
   }
   public function bi_fast_detail($id = NULL)
   {
      $role_id = $this->session->userdata('role');
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/bi_fast');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail BI Fast';

      $this->load->model('BiFast');
      $data['bifast_data'] = $this->BiFast->getBifastDetail($id);

      // Breadcrumb override: Mask numeric ID with Invoice No
      $displayId = '#' . $id;
      if (!empty($data['bifast_data'])) {
         $displayId = '#' . $data['bifast_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('bifast/detail', $data);
      $this->load->view('templates/user_footer.php', $data);
   }
   public function download_bi_fast()
   {
      $search_date_bifast = isset($_GET['search_date_bifast']) ? $_GET['search_date_bifast'] : '';
      $search_name_bifast = isset($_GET['search_name_bifast']) ? $_GET['search_name_bifast'] : '';

      // var_dump($search_date_bifast);
      if (empty($search_date_bifast) && empty($search_name_bifast)) {
         $this->session->set_flashdata('error_message', 'Please fill date and search before to continue download.');
         redirect('admin/bi_fast');
      }
      $data['user'] = $this->Model_user->view_user()->row_array();
      $adminID = $data['user']['id'];
      // var_dump($adminID);

      $additionalFilter = $search_date_bifast . '|' . $search_name_bifast;

      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'BI Fast',
      );

      $result = $this->db->insert('admin_download', $data);
      if ($result) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to <a href="' . base_url('admin/report') . '">Download Report</a> menu to retrieve the file.');
      }
      else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/bi_fast');
   }
   public function Va_dynamic()
   {
      $this->load->model('VADynamic');
      is_logged_in();

      $data['title'] = 'VA Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Read filters from POST or Session
      $search_name_vad = $this->input->post('search_name_vad') != NULL ? $this->input->post('search_name_vad') : $this->session->userdata('search_name_vad');
      $search_date_vad = $this->input->post('search_date_vad') != NULL ? $this->input->post('search_date_vad') : $this->session->userdata('search_date_vad');
      $search_date_vad_to = $this->input->post('search_date_vad_to') != NULL ? $this->input->post('search_date_vad_to') : $this->session->userdata('search_date_vad_to');
      $search_va_number = $this->input->post('search_va_number') != NULL ? $this->input->post('search_va_number') : $this->session->userdata('search_va_number');
      $search_merchant_trxid = $this->input->post('search_merchant_trxid') != NULL ? $this->input->post('search_merchant_trxid') : $this->session->userdata('search_merchant_trxid');

      // Store in Session
      $this->session->set_userdata([
         'search_name_vad' => $search_name_vad,
         'search_date_vad' => $search_date_vad,
         'search_date_vad_to' => $search_date_vad_to,
         'search_va_number' => $search_va_number,
         'search_merchant_trxid' => $search_merchant_trxid
      ]);

      // Handle DataTables AJAX
      if ($this->input->is_ajax_request()) {
         $draw = intval($this->input->post("draw"));
         $start = intval($this->input->post("start"));
         $length = intval($this->input->post("length"));

         $list = $this->VADynamic->get_datatables($search_name_vad, $search_date_vad, $search_va_number, $search_merchant_trxid, $search_date_vad_to);
         $dataItems = array();
         $no = $start;

         foreach ($list as $items) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['c_datetimeRequest'] = $items->c_datetimeRequest;
            $row['name_merchant'] = $items->name_merchant ?: '-';
            $row['name_submerchant'] = $items->name_submerchant;
            $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;
            $row['ref_cashinChannelId'] = $items->ref_cashinChannelId;
            if (!empty($items->ref_cashinExternalLogVaIdCreate)) {
                $row['ref_cashinExternalId'] = '<a data-toggle="modal" href="#" 
                    data-target="#detailVaDynamicChannelExternalModal" 
                    data-merchanttransactionid="' . $items->c_merchantTransactionId . '" 
                    data-ref_cashinexternalid="' . $items->ref_cashinExternalId . '" 
                    data-ref_cashinexternallogvaidcreate="' . $items->ref_cashinExternalLogVaIdCreate . '" 
                    class="detailVaDynamicChannelExternalAjax">' . $items->ref_cashinExternalId . '</a>';
            } else {
                $row['ref_cashinExternalId'] = $items->ref_cashinExternalId;
            }
            $row['c_vaNumber'] = $items->c_vaNumber;
            $row['c_amount'] = $items->c_amount;
            $row['c_datetimeExpired'] = $items->c_datetimeExpired;

            $status_class = 'secondary';
            if ($items->c_status == 'PAID' || $items->c_status == 'SUCCESS')
               $status_class = 'success';
            if ($items->c_status == 'EXPIRED' || $items->c_status == 'FAILED')
               $status_class = 'danger';
            if ($items->c_status == 'PENDING')
               $status_class = 'warning';
            $row['c_status'] = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';

            $dataItems[] = $row;
         }

         $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->VADynamic->count_all_dt($search_name_vad, $search_date_vad, $search_date_vad_to),
            "recordsFiltered" => $this->VADynamic->count_filtered($search_name_vad, $search_date_vad, $search_va_number, $search_merchant_trxid, $search_date_vad_to),
            "data" => $dataItems,
         );
         echo json_encode($output);
         exit;
      }

      $data['start'] = 0;
      $data['merchants'] = $this->VADynamic->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('virtualaccount/vadynamic', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetVa_dynamic()
   {
      $this->session->unset_userdata('search_name_vad');
      $this->session->unset_userdata('search_date_vad');
      $this->session->unset_userdata('search_date_vad_to');
      $this->session->unset_userdata('search_va_number');
      $this->session->unset_userdata('search_merchant_trxid');
      redirect('admin/Va_dynamic');
   }
   public function VA_recurring()
   {
      $this->load->model('VARecurring');
      is_logged_in();

      $role_id = $this->session->userdata('role');



      $data['title'] = 'VA Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_name_var = $this->input->post('search_name_var');
      $search_date_var = $this->input->post('search_date_var');
      $search_date_var_to = $this->input->post('search_date_var_to');
      $search_submerchant_var = $this->input->post('search_submerchant_var');

      if ($search_date_var)
         $this->session->set_userdata('search_date_var', $search_date_var);
      else
         $search_date_var = $this->session->userdata('search_date_var');

      if ($search_date_var_to)
         $this->session->set_userdata('search_date_var_to', $search_date_var_to);
      else
         $search_date_var_to = $this->session->userdata('search_date_var_to');

      if ($search_name_var)
         $this->session->set_userdata('search_name_var', $search_name_var);
      else
         $search_name_var = $this->session->userdata('search_name_var');

      if ($search_submerchant_var)
         $this->session->set_userdata('search_submerchant_var', $search_submerchant_var);
      else
         $search_submerchant_var = $this->session->userdata('search_submerchant_var');

      $config['total_rows'] = $this->VARecurring->count_filtered($search_name_var, $search_date_var, $search_submerchant_var);
      $config['base_url'] = base_url('admin/VA_recurring');
      $config['per_page'] = 10;
      $config['uri_segment'] = 3;

      $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
      $config['full_tag_close'] = '</ul>';

      $config['first_link'] = 'First';
      $config['last_link'] = 'Last';

      $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['first_tag_close'] = '</span></li>';

      $config['prev_link'] = '&laquo';
      $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['prev_tag_close'] = '</span></li>';

      $config['next_link'] = '&raquo';
      $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['next_tag_close'] = '</span></li>';

      $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['last_tag_close'] = '</span></li>';

      $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
      $config['cur_tag_close'] = '</a></li>';

      $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
      $config['num_tag_close'] = '</span></li>';

      $this->pagination->initialize($config);

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $search_name = $this->session->userdata('search_name_var');
            $search_date = $this->session->userdata('search_date_var');
            $search_sub = $this->session->userdata('search_submerchant_var');

            $list = $this->VARecurring->get_datatables($search_name, $search_date, $search_sub);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetimeRequest'] = $items->c_datetimeRequest;
               $row['name_merchant'] = $items->name_merchant ?: '-';
               $row['name_submerchant'] = $items->name_submerchant;
               $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;
               $row['ref_cashinChannelId'] = $items->ref_cashinChannelId;
               if (!empty($items->ref_cashinExternalLogVaIdCreate)) {
                  $row['ref_cashinExternalId'] = '<a data-toggle="modal" href="#" 
                        data-target="#detailVaDynamicChannelExternalModal" 
                        data-merchanttransactionid="' . $items->c_merchantTransactionId . '" 
                        data-ref_cashinexternalid="' . $items->ref_cashinExternalId . '" 
                        data-ref_cashinexternallogvaidcreate="' . $items->ref_cashinExternalLogVaIdCreate . '" 
                        class="detailVaDynamicChannelExternalAjax">' . $items->ref_cashinExternalId . '</a>';
               } else {
                  $row['ref_cashinExternalId'] = $items->ref_cashinExternalId;
               }
               $row['c_vaNumber'] = $items->c_vaNumber;
               $row['c_amount'] = $items->c_amount;

               $status_class = 'secondary';
               if ($items->c_status == 'PAID' || $items->c_status == 'SUCCESS') $status_class = 'success';
               elseif ($items->c_status == 'EXPIRED' || $items->c_status == 'FAILED') $status_class = 'danger';
               elseif ($items->c_status == 'PENDING') $status_class = 'warning';
               $row['c_status'] = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->VARecurring->count_all_dt($search_name, $search_date),
               "recordsFiltered" => $this->VARecurring->count_filtered($search_name, $search_date, $search_sub),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving VA Recurring data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['start'] = 0;
      $data['pagination'] = '';
      $data['varecurrings'] = [];
      $data['merchants'] = $this->VARecurring->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('virtualaccount/varecurring', $data);
      $this->load->view('templates/user_footer.php', $data);

   }

   public function resetVa_recurring()
   {

      $this->session->unset_userdata('search_date_var');
      $this->session->unset_userdata('search_name_var');
      $this->session->unset_userdata('search_submerchant_var');
      redirect('admin/VA_recurring');

   }
   public function qris_dynamic_list()
   {
      $this->load->model('QRISDynamiclist');

      $start = $this->input->post("start");
      $length = $this->input->post("length");
      $search = $this->input->post("search")['value'];
      $orderColumn = $this->input->post("order")[0]['column'];
      $orderDir = $this->input->post("order")[0]['dir'];

      // Fetch paginated data
      $data = $this->QRISDynamiclist->get_qris_dynamic_data($start, $length, $search, $orderColumn, $orderDir);

      // Count total records
      $totalRecords = $this->QRISDynamiclist->count_all_qris_dynamic();
      $filteredRecords = $this->QRISDynamiclist->count_filtered_qris_dynamic($search);

      echo json_encode([
         "draw" => intval($this->input->post("draw")),
         "recordsTotal" => $totalRecords,
         "recordsFiltered" => $filteredRecords,
         "data" => $data
      ]);
   }

   public function qris_dynamic()
   {
      $this->load->model('QRISDynamic');
      is_logged_in();

      $data['title'] = 'QRIS Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Read filters from POST or Session
      $search_name_qd = $this->input->post('search_name_qd') != NULL ? $this->input->post('search_name_qd') : $this->session->userdata('search_name_qd');
      $search_date_qd = $this->input->post('search_date_qd') != NULL ? $this->input->post('search_date_qd') : $this->session->userdata('search_date_qd');
      $search_date_qd_to = $this->input->post('search_date_qd_to') != NULL ? $this->input->post('search_date_qd_to') : $this->session->userdata('search_date_qd_to');
      $search_transid_qd = $this->input->post('search_transid_qd') != NULL ? $this->input->post('search_transid_qd') : $this->session->userdata('search_transid_qd');
      $search_status_transaction_qd = $this->input->post('search_status_transaction_qd') != NULL ? $this->input->post('search_status_transaction_qd') : $this->session->userdata('search_status_transaction_qd');
      $search_reff_label = $this->input->post('search_reff_label') != NULL ? $this->input->post('search_reff_label') : $this->session->userdata('search_reff_label');

      // Store in Session
      $this->session->set_userdata([
         'search_name_qd' => $search_name_qd,
         'search_date_qd' => $search_date_qd,
         'search_date_qd_to' => $search_date_qd_to,
         'search_transid_qd' => $search_transid_qd,
         'search_status_transaction_qd' => $search_status_transaction_qd,
         'search_reff_label' => $search_reff_label
      ]);

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->QRISDynamic->get_datatables($search_name_qd, $search_date_qd, $search_transid_qd, $search_status_transaction_qd, $search_reff_label, $search_date_qd_to);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetimeRequest'] = $items->c_datetimeRequest;
               $row['name_merchant'] = ' [' . ($items->ref_merchantId ?? '-') . '] - ' . ($items->name_merchant ?? '-');
               $row['name_submerchant'] = ' [' . ($items->ref_subMerchantId ?? '-') . '] - ' . ($items->name_submerchant ?? '-');
               $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;

               // Channel External with Modal link
               if (!empty($items->ref_cashinExternalId)) {
                  $logId = !empty($items->ref_cashinExternalLogQrisMpmIdCreate) ? $items->ref_cashinExternalLogQrisMpmIdCreate : '';
                  $row['ref_cashinExternalId'] = '<a data-toggle="modal" href="#" 
                        data-target="#detailQrisDynamicChannelExternalModal" 
                        data-merchantTransactionId="' . $items->c_merchantTransactionId . '" 
                        data-ref_cashinExternalId="' . $items->ref_cashinExternalId . '" 
                        data-ref_cashinExternalLogQrisMpmIdCreate="' . $logId . '" 
                        class="detailQrisDynamicChannelExternalAjax">' . $items->ref_cashinExternalId . '</a>';
               } else {
                  $row['ref_cashinExternalId'] = $items->ref_cashinExternalId ?? '-';
               }

               $row['c_amount'] = $items->c_amount;
               $row['c_datetimeExpired'] = $items->c_datetimeExpired;

               // Status with badges
               $status_class = 'secondary';
               $c_status = strtoupper($items->c_status);
               if ($c_status == 'PAID' || $c_status == 'SUCCESS') $status_class = 'success';
               elseif ($c_status == 'FAILED' || $c_status == 'EXPIRED') $status_class = 'danger';
               elseif ($c_status == 'PENDING' || $c_status == 'CREATED') $status_class = 'warning';

               $status_label = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';
               if ($c_status == "PAID") {
                  $row['c_status'] = '<a href="' . base_url('admin/qris_detail/' . $items->ref_cashinPaymentQrisMpmId) . '" target="_blank">' . $status_label . '</a>';
               } else {
                  $row['c_status'] = $status_label;
               }

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->QRISDynamic->count_all_dt($search_name_qd, $search_date_qd, $search_date_qd_to),
               "recordsFiltered" => $this->QRISDynamic->count_filtered($search_name_qd, $search_date_qd, $search_transid_qd, $search_status_transaction_qd, $search_reff_label, $search_date_qd_to),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Dynamic data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['merchants'] = $this->QRISDynamic->get_merchant();
      $data['search_reff_label'] = $search_reff_label;

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('qris/qrisdynamic', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetqris_dynamic()
   {
      $this->session->unset_userdata('search_date_qd');
      $this->session->unset_userdata('search_date_qd_to');
      $this->session->unset_userdata('search_name_qd');
      $this->session->unset_userdata('search_transid_qd');
      $this->session->unset_userdata('search_status_transaction_qd');
      $this->session->unset_userdata('search_reff_label');
      redirect('admin/qris_dynamic');
   }

   public function ewallet_dynamic()
   {
      $this->load->model('Model_user');
      $this->load->model('EwalletDynamic');
      is_logged_in();

      $data['title'] = 'E-Wallet Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_date_qd = $this->input->post('search_date_qd');
      $search_date_qd_to = $this->input->post('search_date_qd_to');
      $search_name_qd = $this->input->post('search_name_qd');
      $search_transid_qd = $this->input->post('search_transid_qd');
      $search_status_transaction_qd = $this->input->post('search_status_transaction_qd');

      // Sync Session
      if ($search_date_qd !== null)
         $this->session->set_userdata('search_date_qd', $search_date_qd);
      else
         $search_date_qd = $this->session->userdata('search_date_qd');

      if ($search_date_qd_to !== null)
         $this->session->set_userdata('search_date_qd_to', $search_date_qd_to);
      else
         $search_date_qd_to = $this->session->userdata('search_date_qd_to');

      if ($search_name_qd !== null)
         $this->session->set_userdata('search_name_qd', $search_name_qd);
      else
         $search_name_qd = $this->session->userdata('search_name_qd');

      if ($search_transid_qd !== null)
         $this->session->set_userdata('search_transid_qd', $search_transid_qd);
      else
         $search_transid_qd = $this->session->userdata('search_transid_qd');

      if ($search_status_transaction_qd !== null)
         $this->session->set_userdata('search_status_transaction_qd', $search_status_transaction_qd);
      else
         $search_status_transaction_qd = $this->session->userdata('search_status_transaction_qd');

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->EwalletDynamic->get_datatables($search_name_qd, $search_date_qd, $search_date_qd_to, $search_transid_qd, $search_status_transaction_qd);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetimeRequest'] = $items->c_datetimeRequest;
               $row['name_submerchant'] = ' [' . $items->ref_subMerchantId . '] - ' . $items->name_submerchant;
               $row['ref_cashinChannelId'] = $items->ref_cashinChannelId;
               $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;

               // Channel External with Modal link
               if (!empty($items->ref_cashinExternalLogEwalletIdCreate)) {
                  $row['ref_cashinExternalId'] = '<a data-toggle="modal" href="#" 
                        data-target="#detailQrisDynamicChannelExternalModal" 
                        data-merchanttransactionid="' . $items->c_merchantTransactionId . '" 
                        data-ref_cashinexternalid="' . $items->ref_cashinExternalId . '" 
                        data-ref_cashinexternallogewalletidcreate="' . $items->ref_cashinExternalLogEwalletIdCreate . '" 
                        class="detailEwalletDynamicChannelExternalAjax">' . $items->ref_cashinExternalId . '</a>';
               } else {
                  $row['ref_cashinExternalId'] = $items->ref_cashinExternalId;
               }

               $row['c_amount'] = $items->c_amount;
               $row['c_datetimeExpired'] = $items->c_datetimeExpired;

               // Status with badges and optional detail link
               $status_class = 'secondary';
               if (in_array(strtoupper($items->c_status), ['PAID', 'SUCCESS'])) $status_class = 'success';
               elseif (in_array(strtoupper($items->c_status), ['FAILED', 'EXPIRED'])) $status_class = 'danger';
               elseif (in_array(strtoupper($items->c_status), ['PENDING', 'CREATED'])) $status_class = 'warning';

               $status_label = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';
               if (strtoupper($items->c_status) == "PAID") {
                  $row['c_status'] = '<a href="' . base_url('admin/ewallet_detail/' . $items->ref_cashinPaymentEwalletId) . '" target="_blank">' . $status_label . '</a>';
               } else {
                  $row['c_status'] = $status_label;
               }
               $row['simulation'] = '';

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->EwalletDynamic->count_all_dt($search_name_qd, $search_date_qd, $search_date_qd_to),
               "recordsFiltered" => $this->EwalletDynamic->count_filtered($search_name_qd, $search_date_qd, $search_date_qd_to, $search_transid_qd, $search_status_transaction_qd),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving E-Wallet Dynamic data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['merchants'] = $this->EwalletDynamic->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('ewallet/ewallet_dynamic', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function ewallet_detail($id = NULL)
   {
      $role_id = $this->session->userdata('role');
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/ewallet');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail Ewallet';
      $data['saldo'] = $this->Model_user->saldo();

      $this->load->model('Ewallet');
      $data['ewallet_data'] = $this->Ewallet->ewallet_detail($id);

      // Breadcrumb override: Mask numeric ID with Invoice No
      $displayId = '#' . $id;
      if (!empty($data['ewallet_data'])) {
         $displayId = '#' . $data['ewallet_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      // echo '<pre>';
      // print_r($data['ewallet_data']);
      // echo '</pre>';
      // exit;

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('ewallet/ewallet_detail', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetewallet_dynamic()
   {
      $this->session->unset_userdata('search_date_qd');
      $this->session->unset_userdata('search_date_qd_to');
      $this->session->unset_userdata('search_name_qd');
      $this->session->unset_userdata('search_transid_qd');
      $this->session->unset_userdata('search_status_transaction_qd');
      redirect('admin/ewallet_dynamic');
   }

   public function qris_recurring()
   {
      $this->load->model('Model_user');
      $this->load->model('QRISRecurring');
      is_logged_in();

      $data['title'] = 'QRIS Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_date_qr = $this->input->post('search_date_qr');
      $search_date_qr_to = $this->input->post('search_date_qr_to');
      $search_name_qr = $this->input->post('search_name_qr');
      $search_submerchant_qr = $this->input->post('search_submerchant_qr');

      // Sync Session
      if ($search_date_qr !== null)
         $this->session->set_userdata('search_date_qr', $search_date_qr);
      else
         $search_date_qr = $this->session->userdata('search_date_qr');

      if ($search_date_qr_to !== null)
         $this->session->set_userdata('search_date_qr_to', $search_date_qr_to);
      else
         $search_date_qr_to = $this->session->userdata('search_date_qr_to');

      if ($search_name_qr !== null)
         $this->session->set_userdata('search_name_qr', $search_name_qr);
      else
         $search_name_qr = $this->session->userdata('search_name_qr');

      if ($search_submerchant_qr !== null)
         $this->session->set_userdata('search_submerchant_qr', $search_submerchant_qr);
      else
         $search_submerchant_qr = $this->session->userdata('search_submerchant_qr');

      if ($this->input->is_ajax_request()) {
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->QRISRecurring->get_datatables($search_name_qr, $search_date_qr, $search_date_qr_to, $search_submerchant_qr);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetimeRequest'] = $items->c_datetimeRequest;
               $row['name_merchant'] = $items->name_merchant ?? '-';
               $row['name_submerchant'] = '[' . ($items->ref_subMerchantId ?? '-') . '] ' . ($items->name_submerchant ?? '-');
               $row['c_merchantTransactionId'] = $items->c_merchantTransactionId;
               if (!empty($items->ref_cashinExternalId)) {
                  $logId = !empty($items->ref_cashinExternalLogQrisMpmIdCreate) ? $items->ref_cashinExternalLogQrisMpmIdCreate : '';
                  $row['ref_cashinExternalId'] = '<a data-toggle="modal" href="#" 
                        data-target="#detailQrisDynamicChannelExternalModal" 
                        data-merchantTransactionId="' . $items->c_merchantTransactionId . '" 
                        data-ref_cashinExternalId="' . $items->ref_cashinExternalId . '" 
                        data-ref_cashinExternalLogQrisMpmIdCreate="' . $logId . '" 
                        class="detailQrisDynamicChannelExternalAjax">' . $items->ref_cashinExternalId . '</a>';
               } else {
                  $row['ref_cashinExternalId'] = $items->ref_cashinExternalId ?? '-';
               }
               $row['c_amount'] = $items->c_amount;

               $status_class = 'secondary';
               if (in_array(strtoupper($items->c_status), ['PAID', 'SUCCESS'])) $status_class = 'success';
               elseif (in_array(strtoupper($items->c_status), ['FAILED', 'EXPIRED'])) $status_class = 'danger';
               elseif (in_array(strtoupper($items->c_status), ['PENDING', 'CREATED'])) $status_class = 'warning';

               $row['c_status'] = '<span class="badge badge-' . $status_class . '">' . $items->c_status . '</span>';

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->QRISRecurring->count_all_dt($search_name_qr, $search_date_qr, $search_date_qr_to),
               "recordsFiltered" => $this->QRISRecurring->count_filtered($search_name_qr, $search_date_qr, $search_date_qr_to, $search_submerchant_qr),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Recurring data: " . $e->getMessage()
            ));
         }
         exit;
      }

      $data['merchants'] = $this->QRISRecurring->get_merchant();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('qris/qrisrecurring', $data);
      $this->load->view('templates/user_footer.php', $data);
   }

   public function resetqris_recurring()
   {
      $this->session->unset_userdata('search_date_qr');
      $this->session->unset_userdata('search_date_qr_to');
      $this->session->unset_userdata('search_name_qr');
      $this->session->unset_userdata('search_submerchant_qr');
      redirect('admin/qris_recurring');
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
         $this->db->db_debug = FALSE;
         try {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            $list = $this->AdminDownload->get_datatables($search_date);
            
            // Explicit error check
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
               throw new Exception("Database Error [" . $db_error['code'] . "]: " . $db_error['message']);
            }

            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
               $no++;
               $row = array();
               $row['no'] = $no;
               $row['c_datetime'] = $items->c_datetime;
               $row['c_type'] = $items->c_type;
               $row['c_filename'] = '<a href="' . base_url('admin/download?filename=' . urlencode($items->c_filename)) . '" class="text-primary font-weight-bold">' . $items->c_filename . '</a>';
               $row['c_status'] = $items->c_status;
               $row['c_remark'] = $items->c_remark;

               $dataItems[] = $row;
            }

            $output = array(
               "draw" => $draw,
               "recordsTotal" => $this->AdminDownload->count_all_dt($search_date),
               "recordsFiltered" => $this->AdminDownload->count_filtered($search_date),
               "data" => $dataItems,
            );
            echo json_encode($output);
         } catch (Throwable $e) {
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving report data: " . $e->getMessage()
            ));
         }
         exit;
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

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('report/index', $data);
      $this->load->view('templates/user_footer.php', $data);
   }
   public function resetdownload()
   {

      $this->session->unset_userdata('search_date');
      redirect('admin/report');
   }

   public function get_submerchants()
   {

      $role_id = $this->session->userdata('role');



      $this->load->model('SubMerchant');

      if ($this->input->post('merchant_id')) {
         $merchant_id = $this->input->post('merchant_id');

         $submerchants = $this->SubMerchant->get_submerchants_by_merchant_id($merchant_id);

         $options = '<option value="">Pilih SubMerchant</option>';
         foreach ($submerchants as $submerchant) {
            $options .= '<option value="' . $submerchant->id . '">' . $submerchant->c_name . '</option>';
         }

         // Kembalikan opsi submerchant sebagai respons
         echo $options;
      }
   }

   public function createCreditBalance()
   {

      $serviceName = "balance_merchant_module";
      is_logged_in();

      $role_id = $this->session->userdata('role');



      // $role_id = $this->session->userdata('role');



      $error = 0;

      $merchantId = $_POST["merchantId"];
      // var_dump($merchantId);
      $channelId = $_POST["channelId"];
      $description = $_POST["description"];
      $amount = $_POST["rawAmountCredit"];

      if (empty($merchantId)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'merchantId still empty');
         redirect('admin/merchant');
      }

      if (empty($channelId)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'channelId still empty');
         redirect('admin/merchant');
      }

      if (empty($description)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'description still empty');
         redirect('admin/merchant');
      }

      if (empty($amount)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'amount still empty');
         redirect('admin/merchant');
      }

      if ($error == 0) {

         $internalRequestBody = array(
            "merchantId" => $merchantId,
            "channelId" => $channelId,
            'description' => $description,
            'amount' => $amount
         );

         $internalUrlHit = $this->internalUrlHit . "/Merchant/creditBalance";

         $internalCurl = curl_init();
         curl_setopt_array($internalCurl, array(
            CURLOPT_URL => $internalUrlHit,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json'
            ),
         ));

         $responseInterlCurl = curl_exec($internalCurl);
         curl_close($internalCurl);

         if ($responseInterlCurl !== false) {
            $this->session->set_flashdata('success', 'Credit Balance Success.');
         }
         else {
            $this->session->set_flashdata('error', 'Gagal mengirim data.');
         }

         redirect('admin/merchant');
      }
   }

   public function createDebitBalance()
   {
      $serviceName = "balance_merchant_module";
      is_logged_in();

      $role_id = $this->session->userdata('role');





      $error = 0;

      $merchantId = $_POST["merchantIdDebit"];
      $channelId = $_POST["channelId"];
      $description = $_POST["description"];
      $amount = $_POST["rawAmountDebit"];

      if (empty($merchantId)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'merchantId still empty');
         redirect('admin/merchant');
      }

      if (empty($channelId)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'channelId still empty');
         redirect('admin/merchant');
      }

      if (empty($description)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'description still empty');
         redirect('admin/merchant');
      }

      if (empty($amount)) {
         $error = 1;
         $this->session->set_flashdata('error_message', 'amount still empty');
         redirect('admin/merchant');
      }

      if ($error == 0) {

         $internalRequestBody = array(
            "merchantId" => $merchantId,
            "channelId" => $channelId,
            'description' => $description,
            'amount' => $amount
         );

         $internalUrlHit = $this->internalUrlHit . "/Merchant/debitBalance";

         $internalCurl = curl_init();
         curl_setopt_array($internalCurl, array(
            CURLOPT_URL => $internalUrlHit,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json'
            ),
         ));

         $responseInterlCurl = curl_exec($internalCurl);
         curl_close($internalCurl);

         if ($responseInterlCurl !== false) {
            $this->session->set_flashdata('success', 'Debit Balance Success.');
         }
         else {
            $this->session->set_flashdata('error', 'Gagal mengirim data.');
         }

         redirect('admin/merchant');
      }
   }

   public function download()
   {
      $filename = $this->input->get('filename');

      if (!empty($filename)) {

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

            exit;
         }
         else {

            echo 'File not found.';
         }
      }
      else {

         echo 'Filename parameter is missing.';
      }
   }

   public function SendnotifikasiVA()
   {
      $merchant = $this->Model_user->view_user()->row_array();

      $ref_cashinPaymentVaId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentVaId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/virtual_account');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_va",
         "msgInfo" => array(
            "ref_cashinPaymentVaId" => $ref_cashinPaymentVaId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = $this->internalUrlHit . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
         ),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      $this->session->set_flashdata('success', 'Notification has resend');

      redirect('admin/virtual_account');
   }

   public function SendnotifikasiQRIS()
   {

      $merchant = $this->Model_user->view_user()->row_array();

      $ref_cashinPaymentQrisMpmId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentQrisMpmId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/qris');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_qris_mpm",
         "msgInfo" => array(
            "ref_cashinPaymentQrisMpmId" => $ref_cashinPaymentQrisMpmId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = $this->internalUrlHit . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
         ),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      $this->session->set_flashdata('success', 'Notification has resend');

      redirect('admin/qris');
   }
   public function Sendnotifikasiewallet()
   {

      $merchant = $this->Model_user->view_user()->row_array();

      $ref_cashinPaymentEwalletId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentEwalletId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/ewallet');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_ewallet",
         "msgInfo" => array(
            "ref_cashinPaymentEwalletId" => $ref_cashinPaymentEwalletId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = $this->internalUrlHit . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
         ),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      $this->session->set_flashdata('success', 'Notification has resend');

      redirect('admin/ewallet');
   }
   public function getDetailQrisDynamicChannelExternal()
   {

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('QRISDynamic');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailEwalletDynamicChannelExternal()
   {

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('EwalletDynamic');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogEwalletIdCreate = $this->input->post('ref_cashinExternalLogEwalletIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->EwalletDynamic->getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailBiFastChannelExternal()
   {

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('BiFast');

      $ref_cashoutExternalId = $this->input->post('ref_cashoutExternalId');
      $ref_cashoutExternalLogBifastId = $this->input->post('ref_cashoutExternalLogBifastId');

      if (empty($ref_cashoutExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->BiFast->getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogBifastId);
      echo json_encode($detailData);
   }

   public function getDetailEwalletChannelExternal()
   {

      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('QRISDynamic');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailVaDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('VADynamic');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->VADynamic->getDataVaDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailVaRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('VARecurring');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->VARecurring->getDataVaRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailQrisRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('QRISRecurring');

      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISRecurring->getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate);
      echo json_encode($detailData);
   }

   public function getChannelsByPosition()
   {
      $position = $this->input->post('position');
      $merchant_id = $this->input->post('merchant_id');
      
      if (empty($position) || empty($merchant_id)) {
         echo json_encode([]);
         return;
      }

      $this->load->model('Mutation_model');
      if ($position === 'Credit') {
         $channels = $this->Mutation_model->get_cashin_channels($merchant_id);
      } else {
         $channels = $this->Mutation_model->get_cashout_channels($merchant_id);
      }

      echo json_encode($channels);
   }
}
