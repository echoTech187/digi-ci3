<?php defined('BASEPATH') or exit('No direct script access allowed');

class GlobalSearchController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->library('rbac');
        $this->load->model('Model_user');
        $this->load->model('Model_menu');

        is_logged_in();
    }

    public function globalSearch()
    {
        $this->db->query("SET SESSION max_execution_time = 15000");
        ini_set('memory_limit', '1024M');
        try {
            $query = $this->input->get('q');
            if (!$query || strlen($query) < 2) { echo json_encode([]); return; }
            $results = []; $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
            $safeQuery = $this->db->escape_str($query);
            $query_upper = strtoupper($query);
            
            $extracted_date = null;
            if (preg_match('/_([0-9]{6})_/', $query, $matches)) {
                $yymmdd = $matches[1]; $year = "20" . substr($yymmdd, 0, 2); $month = substr($yymmdd, 2, 2); $day = substr($yymmdd, 4, 2);
                if (checkdate($month, $day, $year)) $extracted_date = "$year-$month-$day";
            }
            // Support for GD260505... and similar formats
            if (!$extracted_date && preg_match('/(?:GD|TRX|VA|EW|BF)([0-9]{6})/i', $query, $matches)) {
                $yymmdd = $matches[1]; $year = "20" . substr($yymmdd, 0, 2); $month = substr($yymmdd, 2, 2); $day = substr($yymmdd, 4, 2);
                if (checkdate($month, $day, $year)) $extracted_date = "$year-$month-$day";
            }

            // 1. Metadata
            $menus = $this->db->select('m.title, m.url, m.icon')->from('user_menu m')->join('user_access_menu a', 'a.menu_id = m.id')->where(['a.role_id' => $role_id, 'm.is_active' => 1])->group_start()->like('m.title', $query)->or_like('m.url', $query)->group_end()->limit(5)->get()->result();
            foreach ($menus as $m) if ($m->url !== '#') $results[] = ['title' => $m->title, 'url' => base_url($m->url), 'category' => 'Navigation', 'icon' => $m->icon ?: 'fas fa-link'];

            $merchants = $this->db->select('id, c_name')->from('merchant')->group_start()->like('c_name', $query)->or_where('id', $query)->group_end()->limit(3)->get()->result();
            foreach ($merchants as $m) $results[] = ['title' => $m->c_name . " (#$m->id)", 'url' => base_url('merchant/manage?search_merchant=' . $m->id), 'category' => 'Merchant', 'icon' => 'fas fa-store'];

            // 2. IDs
            $cashin_ids = [-1]; $cashout_ids = [-1]; $rrn_ids = [-1];
            if (strlen($query) >= 6) {
                $op = (strlen($query) >= 15) ? '=' : 'LIKE';
                $val = (strlen($query) >= 15) ? "'$safeQuery'" : "'$safeQuery%'";

                $res_dyn = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                if ($res_dyn) {
                    $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 5")->result(), 'id');
                    $q_p = $this->db->query("SELECT ref_cashinId, id FROM cashin_payment_qris_mpm WHERE ref_cashinDynamicQrisMpmId IN (".implode(',', $ids).") LIMIT 5")->result();
                    foreach ($q_p as $rp) { $cashin_ids[] = $rp->ref_cashinId; $rrn_ids[] = $rp->id; }
                }
                $res_va = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val OR c_vaNumber $op $val LIMIT 1")->result();
                if ($res_va) {
                    $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val OR c_vaNumber $op $val LIMIT 5")->result(), 'id');
                    $q_p = $this->db->query("SELECT ref_cashinId FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $ids).") LIMIT 5")->result();
                    foreach ($q_p as $rp) $cashin_ids[] = $rp->ref_cashinId;
                }
                $res_ewd = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                if ($res_ewd) {
                    $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 5")->result(), 'id');
                    $q_p = $this->db->query("SELECT ref_cashinId FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (".implode(',', $ids).") LIMIT 5")->result();
                    foreach ($q_p as $rp) $cashin_ids[] = $rp->ref_cashinId;
                }
                $res_qrr = $this->db->query("SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                
                $res_vrr = $this->db->query("SELECT id FROM cashin_recurring_va WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                
                $res_bf = $this->db->query("SELECT ref_cashoutId FROM cashout_payment_bifast WHERE c_merchantTransactionId $op $val OR c_accountNo $op $val LIMIT 5")->result();
                if ($res_bf) foreach ($res_bf as $rbf) $cashout_ids[] = $rbf->ref_cashoutId;

                $res_ppob_id = $this->db->query("SELECT ref_cashoutId FROM cashout_payment_ppob WHERE ref_cashoutChannelId $op $val OR c_phone $op $val LIMIT 5")->result();
                if ($res_ppob_id) foreach ($res_ppob_id as $rpp) $cashout_ids[] = $rpp->ref_cashoutId;

                foreach (['external_paydgn_qris_mpm_callback', 'external_paylabs_qris_mpm_callback_payment', 'external_quantum_qris_mpm_calback_payment'] as $cb_t) {
                    $col = ($cb_t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
                    foreach ($this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $cb_t WHERE $col $op $val LIMIT 5")->result() as $rcb) if ($rcb->ref_cashinPaymentQrisMpmId) $rrn_ids[] = $rcb->ref_cashinPaymentQrisMpmId;
                }
            }

            // 3. Optimized Scan (Priority: Invoice match)
            $found_in_subtables = (count($cashin_ids) > 1 || count($cashout_ids) > 1 || count($rrn_ids) > 1);
            if (!$found_in_subtables && ((preg_match('/[A-Z_]/', $query_upper) && strlen($query) >= 4) || $extracted_date)) {
                // Search Cashin
                $this->db->reset_query(); 
                $this->db->select('id')->from('cashin');
                if ($extracted_date) {
                    $this->db->where('c_datetime >=', "$extracted_date 00:00:00");
                    $this->db->where('c_datetime <=', "$extracted_date 23:59:59");
                    $this->db->like('c_invoiceNo', $query, 'after');
                } else {
                    $this->db->like('c_invoiceNo', $query, 'after');
                }
                foreach ($this->db->limit(15)->get()->result() as $r) $cashin_ids[] = $r->id;

                // Search Cashout
                $this->db->reset_query(); 
                $this->db->select('id')->from('cashout');
                if ($extracted_date) {
                    $this->db->where('c_datetime >=', "$extracted_date 00:00:00");
                    $this->db->where('c_datetime <=', "$extracted_date 23:59:59");
                    $this->db->like('c_invoiceNo', $query, 'after');
                } else {
                    $this->db->like('c_invoiceNo', $query, 'after');
                }
                foreach ($this->db->limit(10)->get()->result() as $r) $cashout_ids[] = $r->id;
            }

            // 4. Results
            if (count($cashin_ids) > 1 || count($rrn_ids) > 1) {
                $this->db->reset_query(); $this->db->select("MAX(q.id) as id, c.c_invoiceNo, MAX(q.c_amount) as c_amount")->from('cashin_payment_qris_mpm q')->join('cashin c', 'c.id = q.ref_cashinId')->group_start()->where_in('q.ref_cashinId', $cashin_ids);
                if (count($rrn_ids) > 1) $this->db->or_where_in('q.id', $rrn_ids);
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/qris?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'QRIS', 'icon' => 'fas fa-qrcode'];
                }
            }
            if (count($cashin_ids) > 1 || strlen($query) >= 10) {
                $this->db->reset_query(); $this->db->select("MAX(v.id) as id, c.c_invoiceNo, MAX(v.c_vaNumber) as c_vaNumber, MAX(v.c_amount) as c_amount")->from('cashin_payment_va v')->join('cashin c', 'c.id = v.ref_cashinId')->group_start()->where_in('v.ref_cashinId', $cashin_ids);
                if (is_numeric($query)) $this->db->or_like('v.c_vaNumber', $query, 'after');
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/virtual-account?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'VA', 'icon' => 'fas fa-university'];
                }
            }
            if (count($cashout_ids) > 1) {
                // BI-FAST
                $this->db->reset_query(); 
                $this->db->select("c.c_invoiceNo, MAX(b.c_amount) as c_amount")
                    ->from('cashout_payment_bifast b')
                    ->join('cashout c', 'c.id = b.ref_cashoutId')
                    ->where_in('b.ref_cashoutId', $cashout_ids)
                    ->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query), 'url' => base_url('finance/bi-fast?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'BI-FAST', 'icon' => 'fas fa-exchange-alt'];
                }

                // PPOB / Purchase
                $this->db->reset_query(); 
                $this->db->select("c.c_invoiceNo, MAX(p.c_amount) as c_amount")
                    ->from('cashout_payment_ppob p')
                    ->join('cashout c', 'c.id = p.ref_cashoutId')
                    ->where_in('p.ref_cashoutId', $cashout_ids)
                    ->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query), 'url' => base_url('finance/history?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'Purchase', 'icon' => 'fas fa-shopping-cart'];
                }
            }
            if (count($cashin_ids) > 1) {
                $this->db->reset_query(); $this->db->select("MAX(e.id) as id, c.c_invoiceNo, MAX(e.c_amount) as c_amount")->from('cashin_payment_ewallet e')->join('cashin c', 'c.id = e.ref_cashinId')->where_in('e.ref_cashinId', $cashin_ids)->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/e-wallet?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'E-Wallet', 'icon' => 'fas fa-wallet'];
                }
            }

            // 5. Dynamic & Recurring Modules
            $safeQuery = $this->db->escape_str($query);
            
            // VA Dynamic
            $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_dynamic_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
            foreach ($res as $r) {
                $results[] = ['title' => $r->c_vaNumber . " - Rp " . number_format($r->c_amount), 'url' => base_url('virtual-account/dynamic?transid=' . $query), 'category' => 'VA Dynamic', 'icon' => 'fas fa-university'];
            }
            
            // VA Recurring
            $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_recurring_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
            foreach ($res as $r) {
                $results[] = ['title' => $r->c_vaNumber . " - Rp " . number_format($r->c_amount), 'url' => base_url('virtual-account/recurring?transid=' . $query), 'category' => 'VA Recurring', 'icon' => 'fas fa-history'];
            }
            
            // QRIS Dynamic
            $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
            foreach ($res as $r) {
                $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('qris/dynamic?transid=' . $query), 'category' => 'QRIS Dynamic', 'icon' => 'fas fa-qrcode'];
            }
            
            // QRIS Recurring
            $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
            foreach ($res as $r) {
                $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('qris/recurring?transid=' . $query), 'category' => 'QRIS Recurring', 'icon' => 'fas fa-redo'];
            }
            
            // E-Wallet Dynamic
            $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
            foreach ($res as $r) {
                $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('e-wallet/dynamic?transid=' . $query), 'category' => 'E-Wallet Dynamic', 'icon' => 'fas fa-wallet'];
            }

            echo json_encode(array_slice($results, 0, 15));
        } catch (Exception $e) { echo json_encode([]); }
    }
}
