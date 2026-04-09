<?php defined('BASEPATH') or exit('No direct script access allowed');

function is_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('email') && !$ci->session->userdata('c_email')) {
        redirect('auth');
    } else {
        $actual_role_id = $ci->session->userdata('role');
        if (!$actual_role_id) {
            $actual_role_id = $ci->session->userdata('role_id'); 
        }

        $segment1 = $ci->uri->segment(1);
        $segment2 = $ci->uri->segment(2);

        $current_url = $segment1;
        if ($segment2) {
            $current_url .= '/' . $segment2;
        }

        // Allow default dashboard routes
        if ($current_url == 'admin' || $current_url == 'user') {
            return;
        }

        // Look up URL in user_menu table
        $menuItem = $ci->db->get_where('user_menu', ['url' => $current_url])->row_array();

        if ($menuItem) {
            $menu_id = $menuItem['id'];
            
            // Check Access mapping
            $userAccess = $ci->db->get_where('user_access_menu', [
                'role_id' => $actual_role_id,
                'menu_id' => $menu_id
            ]);

            if ($userAccess->num_rows() < 1) {
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
