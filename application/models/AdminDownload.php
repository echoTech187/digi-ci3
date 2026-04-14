<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDownload extends CI_Model {

    var $table = 'admin_download ad';
    var $column_order = array(null, 'ad.c_datetime', 'ad.c_type', 'ad.c_filename', 'ad.c_status', 'ad.c_remark');
    var $column_search = array('ad.c_type', 'ad.c_filename', 'ad.c_status', 'ad.c_remark');
    var $order = array('ad.id' => 'desc');

    private function _get_datatables_query($search_date = null)
    {
        $this->db->from($this->table);

        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $this->db->where('ad.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('ad.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
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

    public function get_datatables($search_date = null)
    {
        $this->_get_datatables_query($search_date);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_date = null)
    {
        $this->_get_datatables_query($search_date);
        return $this->db->count_all_results();
    }

    public function count_all_dt($search_date = null)
    {
        $this->db->from($this->table);
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $this->db->where('c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('c_datetime <=', $formatted_date . ' 23:59:59');
        }
        return $this->db->count_all_results();
    }

    public function get_download($limit, $start, $search_date = null) {
    
        $query = "SELECT admin_download.c_datetime, 
                        admin_download.c_type, 
                        admin_download.c_filename, 
                        admin_download.c_status, 
                        admin_download.c_remark 
                        FROM admin_download";
       
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $query .= " where admin_download.c_datetime >= '$formatted_date 00:00:00' AND admin_download.c_datetime <= '$formatted_date 23:59:59'";
        }
    
        $query .= " ORDER BY id DESC
                LIMIT $start, $limit";
    
        return $this->db->query($query)->result();
    }
}
?>