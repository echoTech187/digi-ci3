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
         redirect('merchant/manage');
      }

      $data['title'] = 'Sub Account';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Mutation_model->get_merchant($id);
      $data['total_submerchants'] = $this->SubMerchant->count_all_dt($id);

      // Calculate merchant level from c_ancestorPath
      $curr_merchant = $this->db->select('c_ancestorPath')->get_where('merchant', ['id' => $id])->row_array();
      $ancestorPath = (isset($curr_merchant['c_ancestorPath']) && $curr_merchant['c_ancestorPath'] !== '') ? trim($curr_merchant['c_ancestorPath'], '/') : '';
      if (empty($ancestorPath)) {
          $merchant_level = 0;
      } else {
          $merchant_level = count(explode('/', $ancestorPath)) - 1;
      }
      $data['merchant_level'] = $merchant_level;

      // Breadcrumb override: Replace ID with Merchant Name
      $merchant_name = isset($data['merchant'][0]) ? $data['merchant'][0]->c_name : 'Merchant';
      $data['breadcrumb_replace'] = [$id => $merchant_name];

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos((string)$this->input->get_request_header('Accept'), 'json') !== false
         || $this->input->method() === 'post';

      if ($is_api) {
         try {
            $out = $this->SubMerchant->get_datatables_handler($id);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'Submerchant AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving submerchant data: " . $e->getMessage()
               )));
            return;
         }
      }

      $this->load->view('submerchant/index', $data);
   }

   public function resetsubmerchant()
   {
      $id = $this->uri->segment(3);
      $this->session->unset_userdata('search_submerchant');

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Submerchant search filters reset successfully.'
         ]));
         return;
      }

      redirect("merchant/sub-account/$id");
   }

   public function registersubMerchant()
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


      $formValidationRules = [
         ['field' => 'ref_merchantId', 'label' => 'Merchant ID', 'rules' => 'trim|required'],
         ['field' => 'c_name', 'label' => 'Nama', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_status', 'label' => 'Status', 'rules' => 'trim|required']
      ];

      $this->form_validation->set_rules($formValidationRules);

      if ($this->form_validation->run() == FALSE) {
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $clean_error ?: 'Validation failed.']));
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/sub-account/' . $this->input->post('ref_merchantId'));
         return;
      }

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
      
      // Check maximum level depth limit of 3 using c_ancestorPath
      $parent_merchant = $this->db->select('c_ancestorPath')->get_where('merchant', ['id' => $parent_id])->row_array();
      $ancestorPath = (isset($parent_merchant['c_ancestorPath']) && $parent_merchant['c_ancestorPath'] !== '') ? trim($parent_merchant['c_ancestorPath'], '/') : '';
      if (empty($ancestorPath)) {
          $parent_level = 0;
      } else {
          $parent_level = count(explode('/', $ancestorPath)) - 1;
      }
      
      if ($parent_level >= 3) {
          if ($is_api_request) {
              $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Maximum hierarchy depth level of 3 has been reached. Cannot create deeper sub-accounts.']));
              return;
          }
          $this->session->set_flashdata('error', 'Maximum hierarchy depth level of 3 has been reached. Cannot create deeper sub-accounts.');
          redirect('merchant/sub-account/' . $parent_id);
          return;
      }
      
      // Set default values for the new merchant account
      $data['c_password'] = password_hash($this->input->post('c_email'), PASSWORD_DEFAULT);
      $data['c_status'] = $this->input->post('c_status') ?: 'Active';

      $newId = $this->SubMerchant->create_submerchant_standard($parent_id, $data, $subData);

      if ($newId) {
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Submerchant successfully registered']));
             return;
         }
         $this->session->set_flashdata('success', 'Submerchant successfully registered');
         redirect('merchant/sub-account/' . $parent_id);
      } else {
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Failed to register Submerchant.']));
             return;
         }
         $this->session->set_flashdata('error', 'Failed to register Submerchant.');
         redirect('merchant/sub-account/' . $parent_id);
      }
   }

   public function edit_submerchant($id = null)
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

      if (!$id) $id = $this->uri->segment(4);

      $existing = $id ? $this->db->get_where('submerchant', ['id' => $id])->row_array() : null;


      $this->form_validation->set_rules('c_name', 'Nama', 'required');
      $this->form_validation->set_rules('c_email', 'Email', 'required|valid_email');

      if ($this->form_validation->run() == FALSE) {
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $clean_error ?: 'Validation failed.']));
             return;
         }
         $this->session->set_flashdata('error', validation_errors());
         redirect($_SERVER['HTTP_REFERER']);
         return;
      }

      $data = [
         'c_name' => $this->input->post('c_name', true),
         'c_email' => $this->input->post('c_email', true),
         'c_status' => $this->input->post('c_status', true),
      ];

      $updated = $this->SubMerchant->update_submerchant($id, $data);
      $refMerchantId = $this->input->post('ref_merchantId') ?: ($existing ? $existing['ref_merchantId'] : '');

      if ($updated) {
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'SubMerchant was successfully updated.']));
             return;
         }
         $this->session->set_flashdata('success', 'SubMerchant was successfully updated.');
         redirect('merchant/sub-account/' . $refMerchantId);
      } else {
         if ($is_api_request) {
             $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update SubMerchant.']));
             return;
         }
         $this->session->set_flashdata('error', 'Failed to update SubMerchant.');
         redirect('merchant/sub-account/' . $refMerchantId);
      }
   }

   public function get_submerchants()
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

      $merchant_id = $this->input->get('merchant_id') ?: $this->input->post('merchant_id');
      $submerchants = $this->SubMerchant->get_submerchants_by_merchant_id($merchant_id);

      if ($is_api_request) {
         $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
               'status' => true,
               'message' => 'Submerchants list retrieved successfully',
               'data' => $submerchants
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         return;
      }

      $options = '<option value="">Pilih SubMerchant</option>';
      foreach ($submerchants as $submerchant) {
         $options .= '<option value="' . $submerchant->id . '">' . $submerchant->c_name . '</option>';
      }
      echo $options;
   }
}
