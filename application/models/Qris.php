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

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null, $only_ids = false, $count_only = false, $force_reverse = false)
    {
        $this->db->query("SET SESSION max_execution_time = 30000");
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(*) as total");
        } elseif ($only_ids) {
            $this->db->select("cpq.id");
        } else {
            $this->db->select("cpq.id, cpq.c_datetime, cpq.c_type, cpq.c_amount, cpq.c_mdr, cpq.c_fee, cpq.c_isSettlementRealtime, cpq.c_datetimeSettlement, cpq.ref_merchantId, cpq.ref_subMerchantId, cpq.ref_cashinId, cpq.ref_cashinDynamicQrisMpmId, cpq.ref_cashinRecurringQrisMpmId, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);

        $isInvoiceSearch = (preg_match('/^(INV|EWALLET|QRIS|VA|BIF|BIFAST)/i', $searchValue));
        $isTechnicalId = preg_match('/^([0-9]{2,30}|[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-.*|(GD|GR|EWALLET|QRIS|VA|BIF|INV|BIFAST|UT)[0-9a-zA-Z_-]+|0000[0-9a-fA-F]+|[a-zA-Z0-9_-]{10,})$/i', $searchValue);
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';
        $isExternalSort = !empty($sort_col) && !preg_match('/^cpq\./', $sort_col) && $sort_col != 'Merchant_Transaction_Id';
        $needFullJoins = (!$only_ids && !$count_only) || $isExternalSort;

        if ($needFullJoins || $isInvoiceSearch || $isTechnicalId) {
            $this->db->join('cashin c', 'c.id = cpq.ref_cashinId');
        }

        $isTextSearch = $searchValue && !preg_match('/^[0-9]{5,25}$/', $searchValue) && !$isInvoiceSearch;
        if ($needFullJoins || $search_name || $isTextSearch) {
            $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
            $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
        }

        if ($search_name) {
            $this->db->where('cpq.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        if ($search_settlement) {
            $f_date = date('Y-m-d', strtotime($search_settlement));
            $this->db->where('cpq.c_datetimeSettlement >=', "$f_date 00:00:00");
            $this->db->where('cpq.c_datetimeSettlement <=', "$f_date 23:59:59");
        }
        if ($search_invoice && !$searchValue) {
            $this->db->where('c.c_invoiceNo', $search_invoice);
        }
        if ($search_transid && !$searchValue) {
            $this->db->group_start();
            $this->db->where('cdq.c_merchantTransactionId', $search_transid);
            $this->db->or_where('crq.c_merchantTransactionId', $search_transid);
            $this->db->group_end();
        }
        if ($search_rrn && !$searchValue) {
            $m_ids = $this->_get_ids_by_rrn($search_rrn);
            if (!empty($m_ids)) {
                $this->db->where_in('cpq.id', $m_ids);
            } else {
                $this->db->where('1=0', null, false);
            }
        }

        if ($searchValue) {
            $safe = $this->db->escape_str($searchValue);
            if ($isTechnicalId) {
                $this->db->group_start();
                $this->db->like('c.c_invoiceNo', $safe, 'both');
                $this->db->or_like('cdq.c_merchantTransactionId', $safe, 'both');
                $this->db->or_like('crq.c_merchantTransactionId', $safe, 'both');
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->like('m.c_name', $safe, 'both');
                $this->db->or_like('s.c_name', $safe, 'both');
                $this->db->group_end();
            }
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_idx = $_POST['order']['0']['column'];
                $dir = $_POST['order']['0']['dir'];
                if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
                if (isset($this->column_order[$sort_idx])) {
                    $this->db->order_by($this->column_order[$sort_idx], $dir);
                }
            } elseif (isset($this->order)) {
                $key = key($this->order);
                $dir = $this->order[$key];
                if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
                $this->db->order_by($key, $dir);
            }
        }
    }

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
        return $this->db->query("SELECT c_name FROM merchant WHERE id = ?", [$id])->result_array();
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

        $date_from_query = (!empty($date_from) && !empty($date_to)) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = (!empty($date_from) && !empty($date_to)) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid);
        $is_filtered = $search_name || $date_from || $date_to || $search_settlement || $search_rrn || $search_invoice || $search_transid || $this->input->post('search')['value'];
        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid) : $recordsTotal;

        $original_start = $_POST['start'];
        $_POST['start'] = 0;

        $output = $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function ($row) use ($original_start) {
                static $no = null;
                if ($no === null) $no = intval($original_start);
                return ++$no;
            })
            ->make(false);

        $_POST['start'] = $original_start;
        $output['draw'] = intval($this->input->post('draw'));

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

        $q = $this->db->query($union_sql);
        if ($q) {
            foreach ($q->result() as $row) {
                $rrn_map[$row->ref_cashinPaymentQrisMpmId] = $row->c_issuerRrn;
            }
        }

        foreach ($list as &$item) {
            $itemId = is_array($item) ? $item['id'] : $item->id;
            $rrn = $rrn_map[$itemId] ?? null;
            is_array($item) ? $item['c_issuerRrn'] = $rrn : $item->c_issuerRrn = $rrn;
        }
        return $list;
    }

    private function _get_ids_by_rrn($rrn)
    {
        $likePattern = $rrn . '%';
        $union_sql = "
            SELECT ref_cashinPaymentQrisMpmId FROM external_paydgn_qris_mpm_callback WHERE c_issuerRrn LIKE ?
            UNION ALL SELECT ref_cashinPaymentQrisMpmId FROM external_gvconnect_snap_qris_mpm_callback WHERE c_issuerRrn LIKE ?
            UNION ALL SELECT ref_cashinPaymentQrisMpmId FROM external_inacash_qris_mpm_callback WHERE c_issuerRrn LIKE ?
            UNION ALL SELECT ref_cashinPaymentQrisMpmId FROM external_paylabs_qris_mpm_callback_payment WHERE c_issuerRrn LIKE ?
            UNION ALL SELECT ref_cashinPaymentQrisMpmId FROM external_quantum_qris_mpm_calback_payment WHERE c_transactionId LIKE ?
        ";
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $q = $this->db->query($union_sql, [$likePattern, $likePattern, $likePattern, $likePattern, $likePattern]);
        $ids = $q ? array_filter(array_column($q->result(), 'ref_cashinPaymentQrisMpmId')) : [];
        $this->db->db_debug = $db_debug;
        return array_values(array_unique(array_map('intval', $ids)));
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