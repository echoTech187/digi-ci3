<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceLogModel extends CI_Model {

    var $table = 'merchant_balance_hold_log mbhl';
    var $column_order = array(null, 'mbhl.created_at', 'mbhl.merchant_id', 'mbhl.merchant_name', 'mbhl.add_to_available');
    var $column_search = array('mbhl.merchant_name', 'mbhl.add_to_available');
    var $order = array('mbhl.created_at' => 'desc');


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
    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $dt = $this->datatables->of('merchant_balance_hold_log mbhl')
            ->select('mbhl.id, mbhl.created_at, mbhl.merchant_id, mbhl.merchant_name, mbhl.add_to_available');

        if (!empty($filters['merchant'])) {
            $dt->where('mbhl.merchant_id', $filters['merchant']);
        }
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $dt->where('mbhl.created_at >=', $filters['date_from'] . ' 00:00:00');
            $dt->where('mbhl.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $dt->set_column_order([null, 'mbhl.created_at', 'mbhl.merchant_id', 'mbhl.merchant_name', 'mbhl.add_to_available'])
            ->set_column_search(['mbhl.merchant_name', 'mbhl.add_to_available'])
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