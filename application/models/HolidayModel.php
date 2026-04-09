<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HolidayModel extends CI_Model {

  public function get_holidays(){
    $query = "SELECT * FROM holiday ORDER BY c_date DESC";
    return $this->db->query($query)->result();
  }

  public function add_holiday($data) {
    if ($this->db->insert('holiday', $data)) {
        return true;
    } else {
        return $this->db->error(); // Returns array with 'code' and 'message'
    }
  }
  
  public function update_holiday($date, $data) {
    $this->db->where('c_date', $date);
    if ($this->db->update('holiday', $data)) {
        return true;
    } else {
        return $this->db->error(); // Returns array with 'code' and 'message'
    }
  }
}