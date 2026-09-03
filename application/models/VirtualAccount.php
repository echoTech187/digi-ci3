<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * VirtualAccount Model
 * Handles Virtual Account transaction queries, DataTables handlers, summary metrics, and detail lookups.
 */
class VirtualAccount extends CI_Model
{
    private static $cached_total = null;
    var $table = 'cashin_payment_va cpv';
    var $column_order = [
        null, 'cpv.c_datetime', 'm.c_name', 'Merchant_Transaction_Id', 'cpv.c_vaNumber',
        'egv.c_custom', 'c.c_invoiceNo', 'cpv.ref_cashinChannelId', 'cpv.c_type',
        'cpv.c_amount', 'cpv.c_fee', 'cpv.c_isSettlementRealtime', 'cpv.c_datetimeSettlement', null
    ];
    var $column_search = ['cpv.id', 'm.c_name', 'c.c_invoiceNo', 'cpv.c_vaNumber', 'cdv.c_merchantTransactionId', 'crv.c_merchantTransactionId', 'egv.c_custom'];
    var $order = ['cpv.id' => 'desc'];

    private function _get_datatables_query($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null, $only_ids = false, $count_only = false)
    {
        $this->db->query("SET SESSION max_execution_time = 30000");
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(*) as total");
        } elseif ($only_ids) {
            $this->db->select("cpv.id");
        } else {
            $this->db->select("cpv.id, cpv.c_datetime, cpv.c_type, cpv.c_vaNumber, cpv.c_amount, cpv.c_fee, cpv.c_isSettlementRealtime, cpv.c_datetimeSettlement, cpv.ref_merchantId, cpv.ref_subMerchantId, cpv.ref_cashinId, cpv.ref_cashinChannelId, cpv.ref_cashinDynamicVaId, cpv.ref_cashinRecurringVaId, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name, IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id, egv.c_custom, cc.c_description AS channel_description");
        }
        $this->db->from($this->table);

