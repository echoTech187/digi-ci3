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

        // Verify user existence in DB (Optimized with Session caching for 5 minutes)
        $user_verify_key = 'auth_user_verify_'.md5($email);
        $last_verify = $ci->session->userdata($user_verify_key);
        if (!$last_verify || (time() - $last_verify) > 300) {
            $user = $ci->db->get_where('admin', ['c_email' => $email])->row_array();
            if (!$user) {
                redirect('auth/logout');
            }
            $ci->session->set_userdata($user_verify_key, time());
        }

        // Detect effective Role ID
        $role_id = $ci->session->userdata('role') ?: $ci->session->userdata('role_id');

        // WHITELIST
        $public_segments = ['admin', 'welcome', 'auth', 'user'];
        if (in_array($segment1, $public_segments) && empty($segment2)) {
            return;
        }

        $current_url = $segment1 . ($segment2 ? '/' . $segment2 : '');

        // RBAC Check: Optimized using Rbac library cache (NO DB Hit if cached)
        $ci->load->library('rbac');
        $menus = $ci->rbac->get_menus_by_role($role_id);
        
        $has_access = false;
        $url_found_in_menu = false;

        // Traverse menu tree to find current URL
        foreach ($menus as $m) {
            if (strtolower($m['url']) === $current_url) {
                $url_found_in_menu = true;
                $has_access = true; // If found in get_menus_by_role, they HAVE access
                break;
            }
            if (!empty($m['sub_menus'])) {
                foreach ($m['sub_menus'] as $sm) {
                    if (strtolower($sm['url']) === $current_url) {
                        $url_found_in_menu = true;
                        $has_access = true;
                        break 2;
                    }
                }
            }
        }

        // If the URL is NOT in the user_menu table at all, we allow it (e.g. utility pages)
        // But if it IS in the table and they don't have access, we block it.
        // To be safe, we only block if we explicitly know they don't have access.
        
        if (!$has_access) {
            // Check if it exists in DB at all for ANYONE
            $ci->db->select('id')->from('user_menu')->where('url', $current_url)->limit(1);
            $exists_in_db = $ci->db->get()->num_rows() > 0;
            
            if ($exists_in_db) {
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
