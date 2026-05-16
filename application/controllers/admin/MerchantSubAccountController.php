<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani pendaftaran dan pengelolaan Sub-Merchant.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class MerchantSubAccountController extends CI_Controller
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
      $this->load->model('SubMerchant');
      
      // Pastikan user sudah login
      is_logged_in();
   }

   public function Submerchant($id = NULL)
   {
      if (!$id) $id = $this->uri->segment(3);
      if (!$id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('admin/merchant');
      }

      $data['title'] = 'Sub Account';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Mutation_model->get_merchant($id);
      $data['total_submerchants'] = $this->SubMerchant->count_all_dt($id);

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [$id => $merchant_name];

      if ($this->input->is_ajax_request()) {
         try {
            return $this->SubMerchant->get_datatables_handler($id);
         } catch (Throwable $e) {
            log_message('error', 'Submerchant AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving submerchant data: " . $e->getMessage()
            ));
         }
      }

      $this->load->view('submerchant/index', $data);
   }

   public function resetsubmerchant()
   {
      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_submerchant');
      redirect("admin/submerchant/$id");
   }

   public function registersubMerchant()
   {
      $formValidationRules = [
         ['field' => 'ref_merchantId', 'label' => 'Merchant ID', 'rules' => 'trim|required'],
         ['field' => 'c_name', 'label' => 'Nama', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Email', 'rules' => 'trim|required|valid_email'],
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
      } else {
         $data = [];
         $subData = [];
         
         foreach ($formValidationRules as $rule) {
            $field = $rule['field'];
            $val = $this->input->post($field);
            
            if (strpos($field, 'c_gvconnect') === 0) {
                $subData[$field] = $val;
            } elseif ($field !== 'ref_merchantId') {
                $data[$field] = $val;
            }
         }

         $parent_id = $this->input->post('ref_merchantId');
         
         // Set default values for the new merchant account
         $data['c_password'] = password_hash($this->input->post('c_email'), PASSWORD_DEFAULT);
         $data['c_status'] = $this->input->post('c_status') ?: 'Active';
         $data['c_type'] = "Sub Account";

         $newId = $this->SubMerchant->create_submerchant_standard($parent_id, $data, $subData);

         if ($newId) {
            $this->session->set_flashdata('success', 'Submerchant successfully registered');
         } else {
            $this->session->set_flashdata('error', 'Failed to register Submerchant.');
         }

         redirect('admin/submerchant/' . $parent_id);
      }
   }

   public function edit_submerchant($id = null)
   {
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
      } else {
         $this->session->set_flashdata('error', 'Failed to update SubMerchant.');
      }

      $refMerchantId = $this->input->post('ref_merchantId');
      redirect('admin/submerchant/' . $refMerchantId);
   }

   public function get_submerchants()
   {
      if ($this->input->post('merchant_id')) {
         $merchant_id = $this->input->post('merchant_id');
         $submerchants = $this->SubMerchant->get_submerchants_by_merchant_id($merchant_id);

         $options = '<option value="">Pilih SubMerchant</option>';
         foreach ($submerchants as $submerchant) {
            $options .= '<option value="' . $submerchant->id . '">' . $submerchant->c_name . '</option>';
         }
         echo $options;
      }
   }
}
