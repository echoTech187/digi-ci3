<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function get_today_stats()
    {
        $today = date('Y-m-d');
        
        $stats = [
            'total_volume' => 0,
            'total_qty' => 0,
            'qris' => ['amount' => 0, 'qty' => 0],
            'va' => ['amount' => 0, 'qty' => 0],
            'ewallet' => ['amount' => 0, 'qty' => 0],
            'disburse' => ['amount' => 0, 'qty' => 0]
        ];

        // QRIS Today
        $qris = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashin_payment_qris_mpm')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['qris']['amount'] = $qris->amount != null ? $qris->amount : 0;
        $stats['qris']['qty'] = $qris->qty != null ? $qris->qty : 0;

        // VA Today
        $va = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashin_payment_va')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['va']['amount'] = $va->amount != null ? $va->amount : 0;
        $stats['va']['qty'] = $va->qty != null ? $va->qty : 0;

        // E-Wallet Today
        $ewallet = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashin_payment_ewallet')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['ewallet']['amount'] = $ewallet->amount != null ? $ewallet->amount : 0;
        $stats['ewallet']['qty'] = $ewallet->qty != null ? $ewallet->qty : 0;

        // Disbursement Today
        $disburse = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashout_payment_bifast')
            ->where('c_datetime >=', $today.' 00:00:00')
            ->where('c_datetime <=', $today.' 23:59:59')
            ->where('c_status', 'SUCCESS')
            ->get()->row();
        $stats['disburse']['amount'] = $disburse->amount != null ? $disburse->amount : 0;
        $stats['disburse']['qty'] = $disburse->qty != null ? $disburse->qty : 0;

        $stats['total_volume'] = $stats['qris']['amount'] + $stats['va']['amount'] + $stats['ewallet']['amount'] + $stats['disburse']['amount'];
        $stats['total_qty'] = $stats['qris']['qty'] + $stats['va']['qty'] + $stats['ewallet']['qty'] + $stats['disburse']['qty'];

        return $stats;
    }

    public function get_monthly_overview()
    {
        $year = date('Y');
        $current_month = (int)date('m');
        $channels = ['qris', 'va', 'ewallet', 'disburse'];
        
        $this->load->driver('cache', array('adapter' => 'file'));
        $history_cache_key = "dashboard_monthly_history_{$year}_v1";
        $history = $this->cache->get($history_cache_key) ?: [];

        $data = [];
        foreach ($channels as $channel) {
            $table = ''; $date_field = ''; $status_field = '';
            switch ($channel) {
                case 'qris': $table = 'cashin_payment_qris_mpm'; $date_field = 'c_datetimePayment'; break;
                case 'va': $table = 'cashin_payment_va'; $date_field = 'c_datetimePayment'; break;
                case 'ewallet': $table = 'cashin_payment_ewallet'; $date_field = 'c_datetimePayment'; break;
                case 'disburse': $table = 'cashout_payment_bifast'; $date_field = 'c_datetime'; $status_field = 'c_status'; break;
            }

            // 1. Initialize channel data with 0s
            $channel_data = array_fill(0, 12, 0);

            // 2. Load past months from history cache if available
            $needs_full_recalc = false;
            if (isset($history[$channel])) {
                for ($m = 1; $m < $current_month; $m++) {
                    $channel_data[$m - 1] = $history[$channel][$m - 1];
                }
            } else {
                $needs_full_recalc = true;
            }

            // 3. Query DB
            if ($needs_full_recalc) {
                // First time: Query everything up to now with grouping
                $this->db->select("MONTH($date_field) as month, SUM(c_amount) as amount");
                $this->db->from($table);
                $this->db->where("$date_field >=", $year . '-01-01 00:00:00');
                $this->db->where("$date_field <=", date('Y-m-d') . ' 23:59:59');
                if ($status_field !== '') $this->db->where($status_field, 'SUCCESS');
                
                $results = $this->db->group_by("MONTH($date_field)")->get()->result_array();
                foreach ($results as $row) {
                    $m = (int)$row['month'];
                    $channel_data[$m - 1] = (float)$row['amount'];
                }
            } else {
                // Regular: ONLY query current month TOTAL (ULTRA FAST - NO GROUP BY)
                $this->db->select("SUM(c_amount) as amount");
                $this->db->from($table);
                $this->db->where("$date_field >=", $year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00');
                $this->db->where("$date_field <=", date('Y-m-d') . ' 23:59:59');
                if ($status_field !== '') $this->db->where($status_field, 'SUCCESS');
                
                $row = $this->db->get()->row_array();
                $channel_data[$current_month - 1] = (float)($row['amount'] ?? 0);
            }

            $data[$channel] = $channel_data;
        }

        // 4. Update history cache for past months
        $history_payload = [];
        foreach ($data as $chan => $months) {
            $history_payload[$chan] = $months;
        }
        // Cache history for 24 hours (it only changes when a month rolls over or if we force recalc)
        $this->cache->save($history_cache_key, $history_payload, 86400);

        return $data;
    }

    public function get_recent_mutations($limit = 10)
    {
        // Optimized: LIMIT inside subquery BEFORE JOIN to avoid scanning millions of rows
        $today = date('Y-m-d');
        $start_time = $today . ' 00:00:00';
        $end_time = $today . ' 23:59:59';

        $sql = "
            (SELECT t.date, CAST('QRIS' AS CHAR) as type, t.amount, CAST('SUCCESS' AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, ref_merchantId FROM cashin_payment_qris_mpm WHERE c_datetime >= '$start_time' AND c_datetime <= '$end_time' ORDER BY c_datetime DESC LIMIT 20) t
             JOIN merchant m ON m.id = t.ref_merchantId)
            UNION ALL
            (SELECT t.date, CAST('VA' AS CHAR) as type, t.amount, CAST('SUCCESS' AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, ref_merchantId FROM cashin_payment_va WHERE c_datetime >= '$start_time' AND c_datetime <= '$end_time' ORDER BY c_datetime DESC LIMIT 20) t
             JOIN merchant m ON m.id = t.ref_merchantId)
             UNION ALL
            (SELECT t.date, CAST('E-WALLET' AS CHAR) as type, t.amount, CAST('PAID' AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetimePayment as date, c_amount as amount, ref_merchantId FROM cashin_payment_ewallet WHERE c_datetimePayment >= '$start_time' AND c_datetimePayment <= '$end_time' ORDER BY c_datetimePayment DESC LIMIT 20) t
             JOIN merchant m ON m.id = t.ref_merchantId)
            UNION ALL
            (SELECT t.date, CAST('DISBURSE' AS CHAR) as type, t.amount, CAST(t.status AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, c_status as status, ref_merchantId FROM cashout_payment_bifast WHERE c_status = 'SUCCESS' AND c_datetime >= '$start_time' AND c_datetime <= '$end_time' ORDER BY c_datetime DESC LIMIT 20) t
             JOIN merchant m ON m.id = t.ref_merchantId)
            ORDER BY date DESC LIMIT $limit
        ";
        return $this->db->query($sql)->result();
    }

    public function get_merchant_count()
    {
        return $this->db->select('count(id) as total')->from('merchant')->get()->row()->total;
    }

    public function get_top_merchants($limit = 5)
    {
        $today = date('Y-m-d');
        // Simplified top merchants based on QRIS volume today
        return $this->db->select('merchant.c_name, SUM(cashin_payment_qris_mpm.c_amount) as volume')
            ->from('cashin_payment_qris_mpm')
            ->join('merchant', 'merchant.id = cashin_payment_qris_mpm.ref_merchantId')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->group_by('merchant.id')
            ->order_by('volume', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
}
