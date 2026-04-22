<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VirtualAccount extends CI_Model {
    
    // DataTables variables
    var $table = 'cashin_payment_va cpv';
    var $column_order = array(null, 'cpv.c_datetime', 'm.c_name', 'c.c_invoiceNo', 'cpv.ref_cashinChannelId', 'cpv.c_type', 'cpv.c_vaNumber', 'egv.c_custom', 'cpv.c_amount', 'cpv.c_fee', 'cpv.c_isSettlementRealtime', 'cpv.c_datetimeSettlement', null); 
    var $column_search = array('cpv.id', 'm.c_name', 'c.c_invoiceNo', 'cpv.c_vaNumber', 'cdv.c_merchantTransactionId', 'crv.c_merchantTransactionId', 'egv.c_custom');
    var $order = array('cpv.id' => 'desc');

    private function _get_datatables_query($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $only_ids = false, $count_only = false)
    {
        // Emergency safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");

        if ($count_only) {
            $this->db->select("count(cpv.id) as total");
        } else if ($only_ids) {
            $this->db->select("cpv.id");
        } else {
            $this->db->select("cpv.*, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name, 
                               IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id,
                               egv.c_custom");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $isInvoiceSearch = (preg_match('/^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join cashin only if searching for invoice, sorting by it, or getting full data
        if (!$only_ids && !$count_only || $isInvoiceSearch || strpos($sort_col, 'c.') !== false) {
            $this->db->join('cashin c', 'cpv.ref_cashinId = c.id', 'left');
        }

        // Join callback/custom data only if getting full data (NOT during ID-fetch to prevent timeouts)
        if (!$only_ids && !$count_only || strpos($sort_col, 'egv.') !== false) {
            $this->db->join('external_gvpay_va_callback_payment egv', 'egv.ref_subMerchantId = cpv.ref_subMerchantId AND egv.ref_cashinPaymentVaId = cpv.id', 'left');
        }
        
        // Join merchant only if needed (NOT during ID-fetch for transid search)
        $isTextSearch = $searchValue && !preg_match('/^(VA|INV|[0-9]{8,})/i', $searchValue);
        if (!$only_ids && !$count_only || $search_merchant || $isTextSearch || strpos($sort_col, 'm.') !== false) {
            $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        }

        if (!$only_ids && !$count_only) {
            $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
        }

        // Trans ID joins ONLY for full data display, NEVER during ID-fetch (use Pre-Lookup instead)
        if (!$only_ids && !$count_only) {
            $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId', 'left');
            $this->db->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId AND crv.ref_merchantId = cpv.ref_merchantId', 'left');
        }

        if ($search_date && $search_date_to) {
            $this->db->where('cpv.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpv.c_datetime <=', $search_date_to . ' 23:59:59');
        } elseif ($search_date) {
            $this->db->where('cpv.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpv.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpv.ref_merchantId', $search_merchant);
        }
        if ($search_va !== null && $search_va !== '') {
            $search_va = trim($search_va);
            if ($search_va !== '') {
                $safeVa = $this->db->escape_str($search_va);
                $matching_ids = [-1];
                
                // 1. Check c_vaNumber & egv.c_custom
                $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE c_vaNumber LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                
                $egv_res = $this->db->query("SELECT ref_cashinPaymentVaId FROM external_gvpay_va_callback_payment WHERE c_custom LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($egv_res)) $matching_ids = array_merge($matching_ids, array_column($egv_res, 'ref_cashinPaymentVaId'));

                // 2. Check Merchant Transaction IDs (sub-tables)
                $cdv_res = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($cdv_res)) {
                    $cdv_ids = array_column($cdv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $cdv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                $this->db->where_in('cpv.id', array_unique($matching_ids));
            }
        }
        if ($search_transid !== null && $search_transid !== '') {
            $search_transid = trim($search_transid);
            if ($search_transid !== '') {
                $safeTransId = $this->db->escape_str($search_transid);
                $matching_ids = [-1];
                
                // 1. Check c_vaNumber & egv.c_custom
                $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE c_vaNumber LIKE '$safeTransId%' LIMIT 50")->result();
                if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));

                $egv_res = $this->db->query("SELECT ref_cashinPaymentVaId FROM external_gvpay_va_callback_payment WHERE c_custom LIKE '$safeTransId%' LIMIT 50")->result();
                if (!empty($egv_res)) $matching_ids = array_merge($matching_ids, array_column($egv_res, 'ref_cashinPaymentVaId'));

                // 2. Check Merchant Transaction IDs (sub-tables)
                $cdv_res = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId LIKE '$safeTransId%' LIMIT 50")->result();
                if (!empty($cdv_res)) {
                    $cdv_ids = array_column($cdv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $cdv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                $crv_res = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_merchantTransactionId LIKE '$safeTransId%' LIMIT 50")->result();
                if (!empty($crv_res)) {
                    $crv_ids = array_column($crv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinRecurringVaId IN (".implode(',', $crv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                $this->db->where_in('cpv.id', array_unique($matching_ids));
            }
        }

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            // Detect technical IDs (Numeric > 8 digits, or starting with VA/INV/GD/GR/0000)
            $isTechnicalId = preg_match('/^([0-9]{8,30}|(VA|INV|GD|GR|0000)[0-9a-zA-Z]+)/i', $searchValue);
            $isInvoiceSearch = preg_match('/^INV/i', $searchValue);

            if ($isTechnicalId) {
                $matching_ids = [-1];
                
                // 1. Direct ID match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }
                
                // 2. VA Number match
                $va_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE c_vaNumber LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($va_res)) $matching_ids = array_merge($matching_ids, array_column($va_res, 'id'));
                
                // 3. EGV Custom field match
                $egv_res = $this->db->query("SELECT ref_cashinPaymentVaId FROM external_gvpay_va_callback_payment WHERE c_custom LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($egv_res)) $matching_ids = array_merge($matching_ids, array_column($egv_res, 'ref_cashinPaymentVaId'));
                
                // 4. Trans ID Dynamic
                $cdv_res = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($cdv_res)) {
                    $cdv_ids = array_column($cdv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $cdv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                // 5. Trans ID Recurring
                $crv_res = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($crv_res)) {
                    $crv_ids = array_column($crv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinRecurringVaId IN (".implode(',', $crv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                $this->db->where_in('cpv.id', array_unique($matching_ids));

                
            } else if ($isInvoiceSearch) {
                $this->db->where("cpv.ref_cashinId IN (SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%')", NULL, FALSE);
            } else {
                // TEXT SEARCH: Merchant name only (min 4 chars)
                if (strlen($searchValue) >= 4) {
                    $this->db->like('m.c_name', $searchValue, 'after');
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
    

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null)
    {
        // STEP 1: Get only IDs for the current page (Fast query)
        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full data for only these specific IDs
        $this->db->select("cpv.*, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name, 
                           IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id,
                           egv.c_custom");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'cpv.ref_cashinId = c.id', 'left');
        $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId', 'left');
        $this->db->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId AND crv.ref_merchantId = cpv.ref_merchantId', 'left');
        $this->db->join('external_gvpay_va_callback_payment egv', 'egv.ref_subMerchantId = cpv.ref_subMerchantId AND egv.ref_cashinPaymentVaId = cpv.id', 'left');
        
        $this->db->where_in('cpv.id', $ids);
        
        // Re-apply sorting to maintain order from STEP 1
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_date || $search_date_to || $search_merchant || $search_settlement || $search_va || $search_transid || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_date = null, $search_date_to = null, $search_merchant = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }


    public function get_va($limit, $start, $search_date_va = null, $search_name_va = null, $search_date_va_settlement = null, $search_va_number = null, $search_va_transid = null) 
    {
        $base_query = " FROM cashin_payment_va cpv
                        JOIN cashin c ON cpv.ref_cashinId = c.id
                        JOIN submerchant s ON cpv.ref_subMerchantId = s.id
                        LEFT JOIN cashin_dynamic_va ON (cashin_dynamic_va.id = cpv.ref_cashinDynamicVaId AND cashin_dynamic_va.ref_merchantId = cpv.ref_merchantId)
                        LEFT JOIN cashin_recurring_va ON (cashin_recurring_va.id = cpv.ref_cashinRecurringVaId AND cashin_recurring_va.ref_merchantId = cpv.ref_merchantId)
                        LEFT JOIN merchant m ON cpv.ref_merchantId = m.id
                        LEFT JOIN external_gvpay_va_callback_payment ON (external_gvpay_va_callback_payment.ref_subMerchantId = cpv.ref_subMerchantId
                        AND external_gvpay_va_callback_payment.ref_cashinPaymentVaId = cpv.id)
                        WHERE 1=1";

        if ($search_date_va) {
            $search_date_va = date('Y-m-d', strtotime($search_date_va));
            $base_query .= " AND cpv.c_datetime >= '$search_date_va 00:00:00' AND cpv.c_datetime <= '$search_date_va 23:59:59'";
        }
        if ($search_name_va) {
            $base_query .= " AND m.id = $search_name_va";
        }
        if ($search_date_va_settlement) {
            $search_date_va_settlement = date('Y-m-d', strtotime($search_date_va_settlement));
            $base_query .= " AND cpv.c_datetimeSettlement >= '$search_date_va_settlement 00:00:00' AND cpv.c_datetimeSettlement <= '$search_date_va_settlement 23:59:59'";
        }
        if ($search_va_number) {
            $search_va_number = trim($search_va_number);
            if ($search_va_number !== '') {
                $base_query .= " AND cpv.c_vaNumber LIKE '{$this->db->escape_like_str($search_va_number)}%'";
            }
        }
        if ($search_va_transid) {
            $search_va_transid = trim($search_va_transid);
            if ($search_va_transid !== '') {
                $escaped_transid = $this->db->escape_like_str($search_va_transid);
                $base_query .= " AND (cashin_dynamic_va.c_merchantTransactionId LIKE '{$escaped_transid}%' OR cashin_recurring_va.c_merchantTransactionId LIKE '{$escaped_transid}%')";
            }
        }

        // Hitung total rows untuk pagination
        $total_query = "SELECT COUNT(*) AS total_rows" . $base_query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;

        // Ambil data dengan limit
        $data_query = "SELECT cpv.*, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name,
                    IF(cpv.c_type = 'Dynamic', cashin_dynamic_va.c_merchantTransactionId, cashin_recurring_va.c_merchantTransactionId) AS Merchant_Transaction_Id,
                    external_gvpay_va_callback_payment.c_custom" . $base_query . " ORDER BY cpv.id DESC LIMIT $start, $limit";
        $data = $this->db->query($data_query)->result();

        return [
            'total_rows' => $total_rows,
            'data' => $data
        ];
    }


    public function count_va($search_date_va = null, $search_name_va = null, $search_date_va_settlement = null, $search_va_number = null, $search_va_transid = null) 
    {
        $this->db->from('cashin_payment_va cpv');
        $this->db->join('cashin c', 'cpv.ref_cashinId = c.id');
        $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
        $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId', 'left');
        
        if ($search_date_va) {
            $formatted_date = date('Y-m-d', strtotime($search_date_va));
            $this->db->where('cpv.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpv.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if ($search_name_va) {
            $this->db->where('m.id', $search_name_va);
        }

        if ($search_date_va_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_date_va_settlement));
            $this->db->where('cpv.c_datetimeSettlement >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpv.c_datetimeSettlement <=', $formatted_date . ' 23:59:59');
        }

        if ($search_va_number) {
            $search_va_number = trim($search_va_number);
            if ($search_va_number !== '') {
                $this->db->like('cpv.c_vaNumber', $search_va_number, 'after');
            }
        }

        if ($search_va_transid) {
            $search_va_transid = trim($search_va_transid);
            if ($search_va_transid !== '') {
                $this->db->group_start();
                $this->db->like('cdv.c_merchantTransactionId', $search_va_transid, 'after');
                $this->db->or_like('cashin_recurring_va.c_merchantTransactionId', $search_va_transid, 'after');
                $this->db->group_end();
            }
        }

        return (int)$this->db->count_all_results();
    }



        public function get_summary($date_from, $date_to, $refMerchantId = null) {
            // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
            $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
            FROM cashin_payment_va a
            WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";
    
            if (!empty($refMerchantId)) {
                $query .= " AND a.ref_merchantId = '$refMerchantId'";
            }
            return $this->db->query($query)->result_array();
        }

    public function va_detail($id)
    {
        $id = $this->db->escape($id);
        $query = "SELECT 
                    cpv.*, 
                    m.id as id_merchant, 
                    m.c_name AS name_merchant, 
                    s.id as id_submerchant, 
                    s.c_name AS name_submerchant, 
                    c.c_invoiceNo,
                    IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id
                FROM 
                    cashin_payment_va cpv 
                    LEFT JOIN cashin c ON cpv.ref_cashinId = c.id 
                    LEFT JOIN merchant m ON cpv.ref_merchantId = m.id
                    LEFT JOIN submerchant s ON cpv.ref_subMerchantId = s.id 
                    LEFT JOIN cashin_dynamic_va cdv ON cdv.id = cpv.ref_cashinDynamicVaId AND cdv.ref_merchantId = cpv.ref_merchantId
                    LEFT JOIN cashin_recurring_va crv ON crv.id = cpv.ref_cashinRecurringVaId AND crv.ref_merchantId = cpv.ref_merchantId
                WHERE 
                    cpv.id = $id";
        return $this->db->query($query)->result_array();
    }
    public function get_merchant(){
            $query = "select id,c_name from merchant ";
            return $this->db->query($query)->result();
        }

    /**
     * Standardized DataTables handler for Virtual Account list.
     * Utilizes the optimized two-step Pre-Lookup query logic with Datatables library.
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_merchant = $filters['merchant'] ?? null;
        $search_settlement = $filters['settlement'] ?? null;
        $search_va = $filters['va_number'] ?? null;
        $search_transid = $filters['transid'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid);

        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_date || $search_date_to || $search_merchant || $search_settlement || $search_va || $search_transid || (!empty($searchValue));
        
        $recordsTotal = $this->count_all_dt($search_date, $search_date_to, $search_merchant);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid) : $recordsTotal;

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