        $isInvoiceSearch = (preg_match('/^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        if (!$only_ids && !$count_only || $isInvoiceSearch || strpos($sort_col, 'c.') !== false) {
            $this->db->join('cashin c', 'cpv.ref_cashinId = c.id', 'left');
        }

        if (!$only_ids && !$count_only || strpos($sort_col, 'egv.') !== false) {
            $this->db->join('external_gvpay_va_callback_payment egv', 'egv.ref_subMerchantId = cpv.ref_subMerchantId AND egv.ref_cashinPaymentVaId = cpv.id', 'left');
        }

        $isTextSearch = $searchValue && !preg_match('/^(VA|INV|[0-9]{8,})/i', $searchValue);
        if (!$only_ids && !$count_only || $search_merchant || $isTextSearch || strpos($sort_col, 'm.') !== false) {
            $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        }

        if (!$only_ids && !$count_only || $isTextSearch || strpos($sort_col, 's.') !== false) {
            $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
        }

        if (!$only_ids && !$count_only || strpos($sort_col, 'cc.') !== false) {
            $this->db->join('cashin_channel cc', 'cc.id = cpv.ref_cashinChannelId', 'left');
        }

        if (!$only_ids && !$count_only || strpos($sort_col, 'Merchant_Transaction_Id') !== false) {
            $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId', 'left');
            $this->db->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId', 'left');
        }

        if ($search_merchant) {
            $this->db->where('cpv.ref_merchantId', $search_merchant);
        }
        if ($search_date && $search_date_to) {
            $this->db->where('cpv.c_datetime >=', $search_date);
            $this->db->where('cpv.c_datetime <=', $search_date_to);
        }
        if ($search_settlement) {
            $f_date = date('Y-m-d', strtotime($search_settlement));
            $this->db->where('cpv.c_datetimeSettlement >=', "$f_date 00:00:00");
            $this->db->where('cpv.c_datetimeSettlement <=', "$f_date 23:59:59");
        }
        if ($search_va && !$searchValue) {
            $this->db->where('cpv.c_vaNumber', $search_va);
        }
        if ($search_invoice && !$searchValue) {
            $this->db->where('c.c_invoiceNo', $search_invoice);
        }
        if ($search_channel) {
            $this->db->where('cpv.ref_cashinChannelId', $search_channel);
        }

        if ($search_transid && !$searchValue) {
            $this->db->group_start();
            $this->db->where('cdv.c_merchantTransactionId', $search_transid);
            $this->db->or_where('crv.c_merchantTransactionId', $search_transid);
            $this->db->group_end();
        }

        if ($searchValue) {
            $safe = $this->db->escape_str($searchValue);
            if (is_numeric($searchValue) && strlen($searchValue) >= 8) {
                $this->db->where('cpv.c_vaNumber', $searchValue);
            } elseif ($isInvoiceSearch) {
                $this->db->like('c.c_invoiceNo', $safe, 'after');
            } else {
                $this->db->group_start();
                $this->db->like('cpv.c_vaNumber', $safe, 'after');
                $this->db->or_like('m.c_name', $safe, 'both');
                $this->db->or_like('s.c_name', $safe, 'both');
                $this->db->group_end();
            }
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_idx = $_POST['order']['0']['column'];
                $dir = $_POST['order']['0']['dir'];
                if (isset($this->column_order[$sort_idx])) {
                    $this->db->order_by($this->column_order[$sort_idx], $dir);
                }
            } elseif (isset($this->order)) {
                $key = key($this->order);
                $this->db->order_by($key, $this->order[$key]);
            }
        }
    }

    public function get_datatables($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null)
    {
        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel, true);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $id_results = $this->db->get()->result();
        if (empty($id_results)) return [];

        $ids = array_column($id_results, 'id');
        $this->db->select("cpv.*, m.c_name AS name_merchant, s.c_name AS name_submerchant, c.c_invoiceNo, IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id, egv.c_custom, cc.c_description AS channel_description");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'cpv.ref_cashinId = c.id', 'left');
        $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
        $this->db->join('cashin_channel cc', 'cc.id = cpv.ref_cashinChannelId', 'left');
        $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId', 'left');
        $this->db->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId', 'left');
        $this->db->join('external_gvpay_va_callback_payment egv', 'egv.ref_subMerchantId = cpv.ref_subMerchantId AND egv.ref_cashinPaymentVaId = cpv.id', 'left');
        $this->db->where_in('cpv.id', $ids);
        $this->db->order_by('cpv.id', 'desc');

        return $this->db->get()->result();
    }

    public function count_filtered($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null)
    {
        $searchValue = $this->input->post('search')['value'] ?? '';
        $is_filtered = $search_date || $search_date_to || $search_merchant || $search_settlement || $search_va || $search_transid || $search_invoice || $search_channel || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel, false, true);
        $query = $this->db->get();
        return (is_object($query) && $query->num_rows() > 0) ? $query->row()->total : 0;
    }

    public function count_all_dt($search_merchant = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        if (!$search_merchant && !$search_date && !$search_date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_payment_va'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int) $res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(*) as total")->from($this->table);
        if ($search_merchant) $this->db->where('cpv.ref_merchantId', $search_merchant);
        if ($search_date && $search_date_to) {
            $this->db->where('cpv.c_datetime >=', $search_date);
            $this->db->where('cpv.c_datetime <=', $search_date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int) $query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_va($limit, $start, $search_date_va = null, $search_date_va_to = null, $search_name_va = null, $search_date_va_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null)
    {
        $this->db->select('cpv.*, m.c_name AS name_merchant, s.c_name AS name_submerchant, c.c_invoiceNo, IF(cpv.c_type = \'Dynamic\', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id')
            ->from('cashin_payment_va cpv')
            ->join('cashin c', 'c.id = cpv.ref_cashinId', 'left')
            ->join('merchant m', 'm.id = cpv.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cpv.ref_subMerchantId', 'left')
            ->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId', 'left')
            ->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId', 'left');

        if (!empty($search_name_va)) $this->db->where('cpv.ref_merchantId', $search_name_va);
        if (!empty($search_date_va) && !empty($search_date_va_to)) {
            $this->db->where('cpv.c_datetime >=', $search_date_va)->where('cpv.c_datetime <=', $search_date_va_to);
        }
        if (!empty($search_date_va_settlement)) {
            $f_date = date('Y-m-d', strtotime($search_date_va_settlement));
            $this->db->where('cpv.c_datetimeSettlement >=', "$f_date 00:00:00")->where('cpv.c_datetimeSettlement <=', "$f_date 23:59:59");
        }
        if (!empty($search_va)) $this->db->where('cpv.c_vaNumber', $search_va);
        if (!empty($search_invoice)) $this->db->where('c.c_invoiceNo', $search_invoice);

        $this->db->order_by('cpv.id', 'DESC')->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_merchant()
    {
        return $this->db->select('id, c_name')->get('merchant')->result();
    }

    public function get_summary($date_from, $date_to, $refMerchantId = null)
    {
        $params = [$date_from, $date_to];
        $sql = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external FROM cashin_payment_va a WHERE a.c_datetime >= ? AND a.c_datetime <= ?";
        if (!empty($refMerchantId)) {
            $sql .= " AND a.ref_merchantId = ?";
            $params[] = $refMerchantId;
        }
        return $this->db->query($sql, $params)->result_array();
    }

    public function getVaDetail($id)
    {
        $sql = "SELECT cpv.*, c.c_invoiceNo, m.c_name AS name_merchant, s.c_name AS name_submerchant, cc.c_description as channel_description, IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id, cdv.ref_cashinExternalLogVaIdCreate as dynamic_create_log_id, crv.ref_cashinExternalLogVaIdCreate as recurring_create_log_id FROM cashin_payment_va cpv JOIN cashin c ON c.id = cpv.ref_cashinId JOIN merchant m ON m.id = cpv.ref_merchantId JOIN submerchant s ON s.id = cpv.ref_subMerchantId LEFT JOIN cashin_channel cc ON cc.id = cpv.ref_cashinChannelId LEFT JOIN cashin_dynamic_va cdv ON cdv.id = cpv.ref_cashinDynamicVaId LEFT JOIN cashin_recurring_va crv ON crv.id = cpv.ref_cashinRecurringVaId WHERE cpv.id = ?";
        return $this->db->query($sql, [$id])->result_array();
    }

    public function get_external_payment_log($id, $ref_cashinExternalId)
    {
        $tblMap = [
            'gvpay'    => 'external_gvpay_va_callback_payment',
            'oy'       => 'external_oy_va_callback_payment',
            'paylabs'  => 'external_paylabs_va_callback_payment',
            'paylabs2' => 'external_paylabs2_va_callback_payment',
            'ina'      => 'external_ina_va_callback_payment',
            'faspay'   => 'external_faspay_va_callback_payment',
            'artajasa' => 'external_artajasa_va_callback_payment'
        ];
        if (isset($tblMap[$ref_cashinExternalId])) {
            $q = $this->db->get_where($tblMap[$ref_cashinExternalId], ['ref_cashinPaymentVaId' => $id]);
            return $q ? $q->row_array() : null;
        }
        return null;
    }

    public function get_internal_channels()
    {
        return $this->db->select('id, c_description')->where('c_channelGroup', 'va')->order_by('c_description', 'ASC')->get('cashin_channel')->result();
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');
        $search_merchant = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_settlement = $filters['settlement'] ?? null;
        $search_va = $filters['va_number'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_invoice = $filters['invoice'] ?? null;
        $search_channel = $filters['channel'] ?? null;

        $date_from_query = (!empty($date_from) && !empty($date_to)) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = (!empty($date_from) && !empty($date_to)) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        $list = $this->get_datatables($date_from_query, $date_to_query, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel);
        $is_filtered = $search_merchant || $date_from || $date_to || $search_settlement || $search_va || $search_transid || $search_invoice || $search_channel || $this->input->post('search')['value'];
        $recordsTotal = $this->count_all_dt($search_merchant, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($date_from_query, $date_to_query, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel) : $recordsTotal;

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
}