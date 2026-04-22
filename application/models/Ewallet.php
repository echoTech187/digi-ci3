<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ewallet extends CI_Model {
    var $table = 'cashin_payment_ewallet cpe';
    var $column_order = array(null, 'cpe.c_datetime', 's.c_name', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', 'cde.c_merchantTransactionId', null);
    var $column_search = array('cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cpe.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $only_ids = false, $count_only = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 3000");
        
        if ($count_only) {
            $this->db->select("count(cpe.id) as total");
        } else if ($only_ids) {
            $this->db->select("cpe.id");
        } else {
            $this->db->select("cpe.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                               cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
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
        if ($search_invoice_no) {
            $search_invoice_no = trim($search_invoice_no);
            if ($search_invoice_no !== '') {
                $safeInv = $this->db->escape_str($search_invoice_no);
                $this->db->where_in('cpe.ref_cashinId', "SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeInv%'", FALSE);
            }
        }

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            // Detect technical IDs (Numeric > 8 digits, or starting with INV/GD/GR/0000)
            $isTechnicalId = preg_match('/^([0-9]{8,30}|(INV|GD|GR|0000)[0-9a-zA-Z]+)/i', $searchValue);
            $isInvoiceSearch = preg_match('/^INV/i', $searchValue);

            if ($isTechnicalId) {
                $matching_ids = [-1];
                
                // 1. Direct ID match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }
                
                // 2. Invoice No match (via sub-query lookup)
                $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
                $inv_ids = array_column($inv_res, 'id');
                
                // 3. Merchant Trans ID match (via dynamic ewallet lookup)
                $cde_res = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($cde_res)) {
                    $cde_ids = array_column($cde_res, 'id');
                    $cpe_res = $this->db->query("SELECT id FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (".implode(',', $cde_ids).") LIMIT 50")->result();
                    if (!empty($cpe_res)) $matching_ids = array_merge($matching_ids, array_column($cpe_res, 'id'));
                }
                
                // Construct the combined filter for technical IDs
                $this->db->group_start();
                if (count($matching_ids) > 1) {
                    $this->db->where_in('cpe.id', array_unique($matching_ids));
                }
                if (!empty($inv_ids)) {
                    if (count($matching_ids) > 1) {
                        $this->db->or_where_in('cpe.ref_cashinId', $inv_ids);
                    } else {
                        $this->db->where_in('cpe.ref_cashinId', $inv_ids);
                    }
                } else if (count($matching_ids) <= 1) {
                    $this->db->where('1=0', NULL, FALSE);
                }
                $this->db->group_end();
                
            } else {
                // TEXT SEARCH: Submerchant name only (min 4 chars)
                if (strlen($searchValue) >= 4) {
                    $this->db->like('s.c_name', $searchValue, 'after');
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

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        if (!$query) return array(); // Fail-safe
        
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("cpe.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                           cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId');
        $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpe.ref_merchantId = m.id');
        $this->db->join('cashin_dynamic_ewallet cde', 'cde.ref_merchantId = cpe.ref_merchantId AND cde.id = cpe.ref_cashinDynamicEwalletId', 'left');
        
        $this->db->where_in('cpe.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_date_settlement || $search_invoice_no || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
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

        // Format dates for query
        $date_from_query = !empty($date_from) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = !empty($date_to) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_date_settlement || $search_invoice_no || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_date_settlement, $search_invoice_no) : $recordsTotal;

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