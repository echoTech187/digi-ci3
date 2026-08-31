<?php defined('BASEPATH') or exit('No direct script access allowed');

class MenuController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      is_logged_in();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->model('Model_user');
      $this->load->model('Model_menu');
   }

   public function index()
   {
      $data['title'] = 'Menu Management';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['Mmenu'] = $this->Model_menu->view_menu()->result_array();
      $data['menu'] = $this->Model_menu->getMenu();

      $this->form_validation->set_rules('menu', 'Menu', 'required');

      if ($this->form_validation->run() == false) {
         $this->load->view('menu/index', $data);
      } else {
         $data = [
            'menu' => $this->input->post('menu')
         ];

         $result = $this->Model_menu->insert_menu($data, 'user_menu');
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'New Menu Added Successfully.']);
                return;
            }
            $this->session->set_flashdata('success', 'New Menu Added Successfully.');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to add menu due to a system constraint. Please contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to create menu items.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }
         redirect('menu');
      }
   }

   public function changeMenu($id = null)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$id) $id = $this->uri->segment(3);

      $where = ['id' => $id];
      $menuItem = $id ? $this->Model_menu->editMenu($where, 'user_menu')->row_array() : null;

      if ($is_api_request) {
          if (!$menuItem) {
              $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => false,
                  'message' => 'Menu ID not found in database.'
              ]));
              return;
          }
          $this->output->set_content_type('application/json')->set_output(json_encode([
              'status' => true,
              'message' => 'Menu detail retrieved successfully',
              'data' => $menuItem
          ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
          return;
      }

      if (!$id || !$menuItem) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Menu ID not found.</div>');
         redirect('menu');
         return;
      }

      $data['title'] = 'Change Menu';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['Mmenu'] = [$menuItem];
      $data['menu'] = $this->Model_menu->getMenu();

      $this->load->view('menu/editMenu', $data);
   }

   public function updateMenu()
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

      $id = $this->input->post('id');
      $menu = $this->input->post('menu') ?: $this->input->post('title');

      if (empty($id) || empty($menu)) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Parameters id and menu are required.']));
         }
         $this->session->set_flashdata('error', 'Parameters id and menu are required.');
         redirect('menu');
         return;
      }

      $data = [
         'menu' => $menu
      ];

      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->changeMenu($where, $data, 'user_menu');
      if ($result === true) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Menu Updated Successfully.']));
         }
         $this->session->set_flashdata('success', 'Menu Updated Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to update menu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to modify menu items.';
         }
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('menu');
   }

   public function subMenu()
   {
      $data['title'] = 'Submenu Management';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['subMenu'] = $this->Model_menu->getSubMenu()->result_array();

      $data['menu'] = $this->Model_menu->view_subMenu();
      $data['menu'] = $this->Model_menu->getMenu();

      $this->form_validation->set_rules('menu_id', 'Menu', 'required');
      $this->form_validation->set_rules('title', 'Title', 'required');
      $this->form_validation->set_rules('url', 'Url', 'required');
      $this->form_validation->set_rules('icon', 'Icon', 'required');

      if ($this->form_validation->run() == false) {
         $this->load->view('menu/subMenu', $data);
      } else {
         $data = [
            'menu_id'   => $this->input->post('menu_id'),
            'title'     => $this->input->post('title'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
         ];

         $result = $this->Model_menu->insert_subMenu($data, 'user_sub_menu');
         if ($result === true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'New Submenu Added Successfully.']);
                return;
            }
            $this->session->set_flashdata('success', 'New Submenu Added Successfully.');
         } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to add submenu due to a system constraint. Please contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to create submenu items.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         }
         redirect('menu/subMenu');
      }
   }

   public function editSubMenu($id)
   {
      if (!$id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Submenu ID not found.</div>');
         redirect('menu/subMenu');
      }
      $where = [
         'id' => $id
      ];

      $data['title'] = 'Change Sub Menu';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['menu'] = $this->Model_menu->getMenu();
      $data['getMenu'] = $this->Model_menu->getSubMenu()->result_array();
      $data['subMenu'] = $this->Model_menu->editSubMenu($where, 'user_sub_menu')->result_array();

      $this->form_validation->set_rules('menu_id', 'Menu', 'required');
      $this->form_validation->set_rules('title', 'Title', 'required');
      $this->form_validation->set_rules('url', 'Url', 'required');
      $this->form_validation->set_rules('icon', 'Icon', 'required');

      if ($this->form_validation->run() == false) {
         // Breadcrumb override: Replace ID with Sub-menu Title
         $subMenuName = isset($data['subMenu'][0]['title']) ? $data['subMenu'][0]['title'] : 'Sub-menu';
         $data['breadcrumb_replace'] = [
            $id => $subMenuName
         ];

         $this->load->view('menu/editSubMenu', $data);
      }
   }

   public function updateSubMenu()
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

      $id = $this->input->post('id');
      $menu_id = $this->input->post('menu_id') ?: 1;
      $title = $this->input->post('title');
      $url = $this->input->post('url') ?: 'menu';
      $icon = $this->input->post('icon') ?: 'fas fa-fw fa-folder';
      $is_active = $this->input->post('is_active') !== null ? $this->input->post('is_active') : 1;

      if (empty($id) || empty($title)) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Parameters id and title are required.']));
         }
         $this->session->set_flashdata('error', 'Parameters id and title are required.');
         redirect('menu/submenu');
         return;
      }

      $data = [
         'menu_id'   => $menu_id,
         'title'     => $title,
         'url'       => $url,
         'icon'      => $icon,
         'is_active' => $is_active
      ];

      $where = [
         'id' => $id,
      ];

      $result = $this->Model_menu->changeSubMenu($where, $data, 'user_sub_menu');
      if ($result === true) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Submenu Updated Successfully.']));
         }
         $this->session->set_flashdata('success', 'Submenu Updated Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to update submenu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to modify submenu items.';
         }
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('menu/submenu');
   }

   public function hapus($id = null)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$id) $id = $this->uri->segment(3) ?: $this->input->post('id');

      if (!$id) {
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Menu ID not found.']));
         }
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Menu ID not found.</div>');
         redirect('menu');
         return;
      }
      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->hapus_menu($where, 'user_menu');
      if ($result === true) {
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Menu Deleted Successfully.']));
         }
         $this->session->set_flashdata('success', 'Menu Deleted Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to delete menu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to delete menu items.';
         } elseif ($code == 1451) {
            $msg = 'Cannot delete this menu because it contains active submenus or access permissions.';
         }
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('menu');
   }

   public function hapus_subMenu($id = null)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$id) $id = $this->uri->segment(4) ?: ($this->uri->segment(3) ?: $this->input->post('id'));

      if (!$id) {
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Submenu ID not found.']));
         }
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Submenu ID not found.</div>');
         redirect('menu/subMenu');
         return;
      }
      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->hapus_subMenu($where, 'user_sub_menu');
      if ($result === true) {
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Submenu Deleted Successfully.']));
         }
         $this->session->set_flashdata('success', 'Submenu Deleted Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to delete submenu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to delete submenu items.';
         }
         if ($is_api_request) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('menu/subMenu');
   }

   public function role()
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      $this->load->model('AdminModel');
      $roles = $this->AdminModel->get_roles();

      if ($is_api_request) {
          return $this->output->set_content_type('application/json')->set_output(json_encode([
              'status' => true,
              'message' => 'Roles list retrieved successfully',
              'data' => $roles
          ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      }

      $data['title'] = 'Role Access';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['role'] = $roles;
      $data['breadcrumb_url_replace'] = [
         'menu' => 'access-control/roles'
      ];
      $this->load->view('menu/role', $data);
   }

   public function roleAccess($role_id)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if (!$role_id) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Role ID not found.']));
         }
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Role ID not found.</div>');
         redirect('access-control/roles');
         return;
      }

      $this->load->model('AdminModel');
      $roleData = $this->db->get_where('roles', ['id' => $role_id])->row_array();

      // Fetch all assigned menu IDs for this role in ONE query
      $this->db->select('menu_id');
      $this->db->where('role_id', $role_id);
      $access_query = $this->db->get('user_access_menu')->result_array();
      $assigned_menu_ids = array_column($access_query, 'menu_id');

      $menus = $this->Model_menu->get_all_menus_hierarchical();

      if ($is_api_request) {
          return $this->output->set_content_type('application/json')->set_output(json_encode([
              'status' => true,
              'message' => 'Role access details retrieved successfully',
              'data' => [
                  'role' => $roleData,
                  'assigned_menu_ids' => $assigned_menu_ids,
                  'all_menus' => $menus
              ]
          ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      }

      $data['title'] = 'Role Access';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['role'] = $roleData;
      $data['role_access_ids'] = $assigned_menu_ids;
      $data['menu'] = $menus;
      $data['main_menus'] = $this->db->get_where('user_menu', ['parent_id' => 0])->result_array();

      // Breadcrumb override: Replace ID with Role name
      $role_name = isset($data['role']['role_name']) ? $data['role']['role_name'] : 'Role';
      $data['breadcrumb_replace'] = [
         $role_id => $role_name
      ];

      // Custom breadcrumb redirects
      $data['breadcrumb_url_replace'] = [
         'menu'       => 'access-control/roles',
         'roleAccess' => 'access-control/roles/access/'.$role_id,
      ];

      $this->db->select('group_modules');
      $this->db->where('group_modules !=', '');
      $this->db->where('group_modules IS NOT NULL', null, false);
      $this->db->group_by('group_modules');
      $data['group_modules'] = $this->db->get('user_menu')->result_array();

      $this->load->view('menu/roleAccess', $data);
   }

   public function changeAccess()
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

      $menu_id = $this->input->post('menuId') ?: $this->input->post('menu_id');
      $role_id = $this->input->post('roleId') ?: $this->input->post('role_id');

      if (empty($menu_id) || empty($role_id)) {
         if ($is_api_request) {
             return $this->output->set_content_type('application/json')->set_output(json_encode([
                 'status' => false,
                 'message' => 'Parameters role_id (or roleId) and menu_id (or menuId) are required.'
             ]));
         }
         $this->session->set_flashdata('error', 'Parameters role_id and menu_id are required.');
         redirect('access-control/roles');
         return;
      }

      $data = [
         'role_id' => $role_id,
         'menu_id' => $menu_id
      ];

      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;

      $result = $this->db->get_where('user_access_menu', $data);

      if ($result->num_rows() < 1) {
         $this->db->insert('user_access_menu', $data);
      } else {
         $this->db->delete('user_access_menu', $data);
      }

      $this->db->db_debug = $db_debug;
      
      $this->rbac->clear_menu_cache();

      if ($is_api_request) {
          return $this->output->set_content_type('application/json')->set_output(json_encode([
              'status' => true,
              'message' => 'Menu access modified successfully.'
          ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      }

      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Access Modified!</div>');
   }

   public function getMenuById($id = null)
   {
      if (!$id) $id = $this->uri->segment(3);
      $menu = $this->db->get_where('user_menu', ['id' => $id])->row_array();
      return $this->output->set_content_type('application/json')->set_output(json_encode([
          'status' => $menu ? true : false,
          'message' => $menu ? 'Menu detail retrieved successfully' : 'Menu not found',
          'data' => $menu
      ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
   }

   public function saveMenuAjax()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $data = [
         'title' => $this->input->post('title'),
         'url' => $this->input->post('url'),
         'icon' => $this->input->post('icon'),
         'parent_id' => $this->input->post('parent_id') ?: 0,
         'group_modules' => $this->input->post('group_modules') ?: 'General',
         'menu_order' => $this->input->post('menu_order') ?: 1,
         'is_active' => 1
      ];

      $this->Model_menu->insert_menu($data, 'user_menu');
      $this->rbac->clear_menu_cache();
      $this->session->set_flashdata('message', 'New Menu Added Successfully!');
      return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'New Menu Added Successfully!']));
   }

   public function updateMenuAjax()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $id = $this->input->post('id');
      $data = [
         'title' => $this->input->post('title'),
         'url' => $this->input->post('url'),
         'icon' => $this->input->post('icon'),
         'parent_id' => $this->input->post('parent_id'),
         'group_modules' => $this->input->post('group_modules'),
         'menu_order' => $this->input->post('menu_order')
      ];

      $this->Model_menu->changeMenu(['id' => $id], array_filter($data, function($v) { return $v !== null; }), 'user_menu');
      $this->rbac->clear_menu_cache();
      $this->session->set_flashdata('message', 'Menu Updated Successfully!');
      return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Menu Updated Successfully!']));
   }

   public function deleteMenuAjax()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === null && $this->input->post($k) === null) {
               $_POST[$k] = $v;
            }
         }
      }

      $id = $this->input->post('id');

      // Also delete submenus
      $this->db->where('parent_id', $id);
      $successSub = $this->db->delete('user_menu');
      $errSub = $this->db->error();
      
      // Delete the menu itself
      $resultMenu = $this->Model_menu->hapus_menu(['id' => $id], 'user_menu');
      
      // Clean up access mappings
      $this->db->where('menu_id', $id);
      $successAccess = $this->db->delete('user_access_menu');
      $errAccess = $this->db->error();

      if (!$successSub || $resultMenu !== true || !$successAccess) {
         $err = (!$successSub) ? $errSub : (($resultMenu !== true) ? $resultMenu : $errAccess);
         $code = isset($err['code']) ? $err['code'] : 0;
         $msg = 'Unable to delete menu due to a system constraint.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to delete menu items.';
         } elseif ($code == 1451) {
            $msg = 'Cannot delete this menu because it contains active submenus or access permissions.';
         }
         return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
      } else {
         $this->rbac->clear_menu_cache();
         $this->session->set_flashdata('message', 'Menu Deleted Successfully!');
         return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Menu Deleted Successfully!']));
      }
   }
}
