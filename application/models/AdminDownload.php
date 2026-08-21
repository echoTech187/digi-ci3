<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDownload extends CI_Model {

    var $table = 'admin_download ad';
    var $column_order = array(null, 'ad.c_datetime', 'ad.c_type', 'ad.c_filename', 'ad.c_status', 'ad.c_remark');
    var $column_search = array('ad.c_filename', 'ad.c_remark');
    var $order = array('ad.id' => 'desc');


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
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_type = $filters['type'] ?? null;
        $search_status = $filters['status'] ?? null;

        $dt = $this->datatables->of('admin_download ad')
            ->select('ad.id, ad.c_datetime, ad.c_type, ad.c_filename, ad.c_status, ad.c_remark');

        if ($search_date && $search_date_to) {
            $dt->where('ad.c_datetime >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
            $dt->where('ad.c_datetime <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $dt->where('ad.c_datetime >=', $formatted_date . ' 00:00:00');
            $dt->where('ad.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if ($search_type) {
            $dt->where('ad.c_type', $search_type);
        }

        if ($search_status) {
            $dt->where('ad.c_status', $search_status);
        }

        return $dt->set_column_order([null, 'ad.c_datetime', 'ad.c_type', 'ad.c_filename', 'ad.c_status', 'ad.c_remark'])
            ->set_column_search(['ad.c_filename', 'ad.c_remark'])
            ->set_default_order(['ad.id' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>