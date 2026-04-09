<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac {
    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Check if the current user has a specific permission.
     *
     * @param int $role_id
     * @param string $permission
     * @return bool
     */
    public function has_permission($role_id, $permission) {
        $query = $this->CI->db
            ->select('rp.*')
            ->from('role_permissions rp')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('rp.role_id', $role_id)
            ->where('p.permission_name', $permission)
            ->get();
        return $query->num_rows() > 0;
    }

    public function get_permissions_by_role($role_id) {
        $query = $this->CI->db
            ->select('p.permission_name')
            ->from('role_permissions rp')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('rp.role_id', $role_id)
            ->get();
        return $query->result_array();
    }

    /**
     * Get accessible hierarchical menus for a role.
     *
     * @param int $role_id
     * @return array
     */
    public function get_menus_by_role($role_id) {
        // Fetch Main Menus
        $this->CI->db->select('m.*');
        $this->CI->db->from('user_menu m');
        $this->CI->db->join('user_access_menu am', 'm.id = am.menu_id');
        $this->CI->db->where('am.role_id', $role_id);
        $this->CI->db->where('m.parent_id', 0);
        $this->CI->db->where('m.is_active', 1);
        $this->CI->db->order_by('m.menu_order', 'ASC');
        $main_menus = $this->CI->db->get()->result_array();

        // Fetch Sub Menus for each Main Menu
        foreach($main_menus as &$menu) {
            $this->CI->db->select('m.*');
            $this->CI->db->from('user_menu m');
            $this->CI->db->join('user_access_menu am', 'm.id = am.menu_id');
            $this->CI->db->where('am.role_id', $role_id);
            $this->CI->db->where('m.parent_id', $menu['id']);
            $this->CI->db->where('m.is_active', 1);
            $this->CI->db->order_by('m.menu_order', 'ASC');
            $menu['sub_menus'] = $this->CI->db->get()->result_array();
        }
        return $main_menus;
    }
}
