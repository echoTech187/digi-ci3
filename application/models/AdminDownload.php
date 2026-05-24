<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDownload extends CI_Model {

    var $table = 'admin_download ad';
    var $column_order = array(null, 'ad.c_datetime', 'ad.c_type', 'ad.c_filename', 'ad.c_status', 'ad.c_remark');
    var $column_search = array('ad.c_filename', 'ad.c_remark');
    var $order = array('ad.id' => 'desc');
    private static $cached_total = null;

    private function _get_datatables_query($search_date = null, $include_order = true)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        $this->db->select('ad.id, ad.c_datetime, ad.c_type, ad.c_filename, ad.c_status, ad.c_remark');
        $this->db->from($this->table);

        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $this->db->where('ad.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('ad.c_datetime <=', $formatted_date . ' 23:59:59');
        }

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

        if ($include_order) {
            if (isset($_POST['order'])) {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
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
        $this->_get_datatables_query($search_date, false);
        return $this->db->count_all_results();
    }

    public function count_all_dt($search_date = null)
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