<?php defined('BASEPATH') or exit('No direct script access allowed');

class SubMerchant extends CI_Model
{
    private function _get_datatables_query($id)
    {
        $this->db->from('merchant');
        $this->db->where('parent_merchant_id', $id);
        $this->db->where('c_merchantLevel >', 0);

        if (isset($_POST['search']['value']) && $_POST['search']['value'] != "") {
            $search = $_POST['search']['value'];
            $this->db->group_start();
            $this->db->like('c_name', $search);
            $this->db->or_like('c_email', $search);
            $this->db->or_like('id', $search);
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $column_order = [null, 'c_name', 'c_email', 'c_gvconnectBusinessId', 'c_status'];
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id', 'DESC');
        }
    }

    public function get_datatables($id)
    {
        $this->_get_datatables_query($id);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($id)
    {
        $is_filtered = (isset($_POST['search']['value']) && !empty($_POST['search']['value']));
        if (!$is_filtered) {
            return $this->count_all_dt($id);
        }

        $this->db->select('count(merchant.id) as total');
        $this->_get_datatables_query($id);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($id)
    {
        $this->db->select('count(id) as total');
        $this->db->from('merchant');
        $this->db->where('parent_merchant_id', $id);
        $this->db->where('c_merchantLevel >', 0);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function create_submerchant($data)
    {
        // This now creates a Level 1+ Merchant
        $this->db->insert('merchant', $data);
        return true;
    }

    public function update_submerchant($id, $data)
    {
        return $this->db->where('id', $id)->update('merchant', $data);
    }
}