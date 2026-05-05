<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VADynamic extends CI_Model {
    var $table = 'cashin_dynamic_va cdv';
    var $column_order = array(null, 'cdv.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdv.c_merchantTransactionId', 'cdv.c_vaNumber', 'cdv.ref_cashinChannelId', 'cdv.ref_cashinExternalId', 'cdv.c_amount', 'cdv.c_datetimeExpired', 'cdv.c_status');
    var $column_search = array('cdv.c_vaNumber', 'cdv.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cdv.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;

    private function _get_datatables_query($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null, $only_ids = false, $count_only = false)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        if ($count_only) {
            $this->db->select("count(cdv.id) as total");
        } else if ($only_ids) {
            $this->db->select("cdv.id");
        } else {
            $this->db->select("cdv.id, cdv.c_datetimeRequest, cdv.c_merchantTransactionId, cdv.c_vaNumber, cdv.ref_cashinChannelId, cdv.ref_cashinExternalId, cdv.c_amount, cdv.c_datetimeExpired, cdv.c_status, cdv.ref_merchantId, cdv.ref_subMerchantId, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join only if needed
        $isTextSearch = $searchValue && !preg_match('/^([0-9]{8,}|(GD|INV|QRIS|VA|EWALLET|BIF)[0-9a-zA-Z_-]+)/i', $searchValue);
        if (!$only_ids && !$count_only || $search_name || $isTextSearch || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 's.id = cdv.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = cdv.ref_merchantId', 'left');
        }

        if ($search_name) {
            $this->db->where('cdv.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            if (!empty($search_date_to)) {
                $formatted_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("cdv.c_datetimeRequest >= '$formatted_date 00:00:00' AND cdv.c_datetimeRequest <= '$formatted_date_to 23:59:59'");
            } else {
                $this->db->where("cdv.c_datetimeRequest >= '$formatted_date 00:00:00' AND cdv.c_datetimeRequest <= '$formatted_date 23:59:59'");
            }
        }
        if ($search_va) {
            $this->db->group_start();
            $this->db->where('cdv.c_vaNumber', $search_va);
            $this->db->or_where('cdv.c_merchantTransactionId', $search_va);
            $this->db->group_end();
        }
        if ($search_trxid) {
            $this->db->group_start();
            $this->db->where('cdv.c_merchantTransactionId', $search_trxid);
            $this->db->or_where('cdv.c_vaNumber', $search_trxid);
            $this->db->group_end();
        }

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            if (self::$cached_ids === null) {
                // 1. Always try finding ID matches first (Fast Indexed Lookup)
                $matching_ids = [-1];
                
                // Check in technical ID columns
                $res_va = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_vaNumber LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($res_va)) $matching_ids = array_merge($matching_ids, array_column($res_va, 'id'));
                
                $res_trx = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($res_trx)) $matching_ids = array_merge($matching_ids, array_column($res_trx, 'id'));

                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            // 2. Decide strategy: If IDs found, use them. If not, search by Name.
            if (count($matching_ids) > 1) {
                $this->db->where_in('cdv.id', $matching_ids);
            } else {
                // FALLBACK: Name search if no specific ID matched
                if (strlen($searchValue) >= 3) {
                    // Ensure joins are present for name search fallback
                    $this->db->join('submerchant s', 'cdv.ref_subMerchantId = s.id', 'left');
                    $this->db->join('merchant m', 'cdv.ref_merchantId = m.id', 'left');
                    
                    $this->db->group_start();
                    $this->db->like('s.c_name', $searchValue, 'both');
                    $this->db->or_like('m.c_name', $searchValue, 'both');
                    $this->db->group_end();
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
        }
    }

    public function get_datatables($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null)
    {
        // STEP 1: Get matching IDs only
        $this->_get_datatables_query($search_name, $search_date, $search_va, $search_trxid, $search_date_to, true);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full records for only these specific IDs
        $this->db->select("cdv.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = cdv.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = cdv.ref_merchantId', 'left');
        
        $this->db->where_in('cdv.id', $ids);
        
        // Order must be re-applied
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_va = null, $search_trxid = null, $search_date_to = null)
    {
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : null;
        $is_filtered = ($search_name || $search_date || $search_va || $search_trxid || (isset($searchValue) && !empty($searchValue)));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $search_date, $search_va, $search_trxid, $search_date_to, false, true);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_dynamic_va'");
        $res = $q->row();
        if ($res && isset($res->Rows) && $res->Rows > 10000) {
            self::$cached_total = (int)$res->Rows;
            return self::$cached_total;
        }

        $this->db->select("count(id) as total");
        $this->db->from($this->table);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_va = null, $search_trxid = null)
    {
        $this->db->select("COUNT(*) as qty, SUM(c_amount) as amount");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('ref_merchantId', $search_name);
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $formatted_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("c_datetimeRequest >= '$formatted_date 00:00:00' AND c_datetimeRequest <= '$formatted_date_to 23:59:59'");
            } else {
                $this->db->where("c_datetimeRequest >= '$formatted_date 00:00:00' AND c_datetimeRequest <= '$formatted_date 23:59:59'");
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
                $formatted_date = date('Y-m-d', strtotime($search_date_vad));
                $query .= " and cashin_dynamic_va.c_datetimeRequest >= '$formatted_date 00:00:00' AND cashin_dynamic_va.c_datetimeRequest <= '$formatted_date 23:59:59'";
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
        $this->db->select('count(cdv.id) as total');
        $this->db->from('cashin_dynamic_va cdv');
        $this->db->where('cdv.ref_merchantId', $refMerchantId);

        if ($search_date_vad) {
            $formatted_date = date('Y-m-d', strtotime($search_date_vad));
            $this->db->where('cdv.c_datetimeRequest >=', $formatted_date . ' 00:00:00');
            $this->db->where('cdv.c_datetimeRequest <=', $formatted_date . ' 23:59:59');
        }

        $query = $this->db->get();
        return $query->row()->total;
    }
    public function get_merchant(){
            $query = "select id,c_name from merchant ";
            return $this->db->query($query)->result();
        }

    public function getDataVaDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId = null) {
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
        $search_va = $filters['va_number'] ?? null;
        $search_trxid = $filters['merchant_trxid'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_va, $search_trxid, $search_date_to);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_va || $search_trxid || (!empty($searchValue));
        
        $recordsTotal = $this->count_all_dt($search_name, $search_date);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_va, $search_trxid, $search_date_to) : $recordsTotal;

        // Use Datatables Library for final processing and JSON output
        return $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>