<?php defined('BASEPATH') or exit('No direct script access allowed');

class Model_menu extends CI_Model
{
   public function view_menu()
   {
      return $this->db->get('user_menu');
   }

   public function view_subMenu()
   {
      return $this->db->get('user_menu')->result_array();
   }

   public function getMenu()
   {
      $role_id = $this->session->userdata('role_id');
      $queryMenu = "SELECT `user_menu`.`id`, `menu`
                      FROM `user_menu` JOIN `user_access_menu` 
                        ON `user_menu`.`id` = `user_access_menu`.`menu_id`
                     WHERE `user_access_menu`.`role_id` = $role_id
                  ORDER BY `user_access_menu`.`menu_id` ASC
                  ";

      return $this->db->query($queryMenu)->result_array();
   }

   public function getSubMenu()
   {
      $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
                  FROM `user_sub_menu` JOIN `user_menu`
                    ON `user_sub_menu`.`menu_id` = `user_menu`.`id`
               ";

      return $this->db->query($query);
   }

   public function insert_menu($data, $table)
   {
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->insert($table, $data);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function insert_subMenu($data, $table)
   {
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->insert($table, $data);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function hapus_menu($where, $table)
   {
      $this->db->where($where);
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->delete($table);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function hapus_subMenu($where, $table)
   {
      $this->db->where($where);
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->delete($table);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function editSubMenu($where, $table)
   {
      return $this->db->get_where($table, $where);
   }

   public function changeSubMenu($where, $data, $table)
   {
      $this->db->where($where);
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->update($table, $data);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function editMenu($where, $table)
   {
      return $this->db->get_where($table, $where);
   }

   public function changeMenu($where, $data, $table)
   {
      $this->db->where($where);
      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;
      $success = $this->db->update($table, $data);
      $error = $this->db->error();
      $this->db->db_debug = $db_debug;
      return $success ? true : $error;
   }

   public function get_all_menus_hierarchical()
   {
      // Optimized: Fetch all menus in one query and organize hierarchy in PHP memory
      $menus = $this->db->order_by('parent_id', 'ASC')->order_by('menu_order', 'ASC')->get('user_menu')->result_array();
      
      $parents = [];
      $children = [];
      foreach ($menus as $m) {
         if ($m['parent_id'] == 0) {
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
}
