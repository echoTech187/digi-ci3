<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VirtualAccount extends CI_Model {
    
    // DataTables variables
    private static $cached_total = null;
    var $table = 'cashin_payment_va cpv';
    var $column_order = array(null, 'cpv.c_datetime', 'm.c_name', 'Merchant_Transaction_Id', 'cpv.c_vaNumber', 'egv.c_custom', 'c.c_invoiceNo', 'cpv.ref_cashinChannelId', 'cpv.c_type', 'cpv.c_amount', 'cpv.c_fee', 'cpv.c_isSettlementRealtime', 'cpv.c_datetimeSettlement', null); 
    var $column_search = array('cpv.id', 'm.c_name', 'c.c_invoiceNo', 'cpv.c_vaNumber', 'cdv.c_merchantTransactionId', 'crv.c_merchantTransactionId', 'egv.c_custom');
    var $order = array('cpv.id' => 'desc');




    public function get_va($limit, $start, $search_date_va = null, $search_name_va = null, $search_date_va_settlement = null, $search_va_number = null, $search_va_transid = null) 
    {
        $base_query = " FROM cashin_payment_va cpv
                        JOIN cashin c ON cpv.ref_cashinId = c.id
                        JOIN submerchant s ON cpv.ref_subMerchantId = s.id
                        LEFT JOIN cashin_dynamic_va ON (cashin_dynamic_va.id = cpv.ref_cashinDynamicVaId AND cashin_dynamic_va.ref_merchantId = cpv.ref_merchantId)
                        LEFT JOIN cashin_recurring_va ON (cashin_recurring_va.id = cpv.ref_cashinRecurringVaId AND cashin_recurring_va.ref_merchantId = cpv.ref_merchantId)
                        LEFT JOIN merchant m ON cpv.ref_merchantId = m.id
                        LEFT JOIN external_gvpay_va_callback_payment ON (external_gvpay_va_callback_payment.ref_subMerchantId = cpv.ref_subMerchantId
                        AND external_gvpay_va_callback_payment.ref_cashinPaymentVaId = cpv.id)
                        WHERE 1=1";

        if ($search_date_va) {
            $search_date_va = date('Y-m-d', strtotime($search_date_va));
            $base_query .= " AND cpv.c_datetime >= '$search_date_va 00:00:00' AND cpv.c_datetime <= '$search_date_va 23:59:59'";
        }
        if ($search_name_va) {
            $base_query .= " AND m.id = $search_name_va";
        }
        if ($search_date_va_settlement) {
            $search_date_va_settlement = date('Y-m-d', strtotime($search_date_va_settlement));
            $base_query .= " AND cpv.c_datetimeSettlement >= '$search_date_va_settlement 00:00:00' AND cpv.c_datetimeSettlement <= '$search_date_va_settlement 23:59:59'";
        }
        if ($search_va_number) {
            $search_va_number = trim($search_va_number);
            if ($search_va_number !== '') {
                $base_query .= " AND cpv.c_vaNumber LIKE '{$this->db->escape_like_str($search_va_number)}%'";
            }
        }
        if ($search_va_transid) {
            $search_va_transid = trim($search_va_transid);
            if ($search_va_transid !== '') {
                $escaped_transid = $this->db->escape_like_str($search_va_transid);
                $base_query .= " AND (cashin_dynamic_va.c_merchantTransactionId LIKE '{$escaped_transid}%' OR cashin_recurring_va.c_merchantTransactionId LIKE '{$escaped_transid}%')";
            }
        }

        // Hitung total rows untuk pagination
        $total_query = "SELECT COUNT(*) AS total_rows" . $base_query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;

        // Ambil data dengan limit
        $data_query = "SELECT cpv.*, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name,
                    IF(cpv.c_type = 'Dynamic', cashin_dynamic_va.c_merchantTransactionId, cashin_recurring_va.c_merchantTransactionId) AS Merchant_Transaction_Id,
                    external_gvpay_va_callback_payment.c_custom" . $base_query . " ORDER BY cpv.id DESC LIMIT $start, $limit";
        $data = $this->db->query($data_query)->result();

        return [
            'total_rows' => $total_rows,
            'data' => $data
        ];
    }


    public function count_va($search_date_va = null, $search_name_va = null, $search_date_va_settlement = null, $search_va_number = null, $search_va_transid = null) 
    {
        $this->db->from('cashin_payment_va cpv');
        $this->db->join('cashin c', 'cpv.ref_cashinId = c.id');
        $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId', 'left');
        
        if ($search_date_va) {
            $formatted_date = date('Y-m-d', strtotime($search_date_va));
            $this->db->where('cpv.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpv.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if ($search_name_va) {
            $this->db->where('m.id', $search_name_va);
        }

        if ($search_date_va_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_date_va_settlement));
            $this->db->where('cpv.c_datetimeSettlement >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpv.c_datetimeSettlement <=', $formatted_date . ' 23:59:59');
        }

        if ($search_va_number) {
            $search_va_number = trim($search_va_number);
            if ($search_va_number !== '') {
                $this->db->like('cpv.c_vaNumber', $search_va_number, 'after');
            }
        }

        if ($search_va_transid) {
            $search_va_transid = trim($search_va_transid);
            if ($search_va_transid !== '') {
                $this->db->group_start();
                $this->db->like('cdv.c_merchantTransactionId', $search_va_transid, 'after');
                $this->db->or_like('cashin_recurring_va.c_merchantTransactionId', $search_va_transid, 'after');
                $this->db->group_end();
            }
        }

        return (int)$this->db->count_all_results();
    }


    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashin_channel 
                WHERE c_channelGroup IN ('va', 'VIRTUAL_ACCOUNT')
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function get_external_channels(){
        $query = "SELECT c_cashinExternalId FROM cashin_external_x_channel 
                WHERE c_cashinChannelGroup IN ('va', 'VIRTUAL_ACCOUNT')
                GROUP BY c_cashinExternalId
                ORDER BY c_cashinExternalId ASC";
        return $this->db->query($query)->result();
    }

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashin_payment_va a
        WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }
        return $this->db->query($query)->result_array();
    }

    public function va_detail($id)
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

    /**
     * Standardized DataTables handler for Virtual Account list.
     * Utilizes the optimized two-step Pre-Lookup query logic with Datatables library.
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_merchant = $filters['merchant'] ?? null;
        $search_settlement = $filters['settlement'] ?? null;
        $search_va = $filters['va_number'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_invoice = $filters['invoice_no'] ?? null;
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
?>