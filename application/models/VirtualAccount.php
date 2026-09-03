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
        $id = $this->db->escape($id);
        $query = "SELECT 
                    cpv.*, 
                    m.id as id_merchant, 
                    m.c_name AS name_merchant, 
                    m.c_name AS merchant_name, 
                    s.id as id_submerchant, 
                    s.c_name AS name_submerchant, 
                    s.c_name AS sub_account_name, 
                    c.c_invoiceNo,
                    IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id
                FROM 
                    cashin_payment_va cpv 
                    LEFT JOIN cashin c ON cpv.ref_cashinId = c.id 
                    LEFT JOIN merchant m ON cpv.ref_merchantId = m.id
                    LEFT JOIN submerchant s ON cpv.ref_subMerchantId = s.id 
                    LEFT JOIN cashin_dynamic_va cdv ON cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId
                    LEFT JOIN cashin_recurring_va crv ON crv.id = cpv.ref_cashinRecurringVaId AND crv.ref_merchantId = cpv.ref_merchantId
                WHERE 
                    cpv.id = $id";
        return $this->db->query($query)->result_array();
    }
    public function get_merchant(){
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
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

        $dt = $this->datatables->of('cashin_payment_va cpv')
            ->select('cpv.id, cpv.c_datetime, cpv.c_type, cpv.c_vaNumber, cpv.c_amount, cpv.c_fee, cpv.c_isSettlementRealtime, cpv.c_datetimeSettlement, cpv.ref_merchantId, cpv.ref_subMerchantId, cpv.ref_cashinId, cpv.ref_cashinChannelId, m.c_name as merchant_name, s.c_name as sub_account_name, c.c_invoiceNo, IF(cpv.c_type = "Dynamic", cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id, IF(cpv.c_type = "Dynamic", cdv.ref_cashinExternalLogVaIdCreate, crv.ref_cashinExternalLogVaIdCreate) AS ref_cashinExternalLogVaIdCreate, IF(cpv.c_type = "Dynamic", cdv.ref_cashinExternalId, crv.ref_cashinExternalId) AS ref_cashinExternalId, egv.c_custom, cc.c_description AS channel_description', FALSE)
            ->join('merchant m', 'm.id = cpv.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cpv.ref_subMerchantId', 'left')
            ->join('cashin c', 'c.id = cpv.ref_cashinId', 'left')
            ->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId', 'left')
            ->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId AND crv.ref_merchantId = cpv.ref_merchantId', 'left')
            ->join('external_gvpay_va_callback_payment egv', 'egv.ref_subMerchantId = cpv.ref_subMerchantId AND egv.ref_cashinPaymentVaId = cpv.id', 'left')
            ->join('cashin_channel cc', 'cc.id = cpv.ref_cashinChannelId', 'left')
            ->set_column_order([null, 'cpv.c_datetime', 's.c_name', null, 'cpv.c_vaNumber', 'c.c_invoiceNo', 'cpv.c_type', 'cpv.ref_cashinChannelId', 'cpv.c_amount', 'cpv.c_fee', 'cpv.c_datetimeSettlement', null])
            ->set_column_search(['cpv.id', 'cpv.c_vaNumber', 'c.c_invoiceNo', 's.c_name', 'm.c_name'])
            ->set_default_order(['cpv.id' => 'desc']);

        if ($search_merchant) $dt->where('cpv.ref_merchantId', $search_merchant);
        if ($search_channel) $dt->where('cpv.ref_cashinChannelId', $search_channel);
        if ($search_va) $dt->where('cpv.c_vaNumber', $search_va);
        if ($search_invoice) $dt->where('c.c_invoiceNo', $search_invoice);
        if ($search_date && $search_date_to) {
            $dt->where('cpv.c_datetime >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cpv.c_datetime <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $dt->where('cpv.c_datetime >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cpv.c_datetime <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
        }
        if ($search_settlement) {
            $dt->where('cpv.c_datetimeSettlement >=', date('Y-m-d', strtotime($search_settlement)) . ' 00:00:00')
               ->where('cpv.c_datetimeSettlement <=', date('Y-m-d', strtotime($search_settlement)) . ' 23:59:59');
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}