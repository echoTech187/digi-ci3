<?php defined('BASEPATH') or exit('No direct script access allowed');

class Mutation_model extends CI_Model
{
    private static $cached_total = null;
 
    private function _get_datatables_query($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        $this->db->select("
            mutation.id, 
            mutation.ref_merchantId, 
            mutation.c_datetime, 
            mutation.c_potition,
            IF(mutation.c_potition = 'Credit', cashin.ref_cashinChannelId, cashout.ref_cashoutChannelId) AS channelName,
            IF(mutation.c_potition = 'Credit', mutation.ref_cashinId, mutation.ref_cashoutId) AS refLog,
            IF(mutation.c_potition = 'Credit', cashin.c_datetime, cashout.c_datetime) AS timeRefLog,
            IF(mutation.c_potition = 'Credit', cashin.c_description, cashout.c_description) AS description,
            IF(mutation.c_potition = 'Credit', cashin.c_invoiceNo, cashout.c_invoiceNo) AS refNoLog,
            mutation.c_amount,
            mutation.c_balanceAfter 
        ", FALSE);
        $this->db->from('mutation');
        // Optimized: Only join cashin/cashout if we actually need the columns for display
        $this->db->join('cashin', 'cashin.ref_merchantId = mutation.ref_merchantId AND cashin.id = mutation.ref_cashinId', 'left');
        $this->db->join('cashout', 'cashout.ref_merchantId = mutation.ref_merchantId AND cashout.id = mutation.ref_cashoutId', 'left');
        $this->db->where('mutation.ref_merchantId', $id);

        if ($search_date_mutation && $search_date_mutation_to) {
            $this->db->where('mutation.c_datetime >=', date('Y-m-d', strtotime($search_date_mutation)) . ' 00:00:00');
            $this->db->where('mutation.c_datetime <=', date('Y-m-d', strtotime($search_date_mutation_to)) . ' 23:59:59');
        } elseif ($search_date_mutation) {
            $formatted_date = date('Y-m-d', strtotime($search_date_mutation));
            $this->db->where('mutation.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('mutation.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if (!empty($position)) {
            $this->db->where('mutation.c_potition', $position);
        }

        if (!empty($channel) && !empty($position)) {
            if ($position === 'Credit') {
                $this->db->where('cashin.ref_cashinChannelId', $channel);
            } elseif ($position === 'Debit') {
                $this->db->where('cashout.ref_cashoutChannelId', $channel);
            }
        }

        // TRULY SMART SEARCH
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($searchValue != "") {
            $safeSearch = $this->db->escape_str($searchValue);
            $matching_ids = [-1];
            
            // 1. Direct ID match
            if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                $matching_ids[] = (int)$searchValue;
            }

            // 2. Search by Invoice No in linked cashin/cashout
            $inv_res_in = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res_in)) {
                $ids_in = array_column($inv_res_in, 'id');
                $mut_res = $this->db->query("SELECT id FROM mutation WHERE ref_cashinId IN (".implode(',', $ids_in).") LIMIT 50")->result();
                if (!empty($mut_res)) $matching_ids = array_merge($matching_ids, array_column($mut_res, 'id'));
            }

            $inv_res_out = $this->db->query("SELECT id FROM cashout WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res_out)) {
                $ids_out = array_column($inv_res_out, 'id');
                $mut_res = $this->db->query("SELECT id FROM mutation WHERE ref_cashoutId IN (".implode(',', $ids_out).") LIMIT 50")->result();
                if (!empty($mut_res)) $matching_ids = array_merge($matching_ids, array_column($mut_res, 'id'));
            }

            $matching_ids = array_unique($matching_ids);

            if (count($matching_ids) > 1) {
                $this->db->where_in('mutation.id', $matching_ids);
            } else {
                // Fallback to position search if no specific ID found
                $this->db->like('mutation.c_potition', $searchValue);
            }
        }

        // Standard DataTables Order
        if (isset($_POST['order'])) {
            $column_order = [null, 'mutation.id', 'mutation.c_datetime', 'mutation.c_potition', 'channelName', 'description', 'mutation.c_amount', 'mutation.c_balanceAfter'];
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('mutation.id', 'DESC');
        }
    }

