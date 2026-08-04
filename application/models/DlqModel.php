<?php defined('BASEPATH') or exit('No direct script access allowed');

class DlqModel extends CI_Model {

    public function getDlqDataTable($custom_search = null, $merchant_id = null, $start_date = null, $end_date = null) {
        $this->load->library('datatables');

        // 1. Optimized Total Count (No JOINs needed)
        $this->db->from('log_failed_notification_dlq');
        if (!empty($merchant_id)) $this->db->where('ref_merchantId', $merchant_id);
        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        $this->db->where('c_status', 1);
        $recordsTotal = $this->db->count_all_results();

        // 2. Setup DataTables
        $dt = $this->datatables->of('log_failed_notification_dlq dm')
            ->select('dm.id, dm.created_at, dm.type, dm.ref_transactionId, m.c_name as merchant_name, dm.ref_merchantId', false)
            ->join('merchant m', 'm.id = dm.ref_merchantId', 'left')
            ->set_column_order([null, 'dm.created_at', 'm.c_name', 'dm.type', 'dm.ref_transactionId', null])
            ->set_column_search(['m.c_name', 'dm.type', 'dm.ref_transactionId'])
            ->set_default_order(['dm.created_at' => 'desc'])
            ->set_recordsTotal($recordsTotal);
        $dt->where('dm.c_status', 1);
        
        // Apply custom filters
        if (!empty($merchant_id)) $dt->where('dm.ref_merchantId', $merchant_id);
        if (!empty($start_date) && !empty($end_date)) {
            $dt->where('dm.created_at >=', $start_date . ' 00:00:00');
            $dt->where('dm.created_at <=', $end_date . ' 23:59:59');
        }
        
        // Apply custom search
        if ($custom_search) {
            $search = $this->db->escape_like_str($custom_search);
            $dt->where("(m.c_name LIKE '%$search%' ESCAPE '!' OR dm.ref_transactionId LIKE '%$search%' ESCAPE '!')", null, false);
        }

        // 3. Optimized Filtered Count (Only if search is active)
        $dt_search = $this->input->post('search');
        $global_search = isset($dt_search['value']) ? $dt_search['value'] : '';
        
        if (empty($custom_search) && empty($global_search)) {
            $dt->set_recordsFiltered($recordsTotal);
        }

        return $dt->addColumn('no', function ($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }

    public function get_dlq_by_id($id) {
        return $this->db->get_where('log_failed_notification_dlq', ['id' => $id])->row_array();
    }
}
