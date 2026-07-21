<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qris extends CI_Model {

    // DataTables variables
    var $table = 'cashin_payment_qris_mpm cpq';
    var $column_order = array(null, 'cpq.c_datetime', 'm.c_name', 's.c_name', 'Merchant_Transaction_Id', 'c.c_invoiceNo', 'cpq.c_type', 'cpq.c_amount', 'cpq.c_mdr', 'cpq.c_fee', 'cpq.c_issuerRrn', 'cpq.c_isSettlementRealtime', 'cpq.c_datetimeSettlement', null); 
    var $column_search = array('cpq.id', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'crq.c_merchantTransactionId', 'cpq.c_issuerRrn');
    var $order = array('cpq.id' => 'desc');
    private static $cached_total = null;

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null, $only_ids = false, $count_only = false, $force_reverse = false)
    {
        $this->db->query("SET SESSION max_execution_time = 30000");
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(*) as total");
        } else if ($only_ids) {
            $this->db->select("cpq.id");
        } else {
            $this->db->select("cpq.id, cpq.c_datetime, cpq.c_type, cpq.c_amount, cpq.c_mdr, cpq.c_fee, cpq.c_isSettlementRealtime, cpq.c_datetimeSettlement, cpq.ref_merchantId, cpq.ref_subMerchantId, cpq.ref_cashinId, cpq.ref_cashinDynamicQrisMpmId, cpq.ref_cashinRecurringQrisMpmId, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                               IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $isInvoiceSearch = (preg_match('/^(INV|EWALLET|QRIS|VA|BIF|BIFAST)/i', $searchValue));
        $isTechnicalId = preg_match('/^([0-9]{2,30}|[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-.*|(GD|GR|EWALLET|QRIS|VA|BIF|INV|BIFAST|UT)[0-9a-zA-Z_-]+|0000[0-9a-fA-F]+|[a-zA-Z0-9_-]{10,})$/i', $searchValue);
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Optimization: Use joins only if global search is active...
        $isExternalSort = !empty($sort_col) && !preg_match('/^cpq\./', $sort_col) && $sort_col != 'Merchant_Transaction_Id';
        
        // When fetching ONLY IDs, we DO NOT need full joins for global search anymore because we use subqueries!
        $needFullJoins = (!$only_ids && !$count_only) || $isExternalSort;

        // Join cashin only if searching for invoice via global search, sorting by it, or getting full data
        if ($needFullJoins || $isInvoiceSearch || $isTechnicalId) {
            $this->db->join('cashin c', 'c.id = cpq.ref_cashinId');
        }
        
        // Join merchant and submerchant only if needed for global search or sorting or full data
        $isTextSearch = $searchValue && !preg_match('/^[0-9]{5,25}$/', $searchValue) && !$isInvoiceSearch;
        $joined_merchant_submerchant = false;
        if ($needFullJoins || $search_name || $isTextSearch) {
            $this->db->join('merchant m', 'cpq.ref_merchantId = m.id');
            $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id');
            $joined_merchant_submerchant = true;
        }

        // Transactions ID joins (Only if full data, NEVER during ID fetch to prevent timeouts)
        if ($needFullJoins) {
            $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
            $this->db->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        }

        if ($search_name) {
            $this->db->where('cpq.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        if ($search_rrn && !$searchValue) {
            $matching_rrn_ids = $this->_get_ids_by_rrn($search_rrn);
            if (!empty($matching_rrn_ids)) {
                $this->db->where_in('cpq.id', $matching_rrn_ids);
            } else {
                $this->db->where('1=0', NULL, FALSE);
            }
        }
        if ($search_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_settlement));
            $this->db->where('cpq.c_datetimeSettlement >=', $formatted_date . " 00:00:00");
            $this->db->where('cpq.c_datetimeSettlement <=', $formatted_date . " 23:59:59");
        }
        if ($search_transid && !$searchValue) {
            $safeTransId = $this->db->escape_str($search_transid);
            $matching_ids = [-1]; // Defaults to -1 to force empty result if not found
            
            $cdq_res = $this->db->query("
                SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId = '$safeTransId'
                LIMIT 20
            ")->result();
            if (!empty($cdq_res)) {
                $cdq_ids = array_column($cdq_res, 'id');
                $cpq_res = $this->db->query("SELECT id FROM cashin_payment_qris_mpm WHERE ref_cashinDynamicQrisMpmId IN (".implode(',', $cdq_ids).") LIMIT 20")->result();
                $matching_ids = array_merge($matching_ids, array_column($cpq_res, 'id'));
            }
            
            $crq_res = $this->db->query("
                SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId = '$safeTransId'
                LIMIT 20
            ")->result();
            if (!empty($crq_res)) {
                $crq_ids = array_column($crq_res, 'id');
                $cpq_res = $this->db->query("SELECT id FROM cashin_payment_qris_mpm WHERE ref_cashinRecurringQrisMpmId IN (".implode(',', $crq_ids).") LIMIT 20")->result();
                $matching_ids = array_merge($matching_ids, array_column($cpq_res, 'id'));
            }
            
            $this->db->where_in('cpq.id', array_unique($matching_ids));
        }
        if ($search_invoice && !$searchValue) {
            // Fast invoice lookup via subquery
            $this->db->where("cpq.ref_cashinId IN (SELECT id FROM cashin WHERE c_invoiceNo = '".$this->db->escape_str($search_invoice)."')", NULL, FALSE);
        }

        if ($searchValue) {
            $safeSearchValue = $this->db->escape_str($searchValue);
            
            // CACHING LOGIC: Prevent redundant scans across multiple calls (count + fetch)
            static $cached_ids = null;
            static $cached_inv_ids = null;
            static $last_query = null;

            if ($cached_ids === null || $last_query !== $searchValue) {
                $last_query = $searchValue;
                $matching_ids = [-1];
                $matching_inv_ids = [-1];

                $op = (strlen($searchValue) >= 15) ? '=' : 'LIKE';
                $val = (strlen($searchValue) >= 15) ? "'$safeSearchValue'" : "'$safeSearchValue%'";

                // 1. Priority: Check Transaction ID from Dynamic/Recurring (Often specific and indexed)
                $cdq_res = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 20")->result();
                if (!empty($cdq_res)) {
                    $cdq_ids = array_column($cdq_res, 'id');
                    $cpq_res = $this->db->query("SELECT id FROM cashin_payment_qris_mpm WHERE ref_cashinDynamicQrisMpmId IN (".implode(',', $cdq_ids).") LIMIT 50")->result();
                    if (!empty($cpq_res)) $matching_ids = array_merge($matching_ids, array_column($cpq_res, 'id'));
                }
                
                $crq_res = $this->db->query("SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 20")->result();
                if (!empty($crq_res)) {
                    $crq_ids = array_column($crq_res, 'id');
                    $cpq_res = $this->db->query("SELECT id FROM cashin_payment_qris_mpm WHERE ref_cashinRecurringQrisMpmId IN (".implode(',', $crq_ids).") LIMIT 50")->result();
                    if (!empty($cpq_res)) $matching_ids = array_merge($matching_ids, array_column($cpq_res, 'id'));
                }
                
                // Ext Ref IDs removed (columns don't exist on dynamic/recurring tables)

                // 2. Check Invoice Number (Only if specific ID not found OR query is short)
                // CRITICAL: Removed leading % to prevent full table scan on 80M+ rows
                if (count($matching_ids) <= 1 || strlen($searchValue) < 15) {
                    if (strlen($searchValue) >= 4) {
                        $inv_q = "SELECT id FROM cashin WHERE c_invoiceNo $op $val ";
                        $inv_res = $this->db->query($inv_q . " LIMIT 50")->result();
                        if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
                    }
                }

                // 3. Check RRN via helper
                if (count($matching_ids) <= 1) {
                    $cpq_rrn_ids = $this->_get_ids_by_rrn($safeSearchValue);
                    if (!empty($cpq_rrn_ids)) {
                        $matching_ids = array_merge($matching_ids, $cpq_rrn_ids);
                    }
                }

                // 4. Check Direct PK
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                $cached_ids = array_unique($matching_ids);
                $cached_inv_ids = array_unique($matching_inv_ids);
            }

            // 2. Decide strategy
            if (count($cached_ids) > 1 || count($cached_inv_ids) > 1) {
                $this->db->group_start();
                if (count($cached_ids) > 1) $this->db->where_in('cpq.id', $cached_ids);
                if (count($cached_inv_ids) > 1) {
                    if (count($cached_ids) > 1) $this->db->or_where_in('cpq.ref_cashinId', $cached_inv_ids);
                    else $this->db->where_in('cpq.ref_cashinId', $cached_inv_ids);
                }
                $this->db->group_end();
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    // Ensure joins for name search
                    if (!$joined_merchant_submerchant) {
                        $this->db->join('merchant m', 'cpq.ref_merchantId = m.id', 'left');
                        $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id', 'left');
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
            // No grouping needed as ref_cashinId is unique
            
            if (isset($_POST['order'])) {
                $sort_idx = $_POST['order']['0']['column'];
                $sort_col = $this->column_order[$sort_idx];
                $dir = $_POST['order']['0']['dir'];
                
                if ($sort_col == 'Merchant_Transaction_Id') {
                    if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
                    $this->db->order_by("IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId)", $dir);
                } else if ($only_ids && ($sort_col == 'cpq.id' || $sort_col == 'id')) {
                    $this->db->order_by('id', $dir, FALSE);
                } else if ($sort_col) {
                    if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
                    $this->db->order_by($sort_col, $dir);
                }
            } else if (isset($this->order)) {
                $order = $this->order;
                $key = key($order);
                $dir = $order[$key];
                if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
                
                if ($only_ids && ($key == 'cpq.id' || $key == 'id')) {
                    $this->db->order_by('id', $dir, FALSE);
                } else {
                    $this->db->order_by($key, $dir);
                }
            }
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $start = (int) $_POST['start'];
        $length = (int) $_POST['length'];
        
        // 1. Get filtered count to see if we are in the "deep half"
        $total = $this->count_filtered($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid);
        
        $force_reverse = false;
        $fetch_start = $start;
        $fetch_length = $length;

        // Optimization: If we are deep into the table (>50%), scan from the other end.
        // This makes "Last Page" as fast as "First Page".
        if ($total > 5000 && $start > ($total / 2)) {
            $force_reverse = true;
            $fetch_start = $total - $start - $length;
            if ($fetch_start < 0) {
                $fetch_length = $length + $fetch_start;
                $fetch_start = 0;
            }
        }

        // STEP 1: Get only IDs matching filters/pagination (Fast)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, true, false, $force_reverse);
        if ($length != -1)
            $this->db->limit($fetch_length, $fetch_start);
            
        $query = $this->db->get();
        if (!is_object($query)) return array();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full records for only these specific IDs
        $this->db->select("cpq.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                           IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId) AS Merchant_Transaction_Id,
                           IF(cpq.c_type='Dynamic', cdq.ref_cashinExternalId, crq.ref_cashinExternalId) AS ref_cashinExternalId");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpq.ref_cashinId', 'left');
        $this->db->join('submerchant s', 'cpq.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cpq.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_qris_mpm cdq', 'cdq.id = cpq.ref_cashinDynamicQrisMpmId', 'left');
        $this->db->join('cashin_recurring_qris_mpm crq', 'crq.id = cpq.ref_cashinRecurringQrisMpmId', 'left');
        
        $this->db->where_in('cpq.id', $ids);
        
        // Re-apply sorting to maintain order during fetch
        if (isset($_POST['order'])) {
            $sort_idx = $_POST['order']['0']['column'];
            $sort_col = $this->column_order[$sort_idx];
            $dir = $_POST['order']['0']['dir'];
            if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');

            if ($sort_col == 'Merchant_Transaction_Id') {
                $this->db->order_by("IF(cpq.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId)", $dir);
            } else if ($sort_col) {
                $this->db->order_by($sort_col, $dir);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $key = key($order);
            $dir = $order[$key];
            if ($force_reverse) $dir = ($dir == 'asc' ? 'desc' : 'asc');
            $this->db->order_by($key, $dir);
        }
        
        $query = $this->db->get();
        $final_results = is_object($query) ? $query->result() : array();

        // If we used a reverse scan, we must flip the results back to the original intended order
        if ($force_reverse && !empty($final_results)) {
            $final_results = array_reverse($final_results);
        }

        if (!empty($final_results)) {
            $final_results = $this->_enrich_with_rrns($final_results);
        }

        return $final_results;
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_settlement = null, $search_rrn = null, $search_invoice = null, $search_transid = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_settlement || $search_rrn || $search_invoice || $search_transid || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_settlement, $search_rrn, $search_invoice, $search_transid, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    /**
     * Optimized total count for large datasets (23M+ rows).
     * Uses table metadata instead of real-time count to prevent timeouts.
     */
    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // If no filters, use the fastest possible estimate from metadata (Instant)
        if (!$search_name && !$date_from && !$date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_payment_qris_mpm'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int)$res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(*) as total");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpq.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpq.c_datetime >=', $date_from);
            $this->db->where('cpq.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }


    public function get_qris($limit, $start, $search_date_qris = null, $search_date_qris_to = null, $search_name_qris = null, $search_date_qris_settlement = null, $search_rrn = null, $search_transactionid_ht = null)
    {

        $query = "SELECT 
        merchant.c_name as name_merchant,
        cashin_payment_qris_mpm.id, 
        cashin_payment_qris_mpm.c_datetime, 
        submerchant.c_name as name_submerchant, 
        cashin.c_invoiceNo, 
        cashin_payment_qris_mpm.c_type,
        cashin_payment_qris_mpm.ref_merchantId, 
        cashin_payment_qris_mpm.ref_subMerchantId, 
        cashin_payment_qris_mpm.c_amount, 
        cashin_payment_qris_mpm.c_mdr, 
        cashin_payment_qris_mpm.c_fee,
        cashin_payment_qris_mpm.c_datetimePayment,        cashin_payment_qris_mpm.c_isSettlementRealtime, 
        cashin_payment_qris_mpm.c_datetimeSettlement, cashin_payment_qris_mpm.c_isSettlementRealtimeExternal, 
        cashin_payment_qris_mpm.c_feeExternal, cashin_payment_qris_mpm.c_datetimeSettlementExternal,
        IF(cashin_payment_qris_mpm.c_type='Dynamic', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
        FROM cashin_payment_qris_mpm 
        JOIN cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
        JOIN submerchant on cashin_payment_qris_mpm.ref_subMerchantId = submerchant.id 
        JOIN merchant on cashin_payment_qris_mpm.ref_merchantId = merchant.id
        LEFT JOIN cashin_dynamic_qris_mpm on cashin_dynamic_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinDynamicQrisMpmId
        LEFT JOIN cashin_recurring_qris_mpm on cashin_recurring_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinRecurringQrisMpmId";

        $query .= " WHERE 1=1 ";
        

        if (!empty($search_name_qris)) {
            $query .= " and cashin_payment_qris_mpm.ref_merchantId = '$search_name_qris'";
        }

        if (!empty($search_date_qris) && !empty($search_date_qris_to)) {
            // $search_date_qris = date('Y-m-d', strtotime($search_date_qris));
            $query .= " and cashin_payment_qris_mpm.c_datetime >= '$search_date_qris' AND cashin_payment_qris_mpm.c_datetime <= '$search_date_qris_to'";
        }

        if (!empty($search_date_qris_settlement)) {
            $formatted_date = date('Y-m-d', strtotime($search_date_qris_settlement));
            $query .= " and cashin_payment_qris_mpm.c_datetimeSettlement >= '$formatted_date 00:00:00' AND cashin_payment_qris_mpm.c_datetimeSettlement <= '$formatted_date 23:59:59'";
        }

        if (!empty($search_transactionid_ht)) {
            $query .= " and cashin_dynamic_qris_mpm.c_merchantTransactionId= '$search_transactionid_ht'
                        and cashin_dynamic_qris_mpm.c_status = 'Paid'";
        }

        if (!empty($search_rrn)) {
            $matching_ids = $this->_get_ids_by_rrn($search_rrn);
            if (!empty($matching_ids)) {
                $query .= " AND cashin_payment_qris_mpm.id IN (" . implode(',', $matching_ids) . ")";
            } else {
                $query .= " AND 1=0";
            }
        }

        $query .= " ORDER BY cashin_payment_qris_mpm.id DESC
                    LIMIT $start, $limit";

        $res = $this->db->query($query)->result();
        if (!empty($res)) {
            $res = $this->_enrich_with_rrns($res);
        }
        return $res;
    }

    public function get_merchant_detail($id)
    {
        $query = "SELECT c_name FROM merchant WHERE id = '$id'";
        return $this->db->query($query)->result_array();
    }

    public function count_qris($refMerchantId, $search_date_qris = null)
    {
        $this->db->from('cashin_payment_qris_mpm');
        $this->db->join('cashin', 'cashin.id = cashin_payment_qris_mpm.ref_cashinId');
        $this->db->join('merchant', 'merchant.id = cashin_payment_qris_mpm.ref_merchantId');
        $this->db->join('submerchant', 'submerchant.id = cashin_payment_qris_mpm.ref_subMerchantId');
        $this->db->where('cashin_payment_qris_mpm.ref_merchantId', $refMerchantId);

        if ($search_date_qris) {
            $this->db->where('cashin_payment_qris_mpm.c_datetime', $search_date_qris);
        }

        return $this->db->count_all_results();
    }

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashin_payment_qris_mpm a
        WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }
        return $this->db->query($query)->result_array();
    }

    public function monthly_qris() {
        $year = date('Y');
        $query = "SELECT MONTH(c_datetime) AS month, SUM(c_amount) AS amount
                  FROM cashin_payment_qris_mpm 
                  WHERE c_datetime >= '$year-01-01 00:00:00' AND c_datetime <= '$year-12-31 23:59:59'
                  GROUP BY MONTH(c_datetime)
                  ORDER BY month";
        return $this->db->query($query)->result_array();
    }

    // public function qris_detail($id)
    // {
    //     $query = "SELECT cashin_payment_qris_mpm.*, cashin.*, cashin_payment_qris_mpm.*, m.id as id_merchant, m.c_name AS name_merchant, s.id as id_submerchant, s.c_name AS name_submerchant, 
    //     cashin_dynamic_qris_mpm.*, cashin_recurring_qris_mpm.*,
    //     IF(cashin_payment_qris_mpm.c_type='Dynamic ', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
    //     FROM cashin_payment_qris_mpm 
    //     join cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
    //     JOIN merchant m ON cashin_payment_qris_mpm.ref_merchantId = m.id
    //     join submerchant s on cashin_payment_qris_mpm.ref_subMerchantId = s.id 
    //     left join cashin_dynamic_qris_mpm on cashin_dynamic_qris_mpm.ref_subMerchantId = s.id 
    //     left join cashin_recurring_qris_mpm on cashin_recurring_qris_mpm.ref_subMerchantId = s.id 
    //     where cashin_payment_qris_mpm.id = $id group by cashin_payment_qris_mpm.id";

    //     return $this->db->query($query)->result_array();
    // }

    public function qris_detail($id)
    {
        $query = "SELECT a.id, a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, 
                    d.c_name AS name_submerchant, b.c_invoiceNo, 
                    a.c_type, a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment, 
                    a.c_isSettlementRealtime, a.c_datetimeSettlement, 
                    IF(a.c_type='Dynamic', e.c_merchantTransactionId, f.c_merchantTransactionId) AS c_merchantTransactionId,
                    a.ref_cashinExternalId, a.c_isSettlementRealtimeExternal, a.c_datetimeSettlementExternal,
                    a.c_mdrExternal, a.c_feeExternal,
                    e.ref_cashinExternalLogQrisMpmIdCreate AS dynamic_create_log_id,
                    f.ref_cashinExternalLogQrisMpmIdCreate AS recurring_create_log_id,
                    a.ref_cashinDynamicQrisMpmId, a.ref_cashinRecurringQrisMpmId
                    FROM cashin_payment_qris_mpm a
                    LEFT JOIN cashin b ON b.id=a.ref_cashinId
                    LEFT JOIN merchant c ON a.ref_merchantId=c.id
                    LEFT JOIN submerchant d ON a.ref_subMerchantId=d.id
                    LEFT JOIN cashin_dynamic_qris_mpm e ON e.id = a.ref_cashinDynamicQrisMpmId
                    LEFT JOIN cashin_recurring_qris_mpm f ON f.id = a.ref_cashinRecurringQrisMpmId
                    WHERE a.id ='$id'";

        $res = $this->db->query($query)->result_array();
        if (!empty($res)) {
            $res = $this->_enrich_with_rrns($res);
        }
        return $res;
    }

    public function get_external_payment_log($id, $ref_cashinExternalId)
    {
        $table = '';
        if ($ref_cashinExternalId == 'paydgn') {
            $table = 'external_paydgn_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'gvconnect') {
            $table = 'external_gvconnect_snap_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'inacash') {
            $table = 'external_inacash_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'paylabs' || $ref_cashinExternalId == 'paylabs2') {
            $table = 'external_paylabs_qris_mpm_callback_payment';
        } elseif ($ref_cashinExternalId == 'quantum') {
            $table = 'external_quantum_qris_mpm_calback_payment';
        } elseif ($ref_cashinExternalId == 'stm') {
            $table = 'external_stm_qris_mpm_callback';
        } elseif ($ref_cashinExternalId == 'yukk') {
            $table = 'external_yukk_qris_mpm_callback';
        }

        if ($table != '') {
            $q = $this->db->query("SELECT * FROM $table WHERE ref_cashinPaymentQrisMpmId = '$id' LIMIT 1");
            if ($q) {
                return $q->row_array();
            }
        }
        return null;
    }
    
    public function get_merchant(){
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    /**
     * Standardized DataTables handler for QRIS list.
     * Utilizes the optimized two-step Pre-Lookup query logic with Datatables library.
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_settlement = $filters['settlement'] ?? null;
        $search_rrn = $filters['rrn'] ?? null;
        $search_invoice = $filters['invoice'] ?? null;
        $search_transid = $filters['transid'] ?? null;

        // Format dates for query
        $date_from_query = null;
        $date_to_query = null;
        if (!empty($date_from) && !empty($date_to)) {
            $date_from_query = date('Ymd', strtotime($date_from)) . "000001";
            $date_to_query = date('Ymd', strtotime($date_to)) . "235959";
        }

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid);


        $is_filtered = $search_name || $date_from || $date_to || $search_settlement || $search_rrn || $search_invoice || $search_transid || $this->input->post('search')['value'];
        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_settlement, $search_rrn, $search_invoice, $search_transid) : $recordsTotal;

        // Trick the library to NOT re-slice our already-paginated $list
        $original_start = $_POST['start'];
        $_POST['start'] = 0; 

        $output = $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function($row) use ($original_start) {
                static $no = null;
                if ($no === null) $no = intval($original_start);
                return ++$no;
            })
            ->make(false);
            
        // Restore original start and output JSON
        $_POST['start'] = $original_start;
        $output['draw'] = intval($this->input->post('draw'));
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }


    /**
     * Helper to fetch RRNs from multiple callback tables for a batch of transactions.
     * Decoupled lookup prevents database timeout on joins.
     */
    private function _enrich_with_rrns($list) {
        $ids = array_column($list, 'id');
        if (empty($ids)) return $list;

        $id_str = implode(',', $ids);
        $rrn_map = []; // id => rrn

        $tables = [
            'external_paydgn_qris_mpm_callback',
            'external_gvconnect_snap_qris_mpm_callback',
            'external_inacash_qris_mpm_callback',
            'external_paylabs_qris_mpm_callback_payment',
            'external_quantum_qris_mpm_calback_payment'
        ];

        foreach ($tables as $t) {
            $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId AS c_issuerRrn' : 'c_issuerRrn';
            $q = $this->db->query("SELECT ref_cashinPaymentQrisMpmId, $col FROM $t WHERE ref_cashinPaymentQrisMpmId IN ($id_str)");
            if ($q) {
                foreach ($q->result() as $row) {
                    if (!isset($rrn_map[$row->ref_cashinPaymentQrisMpmId])) {
                        $rrn_map[$row->ref_cashinPaymentQrisMpmId] = $row->c_issuerRrn;
                    }
                }
            }
        }

        // Apply back to list
        foreach ($list as &$item) {
            $itemId = is_array($item) ? $item['id'] : $item->id;
            $rrn = $rrn_map[$itemId] ?? null;
            if (is_array($item)) {
                $item['c_issuerRrn'] = $rrn;
            } else {
                $item->c_issuerRrn = $rrn;
            }
        }

        return $list;
    }

    /**
     * Helper to find IDs by RRN across all providers.
     */
    private function _get_ids_by_rrn($rrn) {
        $safeRrn = $this->db->escape_str($rrn);
        $ids = [];

        $tables = [
            'external_paydgn_qris_mpm_callback',
            'external_gvconnect_snap_qris_mpm_callback',
            'external_inacash_qris_mpm_callback',
            'external_paylabs_qris_mpm_callback_payment',
            'external_quantum_qris_mpm_calback_payment'
        ];

        // Query information_schema once to safely determine which callback tables exist
        $db_name = $this->db->database;
        $info_query = $this->db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME LIKE 'external_%'")->result_array();
        $valid_tables = array_column($info_query, 'TABLE_NAME');

        foreach ($tables as $t) {
            if (!in_array($t, $valid_tables)) continue; // Skip non-existent tables safely
            
            $col = ($t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
            $q = $this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $t WHERE $col LIKE '$safeRrn%' LIMIT 50");
            if ($q) {
                foreach ($q->result() as $row) {
                    if ($row->ref_cashinPaymentQrisMpmId) $ids[] = $row->ref_cashinPaymentQrisMpmId;
                }
            }
        }
        return array_unique($ids);
    }

    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashin_channel
                WHERE c_channelGroup ='qris_mpm'
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function get_external_channels(){
        $query = "SELECT c_cashinExternalId FROM cashin_external_x_channel 
                WHERE c_cashinChannelGroup ='qris_mpm'
                GROUP BY c_cashinExternalId
                ORDER BY c_cashinExternalId ASC";
        return $this->db->query($query)->result();
    }
}
?>