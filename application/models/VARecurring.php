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

    private function _get_datatables_query($search_name = null, $search_date = null, $search_sub = null, $search_va = null, $search_trxid = null, $only_ids = false, $count_only = false, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        if ($count_only) {
            $this->db->select("count(crv.id) as total");
        } else if ($only_ids) {
            $this->db->select("crv.id");
        } else {
            $this->db->select("crv.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join only if needed
        $isTextSearch = $searchValue && !preg_match('/^([0-9]{8,}|(GD|INV|QRIS|VA|EWALLET|BIF)[0-9a-zA-Z_-]+)/i', $searchValue);
        $joined_merchant_submerchant = false;
        if (!$only_ids && !$count_only || $search_name || $isTextSearch || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = crv.ref_merchantId', 'left');
            $joined_merchant_submerchant = true;
        }

        if ($search_name) {
            $this->db->where('crv.ref_merchantId', $search_name);
        }
        if ($search_channel) {
            $this->db->where('crv.ref_cashinChannelId', $search_channel);
        }
        if ($search_external_channel) {
            $this->db->where('crv.ref_cashinExternalId', $search_external_channel);
        }
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            if (!empty($_SESSION['search_date_var_to'])) {
                $formatted_date_to = date('Y-m-d', strtotime($_SESSION['search_date_var_to']));
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date_to 23:59:59'");
            } else {
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date 23:59:59'");
            }
        }
        if ($search_sub) {
            $this->db->where('crv.ref_subMerchantId', $search_sub);
        }
        if ($search_va) {
            $this->db->group_start();
            $this->db->where('crv.c_vaNumber', $search_va);
            $this->db->or_where('crv.c_merchantTransactionId', $search_va);
            $this->db->group_end();
        }
        if ($search_trxid) {
            $this->db->group_start();
            $this->db->where('crv.c_merchantTransactionId', $search_trxid);
            $this->db->or_where('crv.c_vaNumber', $search_trxid);
            $this->db->group_end();
        }
        if ($search_status) {
            $this->db->where('crv.c_status', $search_status);
        }

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            if (self::$cached_ids === null) {
                // 1. Always try finding ID matches first (Fast Indexed Lookup)
                $matching_ids = [-1];
                
                // Check in technical ID columns
                $res_va = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_vaNumber LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($res_va)) $matching_ids = array_merge($matching_ids, array_column($res_va, 'id'));
                
                $res_trx = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($res_trx)) $matching_ids = array_merge($matching_ids, array_column($res_trx, 'id'));

                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            // 2. Decide strategy: If IDs found, use them. If not, search by Name.
            if (count($matching_ids) > 1) {
                $this->db->where_in('crv.id', $matching_ids);
            } else {
                // FALLBACK: Name search if no specific ID matched
                if (strlen($searchValue) >= 3) {
                    // Ensure joins are present for name search fallback
                    if (!$joined_merchant_submerchant) {
                        $this->db->join('submerchant s', 'crv.ref_subMerchantId = s.id', 'left');
                        $this->db->join('merchant m', 'crv.ref_merchantId = m.id', 'left');
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

    public function get_datatables($search_name = null, $search_date = null, $search_sub = null, $search_va = null, $search_trxid = null, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        // STEP 1: Get matching IDs only
        $this->_get_datatables_query($search_name, $search_date, $search_sub, $search_va, $search_trxid, true, false, $search_status, $search_channel, $search_external_channel);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full records for matching IDs
        $this->db->select("crv.*, s.c_name as name_submerchant, m.c_name as name_merchant, m.c_merchantLevel, cc.id AS channel_description", FALSE);
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = crv.ref_merchantId', 'left');
        $this->db->join('cashin_external_x_channel cc', 'cc.id = crv.ref_cashinChannelId', 'left');
        
        $this->db->where_in('crv.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_sub = null, $search_va = null, $search_trxid = null, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : null;
        $is_filtered = ($search_name || $search_date || $search_sub || $search_va || $search_trxid || $search_status || $search_channel || $search_external_channel || (isset($searchValue) && !empty($searchValue)));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $search_date, $search_sub, $search_va, $search_trxid, false, true, $search_status, $search_channel, $search_external_channel);
        $query = $this->db->get();
        return $query->row()->total;
    }
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

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_recurring_va'");
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

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_sub, $search_va, $search_trxid, $search_status, $search_channel, $search_external_channel);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_sub || $search_va || $search_trxid || $search_status || $search_channel || $search_external_channel || (!empty($searchValue));
        
        $recordsTotal = $this->count_all_dt($search_name, $search_date);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_sub, $search_va, $search_trxid, $search_status, $search_channel, $search_external_channel) : $recordsTotal;

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