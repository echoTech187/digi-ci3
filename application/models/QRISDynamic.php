<?php defined('BASEPATH') or exit('No direct script access allowed');

class QRISDynamic extends CI_Model
{
    var $table = 'cashin_dynamic_qris_mpm cdq';
    var $column_order = array(null, 'cdq.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'cdq.ref_cashinExternalId', 'cdq.c_amount', 'cdq.c_datetimeExpired', 'cdq.c_status');
    var $column_search = array('cdq.c_merchantTransactionId', 'cdq.ref_merchantId', 'cdq.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('cdq.id' => 'desc');

    private function _apply_filters($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        if ($search_name) {
            $this->db->where('cdq.ref_merchantId', $search_name);
        }

        if ($search_date && $search_date_to) {
            $date_from = date('Y-m-d 00:00:00', strtotime($search_date));
            $date_to = date('Y-m-d 23:59:59', strtotime($search_date_to));
            $this->db->where('cdq.c_datetimeRequest >=', $date_from);
            $this->db->where('cdq.c_datetimeRequest <=', $date_to);
        } elseif ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $this->db->where('cdq.c_datetimeRequest >=', $formatted_date . ' 00:00:00');
            $this->db->where('cdq.c_datetimeRequest <=', $formatted_date . ' 23:59:59');
        }

        if ($search_transid) {
            $this->db->where('cdq.c_merchantTransactionId', $search_transid);
        }

        if ($search_status) {
            $this->db->where('cdq.c_status', $search_status);
        }

        if ($search_reff) {
            $this->db->join('external_paydgn_qris_mpm_create epq', 'cdq.ref_cashinExternalLogQrisMpmIdCreate = epq.id');
            $this->db->where('epq.refId', $search_reff);
            $this->db->where('cdq.ref_cashinExternalId', 'paydgn');
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        $this->db->select("cdq.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = cdq.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = cdq.ref_merchantId', 'left');

        $this->_apply_filters($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);

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

    public function get_datatables($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        $this->_get_datatables_query($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        $this->_get_datatables_query($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);
        return $this->db->count_all_results();
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, null, null, null, $search_date_to);
        return $this->db->count_all_results();
    }

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null, $search_reff = null)
    {
        $this->db->select("COUNT(cdq.id) as qty, SUM(cdq.c_amount) as total_amount");
        $this->db->from($this->table);
        
        // Only join if we are filtering by submerchant or merchant name to avoid massive scan overhead
        if ($search_name) {
             $this->db->join('merchant m', 'm.id = cdq.ref_merchantId', 'left');
        }
        
        // Note: _apply_filters might join submerchant if needed, but we ensure minimal JOIN here
        $this->db->join('submerchant s', 's.id = cdq.ref_subMerchantId', 'left');

        $this->_apply_filters($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);
        $query = $this->db->get();
        return $query->row();
    }

    // public function get_qrisdynamic($limit, $start, $search_date_qd = null, $search_name_qd = null, $search_transid_qd = null, $search_status_transaction_qd = null, $search_reff_label = null)
    // {
    //     $query = "SELECT cashin_dynamic_qris_mpm.*, submerchant.c_name as name_submerchant, merchant.c_name as name_merchant
    //         FROM cashin_dynamic_qris_mpm 
    //         JOIN submerchant  on cashin_dynamic_qris_mpm.ref_subMerchantId = submerchant.id
    //         JOIN merchant on cashin_dynamic_qris_mpm.ref_merchantId = merchant.id";

    //     $query .= " WHERE 1=1 ";

    //     if (!empty($search_date_qd)) {
    //         $search_date_qd = date('Y-m-d', strtotime($search_date_qd));
    //         $query .= " AND DATE(cashin_dynamic_qris_mpm.c_datetimeRequest) = '$search_date_qd'";
    //     }

    //     if (!empty($search_name_qd)) {
    //         $query .= " AND cashin_dynamic_qris_mpm.ref_merchantId = '$search_name_qd'";
    //     }

    //     if (!empty($search_transid_qd)) {
    //         $query .= " AND cashin_dynamic_qris_mpm.c_merchantTransactionId ='$search_transid_qd'";
    //     }

    //     if (!empty($search_status_transaction_qd)) {
    //         $query .= " AND cashin_dynamic_qris_mpm.c_status ='$search_status_transaction_qd'";
    //     }

    //     if(!empty($search_reff_label)) {
            
    //     }

    //     $query .= " ORDER BY cashin_dynamic_qris_mpm.id DESC
    //             LIMIT $start, $limit";

    //     // var_dump($query);
    //     return $this->db->query($query)->result();
    // }

    public function get_qrisdynamic($limit, $start, $search_date_qd = null, $search_name_qd = null, $search_transid_qd = null, $search_status_transaction_qd = null, $search_reff_label = null)
    {
        // Base query tanpa SELECT agar bisa digunakan untuk COUNT dan SELECT
        $base_query = " FROM cashin_dynamic_qris_mpm 
                        JOIN submerchant ON cashin_dynamic_qris_mpm.ref_subMerchantId = submerchant.id";
    
        // Tambahkan join ke external_paylabs_qris_mpm_create jika $search_reff_label tidak null
        if (!empty($search_reff_label)) {
            $base_query .= " JOIN external_paydgn_qris_mpm_create 
                             ON cashin_dynamic_qris_mpm.ref_cashinExternalLogQrisMpmIdCreate = external_paydgn_qris_mpm_create.id";
        }
    
        $base_query .= " WHERE 1=1";
    
        // Tambahkan filter berdasarkan ref_cashinExternalId
        if (!empty($search_reff_label)) {
            $base_query .= " AND cashin_dynamic_qris_mpm.ref_cashinExternalId = 'paydgn'";
        }
    
        if (!empty($search_date_qd)) {
            $search_date_qd1 = date('Ymd', strtotime($search_date_qd))."000000";
            $search_date_qd2 = date('Ymd', strtotime($search_date_qd))."235959";
            $base_query .= " AND cashin_dynamic_qris_mpm.c_datetimeRequest >= '$search_date_qd1' AND cashin_dynamic_qris_mpm.c_datetimeRequest <= '$search_date_qd2'";
        }
    
        if (!empty($search_name_qd)) {
            $base_query .= " AND cashin_dynamic_qris_mpm.ref_merchantId = '$search_name_qd'";
        }
    
        if (!empty($search_transid_qd)) {
            $base_query .= " AND cashin_dynamic_qris_mpm.c_merchantTransactionId = '$search_transid_qd'";
        }
    
        if (!empty($search_status_transaction_qd)) {
            $base_query .= " AND cashin_dynamic_qris_mpm.c_status = '$search_status_transaction_qd'";
        }
    
        // Tambahkan filter untuk c_platformTradeNo jika ada
        if (!empty($search_reff_label)) {
            $base_query .= " AND external_paydgn_qris_mpm_create.refId = '$search_reff_label'";
        }
    
        // Query untuk menghitung total rows tanpa limit
        $total_query = "SELECT COUNT(*) as total_rows" . $base_query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;
    
        // Query untuk mendapatkan data dengan limit dan offset
        $data_query = "SELECT cashin_dynamic_qris_mpm.*, 
                              submerchant.c_name as name_submerchant";
    
        if (!empty($search_reff_label)) {
            $data_query .= ", external_paydgn_qris_mpm_create.refId";
        }

        $start = (is_numeric($start) && $start >= 0) ? (int)$start : 0;
        $limit = (is_numeric($limit) && $limit > 0) ? (int)$limit : 10;
        // Remove limit - Anthony
        $data_query .= $base_query . " ORDER BY cashin_dynamic_qris_mpm.id DESC
                                       LIMIT $start, $limit";
        
        $data = $this->db->query($data_query)->result();
    
        // Return hasil query beserta total rows

        return [
            'total_rows' => $total_rows,
            'data' => $data
        ];
    }

    public function get_merchant_detail($id)
    {
        $query = "SELECT c_name FROM merchant WHERE id = '$id'";

        // var_dump($query);
        // exit;

        return $this->db->query($query)->result_array();
    }
    


    public function count_qrisdynamic($refMerchantId, $search_date_qd = null)
    {
        $this->db->from('cashin_dynamic_qris_mpm cdqm');
        $this->db->join('submerchant s', 'cdqm.ref_subMerchantId = s.id');
        $this->db->where('cdqm.ref_merchantId', $refMerchantId);

        if ($search_date_qd) {
            $formatted_date = date('Y-m-d', strtotime($search_date_qd));
            $this->db->where('cdqm.c_datetimeRequest >=', $formatted_date . ' 00:00:00');
            $this->db->where('cdqm.c_datetimeRequest <=', $formatted_date . ' 23:59:59');
        }

        return $this->db->count_all_results();
    }

    public function get_merchant()
    {
        $query = "select * from merchant ";
        return $this->db->query($query)->result();
    }
    

    public function getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate) {
        
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        if ($ref_cashinExternalId == 'paylabs') {

            $qtxt1_1    = "SELECT c_platformTradeNo, c_merchantTradeNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
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

        } elseif ($ref_cashinExternalId == 'gvconnect') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceLabel, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvconnect_snap_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceLabel;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'paydgn') {

            $qtxt1_1    = "SELECT refId, partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paydgn_qris_mpm_create WHERE id='$ref_cashinExternalLogQrisMpmIdCreate'";
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