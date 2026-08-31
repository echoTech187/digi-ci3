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
        $currentUserRoleId = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($currentUserRoleId != 1) {
            show_error('Unauthorized access.', 403);
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

        // ── Handle AJAX DataTables / API Request ──
        if ($is_api_request) {
            // SILENT RESET: If DT search is cleared, clear session
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_roles');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
                $this->resetRoles(false);
            }

            if ($dtSearch !== '') {
                $this->session->set_userdata('last_dt_search_roles', $dtSearch);
            }

            $this->Rbac_model->get_datatables_handler();
            return;
        }

        $data['title'] = 'Merchant Role Management';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['permissions'] = $this->Rbac_model->getPermissionsByGroup();

        $this->load->view('rbac/roles', $data);
    }

    public function resetRoles($redirect = true)
    {
        $currentUserRoleId = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        if ($currentUserRoleId != 1) {
            show_error('Unauthorized access.', 403);
        }

        $this->session->unset_userdata('last_dt_search_roles');
        if ($redirect) {
            redirect('merchant/access-control/roles');
        }
    }

    public function save_role()
    {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $currentUserRoleId = intval($this->session->userdata('role') ?: ($this->session->userdata('role_id') ?: 1));
        if ($currentUserRoleId != 1) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Unauthorized access.']));
            }
            show_error('Unauthorized access.', 403);
            return;
        }

        $roleId      = $this->input->post('role_id') ?: $this->input->post('id');
        $roleName    = trim((string)($this->input->post('c_name') ?: ($this->input->post('role_name') ?: ($this->input->post('name') ?: $this->input->post('c_label')))));
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
                if ($is_api_request) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Role name is required.']));
                }
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
        if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Role saved successfully.']));
        }
        $this->session->set_flashdata('success', 'Role saved successfully.');
        redirect('merchant/access-control/roles');
    }

    public function get_role_permissions_json($roleId = null)
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $targetRoleId = $roleId ?: $this->uri->segment(4);

        $currentUserRoleId = intval($this->session->userdata('role') ?: ($this->session->userdata('role_id') ?: 1));
        if ($currentUserRoleId != 1) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Unauthorized access.']));
            }
            show_error('Unauthorized access.', 403);
            return;
        }

        $permissions = $this->Rbac_model->getRolePermissions($targetRoleId);
        $ids = array_column($permissions, 'id');
        return $this->output->set_content_type('application/json')->set_output(json_encode($ids));
    }

    public function menus()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

        $menus = $this->Rbac_model->getAllMenusFlat();

        if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'message' => 'Merchant menus retrieved successfully',
                'data' => $menus
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $data['title'] = 'Merchant Menu Management';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['menus'] = $menus;
        $data['permissions_grouped'] = $this->Rbac_model->getPermissionsByGroup(); // Grouped for dropdown
        $data['main_menus'] = $this->db->get_where('rbac_sidebar_menus', ['parent_id' => NULL])->result_array();

        $this->load->view('rbac/menus', $data);
    }

    public function save_menu()
    {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $currentUserRoleId = intval($this->session->userdata('role') ?: ($this->session->userdata('role_id') ?: 1));
        if ($currentUserRoleId != 1) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Unauthorized access.']));
            }
            show_error('Unauthorized access.', 403);
            return;
        }

        $id = $this->input->post('menu_id') ?: $this->input->post('id');
        $label = $this->input->post('c_label') ?: ($this->input->post('menu_title') ?: ($this->input->post('label') ?: $this->input->post('title')));
        $url   = $this->input->post('c_url') ?: ($this->input->post('url') ?: $this->input->post('route'));
        $icon  = $this->input->post('c_icon') ?: ($this->input->post('icon') ?: 'fas fa-link');
        $sortOrder = $this->input->post('c_sortOrder') ?: ($this->input->post('sort_order') ?: 0);
        $isActive  = $this->input->post('c_isActive') !== null ? $this->input->post('c_isActive') : ($this->input->post('is_active') !== null ? $this->input->post('is_active') : 1);

        if (empty($label)) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Menu title/label is required.']));
            }
            $this->session->set_flashdata('error', 'Menu title/label is required.');
            redirect('merchant/access-control/menus');
            return;
        }

        $newPermCode = $this->input->post('new_permission_code');
        $refPermissionId = $this->input->post('ref_permissionId') ?: NULL;

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        // ── Auto-Create or Update Permission Group ──
        if (!empty($newPermCode)) {
            $existing = $this->db->get_where('rbac_permissions', ['c_code' => $newPermCode])->row_array();
            if ($existing) {
                $refPermissionId = $existing['id'];
                if ($this->input->post('c_group')) {
                    $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
                }
            } else {
                $permData = [
                    'c_code' => $newPermCode,
                    'c_name' => $label,
                    'c_group' => $this->input->post('c_group') ?: 'General',
                    'c_description' => 'Auto-generated for menu: ' . $label,
                    'c_createdAt' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('rbac_permissions', $permData);
                $refPermissionId = $this->db->insert_id();
            }
        } elseif ($refPermissionId && $this->input->post('c_group')) {
            $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
        } elseif (!$refPermissionId && !empty($this->input->post('c_group')) && $this->input->post('c_group') !== 'General') {
            $autoCode = 'view_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $label));

            $existing = $this->db->get_where('rbac_permissions', ['c_code' => $autoCode])->row_array();
            if ($existing) {
                $refPermissionId = $existing['id'];
                $this->db->where('id', $refPermissionId)->update('rbac_permissions', ['c_group' => $this->input->post('c_group')]);
            } else {
                $permData = [
                    'c_code' => $autoCode,
                    'c_name' => 'View ' . $label,
                    'c_group' => $this->input->post('c_group'),
                    'c_description' => 'Automatically created to support menu grouping',
                    'c_createdAt' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('rbac_permissions', $permData);
                $refPermissionId = $this->db->insert_id();
            }
        }

        $data = [
            'c_label' => $label,
            'c_url'   => $url ?: '',
            'c_icon'  => $icon,
            'parent_id' => $this->input->post('parent_id') ?: NULL,
            'ref_permissionId' => $refPermissionId,
            'c_sortOrder' => $sortOrder,
            'c_isActive' => $isActive ? 1 : 0
        ];

        if ($id) {
            $res = $this->db->where('id', $id)->update('rbac_sidebar_menus', $data);
        } else {
            $res = $this->db->insert('rbac_sidebar_menus', $data);
        }

        $err = $this->db->error();
        $this->db->db_debug = $db_debug;

        // Clear local RBAC menu cache if loaded
        if ($this->load->is_loaded('rbac')) {
            $this->rbac->clear_menu_cache();
        }

        if ($is_api_request) {
            if ($res || $err['code'] == 0) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Menu item saved successfully.']));
            } else {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => !empty($err['message']) ? $err['message'] : 'Failed to save menu item.']));
            }
        }
        $this->session->set_flashdata('success', 'Menu item saved successfully.');
        redirect('merchant/access-control/menus');
    }
}