    public function get_datatables($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        $this->_get_datatables_query($id, $search_date_mutation, $position, $channel, $search_date_mutation_to);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        $is_filtered = ($search_date_mutation || $position || $channel || $search_date_mutation_to || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt($id);
        }

        $this->db->select('count(mutation.id) as total');
        // Optimized: Skip the columns and intensive joins for count if not filtering by channel
        $this->db->from('mutation');
        $this->db->where('mutation.ref_merchantId', $id);

        if ($search_date_mutation && $search_date_mutation_to) {
            $this->db->where('mutation.c_datetime >=', date('Y-m-d', strtotime($search_date_mutation)) . ' 00:00:00');
            $this->db->where('mutation.c_datetime <=', date('Y-m-d', strtotime($search_date_mutation_to)) . ' 23:59:59');
        } elseif ($search_date_mutation) {
            $formatted_date = date('Y-m-d', strtotime($search_date_mutation));
            $this->db->where('mutation.c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('mutation.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if (!empty($position)) {
            $this->db->where('mutation.c_potition', $position);
        }

        if (!empty($channel) && !empty($position)) {
            // Only join if we filter by channel
            if ($position === 'Credit') {
                $this->db->join('cashin', 'cashin.ref_merchantId = mutation.ref_merchantId AND cashin.id = mutation.ref_cashinId');
                $this->db->where('cashin.ref_cashinChannelId', $channel);
            } elseif ($position === 'Debit') {
                $this->db->join('cashout', 'cashout.ref_merchantId = mutation.ref_merchantId AND cashout.Id = mutation.ref_cashoutId');
                $this->db->where('cashout.ref_cashoutChannelId', $channel);
            }
        }

        // TRULY SMART SEARCH in Count
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($searchValue != "") {
            $safeSearch = $this->db->escape_str($searchValue);
            $matching_ids = [-1];
            if (is_numeric($searchValue) && strlen($searchValue) < 15) $matching_ids[] = (int)$searchValue;

            $inv_res_in = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res_in)) {
                $ids_in = array_column($inv_res_in, 'id');
                $mut_res = $this->db->query("SELECT id FROM mutation WHERE ref_cashinId IN (".implode(',', $ids_in).") LIMIT 50")->result();
                if (!empty($mut_res)) $matching_ids = array_merge($matching_ids, array_column($mut_res, 'id'));
            }

            $inv_res_out = $this->db->query("SELECT id FROM cashout WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
            if (!empty($inv_res_out)) {
                $ids_out = array_column($inv_res_out, 'id');
                $mut_res = $this->db->query("SELECT id FROM mutation WHERE ref_cashoutId IN (".implode(',', $ids_out).") LIMIT 50")->result();
                if (!empty($mut_res)) $matching_ids = array_merge($matching_ids, array_column($mut_res, 'id'));
            }

            $matching_ids = array_unique($matching_ids);
            if (count($matching_ids) > 1) {
                $this->db->where_in('mutation.id', $matching_ids);
            } else {
                $this->db->like('mutation.c_potition', $searchValue);
            }
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($id)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // Use table status estimates if no filters beyond merchant ID are needed 
        // (Mutation is always filtered by merchantId, so we still do a count but optimized)
        $this->db->select('count(id) as total');
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $id);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_mutations($limit, $start, $id, $search_date_mutation = null, $position = null, $channel = null)
    {
        // Legacy method for non-AJAX pages if any
        $this->_get_datatables_query($id, $search_date_mutation, $position, $channel);
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_merchant($id)
    {
        $this->db->select('id, c_name, c_email, c_status, c_merchantLevel, c_balanceTotal, c_balanceHold, c_openapiStatus', FALSE);
        return $this->db->where('id', $id)->get('merchant')->result();
    }

    public function count_mutations($refMerchantId, $search_date_mutation = null, $search_potition = null)
    {
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $refMerchantId);
        if ($search_date_mutation) {
            $formatted_date = date('Y-m-d', strtotime($search_date_mutation));
            $this->db->where('c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('c_datetime <=', $formatted_date . ' 23:59:59');
        }
        if ($search_potition) {
            $this->db->where('c_potition', $search_potition);
        }
        return $this->db->count_all_results();
    }


     public function get_cashin_channels($merchantId)
    {
        return array_column(
            $this->db->select('DISTINCT(ref_cashinChannelId) AS channel')
                ->from('cashin')
                ->where('ref_merchantId', $merchantId)
                ->get()
                ->result_array(),
            'channel'
        );
    }

    public function get_cashout_channels($merchantId)
    {
        return array_column(
            $this->db->select('DISTINCT(ref_cashoutChannelId) AS channel')
                ->from('cashout')
                ->where('ref_merchantId', $merchantId)
                ->get()
                ->result_array(),
            'channel'
        );
    }

    public function get_summary($id, $search_date_mutation = null, $search_date_mutation_to = null)
    {
        $this->db->select("
            SUM(CASE WHEN c_potition = 'Credit' THEN c_amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN c_potition = 'Debit' THEN c_amount ELSE 0 END) as total_debit,
            COUNT(*) as total_count
        ");
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $id);

        if ($search_date_mutation && $search_date_mutation_to) {
            $this->db->where('c_datetime >=', date('Y-m-d', strtotime($search_date_mutation)) . ' 00:00:00');
            $this->db->where('c_datetime <=', date('Y-m-d', strtotime($search_date_mutation_to)) . ' 23:59:59');
        } elseif ($search_date_mutation) {
            $formatted_date = date('Y-m-d', strtotime($search_date_mutation));
            $this->db->where('c_datetime >=', $formatted_date . ' 00:00:00');
            $this->db->where('c_datetime <=', $formatted_date . ' 23:59:59');
        }
        return $this->db->get()->row();
    }

    /**
     * Standardized DataTables handler for Mutation list.
     */
    public function get_datatables_handler($id, $filters = [])
    {
        $this->load->library('datatables');
        
        // Safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");

        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $position = $filters['position'] ?? null;
        $channel = $filters['channel'] ?? null;

        $dt = $this->datatables->of('mutation')
            ->select("
                mutation.id, 
                mutation.ref_merchantId, 
                mutation.c_datetime, 
                mutation.c_potition,
                IF(mutation.c_potition = 'Credit', cashin.ref_cashinChannelId, cashout.ref_cashoutChannelId) AS channelName,
                IF(mutation.c_potition = 'Credit', mutation.ref_cashinId, mutation.ref_cashoutId) AS refLog,
                IF(mutation.c_potition = 'Credit', cashin.c_datetime, cashout.c_datetime) AS timeRefLog,
                IF(mutation.c_potition = 'Credit', cashin.c_description, cashout.c_description) AS description,
                IF(mutation.c_potition = 'Credit', cashin.c_invoiceNo, cashout.c_invoiceNo) AS refNoLog,
                mutation.c_amount,
                mutation.c_balanceAfter
            ", FALSE)
            ->join('cashin', 'cashin.ref_merchantId = mutation.ref_merchantId AND cashin.id = mutation.ref_cashinId', 'left')
            ->join('cashout', 'cashout.ref_merchantId = mutation.ref_merchantId AND cashout.id = mutation.ref_cashoutId', 'left')
            ->where('mutation.ref_merchantId', $id);

        if ($search_date && $search_date_to) {
            $dt->where('mutation.c_datetime >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
            $dt->where('mutation.c_datetime <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $dt->where('mutation.c_datetime >=', $formatted_date . ' 00:00:00');
            $dt->where('mutation.c_datetime <=', $formatted_date . ' 23:59:59');
        }

        if (!empty($position)) {
            $dt->where('mutation.c_potition', $position);
        }

        if (!empty($channel) && !empty($position)) {
            if ($position === 'Credit') {
                $dt->where('cashin.ref_cashinChannelId', $channel);
            } elseif ($position === 'Debit') {
                $dt->where('cashout.ref_cashoutChannelId', $channel);
            }
        }

        return $dt->set_column_order([null, 'mutation.id', 'mutation.c_datetime', 'mutation.c_potition', 'channelName', 'description', 'mutation.c_amount', 'mutation.c_balanceAfter'])
            ->set_column_search(['mutation.id', 'mutation.c_potition'])
            ->set_default_order(['mutation.id' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->editColumn('channelName', function($row) {
                return $row->channelName ?: '-';
            })
            ->editColumn('description', function($row) {
                return $row->description ?: '-';
            })
            ->addColumn('c_amount_raw', function($row) { return $row->c_amount; })
            ->addColumn('c_balance_raw', function($row) { return $row->c_balanceAfter; })
            ->addColumn('c_position_raw', function($row) { return $row->c_potition; })
            ->make(true);
    }
}
?>