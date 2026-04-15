<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function get_today_stats()
    {
        $today = date('Y-m-d');
        
        $stats = [
            'total_volume' => 0,
            'total_qty' => 0,
            'total_profit' => 0,
            'qris' => ['amount' => 0, 'qty' => 0, 'profit' => 0],
            'va' => ['amount' => 0, 'qty' => 0, 'profit' => 0],
            'ewallet' => ['amount' => 0, 'qty' => 0, 'profit' => 0],
            'disburse' => ['amount' => 0, 'qty' => 0, 'profit' => 0]
        ];

        // QRIS Today
        $qris = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty, SUM(c_fee - c_feeExternal) as profit')
            ->from('cashin_payment_qris_mpm')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['qris']['amount'] = $qris->amount ?: 0;
        $stats['qris']['qty'] = $qris->qty ?: 0;
        $stats['qris']['profit'] = $qris->profit ?: 0;

        // VA Today
        $va = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty, SUM(c_fee - c_feeExternal) as profit')
            ->from('cashin_payment_va')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['va']['amount'] = $va->amount ?: 0;
        $stats['va']['qty'] = $va->qty ?: 0;
        $stats['va']['profit'] = $va->profit ?: 0;

        // E-Wallet Today
        $ewallet = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty, SUM(c_fee - c_feeExternal) as profit')
            ->from('cashin_payment_ewallet')
            ->where('c_datetimePayment >=', $today.' 00:00:00')
            ->where('c_datetimePayment <=', $today.' 23:59:59')
            ->get()->row();
        $stats['ewallet']['amount'] = $ewallet->amount ?: 0;
        $stats['ewallet']['qty'] = $ewallet->qty ?: 0;
        $stats['ewallet']['profit'] = $ewallet->profit ?: 0;

        // Disbursement Today
        $disburse = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty, SUM(c_fee - c_feeExternal) as profit')
            ->from('cashout_payment_bifast')
            ->where('c_datetime >=', $today.' 00:00:00')
            ->where('c_datetime <=', $today.' 23:59:59')
            ->where('c_status', 'SUCCESS')
            ->get()->row();
        $stats['disburse']['amount'] = $disburse->amount ?: 0;
        $stats['disburse']['qty'] = $disburse->qty ?: 0;
        $stats['disburse']['profit'] = $disburse->profit ?: 0;

        $stats['total_volume'] = $stats['qris']['amount'] + $stats['va']['amount'] + $stats['ewallet']['amount'] + $stats['disburse']['amount'];
        $stats['total_qty'] = $stats['qris']['qty'] + $stats['va']['qty'] + $stats['ewallet']['qty'] + $stats['disburse']['qty'];
        $stats['total_profit'] = $stats['qris']['profit'] + $stats['va']['profit'] + $stats['ewallet']['profit'] + $stats['disburse']['profit'];

        return $stats;
    }

    public function get_monthly_overview()
    {
        $year = date('Y');
        $today = date('Y-m-d');
        $channels = ['qris', 'va', 'ewallet', 'disburse'];
        $data = [];

        // Mapping types in Summary Table to Channel Keys
        $type_map = [
            'QRIS' => 'qris',
            'VA' => 'va',
            'EWALLET' => 'ewallet',
            'BIFAST' => 'disburse'
        ];

        // 1. Initialize data structure
        foreach ($channels as $channel) {
            $data[$channel] = array_fill(1, 12, 0);
        }

        // 2. Fetch Historical Sums from Summary Table (Total up to yesterday or today's already recapped data)
        $this->db->select("MONTH(summary_date) as month, transaction_type, SUM(total_amount) as amount");
        $this->db->from('tr_summary_daily');
        $this->db->where('summary_date >=', $year . '-01-01');
        $this->db->where('summary_date <', $today); // History only
        $this->db->where_in('transaction_type', array_keys($type_map));
        $monthly_hist = $this->db->group_by("MONTH(summary_date), transaction_type")->get()->result_array();

        foreach ($monthly_hist as $row) {
            $ch = $type_map[$row['transaction_type']];
            $data[$ch][(int)$row['month']] += (float)$row['amount'];
        }

        // 3. Add Live Today's Data
        $live_stats = $this->get_today_stats();
        $current_month = (int)date('n');

        $data['qris'][$current_month] += (float)$live_stats['qris']['amount'];
        $data['va'][$current_month] += (float)$live_stats['va']['amount'];
        $data['ewallet'][$current_month] += (float)$live_stats['ewallet']['amount'];
        $data['disburse'][$current_month] += (float)$live_stats['disburse']['amount'];

        // Format for output (array of values)
        foreach ($data as $ch => $months) {
            $data[$ch] = array_values($months);
        }

        return $data;
    }

    public function get_recent_mutations($limit = 10)
    {
        // Optimized: LIMIT inside subquery BEFORE JOIN to avoid scanning millions of rows
        $sql = "
            (SELECT t.date, CAST('QRIS' AS CHAR) as type, t.amount, CAST(m.c_status AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, ref_merchantId FROM cashin_payment_qris_mpm ORDER BY c_datetime DESC LIMIT 5) t
             JOIN merchant m ON m.id = t.ref_merchantId)
            UNION ALL
            (SELECT t.date, CAST('VA' AS CHAR) as type, t.amount, CAST(m.c_status AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, ref_merchantId FROM cashin_payment_va ORDER BY c_datetime DESC LIMIT 5) t
             JOIN merchant m ON m.id = t.ref_merchantId)
             UNION ALL
            (SELECT t.date, CAST('E-WALLET' AS CHAR) as type, t.amount, CAST('PAID' AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetimePayment as date, c_amount as amount, ref_merchantId FROM cashin_payment_ewallet ORDER BY c_datetimePayment DESC LIMIT 5) t
             JOIN merchant m ON m.id = t.ref_merchantId)
            UNION ALL
            (SELECT t.date, CAST('DISBURSE' AS CHAR) as type, t.amount, CAST(t.status AS CHAR) as status, CAST(m.c_name AS CHAR) as merchant 
             FROM (SELECT c_datetime as date, c_amount as amount, c_status as status, ref_merchantId FROM cashout_payment_bifast WHERE c_status = 'SUCCESS' ORDER BY c_datetime DESC LIMIT 5) t
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
