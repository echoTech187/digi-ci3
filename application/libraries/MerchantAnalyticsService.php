<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MerchantAnalyticsService
 * Encapsulates complex aggregate metrics, channel breakdowns, trends, and submerchant reports for merchant analytics.
 */
class MerchantAnalyticsService
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function renderMerchantDetail($id)
    {
        if (!$id) {
            redirect('merchant/manage');
            return;
        }

        $merchant = $this->CI->Merchant->get_merchant_by_id($id);
        if (!$merchant) {
            $this->CI->session->set_flashdata('error', 'Merchant not found.');
            redirect('merchant/manage');
            return;
        }

        $submerchant_count = $this->CI->db->where('parent_merchant_id', $id)
            ->where('c_merchantLevel >', 0)
            ->count_all_results('merchant');

        $data = [
            'title'             => 'Detail Merchant: ' . ($merchant['c_name'] ?? 'Unknown'),
            'user'              => $this->CI->Model_user->view_user()->row_array(),
            'merchant'          => $merchant,
            'merchant_id'       => $id,
            'submerchant_count' => $submerchant_count,
            'supervisors'       => $this->CI->Merchant->get_all_supervisors(),
            'default_period'    => 'last_7_days'
        ];
        $this->CI->load->view('merchant/detail_analytics', $data);
    }

    public function ajaxMerchantAnalyticsOverview()
    {
        session_write_close();
        $merchant_id = $this->CI->input->get('merchant_id') ?: $this->CI->input->post('merchant_id');
        $period = $this->CI->input->get('period') ?: 'last_7_days';
        $dates = $this->_resolveDateRange($period);

        $data = $this->getOverviewData($merchant_id, $dates['start_date'], $dates['end_date']);

        return $this->CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data['summary']
            ]));
    }

    public function ajaxMerchantAnalyticsTrends()
    {
        session_write_close();
        $merchant_id = $this->CI->input->get('merchant_id') ?: $this->CI->input->post('merchant_id');
        $period = $this->CI->input->get('period') ?: 'last_7_days';
        $dates = $this->_resolveDateRange($period);

        $data = $this->getOverviewData($merchant_id, $dates['start_date'], $dates['end_date']);

        return $this->CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data['trends']
            ]));
    }

    public function ajaxMerchantAnalyticsChannelBreakdown()
    {
        session_write_close();
        $merchant_id = $this->CI->input->get('merchant_id') ?: $this->CI->input->post('merchant_id');
        $period = $this->CI->input->get('period') ?: 'last_7_days';
        $dates = $this->_resolveDateRange($period);

        $data = $this->getOverviewData($merchant_id, $dates['start_date'], $dates['end_date']);

        return $this->CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data['channels']
            ]));
    }

    public function ajaxMerchantSubmerchants()
    {
        session_write_close();
        $merchant_id = $this->CI->input->get('merchant_id') ?: $this->CI->input->post('merchant_id');
        $period = $this->CI->input->get('period') ?: 'last_7_days';
        $dates = $this->_resolveDateRange($period);

        $data = $this->getOverviewData($merchant_id, $dates['start_date'], $dates['end_date']);

        return $this->CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data['sub_merchants']
            ]));
    }

    private function _resolveDateRange($period)
    {
        $today = new \DateTime('today');
        if ($period === 'yesterday') {
            $s = (clone $today)->modify('-1 day');
            $e = clone $s;
        } elseif ($period === 'last_month') {
            $s = (clone $today)->modify('first day of last month');
            $e = (clone $today)->modify('last day of last month');
        } elseif ($period === 'this_month') {
            $s = (clone $today)->modify('first day of this month');
            $e = clone $today;
        } else {
            $s = (clone $today)->modify('-6 days');
            $e = clone $today;
        }

        return [
            'start_date' => $s->format('Y-m-d'),
            'end_date'   => $e->format('Y-m-d')
        ];
    }

    public function getOverviewData($merchant_id, $start_date, $end_date)
    {
        $merchant_id = intval($merchant_id);
        $s_datetime = $start_date . ' 00:00:00';
        $e_datetime = $end_date . ' 23:59:59';

        // 1. Batch Channels Summary (1 single query)
        $summary_rows = $this->CI->db->query("
            SELECT 'PPOB' AS channel, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt, 0 AS fee, 
                   COALESCE(SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END), 0) AS success_amt, 
                   SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt 
            FROM cashout_payment_ppob 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            UNION ALL
            SELECT 'VA' AS channel, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt, COALESCE(SUM(c_fee), 0) AS fee, 
                   COALESCE(SUM(c_amount), 0) AS success_amt, COUNT(*) AS success_cnt 
            FROM cashin_payment_va 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            UNION ALL
            SELECT 'QRIS' AS channel, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt, COALESCE(SUM(c_fee), 0) AS fee, 
                   COALESCE(SUM(c_amount), 0) AS success_amt, COUNT(*) AS success_cnt 
            FROM cashin_payment_qris_mpm 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            UNION ALL
            SELECT 'EWallet' AS channel, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt, COALESCE(SUM(c_fee), 0) AS fee, 
                   COALESCE(SUM(c_amount), 0) AS success_amt, COUNT(*) AS success_cnt 
            FROM cashin_payment_ewallet 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            UNION ALL
            SELECT 'BiFast' AS channel, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt, COALESCE(SUM(c_fee), 0) AS fee, 
                   COALESCE(SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END), 0) AS success_amt, 
                   SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt 
            FROM cashout_payment_bifast 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
        ", [
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime
        ])->result_array();

        $channels = [
            'PPOB'    => ['cnt' => 0, 'amt' => 0.0, 'fee' => 0.0, 'success_cnt' => 0, 'success_amt' => 0.0],
            'VA'      => ['cnt' => 0, 'amt' => 0.0, 'fee' => 0.0, 'success_cnt' => 0, 'success_amt' => 0.0],
            'QRIS'    => ['cnt' => 0, 'amt' => 0.0, 'fee' => 0.0, 'success_cnt' => 0, 'success_amt' => 0.0],
            'EWallet' => ['cnt' => 0, 'amt' => 0.0, 'fee' => 0.0, 'success_cnt' => 0, 'success_amt' => 0.0],
            'BiFast'  => ['cnt' => 0, 'amt' => 0.0, 'fee' => 0.0, 'success_cnt' => 0, 'success_amt' => 0.0]
        ];

        $summary = [
            'total_cnt'   => 0,
            'total_amt'   => 0.0,
            'total_fee'   => 0.0,
            'success_cnt' => 0,
            'success_amt' => 0.0
        ];

        foreach ($summary_rows as $row) {
            $ch = $row['channel'];
            $cnt = intval($row['cnt']);
            $amt = floatval($row['amt']);
            $fee = floatval($row['fee']);
            $s_cnt = intval($row['success_cnt']);
            $s_amt = floatval($row['success_amt']);

            $channels[$ch] = [
                'cnt'         => $cnt,
                'amt'         => $amt,
                'fee'         => $fee,
                'success_cnt' => $s_cnt,
                'success_amt' => $s_amt
            ];

            $summary['total_cnt'] += $cnt;
            $summary['total_amt'] += $amt;
            $summary['total_fee'] += $fee;
            $summary['success_cnt'] += $s_cnt;
            $summary['success_amt'] += $s_amt;
        }

        // 2. Batch Trends (1 single query for all dates and channels)
        $dates = [];
        $period = new \DatePeriod(new \DateTime($start_date), new \DateInterval('P1D'), (new \DateTime($end_date))->modify('+1 day'));
        foreach ($period as $date) {
            $dates[$date->format('Y-m-d')] = [
                'PPOB'    => 0.0,
                'VA'      => 0.0,
                'QRIS'    => 0.0,
                'EWallet' => 0.0,
                'BiFast'  => 0.0,
                'total'   => 0.0
            ];
        }

        $trend_rows = $this->CI->db->query("
            SELECT 'PPOB' AS channel, DATE(c_datetime) AS tx_date, COALESCE(SUM(c_amount), 0) AS amt 
            FROM cashout_payment_ppob 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? AND c_status = 'SUCCESS' 
            GROUP BY DATE(c_datetime)
            UNION ALL
            SELECT 'VA' AS channel, DATE(c_datetime) AS tx_date, COALESCE(SUM(c_amount), 0) AS amt 
            FROM cashin_payment_va 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? 
            GROUP BY DATE(c_datetime)
            UNION ALL
            SELECT 'QRIS' AS channel, DATE(c_datetime) AS tx_date, COALESCE(SUM(c_amount), 0) AS amt 
            FROM cashin_payment_qris_mpm 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? 
            GROUP BY DATE(c_datetime)
            UNION ALL
            SELECT 'EWallet' AS channel, DATE(c_datetime) AS tx_date, COALESCE(SUM(c_amount), 0) AS amt 
            FROM cashin_payment_ewallet 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? 
            GROUP BY DATE(c_datetime)
            UNION ALL
            SELECT 'BiFast' AS channel, DATE(c_datetime) AS tx_date, COALESCE(SUM(c_amount), 0) AS amt 
            FROM cashout_payment_bifast 
            WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? AND c_status = 'SUCCESS' 
            GROUP BY DATE(c_datetime)
        ", [
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime,
            $merchant_id, $s_datetime, $e_datetime
        ])->result_array();

        foreach ($trend_rows as $r) {
            $tDate = $r['tx_date'];
            $channel = $r['channel'];
            if (isset($dates[$tDate])) {
                $amt = (float) $r['amt'];
                $dates[$tDate][$channel] = $amt;
                $dates[$tDate]['total'] += $amt;
            }
        }

        // 3. Batch Sub-merchants report (1 single query)
        $sub_merchants = $this->CI->db->query("
            SELECT m.id AS sub_merchant_id, m.c_name AS sub_merchant_name, m.c_email AS sub_merchant_email,
                   COALESCE(tx.total_cnt, 0) AS total_cnt, COALESCE(tx.success_cnt, 0) AS success_cnt, COALESCE(tx.success_amt, 0) AS success_amt
            FROM merchant m
            LEFT JOIN (
                SELECT ref_merchantId, SUM(cnt) AS total_cnt, SUM(success_cnt) AS success_cnt, SUM(success_amt) AS success_amt
                FROM (
                    SELECT ref_merchantId, COUNT(*) AS cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END) AS success_amt FROM cashout_payment_ppob WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY ref_merchantId
                    UNION ALL
                    SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt FROM cashin_payment_va WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY ref_merchantId
                    UNION ALL
                    SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt FROM cashin_payment_qris_mpm WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY ref_merchantId
                    UNION ALL
                    SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt FROM cashin_payment_ewallet WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY ref_merchantId
                    UNION ALL
                    SELECT ref_merchantId, COUNT(*) AS cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END) AS success_amt FROM cashout_payment_bifast WHERE c_datetime >= ? AND c_datetime <= ? GROUP BY ref_merchantId
                ) unified_tx GROUP BY ref_merchantId
            ) tx ON m.id = tx.ref_merchantId
            WHERE m.parent_merchant_id = ? AND m.c_merchantLevel > 0
            ORDER BY success_amt DESC, total_cnt DESC
        ", [
            $s_datetime, $e_datetime,
            $s_datetime, $e_datetime,
            $s_datetime, $e_datetime,
            $s_datetime, $e_datetime,
            $s_datetime, $e_datetime,
            $merchant_id
        ])->result_array();

        return [
            'summary'       => $summary,
            'channels'      => $channels,
            'trends'        => [
                'labels'   => array_keys($dates),
                'datasets' => [
                    'PPOB'    => array_column($dates, 'PPOB'),
                    'VA'      => array_column($dates, 'VA'),
                    'QRIS'    => array_column($dates, 'QRIS'),
                    'EWallet' => array_column($dates, 'EWallet'),
                    'BiFast'  => array_column($dates, 'BiFast'),
                    'total'   => array_column($dates, 'total')
                ]
            ],
            'sub_merchants' => $sub_merchants
        ];
    }
}
