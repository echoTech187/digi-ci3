<?php defined('BASEPATH') or exit('No direct script access allowed');

class UserAccessController extends CI_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->model('Model_user');
      $this->load->model('AdminModel');
      $this->load->model('HolidayModel');
      is_logged_in();
   }

   public function holiday()
   {
      $serviceName            = "view_holidays";
      $data['title']          = 'Holiday Calendar';
      $data['service_name']   = $serviceName;

      $this->load->model('HolidayModel');
      $data['holidays'] = $this->HolidayModel->get_holidays() != NULL ? $this->HolidayModel->get_holidays() : [];
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['holidays'] = array_map(function($row) {
         return [
            'id'              => $row->c_date,
            'title'           => $row->c_desc,
            'start'           => $row->c_date,
            'status'          => $row->c_status,
            'backgroundColor' => strtolower($row->c_status) == 'active' ? '' : 'gray',
            'resourceId'      => 'holidays',
            'desc'            => $row->c_desc,
         ];
      }, $data['holidays']);
   
      $this->load->view('admin/holiday', $data);
   }

   public function manageHoliday() 
   {
      // Load model
      $this->load->model('HolidayModel');

      // Get POST data
      $c_date   = $this->input->post('c_date');
      $c_desc   = $this->input->post('c_desc');
      $c_status = $this->input->post('c_status');
      $c_action = $this->input->post('c_action'); // create or update

      // Basic validation
      if (empty($c_date) || empty($c_desc) || empty($c_status)) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
             return;
         }
         $this->session->set_flashdata('error', 'All fields are required.');
         redirect('access-control/holiday');
         return;
      }

      $data = [
         'c_date'   => $c_date,
         'c_desc'   => $c_desc,
         'c_status' => $c_status
      ];

      if ($c_action === 'update') {
         // Update existing holiday by date
         $this->HolidayModel->update_holiday($c_date, $data);
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Holiday updated successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Holiday updated successfully.');
      } else {
         // Insert new holiday
         $this->HolidayModel->add_holiday($data);
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Holiday added successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Holiday added successfully.');
      }

      redirect('access-control/holiday');
   }

   public function listAdmin()
   {
      $this->load->model('AdminModel');

      // Intercept AJAX request for DataTables
      if ($this->input->is_ajax_request()) {
         try {
            $filters = [];
            if ($this->input->post('role_id')) {
                $filters['a.role_id'] = $this->input->post('role_id');
            }
            if ($this->input->post('status')) {
                $filters['a.c_status'] = $this->input->post('status');
            }
            return $this->AdminModel->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'Admin List AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving admin list data: " . $e->getMessage()
            ));
         }
      }

      $serviceName            = "manage_users";
      $data['title']          = 'Admin Accounts';
      $data['service_name']   = $serviceName;

      $data['roles'] = $this->AdminModel->get_roles() != NULL ? $this->AdminModel->get_roles() : [];
      $data['user'] = $this->Model_user->view_user()->row_array();
   
      $this->load->view('admin/index', $data);
   }

   public function createAdmin()
   {
      $this->load->model('AdminModel');

      $c_email  = $this->input->post('c_email');
      $c_name   = $this->input->post('c_name');
      $c_level  = $this->input->post('c_level');
      $c_status = $this->input->post('c_status');
      $role_id  = $this->input->post('role_id'); 
      $c_password = $this->input->post('c_password');
      $c_password_confirm = $this->input->post('c_password_confirm');

      if (empty($c_email) || empty($c_password) || empty($c_password_confirm)) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Email, Password, and Password Confirmation are required for new admin.']);
             return;
         }
         $this->session->set_flashdata('error', 'Email, Password, and Password Confirmation are required for new admin.');
         redirect('access-control/accounts');
         return;
      }
      if ($c_password !== $c_password_confirm) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Password and Password Confirmation do not match.']);
             return;
         }
         $this->session->set_flashdata('error', 'Password and Password Confirmation do not match.');
         redirect('access-control/accounts');
         return;
      }
      $existing = $this->db->get_where('admin', ['c_email' => $c_email])->row();
      if ($existing) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
             return;
         }
         $this->session->set_flashdata('error', 'Email already exists.');
         redirect('access-control/accounts');
         return;
      }
      if (!in_array($c_level, ['1', '2'])) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid Level. Allowed values are 1 or 2.']);
             return;
         }
         $this->session->set_flashdata('error', 'Invalid Level. Allowed values are 1 or 2.');
         redirect('access-control/accounts');
         return;
      }

      $data = [
         'c_email'   => $c_email,
         'c_name'    => $c_name,
         'c_level'   => $c_level,
         'role_id'   => $role_id,
         'c_status'  => $c_status,
         'c_password'=> password_hash($c_password, PASSWORD_DEFAULT)
      ];

      $result = $this->AdminModel->add_admin($data);
      if ($result === true) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Admin added successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Admin added successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to create admin account due to a system constraint. Please verify your input or contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to create administrator accounts.';
         } elseif ($code == 1062) {
            $msg = 'An account with these credentials already exists in the system.';
         }
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $msg]);
             return;
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('access-control/accounts');
   }

   public function updateAdmin($id = NULL)
   {
      $this->load->model('AdminModel');

      if (!$id) {
         $id = $this->uri->segment(4);
      }

      if (empty($id)) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid admin ID.']);
             return;
         }
         $this->session->set_flashdata('error', 'Invalid admin ID.');
         redirect('access-control/accounts');
         return;
      }

      $c_email  = $this->input->post('c_email');
      $c_name   = $this->input->post('c_name');
      $c_level  = $this->input->post('c_level');
      $c_status = $this->input->post('c_status');
      $role_id  = $this->input->post('role_id'); 
      $c_password = $this->input->post('c_password');
      $c_password_confirm = $this->input->post('c_password_confirm');

      if (!in_array($c_level, ['1', '2'])) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid Level. Allowed values are 1 or 2.']);
             return;
         }
         $this->session->set_flashdata('error', 'Invalid Level. Allowed values are 1 or 2.');
         redirect('access-control/accounts');
         return;
      }

      $data = [
         'c_name'    => $c_name,
         'c_level'   => $c_level,
         'role_id'   => $role_id,
         'c_status'  => $c_status
      ];

      if (!empty($c_email)) {
         // Check if email already exists for a DIFFERENT admin
         $existing = $this->db->get_where('admin', ['c_email' => $c_email, 'id !=' => $id])->row();
         if ($existing) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists for another account.']);
                return;
            }
            $this->session->set_flashdata('error', 'Email already exists for another account.');
            redirect('access-control/accounts');
            return;
         }
         $data['c_email'] = $c_email;
      }

      if (!empty($c_password)) {
         if ($c_password !== $c_password_confirm) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Password and Password Confirmation do not match.']);
                return;
            }
            $this->session->set_flashdata('error', 'Password and Password Confirmation do not match.');
            redirect('access-control/accounts');
            return;
         }
         $data['c_password'] = password_hash($c_password, PASSWORD_DEFAULT);
      }

      $result = $this->AdminModel->update_admin($id, $data);
      if ($result === true) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Admin updated successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Admin updated successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to update account details due to a system constraint. Please verify your input or contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to modify administrator accounts.';
         } elseif ($code == 1062) {
            $msg = 'The email address provided is already registered to another account.';
         }
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $msg]);
             return;
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('access-control/accounts');
   }

   public function deleteAdmin($id = NULL)
   {
      $this->load->model('AdminModel');

      if (!$id) {
         $id = $this->uri->segment(4);
      }

      if (empty($id)) {
         $this->session->set_flashdata('error', 'Invalid admin ID.');
         redirect('access-control/accounts');
         return;
      }

      // Prevent deleting oneself
      $currentUser = $this->Model_user->view_user()->row_array();
      if ($currentUser && isset($currentUser['id']) && $currentUser['id'] == $id) {
         $this->session->set_flashdata('warning', 'You cannot delete your own account while logged in.');
         redirect('access-control/accounts');
         return;
      }

      $result = $this->AdminModel->delete_admin($id);
      if ($result === true) {
         $this->session->set_flashdata('success', 'Admin account deleted successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete administrator accounts.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this account because it is currently linked to existing transaction or activity records.');
         } else {
            $this->session->set_flashdata('error', 'Unable to complete the deletion process due to a system constraint. Please contact technical support.');
         }
      }
      redirect('access-control/accounts');
   }

}

