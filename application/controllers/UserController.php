<?php defined('BASEPATH') or exit('No direct script access allowed');

class UserController extends CI_Controller
{

    public function __construct()
    {
       parent::__construct();
       $this->load->library('rbac');
       $this->load->model('Model_user');
    }

    public function index()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

        $userData = $this->Model_user->view_user()->row_array();

        if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'User profile retrieved successfully',
                'data' => $userData
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $data['title'] = 'My Profile';
        $data['user'] = $userData;
        $this->load->view('user/index', $data);
    }

    public function changePassword()
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

        $email = $this->session->userdata('c_email');
        if (!$email) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthenticated: Please login first.'
                ]));
            }
            redirect('auth');
            return;
        }

        $currentPassword = $this->input->post('current_password') ?: $this->input->post('currentPassword');
        $newPassword = $this->input->post('new_password') ?: $this->input->post('newPassword');
        $repeatPassword = $this->input->post('confirm_password') ?: ($this->input->post('repeatPassword') ?: $this->input->post('repeat_password'));

        if (!empty($currentPassword)) $_POST['currentPassword'] = $currentPassword;
        if (!empty($newPassword)) $_POST['newPassword'] = $newPassword;
        if (!empty($repeatPassword)) $_POST['repeatPassword'] = $repeatPassword;

        $data['title'] = 'Change Password';
        $data['user'] = $this->Model_user->view_user()->row_array();

        $this->form_validation->set_rules('currentPassword', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('newPassword', 'New Password', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('repeatPassword', 'Repeat Password', 'required|trim|matches[newPassword]');

        if ($this->form_validation->run() == false) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => validation_errors() ? trim(strip_tags(validation_errors())) : 'Validation error: All password fields are required.'
                ]));
            }
            $this->load->view('user/changePassword', $data);
        } else {
            // Periksa password saat ini
            if (empty($data['user']['c_password']) || !password_verify($currentPassword, $data['user']['c_password'])) {
                if ($is_api_request) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode([
                        'status' => false,
                        'message' => 'Wrong current password!'
                    ]));
                }
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Wrong current password!</div>');
                redirect('user/change-password');
                return;
            }

            // Periksa apakah password baru sama dengan password lama
            if ($currentPassword == $newPassword) {
                if ($is_api_request) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode([
                        'status' => false,
                        'message' => 'New password cannot be the same as current password!'
                    ]));
                }
                $this->session->set_flashdata('message', '<div class="alert alert-danger">New password cannot be the same as current password!</div>');
                redirect('user/change-password');
                return;
            }

            // Update password
            $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->Model_user->updatePassword($email, $password_hash);

            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Password changed successfully!'
                ]));
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success">Password changed successfully!</div>');
            redirect('user/change-password');
        }
    }
}
