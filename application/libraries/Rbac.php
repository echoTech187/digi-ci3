<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac {
    protected $CI;
    private $_permissions = [];
    private $_menu_tree   = [];

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
        $perms = $this->get_permissions_by_role($role_id);
        foreach ($perms as $p) {
            if ($p['permission_name'] === $permission) return true;
        }
        return false;
    }

    public function get_permissions_by_role($role_id) {
        if (isset($this->_permissions[$role_id])) {
            return $this->_permissions[$role_id];
        }

        $db_name = $this->CI->db->database;
        $cache_key = 'rbac_perms_cache_' . $db_name . '_' . $role_id;

        // 1. Try Session Cache
        $cached_perms = $this->CI->session->userdata($cache_key);
        if ($cached_perms && is_array($cached_perms)) {
            $this->_permissions[$role_id] = $cached_perms;
            return $this->_permissions[$role_id];
        }

        // 2. Fetch from DB
        $query = $this->CI->db
            ->select('p.permission_name')
            ->from('role_permissions rp')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('rp.role_id', $role_id)
            ->get();
        $this->_permissions[$role_id] = $query->result_array();

        // 3. Store in Session
        $this->CI->session->set_userdata($cache_key, $this->_permissions[$role_id]);

        return $this->_permissions[$role_id];
    }

    /**
     * Get accessible hierarchical menus for a role.
     *
     * @param int $role_id
     * @return array
     */
    public function get_menus_by_role($role_id) {
        if (isset($this->_menu_tree[$role_id])) {
            return $this->_menu_tree[$role_id];
        }

        $db_name = $this->CI->db->database;
        $cache_key = 'rbac_menu_cache_' . $db_name . '_' . $role_id;
        
        // 1. Check Session Cache first to avoid Remote DB latency
        $cached_data = $this->CI->session->userdata($cache_key);
        if ($cached_data && is_array($cached_data)) {
            $this->_menu_tree[$role_id] = $cached_data;
            return $this->_menu_tree[$role_id];
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
        $this->_menu_tree[$role_id] = $result;

        return $result;
    }
    /**
     * Clear all RBAC menu caches in the current session.
     * Use this when menu structure or permissions change.
     */
    public function clear_menu_cache() {
        $this->_permissions = [];
        $this->_menu_tree = [];
        
        $db_name = $this->CI->db->database;
        $all_session = $this->CI->session->all_userdata();
        $prefix_menu = 'rbac_menu_cache_' . $db_name;
        $prefix_perms = 'rbac_perms_cache_' . $db_name;
        
        foreach ($all_session as $key => $value) {
            if (strpos($key, $prefix_menu) === 0 || strpos($key, $prefix_perms) === 0) {
                $this->CI->session->unset_userdata($key);
            }
        }
    }
}
