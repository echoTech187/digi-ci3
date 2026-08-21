<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qris extends CI_Model {

    // DataTables variables
    var $table = 'cashin_payment_qris_mpm cpq';
    var $column_order = array(null, 'cpq.c_datetime', 'm.c_name', 's.c_name', 'Merchant_Transaction_Id', 'c.c_invoiceNo', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_mdr', 'cpq.c_fee', 'cpq.c_issuerRrn', 'cpq.c_isSettlementRealtime', 'cpq.c_datetimeSettlement', null); 
    var $column_search = array('cpq.id', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'crq.c_merchantTransactionId', 'cpq.c_issuerRrn');
    var $order = array('cpq.id' => 'desc');
    private static $cached_total = null;




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
        cashin_payment_qris_mpm.c_datetimePayment,        cashin_payment_qris_mpm.c_isSettlementRealtime, 
        cashin_payment_qris_mpm.c_datetimeSettlement, cashin_payment_qris_mpm.c_isSettlementRealtimeExternal, 
        cashin_payment_qris_mpm.c_feeExternal, cashin_payment_qris_mpm.c_datetimeSettlementExternal,
        IF(cashin_payment_qris_mpm.c_type='Dynamic', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
        FROM cashin_payment_qris_mpm 
        JOIN cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
        JOIN submerchant on cashin_payment_qris_mpm.ref_subMerchantId = submerchant.id 
        JOIN merchant on cashin_payment_qris_mpm.ref_merchantId = merchant.id
        LEFT JOIN cashin_dynamic_qris_mpm on cashin_dynamic_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinDynamicQrisMpmId
        LEFT JOIN cashin_recurring_qris_mpm on cashin_recurring_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinRecurringQrisMpmId";

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
            $matching_ids = $this->_get_ids_by_rrn($search_rrn);
            if (!empty($matching_ids)) {
                $query .= " AND cashin_payment_qris_mpm.id IN (" . implode(',', $matching_ids) . ")";
            } else {
                $query .= " AND 1=0";
            }
        }

        $query .= " ORDER BY cashin_payment_qris_mpm.id DESC
                    LIMIT $start, $limit";

        $res = $this->db->query($query)->result();
        if (!empty($res)) {
            $res = $this->_enrich_with_rrns($res);
        }
        return $res;
    }

    public function get_merchant_detail($id)
    {
        $query = "SELECT c_name FROM merchant WHERE id = '$id'";
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
        $query = "SELECT a.id, a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, 
                    d.c_name AS name_submerchant, b.c_invoiceNo, 
                    a.c_type, a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment, 
                    a.c_isSettlementRealtime, a.c_datetimeSettlement, 
                    IF(a.c_type='Dynamic', e.c_merchantTransactionId, f.c_merchantTransactionId) AS c_merchantTransactionId,
                    a.ref_cashinExternalId, a.c_isSettlementRealtimeExternal, a.c_datetimeSettlementExternal,
                    a.c_mdrExternal, a.c_feeExternal,
                    e.ref_cashinExternalLogQrisMpmIdCreate AS dynamic_create_log_id,
                    f.ref_cashinExternalLogQrisMpmIdCreate AS recurring_create_log_id,
                    a.ref_cashinDynamicQrisMpmId, a.ref_cashinRecurringQrisMpmId
                    FROM cashin_payment_qris_mpm a
                    LEFT JOIN cashin b ON b.id=a.ref_cashinId
                    LEFT JOIN merchant c ON a.ref_merchantId=c.id
                    LEFT JOIN submerchant d ON a.ref_subMerchantId=d.id
                    LEFT JOIN cashin_dynamic_qris_mpm e ON e.id = a.ref_cashinDynamicQrisMpmId
                    LEFT JOIN cashin_recurring_qris_mpm f ON f.id = a.ref_cashinRecurringQrisMpmId
                    WHERE a.id ='$id'";

        $res = $this->db->query($query)->result_array();
        if (!empty($res)) {
            $res = $this->_enrich_with_rrns($res);
        }
        return $res;
    }

    public function get_external_payment_log($id, $ref_cashinExternalId)
    {
        $table = '';
        if ($ref_cashinExternalId == 'paydgn') {
            $table = 'external_paydgn_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'gvconnect') {
            $table = 'external_gvconnect_snap_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'inacash') {
            $table = 'external_inacash_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'paylabs' || $ref_cashinExternalId == 'paylabs2') {
            $table = 'external_paylabs_qris_mpm_callback_payment';
        } elseif ($ref_cashinExternalId == 'quantum') {
            $table = 'external_quantum_qris_mpm_calback_payment';
        } elseif ($ref_cashinExternalId == 'stm') {
            $table = 'external_stm_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'yukk') {
            $table = 'external_yukk_qris_mpm_callback';
        }

        if ($table != '') {
            $q = $this->db->query("SELECT * FROM $table WHERE ref_cashinPaymentQrisMpmId = '$id' LIMIT 1");
            if ($q) {
                return $q->row_array();
            }
        }
        return null;
    }
    
    public function get_merchant(){
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    /**
     * Standardized DataTables handler for QRIS list.
     * Utilizes the optimized two-step Pre-Lookup query logic with Datatables library.
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_settlement = $filters['settlement'] ?? null;
        $search_rrn = $filters['rrn'] ?? null;
        $search_invoice = $filters['invoice'] ?? null;
        $search_transid = $filters['transid'] ?? null;

        $dt = $this->datatables->of('cashin_payment_qris_mpm cpq')
            ->select("cpq.id, cpq.c_datetime, cpq.c_type, cpq.c_amount, cpq.c_mdr, cpq.c_fee, cpq.c_isSettlementRealtime, cpq.c_datetimeSettlement, cpq.ref_merchantId, cpq.ref_subMerchantId, cpq.ref_cashinId, m.c_name as merchant_name, s.c_name as sub_account_name, c.c_invoiceNo, IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id, IF(cpq.c_type='Dynamic', cdq.ref_cashinExternalId, crq.ref_cashinExternalId) AS ref_cashinExternalId, IF(cpq.c_type='Dynamic', cdq.ref_cashinExternalLogQrisMpmIdCreate, crq.ref_cashinExternalLogQrisMpmIdCreate) AS ref_cashinExternalLogQrisMpmIdCreate", false)
            ->join('merchant m', 'm.id = cpq.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cpq.ref_subMerchantId', 'left')
            ->join('cashin c', 'c.id = cpq.ref_cashinId', 'left')
            ->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left')
            ->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left')
            ->set_column_order([null, 'cpq.c_datetime', 's.c_name', 'Merchant_Transaction_Id', 'c.c_invoiceNo', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_fee', 'cpq.c_datetimeSettlement', null])
            ->set_column_search(['cpq.id', 'c.c_invoiceNo', 's.c_name', 'm.c_name'])
            ->set_default_order(['cpq.id' => 'desc']);

        if ($search_name) $dt->where('cpq.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $dt->where('cpq.c_datetime >=', date('Y-m-d', strtotime($date_from)) . ' 00:00:00')
               ->where('cpq.c_datetime <=', date('Y-m-d', strtotime($date_to)) . ' 23:59:59');
        }
        if ($search_settlement) {
            $dt->where('cpq.c_datetimeSettlement >=', date('Y-m-d', strtotime($search_settlement)) . ' 00:00:00')
               ->where('cpq.c_datetimeSettlement <=', date('Y-m-d', strtotime($search_settlement)) . ' 23:59:59');
        }
        if ($search_invoice) $dt->where('c.c_invoiceNo', $search_invoice);
        if ($search_transid) {
            $dt->group_start()
               ->where('cdq.c_merchantTransactionId', $search_transid)
               ->or_where('crq.c_merchantTransactionId', $search_transid)
               ->group_end();
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }


    /**
     * Helper to fetch RRNs from multiple callback tables for a batch of transactions.
     * Decoupled lookup prevents database timeout on joins.
     */
    private function _enrich_with_rrns($list) {
        $ids = array_column($list, 'id');
        if (empty($ids)) return $list;

        $id_str = implode(',', $ids);
        $rrn_map = []; // id => rrn

        $tables = [
            'external_paydgn_qris_mpm_callback',
            'external_gvconnect_snap_qris_mpm_callback',
            'external_inacash_qris_mpm_callback',
            'external_paylabs_qris_mpm_callback_payment',
            'external_quantum_qris_mpm_calback_payment'
        ];

        foreach ($tables as $t) {
            $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId AS c_issuerRrn' : 'c_issuerRrn';
            $q = $this->db->query("SELECT ref_cashinPaymentQrisMpmId, $col FROM $t WHERE ref_cashinPaymentQrisMpmId IN ($id_str)");
            if ($q) {
                foreach ($q->result() as $row) {
                    if (!isset($rrn_map[$row->ref_cashinPaymentQrisMpmId])) {
                        $rrn_map[$row->ref_cashinPaymentQrisMpmId] = $row->c_issuerRrn;
                    }
                }
            }
        }

        // Apply back to list
        foreach ($list as &$item) {
            $itemId = is_array($item) ? $item['id'] : $item->id;
            $rrn = $rrn_map[$itemId] ?? null;
            if (is_array($item)) {
                $item['c_issuerRrn'] = $rrn;
            } else {
                $item->c_issuerRrn = $rrn;
            }
        }

        return $list;
    }

    /**
     * Helper to find IDs by RRN across all providers.
     */
    private function _get_ids_by_rrn($rrn) {
        $safeRrn = $this->db->escape_str($rrn);
        $ids = [];

        $tables = [
            'external_paydgn_qris_mpm_callback',
            'external_gvconnect_snap_qris_mpm_callback',
            'external_inacash_qris_mpm_callback',
            'external_paylabs_qris_mpm_callback_payment',
            'external_quantum_qris_mpm_calback_payment'
        ];

        // Query information_schema once to safely determine which callback tables exist
        $db_name = $this->db->database;
        $info_query = $this->db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME LIKE 'external_%'")->result_array();
        $valid_tables = array_column($info_query, 'TABLE_NAME');

        foreach ($tables as $t) {
            if (!in_array($t, $valid_tables)) continue; // Skip non-existent tables safely
            
            $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
            $q = $this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $t WHERE $col LIKE '$safeRrn%' LIMIT 50");
            if ($q) {
                foreach ($q->result() as $row) {
                    if ($row->ref_cashinPaymentQrisMpmId) $ids[] = $row->ref_cashinPaymentQrisMpmId;
                }
            }
        }
        return array_unique($ids);
    }

    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashin_channel
                WHERE c_channelGroup ='qris_mpm'
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function get_external_channels(){
        $query = "SELECT c_cashinExternalId FROM cashin_external_x_channel 
                WHERE c_cashinChannelGroup ='qris_mpm'
                GROUP BY c_cashinExternalId
                ORDER BY c_cashinExternalId ASC";
        return $this->db->query($query)->result();
    }
}
?>