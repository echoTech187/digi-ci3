<?php defined('BASEPATH') or exit('No direct script access allowed');

class QRISDynamiclist extends CI_Model
{
    var $table = 'cashin_dynamic_qris_mpm cdq';
    var $column_order = array(null, 'cdq.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'cdq.c_referenceNo', 'cdq.ref_cashinExternalId', 'cdq.c_amount', 'cdq.c_datetimeExpired', 'cdq.c_status');
    var $column_search = array('cdq.c_datetimeRequest', 'cdq.c_merchantTransactionId', 's.c_name', 'm.c_name', 'cdq.c_referenceNo', 'cdq.c_status', 'cdq.c_amount');
    var $order = array('cdq.id' => 'desc');

    /**
     * DataTables v2 Handler utilizing $this->datatables->of(...)
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_external_channel = $filters['external_channel'] ?? null;

        $dt = $this->datatables->of('cashin_dynamic_qris_mpm cdq')
            ->select("cdq.id, cdq.c_datetimeRequest, cdq.c_merchantTransactionId, cdq.c_referenceNo, 'qris_mpm' AS ref_cashinChannelId, cdq.ref_cashinExternalId, cdq.ref_cashinExternalLogQrisMpmIdCreate, cdq.c_amount, cdq.c_datetimeExpired, cdq.c_status, cdq.ref_merchantId, cdq.ref_subMerchantId, m.c_name as merchant_name, s.c_name as sub_account_name, m.c_name as name_merchant, s.c_name as name_submerchant", false)
            ->join('merchant m', 'm.id = cdq.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cdq.ref_subMerchantId', 'left')
            ->set_column_order([null, 'cdq.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'cdq.c_referenceNo', 'cdq.ref_cashinExternalId', 'cdq.c_amount', 'cdq.c_datetimeExpired', 'cdq.c_status'])
            ->set_column_search(['cdq.c_datetimeRequest', 'cdq.c_merchantTransactionId', 's.c_name', 'm.c_name', 'cdq.c_referenceNo', 'cdq.c_status', 'cdq.c_amount'])
            ->set_default_order(['cdq.id' => 'desc']);

        if ($search_name) $dt->where('cdq.ref_merchantId', $search_name);
        if ($search_external_channel) $dt->where('cdq.ref_cashinExternalId', $search_external_channel);
        if ($search_status) $dt->where('cdq.c_status', $search_status);
        if ($search_transid) $dt->where('cdq.c_merchantTransactionId', $search_transid);
        if ($search_date && $search_date_to) {
            $dt->where('cdq.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cdq.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $dt->where('cdq.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cdq.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
