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

   public function changeMenu($id)
   {
      if (!$id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Menu ID not found.</div>');
         redirect('menu');
      }
      $where = [
         'id' => $id
      ];

      $data['title'] = 'Change Menu';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['Mmenu'] = $this->Model_menu->view_menu()->result_array();
      $data['Mmenu'] = $this->Model_menu->editMenu($where, 'user_menu')->result_array();
      $data['menu'] = $this->Model_menu->getMenu();

      $this->form_validation->set_rules('menu', 'Menu', 'required');

      $this->load->view('menu/editMenu', $data);
   }

   public function updateMenu()
   {
      $id = $this->input->post('id');

      $data = [
         'menu' => $this->input->post('menu')
      ];

      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->changeMenu($where, $data, 'user_menu');
      if ($result === true) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Menu Updated Successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Menu Updated Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to update menu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to modify menu items.';
         }
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $msg]);
             return;
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
      $id = $this->input->post('id');

      $data = [
         'menu_id'    => $this->input->post('menu_id'),
         'title'    => $this->input->post('title'),
         'url'        => $this->input->post('url'),
         'icon'        => $this->input->post('icon'),
         'is_active' => $this->input->post('is_active')
      ];

      $where = [
         'id' => $id,
      ];

      $result = $this->Model_menu->changeSubMenu($where, $data, 'user_sub_menu');
      if ($result === true) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'success', 'message' => 'Submenu Updated Successfully.']);
             return;
         }
         $this->session->set_flashdata('success', 'Submenu Updated Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         $msg = 'Unable to update submenu due to a system constraint. Please contact technical support.';
         if ($code == 1142) {
            $msg = 'Access Denied. You do not have sufficient database privileges to modify submenu items.';
         }
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => $msg]);
             return;
         }
         $this->session->set_flashdata('error', $msg);
      }
      redirect('menu/submenu');
   }

   public function hapus($id)
   {
      if (!$id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Menu ID not found.</div>');
         redirect('menu');
      }
      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->hapus_menu($where, 'user_menu');
      if ($result === true) {
         $this->session->set_flashdata('success', 'Menu Deleted Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete menu items.');
         } elseif ($code == 1451) {
            $this->session->set_flashdata('error', 'Cannot delete this menu because it contains active submenus or access permissions.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete menu due to a system constraint. Please contact technical support.');
         }
      }
      redirect('menu');
   }

   public function hapus_subMenu($id)
   {
      if (!$id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Submenu ID not found.</div>');
         redirect('menu/subMenu');
      }
      $where = [
         'id' => $id
      ];

      $result = $this->Model_menu->hapus_subMenu($where, 'user_sub_menu');
      if ($result === true) {
         $this->session->set_flashdata('success', 'Submenu Deleted Successfully.');
      } else {
         $code = isset($result['code']) ? $result['code'] : 0;
         if ($code == 1142) {
            $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete submenu items.');
         } else {
            $this->session->set_flashdata('error', 'Unable to delete submenu due to a system constraint. Please contact technical support.');
         }
      }
      redirect('menu/subMenu');
   }

   public function role()
   {
      $data['title'] = 'Role Access';
      $data['user'] = $this->Model_user->view_user()->row_array();
      
      $this->load->model('AdminModel');
      $data['role'] = $this->AdminModel->get_roles();
      $data['breadcrumb_url_replace'] = [
         'menu' => 'access-control/roles'
      ];
      $this->load->view('menu/role', $data);
   }

   public function roleAccess($role_id)
   {
      if (!$role_id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Role ID not found.</div>');
         redirect('access-control/roles');
      }
      $data['title'] = 'Role Access';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $this->load->model('AdminModel');
      $data['role'] = $this->db->get_where('roles', ['id' => $role_id])->row_array();

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

      // Get all menus hierarchically
      $data['menu'] = $this->Model_menu->get_all_menus_hierarchical();
      $data['main_menus'] = $this->db->get_where('user_menu', ['parent_id' => 0])->result_array();

      // Fetch all assigned menu IDs for this role in ONE query to avoid N+1 in view
      $this->db->select('menu_id');
      $this->db->where('role_id', $role_id);
      $access_query = $this->db->get('user_access_menu')->result_array();
      $data['role_access_ids'] = array_column($access_query, 'menu_id');

      $this->db->select('group_modules');
      $this->db->where('group_modules !=', '');
      $this->db->where('group_modules IS NOT NULL', null, false);
      $this->db->group_by('group_modules');
      $data['group_modules'] = $this->db->get('user_menu')->result_array();

      $this->load->view('menu/roleAccess', $data);
   }

   public function changeAccess()
   {
      $menu_id = $this->input->post('menuId');
      $role_id = $this->input->post('roleId');

      $data = [
         'role_id' => $role_id,
         'menu_id' => $menu_id
      ];

      $result = $this->db->get_where('user_access_menu', $data);

      if ($result->num_rows() < 1) {
         $this->db->insert('user_access_menu', $data);
      } else {
         $this->db->delete('user_access_menu', $data);
      }
      
      $this->rbac->clear_menu_cache();
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Access Modified!</div>');
   }

   public function getMenuById($id)
   {
      $menu = $this->db->get_where('user_menu', ['id' => $id])->row_array();
      echo json_encode($menu);
   }

   public function saveMenuAjax()
   {
      $data = [
         'title' => $this->input->post('title'),
         'url' => $this->input->post('url'),
         'icon' => $this->input->post('icon'),
         'parent_id' => $this->input->post('parent_id'),
         'group_modules' => $this->input->post('group_modules'),
         'menu_order' => $this->input->post('menu_order'),
         'is_active' => 1
      ];

      $this->Model_menu->insert_menu($data, 'user_menu');
      $this->rbac->clear_menu_cache();
      $this->session->set_flashdata('message', 'New Menu Added Successfully!');
      echo json_encode(['status' => 'success']);
   }

   public function updateMenuAjax()
   {
      $id = $this->input->post('id');
      $data = [
         'title' => $this->input->post('title'),
         'url' => $this->input->post('url'),
         'icon' => $this->input->post('icon'),
         'parent_id' => $this->input->post('parent_id'),
         'group_modules' => $this->input->post('group_modules'),
         'menu_order' => $this->input->post('menu_order')
      ];

      $this->Model_menu->changeMenu(['id' => $id], $data, 'user_menu');
      $this->rbac->clear_menu_cache();
      $this->session->set_flashdata('message', 'Menu Updated Successfully!');
      echo json_encode(['status' => 'success']);
   }

   public function deleteMenuAjax()
   {
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
         echo json_encode(['status' => 'error', 'message' => $msg]);
      } else {
         $this->rbac->clear_menu_cache();
         $this->session->set_flashdata('message', 'Menu Deleted Successfully!');
         echo json_encode(['status' => 'success']);
      }
   }
}
