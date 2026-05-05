<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceLogModel extends CI_Model {

    var $table = 'merchant_balance_hold_log mbhl';
    var $column_order = array(null, 'mbhl.created_at', 'mbhl.merchant_id', 'mbhl.merchant_name', 'mbhl.add_to_available');
    var $column_search = array('mbhl.merchant_id', 'mbhl.merchant_name');
    var $order = array('mbhl.created_at' => 'desc');
    private static $cached_total = null;

    private function _get_datatables_query($include_order = true)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        $this->db->select('mbhl.id, mbhl.created_at, mbhl.merchant_id, mbhl.merchant_name, mbhl.add_to_available');
        $this->db->from($this->table);

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

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query(false); // pass false to exclude order_by
        return $this->db->count_all_results();
    }

    public function count_all_dt()
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

    public function get_balance_log() {
        $query = "SELECT created_at, merchant_id, merchant_name, add_to_available
            FROM merchant_balance_hold_log
            ORDER BY created_at DESC";

        return $this->db->query($query)->result();
    }

    public function get_summary() {
        $this->db->select("
            COUNT(*) as total_logs,
            COUNT(DISTINCT merchant_id) as total_merchants,
            SUM(add_to_available) as total_settled,
            AVG(add_to_available) as avg_settled
        ");
        $this->db->from($this->table);
        return $this->db->get()->row();
    }
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $dt = $this->datatables->of('merchant_balance_hold_log mbhl')
            ->select('mbhl.id, mbhl.created_at, mbhl.merchant_id, mbhl.merchant_name, mbhl.add_to_available');

        return $dt->set_column_order([null, 'mbhl.created_at', 'mbhl.merchant_id', 'mbhl.merchant_name', 'mbhl.add_to_available'])
            ->set_column_search(['mbhl.merchant_id', 'mbhl.merchant_name'])
            ->set_default_order(['mbhl.created_at' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>