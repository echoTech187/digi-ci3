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
    
    $success = $this->db->insert('admin', $data);
    $error = $this->db->error();
    
    
    if ($success) {
        return true;
    } else {
        return $error;
    }
  }

  public function update_admin($id, $data) {
    $this->db->where('id', $id);
    
    
    $success = $this->db->update('admin', $data);
    $error = $this->db->error();
    
    
    if ($success) {
        return true;
    } else {
        return $error; // Returns array with 'code' and 'message'
    }
  }

  public function delete_admin($id) {
    $this->db->where('id', $id);
    
    
    $success = $this->db->delete('admin');
    $error = $this->db->error();
    
    
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

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $dt = $this->datatables->of('admin a')
            ->select('a.*, b.role_name')
            ->join('roles b', 'a.role_id = b.id', 'left');

        if (!empty($filters)) {
            foreach ($filters as $field => $val) {
                if ($field === 'custom_search') {
                    $escaped_search = $this->db->escape_like_str($val);
                    $dt->where("(a.c_name LIKE '%{$escaped_search}%' ESCAPE '!' OR a.c_email LIKE '%{$escaped_search}%' ESCAPE '!')", null, false);
                } elseif ($val !== '') {
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