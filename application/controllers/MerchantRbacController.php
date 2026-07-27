<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller for managing Merchant Access Control (Roles & Menus)
 * Extracted from MerchantManagementController to improve architecture and SRP.
 */
class MerchantRbacController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load library dasar
        $this->load->library('session');
        $this->load->library('rbac');
        $this->load->library('form_validation');

        // Load model dasar
        $this->load->model('Model_user');
        $this->load->model('Rbac_model');

        // Pastikan user sudah login
        is_logged_in();
    }

    // ── Merchant Portal Access Control (Roles & Menus) ──────────────────────────

    public function roles()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        // ── Handle AJAX DataTables Request ──
        if ($this->input->is_ajax_request()) {
            // SILENT RESET: If DT search is cleared, clear session
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_roles');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                $this->resetRoles(false);
            }

            if ($dtSearch !== '') {
                $this->session->set_userdata('last_dt_search_roles', $dtSearch);
            }

            echo $this->Rbac_model->get_datatables_handler();
            return;
        }

        $data['title'] = 'Merchant Role Management';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['permissions'] = $this->Rbac_model->getPermissionsByGroup();

        $this->load->view('rbac/roles', $data);
    }

    public function resetRoles($redirect = true)
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        $this->session->unset_userdata('last_dt_search_roles');
        if ($redirect) {
            redirect('merchant/access-control/roles');
        }
    }

    public function save_role()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        $roleId      = $this->input->post('role_id');
        $roleName    = trim($this->input->post('c_name'));
        $permissions = $this->input->post('permissions');

        if ($roleId) {
            // EDIT existing role — update permissions directly on the role
            $existingRole = $this->Rbac_model->getRoleById($roleId);
            if ($existingRole && !$existingRole['c_isSystem'] && $roleName) {
                // Only update name for non-system roles
                $this->Rbac_model->updateRole($roleId, ['c_name' => $roleName]);
            }
            $id = $roleId;
        } else {
            // CREATE new role
            if (empty($roleName)) {
                $this->session->set_flashdata('error', 'Role name is required.');
                redirect('merchant/access-control/roles');
                return;
            }
            $roleData = [
                'c_name'      => $roleName,
                'c_isSystem'  => 0,
                'c_isDefault' => 0
            ];
            $id = $this->Rbac_model->createRole($roleData);
        }

        $this->Rbac_model->setRolePermissions($id, $permissions);
        $this->session->set_flashdata('success', 'Role saved successfully.');
        redirect('merchant/access-control/roles');
    }

    public function get_role_permissions_json($roleId)
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        $permissions = $this->Rbac_model->getRolePermissions($roleId);
        $ids = array_column($permissions, 'id');
        echo json_encode($ids);
    }

    public function menus()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        $data['title'] = 'Merchant Menu Management';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['menus'] = $this->Rbac_model->getAllMenusFlat();
        $data['permissions_grouped'] = $this->Rbac_model->getPermissionsByGroup(); // Grouped for dropdown
        $data['main_menus'] = $this->db->get_where('rbac_sidebar_menus', ['parent_id' => NULL])->result_array();

        $this->load->view('rbac/menus', $data);
    }

    public function save_menu()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($role_id != 1) {
            show_error('Unauthorized access.', 403);
        }

        $id = $this->input->post('menu_id');
        $newPermCode = $this->input->post('new_permission_code');
        $refPermissionId = $this->input->post('ref_permissionId') ?: NULL;

        // ── Auto-Create or Update Permission Group ──
        if (!empty($newPermCode)) {
            // Case A: Create New Permission manually via Code input
            $existing = $this->db->get_where('rbac_permissions', ['c_code' => $newPermCode])->row_array();
            if ($existing) {
                $refPermissionId = $existing['id'];
                if ($this->input->post('c_group')) {
                    $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
                }
            } else {
                $permData = [
                    'c_code' => $newPermCode,
                    'c_name' => $this->input->post('c_label'),
                    'c_group' => $this->input->post('c_group') ?: 'General',
                    'c_description' => 'Auto-generated for menu: ' . $this->input->post('c_label'),
                    'c_createdAt' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('rbac_permissions', $permData);
                $refPermissionId = $this->db->insert_id();
            }
        } elseif ($refPermissionId && $this->input->post('c_group')) {
            // Case B: Update group of an EXISTING linked permission
            $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
        } elseif (!$refPermissionId && !empty($this->input->post('c_group')) && $this->input->post('c_group') !== 'General') {
            // Case C: Group provided but NO permission linked -> Auto-create a "view_" permission
            $autoCode = 'view_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $this->input->post('c_label')));

            // Check if this auto-code exists
            $existing = $this->db->get_where('rbac_permissions', ['c_code' => $autoCode])->row_array();
            if ($existing) {
                $refPermissionId = $existing['id'];
                $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
            } else {
                $permData = [
                    'c_code' => $autoCode,
                    'c_name' => 'View ' . $this->input->post('c_label'),
                    'c_group' => $this->input->post('c_group'),
                    'c_description' => 'Automatically created to support menu grouping',
                    'c_createdAt' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('rbac_permissions', $permData);
                $refPermissionId = $this->db->insert_id();
            }
        }

        $data = [
            'c_label' => $this->input->post('c_label'),
            'c_url'   => $this->input->post('c_url'),
            'c_icon'  => $this->input->post('c_icon'),
            'parent_id' => $this->input->post('parent_id') ?: NULL,
            'ref_permissionId' => $refPermissionId,
            'c_sortOrder' => $this->input->post('c_sortOrder'),
            'c_isActive' => $this->input->post('c_isActive') ? 1 : 0
        ];

        if ($id) {
            $this->db->where('id', $id)->update('rbac_sidebar_menus', $data);
        } else {
            $this->db->insert('rbac_sidebar_menus', $data);
        }

        // Clear local RBAC menu cache if loaded
        if ($this->load->is_loaded('rbac')) {
            $this->rbac->clear_menu_cache();
        }

        $this->session->set_flashdata('success', 'Menu item saved successfully.');
        redirect('merchant/access-control/menus');
    }
}
