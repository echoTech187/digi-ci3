<?php defined('BASEPATH') or exit('No direct script access allowed');

class MerchantSupervisorController extends CI_Controller
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

   public function merchant_spv()
   {
      // Clear session if direct access (not ajax) without parameters
      if (!$this->input->is_ajax_request() && $this->input->get('search_spv') === null && $this->input->post('search_spv') === null) {
         $this->session->unset_userdata('search_spv');
      }

      $search_spv = $this->input->get('search_spv') ?: $this->input->post('search_spv');
      if ($search_spv !== null) {
         $this->session->set_userdata('search_spv', $search_spv);
      } else {
         $search_spv = $this->session->userdata('search_spv');
      }

      if ($this->input->is_ajax_request()) {
         try {
            $where = [];
            
            $search_spv_sess = $this->session->userdata('search_spv');
            $filter_status = $this->session->userdata('search_spv_status');
            if (!empty($filter_status)) {
               $where['c_status'] = $filter_status;
            }

            $filter_date_from = $this->session->userdata('search_spv_date_from');
            $filter_date_to = $this->session->userdata('search_spv_date_to');
            if (!empty($filter_date_from)) {
               $where['c_created_date >='] = $filter_date_from . ' 00:00:00';
            }
            if (!empty($filter_date_to)) {
               $where['c_created_date <='] = $filter_date_to . ' 23:59:59';
            }

            return $this->Merchant->get_merchant_spv_handler($where, $search_spv_sess);
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

      $search_status = $this->input->post('search_spv_status');
      if ($search_status !== null) {
         $this->session->set_userdata('search_spv_status', $search_status);
      } else {
         $search_status = $this->session->userdata('search_spv_status');
      }

      $search_date_from = $this->input->post('search_spv_date_from');
      if ($search_date_from !== null) {
         $this->session->set_userdata('search_spv_date_from', $search_date_from);
      } else {
         $search_date_from = $this->session->userdata('search_spv_date_from');
      }

      $search_date_to = $this->input->post('search_spv_date_to');
      if ($search_date_to !== null) {
         $this->session->set_userdata('search_spv_date_to', $search_date_to);
      } else {
         $search_date_to = $this->session->userdata('search_spv_date_to');
      }

      $this->load->view('merchantspv/index', $data);
   }

   public function resetMerchantSpv()
   {
      $this->session->unset_userdata('search_spv_status');
      $this->session->unset_userdata('search_spv_date_from');
      $this->session->unset_userdata('search_spv_date_to');
      redirect("merchant/supervisor");
   }

   public function listMerchants($supervisorId)
   {
      if (!$supervisorId) {
         $this->session->set_flashdata('error', 'Supervisor ID not found.');
         redirect('merchant/supervisor');
      }

      if ($this->input->is_ajax_request()) {
         try {
            $where = [];
            $filter_status = $this->session->userdata('search_spv_merchant_status');
            if (!empty($filter_status)) {
               $where['merchant.c_status'] = $filter_status;
            }

            $filter_openapi_status = $this->session->userdata('search_spv_merchant_openapi_status');
            if (!empty($filter_openapi_status)) {
               $where['merchant.c_openapiStatus'] = $filter_openapi_status;
            }

            $filter_date_from = $this->session->userdata('search_spv_merchant_date_from');
            $filter_date_to = $this->session->userdata('search_spv_merchant_date_to');
            if (!empty($filter_date_from)) {
               $where['merchant.c_dateCreated >='] = $filter_date_from . ' 00:00:00';
            }
            if (!empty($filter_date_to)) {
               $where['merchant.c_dateCreated <='] = $filter_date_to . ' 23:59:59';
            }

            $role_id = $this->session->userdata('role');
            $hasBalancePermission = $this->rbac->has_permission($role_id, 'balance_merchant_module');
            return $this->Merchant->get_merchants_by_supervisor_handler($supervisorId, $hasBalancePermission, $where);
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

      $search_status = $this->input->post('search_spv_merchant_status');
      if ($search_status !== null) {
         $this->session->set_userdata('search_spv_merchant_status', $search_status);
      } else {
         $search_status = $this->session->userdata('search_spv_merchant_status');
      }

      $search_openapi = $this->input->post('search_spv_merchant_openapi_status');
      if ($search_openapi !== null) {
         $this->session->set_userdata('search_spv_merchant_openapi_status', $search_openapi);
      } else {
         $search_openapi = $this->session->userdata('search_spv_merchant_openapi_status');
      }

      $search_date_from = $this->input->post('search_spv_merchant_date_from');
      if ($search_date_from !== null) {
         $this->session->set_userdata('search_spv_merchant_date_from', $search_date_from);
      } else {
         $search_date_from = $this->session->userdata('search_spv_merchant_date_from');
      }

      $search_date_to = $this->input->post('search_spv_merchant_date_to');
      if ($search_date_to !== null) {
         $this->session->set_userdata('search_spv_merchant_date_to', $search_date_to);
      } else {
         $search_date_to = $this->session->userdata('search_spv_merchant_date_to');
      }

      $data['merchants'] = $this->db->get_where('merchant', ['c_refSupervisor' => $supervisorId])->result_array();

      $supervisor = $this->db->get_where('merchant_supervisor', ['id' => $supervisorId])->row_array();
      $supervisor_name = $supervisor ? $supervisor['c_name'] : 'Supervisor';
      $data['breadcrumb_replace'] = [$supervisorId => $supervisor_name];

      $this->load->view('merchantspv/list', $data);
   }

   public function resetListMerchants($supervisorId)
   {
      $this->session->unset_userdata('search_spv_merchant_status');
      $this->session->unset_userdata('search_spv_merchant_openapi_status');
      $this->session->unset_userdata('search_spv_merchant_date_from');
      $this->session->unset_userdata('search_spv_merchant_date_to');
      redirect("merchant/manage/list/" . $supervisorId);
   }


   public function registerMerchantSpv() 
   {
      $this->load->library('MerchantRegistrationService', null, 'MerchantRegistrationService');
      try {
         $result = $this->MerchantRegistrationService->registerSupervisor($this->input->post());
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Merchant Supervisor Added Successfully.']);
                return;
            }
            $this->session->set_flashdata('success', 'Merchant Supervisor Added Successfully.');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to create supervisor account due to a system constraint. Please verify your input or contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to create supervisor accounts.';
            } elseif ($code == 1062) {
               $msg = 'A supervisor account with this username or email already exists.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }
         redirect('merchant/supervisor');
      } catch (Exception $e) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
             return;
         }
         $this->session->set_flashdata('error', $e->getMessage());
         if ($e->getMessage() === 'Password not match') {
            redirect('merchant/supervisor/register');
         } else {
            redirect('merchant/supervisor');
         }
      }
   }

   public function deleteMerchantSpv($id)
   {
      if (!$id) {
         $this->session->set_flashdata('error', 'Supervisor ID missing.');
         redirect('merchant/supervisor');
      }


      $this->db->trans_start();
      $this->db->where('c_refSupervisor', $id);
      $successUpdate = $this->db->update('merchant', ['c_refSupervisor' => NULL]);
      $errUpdate = $this->db->error();

      $this->db->where('id', $id);
      $successDelete = $this->db->delete('merchant_supervisor');
      $errDelete = $this->db->error();

      $this->db->trans_complete();

      if (!$successDelete || !$successUpdate) {
         $err = !$successDelete ? $errDelete : $errUpdate;
         $code = isset($err['code']) ? $err['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete supervisor accounts.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete supervisor because active merchant records are still linked to this account.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete supervisor account due to a system constraint. Please contact technical support.');
         }
      } else {
         $this->session->set_flashdata('success', 'Supervisor deleted successfully.');
      }
      redirect('merchant/supervisor');
   }


   public function getSupervisorJson($id = null)
   {
      if (!$id) {
         $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Supervisor ID missing.']));
         return;
      }

      $supervisor = $this->db->get_where('merchant_supervisor', ['id' => $id])->row_array();
      if (!$supervisor) {
         $this->output->set_status_header(404)->set_output(json_encode(['status' => 'error', 'message' => 'Supervisor not found.']));
         return;
      }

      // Ambil merchant yang di-assign ke supervisor ini
      $this->db->select('id, c_name');
      $this->db->from('merchant');
      $this->db->where('c_refSupervisor', $id);
      $merchants = $this->db->get()->result_array();

      $supervisor['assigned_merchants'] = $merchants;
      
      $this->output->set_content_type('application/json')->set_output(json_encode($supervisor));
   }

   public function updateMerchantSpv($id = null)
   {
      if (!$id) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Supervisor ID missing.']);
             return;
         }
         $this->session->set_flashdata('error', 'Supervisor ID missing.');
         redirect('merchant/supervisor');
      }

      $this->load->library('MerchantRegistrationService', null, 'MerchantRegistrationService');
      try {
         $result = $this->MerchantRegistrationService->updateSupervisor($id, $this->input->post());
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Merchant Supervisor Updated Successfully.']);
                return;
            }
            $this->session->set_flashdata('success', 'Merchant Supervisor Updated Successfully.');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to update supervisor account due to a system constraint.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient privileges to edit supervisor accounts.';
            } elseif ($code == 1062) {
               $msg = 'A supervisor account with this username or email already exists.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }
      } catch (Exception $e) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
             return;
         }
         $this->session->set_flashdata('error', $e->getMessage());
      }
      redirect('merchant/supervisor');
   }

   public function fetchMerchantPermissions($merchantId = null)
   {
      if (!$merchantId) {
         echo json_encode(['status' => 'error', 'message' => 'ID missing']);
         return;
      }
      $all_permissions = $this->Merchant->get_rbac_permissions();
      $explicit_grants = $this->Merchant->get_merchant_explicit_grants($merchantId);
      $grants_map = [];
      foreach ($explicit_grants as $g) $grants_map[$g->ref_permissionId] = $g->c_isAllowed;

      // Load merchant's active role ID to determine the inherited permission state
      $role_id = null;
      $user_role = $this->db->get_where('rbac_user_roles', [
         'ref_userId' => $merchantId,
         'c_user_type' => 'merchant',
         'c_isActive' => 1
      ])->row();
      if ($user_role) {
         $role_id = $user_role->ref_roleId;
      } else {
         // Fallback to default merchant role if none assigned
         $default_role = $this->db->get_where('rbac_roles', ['c_isDefault' => 1, 'c_name' => 'merchant_basic'])->row();
         if ($default_role) {
            $role_id = $default_role->id;
         }
      }

      $role_permissions = [];
      if ($role_id) {
         $role_perms_query = $this->db->get_where('rbac_role_permissions', ['ref_roleId' => $role_id])->result();
         foreach ($role_perms_query as $rp) {
            $role_permissions[$rp->ref_permissionId] = true;
         }
      }

      $data = [];
      foreach ($all_permissions as $p) {
         if (isset($grants_map[$p->id])) {
            $status = ($grants_map[$p->id] == 1) ? 'Grant' : 'Deny';
         } else {
            // Default to the role's inherited permission status (rather than hardcoded Deny)
            $status = isset($role_permissions[$p->id]) ? 'Grant' : 'Deny';
         }
         $data[] = ['id' => $p->id, 'name' => $p->c_code, 'label' => $p->c_name, 'description' => $p->c_description, 'status' => $status];
      }
      $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
   }

   public function saveDelegation($merchantId)
   {
      $merchantId =$merchantId ?? $this->input->post('merchantId');
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
}
