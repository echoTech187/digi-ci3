<?php defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Model {

    public function get_history($limit, $start, $search_date_purchase = null, $search_merchant_purchase = null) {
    
        $query = "SELECT 
                m.c_name as name_merchant,
                cashout_payment_ppob.c_datetime, 
                cashout_payment_ppob.ref_cashoutChannelId, 
                c_invoiceNo, 
                c_phone, 
                cashout_payment_ppob.c_amount, 
                cashout_payment_ppob.c_status
                FROM cashout_payment_ppob 
                LEFT JOIN cashout ON cashout.id = cashout_payment_ppob.ref_cashoutId
                left join merchant m on cashout_payment_ppob.ref_merchantId = m.id ";
        // var_dump($query);
        $query .= "WHERE 1=1 ";

        if ($search_date_purchase) {
            $formatted_date = date('Y-m-d', strtotime($search_date_purchase));
            $query .= " AND cashout_payment_ppob.c_datetime >= '$formatted_date 00:00:00' AND cashout_payment_ppob.c_datetime <= '$formatted_date 23:59:59'";
        }
    
        if ($search_merchant_purchase) {
            $query .= " AND m.id = $search_merchant_purchase";
        }
    
        $query .= " LIMIT $start, $limit";
    // var_dump($query);
        return $this->db->query($query)->result();
    }
    
    public function count_history($refMerchantId, $search_date_purchase = null) {

        $query = "SELECT COUNT(cashout_payment_ppob.id) as total
            FROM cashout_payment_ppob
            WHERE cashout_payment_ppob.ref_merchantId = $refMerchantId ";

        if ($search_date_purchase) {
            $formatted_date = date('Y-m-d', strtotime($search_date_purchase));
            $query .= " AND cashout_payment_ppob.c_datetime >= '$formatted_date 00:00:00' AND cashout_payment_ppob.c_datetime <= '$formatted_date 23:59:59'";
        }

        return (int)$this->db->query($query)->row()->total;
    }

    public function get_merchant(){
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    // --- DataTables Server-Side Processing ---
    var $table = 'cashout_payment_ppob cpp';
    var $column_order = array(null, 'm.c_name', 'cpp.c_datetime', 'cpp.ref_cashoutChannelId', 'c.c_invoiceNo', 'cpp.c_phone', 'cpp.c_amount', 'cpp.c_status');
    var $column_search = array('cpp.id', 'm.c_name', 'c.c_invoiceNo', 'cpp.c_phone', 'cpp.ref_cashoutChannelId');
    var $order = array('cpp.id' => 'desc');

    private function _get_datatables_query($search_date = null, $search_merchant = null)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        $this->db->select('cpp.*, m.c_name as name_merchant, c.c_invoiceNo');
        $this->db->from($this->table);
        $this->db->join('merchant m', 'cpp.ref_merchantId = m.id', 'left');
        $this->db->join('cashout c', 'c.id = cpp.ref_cashoutId', 'left');

        if ($search_date) {
            $this->db->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpp.ref_merchantId', $search_merchant);
        }

        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            // TRULY SMART SEARCH: 
            // 1. Always try finding ID matches first (Fast Indexed Lookup)
            $matching_ids = [-1];
            $matching_inv_ids = [-1];

            // A. Check Invoice Number (Fast Lookup)
            $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));

            // B. Check ID & Phone Number match
            $cpp_res = $this->db->query("SELECT id FROM cashout_payment_ppob WHERE c_phone LIKE '$safeSearch%' OR ref_cashoutChannelId LIKE '$safeSearch%' LIMIT 100")->result();
            if (!empty($cpp_res)) $matching_ids = array_merge($matching_ids, array_column($cpp_res, 'id'));
            
            // C. Direct PK match
            if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                $matching_ids[] = (int)$searchValue;
            }

            $matching_ids = array_unique($matching_ids);
            $matching_inv_ids = array_unique($matching_inv_ids);

            // 2. Decide strategy
            if (count($matching_ids) > 1 || count($matching_inv_ids) > 1) {
                $this->db->group_start();
                if (count($matching_ids) > 1) $this->db->where_in('cpp.id', $matching_ids);
                if (count($matching_inv_ids) > 1) {
                    if (count($matching_ids) > 1) $this->db->or_where_in('cpp.ref_cashoutId', $matching_inv_ids);
                    else $this->db->where_in('cpp.ref_cashoutId', $matching_inv_ids);
                }
                $this->db->group_end();
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    $this->db->like('m.c_name', $searchValue, 'both');
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($search_date = null, $search_merchant = null)
    {
        $this->_get_datatables_query($search_date, $search_merchant);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_date = null, $search_merchant = null)
    {
        $is_filtered = ($search_date || $search_merchant || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(cpp.id) as total');
        // Optimized: Only join if global search is used
        $this->db->from($this->table);
        
        if ($search_date) {
            $this->db->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpp.ref_merchantId', $search_merchant);
        }

        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if (!empty($searchValue)) {
            $safeSearch = $this->db->escape_str($searchValue);
            $matching_ids = [-1];
            $matching_inv_ids = [-1];
            $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
            $cpp_res = $this->db->query("SELECT id FROM cashout_payment_ppob WHERE c_phone LIKE '$safeSearch%' OR ref_cashoutChannelId LIKE '$safeSearch%' LIMIT 100")->result();
            if (!empty($cpp_res)) $matching_ids = array_merge($matching_ids, array_column($cpp_res, 'id'));
            if (is_numeric($searchValue) && strlen($searchValue) < 15) $matching_ids[] = (int)$searchValue;
            
            if (count($matching_ids) > 1 || count($matching_inv_ids) > 1) {
                $this->db->group_start();
                if (count($matching_ids) > 1) $this->db->where_in('cpp.id', array_unique($matching_ids));
                if (count($matching_inv_ids) > 1) {
                    if (count($matching_ids) > 1) $this->db->or_where_in('cpp.ref_cashoutId', array_unique($matching_inv_ids));
                    else $this->db->where_in('cpp.ref_cashoutId', array_unique($matching_inv_ids));
                }
                $this->db->group_end();
            } else {
                $this->db->join('merchant m', 'cpp.ref_merchantId = m.id', 'left');
                if (strlen($searchValue) >= 3) {
                    $this->db->like('m.c_name', $searchValue, 'both');
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_date = null, $search_merchant = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function get_summary($search_date = null, $search_merchant = null)
    {
        $this->db->select('COUNT(cpp.id) as qty, SUM(cpp.c_amount) as amount');
        $this->db->from($this->table);
        $this->db->join('merchant m', 'cpp.ref_merchantId = m.id', 'left');

        if ($search_date) {
            $this->db->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpp.ref_merchantId', $search_merchant);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Standardized DataTables handler for History (PPOB) list.
     */
    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');
        
        $search_date = $filters['date'] ?? null;
        $search_merchant = $filters['merchant'] ?? null;

        $dt = $this->datatables->of('cashout_payment_ppob cpp')
            ->select('cpp.*, m.c_name as name_merchant, c.c_invoiceNo')
            ->join('merchant m', 'cpp.ref_merchantId = m.id', 'left')
            ->join('cashout c', 'c.id = cpp.ref_cashoutId', 'left');

        if ($search_date) {
            $dt->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $dt->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $dt->where('cpp.ref_merchantId', $search_merchant);
        }
        if (isset($filters['invoice']) && $filters['invoice']) {
            $dt->where('c.c_invoiceNo', $filters['invoice']);
        }

        return $dt->set_column_order([null, 'm.c_name', 'cpp.c_datetime', 'cpp.ref_cashoutChannelId', 'c.c_invoiceNo', 'cpp.c_phone', 'cpp.c_amount', 'cpp.c_status'])
            ->set_column_search(['cpp.id', 'm.c_name', 'c.c_invoiceNo', 'cpp.c_phone', 'cpp.ref_cashoutChannelId'])
            ->set_default_order(['cpp.id' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
?>