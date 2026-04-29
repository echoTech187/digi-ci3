<?php defined('BASEPATH') or exit('No direct script access allowed');

function is_logged_in()
{
    $ci = get_instance();
    $segment1 = strtolower($ci->uri->segment(1));
    $segment2 = strtolower($ci->uri->segment(2));
    $email = $ci->session->userdata('c_email') ?: $ci->session->userdata('email');

    if (!$email) {
        // If no session and not on auth page, redirect to login
        if ($segment1 !== 'auth') {
            redirect('auth');
        }
    } else {
        // Exempt auth controller from DB check to prevent redirect loops (especially during logout)
        if ($segment1 === 'auth') {
            return;
        }

        // Verify user existence in DB to prevent "null offset" errors if session is stale or user was deleted
        $user = $ci->db->get_where('admin', ['c_email' => $email])->row_array();
        if (!$user) {
            redirect('auth/logout');
        }

        // Detect effective Role ID (Prioritize 'role_id' as the primary access level identifier)
        $role_id = $ci->session->userdata('role_id') ?: $ci->session->userdata('role');

        // WHITELIST: Allow base segments that serve as entry points/dispatchers
        $public_segments = ['admin', 'welcome', 'auth', 'user'];
        if (in_array($segment1, $public_segments) && empty($segment2)) {
            return;
        }

        $current_url = $segment1 . ($segment2 ? '/' . $segment2 : '');

        // RBAC Check: Verify menu exists and user has access
        $ci->db->select('m.id as menu_id, am.id as access_id');
        $ci->db->from('user_menu m');
        // Use flexible comparison (without forced cast) to support string/int role IDs
        $ci->db->join('user_access_menu am', 'm.id = am.menu_id AND am.role_id = ' . $ci->db->escape($role_id), 'left');
        $ci->db->where('m.url', $current_url);
        $check = $ci->db->get();

        if ($check->num_rows() > 0) {
            $result = $check->row_array();
            if (!$result['access_id']) {
                redirect('auth/blocked');
            }
        }
    }
}

function check_access($role_id, $menu_id)
{
    $ci = get_instance();

    $result = $ci->db->get_where('user_access_menu', [
        'role_id' => $role_id,
        'menu_id' => $menu_id
    ]);

    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }
}
