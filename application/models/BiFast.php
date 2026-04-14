<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BiFast extends CI_Model {

    // DataTables variables
    var $table = 'cashout_payment_bifast cpb';
    var $column_order = array(null, 'm.c_name', 'cpb.c_datetime', 'c.c_invoiceNo', 'cpb.c_merchantTransactionId', 'cpb.ref_cashoutChannelId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName', 'cpb.c_amount', 'cpb.c_fee', 'cpb.c_status', null, null);
    var $column_search = array('cpb.id', 'm.c_name', 'c.c_invoiceNo', 'cpb.c_merchantTransactionId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName');
    var $order = array('cpb.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null)
    {
        $this->db->select("cpb.*, m.c_name AS name_merchant, c.c_invoiceNo, mab.c_beneficiaryAccountName,
                           COALESCE(epb.c_responseBody, egb.c_responseBody) AS c_responseBody");
        $this->db->from($this->table);
        $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId');
        $this->db->join('merchant m', 'm.id = cpb.ref_merchantId');
        $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
        $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');

        if ($search_name) {
            $this->db->where('cpb.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        if ($search_transid) {
            $this->db->where('cpb.c_merchantTransactionId', $search_transid);
        }
        if ($search_status) {
            $this->db->where('cpb.c_status', $search_status);
        }
        if ($search_external_reff && $search_channel) {
            if ($search_channel == "paylabs") {
                $this->db->where('epb.c_referenceNo', $search_external_reff);
            } else if ($search_channel == "gvconnect") {
                $this->db->where('egb.c_partnerReferenceNo', $search_external_reff);
            }
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_transid, $search_external_reff, $search_channel, $search_status);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null)
    {
        $this->db->select('count(cpb.id) as total');
        // Optimized: Only join what is necessary for filtering
        $this->db->from($this->table);
        $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId'); // Needed for InvoiceNo
        
        if ($search_name) $this->db->where('cpb.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        if ($search_transid) $this->db->where('cpb.c_merchantTransactionId', $search_transid);
        if ($search_status) $this->db->where('cpb.c_status', $search_status);
        
        if ($search_external_reff && $search_channel) {
            if ($search_channel == "paylabs") {
                $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id');
                $this->db->where('epb.c_referenceNo', $search_external_reff);
            } else if ($search_channel == "gvconnect") {
                $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id');
                $this->db->where('egb.c_partnerReferenceNo', $search_external_reff);
            }
        }

        // Global Search requires more joins
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $this->db->join('merchant m', 'm.id = cpb.ref_merchantId');
            $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
            
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        $this->db->select('count(cpb.id) as total');
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpb.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }


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
                    external_gvconnect_snap_disbursement_transfer_bank.c_responseBody
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

        // echo $query;
        // exit;

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
        $query = "select * from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_channels(){
        $query = "SELECT c_cashoutExternalId FROM cashout_external_x_channel  
                WHERE c_cashoutChannelGroup = 'bifast' 
                GROUP BY c_cashoutExternalId  ";
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

}
?>