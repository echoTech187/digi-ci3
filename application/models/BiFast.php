<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BiFast extends CI_Model {

    // DataTables variables
    var $table = 'cashout_payment_bifast cpb';
    var $column_order = array(null, 'm.c_name', 'cpb.c_datetime', 'cpb.c_merchantTransactionId', 'c.c_invoiceNo', 'cpb.ref_cashoutExternalId', 'cpb.ref_cashoutChannelId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName', 'cpb.c_amount', 'cpb.c_fee', 'cpb.c_status', null, null);
    var $column_search = array('cpb.id', 'm.c_name', 'cpb.c_merchantTransactionId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName');
    private static $cached_total = null;
    var $order = array('cpb.id' => 'desc');




    public function get_bifast($limit, $start, $date_from = null, $date_to = null, $search_name_bifast = null, $search_transid_bifast = null, $search_external_reff_id = null, $search_channel_bifast = null, $search_status_transaction_bifast = null)
    {
        $query = " FROM cashout_payment_bifast
                   JOIN cashout 
                        ON cashout.id = cashout_payment_bifast.ref_cashoutId
                   JOIN merchant 
                        ON merchant.id = cashout_payment_bifast.ref_merchantId
                   LEFT JOIN merchant_account_bank ON merchant_account_bank.c_beneficiaryAccountNo = cashout_payment_bifast.c_accountNo
                            AND merchant_account_bank.ref_cashoutChannelId = cashout_payment_bifast.ref_cashoutChannelId
                            AND merchant_account_bank.ref_merchantId = cashout_payment_bifast.ref_merchantId
                   LEFT JOIN external_paylabs_disbursement_transfer_bank 
                        ON external_paylabs_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_gvconnect_snap_disbursement_transfer_bank 
                        ON external_gvconnect_snap_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_ifp_bifast_transfer_interbank 
                        ON external_ifp_bifast_transfer_interbank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_paydgn_disbursement_transfer_bank 
                        ON external_paydgn_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   WHERE 1=1
                 ";
                 

        // Optimized: Create a lean query for counting total rows without unnecessary joins
        $lean_query = " FROM cashout_payment_bifast WHERE 1=1 ";
        if (!empty($date_from) && !empty($date_to)) {
            $lean_query .= " and cashout_payment_bifast.c_datetime >= '$date_from' AND cashout_payment_bifast.c_datetime <= '$date_to'";
        }
        if ($search_name_bifast) {
            $lean_query .= " AND cashout_payment_bifast.ref_merchantId = $search_name_bifast";
        }
        if (!empty($search_transid_bifast)) {
            $lean_query .= " AND cashout_payment_bifast.c_merchantTransactionId ='$search_transid_bifast'";
        }
        if (!empty($search_status_transaction_bifast)) {
            $lean_query .= " AND cashout_payment_bifast.c_status ='$search_status_transaction_bifast'";
        }

        $total_query = "SELECT COUNT(*) as total_rows " . $lean_query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;

        $data_query = "SELECT 
                merchant.c_name AS name_merchant,
                cashout_payment_bifast.id, 
                cashout_payment_bifast.ref_merchantId,
                cashout_payment_bifast.c_datetime, 
                cashout.c_invoiceNo, 
                cashout_payment_bifast.c_merchantTransactionId,
                cashout_payment_bifast.ref_cashoutChannelId, 
                cashout_payment_bifast.c_amount, 
                cashout_payment_bifast.c_fee, 
                cashout_payment_bifast.c_status,
                cashout_payment_bifast.c_feeExternal,
                cashout_payment_bifast.c_accountNo,
                cashout_payment_bifast.ref_cashoutExternalId,
                cashout_payment_bifast.ref_cashoutExternalLogBifastId,
                merchant_account_bank.c_beneficiaryAccountName,
                COALESCE(
                    external_paylabs_disbursement_transfer_bank.c_responseBody,
                    external_gvconnect_snap_disbursement_transfer_bank.c_responseBody,
                    external_ifp_bifast_transfer_interbank.c_responseBody,
                    external_paydgn_disbursement_transfer_bank.c_responseBody
                ) AS c_responseBody " . $query . " Order BY cashout_payment_bifast.id DESC LIMIT $start, $limit";

        $data = $this->db->query($data_query)->result();
        return [
        'total_rows' => $total_rows,
        'data' => $data
        ];
    }
    
    

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashout_payment_bifast a
        WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }

        return $this->db->query($query)->result_array();
    }

    public function getBifastDetail($id)
    {
        $query = "SELECT cashout_payment_bifast.*, cashout.*, merchant.c_name as name_merchant, merchant_account_bank.c_beneficiaryAccountName
        FROM cashout_payment_bifast 
        JOIN cashout ON cashout.id = cashout_payment_bifast.ref_cashoutId
        JOIN merchant ON merchant.id = cashout_payment_bifast.ref_merchantId
        LEFT JOIN merchant_account_bank ON merchant_account_bank.c_beneficiaryAccountNo = cashout_payment_bifast.c_accountNo
                AND merchant_account_bank.ref_cashoutChannelId = cashout_payment_bifast.ref_cashoutChannelId
                AND merchant_account_bank.ref_merchantId = cashout_payment_bifast.ref_merchantId
        WHERE cashout_payment_bifast.id = ?";

        return $this->db->query($query, array($id))->result_array();
    }
    
    public function get_merchant(){
        $query = "select id, c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_channels(){
        $query = "SELECT c_cashoutExternalId FROM cashout_external_x_channel  
                WHERE c_cashoutChannelGroup = 'bifast' AND c_status = 'Active' 
                GROUP BY c_cashoutExternalId  ";
        return $this->db->query($query)->result();
    }

    public function get_channel_mappings() {
        $query = "SELECT c_cashoutExternalId, ref_cashoutChannelId FROM cashout_external_x_channel 
                WHERE c_cashoutChannelGroup = 'bifast' AND c_status = 'Active'";
        return $this->db->query($query)->result_array();
    }

    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashout_channel 
                WHERE c_channelGroup = 'bifast' 
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogQrisMpmIdCreate) {
        
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        if ($ref_cashoutExternalId == 'gvconnect') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvconnect_snap_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } else if ($ref_cashoutExternalId == 'ifp') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_bifast_transfer_interbank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if($ref_cashoutExternalId == 'inacash'){
            $qtxt1_1    = "SELECT c_refId, c_partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_inacash_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_refId;
                $TransactionIdExternal2     = $result1_1->c_partnerRefId;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }
        } else if ($ref_cashoutExternalId == 'stm') {

            $qtxt1_1    = "SELECT client_trans_reference, refIdTransfer, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_stm_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->client_trans_reference;
                $TransactionIdExternal2     = $result1_1->refIdTransfer;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'paylabs') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'paydgn') {

            $qtxt1_1    = "SELECT c_refId, c_partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paydgn_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_refId;
                $TransactionIdExternal2     = $result1_1->c_partnerRefId;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'quantum') {

            $qtxt1_1    = "SELECT c_requestId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_quantum_bifast_transfer WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_requestId;
                $TransactionIdExternal2     = $result1_1->c_transactionId;

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
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_external_reff = $filters['external_reff'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_internal_channel = $filters['internal_channel'] ?? null;
        $search_status = $filters['search_status'] ?? null;

        $dt = $this->datatables->of('cashout_payment_bifast cpb')
            ->select('cpb.id, cpb.c_datetime, cpb.c_amount, cpb.c_fee, cpb.c_status, cpb.ref_merchantId, cpb.ref_cashoutId, cpb.ref_cashoutChannelId, m.c_name as merchant_name, c.c_invoiceNo, cpb.c_merchantTransactionId, cpb.ref_cashoutExternalId, cpb.c_accountNo, COALESCE(NULLIF(TRIM(mab.c_beneficiaryAccountName), ""), "-") as c_beneficiaryAccountName, cc.c_description as channel_name', FALSE)
            ->join('merchant m', 'm.id = cpb.ref_merchantId', 'left')
            ->join('cashout c', 'c.id = cpb.ref_cashoutId', 'left')
            ->join('cashout_channel cc', 'cc.id = cpb.ref_cashoutChannelId', 'left')
            ->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_merchantId = cpb.ref_merchantId', 'left')
            ->set_column_order([null, 'cpb.c_datetime', 'm.c_name', 'c.c_invoiceNo', 'cpb.ref_cashoutChannelId', 'cpb.c_amount', 'cpb.c_fee', 'cpb.c_status', null])
            ->set_column_search(['cpb.id', 'c.c_invoiceNo', 'm.c_name'])
            ->set_default_order(['cpb.id' => 'desc']);

        if ($search_name) $dt->where('cpb.ref_merchantId', $search_name);
        if ($search_channel) $dt->where('cpb.ref_cashoutChannelId', $search_channel);
        if ($search_status) $dt->where('cpb.c_status', $search_status);
        if ($date_from && $date_to) {
            $dt->where('cpb.c_datetime >=', date('Y-m-d', strtotime($date_from)) . ' 00:00:00')
               ->where('cpb.c_datetime <=', date('Y-m-d', strtotime($date_to)) . ' 23:59:59');
        }
        if ($search_transid) $dt->where('c.c_invoiceNo', $search_transid);

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>