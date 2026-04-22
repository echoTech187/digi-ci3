<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QRISRecurring extends CI_Model {
    var $table = 'cashin_recurring_qris_mpm as crqm';
    var $column_order = array(null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name', 'crqm.c_merchantTransactionId', 'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status');
    var $column_search = array('crqm.c_merchantTransactionId', 'crqm.ref_merchantId', 'crqm.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('crqm.id' => 'desc');

    private function _apply_filters($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null)
    {
        if ($search_name) {
            $this->db->where('crqm.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $this->db->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
        }
        if ($search_date_to) {
            $this->db->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        }
        if ($search_submerchant) {
            $this->db->where('crqm.ref_subMerchantId', $search_submerchant);
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $only_ids = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        if ($only_ids) {
            $this->db->select("crqm.id");
        } else {
            $this->db->select("crqm.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        if (!$only_ids || $_POST['search']['value']) {
            $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
        }

        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_submerchant);

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

    public function get_datatables($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_submerchant, true);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("crqm.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
        
        $this->db->where_in('crqm.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(DISTINCT crqm.id) as total');
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_submerchant);

        // Lean joins based on active filters
        if (!empty($searchValue)) {
            $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
            
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $searchValue);
                } else {
                    $this->db->or_like($item, $searchValue);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }

        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_submerchant = $filters['submerchant'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_date_to, $search_submerchant);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_date_to, $search_submerchant) : $recordsTotal;

        // Use Datatables Library for final processing and JSON output
        return $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }
}
?>
