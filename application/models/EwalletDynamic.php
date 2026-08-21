<?php defined('BASEPATH') or exit('No direct script access allowed');

class EwalletDynamic extends CI_Model
{
    var $table = 'cashin_dynamic_ewallet cde';
    var $column_order = array(null, 'cde.c_datetimeRequest', 's.c_name', 'cde.ref_cashinChannelId', 'cde.c_merchantTransactionId', 'cde.ref_cashinExternalId', 'cde.c_amount', 'cde.c_datetimeExpired', 'cde.c_status', null);
    var $column_search = array('cde.c_merchantTransactionId', 'cde.ref_merchantId', 'cde.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('cde.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;



    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $this->db->select("COUNT(*) as qty, SUM(c_amount) as total_trx");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cde.ref_merchantId', $search_name);
        if ($search_date) $this->db->where('cde.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
        if ($search_date_to) $this->db->where('cde.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        if ($search_transid) $this->db->where('cde.c_merchantTransactionId', $search_transid);
        if ($search_status) $this->db->where('cde.c_status', $search_status);
        return $this->db->get()->row();
    }



    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate) {
        
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        $ref_cashinExternalId = strtolower($ref_cashinExternalId);

        if ($ref_cashinExternalId == 'ifp') {

            $qtxt1_1    = "SELECT c_orderId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_ewallet_create WHERE id='$ref_cashinExternalLogEwalletIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_orderId;
                $TransactionIdExternal2     = $result1_1->c_transactionId;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'gvpay' || $ref_cashinExternalId == 'gvconnect') {

            $qtxt1_1    = "SELECT c_customId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvpay_ewallet_create WHERE id='$ref_cashinExternalLogEwalletIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_customId;
                $TransactionIdExternal2     = '-';
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        }

        return array(
                    'TransactionIdExternal1'    => $TransactionIdExternal1, 
                    'TransactionIdExternal2'    => $TransactionIdExternal2, 
                    'RequestDatetime'           => $DatetimeRequest, 
                    'RequestHeader'             => json_decode($RequestHeader, true),
                    'RequestBody'               => json_decode($RequestBody, true),
                    'ResponseDatetime'          => $DatetimeResponse,
                    'ResponseHeader'            => json_decode($ResponseHeader, true),
                    'ResponseBody'              => json_decode($ResponseBody, true)
                );
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_external_channel = $filters['external_channel'] ?? null;

        $dt = $this->datatables->of('cashin_dynamic_ewallet cde')
            ->select('cde.id, cde.c_datetimeRequest, cde.c_merchantTransactionId, cde.ref_cashinChannelId, cde.ref_cashinExternalId, cde.c_amount, cde.c_datetimeExpired, cde.c_status, cde.ref_merchantId, cde.ref_subMerchantId, cde.ref_cashinExternalLogEwalletIdCreate, m.c_name as merchant_name, s.c_name as sub_account_name', FALSE)
            ->join('merchant m', 'm.id = cde.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = cde.ref_subMerchantId', 'left')
            ->set_column_order([null, 'cde.c_datetimeRequest', 'm.c_name', 's.c_name', 'cde.c_merchantTransactionId', 'cde.ref_cashinChannelId', 'cde.ref_cashinExternalId', 'cde.c_amount', 'cde.c_datetimeExpired', 'cde.c_status'])
            ->set_column_search(['cde.c_merchantTransactionId', 's.c_name', 'm.c_name'])
            ->set_default_order(['cde.id' => 'desc']);

        if ($search_name) $dt->where('cde.ref_merchantId', $search_name);
        if ($search_channel) $dt->where('cde.ref_cashinChannelId', $search_channel);
        if ($search_external_channel) $dt->where('cde.ref_cashinExternalId', $search_external_channel);
        if ($search_status) $dt->where('cde.c_status', $search_status);
        if ($search_transid) $dt->where('cde.c_merchantTransactionId', $search_transid);
        if ($search_date && $search_date_to) {
            $dt->where('cde.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cde.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $dt->where('cde.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('cde.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->addColumn('simulation', function($row) {
                return '<button type="button" class="btn btn-sm btn-dt-action btn-dt-secondary detailEwalletDynamicChannelExternalAjax" 
                        data-merchanttransactionid="' . $row->c_merchantTransactionId . '" 
                        data-ref_cashinexternalid="' . $row->ref_cashinExternalId . '" 
                        data-ref_cashinexternallogewalletidcreate="' . $row->ref_cashinExternalLogEwalletIdCreate . '">
                        <i class="fas fa-info-circle mr-1"></i> DETAILS
                    </button>';
            })
            ->make(true);
    }
}
?>