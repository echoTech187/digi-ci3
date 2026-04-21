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

        $current_url = strtolower($segment1);
        if ($segment2) {
            $current_url .= '/' . strtolower($segment2);
        }

        // REMOVED: Broad segment bypass for 'admin' and 'user' to ensure granular RBAC
        // Combined Optimized Query: Check menu existence and access in one round-trip
        $ci->db->select('m.id as menu_id, am.id as access_id');
        $ci->db->from('user_menu m');
        $ci->db->join('user_access_menu am', 'm.id = am.menu_id AND am.role_id = ' . (int)$actual_role_id, 'left');
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
