<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Qris Model
 * Handles QRIS MPM transaction queries, RRN enrichment, two-step DataTables processing, and external callback log resolution.
 */
class Qris extends CI_Model
{
    var $table = 'cashin_payment_qris_mpm cpq';
    var $column_order = [
        null, 'cpq.c_datetime', 'm.c_name', 's.c_name', 'Merchant_Transaction_Id',
        'c.c_invoiceNo', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_mdr', 'cpq.c_fee',
        'cpq.c_issuerRrn', 'cpq.c_isSettlementRealtime', 'cpq.c_datetimeSettlement', null
    ];
    var $column_search = ['cpq.id', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'crq.c_merchantTransactionId', 'cpq.c_issuerRrn'];
    var $order = ['cpq.id' => 'desc'];
    private static $cached_total = null;
  
    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        $length = isset($_POST['length']) ? (int) $_POST['length'] : 10;
        $force_reverse = false;
        $fetch_start = $start;
        $fetch_length = $length;

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, true, false, $force_reverse);
        if ($length != -1) {
            $this->db->limit($fetch_length, $fetch_start);
        }

        $query = $this->db->get();
        if (!is_object($query)) return [];
        $id_results = $query->result();
        if (empty($id_results)) return [];

        $ids = array_column($id_results, 'id');
        $this->db->select("cpq.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id, IF(cpq.c_type='Dynamic', cdq.ref_cashinExternalId, crq.ref_cashinExternalId) AS ref_cashinExternalId");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpq.ref_cashinId', 'left');
        $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cpq.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
        $this->db->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        $this->db->where_in('cpq.id', $ids);
        $this->db->order_by('cpq.id', 'desc');

        $final_results = $this->db->get()->result();
        return !empty($final_results) ? $this->_enrich_with_rrns($final_results) : [];
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $searchValue = $this->input->post('search')['value'] ?? '';
        $is_filtered = $search_name || $date_from || $date_to || $search_settlement || $search_rrn || $search_invoice || $search_transid || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, false, true);
        $query = $this->db->get();
        return (is_object($query) && $query->num_rows() > 0) ? $query->row()->total : 0;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        if (!$search_name && !$date_from && !$date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_payment_qris_mpm'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int) $res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(*) as total")->from($this->table);
        if ($search_name) $this->db->where('cpq.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int) $query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_qris($limit, $start, $search_date_qris = null, $search_date_qris_to = null, $search_name_qris = null, $search_date_qris_settlement = null, $search_rrn = null, $search_transactionid_ht = null)
    {
        $this->db->select('m.c_name as name_merchant, cpq.id, cpq.c_datetime, s.c_name as name_submerchant, c.c_invoiceNo, cpq.c_type, cpq.ref_merchantId, cpq.ref_subMerchantId, cpq.c_amount, cpq.c_mdr, cpq.c_fee, cpq.c_datetimePayment, cpq.c_isSettlementRealtime, cpq.c_datetimeSettlement, cpq.c_isSettlementRealtimeExternal, cpq.c_feeExternal, cpq.c_datetimeSettlementExternal, IF(cpq.c_type=\'Dynamic\', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id')
            ->from('cashin_payment_qris_mpm cpq')
            ->join('cashin c', 'c.id = cpq.ref_cashinId')
            ->join('submerchant s', 'cpq.ref_subMerchantId = s.id')
            ->join('merchant m', 'cpq.ref_merchantId = m.id')
            ->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left')
            ->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');

        if (!empty($search_name_qris)) $this->db->where('cpq.ref_merchantId', $search_name_qris);
        if (!empty($search_date_qris) && !empty($search_date_qris_to)) {
            $this->db->where('cpq.c_datetime >=', $search_date_qris)->where('cpq.c_datetime <=', $search_date_qris_to);
        }
        if (!empty($search_date_qris_settlement)) {
            $f_date = date('Y-m-d', strtotime($search_date_qris_settlement));
            $this->db->where('cpq.c_datetimeSettlement >=', "$f_date 00:00:00")->where('cpq.c_datetimeSettlement <=', "$f_date 23:59:59");
        }
        if (!empty($search_transactionid_ht)) {
            $this->db->where('cdq.c_merchantTransactionId', $search_transactionid_ht)->where('cdq.c_status', 'Paid');
        }
        if (!empty($search_rrn)) {
            $m_ids = $this->_get_ids_by_rrn($search_rrn);
            !empty($m_ids) ? $this->db->where_in('cpq.id', $m_ids) : $this->db->where('1=0', null, false);
        }

        $this->db->order_by('cpq.id', 'DESC')->limit($limit, $start);
        $res = $this->db->get()->result();
        return !empty($res) ? $this->_enrich_with_rrns($res) : [];
    }

    public function get_merchant_detail($id)
    {
        return $this->db->select('c_name')->from('merchant')->where('id', $id)->get()->result_array();
    }

    public function count_qris($refMerchantId, $search_date_qris = null)
    {
        $this->db->from('cashin_payment_qris_mpm')->where('ref_merchantId', $refMerchantId);
        if ($search_date_qris) $this->db->where('c_datetime', $search_date_qris);
        return $this->db->count_all_results();
    }

    public function get_summary($date_from, $date_to, $refMerchantId = null)
    {
        $params = [$date_from, $date_to];
        $sql = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external FROM cashin_payment_qris_mpm a WHERE a.c_datetime >= ? AND a.c_datetime <= ?";
        if (!empty($refMerchantId)) {
            $sql .= " AND a.ref_merchantId = ?";
            $params[] = $refMerchantId;
        }
        return $this->db->query($sql, $params)->result_array();
    }

    public function monthly_qris()
    {
        $year = date('Y');
        $sql = "SELECT MONTH(c_datetime) AS month, SUM(c_amount) AS amount FROM cashin_payment_qris_mpm WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY MONTH(c_datetime) ORDER BY month";
        return $this->db->query($sql, ["$year-01-01 00:00:00", "$year-12-31 23:59:59"])->result_array();
    }

    public function qris_detail($id)
    {
        $sql = "SELECT a.id, a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, d.c_name AS name_submerchant, b.c_invoiceNo, a.c_type, a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment, a.c_isSettlementRealtime, a.c_datetimeSettlement, IF(a.c_type='Dynamic', e.c_merchantTransactionId, f.c_merchantTransactionId) AS c_merchantTransactionId, a.ref_cashinExternalId, a.c_isSettlementRealtimeExternal, a.c_datetimeSettlementExternal, a.c_mdrExternal, a.c_feeExternal, e.ref_cashinExternalLogQrisMpmIdCreate AS dynamic_create_log_id, f.ref_cashinExternalLogQrisMpmIdCreate AS recurring_create_log_id, a.ref_cashinDynamicQrisMpmId, a.ref_cashinRecurringQrisMpmId FROM cashin_payment_qris_mpm a LEFT JOIN cashin b ON b.id=a.ref_cashinId LEFT JOIN merchant c ON a.ref_merchantId=c.id LEFT JOIN submerchant d ON a.ref_subMerchantId=d.id LEFT JOIN cashin_dynamic_qris_mpm e ON e.id = a.ref_cashinDynamicQrisMpmId LEFT JOIN cashin_recurring_qris_mpm f ON f.id = a.ref_cashinRecurringQrisMpmId WHERE a.id = ?";
        $res = $this->db->query($sql, [$id])->result_array();
        return !empty($res) ? $this->_enrich_with_rrns($res) : [];
    }

    public function get_external_payment_log($id, $ref_cashinExternalId)
    {
        $tblMap = [
            'paydgn'    => 'external_paydgn_qris_mpm_callback',
            'gvconnect' => 'external_gvconnect_snap_qris_mpm_callback',
            'inacash'   => 'external_inacash_qris_mpm_callback',
            'paylabs'   => 'external_paylabs_qris_mpm_callback_payment',
            'paylabs2'  => 'external_paylabs_qris_mpm_callback_payment',
            'quantum'   => 'external_quantum_qris_mpm_calback_payment',
            'stm'       => 'external_stm_qris_mpm_callback',
            'yukk'      => 'external_yukk_qris_mpm_callback'
        ];
        if (isset($tblMap[$ref_cashinExternalId])) {
            $q = $this->db->get_where($tblMap[$ref_cashinExternalId], ['ref_cashinPaymentQrisMpmId' => $id]);
            return $q ? $q->row_array() : null;
        }
        return null;
    }

    public function get_merchant()
    {
        return $this->db->select('id, c_name')->get('merchant')->result();
    }

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

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    private function _enrich_with_rrns($list)
    {
        $ids = array_column($list, 'id');
        if (empty($ids)) return $list;

        $id_str = implode(',', array_map('intval', $ids));
        $rrn_map = [];

        $union_sql = "
            SELECT ref_cashinPaymentQrisMpmId, c_issuerRrn FROM external_paydgn_qris_mpm_callback WHERE ref_cashinPaymentQrisMpmId IN ($id_str)
            UNION ALL SELECT ref_cashinPaymentQrisMpmId, c_issuerRrn FROM external_gvconnect_snap_qris_mpm_callback WHERE ref_cashinPaymentQrisMpmId IN ($id_str)
            UNION ALL SELECT ref_cashinPaymentQrisMpmId, c_issuerRrn FROM external_inacash_qris_mpm_callback WHERE ref_cashinPaymentQrisMpmId IN ($id_str)
            UNION ALL SELECT ref_cashinPaymentQrisMpmId, c_issuerRrn FROM external_paylabs_qris_mpm_callback_payment WHERE ref_cashinPaymentQrisMpmId IN ($id_str)
            UNION ALL SELECT ref_cashinPaymentQrisMpmId, c_transactionId AS c_issuerRrn FROM external_quantum_qris_mpm_calback_payment WHERE ref_cashinPaymentQrisMpmId IN ($id_str)
        ";

        $unions = [];
        foreach ($tables as $t) {
            $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId AS c_issuerRrn' : 'c_issuerRrn';
            $unions[] = "SELECT ref_cashinPaymentQrisMpmId, $col FROM $t WHERE ref_cashinPaymentQrisMpmId IN ($id_str)";
        }
        $union_sql = implode(" UNION ALL ", $unions);
        $q = $this->db->query($union_sql);
        if ($q) {
            foreach ($q->result() as $row) {
                if (!isset($rrn_map[$row->ref_cashinPaymentQrisMpmId])) {
                    $rrn_map[$row->ref_cashinPaymentQrisMpmId] = $row->c_issuerRrn;
                }
            }
        }

        foreach ($list as &$item) {
            $itemId = is_array($item) ? $item['id'] : $item->id;
            $rrn = $rrn_map[$itemId] ?? null;
            is_array($item) ? $item['c_issuerRrn'] = $rrn : $item->c_issuerRrn = $rrn;
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

        $rrn_unions = [];
        foreach ($tables as $t) {
            if (in_array($t, $valid_tables)) {
                $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
                $rrn_unions[] = "(SELECT ref_cashinPaymentQrisMpmId FROM $t WHERE $col LIKE '$safeRrn%' LIMIT 50)";
            }
        }
        if (!empty($rrn_unions)) {
            $q = $this->db->query(implode(" UNION ALL ", $rrn_unions));
            if ($q) {
                foreach ($q->result() as $row) {
                    if ($row->ref_cashinPaymentQrisMpmId) $ids[] = $row->ref_cashinPaymentQrisMpmId;
                }
            }
        }
        return array_unique($ids);
    }

    public function get_internal_channels()
    {
        return $this->db->query("SELECT id, c_description FROM cashin_channel WHERE c_channelGroup ='qris_mpm' ORDER BY c_description ASC")->result();
    }

    public function get_external_channels()
    {
        return $this->db->query("SELECT c_cashinExternalId FROM cashin_external_x_channel WHERE c_cashinChannelGroup ='qris_mpm' GROUP BY c_cashinExternalId ORDER BY c_cashinExternalId ASC")->result();
    }
}