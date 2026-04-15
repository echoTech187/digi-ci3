<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ewallet extends CI_Model {
    var $table = 'cashin_payment_ewallet cpe';
    var $column_order = array(null, 'cpe.c_datetime', 's.c_name', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', 'cde.c_merchantTransactionId', null);
    var $column_search = array('cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cpe.id' => 'desc');

    private function _apply_filters($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $global_search = null)
    {
        if ($search_name) {
            $this->db->where('cpe.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpe.c_datetimePayment >=', $date_from);
            $this->db->where('cpe.c_datetimePayment <=', $date_to);
        }
        if ($search_date_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_date_settlement));
            $this->db->where('cpe.c_datetimeSettlement >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpe.c_datetimeSettlement <=', $formatted_date . ' 23:59:59');
        }
        if ($search_invoice_no) {
            $this->db->where('c.c_invoiceNo', $search_invoice_no);
        }

        if ($global_search) {
            $this->db->group_start();
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->like($item, $global_search);
                } else {
                    $this->db->or_like($item, $global_search);
                }
                $i++;
            }
            $this->db->group_end();
        }
    }

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        $this->db->select("cpe.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                           cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId');
        $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpe.ref_merchantId = m.id');
        $this->db->join('cashin_dynamic_ewallet cde', 'cde.ref_merchantId = cpe.ref_merchantId AND cde.id = cpe.ref_cashinDynamicEwalletId', 'left');

        $global_search = isset($_POST['search']['value']) ? $_POST['search']['value'] : null;
        $this->_apply_filters($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, $global_search);

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        $this->db->select('count(cpe.id) as total');
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId', 'left'); // Needed for InvoiceNo
        
        $global_search = isset($_POST['search']['value']) ? $_POST['search']['value'] : null;

        // Joins needed for filters/search
        if ($global_search) {
             $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
             $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
             $this->db->join('cashin_dynamic_ewallet cde', 'cde.ref_merchantId = cpe.ref_merchantId AND cde.id = cpe.ref_cashinDynamicEwalletId', 'left');
        }

        $this->_apply_filters($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, $global_search);
        
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        $this->db->select('count(cpe.id) as total');
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpe.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpe.c_datetimePayment >=', $date_from);
            $this->db->where('cpe.c_datetimePayment <=', $date_to);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }


    
        public function ewallet_detail($id)
        {
            $query = "SELECT a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, 
                        d.c_name AS name_submerchant, b.c_invoiceNo, 
                        a.c_type, a.ref_cashinChannelId, 
                        a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment,
                        a.c_isSettlementRealtime, a.c_datetimeSettlement,
                        e.c_merchantTransactionId AS Merchant_Transaction_Id
                        FROM cashin_payment_ewallet a   
                        JOIN cashin b ON b.id=a.ref_cashinId
                        JOIN merchant c ON a.ref_merchantId=c.id
                        JOIN submerchant d ON a.ref_subMerchantId=d.id
                        LEFT JOIN cashin_dynamic_ewallet e ON (e.ref_merchantId=a.ref_merchantId AND e.id=a.ref_cashinDynamicEwalletId) 
                        WHERE a.id ='$id'";

            // var_dump($query);
            // exit;

            return $this->db->query($query)->result_array();

        }

    public function insertEwalletDynamic($dataInsert2) {
        foreach ($dataInsert2 as $key => $value) {
            if (is_array($value)) {
                $dataInsert2[$key] = json_encode($value);
            }
        }

        $this->db->insert('cashin_dynamic_ewallet', $dataInsert2);
        return ($this->db->affected_rows() != 1) ? false : true;
    }
    
    public function updateEwalletDynamic($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('cashin_dynamic_ewallet', $data);
        return ($this->db->affected_rows() != 1) ? false : true;
    }
    public function get_detail_ewallet($idRequest2) {
        $query = "select cdv.*, m.c_name, m.id from cashin_dynamic_ewallet cdv join merchant m on m.id = cdv.ref_merchantId where cdv.id = $idRequest2";
        return $this->db->query($query)->result_array();
    }

    public function get_qris($limit, $start, $search_date_qris = null, $search_date_qris_to = null, $search_name_qris = null, $search_date_qris_settlement = null, $search_invoice_no = null)
    {

        $query = "SELECT 
                    merchant.c_name as name_merchant,
                    cashin_payment_qris_mpm.id, 
                    cashin_payment_qris_mpm.c_datetime, 
                    submerchant.c_name as name_submerchant, 
                    cashin.c_invoiceNo, 
                    cashin_payment_qris_mpm.c_type,
                    cashin_payment_qris_mpm.ref_merchantId, 
                    cashin_payment_qris_mpm.ref_subMerchantId, 
                    cashin_payment_qris_mpm.c_amount, 
                    cashin_payment_qris_mpm.c_mdr, 
                    cashin_payment_qris_mpm.c_fee,
                    cashin_payment_qris_mpm.c_datetimePayment, cashin_payment_qris_mpm.c_isSettlementRealtime, 
                    cashin_payment_qris_mpm.c_datetimeSettlement, cashin_payment_qris_mpm.c_isSettlementRealtimeExternal, 
                    cashin_payment_qris_mpm.c_feeExternal, cashin_payment_qris_mpm.c_datetimeSettlementExternal,
                    IF(cashin_payment_qris_mpm.c_type='Dynamic', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
                    FROM cashin_payment_qris_mpm 
                    JOIN cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
                    JOIN submerchant on cashin_payment_qris_mpm.ref_subMerchantId = submerchant.id 
                    JOIN merchant on cashin_payment_qris_mpm.ref_merchantId = merchant.id
                    LEFT JOIN cashin_dynamic_qris_mpm on (cashin_dynamic_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_dynamic_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinDynamicQrisMpmId)
                    LEFT JOIN cashin_recurring_qris_mpm on (cashin_recurring_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_recurring_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinRecurringQrisMpmId)";

        $query .= " WHERE 1=1 ";

        if (!empty($search_name_qris)) {
            $query .= " and cashin_payment_qris_mpm.ref_merchantId = '$search_name_qris'";
        }

        if (!empty($search_date_qris) && !empty($search_date_qris_to)) {
            // $search_date_qris = date('Y-m-d', strtotime($search_date_qris));
            $query .= " and cashin_payment_qris_mpm.c_datetime >= '$search_date_qris' AND cashin_payment_qris_mpm.c_datetime <= '$search_date_qris_to'";
        }

        if (!empty($search_date_qris_settlement)) {
            $formatted_date = date('Y-m-d', strtotime($search_date_qris_settlement));
            $query .= " and cashin_payment_qris_mpm.c_datetimeSettlement >= '$formatted_date 00:00:00' AND cashin_payment_qris_mpm.c_datetimeSettlement <= '$formatted_date 23:59:59'";
        }

        if (!empty($search_invoice_no)) {
            $query .= " and cashin.c_invoiceNo= '$search_invoice_no'";
        }

        $query .= " ORDER BY cashin_payment_qris_mpm.id DESC
                    LIMIT $start, $limit";

        // var_dump($query);
        // exit;

        return $this->db->query($query)->result();
    }

    public function count_qris($refMerchantId, $search_date_qris = null)
    {
        $this->db->from('cashin_payment_qris_mpm');
        $this->db->join('cashin', 'cashin.id = cashin_payment_qris_mpm.ref_cashinId');
        $this->db->join('merchant', 'merchant.id = cashin_payment_qris_mpm.ref_merchantId');
        $this->db->join('submerchant', 'submerchant.id = cashin_payment_qris_mpm.ref_subMerchantId');
        $this->db->where('cashin_payment_qris_mpm.ref_merchantId', $refMerchantId);

        if ($search_date_qris) {
            $this->db->where('cashin_payment_qris_mpm.c_datetime', $search_date_qris);
        }

        return $this->db->count_all_results();
    }

    public function get_merchant()
    {
        $query = "select * from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_summary($date_from = null, $date_to = null, $refMerchantId = null, $search_settlement = null, $search_invoice = null, $global_search = null) {
        $today = date('Y-m-d');
        
        // Hybrid logic for standard view
        $use_hybrid = empty($search_settlement) && empty($search_invoice) && empty($global_search);
        
        if ($use_hybrid) {
            // 1. Get History
            $this->db->select('SUM(total_qty) as qty, SUM(total_amount) as amount, SUM(total_fee) as fee, SUM(total_fee_ext) as fee_external');
            $this->db->from('tr_summary_daily');
            $this->db->where('transaction_type', 'EWALLET');
            
            if ($date_from) $this->db->where('summary_date >=', $date_from);
            if ($date_to) $this->db->where('summary_date <', $today);
            else $this->db->where('summary_date <', $today);

            if ($refMerchantId) $this->db->where('ref_merchantId', $refMerchantId);
            
            $history = $this->db->get()->row();

            // 2. Get Live
            if (!$date_to || $date_to >= $today) {
                $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
                $this->db->from('cashin_payment_ewallet');
                $this->db->where('c_datetimePayment >=', $today . ' 00:00:00');
                if ($refMerchantId) $this->db->where('ref_merchantId', $refMerchantId);
                
                $live = $this->db->get()->row();
            } else {
                $live = (object)['qty' => 0, 'amount' => 0, 'fee' => 0, 'fee_external' => 0];
            }

            return [[
                'qty' => ($history->qty ?? 0) + ($live->qty ?? 0),
                'amount' => ($history->amount ?? 0) + ($live->amount ?? 0),
                'fee' => ($history->fee ?? 0) + ($live->fee ?? 0),
                'fee_external' => ($history->fee_external ?? 0) + ($live->fee_external ?? 0),
            ]];
        }

        // Fallback for complex filters
        $this->db->select('COUNT(cpe.id) as qty, SUM(cpe.c_amount) as amount, SUM(cpe.c_fee) as fee, SUM(cpe.c_feeExternal) as fee_external');
        $this->db->from('cashin_payment_ewallet cpe');
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId', 'left');

        if ($global_search) {
             $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
             $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
             $this->db->join('cashin_dynamic_ewallet cde', 'cde.ref_merchantId = cpe.ref_merchantId AND cde.id = cpe.ref_cashinDynamicEwalletId', 'left');
        }

        $this->_apply_filters($refMerchantId, $date_from, $date_to, $search_settlement, $search_invoice, $global_search);
        return $this->db->get()->result_array();
    }

    public function monthly_ewallet() {
        $year = date('Y');
        // Optimized: Avoid MONTH() index-killer
        $query = "SELECT MONTH(c_datetimePayment) AS month, SUM(c_amount) AS amount
                  FROM cashin_payment_ewallet 
                  WHERE c_datetimePayment >= '$year-01-01 00:00:00' AND c_datetimePayment <= '$year-12-31 23:59:59'
                  GROUP BY MONTH(c_datetimePayment)
                  ORDER BY month";
        return $this->db->query($query)->result_array();
    }
}
?>