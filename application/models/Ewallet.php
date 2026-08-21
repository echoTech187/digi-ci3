<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ewallet extends CI_Model {
    var $table = 'cashin_payment_ewallet cpe';
    var $column_order = array(null, 'cpe.c_datetime', 's.c_name', 'cde.c_merchantTransactionId', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', null);
    var $column_search = array('cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cpe.id' => 'desc');
    private static $cached_total = null;



    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_date_settlement = $filters['settlement'] ?? null;
        $search_invoice_no = $filters['invoice'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_channel = $filters['channel'] ?? null;

        $dt = $this->datatables->of('cashin_payment_ewallet cpe')
            ->select('cpe.id, cpe.c_datetime, cpe.c_type, cpe.c_amount, cpe.c_mdr, cpe.c_fee, cpe.c_isSettlementRealtime, cpe.c_datetimeSettlement, cpe.ref_merchantId, cpe.ref_subMerchantId, cpe.ref_cashinId, cpe.ref_cashinChannelId, m.c_name as merchant_name, s.c_name as sub_account_name, c.c_invoiceNo, cde.c_merchantTransactionId AS Merchant_Transaction_Id, cde.ref_cashinExternalLogEwalletIdCreate, cde.ref_cashinExternalId, cc.c_description AS channel_description', FALSE)
            ->join('merchant m', 'm.id = cpe.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cpe.ref_subMerchantId', 'left')
            ->join('cashin c', 'c.id = cpe.ref_cashinId', 'left')
            ->join('cashin_dynamic_ewallet cde', 'cde.id = cpe.ref_cashinDynamicEwalletId', 'left')
            ->join('cashin_channel cc', 'cc.id = cpe.ref_cashinChannelId', 'left')
            ->set_column_order([null, 'cpe.c_datetime', 's.c_name', 'cde.c_merchantTransactionId', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', null])
            ->set_column_search(['cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name'])
            ->set_default_order(['cpe.id' => 'desc']);

        if ($search_name) $dt->where('cpe.ref_merchantId', $search_name);
        if ($search_channel) $dt->where('cpe.ref_cashinChannelId', $search_channel);
        if ($date_from && $date_to) {
            $dt->where('cpe.c_datetime >=', date('Y-m-d', strtotime($date_from)) . ' 00:00:00')
               ->where('cpe.c_datetime <=', date('Y-m-d', strtotime($date_to)) . ' 23:59:59');
        } elseif ($date_from) {
            $dt->where('cpe.c_datetime >=', date('Y-m-d', strtotime($date_from)) . ' 00:00:00')
               ->where('cpe.c_datetime <=', date('Y-m-d', strtotime($date_from)) . ' 23:59:59');
        }
        if ($search_date_settlement) {
            $dt->where('cpe.c_datetimeSettlement >=', date('Y-m-d', strtotime($search_date_settlement)) . ' 00:00:00')
               ->where('cpe.c_datetimeSettlement <=', date('Y-m-d', strtotime($search_date_settlement)) . ' 23:59:59');
        }
        if ($search_invoice_no) $dt->where('c.c_invoiceNo', $search_invoice_no);
        if ($search_transid) $dt->where('cde.c_merchantTransactionId', $search_transid);

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }

    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashin_channel 
                WHERE c_channelGroup = 'ewallet'
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function get_external_channels(){
        $query = "SELECT c_cashinExternalId FROM cashin_external_x_channel 
                WHERE c_cashinChannelGroup = 'ewallet'
                GROUP BY c_cashinExternalId
                ORDER BY c_cashinExternalId ASC";
        return $this->db->query($query)->result();
    }

    public function ewallet_detail($id)
    {
        $id = $this->db->escape($id);
        $query = "SELECT 
                    cpe.*, 
                    m.id as id_merchant, 
                    m.c_name AS name_merchant, 
                    s.id as id_submerchant, 
                    s.c_name AS name_submerchant, 
                    c.c_invoiceNo,
                    cde.c_merchantTransactionId,
                    cde.c_merchantTransactionId AS Merchant_Transaction_Id
                FROM 
                    cashin_payment_ewallet cpe 
                    LEFT JOIN cashin c ON cpe.ref_cashinId = c.id 
                    LEFT JOIN merchant m ON cpe.ref_merchantId = m.id
                    LEFT JOIN submerchant s ON cpe.ref_subMerchantId = s.id 
                    LEFT JOIN cashin_dynamic_ewallet cde ON cde.id = cpe.ref_cashinDynamicEwalletId
                WHERE 
                    cpe.id = $id";
        return $this->db->query($query)->result_array();
    }
}
?>