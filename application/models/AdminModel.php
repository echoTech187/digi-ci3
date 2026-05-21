<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminModel extends CI_Model {

  public function get_admins(){
    $query = "
      SELECT a.*, b.role_name FROM admin a
      LEFT JOIN roles b ON a.role_id = b.id
      ORDER BY id DESC
    ";
    return $this->db->query($query)->result();
  }

  public function get_roles(){
    $query = "SELECT * FROM roles";
    return $this->db->query($query)->result();
  }
  
  public function add_admin($data) {
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;
    
    $success = $this->db->insert('admin', $data);
    $error = $this->db->error();
    
    $this->db->db_debug = $db_debug;
    
    if ($success) {
        return true;
    } else {
        return $error;
    }
  }

  public function update_admin($id, $data) {
    $this->db->where('id', $id);
    
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;
    
    $success = $this->db->update('admin', $data);
    $error = $this->db->error();
    
    $this->db->db_debug = $db_debug;
    
    if ($success) {
        return true;
    } else {
        return $error; // Returns array with 'code' and 'message'
    }
  }

  public function delete_admin($id) {
    $this->db->where('id', $id);
    
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;
    
    $success = $this->db->delete('admin');
    $error = $this->db->error();
    
    $this->db->db_debug = $db_debug;
    
    if ($success) {
        return true;
    } else {
        return $error;
    }
  }

  // --- DataTables Server-Side Processing ---
  var $table = 'admin a';
  var $column_order = array('a.c_email', 'a.c_name', 'a.c_status', 'a.c_level', 'b.role_name', null);
  var $column_search = array('a.c_email', 'a.c_name', 'a.c_level', 'b.role_name');
  var $order = array('a.id' => 'desc');
  private static $cached_total = null;

  private function _get_datatables_query()
  {
      // Emergency 30-second safeguard
      $this->db->query("SET SESSION max_execution_time = 30000");
      
      $this->db->select('a.*, b.role_name');
      $this->db->from($this->table);
      $this->db->join('roles b', 'a.role_id = b.id', 'left');

      $i = 0;
      foreach ($this->column_search as $item) {
          if (isset($_POST['search']['value']) && $_POST['search']['value']) {
              if ($i === 0) {
                  $this->db->group_start();
                  $this->db->like($item, $_POST['search']['value']);
              } else {
                  $this->db->or_like($item, $_POST['search']['value']);
              }
              if (count($this->column_search) - 1 == $i)
                  $this->db->group_end();
          }
          $i++;
      }

      if (isset($_POST['order'])) {
          $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
      } else if (isset($this->order)) {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
      }
  }

  public function get_datatables()
  {
      $this->_get_datatables_query();
      if (isset($_POST['length']) && $_POST['length'] != -1)
          $this->db->limit($_POST['length'], $_POST['start']);
      $query = $this->db->get();
      return $query->result();
  }

    public function count_filtered()
    {
        $is_filtered = (isset($_POST['search']['value']) && !empty($_POST['search']['value']));
        if (!$is_filtered) {
            return $this->count_all();
        }

        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $table_name = explode(' ', $this->table)[0];
        $q = $this->db->query("SHOW TABLE STATUS LIKE '{$table_name}'");
        $res = $q->row();
        if ($res && isset($res->Rows)) {
            self::$cached_total = (int)$res->Rows;
            return self::$cached_total;
        }

        $this->db->select("count(id) as total");
        $this->db->from($table_name);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $dt = $this->datatables->of('admin a')
            ->select('a.*, b.role_name')
            ->join('roles b', 'a.role_id = b.id', 'left');

        if (!empty($filters)) {
            foreach ($filters as $field => $val) {
                if ($val !== '') {
                    $dt->where($field, $val);
                }
            }
        }

        return $dt->set_column_order(['a.c_email', 'a.c_name', 'a.c_status', 'a.c_level', 'b.role_name', null])
            ->set_column_search(['a.c_email', 'a.c_name', 'a.c_level', 'b.role_name'])
            ->set_default_order(['a.id' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>