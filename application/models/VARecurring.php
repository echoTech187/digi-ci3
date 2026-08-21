<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VARecurring extends CI_Model {
    var $table = 'cashin_recurring_va crv';
    var $column_order = array(null, 'crv.c_datetimeRequest', 'm.c_name', 's.c_name', 'crv.c_merchantTransactionId', 'crv.c_vaNumber', 'crv.ref_cashinChannelId', 'crv.ref_cashinExternalId', 'crv.c_amount', 'crv.c_status');
    var $column_search = array('crv.c_vaNumber', 'crv.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('crv.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_va = null, $search_trxid = null)
    {
        $this->db->select("COUNT(crv.id) as qty, SUM(crv.c_amount) as total_amount");
        $this->db->from($this->table);
        
        if ($search_name) {
            $this->db->join('merchant m', 'crv.ref_merchantId = m.id', 'left');
            $this->db->where('crv.ref_merchantId', $search_name);
        }
        
        if ($search_date) {
            $search_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $search_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("crv.c_datetimeRequest >= '$search_date 00:00:00'");
                $this->db->where("crv.c_datetimeRequest <= '$search_date_to 23:59:59'");
            } else {
                $this->db->where("crv.c_datetimeRequest >= '$search_date 00:00:00'");
                $this->db->where("crv.c_datetimeRequest <= '$search_date 23:59:59'");
            }
        }

        return $this->db->get()->row();
    }

    public function get_varecurring($limit, $start, $search_date_var = null, $search_name_var= null, $search_submerchant_var= null) {
        $query = "SELECT crv.*, s.c_name as name_submerchant, merchant.c_name as name_merchant
                 from cashin_recurring_va crv 
                 join submerchant s on s.id = crv.ref_subMerchantId
                 left join merchant on crv.ref_merchantId = merchant.id";

        $query .= " WHERE 1=1 ";

        if ($search_date_var) {
                $formatted_date = date('Y-m-d', strtotime($search_date_var));
                $query .= " AND crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date 23:59:59'";
            }

        if ($search_name_var) {
            $query .= " AND merchant.id = $search_name_var";
        }
        if ($search_submerchant_var) {
            $query .= " AND s.id LIKE $search_submerchant_var";
        }
        $query .= " ORDER BY crv.id DESC
                    LIMIT $start, $limit";

                    // var_dump($query);
        return $this->db->query($query)->result();
    }

    public function count_varecurring($refMerchantId, $search_date_var = null) {
        $this->db->select('count(crv.id) as total');
        $this->db->from('cashin_recurring_va crv');
        $this->db->where('crv.ref_merchantId', $refMerchantId);

        if ($search_date_var) {
            $formatted_date = date('Y-m-d', strtotime($search_date_var));
            $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00'");
            $this->db->where("crv.c_datetimeRequest <= '$formatted_date 23:59:59'");
        }

        $query = $this->db->get();
        return $query->row()->total;
    }
        
    public function get_merchant(){
            $query = "select id,c_name from merchant ";
            return $this->db->query($query)->result();
        }

    public function getDataVaRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId = null) {
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
            $qtxt1_1    = "SELECT c_orderId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_va_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
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
            $qtxt1_1    = "SELECT c_customId, c_vaNumber, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvpay_va_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                $TransactionIdExternal1     = $result1_1->c_customId;
                $TransactionIdExternal2     = $result1_1->c_vaNumber;
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;
                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }
        } elseif ($ref_cashinExternalId == 'quantum') {
            $qtxt1_1    = "SELECT c_transactionId, c_quantumSubMerchantId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_quantum_qris_mpm_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
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
        } elseif ($ref_cashinExternalId == 'inacash' || $ref_cashinExternalId == 'paydgn') {
            $table = ($ref_cashinExternalId == 'inacash') ? 'external_inacash_qris_mpm_create' : 'external_paydgn_qris_mpm_create';
            $qtxt1_1    = "SELECT refId, partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM $table WHERE id='$ref_cashinExternalLogVaIdCreate'";
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
        } elseif ($ref_cashinExternalId == 'paylabs') {
            $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_qris_mpm_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
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
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_sub = $filters['submerchant'] ?? null;
        $search_va = $filters['va_number'] ?? null;
        $search_trxid = $filters['merchant_trxid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_external_channel = $filters['external_channel'] ?? null;

        $dt = $this->datatables->of('cashin_recurring_va crv')
            ->select('crv.id, crv.c_datetimeRequest, crv.c_merchantTransactionId, crv.c_vaNumber, crv.ref_cashinChannelId, crv.ref_cashinExternalId, crv.ref_cashinExternalLogVaIdCreate, crv.c_status, crv.ref_merchantId, crv.ref_subMerchantId, m.c_name as merchant_name, s.c_name as sub_account_name', FALSE)
            ->join('merchant m', 'm.id = crv.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left')
            ->set_column_order([null, 'crv.c_datetimeRequest', 'm.c_name', 's.c_name', 'crv.c_merchantTransactionId', 'crv.c_vaNumber', 'crv.ref_cashinChannelId', 'crv.ref_cashinExternalId', 'crv.c_status'])
            ->set_column_search(['crv.c_vaNumber', 'crv.c_merchantTransactionId', 's.c_name', 'm.c_name'])
            ->set_default_order(['crv.id' => 'desc']);

        if ($search_name) $dt->where('crv.ref_merchantId', $search_name);
        if ($search_sub) $dt->where('crv.ref_subMerchantId', $search_sub);
        if ($search_channel) $dt->where('crv.ref_cashinChannelId', $search_channel);
        if ($search_external_channel) $dt->where('crv.ref_cashinExternalId', $search_external_channel);
        if ($search_status) $dt->where('crv.c_status', $search_status);
        if ($search_va) $dt->where('crv.c_vaNumber', $search_va);
        if ($search_trxid) $dt->where('crv.c_merchantTransactionId', $search_trxid);
        if ($search_date) {
            $dt->where('crv.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('crv.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
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