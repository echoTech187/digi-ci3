<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceLogModel extends CI_Model {

    var $table = 'merchant_balance_hold_log mbhl';
    var $column_order = array(null, 'mbhl.created_at', 'mbhl.merchant_id', 'mbhl.merchant_name', 'mbhl.add_to_available');
    var $column_search = array('mbhl.merchant_id', 'mbhl.merchant_name');
    var $order = array('mbhl.created_at' => 'desc');

    private function _get_datatables_query()
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        $this->db->select('mbhl.id, mbhl.created_at, mbhl.merchant_id, mbhl.merchant_name, mbhl.add_to_available');
        $this->db->from($this->table);

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
        $is_filtered = (isset($_POST['search']['value']) && !empty($_POST['search']['value']));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(mbhl.id) as total');
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt()
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
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
        $list = $this->get_datatables();
        
        $data = [];
        $no = intval($this->input->post('start'));
        foreach ($list as $items) {
            $no++;
            $row = (array)$items;
            $row['no'] = $no;
            $data[] = $row;
        }

        $recordsTotal = $this->count_all_dt();
        
        // Consistency: Use approx count if no filters, exact if filtered
        $is_filtered = (!empty($this->input->post('search')['value']));
        $recordsFiltered = $is_filtered ? $this->count_filtered() : $recordsTotal;

        $output = [
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}
?>