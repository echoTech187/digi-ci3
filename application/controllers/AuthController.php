<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AuthController
 * Skinny routing controller leveraging AuthService for business logic.
 */
class AuthController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('secrets', TRUE, TRUE);
        $this->load->library('AuthService');
        $this->load->model('NotificationModel');
    }

    public function index()
    {
        if ($this->session->userdata('c_email')) {
            redirect($this->authservice->getRedirectRoute());
        } elseif ($this->session->userdata('email')) {
            redirect('user');
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() === false) {
            $secrets = $this->config->item('secrets');
            $data = [
                'title' => 'Login Admin GIDI',
                'recaptcha_site_key' => $secrets['recaptcha_site_key'] ?? ''
            ];

            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/auth_footer.php');
        } else {
            $this->_login();
        }
    }

    private function _login()
    {
        $this->load->helper('recaptcha');
        $raw_json = json_decode($this->input->raw_input_stream, true);
        $c_email = $this->input->post('email') ?: ($raw_json['email'] ?? null);
        $adminPassword = $this->input->post('password') ?: ($raw_json['password'] ?? null);
        $recaptchaResponse = $this->input->post('g-recaptcha-response') ?: ($raw_json['g-recaptcha-response'] ?? null);

        $content_type = (string) $this->input->get_request_header('Content-Type');
        $accept_header = (string) $this->input->get_request_header('Accept');
        $req_with = (string) $this->input->get_request_header('X-Requested-With');

        $is_ajax = $this->input->is_ajax_request()
            || strtolower($req_with) === 'xmlhttprequest'
            || strpos($content_type, 'json') !== false
            || strpos($accept_header, 'json') !== false
            || (!empty($c_email) && !empty($adminPassword));

        if (empty($recaptchaResponse) && !$is_ajax) {
            $secrets = $this->config->item('secrets');
            $data = [
                'title' => 'Login Admin GIDI',
                'error_message' => 'Please complete the reCAPTCHA verification!',
                'recaptcha_site_key' => $secrets['recaptcha_site_key'] ?? ''
            ];
            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/auth_footer.php');
            return;
        }

        if (!empty($recaptchaResponse) && !$is_ajax) {
            $secrets = $this->config->item('secrets');
            $recaptchaSecret = $secrets['recaptcha_secret_key'] ?? '';
            $response = verify_recaptcha($recaptchaResponse, $recaptchaSecret);

            if (!$response['success']) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">reCAPTCHA validation failed!</div>');
                $data = [
                    'title' => 'Login Admin GIDI',
                    'recaptcha_site_key' => $secrets['recaptcha_site_key'] ?? ''
                ];
                $this->load->view('templates/auth_header.php', $data);
                $this->load->view('auth/login', $data);
                $this->load->view('templates/auth_footer.php');
                return;
            }
        }

        $result = $this->authservice->authenticate($c_email, $adminPassword);

        if ($result['status']) {
            if ($is_ajax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => 'success',
                    'message' => $result['message'],
                    'redirect' => base_url('dashboard'),
                    'user' => [
                        'id' => $result['admin']['id'],
                        'c_name' => $result['admin']['c_name'],
                        'c_email' => $result['admin']['c_email'],
                        'role' => $result['admin']['role_id']
                    ]
                ]));
            }
            redirect($result['redirect']);
        } else {
            if ($is_ajax) {
                return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]));
            }
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">' . $result['message'] . '</div>');
            redirect('auth');
        }
    }

    public function register()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post' || $is_swagger;

        if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Access Denied: Registration is closed. Contact Super Admin to create an account.'
            ]));
        }

        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Access Denied: Registration is closed. Contact Super Admin to create an account.</div>');
        redirect('auth');
    }

    public function verify()
    {
        $email = $this->input->get('email') ?: $this->input->post('email');
        $token = $this->input->get('token') ?: $this->input->post('token');
        $is_api = $this->input->is_ajax_request() || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $result = $this->authservice->verifyAccount($email, $token);

        if ($is_api) {
            return $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }

        $alertClass = $result['status'] ? 'alert-success' : 'alert-danger';
        $this->session->set_flashdata('message', '<div class="alert ' . $alertClass . '" role="alert">' . $result['message'] . '</div>');
        redirect('auth');
    }

    public function logout()
    {
        $this->session->unset_userdata(['email', 'c_email', 'role_id', 'role', 'id', 'ref_entity']);
        $this->session->sess_destroy();

        $is_api = $this->input->is_ajax_request() || $this->input->get('json') == '1' || $this->input->method() === 'post';
        if ($is_api) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'You have been logged out successfully.'
            ]));
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sesi Anda telah berakhir, silakan login kembali.</div>');
        redirect('auth');
    }

    public function blocked()
    {
        if ($this->input->is_ajax_request() || $this->input->get('json') == '1') {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Access Denied: You do not have permission to access this resource.'
            ]));
        }
        $this->load->view('auth/blocked');
    }

    public function forgotPassword()
    {
        $email = $this->input->post('email') ?: $this->input->get('email');
        $is_api = $this->input->is_ajax_request() || $this->input->get('json') == '1' || $this->input->method() === 'post';

<<<<<<< HEAD
        if ($is_api_request) {
            if (empty($email)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Please provide a valid email address.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }

            $user = null;
            if ($this->db->table_exists('user')) {
                $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
            }
            if (!$user) {
                $user = $this->db->get_where('admin', ['c_email' => $email, 'c_status' => 'Active'])->row_array();
            }

            if ($user) {
                $token = base64_encode(random_bytes(32));
                if ($this->db->table_exists('user_token')) {
                    $user_token = [
                        'email' => $email,
                        'token' => $token,
                        'date_created' => time()
                    ];
                    $this->db->insert('user_token', $user_token);
                }
                try {
                    $this->_sendEmail($token, 'forgot');
                } catch (Throwable $e) {
                    log_message('error', 'Failed to send reset password email: ' . $e->getMessage());
                }

                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Please check your email to reset your password!'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            } else {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Email is not registered or activated!'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
=======
        if ($is_api) {
            $result = $this->authservice->requestPasswordReset($email);
            return $this->output->set_content_type('application/json')->set_output(json_encode($result));
>>>>>>> a49b5fe1bf4e6664c99daaa483c66bfbc1e0d4f7
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() === false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/forgotPassword');
            $this->load->view('templates/auth_footer.php');
        } else {
<<<<<<< HEAD
            $email = $this->input->post('email');
            $user = null;
            if ($this->db->table_exists('user')) {
                $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
            }
            if (!$user) {
                $user = $this->db->get_where('admin', ['c_email' => $email, 'c_status' => 'Active'])->row_array();
            }

            if ($user) {
                $token = base64_encode(random_bytes(32));
                if ($this->db->table_exists('user_token')) {
                    $user_token = [
                        'email' => $email,
                        'token' => $token,
                        'date_created' => time()
                    ];

                    $this->db->insert('user_token', $user_token);
                }
                try {
                    $this->_sendEmail($token, 'forgot');
                } catch (Throwable $e) {
                    log_message('error', 'Failed to send reset password email: ' . $e->getMessage());
                }

                $msg = 'Please check your email to reset your password!';
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $msg . '</div>');
                redirect('auth/forgotpassword');
            } else {
                $msg = 'Email is not registered or activated!';
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">' . $msg . '</div>');
                redirect('auth/forgotpassword');
            }
=======
            $result = $this->authservice->requestPasswordReset($email);
            $alertClass = $result['status'] ? 'alert-success' : 'alert-danger';
            $this->session->set_flashdata('message', '<div class="alert ' . $alertClass . '" role="alert">' . $result['message'] . '</div>');
            redirect('auth/forgotpassword');
>>>>>>> a49b5fe1bf4e6664c99daaa483c66bfbc1e0d4f7
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email') ?: $this->input->post('email');
        $token = $this->input->get('token') ?: $this->input->post('token');
        $new_password = $this->input->post('new_password') ?: ($this->input->post('password1') ?: $this->input->post('newPassword'));
        $is_api = $this->input->is_ajax_request() || $this->input->get('json') == '1' || strtolower($this->input->method()) === 'post';

        if ($is_api) {
            $result = $this->authservice->processPasswordReset($token, $email, $new_password);
            return $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }

        if (!empty($email)) {
            $this->session->set_userdata('reset_email', $email);
            $this->changePassword();
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset password failed! Wrong or missing email.</div>');
            redirect('auth');
        }
    }

    public function changePassword()
    {
        $is_api = $this->input->is_ajax_request() || $this->input->get('json') == '1' || $this->input->method() === 'post';
        $current_password = $this->input->post('current_password') ?: $this->input->post('currentPassword');
        $new_password = $this->input->post('new_password') ?: ($this->input->post('newPassword') ?: $this->input->post('password1'));
        $confirm_password = $this->input->post('confirm_password') ?: ($this->input->post('confirmPassword') ?: ($this->input->post('password2') ?: $this->input->post('repeatPassword')));

        if ($is_api) {
            if (empty($current_password) || empty($new_password)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Current password and new password are required.'
                ]));
            }
            if (!empty($confirm_password) && $new_password !== $confirm_password) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Password confirmation does not match.'
                ]));
            }

            $email = $this->session->userdata('c_email') ?: ($this->session->userdata('reset_email') ?: $this->session->userdata('email'));
            $result = $this->authservice->changePassword($email, $current_password, $new_password);
            return $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }

        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
            return;
        }

        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[3]|matches[password1]');

        if ($this->form_validation->run() === false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->session->userdata('reset_email');
            $this->authservice->processPasswordReset(null, $email, $this->input->post('password1'));
            $this->session->unset_userdata('reset_email');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password has been changed! Please login.</div>');
            redirect('auth');
        }
    }
}
