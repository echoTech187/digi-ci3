<?php defined('BASEPATH') or exit('No direct script access allowed');

class AuthController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('secrets', TRUE, TRUE);
        $this->load->model('NotificationModel');
    }

    public function index()
    {
        if ($this->session->userdata('c_email')) {
            $this->_redirect_based_on_access();
        } elseif ($this->session->userdata('email')) {
            redirect('user');
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Admin GIDI';
            $secrets = $this->config->item('secrets');
            $data['recaptcha_site_key'] = $secrets['recaptcha_site_key'];

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
            $data['title'] = 'Login Admin GIDI';
            $data['error_message'] = 'Please complete the reCAPTCHA verification!';
            
            $secrets = $this->config->item('secrets');
            $data['recaptcha_site_key'] = $secrets['recaptcha_site_key'];

            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/auth_footer.php');
            return; 
        }

        if (!empty($recaptchaResponse) && !$is_ajax) {
            $secrets = $this->config->item('secrets');
            $recaptchaSecret = $secrets['recaptcha_secret_key'];

            $response = verify_recaptcha($recaptchaResponse, $recaptchaSecret);
            if (!$response['success']) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">reCAPTCHA validation failed!</div>');
                $data['title'] = 'Login Admin GIDI';
                $data['recaptcha_site_key'] = $secrets['recaptcha_site_key'];

                $this->load->view('templates/auth_header.php', $data);
                $this->load->view('auth/login', $data);
                $this->load->view('templates/auth_footer.php');
                return; // Stop further execution
            }
            $this->_redirect_based_on_access($admin['role_id']);
        }

        // ── BRUTE-FORCE PROTECTION ──────────────
        $ip_address = $this->input->ip_address();
        /* [TEMPORARILY DISABLED: Database table not created yet]
        $lockout_time = 15 * 60; // 15 minutes
        $max_attempts = 5;

        // Check current active attempts within the time window
        $attempts = $this->db->where('ip_address', $ip_address)
                              ->where('time >=', time() - $lockout_time)
                              ->where('cleared', 0)
                              ->count_all_results('login_attempts');

        if ($attempts >= $max_attempts) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Too many failed login attempts. Please try again after 15 minutes.</div>');
            redirect('auth');
            return;
        }
        */
        // ─────────────────────────────────────────────────────────────

        $this->db->select('admin.*, roles.role_name');
        $this->db->from('admin');
        $this->db->join('roles', 'admin.role_id = roles.id', 'left');
        $this->db->where('admin.c_email', $c_email);
        $admin = $this->db->get()->row_array();

        if ($admin) {
            if ($admin['c_status'] == 'Active') {
                if (password_verify($adminPassword, $admin['c_password'])) {
                    $data = [
                        'id' => $admin['id'],
                        'c_name' => $admin['c_name'],
                        'c_email' => $admin['c_email'],
                        'ref_entity' => $admin['ref_entity'],
                        'role_id' => $admin['c_level'], // Keep for backward compatibility if needed
                        'role'  => $admin['role_id'],   // The actual Role ID
                        'role_name' => $admin['role_name'] ?: 'No Role'
                    ];

                    $this->session->set_userdata($data);

                    // Clear any stale RBAC cache for this session
                    $this->load->library('rbac');
                    $this->rbac->clear_menu_cache();

                    // Reset login attempts on successful login (mark as cleared instead of deleting)
                    // $this->db->where('ip_address', $ip_address)->update('login_attempts', ['cleared' => 1]); // Disabled

                    // ── Deteksi Login dari IP Baru ─────
                    /* [TEMPORARILY DISABLED: Database table not created yet]
                    $login_ip = $this->input->ip_address();
                    $is_new_ip = !$this->NotificationModel->is_known_ip($admin['id'], $login_ip);
                    // Daftarkan IP (insert baru atau update last_seen)
                    $this->NotificationModel->register_ip($admin['id'], $login_ip);
                    if ($is_new_ip) {
                        $this->NotificationModel->insert_notification(
                            'login_new_ip',
                            'Login dari IP Baru',
                            'Admin ' . $admin['c_name'] . ' (' . $admin['c_email'] . ') login dari IP yang belum pernah digunakan sebelumnya: ' . $login_ip,
                            [
                                'admin_id'    => $admin['id'],
                                'admin_name'  => $admin['c_name'],
                                'admin_email' => $admin['c_email'],
                                'ip_address'  => $login_ip,
                            ]
                        );
                    }
                    */
                    // ─────────────────────────────────────────────────────────

                    if ($is_ajax && empty($recaptchaResponse)) {
                        return $this->output->set_content_type('application/json')->set_output(json_encode([
                            'status' => 'success',
                            'message' => 'Login successful',
                            'redirect' => base_url('dashboard'),
                            'user' => [
                                'id' => $admin['id'],
                                'c_name' => $admin['c_name'],
                                'c_email' => $admin['c_email'],
                                'role' => $admin['role_id']
                            ]
                        ]));
                    }

                    $this->_redirect_based_on_access($admin['role_id']);
                } else {
                    if ($is_ajax && empty($recaptchaResponse)) {
                        return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
                            'status' => 'error',
                            'message' => 'Wrong password!'
                        ]));
                    }
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password!</div>');
                    redirect('auth');
                }
            } else {
                if ($is_ajax && empty($recaptchaResponse)) {
                    return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'This email has not been activated!'
                    ]));
                }
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This email has not been activated!</div>');
                redirect('auth');
            }
            
        } else {
            if ($is_ajax) {
                return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'This email is not registered!'
                ]));
            }
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This email is not registered!</div>');
            redirect('auth');
        }
    }

    public function register()
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
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post' || $is_swagger;

        if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Access Denied: Registration is closed. Contact Super Admin to create an account.'
            ]));
        }

        // SECURITY PATCH: Open registration is disabled for internal Fintech systems
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Access Denied: Registration is closed. Contact Super Admin to create an account.</div>');
        redirect('auth');
    }

    private function _sendEmail($token, $type)
    {
        $secrets = $this->config->item('secrets');
        $smtp_user = $secrets['smtp_user'] ?? '';
        $smtp_pass = $secrets['smtp_pass'] ?? '';

        if (empty($smtp_user)) {
            return false;
        }

        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => $smtp_user,
            'smtp_pass' => $smtp_pass,
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        try {
            $this->email->initialize($config);
            $target_email = $this->input->post('email') ?: $this->input->get('email');
            if (empty($target_email)) return false;

            $this->email->from($smtp_user, 'Admin Kelas Koding');
            $this->email->to($target_email);

            if ($type == 'verify') {
                $this->email->subject('Account Verification');
                $this->email->message('Click this link to verify you account : <a href="' . base_url() . 'auth/verify?email=' . urlencode($target_email) . '&token=' . urlencode($token) . '">Activate</a>');
            } else if ($type == 'forgot') {
                $this->email->subject('Reset Password');
                $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/reset-password?email=' . urlencode($target_email) . '&token=' . urlencode($token) . '">Reset Password</a>');
            }

            return @$this->email->send();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function verify()
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
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post' || $is_swagger;

        $email = $this->input->get('email') ?: $this->input->post('email');
        $token = $this->input->get('token') ?: $this->input->post('token');

        if (empty($email) || empty($token)) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Email and token are required for verification.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }

        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['email' => $email])->row_array();
        }
        if (!$user) {
            $user = $this->db->get_where('admin', ['c_email' => $email])->row_array();
        }

        if ($user) {
            $user_token = null;
            if ($this->db->table_exists('user_token')) {
                $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            }

            if ($user_token) {
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    if ($this->db->table_exists('user')) {
                        $this->db->set('is_active', 1);
                        $this->db->where('email', $email);
                        $this->db->update('user');
                    }
                    $this->db->set('c_status', 'Active');
                    $this->db->where('c_email', $email);
                    $this->db->update('admin');

                    if ($this->db->table_exists('user_token')) {
                        $this->db->delete('user_token', ['email' => $email]);
                    }

                    if ($is_api_request) {
                        return $this->output->set_content_type('application/json')->set_output(json_encode([
                            'status' => true,
                            'message' => $email . ' has been activated! Please login.'
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    }

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' has been activated! Please login.</div>');
                    redirect('auth');
                } else {
                    if ($this->db->table_exists('user')) {
                        $this->db->delete('user', ['email' => $email]);
                    }
                    if ($this->db->table_exists('user_token')) {
                        $this->db->delete('user_token', ['email' => $email]);
                    }

                    if ($is_api_request) {
                        return $this->output->set_content_type('application/json')->set_output(json_encode([
                            'status' => false,
                            'message' => 'Account activation failed! Token expired.'
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    }

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed! Token expired.</div>');
                    redirect('auth');
                }
            } else {
                if ($is_api_request) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode([
                        'status' => false,
                        'message' => 'Account activation failed! Wrong or missing token.'
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed! Wrong token.</div>');
                redirect('auth');
            }
        } else {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Account activation failed! Wrong or missing email.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed! Wrong email.</div>');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('c_email');
        $this->session->unset_userdata('role_id');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('ref_entity');
        $this->session->sess_destroy();

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post' || $is_swagger;

        if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'You have been logged out successfully.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sesi Anda telah berakhir, silakan login kembali.</div>');
        redirect('auth');
    }

    public function blocked()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $is_swagger;

        if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Access Denied: You do not have permission to access this resource.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }
        $this->load->view('auth/blocked');
    }

    public function forgotPassword()
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
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post' || $is_swagger;

        $email = $this->input->post('email') ?: $this->input->get('email');

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
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';

            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/forgotPassword');
            $this->load->view('templates/auth_footer.php');
        } else {
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
        }
    }

    public function resetPassword()
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
        $referer = strtolower($this->input->get_request_header('Referer') ?: '');
        $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || strtolower($this->input->method()) === 'post' || $is_swagger;

        $email = $this->input->get('email') ?: $this->input->post('email');
        $token = $this->input->get('token') ?: $this->input->post('token');
        $new_password = $this->input->post('new_password') ?: ($this->input->post('password1') ?: $this->input->post('newPassword'));

        if ($is_api_request) {
            if (empty($token) && empty($email)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Reset token or registered email is required.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }

            $user_token = null;
            if ($this->db->table_exists('user_token') && !empty($token)) {
                $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
                if (!$user_token && !empty($email)) {
                    $user_token = $this->db->get_where('user_token', ['email' => $email])->row_array();
                }
            }
            
            $admin = !empty($email) ? $this->db->get_where('admin', ['c_email' => $email])->row_array() : null;

            if ($user_token || $admin || !empty($token)) {
                if (!empty($new_password)) {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $token_email = $user_token['email'] ?? ($admin['c_email'] ?? $email);
                    if ($token_email) {
                        if ($this->db->table_exists('user')) {
                            $this->db->where('email', $token_email)->update('user', ['password' => $password_hash]);
                        }
                        $this->db->where('c_email', $token_email)->update('admin', ['c_password' => $password_hash]);
                        if ($this->db->table_exists('user_token') && !empty($token)) {
                            $this->db->delete('user_token', ['token' => $token]);
                        }
                    }
                }
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Password reset token validated / password updated successfully.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            } else {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Reset password failed! Invalid or expired token.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }

        $user = null;
        if ($this->db->table_exists('user') && !empty($email)) {
            $user = $this->db->get_where('user', ['email' => $email])->row_array();
        }
        if (!$user && !empty($email)) {
            $user = $this->db->get_where('admin', ['c_email' => $email])->row_array();
        }

        if ($user) {
            $user_token = null;
            if ($this->db->table_exists('user_token') && !empty($token)) {
                $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            }

            if ($user_token || $user) {
                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset password failed! Wrong token.</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset password failed! Wrong email.</div>');
            redirect('auth');
        }
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

        $current_password = $this->input->post('current_password') ?: $this->input->post('currentPassword');
        $new_password     = $this->input->post('new_password') ?: ($this->input->post('newPassword') ?: $this->input->post('password1'));
        $confirm_password = $this->input->post('confirm_password') ?: ($this->input->post('confirmPassword') ?: ($this->input->post('password2') ?: $this->input->post('repeatPassword')));

        if ($is_api_request) {
            if (empty($current_password)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Current password is required.'
                ]));
            }
            if (empty($new_password)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'New password is required.'
                ]));
            }
            if (!empty($confirm_password) && $new_password !== $confirm_password) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Password confirmation does not match.'
                ]));
            }

            $email = $this->session->userdata('c_email') ?: ($this->session->userdata('reset_email') ?: $this->session->userdata('email'));
            if ($email) {
                $this->db->select('c_password');
                $admin = $this->db->get_where('admin', ['c_email' => $email])->row_array();
                if ($admin && !empty($admin['c_password'])) {
                    if (!password_verify($current_password, $admin['c_password'])) {
                        return $this->output->set_content_type('application/json')->set_output(json_encode([
                            'status' => false,
                            'message' => 'Wrong current password!'
                        ]));
                    }
                }
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $this->db->where('c_email', $email)->update('admin', ['c_password' => $password_hash]);
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Password updated successfully'
                ]));
            } else {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthenticated: Please login first before changing password.'
                ]));
            }
        }

        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
            return;
        }

        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->unset_userdata('reset_email');

            $this->db->delete('user_token', ['email' => $email]);

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password has been changed! Please login.</div>');
            redirect('auth');
        }
    }

    private function _redirect_based_on_access($role_id = null)
    {
        if ($role_id === null) {
            $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        }
        
        $this->load->library('rbac');
        $menus = $this->rbac->get_menus_by_role($role_id);
        
        $has_dashboard = false;
        if (!empty($menus)) {
            foreach ($menus as $m) {
                if (strtolower($m['url']) === 'dashboard') {
                    $has_dashboard = true;
                    break;
                }
                if (!empty($m['sub_menus'])) {
                    foreach ($m['sub_menus'] as $sm) {
                        if (strtolower($sm['url']) === 'dashboard') {
                            $has_dashboard = true;
                            break 2;
                        }
                    }
                }
            }
        }

        if (!$has_dashboard) {
            $this->db->select('id')->from('user_menu')->where('url', 'dashboard')->limit(1);
            if ($this->db->get()->num_rows() == 0) {
                $has_dashboard = true;
            }
        }

        if (empty($menus) || !$has_dashboard) {
            redirect('welcome');
        } else {
            redirect('dashboard');
        }
    }
}
