<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rbac_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ── Permissions ──────────────────────────────────────────

    public function getAllPermissions() {
        $this->db->select('p.*');
        $this->db->select('(SELECT c_label FROM rbac_sidebar_menus m WHERE m.c_isActive=1 AND m.ref_permissionId = p.id AND m.c_url IS NOT NULL AND m.c_url != "" ORDER BY m.c_sortOrder ASC LIMIT 1) AS menu_label');
        $this->db->from('rbac_permissions p');
        
        $subquery = "(SELECT p2.c_group, MIN(p2.c_sortOrder) as group_sort FROM rbac_permissions p2 WHERE p2.c_group IS NOT NULL AND p2.c_group != '' GROUP BY p2.c_group)";
        $this->db->join($subquery . ' gs', 'gs.c_group = p.c_group', 'left');
        
        $this->db->order_by("CASE WHEN p.c_group IS NULL OR p.c_group = '' OR p.c_group = 'General' THEN 0 ELSE 1 END", 'ASC', FALSE);
        $this->db->order_by('IFNULL(gs.group_sort, 9999)', 'ASC', FALSE);
        $this->db->order_by('p.c_group', 'ASC');
        $this->db->order_by('p.c_sortOrder', 'ASC');
        
        $results = $this->db->get()->result_array();
        
        foreach ($results as &$r) {
            if (!empty($r['menu_label'])) {
                $r['c_name'] = $r['menu_label'];
            }
        }
        
        return $results;
    }

    public function getPermissionsByGroup() {
        $perms = $this->getAllPermissions();
        $grouped = [];
        foreach ($perms as $p) {
            $grouped[$p['c_group']][] = $p;
        }
        return $grouped;
    }

    // ── Roles ─────────────────────────────────────────────────────

    public function getAllRoles() {
        return $this->db->get('rbac_roles')->result_array();
    }

    public function getRoleById($roleId) {
        return $this->db->get_where('rbac_roles', ['id' => $roleId])->row_array();
    }

    public function getRolePermissions($roleId) {
        $this->db->select('p.*');
        $this->db->from('rbac_role_permissions rp');
        $this->db->join('rbac_permissions p', 'p.id = rp.ref_permissionId');
        $this->db->where('rp.ref_roleId', $roleId);
        return $this->db->get()->result_array();
    }

    public function createRole($data) {
        $this->db->insert('rbac_roles', $data);
        return $this->db->insert_id();
    }

    public function updateRole($roleId, $data) {
        return $this->db->where('id', $roleId)->update('rbac_roles', $data);
    }

    public function deleteRole($roleId) {
        $role = $this->getRoleById($roleId);
        if ($role && !$role['c_isSystem']) {
            return $this->db->where('id', $roleId)->delete('rbac_roles');
        }
        return false;
    }

    public function setRolePermissions($roleId, $permissionIds) {
        $this->db->where('ref_roleId', $roleId)->delete('rbac_role_permissions');
        if (!empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $pId) {
                $data[] = [
                    'ref_roleId' => $roleId,
                    'ref_permissionId' => $pId
                ];
            }
            $this->db->insert_batch('rbac_role_permissions', $data);
        }
    }

    // ── User Roles ─────────────────────────────────────────────────

    public function assignRole($userType, $userId, $roleId, $grantedBy = NULL) {
        $data = [
            'c_user_type' => $userType,
            'ref_userId' => $userId,
            'ref_roleId' => $roleId,
            'ref_grantedByMerchantId' => $grantedBy
        ];
        return $this->db->insert('rbac_user_roles', $data);
    }

    public function getUserRoles($userType, $userId) {
        $this->db->select('r.*');
        $this->db->from('rbac_user_roles ur');
        $this->db->join('rbac_roles r', 'r.id = ur.ref_roleId');
        $this->db->where('ur.c_user_type', $userType);
        $this->db->where('ur.ref_userId', $userId);
        $this->db->where('ur.c_isActive', 1);
        return $this->db->get()->result_array();
    }

    public function syncUserRole($userType, $userId, $roleId, $grantedBy = NULL) {
        // Delete existing roles for this user
        $this->db->where('c_user_type', $userType);
        $this->db->where('ref_userId', $userId);
        $this->db->delete('rbac_user_roles');

        if ($roleId) {
            $data = [
                'c_user_type' => $userType,
                'ref_userId' => $userId,
                'ref_roleId' => $roleId,
                'ref_grantedByMerchantId' => $grantedBy,
                'c_isActive' => 1,
                'c_grantedAt' => date('Y-m-d H:i:s')
            ];
            return $this->db->insert('rbac_user_roles', $data);
        }
        return true;
    }

    public function getPrimaryRoleId($userType, $userId) {
        $this->db->select('ref_roleId');
        $this->db->from('rbac_user_roles');
        $this->db->where('c_user_type', $userType);
        $this->db->where('ref_userId', $userId);
        $this->db->where('c_isActive', 1);
        $this->db->limit(1);
        $row = $this->db->get()->row();
        return $row ? $row->ref_roleId : null;
    }

    // ── Effective Permissions ──────────────────────────────────────

    public function getEffectivePermissions($userType, $userId) {
        // 1. Get permissions from roles
        $this->db->select('p.c_code');
        $this->db->from('rbac_user_roles ur');
        $this->db->join('rbac_role_permissions rp', 'rp.ref_roleId = ur.ref_roleId');
        $this->db->join('rbac_permissions p', 'p.id = rp.ref_permissionId');
        $this->db->where('ur.c_user_type', $userType);
        $this->db->where('ur.ref_userId', $userId);
        $this->db->where('ur.c_isActive', 1);
        $rolePerms = $this->db->get()->result_array();
        
        $permissions = [];
        foreach ($rolePerms as $rp) {
            $permissions[$rp['c_code']] = true;
        }

        // 2. Apply Custom Access Grants (The "Ceiling")
        // Both merchants and supervisors managing merchants are subject to this ceiling
        $managedMerchantIds = [];
        if ($userType === 'merchant') {
            $managedMerchantIds = [$userId];
        } else {
            $CI =& get_instance();
            $CI->load->model('Model_user');
            // Get ALL merchants managed by this supervisor
            $managedMerchantIds = $CI->Model_user->get_managed_merchant_ids($userId);
        }
        
        if (!empty($managedMerchantIds)) {
            $this->db->select('p.c_code, ag.c_isAllowed');
            $this->db->from('rbac_merchant_access_grants ag');
            $this->db->join('rbac_permissions p', 'p.id = ag.ref_permissionId');
            $this->db->where_in('ag.ref_granteeMerchantId', $managedMerchantIds);
            // Process Denies (0) last so they override any Grants (1) in this loop
            $this->db->order_by('ag.c_isAllowed', 'DESC'); 
            $grants = $this->db->get()->result_array();

            foreach ($grants as $g) {
                if ($g['c_isAllowed']) {
                    $permissions[$g['c_code']] = true;
                } else {
                    unset($permissions[$g['c_code']]); // Explicit Deny overrides anything
                }
            }
        }

        return array_keys($permissions);
    }

    // ── Access Grants ──────────────────────────────────────────────

    public function getAccessGrantsByGranter($granterId, $granteeId) {
        $this->db->select('ag.*, p.c_code, p.c_name, p.c_group, p.c_description');
        $this->db->from('rbac_merchant_access_grants ag');
        $this->db->join('rbac_permissions p', 'p.id = ag.ref_permissionId');
        $this->db->where('ag.ref_granterMerchantId', $granterId);
        $this->db->where('ag.ref_granteeMerchantId', $granteeId);
        return $this->db->get()->result_array();
    }

    public function upsertAccessGrant($granterId, $granteeId, $permissionId, $isAllowed) {
        $where = [
            'ref_granterMerchantId' => $granterId,
            'ref_granteeMerchantId' => $granteeId,
            'ref_permissionId'      => $permissionId
        ];
        $exists = $this->db->get_where('rbac_merchant_access_grants', $where)->row_array();
        
        if ($exists) {
            return $this->db->where('id', $exists['id'])->update('rbac_merchant_access_grants', ['c_isAllowed' => $isAllowed]);
        } else {
            $data = $where;
            $data['c_isAllowed'] = $isAllowed;
            return $this->db->insert('rbac_merchant_access_grants', $data);
        }
    }

    public function removeAccessGrant($granterId, $granteeId, $permissionId) {
        $where = [
            'ref_granterMerchantId' => $granterId,
            'ref_granteeMerchantId' => $granteeId,
            'ref_permissionId'      => $permissionId
        ];
        return $this->db->delete('rbac_merchant_access_grants', $where);
    }

    // ── Sidebar Menus ──────────────────────────────────────────────

    public function getSidebarMenuTree() {
        $supervisorId = $this->session->userdata('supervisor_id');

        $this->db->select('m.*, p.c_code as permission_code, IFNULL(p.c_group, "") as c_group');
        $this->db->from('rbac_sidebar_menus m');
        $this->db->join('rbac_permissions p', 'p.id = m.ref_permissionId', 'left');
        
        // Calculate the minimum sort order for each group's top-level menus to sort the groups naturally
        $subquery = "(SELECT p2.c_group, MIN(m2.c_sortOrder) as group_sort FROM rbac_sidebar_menus m2 LEFT JOIN rbac_permissions p2 ON p2.id = m2.ref_permissionId WHERE p2.c_group IS NOT NULL AND p2.c_group != '' AND m2.parent_id IS NULL GROUP BY p2.c_group)";
        $this->db->join($subquery . ' gs', 'gs.c_group = p.c_group', 'left');

        $this->db->where('m.c_isActive', 1);

        $this->db->order_by('m.parent_id', 'ASC');
        // Group items together, but keep General/Empty groups at the top (usually for Dashboard)
        $this->db->order_by("CASE WHEN p.c_group IS NULL OR p.c_group = '' OR p.c_group = 'General' THEN 0 ELSE 1 END", 'ASC', FALSE);
        $this->db->order_by('IFNULL(gs.group_sort, 9999)', 'ASC', FALSE);
        $this->db->order_by('p.c_group', 'ASC');
        $this->db->order_by('m.c_sortOrder', 'ASC');
        $all = $this->db->get()->result_array();

        $tree = [];
        $refs = [];

        foreach ($all as $item) {
            $item['children'] = [];
            $id = $item['id'];
            $refs[$id] = $item;

            if ($item['parent_id'] === NULL) {
                $tree[$id] = &$refs[$id];
            } else {
                if (isset($refs[$item['parent_id']])) {
                    $refs[$item['parent_id']]['children'][] = &$refs[$id];
                }
            }
        }

        return array_values($tree);
    }

    public function getAllMenusFlat() {
        $this->db->select('m.*, p.c_code as permission_code, IFNULL(p.c_group, "General") as c_group');
        $this->db->from('rbac_sidebar_menus m');
        $this->db->join('rbac_permissions p', 'p.id = m.ref_permissionId', 'left');
        
        // Calculate the minimum sort order for each group's top-level menus to sort the groups naturally
        $subquery = "(SELECT p2.c_group, MIN(m2.c_sortOrder) as group_sort FROM rbac_sidebar_menus m2 LEFT JOIN rbac_permissions p2 ON p2.id = m2.ref_permissionId WHERE p2.c_group IS NOT NULL AND p2.c_group != '' AND m2.parent_id IS NULL GROUP BY p2.c_group)";
        $this->db->join($subquery . ' gs', 'gs.c_group = p.c_group', 'left');
        
        $this->db->order_by('m.parent_id', 'ASC');
        $this->db->order_by("CASE WHEN p.c_group IS NULL OR p.c_group = '' OR p.c_group = 'General' THEN 0 ELSE 1 END", 'ASC', FALSE);
        $this->db->order_by('IFNULL(gs.group_sort, 9999)', 'ASC', FALSE);
        $this->db->order_by('p.c_group', 'ASC');
        $this->db->order_by('m.c_sortOrder', 'ASC');
        
        $menus = $this->db->get()->result_array();
        
        $parents = [];
        $children = [];
        foreach ($menus as $m) {
            if ($m['parent_id'] === NULL || $m['parent_id'] == 0) {
                $parents[] = $m;
            } else {
                $children[$m['parent_id']][] = $m;
            }
        }

        $all_menus = [];
        foreach ($parents as $p) {
            $all_menus[] = $p;
            if (isset($children[$p['id']])) {
                foreach ($children[$p['id']] as $c) {
                    $all_menus[] = $c;
                }
            }
        }
        return $all_menus;
    }

    public function get_datatables_handler() {
        $this->load->library('datatables');

        $this->datatables->of('rbac_roles')
            ->select('id, c_name, c_isSystem');

        // Standard column searching/ordering mapping
        $this->datatables->set_column_order(['c_name', 'c_isSystem', null]);
        $this->datatables->set_column_search(['c_name']);

        return $this->datatables->make(true);
    }
}
