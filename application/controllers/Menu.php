<?php defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
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
         $this->load->view('templates/user_header.php', $data);
         $this->load->view('templates/user_sidebar.php', $data);
         $this->load->view('templates/user_topbar.php', $data);
         $this->load->view('menu/index', $data);
         $this->load->view('templates/user_footer.php');
      } else {
         $data = [
            'menu' => $this->input->post('menu')
         ];

         $this->Model_menu->insert_menu($data, 'user_menu');

         $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New Menu Added</div>');
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

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('menu/editMenu', $data);
      $this->load->view('templates/user_footer.php');
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

      $this->Model_menu->changeMenu($where, $data, 'user_menu');
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Changed</div>');
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
         $this->load->view('templates/user_header.php', $data);
         $this->load->view('templates/user_sidebar.php', $data);
         $this->load->view('templates/user_topbar.php', $data);
         $this->load->view('menu/subMenu', $data);
         $this->load->view('templates/user_footer.php');
      } else {
         $data = [
            'menu_id'   => $this->input->post('menu_id'),
            'title'     => $this->input->post('title'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
         ];

         $this->Model_menu->insert_subMenu($data, 'user_sub_menu');

         $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New Submenu Added</div>');
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

         $this->load->view('templates/user_header.php', $data);
         $this->load->view('templates/user_sidebar.php', $data);
         $this->load->view('templates/user_topbar.php', $data);
         $this->load->view('menu/editSubMenu', $data);
         $this->load->view('templates/user_footer.php');
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

      $this->Model_menu->changeSubMenu($where, $data, 'user_sub_menu');
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

      $this->Model_menu->hapus_menu($where, 'user_menu');
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

      $this->Model_menu->hapus_subMenu($where, 'user_sub_menu');
      redirect('menu/subMenu');
   }

   public function role()
   {
      $data['title'] = 'Role Access';
      $data['user'] = $this->Model_user->view_user()->row_array();
      
      $this->load->model('AdminModel');
      $data['role'] = $this->AdminModel->get_roles();
      $data['breadcrumb_url_replace'] = [
         'menu' => 'menu/role'
      ];
      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('menu/role', $data);
      $this->load->view('templates/user_footer.php');
   }

   public function roleAccess($role_id)
   {
      if (!$role_id) {
         $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Role ID not found.</div>');
         redirect('menu/role');
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
         'menu'       => 'menu/role',
         'roleAccess' => 'menu/roleAccess/'.$role_id,
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

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('menu/roleAccess', $data);
      $this->load->view('templates/user_footer.php');
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
      $this->session->set_flashdata('message', 'Menu Updated Successfully!');
      echo json_encode(['status' => 'success']);
   }

   public function deleteMenuAjax()
   {
      $id = $this->input->post('id');
      
      // Also delete submenus
      $this->db->where('parent_id', $id)->delete('user_menu');
      
      // Delete the menu itself
      $this->Model_menu->hapus_menu(['id' => $id], 'user_menu');
      
      // Clean up access mappings
      $this->db->where('menu_id', $id)->delete('user_access_menu');

      $this->session->set_flashdata('message', 'Menu Deleted Successfully!');
      echo json_encode(['status' => 'success']);
   }
}
