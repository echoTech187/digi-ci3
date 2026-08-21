<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QRISRecurring extends CI_Model {
    var $table = 'cashin_recurring_qris_mpm as crqm';
    var $column_order = array(null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name', 'crqm.c_merchantTransactionId', 'ref_cashinChannelId', 'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status');
    var $column_search = array('crqm.c_merchantTransactionId', 'crqm.ref_merchantId', 'crqm.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('crqm.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_submerchant = $filters['submerchant'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_external_channel = $filters['external_channel'] ?? null;

        $dt = $this->datatables->of('cashin_recurring_qris_mpm crqm')
            ->select("crqm.id, crqm.c_datetimeRequest, crqm.c_merchantTransactionId, 'qris_mpm' AS ref_cashinChannelId, crqm.ref_cashinExternalId, crqm.ref_cashinExternalLogQrisMpmIdCreate, crqm.c_amount, crqm.c_status, crqm.ref_merchantId, crqm.ref_subMerchantId, m.c_name as merchant_name, s.c_name as sub_account_name", false)
            ->join('merchant m', 'm.id = crqm.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = crqm.ref_subMerchantId', 'left')
            ->set_column_order([null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name', 'crqm.c_merchantTransactionId', null, 'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status'])
            ->set_column_search(['crqm.c_merchantTransactionId', 's.c_name', 'm.c_name'])
            ->set_default_order(['crqm.id' => 'desc']);

        if ($search_name) $dt->where('crqm.ref_merchantId', $search_name);
        if ($search_external_channel) $dt->where('crqm.ref_cashinExternalId', $search_external_channel);
        if ($search_status) $dt->where('crqm.c_status', $search_status);
        if ($search_submerchant) $dt->where('crqm.ref_subMerchantId', $search_submerchant);
        if ($search_transid) $dt->where('crqm.c_merchantTransactionId', $search_transid);
        if ($search_date && $search_date_to) {
            $dt->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $dt->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }

    public function getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId = null)
    {

        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        $ref_cashinExternalId = strtolower($ref_cashinExternalId);
        $result1_1 = false;

        if ($ref_cashinExternalId == 'ifp') {

            $qtxt1_1    = "SELECT c_orderId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
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

        } elseif ($ref_cashinExternalId == 'quantum') {

            $qtxt1_1    = "SELECT c_transactionId, c_quantumSubMerchantId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_quantum_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_transactionId;
                $TransactionIdExternal2     = $result1_1->c_quantumSubMerchantId;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'paylabs') {

            // Fallback for Paylabs: Search by parentId if log ID is missing
            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_qris_mpm_create WHERE ref_cashinRecurringQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_platformTradeNo;
                $TransactionIdExternal2     = $result1_1->c_merchantTradeNo;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'inacash' || $ref_cashinExternalId == 'paydgn') {

            $table = ($ref_cashinExternalId == 'inacash') ? 'external_inacash_qris_mpm_create' : 'external_paydgn_qris_mpm_create';
            
            // Fallback for Inacash/Paydgn: Search by parentId if log ID is missing
            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT refId, partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM $table WHERE ref_cashinRecurringQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT refId, partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM $table WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->refId;
                $TransactionIdExternal2     = $result1_1->partnerRefId;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'gvconnect' || $ref_cashinExternalId == 'gvpay') {

            // Fallback for GVConnect: Search by parentId if log ID is missing
            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceLabel, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvconnect_snap_qris_mpm_create WHERE ref_cashinRecurringQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceLabel, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvconnect_snap_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceLabel;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'yukk') {

            // Fallback for Yukk: Search by parentId if log ID is missing
            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            // Note: Since external_yukk_qris_mpm_create doesn't have ref_cashinRecurringQrisMpmId, we fallback using the provided log ID only.
            // If the log ID is somehow related to dynamic ID, we check that too, assuming parentId could match.
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_yukk_qris_mpm_create WHERE ref_cashinDynamicQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_yukk_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_referenceNo;
                $TransactionIdExternal2     = $result1_1->c_partnerReferenceNo;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'ezeelink') {

            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT c_transactionId, c_transactionCode, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ezeelink_qris_mpm_create WHERE ref_cashinDynamicQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT c_transactionId, c_transactionCode, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ezeelink_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {
                $TransactionIdExternal1     = $result1_1->c_transactionId;
                $TransactionIdExternal2     = $result1_1->c_transactionCode;
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;
                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } elseif ($ref_cashinExternalId == 'stm') {

            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT qris_reff_code, client_reference, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_stm_qris_mpm_create WHERE ref_cashinDynamicQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT qris_reff_code, client_reference, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_stm_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {
                $TransactionIdExternal1     = $result1_1->qris_reff_code;
                $TransactionIdExternal2     = $result1_1->client_reference;
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;
                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } elseif ($ref_cashinExternalId == 'paylabs2') {

            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';
            
            if ($isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs2_qris_mpm_create WHERE ref_cashinRecurringQrisMpmId='$parentId'";
            } else {
                $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs2_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            }

            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if ($result1_1) {
                $TransactionIdExternal1     = $result1_1->c_platformTradeNo;
                $TransactionIdExternal2     = $result1_1->c_merchantTradeNo;
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

    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }
}
?>
