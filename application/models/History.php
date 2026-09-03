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

    // Request-level caching
    private static $cached_total = null;



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
        $search_status = $filters['status'] ?? null;

        $dt = $this->datatables->of('cashout_payment_ppob cpp')
            ->select('cpp.*, m.c_name as merchant_name, "-" as sub_account_name, c.c_invoiceNo', FALSE)
            ->join('merchant m', 'cpp.ref_merchantId = m.id', 'left')
            ->join('cashout c', 'c.id = cpp.ref_cashoutId', 'left');

        if ($search_date) {
            $dt->where('cpp.c_datetime >=', $search_date . ' 00:00:00');
            $dt->where('cpp.c_datetime <=', $search_date . ' 23:59:59');
        }
        if ($search_merchant) {
            $dt->where('cpp.ref_merchantId', $search_merchant);
        }
        
        // Force display only Paid/Success status
        // $dt->where("cpp.c_status IN ('Paid', 'Success', 'SUCCESS', 'PAID', 'paid', 'success')", NULL, FALSE);

        if ($search_status) {
            $dt->where('cpp.c_status', $search_status);
        }
        if (isset($filters['invoice']) && $filters['invoice']) {
            $searchVal = $this->db->escape_str($filters['invoice']);
            $dt->where("(c.c_invoiceNo = '$searchVal' OR cpp.c_phone LIKE '$searchVal%' OR cpp.ref_cashoutChannelId LIKE '$searchVal%')", NULL, FALSE);
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

    /**
     * Get all transaction history (PPOB, VA, QRIS, E-Wallet, BI-FAST) for a specific merchant.
     */
    public function get_merchant_all_history_datatables_handler($merchant_id)
    {
        $this->load->library('datatables');
        $merchant_id = intval($merchant_id);

        $union_sql = "
            SELECT 
                m.c_name AS name_merchant,
                cpp.c_datetime,
                cpp.ref_cashoutChannelId AS ref_cashoutChannelId,
                c.c_invoiceNo,
                cpp.c_phone AS c_phone,
                cpp.c_amount,
                cpp.c_status
            FROM cashout_payment_ppob cpp
            LEFT JOIN cashout c ON cpp.ref_cashoutId = c.id
            LEFT JOIN merchant m ON cpp.ref_merchantId = m.id
            WHERE cpp.ref_merchantId = {$merchant_id}
            
            UNION ALL
            
            SELECT 
                m.c_name AS name_merchant,
                cpv.c_datetime,
                cpv.ref_cashinChannelId AS ref_cashoutChannelId,
                c.c_invoiceNo,
                cpv.c_vaNumber AS c_phone,
                cpv.c_amount,
                'SUCCESS' AS c_status
            FROM cashin_payment_va cpv
            LEFT JOIN cashin c ON cpv.ref_cashinId = c.id
            LEFT JOIN merchant m ON cpv.ref_merchantId = m.id
            WHERE cpv.ref_merchantId = {$merchant_id}

            UNION ALL

            SELECT 
                m.c_name AS name_merchant,
                cpq.c_datetime,
                'QRIS' AS ref_cashoutChannelId,
                c.c_invoiceNo,
                '-' AS c_phone,
                cpq.c_amount,
                'SUCCESS' AS c_status
            FROM cashin_payment_qris_mpm cpq
            LEFT JOIN cashin c ON cpq.ref_cashinId = c.id
            LEFT JOIN merchant m ON cpq.ref_merchantId = m.id
            WHERE cpq.ref_merchantId = {$merchant_id}

            UNION ALL

            SELECT 
                m.c_name AS name_merchant,
                cpe.c_datetime,
                cpe.ref_cashinChannelId AS ref_cashoutChannelId,
                c.c_invoiceNo,
                '-' AS c_phone,
                cpe.c_amount,
                'SUCCESS' AS c_status
            FROM cashin_payment_ewallet cpe
            LEFT JOIN cashin c ON cpe.ref_cashinId = c.id
            LEFT JOIN merchant m ON cpe.ref_merchantId = m.id
            WHERE cpe.ref_merchantId = {$merchant_id}

            UNION ALL

            SELECT 
                m.c_name AS name_merchant,
                cpb.c_datetime,
                'BI-FAST' AS ref_cashoutChannelId,
                c.c_invoiceNo,
                cpb.c_accountNo AS c_phone,
                cpb.c_amount,
                cpb.c_status
            FROM cashout_payment_bifast cpb
            LEFT JOIN cashout c ON cpb.ref_cashoutId = c.id
            LEFT JOIN merchant m ON cpb.ref_merchantId = m.id
            WHERE cpb.ref_merchantId = {$merchant_id}
        ";

        // Calculate counts
        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM ({$union_sql}) AS t");
        $recordsTotal = $total_query->row() ? intval($total_query->row()->total) : 0;

        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $where_clause = '';
        if ($searchValue !== '') {
            $safeSearch = $this->db->escape_str($searchValue);
            $where_clause = " WHERE (
                t.c_invoiceNo LIKE '%{$safeSearch}%' OR 
                t.c_phone LIKE '%{$safeSearch}%' OR 
                t.ref_cashoutChannelId LIKE '%{$safeSearch}%' OR 
                t.name_merchant LIKE '%{$safeSearch}%'
            )";
        }

        if ($searchValue !== '') {
            $filtered_query = $this->db->query("SELECT COUNT(*) AS total FROM ({$union_sql}) AS t {$where_clause}");
            $recordsFiltered = $filtered_query->row() ? intval($filtered_query->row()->total) : 0;
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Determine ordering
        $columns = [
            0 => null,
            1 => 't.name_merchant',
            2 => 't.c_datetime',
            3 => 't.ref_cashoutChannelId',
            4 => 't.c_invoiceNo',
            5 => 't.c_phone',
            6 => 't.c_amount',
            7 => 't.c_status'
        ];

        $order_clause = ' ORDER BY t.c_datetime DESC ';
        if (isset($_POST['order'])) {
            $col_idx = intval($_POST['order']['0']['column']);
            $col_dir = $_POST['order']['0']['dir'] === 'asc' ? 'ASC' : 'DESC';
            if (isset($columns[$col_idx]) && $columns[$col_idx] !== null) {
                $order_clause = " ORDER BY {$columns[$col_idx]} {$col_dir} ";
            }
        }

        // Determine pagination limits
        $limit_clause = '';
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $start = intval($_POST['start']);
            $length = intval($_POST['length']);
            $limit_clause = " LIMIT {$start}, {$length}";
        }

        $data_sql = "SELECT * FROM ({$union_sql}) AS t {$where_clause} {$order_clause} {$limit_clause}";
        $list = $this->db->query($data_sql)->result();

        // Pass manual results to datatables
        $original_start = isset($_POST['start']) ? $_POST['start'] : 0;
        $_POST['start'] = 0;

        $output = $this->datatables->of('merchant') // Dummy table to satisfy the library structure
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