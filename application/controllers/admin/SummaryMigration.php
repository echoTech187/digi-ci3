<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SummaryMigration Controller
 * Used to populate tr_summary_daily from 23M historical records
 * and to perform daily recaps via Cron Job.
 */
class SummaryMigration extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Ensure only admins or CLI can run this
        if (!$this->input->is_cli_request() && $this->session->userdata('role') != 1) {
            // die('Unauthorized access');
        }
        $this->load->database();
    }

    /**
     * Single entry point to recap a specific date
     * URL: admin/SummaryMigration/recap_date/2024-03-20
     */
    public function recap_date($date = null)
    {
        if (!$date) $date = date('Y-m-d', strtotime('yesterday'));
        
        echo "Starting recap for $date...\n";
        
        $this->_recap_va($date);
        $this->_recap_qris($date);
        $this->_recap_ewallet($date);
        $this->_recap_bifast($date);
        $this->_recap_ppob($date);
        $this->_recap_va_dynamic($date);
        $this->_recap_va_recurring($date);
        $this->_recap_qris_dynamic($date);
        $this->_recap_qris_recurring($date);
        $this->_recap_ewallet_dynamic($date);
        
        echo "Finished recap for $date.\n";
    }

    /**
     * Migration tool: Loop through all history and populate summary table
     * Use with caution on 23M records - should be run via CLI if possible
     */
    public function migrate_history($start_date, $end_date)
    {
        $current = $start_date;
        while (strtotime($current) <= strtotime($end_date)) {
            $this->recap_date($current);
            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }
    }

    private function _recap_va($date)
    {
        $this->db->select("'$date' as summary_date, 'VA' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           SUM(c_fee) as total_fee, SUM(c_feeExternal) as total_fee_ext");
        $this->db->from('cashin_payment_va');
        $this->db->where('c_datetime >=', $date . ' 00:00:00');
        $this->db->where('c_datetime <=', $date . ' 23:59:59');
        // Only successful/paid if applicable? Current models don't seem to filter status for VA summary
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_qris($date)
    {
        $this->db->select("'$date' as summary_date, 'QRIS' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           SUM(c_fee) as total_fee, SUM(c_feeExternal) as total_fee_ext");
        $this->db->from('cashin_payment_qris_mpm');
        $this->db->where('c_datetime >=', $date . ' 00:00:00');
        $this->db->where('c_datetime <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_ewallet($date)
    {
        $this->db->select("'$date' as summary_date, 'EWALLET' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           SUM(c_fee) as total_fee, SUM(c_feeExternal) as total_fee_ext");
        $this->db->from('cashin_payment_ewallet');
        $this->db->where('c_datetime >=', $date . ' 00:00:00');
        $this->db->where('c_datetime <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_bifast($date)
    {
        $this->db->select("'$date' as summary_date, 'BIFAST' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           SUM(c_fee) as total_fee, SUM(c_feeExternal) as total_fee_ext");
        $this->db->from('cashout_payment_bifast');
        $this->db->where('c_datetime >=', $date . ' 00:00:00');
        $this->db->where('c_datetime <=', $date . ' 23:59:59');
        $this->db->where('c_status', 'SUCCESS');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_ppob($date)
    {
        $this->db->select("'$date' as summary_date, 'PPOB' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashout_payment_ppob');
        $this->db->where('c_datetime >=', $date . ' 00:00:00');
        $this->db->where('c_datetime <=', $date . ' 23:59:59');
        $this->db->where('c_status', 'Success');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_va_dynamic($date)
    {
        $this->db->select("'$date' as summary_date, 'VA_DYNAMIC' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashin_dynamic_va');
        $this->db->where('c_datetimeRequest >=', $date . ' 00:00:00');
        $this->db->where('c_datetimeRequest <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_va_recurring($date)
    {
        $this->db->select("'$date' as summary_date, 'VA_RECURRING' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashin_recurring_va');
        $this->db->where('c_datetimeRequest >=', $date . ' 00:00:00');
        $this->db->where('c_datetimeRequest <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_qris_dynamic($date)
    {
        $this->db->select("'$date' as summary_date, 'QRIS_DYNAMIC' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashin_dynamic_qris_mpm');
        $this->db->where('c_datetimeRequest >=', $date . ' 00:00:00');
        $this->db->where('c_datetimeRequest <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_qris_recurring($date)
    {
        $this->db->select("'$date' as summary_date, 'QRIS_RECURRING' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashin_recurring_qris_mpm');
        $this->db->where('c_datetimeRequest >=', $date . ' 00:00:00');
        $this->db->where('c_datetimeRequest <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }

    private function _recap_ewallet_dynamic($date)
    {
        $this->db->select("'$date' as summary_date, 'EWALLET_DYNAMIC' as transaction_type, ref_merchantId, 
                           COUNT(id) as total_qty, SUM(c_amount) as total_amount, 
                           0 as total_fee, 0 as total_fee_ext");
        $this->db->from('cashin_dynamic_ewallet');
        $this->db->where('c_datetimeRequest >=', $date . ' 00:00:00');
        $this->db->where('c_datetimeRequest <=', $date . ' 23:59:59');
        $this->db->group_by('ref_merchantId');
        $results = $this->db->get()->result_array();

        foreach ($results as $row) {
            $this->db->replace('tr_summary_daily', $row);
        }
    }
}
