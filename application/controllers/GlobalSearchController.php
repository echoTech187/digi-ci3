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
            $query = trim($this->input->get('q'));
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

            // Fetch all allowed URLs for this user role
            $all_allowed_menus = $this->db->select('m.url')->from('user_menu m')
                ->join('user_access_menu a', 'a.menu_id = m.id')
                ->where(['a.role_id' => $role_id, 'm.is_active' => 1])
                ->get()->result_array();
            $allowed_urls = array_column($all_allowed_menus, 'url');

            // Helper to check access
            $has_access = function($url) use ($allowed_urls) {
                return in_array($url, $allowed_urls);
            };

            // 1. Merchant Management & Shortcuts
            $merchants = $this->db->select('m.id, m.c_name, m.c_merchantLevel, m.parent_merchant_id')
                ->from('merchant m')
                ->join('submerchant s', 's.ref_merchantId = m.id', 'left')
                ->group_start()
                ->like('m.c_name', $query)
                ->or_where('m.id', $query)
                ->or_like('m.c_email', $query)
                ->or_like('s.c_gvconnectBusinessId', $query)
                ->group_end()
                ->group_by('m.id')
                ->limit(2)->get()->result();

            foreach ($merchants as $m) {
                if ($m->c_merchantLevel > 0) {
                    if ($has_access('merchant/manage')) {
                        $results[] = ['title' => "Sub-Account: " . $m->c_name . " (#$m->id)", 'url' => base_url('merchant/sub-account/' . $m->parent_merchant_id . '?search_val=' . urlencode($query)), 'category' => 'Sub-Account', 'icon' => 'fas fa-store-alt', 'merchant_id' => $m->id];
                    }
                } else {
                    if ($has_access('merchant/manage')) {
                        $results[] = ['title' => "Manage " . $m->c_name . " (#$m->id)", 'url' => base_url('merchant/manage?search_merchant=' . urlencode($query)), 'category' => 'Merchant', 'icon' => 'fas fa-store', 'merchant_id' => $m->id];
                    }
                }
                if ($has_access('finance/qris')) {
                    if ($this->db->query("SELECT 1 FROM cashin WHERE ref_merchantId = ? AND c_cashinChannelGroup = 'qris_mpm' LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "QRIS for " . $m->c_name, 'url' => base_url('finance/qris?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-qrcode'];
                    }
                }
                if ($has_access('finance/virtual-account')) {
                    if ($this->db->query("SELECT 1 FROM cashin WHERE ref_merchantId = ? AND c_cashinChannelGroup = 'va' LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "VA for " . $m->c_name, 'url' => base_url('finance/virtual-account?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-university'];
                    }
                }
                if ($has_access('finance/e-wallet')) {
                    if ($this->db->query("SELECT 1 FROM cashin WHERE ref_merchantId = ? AND c_cashinChannelGroup = 'ewallet' LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "E-Wallet for " . $m->c_name, 'url' => base_url('finance/e-wallet?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-wallet'];
                    }
                }
                if ($has_access('finance/bi-fast')) {
                    if ($this->db->query("SELECT 1 FROM cashout WHERE ref_merchantId = ? AND c_cashoutChannelGroup = 'bifast' LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "BI-FAST for " . $m->c_name, 'url' => base_url('finance/bi-fast?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-paper-plane'];
                    }
                }
                if ($has_access('finance/history')) {
                    if ($this->db->query("SELECT 1 FROM cashout WHERE ref_merchantId = ? AND c_cashoutChannelGroup = 'PPOB' LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "PPOB for " . $m->c_name, 'url' => base_url('finance/history?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-mobile-alt'];
                    }
                }
                if ($has_access('external/cashin')) {
                    if ($this->db->query("SELECT 1 FROM cashin_channel_x_merchant WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "Ext Cashin for " . $m->c_name, 'url' => base_url('external/cashin?search_channel=' . urlencode($m->c_name)), 'category' => 'Configuration', 'icon' => 'fas fa-download'];
                    }
                }
                if ($has_access('external/cashout')) {
                    if ($this->db->query("SELECT 1 FROM cashout_channel_x_merchant WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "Ext Cashout for " . $m->c_name, 'url' => base_url('external/cashout?search_channel=' . urlencode($m->c_name)), 'category' => 'Configuration', 'icon' => 'fas fa-upload'];
                    }
                }
                if ($has_access('qris/dynamic')) {
                    if ($this->db->query("SELECT 1 FROM cashin_dynamic_qris_mpm WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "QRIS Dynamic for " . $m->c_name, 'url' => base_url('qris/dynamic?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-qrcode'];
                    }
                }
                if ($has_access('qris/recurring')) {
                    if ($this->db->query("SELECT 1 FROM cashin_recurring_qris_mpm WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "QRIS Recurring for " . $m->c_name, 'url' => base_url('qris/recurring?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-redo'];
                    }
                }
                if ($has_access('virtual-account/dynamic')) {
                    if ($this->db->query("SELECT 1 FROM cashin_dynamic_va WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "VA Dynamic for " . $m->c_name, 'url' => base_url('virtual-account/dynamic?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-university'];
                    }
                }
                if ($has_access('virtual-account/recurring')) {
                    if ($this->db->query("SELECT 1 FROM cashin_recurring_va WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "VA Recurring for " . $m->c_name, 'url' => base_url('virtual-account/recurring?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-history'];
                    }
                }
                if ($has_access('e-wallet/dynamic')) {
                    if ($this->db->query("SELECT 1 FROM cashin_dynamic_ewallet WHERE ref_merchantId = ? LIMIT 1", [$m->id])->row()) {
                        $results[] = ['title' => "E-Wallet Dynamic for " . $m->c_name, 'url' => base_url('e-wallet/dynamic?merchant=' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-wallet'];
                    }
                }
                if ($has_access('report/balance-log')) {
                    $results[] = ['title' => "Balance Log for " . $m->c_name, 'url' => base_url('report/balance-log?search_merchant_balance_log=' . urlencode($m->id)), 'category' => 'Shortcuts', 'icon' => 'fas fa-book'];
                }
                if ($has_access('finance/mutation')) {
                    $results[] = ['title' => "Mutation for " . $m->c_name, 'url' => base_url('finance/mutation/' . $m->id), 'category' => 'Shortcuts', 'icon' => 'fas fa-exchange-alt'];
                }
            }

            // 1b. Admin Account Search
            if ($has_access('access-control/accounts')) {
                $admins = $this->db->select('id, c_name, c_email')->from('admin')
                    ->group_start()->like('c_name', $query)->or_like('c_email', $query)->group_end()
                    ->limit(2)->get()->result();
                foreach ($admins as $a) {
                    $results[] = ['title' => "Admin: " . $a->c_name . " (" . $a->c_email . ")", 'url' => base_url('access-control/accounts?search_admin=' . urlencode($query)), 'category' => 'Admin Accounts', 'icon' => 'fas fa-user-shield'];
                }
            }

            // 1c. Supervisor Management Search
            if ($has_access('merchant/supervisor')) {
                $supervisors = $this->db->select('id, c_name, c_email')->from('merchant_supervisor')
                    ->group_start()->like('c_name', $query)->or_like('c_email', $query)->group_end()
                    ->limit(2)->get()->result();
                foreach ($supervisors as $s) {
                    $results[] = ['title' => "Supervisor: " . $s->c_name . " (" . $s->c_email . ")", 'url' => base_url('merchant/supervisor?search_spv=' . urlencode($query)), 'category' => 'Supervisor Management', 'icon' => 'fas fa-user-tie'];
                }
            }

            // 1d. Cash-In Channels Search
            if ($has_access('channel/cashin')) {
                $cashin_channels = $this->db->select('id, c_description')->from('cashin_channel')
                    ->group_start()->like('id', $query)->or_like('c_description', $query)->or_like('c_channelGroup', $query)->or_like('c_externalIdDefault', $query)->group_end()
                    ->limit(2)->get()->result();
                foreach ($cashin_channels as $cc) {
                    $results[] = ['title' => "Cash-In Channel: " . $cc->id, 'url' => base_url('channel/cashin?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-download'];
                }
            }

            // 1e. Cash-Out Channels Search
            if ($has_access('channel/cashout')) {
                $cashout_channels = $this->db->select('id, c_description')->from('cashout_channel')
                    ->group_start()->like('id', $query)->or_like('c_description', $query)->or_like('c_channelGroup', $query)->or_like('c_externalIdDefault', $query)->group_end()
                    ->limit(2)->get()->result();
                foreach ($cashout_channels as $cc) {
                    $results[] = ['title' => "Cash-Out Channel: " . $cc->id, 'url' => base_url('channel/cashout?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-upload'];
                }
            }

            // 1f. External Cash-In Channels Search
            if ($has_access('external/cashin')) {
                $this->db->select('cxm.id, cxm.ref_cashinChannelId, m.c_name');
                $this->db->from('cashin_channel_x_merchant cxm');
                $this->db->join('merchant m', 'm.id = cxm.ref_merchantId');
                $this->db->group_start()->like('cxm.c_cashinChannelGroup', $query)->or_like('cxm.ref_cashinChannelId', $query)->or_like('cxm.c_externalIdDefault', $query)->group_end();
                $cashin_ext = $this->db->limit(2)->get()->result();
                foreach ($cashin_ext as $ce) {
                    $results[] = ['title' => "Cash-In Ext: " . $ce->ref_cashinChannelId . " (" . $ce->c_name . ")", 'url' => base_url('external/cashin?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-plug'];
                }
            }

            // 1g. External Cash-Out Channels Search
            if ($has_access('external/cashout')) {
                $this->db->select('cxm.id, cxm.ref_cashoutChannelId, m.c_name');
                $this->db->from('cashout_channel_x_merchant cxm');
                $this->db->join('merchant m', 'm.id = cxm.ref_merchantId');
                $this->db->group_start()->like('cxm.c_cashoutChannelGroup', $query)->or_like('cxm.ref_cashoutChannelId', $query)->or_like('cxm.c_externalIdDefault', $query)->group_end();
                $cashout_ext = $this->db->limit(2)->get()->result();
                foreach ($cashout_ext as $ce) {
                    $results[] = ['title' => "Cash-Out Ext: " . $ce->ref_cashoutChannelId . " (" . $ce->c_name . ")", 'url' => base_url('external/cashout?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-plug'];
                }
            }

            // 2. IDs
            $cashin_ids = [-1]; $cashout_ids = [-1]; $rrn_ids = [-1];
            if (strlen($query) >= 6) {
                $op = (strlen($query) >= 15) ? '=' : 'LIKE';
                $val = (strlen($query) >= 15) ? "'$safeQuery'" : "'$safeQuery%'";

                if ($has_access('finance/qris')) {
                    $res_dyn = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                    if ($res_dyn) {
                        $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 5")->result(), 'id');
                        $q_p = $this->db->query("SELECT ref_cashinId, id FROM cashin_payment_qris_mpm WHERE ref_cashinDynamicQrisMpmId IN (".implode(',', $ids).") LIMIT 5")->result();
                        foreach ($q_p as $rp) { $cashin_ids[] = $rp->ref_cashinId; $rrn_ids[] = $rp->id; }
                    }
                    foreach (['external_paydgn_qris_mpm_callback', 'external_paylabs_qris_mpm_callback_payment', 'external_quantum_qris_mpm_calback_payment'] as $cb_t) {
                        $col = ($cb_t == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
                        foreach ($this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $cb_t WHERE $col $op $val LIMIT 5")->result() as $rcb) if ($rcb->ref_cashinPaymentQrisMpmId) $rrn_ids[] = $rcb->ref_cashinPaymentQrisMpmId;
                    }
                }

                if ($has_access('finance/virtual-account')) {
                    $res_va = $this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val OR c_vaNumber $op $val LIMIT 1")->result();
                    if ($res_va) {
                        $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val OR c_vaNumber $op $val LIMIT 5")->result(), 'id');
                        $q_p = $this->db->query("SELECT ref_cashinId FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (".implode(',', $ids).") LIMIT 5")->result();
                        foreach ($q_p as $rp) $cashin_ids[] = $rp->ref_cashinId;
                    }
                }

                if ($has_access('finance/e-wallet')) {
                    $res_ewd = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
                    if ($res_ewd) {
                        $ids = array_column($this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 5")->result(), 'id');
                        $q_p = $this->db->query("SELECT ref_cashinId FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (".implode(',', $ids).") LIMIT 5")->result();
                        foreach ($q_p as $rp) $cashin_ids[] = $rp->ref_cashinId;
                    }
                }

                if ($has_access('finance/bi-fast')) {
                    $res_bf = $this->db->query("SELECT ref_cashoutId FROM cashout_payment_bifast WHERE c_merchantTransactionId $op $val OR c_accountNo $op $val LIMIT 5")->result();
                    if ($res_bf) foreach ($res_bf as $rbf) $cashout_ids[] = $rbf->ref_cashoutId;
                }

                if ($has_access('finance/history')) {
                    $res_ppob_id = $this->db->query("SELECT ref_cashoutId FROM cashout_payment_ppob WHERE ref_cashoutChannelId $op $val OR c_phone $op $val LIMIT 5")->result();
                    if ($res_ppob_id) foreach ($res_ppob_id as $rpp) $cashout_ids[] = $rpp->ref_cashoutId;
                }
            }

            // 3. Optimized Scan (Priority: Invoice match)
            $found_in_subtables = (count($cashin_ids) > 1 || count($cashout_ids) > 1 || count($rrn_ids) > 1);
            if (!$found_in_subtables && ((preg_match('/[A-Z_]/', $query_upper) && strlen($query) >= 4) || $extracted_date)) {
                // Search Cashin
                if ($has_access('finance/qris') || $has_access('finance/virtual-account') || $has_access('finance/e-wallet')) {
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
                }

                // Search Cashout
                if ($has_access('finance/bi-fast') || $has_access('finance/history')) {
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
            }

            // 4. Results
            if ((count($cashin_ids) > 1 || count($rrn_ids) > 1) && $has_access('finance/qris')) {
                $this->db->reset_query(); $this->db->select("MAX(q.id) as id, c.c_invoiceNo, MAX(q.c_amount) as c_amount")->from('cashin_payment_qris_mpm q')->join('cashin c', 'c.id = q.ref_cashinId')->group_start()->where_in('q.ref_cashinId', $cashin_ids);
                if (count($rrn_ids) > 1) $this->db->or_where_in('q.id', $rrn_ids);
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/qris?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'QRIS', 'icon' => 'fas fa-qrcode'];
                }
            }
            if ((count($cashin_ids) > 1 || strlen($query) >= 10) && $has_access('finance/virtual-account')) {
                $this->db->reset_query(); $this->db->select("MAX(v.id) as id, c.c_invoiceNo, MAX(v.c_vaNumber) as c_vaNumber, MAX(v.c_amount) as c_amount")->from('cashin_payment_va v')->join('cashin c', 'c.id = v.ref_cashinId')->group_start()->where_in('v.ref_cashinId', $cashin_ids);
                if (is_numeric($query)) $this->db->or_like('v.c_vaNumber', $query, 'after');
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/virtual-account?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'VA', 'icon' => 'fas fa-university'];
                }
            }
            if (count($cashout_ids) > 1) {
                if ($has_access('finance/bi-fast')) {
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
                }

                if ($has_access('finance/history')) {
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
            }
            if (count($cashin_ids) > 1 && $has_access('finance/e-wallet')) {
                $this->db->reset_query(); $this->db->select("MAX(e.id) as id, c.c_invoiceNo, MAX(e.c_amount) as c_amount")->from('cashin_payment_ewallet e')->join('cashin c', 'c.id = e.ref_cashinId')->where_in('e.ref_cashinId', $cashin_ids)->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                    $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/e-wallet?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'E-Wallet', 'icon' => 'fas fa-wallet'];
                }
            }

            // 5. Dynamic & Recurring Modules
            $safeQuery = $this->db->escape_str($query);
            
            if ($has_access('virtual-account/dynamic')) {
                $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_dynamic_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
                foreach ($res as $r) {
                    $title_prefix = !empty($r->c_vaNumber) ? $r->c_vaNumber : $r->c_merchantTransactionId;
                    $results[] = ['title' => $title_prefix . " - Rp " . number_format($r->c_amount), 'url' => base_url('virtual-account/dynamic?transid=' . $query), 'category' => 'VA Dynamic', 'icon' => 'fas fa-university'];
                }
            }
            
            if ($has_access('virtual-account/recurring')) {
                $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_recurring_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
                foreach ($res as $r) {
                    $title_prefix = !empty($r->c_vaNumber) ? $r->c_vaNumber : $r->c_merchantTransactionId;
                    $results[] = ['title' => $title_prefix . " - Rp " . number_format($r->c_amount), 'url' => base_url('virtual-account/recurring?transid=' . $query), 'category' => 'VA Recurring', 'icon' => 'fas fa-history'];
                }
            }
            
            if ($has_access('qris/dynamic')) {
                $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
                foreach ($res as $r) {
                    $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('qris/dynamic?transid=' . $query), 'category' => 'QRIS Dynamic', 'icon' => 'fas fa-qrcode'];
                }
            }
            
            if ($has_access('qris/recurring')) {
                $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
                foreach ($res as $r) {
                    $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('qris/recurring?transid=' . $query), 'category' => 'QRIS Recurring', 'icon' => 'fas fa-redo'];
                }
            }
            
            if ($has_access('e-wallet/dynamic')) {
                $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
                foreach ($res as $r) {
                    $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('e-wallet/dynamic?transid=' . $query), 'category' => 'E-Wallet Dynamic', 'icon' => 'fas fa-wallet'];
                }
            }

            echo json_encode(array_slice($results, 0, 15));
        } catch (Exception $e) { echo json_encode([]); }
    }
    public function recentTransactionsAjax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $merchant_id = $this->input->get('merchant_id') ?: $this->input->post('merchant_id');

        if (!$merchant_id) {
            echo json_encode(['status' => 'error', 'message' => 'Merchant ID is required']);
            return;
        }

        try {
            // Use UNION ALL across the payment tables and join with parent tables to get the invoice number.
            $query = "
                (SELECT 'Cash-In' as type, 'QRIS' as channel, p.c_amount as amount, 'Success' as status, p.c_datetime as date, c.c_invoiceNo as transid 
                FROM cashin_payment_qris_mpm p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-In' as type, 'Virtual Account' as channel, p.c_amount as amount, 'Success' as status, p.c_datetime as date, c.c_invoiceNo as transid 
                FROM cashin_payment_va p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-In' as type, 'E-Wallet' as channel, p.c_amount as amount, 'Success' as status, p.c_datetime as date, c.c_invoiceNo as transid 
                FROM cashin_payment_ewallet p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-Out' as type, 'BI-FAST' as channel, p.c_amount as amount, p.c_status as status, p.c_datetime as date, c.c_invoiceNo as transid 
                FROM cashout_payment_bifast p JOIN cashout c ON c.id = p.ref_cashoutId WHERE p.ref_merchantId = ?)
                ORDER BY date DESC LIMIT 5
            ";

            $transactions = $this->db->query($query, [$merchant_id, $merchant_id, $merchant_id, $merchant_id])->result();

            // Format dates and amounts for the frontend
            foreach ($transactions as &$t) {
                $t->amount_formatted = 'Rp ' . number_format($t->amount, 0, ',', '.');
                $t->date_formatted = date('d M Y H:i:s', strtotime($t->date));
            }

            echo json_encode([
                'status' => 'success',
                'data' => $transactions
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
