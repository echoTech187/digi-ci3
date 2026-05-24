<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ewallet extends CI_Model {
    var $table = 'cashin_payment_ewallet cpe';
    var $column_order = array(null, 'cpe.c_datetime', 's.c_name', 'cde.c_merchantTransactionId', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', null);
    var $column_search = array('cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cpe.id' => 'desc');
    private static $cached_total = null;

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $search_transid = null, $only_ids = false, $count_only = false)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(DISTINCT cpe.ref_cashinId) as total");
        } else if ($only_ids) {
            $this->db->select("MAX(cpe.id) as id");
        } else {
            $this->db->select("cpe.id, cpe.c_datetime, cpe.c_type, cpe.c_amount, cpe.c_mdr, cpe.c_fee, cpe.c_datetimeSettlement, cpe.ref_merchantId, cpe.ref_subMerchantId, cpe.ref_cashinId, cpe.ref_cashinChannelId, cpe.ref_cashinDynamicEwalletId, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                               cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $isInvoiceSearch = (preg_match('/^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join cashin only if searching for invoice, sorting by it, or getting full data
        // Base joins only added if needed for sorting or full data (Deferred Join pattern)
        if (!$only_ids && !$count_only || $search_name || $searchValue || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
        }

        // Apply Basic Filters
        if ($search_name) {
            $this->db->where('cpe.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpe.c_datetimePayment >=', $date_from);
            $this->db->where('cpe.c_datetimePayment <=', $date_to);
        }
        if ($search_date_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_date_settlement));
            $this->db->where('cpe.c_datetimeSettlement >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpe.c_datetimeSettlement <=', $formatted_date . ' 23:59:59');
        }
        if ($search_invoice_no && !$searchValue) {
            $search_invoice_no = trim($search_invoice_no);
            if ($search_invoice_no !== '') {
                $safeInv = $this->db->escape_str($search_invoice_no);
                $this->db->group_start();
                // Match Invoice No
                $this->db->where_in('cpe.ref_cashinId', "SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeInv%'", FALSE);
                $this->db->group_end();
            }
        }
        if ($search_transid && !$searchValue) {
            $search_transid = trim($search_transid);
            if ($search_transid !== '') {
                $safeTrans = $this->db->escape_str($search_transid);
                // Specifically match Merchant Trans ID
                $this->db->where_in('cpe.ref_cashinDynamicEwalletId', "SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeTrans%'", FALSE);
            }
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

                // 1. Priority: Check Transaction ID match (via dynamic ewallet lookup)
                $cde_res = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 50")->result();
                if (!empty($cde_res)) {
                    $cde_ids = array_column($cde_res, 'id');
                    $cpe_res = $this->db->query("SELECT id FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (".implode(',', $cde_ids).") LIMIT 50")->result();
                    if (!empty($cpe_res)) $matching_ids = array_merge($matching_ids, array_column($cpe_res, 'id'));
                }

                // 2. Check Invoice Number (Only if specific ID not found)
                if (count($matching_ids) <= 1 || strlen($searchValue) < 15) {
                    if (strlen($searchValue) >= 4) {
                        $inv_q = "SELECT id FROM cashin WHERE c_invoiceNo $op $val ";
                        $inv_res = $this->db->query($inv_q . " LIMIT 50")->result();
                        if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
                    }
                }

                // 3. Direct PK match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                $cached_ids = array_unique($matching_ids);
                $cached_inv_ids = array_unique($matching_inv_ids);
            }

            // 2. Decide strategy
            if (count($cached_ids) > 1 || count($cached_inv_ids) > 1) {
                $this->db->group_start();
                if (count($cached_ids) > 1) $this->db->where_in('cpe.id', $cached_ids);
                if (count($cached_inv_ids) > 1) {
                    if (count($cached_ids) > 1) $this->db->or_where_in('cpe.ref_cashinId', $cached_inv_ids);
                    else $this->db->where_in('cpe.ref_cashinId', $cached_inv_ids);
                }
                $this->db->group_end();
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    // Ensure joins for name search fallback
                    $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
                    $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
                    
                    $this->db->like('s.c_name', $searchValue, 'both');
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }
        if (!$count_only) {
            // Deduplication: Only group if NOT counting total
            $this->db->group_by('cpe.ref_cashinId');

            if (isset($_POST['order'])) {
                $order_col = $this->column_order[$_POST['order']['0']['column']];
                // Fix for only_full_group_by: Use alias during grouping
                if ($only_ids && ($order_col == 'cpe.id' || $order_col == 'id')) {
                    $this->db->order_by('id', $_POST['order']['0']['dir'], FALSE);
                } else {
                    $this->db->order_by($order_col, $_POST['order']['0']['dir']);
                }
            } else if (isset($this->order)) {
                $order = $this->order;
                $key = key($order);
                if ($only_ids && ($key == 'cpe.id' || $key == 'id')) {
                    $this->db->order_by('id', $order[$key], FALSE);
                } else {
                    $this->db->order_by($key, $order[$key]);
                }
            }
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $search_transid = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, $search_transid, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        if (!$query) return array(); // Fail-safe
        
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("cpe.*, m.c_name as name_merchant, m.c_merchantLevel, s.c_name as name_submerchant, c.c_invoiceNo, 
                           cde.c_merchantTransactionId AS Merchant_Transaction_Id", FALSE);
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId', 'left');
        $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_ewallet cde', 'cpe.ref_cashinDynamicEwalletId = cde.id', 'left');
        
        $this->db->where_in('cpe.id', $ids);
        
        if (isset($_POST['order'])) {
            $order_col = $this->column_order[$_POST['order']['0']['column']];
            $this->db->order_by($order_col, $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        $results = $query->result();
        return $results;
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $search_transid = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_date_settlement || $search_invoice_no || $search_transid || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, $search_transid, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // If no filters, use the fastest possible estimate from metadata (Instant)
        if (!$search_name && !$date_from && !$date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_payment_ewallet'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int)$res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(DISTINCT cpe.ref_cashinId) as total");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpe.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpe.c_datetime >=', $date_from);
            $this->db->where('cpe.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_date_settlement = $filters['settlement'] ?? null;
        $search_invoice_no = $filters['invoice'] ?? null;
        $search_transid = $filters['transid'] ?? null;

        // Format dates for query
        $date_from_query = !empty($date_from) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = !empty($date_to) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no, $search_transid);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_date_settlement || $search_invoice_no || $search_transid || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no, $search_transid) : $recordsTotal;

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

    public function ewallet_detail($id)
    {
        $id = $this->db->escape($id);
        $query = "SELECT 
                    cpe.*, 
                    m.id as id_merchant, 
                    m.c_name AS name_merchant, 
                    s.id as id_submerchant, 
                    s.c_name AS name_submerchant, 
                    c.c_invoiceNo,
                    cde.c_merchantTransactionId,
                    cde.c_merchantTransactionId AS Merchant_Transaction_Id
                FROM 
                    cashin_payment_ewallet cpe 
                    LEFT JOIN cashin c ON cpe.ref_cashinId = c.id 
                    LEFT JOIN merchant m ON cpe.ref_merchantId = m.id
                    LEFT JOIN submerchant s ON cpe.ref_subMerchantId = s.id 
                    LEFT JOIN cashin_dynamic_ewallet cde ON cde.id = cpe.ref_cashinDynamicEwalletId
                WHERE 
                    cpe.id = $id";
        return $this->db->query($query)->result_array();
    }
}
?>