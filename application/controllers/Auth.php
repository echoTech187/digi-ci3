<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('secrets', TRUE, TRUE);
    }

    public function index()
    {
        if ($this->session->userdata('c_email')) {
            redirect('admin');
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
        $c_email = $this->input->post('email');
        $adminPassword = $this->input->post('password');

        $recaptchaResponse = $this->input->post('g-recaptcha-response');


        
        if (empty($recaptchaResponse)) {
            $data['title'] = 'Login Admin GIDI';
            $data['error_message'] = 'Please complete the reCAPTCHA verification!';
            
            $secrets = $this->config->item('secrets');
            $data['recaptcha_site_key'] = $secrets['recaptcha_site_key'];

            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/auth_footer.php');
            return; 
        }

        // Configuration loaded in constructor
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

                    redirect('admin');
                } else {
                   
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password!</div>');
                    redirect('auth');
                }
            } else {
                
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This email has not been activated!</div>');
                redirect('auth');
            }
            
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This email is not registered!</div>');
            redirect('auth');
        }
    }

    public function register()
    {
        if ($this->session->userdata('c_email')) {
            redirect('admin');
        } elseif ($this->session->userdata('email')) {
            redirect('user');
        }

        // validasi untuk register
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.email]', [
            'is_unique' => 'This email has already registered!',
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[4]|matches[password2]', [
            'matches' => 'Password dont match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'trim|required|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Registration';
            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/register');
            $this->load->view('templates/auth_footer.php');
        } else {
            // ketika berhasil akan mengirimkan data ke database
            $email = $this->input->post('email', true);
            $data = [
                'name' => htmlspecialchars($this->input->post('name')),
                'email' => htmlspecialchars($this->input->post('email')),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];

            // insert data ke database
            $this->db->insert('user', $data);
            $this->db->insert('user_token', $user_token);

            $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation, Your account has been created. please Activated</div>');
            redirect('auth');
        }
    }

    private function _sendEmail($token, $type)
    {
        // Configuration loaded in constructor
        $secrets = $this->config->item('secrets');

        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => $secrets['smtp_user'],
            'smtp_pass' => $secrets['smtp_pass'],
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->email->initialize($config);

        $this->email->from($secrets['smtp_user'], 'Admin Kelas Koding');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message('Click this link to verify you account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    $this->db->set('is_active', 1);
                    $this->db->where('email', $email);
                    $this->db->update('user');

                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' has been activated! Please login.</div>');
                    redirect('auth');
                } else {
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed! Token expired.</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed! Wrong token.</div>');
                redirect('auth');
            }
        } else {
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

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sesi Anda telah berakhir, silakan login kembali.</div>');
        redirect('auth');
    }

    public function blocked()
    {
        $this->load->view('auth/blocked');
    }

    public function forgotPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';

            $this->load->view('templates/auth_header.php', $data);
            $this->load->view('auth/forgotPassword');
            $this->load->view('templates/auth_footer.php');
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please check your email to reset your password!</div>');
                redirect('auth/forgotpassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email is not registered or activated!</div>');
                redirect('auth/forgotpassword');
            }
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
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
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
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
}
