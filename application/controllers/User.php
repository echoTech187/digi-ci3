<?php defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
       parent::__construct();

       $this->load->library('rbac');
 
    }
    public function index()
    {
        $data['title'] = 'My Profile';
        $data['user'] = $this->Model_user->view_user()->row_array();
        // $data['menu'] = $this->Model_menu->getMenu();

        $this->load->view('user/index', $data);
    }

    public function changePassword()
    {
        // Pastikan user sudah login
        if (!$this->session->userdata('c_email')) {
            redirect('auth'); // Redirect jika belum login
        }
    
        $data['title'] = 'Change Password';
        $data['user'] = $this->Model_user->view_user()->row_array();
    
        $this->form_validation->set_rules('currentPassword', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('newPassword', 'New Password', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('repeatPassword', 'Repeat Password', 'required|trim|matches[newPassword]');

        if ($this->form_validation->run() == false) {
            $this->load->view('user/changePassword', $data);
        } else {
            $currentPassword = $this->input->post('currentPassword');
            $newPassword = $this->input->post('newPassword');
    
            // Periksa password saat ini
            if (!password_verify($currentPassword, $data['user']['c_password'])) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Wrong current password!</div>');
                redirect('user/changePassword');
            }
    
            // Periksa apakah password baru sama dengan password lama
            if ($currentPassword == $newPassword) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">New password cannot be the same as current password!</div>');
                redirect('user/changePassword');
            }
    
            // Update password
            $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->Model_user->updatePassword($this->session->userdata('c_email'), $password_hash);
    
            $this->session->set_flashdata('message', '<div class="alert alert-success">Password changed successfully!</div>');
            redirect('user/changePassword');
        }
    }
    
}
