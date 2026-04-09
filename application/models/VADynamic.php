<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VADynamic extends CI_Model {
    var $table = 'cashin_dynamic_va cdv';
    var $column_order = array(null, 'cdv.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdv.c_merchantTransactionId', 'cdv.ref_cashinChannelId', 'cdv.ref_cashinExternalId', 'cdv.c_vaNumber', 'cdv.c_amount', 'cdv.c_datetimeExpired', 'cdv.c_status');
    var $column_search = array('cdv.c_vaNumber', 'cdv.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cdv.id' => 'desc');

    private function _get_datatables_query($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null)
    {
        $this->db->select("cdv.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = cdv.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = cdv.ref_merchantId', 'left');

        if ($search_name) {
            $this->db->where('cdv.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $search_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $search_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("DATE(cdv.c_datetimeRequest) BETWEEN '$search_date' AND '$search_date_to'");
            } else {
                $this->db->where('DATE(cdv.c_datetimeRequest)', $search_date);
            }
        }
        if ($search_va) {
            $this->db->where('cdv.c_vaNumber', $search_va);
        }
        if ($search_trxid) {
            $this->db->where('cdv.c_merchantTransactionId', $search_trxid);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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

    public function get_datatables($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null)
    {
        $this->_get_datatables_query($search_name, $search_date, $search_va, $search_trxid, $search_date_to);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null)
    {
        $this->_get_datatables_query($search_name, $search_date, $search_va, $search_trxid, $search_date_to);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cdv.ref_merchantId', $search_name);
        if ($search_date) {
            $search_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $search_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("DATE(cdv.c_datetimeRequest) BETWEEN '$search_date' AND '$search_date_to'");
            } else {
                $this->db->where('DATE(cdv.c_datetimeRequest)', $search_date);
            }
        }
        return $this->db->count_all_results();
    }

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_va = null, $search_trxid = null)
    {
        $this->db->select("COUNT(*) as qty, SUM(c_amount) as amount");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('ref_merchantId', $search_name);
        if ($search_date) {
            $search_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $search_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("DATE(c_datetimeRequest) BETWEEN '$search_date' AND '$search_date_to'");
            } else {
                $this->db->where('DATE(c_datetimeRequest)', $search_date);
            }
        }
        if ($search_va) $this->db->where('c_vaNumber', $search_va);
        if ($search_trxid) $this->db->where('c_merchantTransactionId', $search_trxid);

        return $this->db->get()->result_array();
    }

    public function get_vadynamic($limit, $start, $search_date_vad = null, $search_name_vad= null, $search_submerchant_vad= null, $search_va_number = null, $search_merchant_trxid = null) {
        $query = "FROM cashin_dynamic_va
                  JOIN submerchant s on s.id  = cashin_dynamic_va.ref_subMerchantId 
                  left join merchant on cashin_dynamic_va.ref_merchantId = merchant.id";

        $query .= " WHERE 1=1 ";
        if ($search_date_vad) {
                $search_date_vad = date('Y-m-d', strtotime($search_date_vad));
                $query .= " and DATE(cashin_dynamic_va.c_datetimeRequest) = '$search_date_vad'";
            }

        if ($search_name_vad) {
                $query .= " and merchant.id = $search_name_vad";
            }

        if ($search_submerchant_vad) {
                $query .= " and s.id = $search_submerchant_vad";
            }

        if (!empty($search_va_number)) {
            $query .= " and cashin_dynamic_va.c_vaNumber = '$search_va_number'";
        }

        if (!empty($search_merchant_trxid)) {
            $query .= " and cashin_dynamic_va.c_merchantTransactionId = '$search_merchant_trxid'";
        }
        
        $start = (is_numeric($start) && $start >= 0) ? (int)$start : 0;
        $limit = (is_numeric($limit) && $limit > 0) ? (int)$limit : 10;

        $total_query = "SELECT COUNT(*) as total_rows " . $query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;

        $data_query = "SELECT cashin_dynamic_va.*, s.c_name, merchant.c_name as name_merchant " . $query . " ORDER BY cashin_dynamic_va.id DESC LIMIT $start, $limit";

        $data = $this->db->query($data_query)->result();

        return [
        'total_rows' => $total_rows,
        'data' => $data
        ];
    }

    public function count_vadynamic($refMerchantId, $search_date_vad = null) {
        $query = "SELECT cashin_dynamic_va.id 
                FROM cashin_dynamic_va
                JOIN submerchant s on s.id  = cashin_dynamic_va.ref_subMerchantId 
                WHERE cashin_dynamic_va.ref_merchantId = $refMerchantId";

        if ($search_date_vad) {
            $search_date_vad = date('Y-m-d', strtotime($search_date_vad));
            $query .= " AND DATE(cashin_dynamic_va.c_datetimeRequest) = '$search_date_vad'";
        }

        return $this->db->query($query)->num_rows();
        }
    public function get_merchant(){
            $query = "select * from merchant ";
            return $this->db->query($query)->result();
        }

    public function getDataVaDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate) {
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

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