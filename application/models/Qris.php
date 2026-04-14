<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qris extends CI_Model {

    // DataTables variables
    var $table = 'cashin_payment_qris_mpm cpq';
    var $column_order = array(null, 'cpq.c_datetime', 'm.c_name', 's.c_name', 'c.c_invoiceNo', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_mdr', 'cpq.c_fee', 'epq.c_issuerRrn', 'cpq.c_isSettlementRealtime', 'cpq.c_datetimeSettlement', null, null); 
    var $column_search = array('cpq.id', 'm.c_name', 's.c_name', 'c.c_invoiceNo', 'cdq.c_merchantTransactionId', 'crq.c_merchantTransactionId', 'epq.c_issuerRrn');
    var $order = array('cpq.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $this->db->select("cpq.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, epq.c_issuerRrn,
                           IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpq.ref_cashinId');
        $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
        $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.ref_subMerchantId = cpq.ref_subMerchantId AND cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
        $this->db->join('cashin_recurring_qris_mpm crq', 'crq.ref_subMerchantId = cpq.ref_subMerchantId AND crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        $this->db->join('external_paydgn_qris_mpm_callback epq', 'epq.ref_subMerchantId = cpq.ref_subMerchantId AND epq.ref_cashinPaymentQrisMpmId = cpq.id', 'left');

        if ($search_name) {
            $this->db->where('cpq.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        // if ($search_settlement) {
        //     $this->db->where('DATE(cpq.c_datetimeSettlement)', $search_settlement);
        // }
        if ($search_rrn) {
            $this->db->where('epq.c_issuerRrn', $search_rrn);
        }
        // if ($search_invoice) {
        //     $this->db->where('c.c_invoiceNo', $search_invoice);
        // }
        if ($search_transid) {
            $this->db->group_start();
            $this->db->where('cdq.c_merchantTransactionId', $search_transid);
            $this->db->or_where('crq.c_merchantTransactionId', $search_transid);
            $this->db->group_end();
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

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        // Optimized: Only join what is necessary for filtering
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpq.ref_cashinId'); // Needed for InvoiceNo
        
        if ($search_name) $this->db->where('cpq.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        if ($search_rrn) {
            $this->db->join('external_paydgn_qris_mpm_callback epq', 'epq.ref_subMerchantId = cpq.ref_subMerchantId AND epq.ref_cashinPaymentQrisMpmId = cpq.id');
            $this->db->where('epq.c_issuerRrn', $search_rrn);
        }
        if ($search_transid) {
            $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.ref_subMerchantId = cpq.ref_subMerchantId AND cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
            $this->db->join('cashin_recurring_qris_mpm crq', 'crq.ref_subMerchantId = cpq.ref_subMerchantId AND crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
            $this->db->group_start();
            $this->db->where('cdq.c_merchantTransactionId', $search_transid);
            $this->db->or_where('crq.c_merchantTransactionId', $search_transid);
            $this->db->group_end();
        }

        // Global Search requires more joins
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
            $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
            
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }

        return $this->db->count_all_results();
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        // Optimized: No joins needed for total records filtered only by merchant/date
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpq.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        return $this->db->count_all_results();
    }


    public function get_qris($limit, $start, $search_date_qris = null, $search_date_qris_to = null, $search_name_qris = null, $search_date_qris_settlement = null, $search_rrn = null, $search_transactionid_ht = null)
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
        external_paydgn_qris_mpm_callback.c_issuerRrn,
        IF(cashin_payment_qris_mpm.c_type='Dynamic', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
        FROM cashin_payment_qris_mpm 
        JOIN cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
        JOIN submerchant on cashin_payment_qris_mpm.ref_subMerchantId = submerchant.id 
        JOIN merchant on cashin_payment_qris_mpm.ref_merchantId = merchant.id
        LEFT JOIN cashin_dynamic_qris_mpm on (cashin_dynamic_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_dynamic_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinDynamicQrisMpmId)
        LEFT JOIN cashin_recurring_qris_mpm on (cashin_recurring_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_recurring_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinRecurringQrisMpmId)
        LEFT JOIN external_paydgn_qris_mpm_callback 
			ON external_paydgn_qris_mpm_callback.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId
			AND external_paydgn_qris_mpm_callback.ref_cashinPaymentQrisMpmId = cashin_payment_qris_mpm.id";

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

        if (!empty($search_transactionid_ht)) {
            $query .= " and cashin_dynamic_qris_mpm.c_merchantTransactionId= '$search_transactionid_ht'
                        and cashin_dynamic_qris_mpm.c_status = 'Paid'";
        }

        if (!empty($search_rrn)) {
            $query .= " AND external_paydgn_qris_mpm_callback.c_issuerRrn = '$search_rrn'";
        }

        $query .= " ORDER BY cashin_payment_qris_mpm.id DESC
                    LIMIT $start, $limit";

        // var_dump($query);
        // exit;

        return $this->db->query($query)->result();
    }

    public function get_merchant_detail($id)
    {
        $query = "SELECT c_name FROM merchant WHERE id = '$id'";

        // var_dump($query);
        // exit;

        return $this->db->query($query)->result_array();
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

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashin_payment_qris_mpm a
        WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }

        // echo $query;
        // exit;

        return $this->db->query($query)->result_array();
    }

    public function monthly_qris() {
        $year = date('Y');
        $query = "SELECT MONTH(c_datetime) AS month, SUM(c_amount) AS amount
                  FROM cashin_payment_qris_mpm 
                  WHERE c_datetime >= '$year-01-01 00:00:00' AND c_datetime <= '$year-12-31 23:59:59'
                  GROUP BY MONTH(c_datetime)
                  ORDER BY month";
        return $this->db->query($query)->result_array();
    }

    // public function qris_detail($id)
    // {
    //     $query = "SELECT cashin_payment_qris_mpm.*, cashin.*, cashin_payment_qris_mpm.*, m.id as id_merchant, m.c_name AS name_merchant, s.id as id_submerchant, s.c_name AS name_submerchant, 
    //     cashin_dynamic_qris_mpm.*, cashin_recurring_qris_mpm.*,
    //     IF(cashin_payment_qris_mpm.c_type='Dynamic ', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
    //     FROM cashin_payment_qris_mpm 
    //     join cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
    //     JOIN merchant m ON cashin_payment_qris_mpm.ref_merchantId = m.id
    //     join submerchant s on cashin_payment_qris_mpm.ref_subMerchantId = s.id 
    //     left join cashin_dynamic_qris_mpm on cashin_dynamic_qris_mpm.ref_subMerchantId = s.id 
    //     left join cashin_recurring_qris_mpm on cashin_recurring_qris_mpm.ref_subMerchantId = s.id 
    //     where cashin_payment_qris_mpm.id = $id group by cashin_payment_qris_mpm.id";

    //     return $this->db->query($query)->result_array();
    // }

    public function qris_detail($id)
    {
        $query = "SELECT a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, 
                    d.c_name AS name_submerchant, b.c_invoiceNo, 
                    a.c_type, a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment, 
                    a.c_isSettlementRealtime, a.c_datetimeSettlement, 
                    IF(a.c_type='Dynamic', e.c_merchantTransactionId, f.c_merchantTransactionId) AS c_merchantTransactionId,
                    a.ref_cashinExternalId, a.c_isSettlementRealtimeExternal, a.c_datetimeSettlementExternal,
                    a.c_mdrExternal, a.c_feeExternal
                    FROM cashin_payment_qris_mpm a
                    JOIN cashin b ON b.id=a.ref_cashinId
                    JOIN merchant c ON a.ref_merchantId=c.id
                    JOIN submerchant d ON a.ref_subMerchantId=d.id
                    LEFT JOIN cashin_dynamic_qris_mpm e ON (e.ref_merchantId=a.ref_merchantId AND e.ref_cashinPaymentQrisMpmId=a.id) 
                    LEFT JOIN cashin_recurring_qris_mpm f ON (f.ref_merchantId=a.ref_merchantId AND e.ref_cashinPaymentQrisMpmId=a.id)
                    WHERE a.id ='$id'";

        return $this->db->query($query)->result_array();
    }
    
    public function get_merchant(){
        $query = "select * from merchant ";
        return $this->db->query($query)->result();
    }
}
?>