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
         $this->session->set_flashdata('error', 'All fields are required.');
         redirect('admin/holiday');
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
         $this->session->set_flashdata('success', 'Holiday updated successfully.');
      } else {
         // Insert new holiday
         $this->HolidayModel->add_holiday($data);
         $this->session->set_flashdata('success', 'Holiday added successfully.');
      }

      redirect('admin/holiday');
   }

   public function listAdmin()
   {
      $this->load->model('AdminModel');

      // Intercept AJAX request for DataTables
      if ($this->input->is_ajax_request()) {
         try {
            return $this->AdminModel->get_datatables_handler();
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

   public function manageUsers()
   {
      // Load model
      $this->load->model('AdminModel');

      // Get POST data
      $c_name   = $this->input->post('c_name');
      $c_level   = $this->input->post('c_level');
      $c_status = $this->input->post('c_status');
      $role_id = $this->input->post('role_id'); 
      $c_password = $this->input->post('c_password');

      $data = [
         'c_name'    => $c_name,
         'c_level'   => $c_level,
         'role_id'   => $role_id,
         'c_status'  => $c_status
      ];

      if (!empty($c_password)) {
         $data['c_password'] = password_hash($c_password, PASSWORD_DEFAULT);
      }

      $id = $this->uri->segment(3);
      if (!$id) {
         $this->session->set_flashdata('error', 'User ID not found.');
         redirect('admin/listadmin');
      }
      $this->AdminModel->update_admin($id, $data);
      $this->session->set_flashdata('success', 'Admin updated successfully.');

      redirect('admin/listadmin');
   }

}
