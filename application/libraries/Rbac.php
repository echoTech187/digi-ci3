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
        $db_name = $this->CI->db->database;
        $cache_key = 'rbac_menu_cache_' . $db_name . '_' . $role_id;
        
        // 1. Check Session Cache first to avoid Remote DB latency
        if ($this->CI->session->has_userdata($cache_key)) {
            return $this->CI->session->userdata($cache_key);
        }

        // 2. Fetch from DB if not cached
        $this->CI->db->select('m.*');
        $this->CI->db->from('user_menu m');
        $this->CI->db->join('user_access_menu am', 'm.id = am.menu_id');
        $this->CI->db->where('am.role_id', $role_id);
        $this->CI->db->where('m.is_active', 1);
        $this->CI->db->order_by('m.parent_id', 'ASC');
        $this->CI->db->order_by('m.menu_order', 'ASC');
        $all_menus = $this->CI->db->get()->result_array();

        $main_menus = [];
        $sub_menus_map = [];

        // Organize menus in PHP memory
        foreach ($all_menus as $menu) {
            if ($menu['parent_id'] == 0) {
                $menu['sub_menus'] = []; 
                $main_menus[$menu['id']] = $menu;
            } else {
                $sub_menus_map[$menu['parent_id']][] = $menu;
            }
        }

        foreach ($main_menus as $id => &$menu) {
            if (isset($sub_menus_map[$id])) {
                $menu['sub_menus'] = $sub_menus_map[$id];
            }
        }

        $result = array_values($main_menus);

        // 3. Store in Session Cache
        $this->CI->session->set_userdata($cache_key, $result);

        return $result;
    }
}
