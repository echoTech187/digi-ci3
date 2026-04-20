<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qris extends CI_Model {

    // DataTables variables
    var $table = 'cashin_payment_qris_mpm cpq';
    var $column_order = array(null, 'cpq.c_datetime', 'm.c_name', 's.c_name', 'c.c_invoiceNo', 'Merchant_Transaction_Id', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_mdr', 'cpq.c_fee', 'epq.c_issuerRrn', 'cpq.c_isSettlementRealtime', 'cpq.c_datetimeSettlement', null); 
    var $column_search = array('cpq.id', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'crq.c_merchantTransactionId', 'epq.c_issuerRrn');
    var $order = array('cpq.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null, $only_ids = false, $count_only = false)
    {
        $this->db->query("SET SESSION max_execution_time = 10000");
        if ($count_only) {
            $this->db->select("count(cpq.id) as total");
        } else if ($only_ids) {
            $this->db->select("cpq.id");
        } else {
            $this->db->select("cpq.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, epq.c_issuerRrn,
                               IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $isInvoiceSearch = (preg_match('/^QRIS|^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Optimization: Use joins only if global search is active, or we need full data/sorting by externally joined columns
        $isExternalSort = !empty($sort_col) && !preg_match('/^cpq\./', $sort_col) && $sort_col != 'Merchant_Transaction_Id';
        $needFullJoins = (!$only_ids && !$count_only) || $searchValue || $isExternalSort;

        // Join cashin only if searching for invoice via global search, sorting by it, or getting full data
        if ($needFullJoins || $isInvoiceSearch) {
            $this->db->join('cashin c', 'c.id = cpq.ref_cashinId');
        }
        
        // Join merchant and submerchant only if needed for global search or sorting or full data
        if ($needFullJoins || $search_name) {
            $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
            $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
        }

        // Transactions ID joins (Only if full data or global search)
        if ($needFullJoins) {
            $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
            $this->db->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        }

        // Callback/RRN joins (Only if full data or global search)
        if ($needFullJoins) {
            $this->db->join('external_paydgn_qris_mpm_callback epq', 'epq.ref_cashinPaymentQrisMpmId = cpq.id', 'left');
        }

        if ($search_name) {
            $this->db->where('cpq.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        if ($search_rrn) {
            // Using subquery for RRN filter to use index on callback table first (avoid 23M row scan)
            $this->db->where("cpq.id IN (SELECT ref_cashinPaymentQrisMpmId FROM external_paydgn_qris_mpm_callback WHERE c_issuerRrn = '".$this->db->escape_str($search_rrn)."')", NULL, FALSE);
        }
        if ($search_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_settlement));
            $this->db->where('cpq.c_datetimeSettlement >=', $formatted_date . " 00:00:00");
            $this->db->where('cpq.c_datetimeSettlement <=', $formatted_date . " 23:59:59");
        }
        if ($search_transid) {
            // High-performance filter using subqueries for Trans ID (essential for 23M rows)
            $this->db->group_start();
            $this->db->where("cpq.ref_cashinDynamicQrisMpmId IN (SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId = '".$this->db->escape_str($search_transid)."')", NULL, FALSE);
            $this->db->or_where("cpq.ref_cashinRecurringQrisMpmId IN (SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId = '".$this->db->escape_str($search_transid)."')", NULL, FALSE);
            $this->db->group_end();
        }
        if ($search_invoice) {
            // Fast invoice lookup via subquery
            $this->db->where("cpq.ref_cashinId IN (SELECT id FROM cashin WHERE c_invoiceNo = '".$this->db->escape_str($search_invoice)."')", NULL, FALSE);
        }

        if ($searchValue) {
            $isTechnicalSearch = (preg_match('/^(QRIS|INV)/i', $searchValue));
            $i = 0;
            foreach ($this->column_search as $item) {
                // EMERGENCY OPTIMIZATION: 
                // 1. Skip searching name columns if search value is a technical ID prefix to prevent 23M row scan
                if ($isTechnicalSearch && in_array($item, ['m.c_name', 's.c_name'])) {
                    continue;
                }

                // 2. Minimum 3 characters for ID/Invoice, 5 for text searches to prevent 23M row scan
                if (strlen($searchValue) < 3) {
                    continue;
                }
                
                // 3. Skip searching heavy text columns if search value is too short
                if (in_array($item, ['m.c_name', 's.c_name']) && strlen($searchValue) < 5) {
                    continue;
                }

                // 4. Skip searching invoice column if search value doesn't look like an invoice
                if ($item == 'c.c_invoiceNo' && !$isInvoiceSearch) {
                    continue;
                }

                if ($i === 0) {
                    $this->db->group_start();
                    // 5. Prefix search only (no leading %) to allow index usage
                    $this->db->like($item, $searchValue, 'after');
                } else {
                    $this->db->or_like($item, $searchValue, 'after');
                }
                $i++;
            }
            if ($i > 0) $this->db->group_end();
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_idx = $_POST['order']['0']['column'];
                $sort_col = $this->column_order[$sort_idx];
                
                if ($sort_col == 'Merchant_Transaction_Id') {
                    $this->db->order_by("IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId)", $_POST['order']['0']['dir']);
                } else if ($sort_col) {
                    $this->db->order_by($sort_col, $_POST['order']['0']['dir']);
                }
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        // STEP 1: Get only IDs matching filters/pagination (Fast)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        if (!is_object($query)) return array();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full records for only these specific IDs
        $this->db->select("cpq.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, epq.c_issuerRrn,
                           IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpq.ref_cashinId');
        $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
        $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.ref_subMerchantId = cpq.ref_subMerchantId AND cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
        $this->db->join('cashin_recurring_qris_mpm crq', 'crq.ref_subMerchantId = cpq.ref_subMerchantId AND crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        $this->db->join('external_paydgn_qris_mpm_callback epq', 'epq.ref_subMerchantId = cpq.ref_subMerchantId AND epq.ref_cashinPaymentQrisMpmId = cpq.id', 'left');
        
        $this->db->where_in('cpq.id', $ids);
        
        // Order must be re-applied to match sort from STEP 1
        if (isset($_POST['order'])) {
            $sort_idx = $_POST['order']['0']['column'];
            $sort_col = $this->column_order[$sort_idx];
            if ($sort_col == 'Merchant_Transaction_Id') {
                $this->db->order_by("IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId)", $_POST['order']['0']['dir']);
            } else if ($sort_col) {
                $this->db->order_by($sort_col, $_POST['order']['0']['dir']);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return is_object($query) ? $query->result() : array();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
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
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }
}
?>