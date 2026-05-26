<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QRISRecurring extends CI_Model {
    var $table = 'cashin_recurring_qris_mpm as crqm';
    var $column_order = array(null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name', 'crqm.c_merchantTransactionId', 'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status');
    var $column_search = array('crqm.c_merchantTransactionId', 'crqm.ref_merchantId', 'crqm.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('crqm.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;

    private function _apply_filters($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null)
    {
        if ($search_name) {
            $this->db->where('crqm.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $this->db->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
        }
        if ($search_date_to) {
            $this->db->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        }
        if ($search_submerchant) {
            $this->db->where('crqm.ref_subMerchantId', $search_submerchant);
        }
        if ($search_transid) {
            $search_transid = trim($search_transid);
            if ($search_transid !== '') {
                $safeTrans = $this->db->escape_str($search_transid);
                $this->db->where("(crqm.c_merchantTransactionId LIKE '$safeTrans%' OR crqm.id LIKE '$safeTrans%')");
            }
        }
        if ($search_status) {
            $this->db->where('crqm.c_status', $search_status);
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $only_ids = false, $count_only = false, $search_status = null)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        if ($count_only) {
            $this->db->select("count(crqm.id) as total");
        } else if ($only_ids) {
            $this->db->select("crqm.id");
        } else {
            $this->db->select("crqm.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join only if needed
        $isTextSearch = $searchValue && !preg_match('/^([0-9]{8,}|(GD|INV|QRIS|VA|EWALLET|BIF)[0-9a-zA-Z_-]+)/i', $searchValue);
        $joined_merchant_submerchant = false;
        if (!$only_ids && !$count_only || $search_name || $isTextSearch || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
            $joined_merchant_submerchant = true;
        }

        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status);

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            if (self::$cached_ids === null) {
                // 1. Always try finding ID matches first (Fast Indexed Lookup)
                $matching_ids = [-1];
                
                // Check in technical ID columns
                $res = $this->db->query("SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 100")->result();
                if (!empty($res)) $matching_ids = array_merge($matching_ids, array_column($res, 'id'));

                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            // 2. Decide strategy: If IDs found, use them. If not, search by Name.
            if (count($matching_ids) > 1) {
                $this->db->where_in('crqm.id', $matching_ids);
            } else {
                // FALLBACK: Name search if no specific ID matched
                if (strlen($searchValue) >= 3) {
                    // Ensure joins are present for name search fallback
                    if (!$joined_merchant_submerchant) {
                        $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
                        $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
                        $joined_merchant_submerchant = true;
                    }
                    
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

    public function get_datatables($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, true, false, $search_status);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("crqm.*, s.c_name as name_submerchant, m.c_name as name_merchant, m.c_merchantLevel", FALSE);
        $this->db->from($this->table);
        $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
        
        $this->db->where_in('crqm.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || $search_transid || $search_status || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, false, true, $search_status);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_submerchant = $filters['submerchant'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || $search_transid || $search_status || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status) : $recordsTotal;

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
    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_recurring_qris_mpm'");
        $res = $q->row();
        if ($res && isset($res->Rows) && $res->Rows > 10000) {
            self::$cached_total = (int)$res->Rows;
            return self::$cached_total;
        }

        $this->db->select('count(id) as total');
        $this->db->from($this->table);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
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
