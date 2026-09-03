<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MenuController
 * Handles navigation menus, submenus, dynamic role access control, and hierarchical grouping.
 */
class MenuController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'form_validation']);
        $this->load->model(['Model_menu', 'Model_user']);
        is_logged_in();
    }

    private function _isApi()
    {
        return $this->input->is_ajax_request()
            || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false
            || $this->input->get('json') == '1';
    }

    private function _parseRawJson()
    {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                foreach ($parsed as $k => $v) {
                    if ($this->input->post($k) === null) {
                        $_POST[$k] = $v;
                    }
                }
            }
        }
    }

    private function _respond($isApi, $status, $message, $redirectUrl = null, $data = [])
    {
        if ($isApi) {
            $resp = array_merge(['status' => $status, 'message' => $message], $data);
            return $this->output->set_content_type('application/json')->set_output(json_encode($resp));
        }
        $this->session->set_flashdata('message', '<div class="alert alert-' . ($status ? 'success' : 'danger') . '" role="alert">' . $message . '</div>');
        if ($redirectUrl) {
            redirect($redirectUrl);
        }
    }

    public function index()
    {
        $data = [
            'title'                  => 'Menu Management',
            'user'                   => $this->Model_user->view_user()->row_array(),
            'menu'                   => $this->Model_menu->get_menu()->result_array(),
            'breadcrumb_url_replace' => ['menu' => 'access-control/menus']
        ];

        $this->form_validation->set_rules('menu', 'Menu', 'required');
        if ($this->form_validation->run() == false) {
            $this->load->view('menu/index', $data);
        } else {
            $this->Model_menu->addMenu(['menu' => $this->input->post('menu')], 'user_menu');
            $this->_respond(false, true, 'New Menu Added!', 'menu');
        }
    }

    public function editMenu($id = null)
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        if ($id === null) {
            $id = $this->input->post('id');
        }

        if (!$id) {
            $this->_respond($isApi, false, 'Menu ID not found.', 'menu');
            return;
        }

        $this->form_validation->set_rules('menu', 'Menu Name', 'required');
        if ($this->form_validation->run() == false) {
            $data = [
                'title'                  => 'Edit Menu',
                'user'                   => $this->Model_user->view_user()->row_array(),
                'menu'                   => $this->Model_menu->get_menu_by_id($id)->row_array(),
                'breadcrumb_url_replace' => ['menu' => 'access-control/menus']
            ];
            $this->load->view('menu/edit_menu', $data);
        } else {
            $this->Model_menu->update_menu(['id' => $id], ['menu' => $this->input->post('menu')], 'user_menu');
            $this->_respond($isApi, true, 'Menu Updated Successfully.', 'menu');
        }
    }

    public function deleteMenu($id)
    {
        $isApi = $this->_isApi();
        if (!$id) {
            $this->_respond($isApi, false, 'Menu ID not found.', 'menu');
            return;
        }

        $this->Model_menu->delete_menu(['id' => $id], 'user_menu');
        $this->_respond($isApi, true, 'Menu Deleted Successfully.', 'menu');
    }

    public function subMenu()
    {
        $isApi = $this->_isApi();
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'Icon', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'title'                  => 'Submenu Management',
                'user'                   => $this->Model_user->view_user()->row_array(),
                'subMenu'                => $this->Model_menu->getSubMenu(),
                'menu'                   => $this->Model_menu->get_menu()->result_array(),
                'breadcrumb_url_replace' => ['menu' => 'access-control/menus', 'subMenu' => 'access-control/submenus']
            ];
            $this->load->view('menu/submenu', $data);
        } else {
            $dataInsert = [
                'title'     => $this->input->post('title'),
                'menu_id'   => $this->input->post('menu_id'),
                'url'       => $this->input->post('url'),
                'icon'      => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            $this->Model_menu->addSubMenu($dataInsert, 'user_sub_menu');
            $this->_respond($isApi, true, 'New Submenu Added!', 'menu/subMenu');
        }
    }

    public function editSubmenu($id = null)
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        if ($id === null) {
            $id = $this->input->post('id');
        }

        if (!$id) {
            $this->_respond($isApi, false, 'Submenu ID not found.', 'menu/subMenu');
            return;
        }

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'Icon', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'title'                  => 'Edit Submenu',
                'user'                   => $this->Model_user->view_user()->row_array(),
                'subMenu'                => $this->Model_menu->getSubMenuById($id)->row_array(),
                'menu'                   => $this->Model_menu->get_menu()->result_array(),
                'breadcrumb_url_replace' => ['menu' => 'access-control/menus', 'subMenu' => 'access-control/submenus']
            ];
            $this->load->view('menu/edit_submenu', $data);
        } else {
            $dataUpdate = [
                'title'     => $this->input->post('title'),
                'menu_id'   => $this->input->post('menu_id'),
                'url'       => $this->input->post('url'),
                'icon'      => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            $this->Model_menu->update_subMenu(['id' => $id], $dataUpdate, 'user_sub_menu');
            $this->_respond($isApi, true, 'Submenu Updated Successfully.', 'menu/subMenu');
        }
    }

    public function deleteSubmenu($id)
    {
        $isApi = $this->_isApi();
        if (!$id) {
            $this->_respond($isApi, false, 'Submenu ID not found.', 'menu/subMenu');
            return;
        }

        $result = $this->Model_menu->hapus_subMenu(['id' => $id], 'user_sub_menu');
        $msg = ($result === true) ? 'Submenu Deleted Successfully.' : 'Unable to delete submenu.';
        $this->_respond($isApi, $result === true, $msg, 'menu/subMenu');
    }

    public function role()
    {
        $this->load->model('AdminModel');
        $roles = $this->AdminModel->get_roles();
        if ($this->_isApi()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'data' => $roles]));
        }

        $data = [
            'title'                  => 'Role Access',
            'user'                   => $this->Model_user->view_user()->row_array(),
            'role'                   => $roles,
            'breadcrumb_url_replace' => ['menu' => 'access-control/roles']
        ];
        $this->load->view('menu/role', $data);
    }

    public function roleAccess($role_id)
    {
        $isApi = $this->_isApi();
        if (!$role_id) {
            $this->_respond($isApi, false, 'Role ID not found.', 'access-control/roles');
            return;
        }

        $roleData = $this->db->get_where('roles', ['id' => $role_id])->row_array();
        $access_query = $this->db->select('menu_id')->where('role_id', $role_id)->get('user_access_menu')->result_array();
        $assigned_menu_ids = array_column($access_query, 'menu_id');
        $menus = $this->Model_menu->get_all_menus_hierarchical();

        if ($isApi) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'role'              => $roleData,
                    'assigned_menu_ids' => $assigned_menu_ids,
                    'all_menus'         => $menus
                ]
            ]));
        }

        $data = [
            'title'                  => 'Role Access',
            'user'                   => $this->Model_user->view_user()->row_array(),
            'role'                   => $roleData,
            'role_access_ids'        => $assigned_menu_ids,
            'menu'                   => $menus,
            'main_menus'             => $this->db->get_where('user_menu', ['parent_id' => 0])->result_array(),
            'breadcrumb_replace'     => [$role_id => $roleData['role_name'] ?? 'Role'],
            'breadcrumb_url_replace' => [
                'menu'       => 'access-control/roles',
                'roleAccess' => 'access-control/roles/access/' . $role_id
            ],
            'group_modules'          => $this->db->select('group_modules')
                ->where('group_modules !=', '')
                ->where('group_modules IS NOT NULL', null, false)
                ->group_by('group_modules')
                ->get('user_menu')
                ->result_array()
        ];
        $this->load->view('menu/roleAccess', $data);
    }

    public function changeAccess()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $menu_id = $this->input->post('menuId') ?: $this->input->post('menu_id');
        $role_id = $this->input->post('roleId') ?: $this->input->post('role_id');

        if (!$menu_id || !$role_id) {
            $this->_respond($isApi, false, 'Invalid parameters.');
            return;
        }

        $params = ['role_id' => $role_id, 'menu_id' => $menu_id];
        $result = $this->db->get_where('user_access_menu', $params);

        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $params);
            $action = 'granted';
        } else {
            $this->db->delete('user_access_menu', $params);
            $action = 'revoked';
        }

        $this->_respond($isApi, true, 'Access ' . $action . ' successfully.', null, ['action' => $action]);
    }
}
