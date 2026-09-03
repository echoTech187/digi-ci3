<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AuthService
 * Business logic layer for Authentication, Account Verification, Password Reset, and Access Routing.
 */
class AuthService
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->config->load('secrets', TRUE, TRUE);
    }

    /**
     * Authenticate admin user by email and password
     */
    public function authenticate($email, $password)
    {
        $this->CI->db->select('admin.*, roles.role_name');
        $this->CI->db->from('admin');
        $this->CI->db->join('roles', 'admin.role_id = roles.id', 'left');
        $this->CI->db->where('admin.c_email', $email);
        $admin = $this->CI->db->get()->row_array();

        if (!$admin) {
            return ['status' => false, 'message' => 'This email is not registered!'];
        }

        if ($admin['c_status'] !== 'Active') {
            return ['status' => false, 'message' => 'This email has not been activated!'];
        }

        if (!password_verify($password, $admin['c_password'])) {
            return ['status' => false, 'message' => 'Wrong password!'];
        }

        // Establish session state
        $sessionData = [
            'id'         => $admin['id'],
            'c_name'     => $admin['c_name'],
            'c_email'    => $admin['c_email'],
            'ref_entity' => $admin['ref_entity'],
            'role_id'    => $admin['c_level'],
            'role'       => $admin['role_id'],
            'role_name'  => $admin['role_name'] ?: 'No Role'
        ];
        $this->CI->session->set_userdata($sessionData);

        // Clear RBAC cache
        $this->CI->load->library('rbac');
        $this->CI->rbac->clear_menu_cache();

        return [
            'status'   => true,
            'message'  => 'Login successful',
            'admin'    => $admin,
            'redirect' => $this->getRedirectRoute($admin['role_id'])
        ];
    }

    /**
     * Determine redirect route according to user role and menu access
     */
    public function getRedirectRoute($role_id = null)
    {
        if ($role_id === null) {
            $role_id = $this->CI->session->userdata('role') ?: $this->CI->session->userdata('role_id');
        }

        $this->CI->load->library('rbac');
        $menus = $this->CI->rbac->get_menus_by_role($role_id);

        $has_dashboard = false;
        if (!empty($menus)) {
            foreach ($menus as $m) {
                if (isset($m['url']) && strtolower($m['url']) === 'dashboard') {
                    $has_dashboard = true;
                    break;
                }
                if (!empty($m['sub_menus'])) {
                    foreach ($m['sub_menus'] as $sm) {
                        if (isset($sm['url']) && strtolower($sm['url']) === 'dashboard') {
                            $has_dashboard = true;
                            break 2;
                        }
                    }
                }
            }
        }

        if (!$has_dashboard) {
            $this->CI->db->select('id')->from('user_menu')->where('url', 'dashboard')->limit(1);
            if ($this->CI->db->get()->num_rows() == 0) {
                $has_dashboard = true;
            }
        }

        return $has_dashboard ? 'dashboard' : 'welcome';
    }

    /**
     * Verify account via email token
     */
    public function verifyAccount($email, $token)
    {
        if (empty($email) || empty($token)) {
            return ['status' => false, 'message' => 'Email and token are required for verification.'];
        }

        $user = null;
        if ($this->CI->db->table_exists('user')) {
            $user = $this->CI->db->get_where('user', ['email' => $email])->row_array();
        }
        if (!$user) {
            $user = $this->CI->db->get_where('admin', ['c_email' => $email])->row_array();
        }

        if (!$user) {
            return ['status' => false, 'message' => 'Account activation failed! Wrong or missing email.'];
        }

        $user_token = null;
        if ($this->CI->db->table_exists('user_token')) {
            $user_token = $this->CI->db->get_where('user_token', ['token' => $token])->row_array();
        }

        if (!$user_token) {
            return ['status' => false, 'message' => 'Account activation failed! Wrong or missing token.'];
        }

        if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
            if ($this->CI->db->table_exists('user')) {
                $this->CI->db->where('email', $email)->update('user', ['is_active' => 1]);
            }
            $this->CI->db->where('c_email', $email)->update('admin', ['c_status' => 'Active']);

            if ($this->CI->db->table_exists('user_token')) {
                $this->CI->db->delete('user_token', ['email' => $email]);
            }

            return ['status' => true, 'message' => $email . ' has been activated! Please login.'];
        }

        if ($this->CI->db->table_exists('user')) {
            $this->CI->db->delete('user', ['email' => $email]);
        }
        if ($this->CI->db->table_exists('user_token')) {
            $this->CI->db->delete('user_token', ['email' => $email]);
        }
        return ['status' => false, 'message' => 'Account activation failed! Token expired.'];
    }

    /**
     * Request password reset token and send email
     */
    public function requestPasswordReset($email)
    {
        if (empty($email)) {
            return ['status' => false, 'message' => 'Please provide a valid email address.'];
        }

        $user = null;
        if ($this->CI->db->table_exists('user')) {
            $user = $this->CI->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
        }
        if (!$user) {
            $user = $this->CI->db->get_where('admin', ['c_email' => $email, 'c_status' => 'Active'])->row_array();
        }

        if (!$user) {
            return ['status' => false, 'message' => 'Email is not registered or activated!'];
        }

        $token = base64_encode(random_bytes(32));
        if ($this->CI->db->table_exists('user_token')) {
            $this->CI->db->insert('user_token', [
                'email'        => $email,
                'token'        => $token,
                'date_created' => time()
            ]);
        }

        try {
            $this->sendEmail($email, $token, 'forgot');
        } catch (\Throwable $e) {
            log_message('error', 'Auth email error: ' . $e->getMessage());
        }

        return ['status' => true, 'message' => 'Please check your email to reset your password!'];
    }

    /**
     * Process password reset token and update password
     */
    public function processPasswordReset($token, $email, $new_password)
    {
        $user_token = null;
        if ($this->CI->db->table_exists('user_token') && !empty($token)) {
            $user_token = $this->CI->db->get_where('user_token', ['token' => $token])->row_array();
            if (!$user_token && !empty($email)) {
                $user_token = $this->CI->db->get_where('user_token', ['email' => $email])->row_array();
            }
        }

        $admin = !empty($email) ? $this->CI->db->get_where('admin', ['c_email' => $email])->row_array() : null;

        if ($user_token || $admin || !empty($token)) {
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $token_email = $user_token['email'] ?? ($admin['c_email'] ?? $email);
                if ($token_email) {
                    if ($this->CI->db->table_exists('user')) {
                        $this->CI->db->where('email', $token_email)->update('user', ['password' => $password_hash]);
                    }
                    $this->CI->db->where('c_email', $token_email)->update('admin', ['c_password' => $password_hash]);
                    if ($this->CI->db->table_exists('user_token') && !empty($token)) {
                        $this->CI->db->delete('user_token', ['token' => $token]);
                    }
                }
            }
            return ['status' => true, 'message' => 'Password reset token validated / password updated successfully.'];
        }

        return ['status' => false, 'message' => 'Reset password failed! Invalid or expired token.'];
    }

    /**
     * Change password for authenticated session
     */
    public function changePassword($email, $current_password, $new_password)
    {
        if (empty($email)) {
            return ['status' => false, 'message' => 'Unauthenticated: Please login first before changing password.'];
        }

        $this->CI->db->select('c_password');
        $admin = $this->CI->db->get_where('admin', ['c_email' => $email])->row_array();
        if ($admin && !empty($admin['c_password'])) {
            if (!password_verify($current_password, $admin['c_password'])) {
                return ['status' => false, 'message' => 'Wrong current password!'];
            }
        }

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->CI->db->where('c_email', $email)->update('admin', ['c_password' => $password_hash]);
        return ['status' => true, 'message' => 'Password updated successfully'];
    }

    /**
     * Send Auth Emails (Verification or Password Reset)
     */
    public function sendEmail($target_email, $token, $type)
    {
        $secrets = $this->CI->config->item('secrets');
        $smtp_user = $secrets['smtp_user'] ?? '';
        $smtp_pass = $secrets['smtp_pass'] ?? '';

        if (empty($smtp_user) || empty($target_email)) {
            return false;
        }

        $this->CI->load->library('email');
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
            $this->CI->email->initialize($config);
            $this->CI->email->from($smtp_user, 'Admin GIDI');
            $this->CI->email->to($target_email);

            if ($type === 'verify') {
                $this->CI->email->subject('Account Verification');
                $this->CI->email->message('Click this link to verify your account: <a href="' . base_url() . 'auth/verify?email=' . urlencode($target_email) . '&token=' . urlencode($token) . '">Activate</a>');
            } else if ($type === 'forgot') {
                $this->CI->email->subject('Reset Password');
                $this->CI->email->message('Click this link to reset your password: <a href="' . base_url() . 'auth/reset-password?email=' . urlencode($target_email) . '&token=' . urlencode($token) . '">Reset Password</a>');
            }

            return @$this->CI->email->send();
        } catch (\Throwable $e) {
            log_message('error', 'SMTP Send error: ' . $e->getMessage());
            return false;
        }
    }
}
