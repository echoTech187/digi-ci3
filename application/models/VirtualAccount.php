<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VirtualAccount extends CI_Model {
    
    // DataTables variables
    private static $cached_total = null;
    var $table = 'cashin_payment_va cpv';
    var $column_order = array(null, 'cpv.c_datetime', 'm.c_name', 'Merchant_Transaction_Id', 'cpv.c_vaNumber', 'egv.c_custom', 'c.c_invoiceNo', 'cpv.ref_cashinChannelId', 'cpv.c_type', 'cpv.c_amount', 'cpv.c_fee', 'cpv.c_isSettlementRealtime', 'cpv.c_datetimeSettlement', null); 
    var $column_search = array('cpv.id', 'm.c_name', 'c.c_invoiceNo', 'cpv.c_vaNumber', 'cdv.c_merchantTransactionId', 'crv.c_merchantTransactionId', 'egv.c_custom');
    var $order = array('cpv.id' => 'desc');

    private function _get_datatables_query($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null, $only_ids = false, $count_only = false)
    {
        // Emergency safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(*) as total");
        } else if ($only_ids) {
            $this->db->select("cpv.id");
        } else {
            $this->db->select("cpv.id, cpv.c_datetime, cpv.c_type, cpv.c_vaNumber, cpv.c_amount, cpv.c_fee, cpv.c_isSettlementRealtime, cpv.c_datetimeSettlement, cpv.ref_merchantId, cpv.ref_subMerchantId, cpv.ref_cashinId, cpv.ref_cashinChannelId, cpv.ref_cashinDynamicVaId, cpv.ref_cashinRecurringVaId, c.c_invoiceNo, m.c_name AS merchant_name, s.c_name AS submerchant_name, 
                               IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id,
                               egv.c_custom");
        }
        $this->db->from($this->table);
        
        // Essential joins
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
        $joined_merchant = false;
        if (!$only_ids && !$count_only || $search_merchant || $isTextSearch || strpos($sort_col, 'm.') !== false) {
            $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
            $joined_merchant = true;
        }

        $joined_submerchant = false;
        if (!$only_ids && !$count_only) {
            $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
            $joined_submerchant = true;
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
        if ($search_channel) {
            $this->db->where('cpv.ref_cashinChannelId', $search_channel);
        }
        if ($search_va !== null && $search_va !== '' && !$searchValue) {
            $search_va = trim($search_va);
            if ($search_va !== '') {
                $safeVa = $this->db->escape_str($search_va);
                $matching_ids = [-1];
                
                // 1. Check c_vaNumber & egv.c_custom
                $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE c_vaNumber LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                
                $egv_res = $this->db->query("SELECT ref_cashinPaymentVaId FROM external_gvpay_va_callback_payment WHERE c_custom LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($egv_res)) $matching_ids = array_merge($matching_ids, array_column($egv_res, 'ref_cashinPaymentVaId'));

                // 1b. Check Invoice No
                $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeVa%' LIMIT 50")->result();
                if (!empty($inv_res)) {
                    $inv_ids = array_column($inv_res, 'id');
                    $cpv_inv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinId IN (".implode(',', $inv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_inv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_inv_res, 'id'));
                }

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
        if ($search_transid !== null && $search_transid !== '' && !$searchValue) {
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
        if ($search_invoice !== null && $search_invoice !== '' && !$searchValue) {
            $search_invoice = trim($search_invoice);
            if ($search_invoice !== '') {
                $safeInvoice = $this->db->escape_str($search_invoice);
                $matching_ids = [-1];

                $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeInvoice%' LIMIT 50")->result();
                if (!empty($inv_res)) {
                    $inv_ids = array_column($inv_res, 'id');
                    $cpv_inv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinId IN (".implode(',', $inv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_inv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_inv_res, 'id'));
                }
                
                $this->db->where_in('cpv.id', array_unique($matching_ids));
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

                // 1. Priority: Check Transaction IDs from Dynamic/Recurring
                $cdv_res = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val LIMIT 20")->result();
                if (!empty($cdv_res)) {
                    $cdv_ids = array_column($cdv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $cdv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }
                
                $crv_res = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_merchantTransactionId $op $val LIMIT 20")->result();
                if (!empty($crv_res)) {
                    $crv_ids = array_column($crv_res, 'id');
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE ref_cashinRecurringVaId IN (".implode(',', $crv_ids).") LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                }

                // 2. Check VA Number & Custom Data
                if (count($matching_ids) <= 1) {
                    $cpv_res = $this->db->query("SELECT id FROM cashin_payment_va WHERE c_vaNumber $op $val LIMIT 50")->result();
                    if (!empty($cpv_res)) $matching_ids = array_merge($matching_ids, array_column($cpv_res, 'id'));
                    
                    $egv_res = $this->db->query("SELECT ref_cashinPaymentVaId FROM external_gvpay_va_callback_payment WHERE c_custom $op $val LIMIT 50")->result();
                    if (!empty($egv_res)) $matching_ids = array_merge($matching_ids, array_column($egv_res, 'ref_cashinPaymentVaId'));
                }

                // 3. Check Invoice Number (Only if specific ID not found)
                if (count($matching_ids) <= 1 || strlen($searchValue) < 15) {
                    if (strlen($searchValue) >= 4) {
                        $inv_q = "SELECT id FROM cashin WHERE c_invoiceNo $op $val ";
                        $inv_res = $this->db->query($inv_q . " LIMIT 50")->result();
                        if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
                    }
                }

                // 4. Direct PK match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                $cached_ids = array_unique($matching_ids);
                $cached_inv_ids = array_unique($matching_inv_ids);
            }

            // 2. Decide strategy
            if (count($cached_ids) > 1 || count($cached_inv_ids) > 1) {
                $this->db->group_start();
                if (count($cached_ids) > 1) $this->db->where_in('cpv.id', $cached_ids);
                if (count($cached_inv_ids) > 1) {
                    if (count($cached_ids) > 1) $this->db->or_where_in('cpv.ref_cashinId', $cached_inv_ids);
                    else $this->db->where_in('cpv.ref_cashinId', $cached_inv_ids);
                }
                $this->db->group_end();
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    // Ensure joins for name search fallback
                    if (!$joined_submerchant) {
                        $this->db->join('submerchant s', 'cpv.ref_subMerchantId = s.id', 'left');
                        $joined_submerchant = true;
                    }
                    if (!$joined_merchant) {
                        $this->db->join('merchant m', 'cpv.ref_merchantId = m.id', 'left');
                        $joined_merchant = true;
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
        
        // No grouping needed as ref_cashinId is unique
        if (!$count_only) {
            // $this->db->group_by('cpv.ref_cashinId');
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_col = $this->column_order[$_POST['order']['0']['column']];
                if ($only_ids && ($sort_col == 'cpv.id' || $sort_col == 'id')) {
                    $this->db->order_by('id', $_POST['order']['0']['dir'], FALSE);
                } else {
                    $this->db->order_by($sort_col, $_POST['order']['0']['dir']);
                }
            } else if (isset($this->order)) {
                $order = $this->order;
                $key = key($order);
                if ($only_ids && ($key == 'cpv.id' || $key == 'id')) {
                    $this->db->order_by('id', $order[$key], FALSE);
                } else {
                    $this->db->order_by($key, $order[$key]);
                }
            }
        }
    

    }

    public function get_datatables($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null)
    {
        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full data for only these specific IDs
        $this->db->select("cpv.*, c.c_invoiceNo, m.c_name AS merchant_name, m.c_merchantLevel, s.c_name AS submerchant_name, 
                           IF(cpv.c_type = 'Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId) AS Merchant_Transaction_Id,
                           egv.c_custom", FALSE);
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

    public function count_filtered($search_date = null, $search_date_to = null, $search_merchant = null, $search_settlement = null, $search_va = null, $search_transid = null, $search_invoice = null, $search_channel = null)
    {
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $is_filtered = $search_date || $search_date_to || $search_merchant || $search_settlement || $search_va || $search_transid || $search_invoice || $search_channel || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt($search_date, $search_date_to, $search_merchant);
        }

        $this->_get_datatables_query($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_date = null, $search_date_to = null, $search_merchant = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // If no filters, use the fastest possible estimate from metadata (Instant)
        if (!$search_date && !$search_date_to && !$search_merchant) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_payment_va'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int)$res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(*) as total");
        $this->db->from($this->table);
        if ($search_merchant) $this->db->where('cpv.ref_merchantId', $search_merchant);
        if ($search_date && $search_date_to) {
            $this->db->where('cpv.c_datetime >=', $search_date);
            $this->db->where('cpv.c_datetime <=', $search_date_to);
        }
        $query = $this->db->get();
        return $query->row()->total;
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


    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashin_channel 
                WHERE c_channelGroup IN ('va', 'VIRTUAL_ACCOUNT')
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
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
        $search_invoice = $filters['invoice_no'] ?? null;
        $search_channel = $filters['channel'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel);

        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_date || $search_date_to || $search_merchant || $search_settlement || $search_va || $search_transid || $search_invoice || $search_channel || (!empty($searchValue));
        
        $recordsTotal = $this->count_all_dt($search_date, $search_date_to, $search_merchant);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_date, $search_date_to, $search_merchant, $search_settlement, $search_va, $search_transid, $search_invoice, $search_channel) : $recordsTotal;

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
}
?>