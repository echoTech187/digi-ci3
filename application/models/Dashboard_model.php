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
            ->where('DATE(c_datetimePayment)', $today)
            ->get()->row();
        $stats['qris']['amount'] = $qris->amount != null ? $qris->amount : 0;
        $stats['qris']['qty'] = $qris->qty != null ? $qris->qty : 0;

        // VA Today
        $va = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashin_payment_va')
            ->where('DATE(c_datetimePayment)', $today)
            ->get()->row();
        $stats['va']['amount'] = $va->amount != null ? $va->amount : 0;
        $stats['va']['qty'] = $va->qty != null ? $va->qty : 0;

        // E-Wallet Today
        $ewallet = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashin_payment_ewallet')
            ->where('DATE(c_datetimePayment)', $today)
            ->get()->row();
        $stats['ewallet']['amount'] = $ewallet->amount != null ? $ewallet->amount : 0;
        $stats['ewallet']['qty'] = $ewallet->qty != null ? $ewallet->qty : 0;

        // Disbursement Today
        $disburse = $this->db->select('SUM(c_amount) as amount, COUNT(id) as qty')
            ->from('cashout_payment_bifast')
            ->where('DATE(c_datetime)', $today)
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
        $channels = ['qris', 'va', 'ewallet', 'disburse'];
        $data = [];

        foreach ($channels as $channel) {
            $table = '';
            $date_field = '';
            $wheres = [];
            switch ($channel) {
                case 'qris': $table = 'cashin_payment_qris_mpm'; $date_field = 'c_datetimePayment'; break;
                case 'va': $table = 'cashin_payment_va'; $date_field = 'c_datetimePayment'; break;
                case 'ewallet': $table = 'cashin_payment_ewallet'; $date_field = 'c_datetimePayment'; break;
                case 'disburse': $table = 'cashout_payment_bifast'; $date_field = 'c_datetime'; $wheres = ['c_status' => 'SUCCESS']; break;
            }

            $monthly = $this->db->select("MONTH($date_field) as month, SUM(c_amount) as amount")
                ->from($table)
                ->where("YEAR($date_field)", $year)
                ->where($wheres)
                ->group_by("MONTH($date_field)")
                ->get()->result_array();

            $formatted = array_fill(1, 12, 0);
            foreach ($monthly as $row) {
                $formatted[(int)$row['month']] = (float)$row['amount'];
            }
            $data[$channel] = array_values($formatted);
        }

        return $data;
    }

    public function get_recent_mutations($limit = 10)
    {
        // Unified recent activity from all payments and disburse
        $sql = "
            (SELECT c_datetime as date, CAST('QRIS' AS CHAR) as type, c_amount as amount, CAST(c_status AS CHAR) as status, CAST(merchant.c_name AS CHAR) as merchant 
             FROM cashin_payment_qris_mpm 
             JOIN merchant ON merchant.id = cashin_payment_qris_mpm.ref_merchantId
             ORDER BY c_datetime DESC LIMIT 5)
            UNION ALL
            (SELECT c_datetime as date, CAST('VA' AS CHAR) as type, c_amount as amount, CAST(c_status AS CHAR) as status, CAST(merchant.c_name AS CHAR) as merchant 
             FROM cashin_payment_va 
             JOIN merchant ON merchant.id = cashin_payment_va.ref_merchantId
             ORDER BY c_datetime DESC LIMIT 5)
             UNION ALL
            (SELECT c_datetimePayment as date, CAST('E-WALLET' AS CHAR) as type, c_amount as amount, CAST('PAID' AS CHAR) as status, CAST(merchant.c_name AS CHAR) as merchant 
             FROM cashin_payment_ewallet 
             JOIN merchant ON merchant.id = cashin_payment_ewallet.ref_merchantId
             ORDER BY c_datetimePayment DESC LIMIT 5)
            UNION ALL
            (SELECT c_datetime as date, CAST('DISBURSE' AS CHAR) as type, c_amount as amount, CAST(cashout_payment_bifast.c_status AS CHAR) as status, CAST(merchant.c_name AS CHAR) as merchant 
             FROM cashout_payment_bifast
             JOIN merchant ON merchant.id = cashout_payment_bifast.ref_merchantId
             WHERE cashout_payment_bifast.c_status = 'SUCCESS'
             ORDER BY c_datetime DESC LIMIT 5)
            ORDER BY date DESC LIMIT $limit
        ";
        return $this->db->query($sql)->result();
    }

    public function get_merchant_count()
    {
        return $this->db->count_all('merchant');
    }

    public function get_top_merchants($limit = 5)
    {
        $today = date('Y-m-d');
        // Simplified top merchants based on QRIS volume today
        return $this->db->select('merchant.c_name, SUM(cashin_payment_qris_mpm.c_amount) as volume')
            ->from('cashin_payment_qris_mpm')
            ->join('merchant', 'merchant.id = cashin_payment_qris_mpm.ref_merchantId')
            ->where('DATE(c_datetimePayment)', $today)
            ->group_by('merchant.id')
            ->order_by('volume', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
}
