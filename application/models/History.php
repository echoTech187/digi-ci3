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
        $query = "select * from merchant ";
        return $this->db->query($query)->result();
    }

    // --- DataTables Server-Side Processing ---
    var $table = 'cashout_payment_ppob cpp';
    var $column_order = array(null, 'm.c_name', 'cpp.c_datetime', 'cpp.ref_cashoutChannelId', 'cpp.c_invoiceNo', 'cpp.c_phone', 'cpp.c_amount', 'cpp.c_status');
    var $column_search = array('cpp.id', 'm.c_name', 'cpp.c_invoiceNo', 'cpp.c_phone', 'cpp.ref_cashoutChannelId');
    var $order = array('cpp.id' => 'desc');

    private function _get_datatables_query($search_date = null, $search_merchant = null)
    {
        $this->db->select('cpp.*, m.c_name as name_merchant');
        $this->db->from($this->table);
        $this->db->join('merchant m', 'cpp.ref_merchantId = m.id', 'left');

        if ($search_date) {
            $this->db->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpp.ref_merchantId', $search_merchant);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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

        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $this->db->join('merchant m', 'cpp.ref_merchantId = m.id', 'left');
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_date = null, $search_merchant = null)
    {
        $this->db->select('count(cpp.id) as total');
        // Optimized: No joins needed for total count
        $this->db->from($this->table);
        if ($search_date) {
            $this->db->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $this->db->where('cpp.ref_merchantId', $search_merchant);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function get_summary($search_date = null, $search_merchant = null)
    {
        $today = date('Y-m-d');
        $is_today_included = false;
        $is_history_included = false;

        // Logic to determine if we need today's live data, historical data, or both
        if ($search_date) {
            $search_date_formatted = date('Y-m-d', strtotime($search_date));
            if ($search_date_formatted == $today) {
                $is_today_included = true;
            } else {
                $is_history_included = true;
            }
        } else {
            // No date filter means "All Time" (Today + History)
            $is_today_included = true;
            $is_history_included = true;
        }

        $total_qty = 0;
        $total_amount = 0;

        // 1. Get Historical Data from Summary Table
        if ($is_history_included) {
            $this->db->select('SUM(total_qty) as qty, SUM(total_amount) as amount');
            $this->db->from('tr_summary_daily');
            $this->db->where('transaction_type', 'PPOB');
            if ($search_date) {
                $this->db->where('summary_date', date('Y-m-d', strtotime($search_date)));
            } else {
                $this->db->where('summary_date <', $today);
            }
            if ($search_merchant) {
                $this->db->where('ref_merchantId', $search_merchant);
            }
            $hist = $this->db->get()->row();
            $total_qty += $hist->qty ?: 0;
            $total_amount += $hist->amount ?: 0;
        }

        // 2. Get Live Data for Today from Main Table
        if ($is_today_included) {
            $this->db->select('COUNT(cpp.id) as qty, SUM(cpp.c_amount) as amount');
            $this->db->from($this->table);
            $this->db->where('cpp.c_datetime >=', $today . ' 00:00:00');
            $this->db->where('cpp.c_datetime <=', $today . ' 23:59:59');
            $this->db->where('cpp.c_status', 'Success');
            if ($search_merchant) {
                $this->db->where('cpp.ref_merchantId', $search_merchant);
            }
            $live = $this->db->get()->row();
            $total_qty += $live->qty ?: 0;
            $total_amount += $live->amount ?: 0;
        }

        return [[
            'qty' => $total_qty,
            'amount' => $total_amount
        ]];
    }
}
?>