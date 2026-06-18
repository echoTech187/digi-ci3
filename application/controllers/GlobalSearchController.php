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
        $this->db->query("SET SESSION max_execution_time = 8000");
        ini_set('memory_limit', '256M');
        try {
            $query = trim($this->input->get('q'));
            if (!$query || strlen($query) < 2) { echo json_encode([]); return; }

            $results      = [];
            $role_id      = $this->session->userdata('role') ?: $this->session->userdata('role_id');
            $safeQ        = $this->db->escape_str($query);
            $qUpper       = strtoupper($query);
            $qLen         = strlen($query);
            $isNumericTid = is_numeric($query) && $qLen >= 10; // true for long numeric transaction IDs

            // ── Extract date embedded in transaction ID patterns ──────────────────────
            $extracted_date = null;
            if (preg_match('/_([0-9]{6})_/', $query, $m)) {
                [$y, $mo, $d] = ["20" . substr($m[1], 0, 2), substr($m[1], 2, 2), substr($m[1], 4, 2)];
                if (checkdate($mo, $d, $y)) $extracted_date = "$y-$mo-$d";
            }
            if (!$extracted_date && preg_match('/(?:GD|TRX|VA|EW|BF)([0-9]{6})/i', $query, $m)) {
                [$y, $mo, $d] = ["20" . substr($m[1], 0, 2), substr($m[1], 2, 2), substr($m[1], 4, 2)];
                if (checkdate($mo, $d, $y)) $extracted_date = "$y-$mo-$d";
            }

            // ── QUERY 1: Menu + ACL (single JOIN, always needed) ─────────────────────
            $all_menus    = $this->db->query(
                "SELECT m.title, m.url, m.icon FROM user_menu m
                 JOIN user_access_menu a ON a.menu_id = m.id
                 WHERE a.role_id = {$role_id} AND m.is_active = 1"
            )->result();
            $allowed_urls = array_column((array) $all_menus, 'url');
            $has_access   = function ($url) use ($allowed_urls) { return in_array($url, $allowed_urls); };

            // Filter menu matches in PHP – zero extra roundtrip
            foreach ($all_menus as $m) {
                if ($m->url !== '#' && (stripos($m->title, $query) !== false || stripos($m->url, $query) !== false)) {
                    $results[] = ['title' => $m->title, 'url' => base_url($m->url), 'category' => 'Navigation', 'icon' => $m->icon ?: 'fas fa-link'];
                    if (count($results) >= 3) break;
                }
            }

            // ── QUERIES 2–3: Merchant + presence (skipped for numeric transaction IDs) ─
            if (!$isNumericTid && $qLen >= 3) {

                // QUERY 2: Merchant search (indexed via idx_merchant_name)
                $merchants = $this->db->query(
                    "SELECT id, c_name, c_merchantLevel, parent_merchant_id FROM merchant
                     WHERE c_name LIKE '$safeQ%' OR id = '$safeQ' OR c_email LIKE '$safeQ%' LIMIT 2"
                )->result();

                if ($merchants) {
                    $m_ids_str = implode(',', array_column($merchants, 'id'));

                    // QUERY 3: ONE UNION ALL → all 7 module-presence checks in 1 roundtrip (was 3 queries)
                    $presence = [];
                    $pres_res = $this->db->query("
                        (SELECT ref_merchantId, CAST(CONCAT('ci_', c_cashinChannelGroup) AS CHAR) AS grp FROM cashin WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId, c_cashinChannelGroup)
                        UNION ALL (SELECT ref_merchantId, CAST(CONCAT('co_', c_cashoutChannelGroup) AS CHAR) FROM cashout WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId, c_cashoutChannelGroup)
                        UNION ALL (SELECT ref_merchantId, CAST('qd' AS CHAR) FROM cashin_dynamic_qris_mpm WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId)
                        UNION ALL (SELECT ref_merchantId, CAST('qr' AS CHAR) FROM cashin_recurring_qris_mpm WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId)
                        UNION ALL (SELECT ref_merchantId, CAST('vd' AS CHAR) FROM cashin_dynamic_va WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId)
                        UNION ALL (SELECT ref_merchantId, CAST('vr' AS CHAR) FROM cashin_recurring_va WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId)
                        UNION ALL (SELECT ref_merchantId, CAST('ed' AS CHAR) FROM cashin_dynamic_ewallet WHERE ref_merchantId IN ($m_ids_str) GROUP BY ref_merchantId)
                    ");
                    if ($pres_res) {
                        foreach ($pres_res->result() as $p) $presence[$p->ref_merchantId][$p->grp] = true;
                    }

                    foreach ($merchants as $m) {
                        $mid = $m->id;
                        if ($m->c_merchantLevel > 0) {
                            if ($has_access('merchant/manage')) $results[] = ['title' => "Sub-Account: $m->c_name (#$mid)", 'url' => base_url('merchant/sub-account/' . $m->parent_merchant_id . '?search_val=' . urlencode($query)), 'category' => 'Sub-Account', 'icon' => 'fas fa-store-alt', 'merchant_id' => $mid];
                        } else {
                            if ($has_access('merchant/manage')) $results[] = ['title' => "Manage $m->c_name (#$mid)", 'url' => base_url('merchant/manage?search_merchant=' . urlencode($query)), 'category' => 'Merchant', 'icon' => 'fas fa-store', 'merchant_id' => $mid];
                        }
                        if ($has_access('finance/qris')              && isset($presence[$mid]['ci_qris_mpm']))     $results[] = ['title' => "QRIS for $m->c_name",            'url' => base_url("finance/qris?merchant=$mid"),              'category' => 'Shortcuts', 'icon' => 'fas fa-qrcode'];
                        if ($has_access('finance/virtual-account')   && isset($presence[$mid]['ci_va']))           $results[] = ['title' => "VA for $m->c_name",              'url' => base_url("finance/virtual-account?merchant=$mid"),   'category' => 'Shortcuts', 'icon' => 'fas fa-university'];
                        if ($has_access('finance/e-wallet')          && isset($presence[$mid]['ci_ewallet']))      $results[] = ['title' => "E-Wallet for $m->c_name",        'url' => base_url("finance/e-wallet?merchant=$mid"),          'category' => 'Shortcuts', 'icon' => 'fas fa-wallet'];
                        if ($has_access('finance/bi-fast')           && isset($presence[$mid]['co_bifast']))       $results[] = ['title' => "BI-FAST for $m->c_name",         'url' => base_url("finance/bi-fast?merchant=$mid"),           'category' => 'Shortcuts', 'icon' => 'fas fa-paper-plane'];
                        if ($has_access('finance/history')           && isset($presence[$mid]['co_PPOB']))         $results[] = ['title' => "PPOB for $m->c_name",            'url' => base_url("finance/history?merchant=$mid"),           'category' => 'Shortcuts', 'icon' => 'fas fa-mobile-alt'];
                        if ($has_access('qris/dynamic')              && isset($presence[$mid]['qd']))              $results[] = ['title' => "QRIS Dynamic for $m->c_name",    'url' => base_url("qris/dynamic?merchant=$mid"),              'category' => 'Shortcuts', 'icon' => 'fas fa-qrcode'];
                        if ($has_access('qris/recurring')            && isset($presence[$mid]['qr']))              $results[] = ['title' => "QRIS Recurring for $m->c_name",  'url' => base_url("qris/recurring?merchant=$mid"),            'category' => 'Shortcuts', 'icon' => 'fas fa-redo'];
                        if ($has_access('virtual-account/dynamic')   && isset($presence[$mid]['vd']))              $results[] = ['title' => "VA Dynamic for $m->c_name",      'url' => base_url("virtual-account/dynamic?merchant=$mid"),   'category' => 'Shortcuts', 'icon' => 'fas fa-university'];
                        if ($has_access('virtual-account/recurring') && isset($presence[$mid]['vr']))              $results[] = ['title' => "VA Recurring for $m->c_name",    'url' => base_url("virtual-account/recurring?merchant=$mid"), 'category' => 'Shortcuts', 'icon' => 'fas fa-history'];
                        if ($has_access('e-wallet/dynamic')          && isset($presence[$mid]['ed']))              $results[] = ['title' => "E-Wallet Dynamic for $m->c_name",'url' => base_url("e-wallet/dynamic?merchant=$mid"),          'category' => 'Shortcuts', 'icon' => 'fas fa-wallet'];
                        if ($has_access('report/balance-log'))   $results[] = ['title' => "Balance Log for $m->c_name", 'url' => base_url("report/balance-log?search_merchant_balance_log=" . urlencode($mid)), 'category' => 'Shortcuts', 'icon' => 'fas fa-book'];
                        if ($has_access('finance/mutation'))     $results[] = ['title' => "Mutation for $m->c_name",    'url' => base_url("finance/mutation/$mid"),                                                 'category' => 'Shortcuts', 'icon' => 'fas fa-exchange-alt'];
                    }
                } // end if $merchants

                // Admin / Supervisor / Channel (text queries only)
                if ($has_access('access-control/accounts')) {
                    foreach ($this->db->query("SELECT id, c_name, c_email FROM admin WHERE c_name LIKE '$safeQ%' OR c_email LIKE '$safeQ%' LIMIT 2")->result() as $a)
                        $results[] = ['title' => "Admin: $a->c_name ($a->c_email)", 'url' => base_url('access-control/accounts?search_admin=' . urlencode($query)), 'category' => 'Admin Accounts', 'icon' => 'fas fa-user-shield'];
                }
                if ($has_access('merchant/supervisor')) {
                    foreach ($this->db->query("SELECT id, c_name, c_email FROM merchant_supervisor WHERE c_name LIKE '$safeQ%' OR c_email LIKE '$safeQ%' LIMIT 2")->result() as $s)
                        $results[] = ['title' => "Supervisor: $s->c_name ($s->c_email)", 'url' => base_url('merchant/supervisor?search_spv=' . urlencode($query)), 'category' => 'Supervisor Management', 'icon' => 'fas fa-user-tie'];
                }
                if ($has_access('channel/cashin')) {
                    foreach ($this->db->query("SELECT id, c_description FROM cashin_channel WHERE c_description LIKE '$safeQ%' OR c_externalIdDefault LIKE '$safeQ%' LIMIT 2")->result() as $cc)
                        $results[] = ['title' => "Cash-In Channel: $cc->id", 'url' => base_url('channel/cashin?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-download'];
                }
                if ($has_access('channel/cashout')) {
                    foreach ($this->db->query("SELECT id, c_description FROM cashout_channel WHERE c_description LIKE '$safeQ%' OR c_externalIdDefault LIKE '$safeQ%' LIMIT 2")->result() as $cc)
                        $results[] = ['title' => "Cash-Out Channel: $cc->id", 'url' => base_url('channel/cashout?search_channel=' . urlencode($query)), 'category' => 'Configuration', 'icon' => 'fas fa-upload'];
                }
            } // end if !$isNumericTid

            // ── QUERY 4: MASTER UNION ALL – ALL transaction lookups in ONE roundtrip ──
            // Returns cols: s=source_key, r=record_id, tid=display_label, amt=amount
            // Replaces 9+ separate queries → single network roundtrip to remote DB.
            $cashin_ids  = [-1];
            $cashout_ids = [-1];
            $rrn_ids     = [-1];
            $qrisDynIds  = [];
            $vaDynIds    = [];
            $ewDynIds    = [];
            $qrisRrnPids = [];

            if ($qLen >= 6) {
                $op  = ($qLen >= 15) ? '='      : 'LIKE';
                $val = ($qLen >= 15) ? "'$safeQ'" : "'$safeQ%'";

                $parts    = [];
                $needQris = $has_access('finance/qris') || $has_access('qris/dynamic') || $has_access('qris/recurring');
                $needVa   = $has_access('finance/virtual-account') || $has_access('virtual-account/dynamic') || $has_access('virtual-account/recurring');
                $needEw   = $has_access('finance/e-wallet') || $has_access('e-wallet/dynamic');

                if ($needQris) {
                    $parts[] = "(SELECT 'qdt' AS s, CAST(id AS CHAR) AS r, CAST(c_merchantTransactionId AS CHAR) AS tid, CAST(c_amount AS CHAR) AS amt FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 5)";
                    if ($has_access('finance/qris')) {
                        // RRN is stored in external callback tables, not in cashin_payment_qris_mpm
                        $rrn_tables = [
                            'external_paydgn_qris_mpm_callback' => 'c_issuerRrn',
                            'external_gvconnect_snap_qris_mpm_callback' => 'c_issuerRrn',
                            'external_inacash_qris_mpm_callback' => 'c_issuerRrn',
                            'external_paylabs_qris_mpm_callback_payment' => 'c_issuerRrn',
                            'external_quantum_qris_mpm_calback_payment' => 'c_transactionId',
                            'external_stm_qris_mpm_callback' => 'c_issuerRrn',
                            'external_yukk_qris_mpm_callback' => 'c_issuerRrn'
                        ];
                        
                        $qris_db_debug = $this->db->db_debug;
                        $this->db->db_debug = FALSE; // Prevent query crash if a callback table is missing
                        
                        $found_rrn_ids = [];
                        foreach ($rrn_tables as $t => $col) {
                            $q = $this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $t WHERE $col $op $val LIMIT 5");
                            if ($q) {
                                foreach ($q->result() as $r) {
                                    if ($r->ref_cashinPaymentQrisMpmId) $found_rrn_ids[] = $r->ref_cashinPaymentQrisMpmId;
                                }
                            }
                        }
                        $this->db->db_debug = $qris_db_debug;
                        
                        if ($found_rrn_ids) {
                            $id_str = implode(',', array_unique($found_rrn_ids));
                            $parts[] = "(SELECT 'qrn', CAST(id AS CHAR), CAST('' AS CHAR), '0' FROM cashin_payment_qris_mpm WHERE id IN ($id_str) LIMIT 5)";
                        }
                    }
                    if ($has_access('qris/recurring'))
                        $parts[] = "(SELECT 'qrc', CAST(id AS CHAR), CAST(c_merchantTransactionId AS CHAR), CAST(c_amount AS CHAR) FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 3)";
                }
                if ($needVa) {
                    $parts[] = "(SELECT 'vdt', CAST(id AS CHAR), CAST(c_merchantTransactionId AS CHAR), CAST(c_amount AS CHAR) FROM cashin_dynamic_va WHERE c_merchantTransactionId $op $val LIMIT 5)";
                    $parts[] = "(SELECT 'vdn', CAST(id AS CHAR), CAST(c_vaNumber AS CHAR),              CAST(c_amount AS CHAR) FROM cashin_dynamic_va WHERE c_vaNumber $op $val LIMIT 5)";
                    if ($has_access('virtual-account/recurring')) {
                        $parts[] = "(SELECT 'vrt', CAST(id AS CHAR), CAST(c_merchantTransactionId AS CHAR), CAST(c_amount AS CHAR) FROM cashin_recurring_va WHERE c_merchantTransactionId $op $val LIMIT 3)";
                        $parts[] = "(SELECT 'vrn', CAST(id AS CHAR), CAST(c_vaNumber AS CHAR),              CAST(c_amount AS CHAR) FROM cashin_recurring_va WHERE c_vaNumber $op $val LIMIT 3)";
                    }
                }
                if ($needEw) {
                    $parts[] = "(SELECT 'ewt', CAST(id AS CHAR), CAST(c_merchantTransactionId AS CHAR), CAST(c_amount AS CHAR) FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId $op $val LIMIT 5)";
                }
                if ($has_access('finance/bi-fast')) {
                    $parts[] = "(SELECT 'bft', CAST(ref_cashoutId AS CHAR), CAST(c_merchantTransactionId AS CHAR), '0' FROM cashout_payment_bifast WHERE c_merchantTransactionId $op $val LIMIT 5)";
                    if (is_numeric($query) && $qLen <= 16)
                        $parts[] = "(SELECT 'bfa', CAST(ref_cashoutId AS CHAR), CAST(c_accountNo AS CHAR), '0' FROM cashout_payment_bifast WHERE c_accountNo $op $val LIMIT 5)";
                }
                if ($has_access('finance/history')) {
                    $parts[] = "(SELECT 'ppb', CAST(ref_cashoutId AS CHAR), CAST(ref_cashoutChannelId AS CHAR), '0' FROM cashout_payment_ppob WHERE ref_cashoutChannelId $op $val LIMIT 5)";
                    $parts[] = "(SELECT 'pph', CAST(ref_cashoutId AS CHAR), CAST(c_phone AS CHAR), '0' FROM cashout_payment_ppob WHERE c_phone $op $val LIMIT 5)";
                }

                if ($parts) {
                    $seen = [];
                    $u_res = $this->db->query(implode(" UNION ALL ", $parts));
                    if ($u_res) {
                        foreach ($u_res->result() as $row) {
                            $rid = (int) $row->r;
                            if (!$rid) continue;
                            $src = $row->s;
                            if (isset($seen[$src][$rid])) continue;
                            $seen[$src][$rid] = true;
                            $tid = $row->tid;
                            $amt = (float) $row->amt;

                            switch ($src) {
                                case 'qdt':
                                    $qrisDynIds[] = $rid;
                                    if ($has_access('qris/dynamic') && $tid && !isset($seen['disp_qd'][$rid])) {
                                        $seen['disp_qd'][$rid] = true;
                                        $results[] = ['title' => "$tid - Rp " . number_format($amt), 'url' => base_url('qris/dynamic?transid=' . $query), 'category' => 'QRIS Dynamic', 'icon' => 'fas fa-qrcode'];
                                    }
                                    break;
                                case 'qrn':
                                    $qrisRrnPids[] = $rid;
                                    break;
                                case 'qrc':
                                    if ($has_access('qris/recurring') && $tid)
                                        $results[] = ['title' => "$tid - Rp " . number_format($amt), 'url' => base_url('qris/recurring?transid=' . $query), 'category' => 'QRIS Recurring', 'icon' => 'fas fa-redo'];
                                    break;
                                case 'vdt':
                                case 'vdn':
                                    $vaDynIds[] = $rid;
                                    if ($has_access('virtual-account/dynamic') && $tid && !isset($seen['disp_vd'][$rid])) {
                                        $seen['disp_vd'][$rid] = true;
                                        $qs = ($src === 'vdn') ? '?va_number=' : '?transid=';
                                        $results[] = ['title' => "$tid - Rp " . number_format($amt), 'url' => base_url('virtual-account/dynamic' . $qs . $query), 'category' => 'VA Dynamic', 'icon' => 'fas fa-university'];
                                    }
                                    break;
                                case 'vrt':
                                case 'vrn':
                                    if ($has_access('virtual-account/recurring') && $tid && !isset($seen['disp_vr'][$rid])) {
                                        $seen['disp_vr'][$rid] = true;
                                        $qs = ($src === 'vrn') ? '?va_number=' : '?transid=';
                                        $results[] = ['title' => "$tid - Rp " . number_format($amt), 'url' => base_url('virtual-account/recurring' . $qs . $query), 'category' => 'VA Recurring', 'icon' => 'fas fa-history'];
                                    }
                                    break;
                                case 'ewt':
                                    $ewDynIds[] = $rid;
                                    if ($has_access('e-wallet/dynamic') && $tid && !isset($seen['disp_ew'][$rid])) {
                                        $seen['disp_ew'][$rid] = true;
                                        $results[] = ['title' => "$tid - Rp " . number_format($amt), 'url' => base_url('e-wallet/dynamic?transid=' . $query), 'category' => 'E-Wallet Dynamic', 'icon' => 'fas fa-wallet'];
                                    }
                                    break;
                                case 'bft':
                                case 'bfa':
                                    $cashout_ids[] = $rid;
                                    break;
                                case 'ppb':
                                case 'pph':
                                    $cashout_ids[] = $rid;
                                    break;
                            }
                        } // end foreach MASTER UNION ALL
                    } // end if $u_res
                } // end if $parts

                // ── QUERY 5: Resolve dynamic IDs → cashin refs (1 UNION ALL) ──────────
                // Replaces 4 separate ref-resolution roundtrips with a single one.
                $qrisDynIds  = array_unique($qrisDynIds);
                $vaDynIds    = array_unique($vaDynIds);
                $ewDynIds    = array_unique($ewDynIds);
                $qrisRrnPids = array_unique($qrisRrnPids);

                $refParts = [];
                if ($qrisDynIds)
                    $refParts[] = "(SELECT 'qp' AS s, ref_cashinId AS cid, CAST(id AS CHAR) AS pid FROM cashin_payment_qris_mpm WHERE ref_cashinDynamicQrisMpmId IN (" . implode(',', $qrisDynIds) . ") LIMIT 5)";
                if ($qrisRrnPids)
                    $refParts[] = "(SELECT 'qr' AS s, ref_cashinId AS cid, CAST(id AS CHAR) AS pid FROM cashin_payment_qris_mpm WHERE id IN (" . implode(',', $qrisRrnPids) . ") LIMIT 5)";
                if ($vaDynIds)
                    $refParts[] = "(SELECT 'vp' AS s, ref_cashinId AS cid, CAST('0' AS CHAR) AS pid FROM cashin_payment_va WHERE ref_cashinDynamicVaId IN (" . implode(',', $vaDynIds) . ") LIMIT 5)";
                if ($ewDynIds)
                    $refParts[] = "(SELECT 'ep' AS s, ref_cashinId AS cid, CAST('0' AS CHAR) AS pid FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (" . implode(',', $ewDynIds) . ") LIMIT 5)";

                if ($refParts) {
                    $ref_res = $this->db->query(implode(" UNION ALL ", $refParts));
                    if ($ref_res) {
                        foreach ($ref_res->result() as $r) {
                            $cashin_ids[] = $r->cid;
                            if ($r->s === 'qp' || $r->s === 'qr') $rrn_ids[] = (int) $r->pid;
                        }
                    }
                }
            } // end if $qLen >= 6

            // ── QUERY 6 (conditional): Invoice scan – only if nothing found yet ───────
            $foundAny = count($cashin_ids) > 1 || count($cashout_ids) > 1 || count($rrn_ids) > 1;
            if (!$foundAny && ((preg_match('/[A-Z_]/', $qUpper) && $qLen >= 4) || $extracted_date)) {
                if ($has_access('finance/qris') || $has_access('finance/virtual-account') || $has_access('finance/e-wallet')) {
                    $this->db->reset_query()->select('id')->from('cashin');
                    if ($extracted_date) $this->db->where('c_datetime >=', "$extracted_date 00:00:00")->where('c_datetime <=', "$extracted_date 23:59:59");
                    $this->db->like('c_invoiceNo', $query, 'after');
                    foreach ($this->db->limit(15)->get()->result() as $r) $cashin_ids[] = $r->id;
                }
                if ($has_access('finance/bi-fast') || $has_access('finance/history')) {
                    $this->db->reset_query()->select('id')->from('cashout');
                    if ($extracted_date) $this->db->where('c_datetime >=', "$extracted_date 00:00:00")->where('c_datetime <=', "$extracted_date 23:59:59");
                    $this->db->like('c_invoiceNo', $query, 'after');
                    foreach ($this->db->limit(10)->get()->result() as $r) $cashout_ids[] = $r->id;
                }
            }

            // ── QUERIES 7–10: Finance page display (JOIN only when IDs found) ─────────
            if ((count($cashin_ids) > 1 || count($rrn_ids) > 1) && $has_access('finance/qris')) {
                $this->db->reset_query();
                $this->db->select("MAX(q.id) as id, c.c_invoiceNo, MAX(q.c_amount) as c_amount")
                    ->from('cashin_payment_qris_mpm q')
                    ->join('cashin c', 'c.id = q.ref_cashinId')
                    ->group_start()->where_in('q.ref_cashinId', $cashin_ids);
                if (count($rrn_ids) > 1) $this->db->or_where_in('q.id', $rrn_ids);
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInv = stripos($r->c_invoiceNo, $query) !== false;
                    $results[] = ['title' => ($isInv ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/qris?' . ($isInv ? 'invoice=' : 'transid=') . ($isInv ? $r->c_invoiceNo : $query)), 'category' => 'QRIS', 'icon' => 'fas fa-qrcode'];
                }
            }
            if (count($cashin_ids) > 1 && $has_access('finance/virtual-account')) {
                $this->db->reset_query();
                $this->db->select("MAX(v.id) as id, c.c_invoiceNo, MAX(v.c_vaNumber) as c_vaNumber, MAX(v.c_amount) as c_amount")
                    ->from('cashin_payment_va v')
                    ->join('cashin c', 'c.id = v.ref_cashinId')
                    ->group_start()->where_in('v.ref_cashinId', $cashin_ids);
                if (is_numeric($query)) $this->db->or_like('v.c_vaNumber', $query, 'after');
                $this->db->group_end()->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInv = stripos($r->c_invoiceNo, $query) !== false;
                    $isVa  = stripos((string)$r->c_vaNumber, $query) !== false;
                    $qsKey = $isInv ? 'invoice' : ($isVa ? 'va_number' : 'transid');
                    $qsVal = $isInv ? $r->c_invoiceNo : ($isVa ? $r->c_vaNumber : $query);
                    $results[] = ['title' => "$qsVal - Rp " . number_format($r->c_amount), 'url' => base_url("finance/virtual-account?$qsKey=" . urlencode($qsVal)), 'category' => 'VA', 'icon' => 'fas fa-university'];
                }
            }
            if (count($cashin_ids) > 1 && $has_access('finance/e-wallet')) {
                $this->db->reset_query();
                $this->db->select("MAX(e.id) as id, c.c_invoiceNo, MAX(e.c_amount) as c_amount")
                    ->from('cashin_payment_ewallet e')
                    ->join('cashin c', 'c.id = e.ref_cashinId')
                    ->where_in('e.ref_cashinId', $cashin_ids)
                    ->group_by('c.id');
                foreach ($this->db->limit(3)->get()->result() as $r) {
                    $isInv = stripos($r->c_invoiceNo, $query) !== false;
                    $results[] = ['title' => ($isInv ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('finance/e-wallet?' . ($isInv ? 'invoice=' : 'transid=') . ($isInv ? $r->c_invoiceNo : $query)), 'category' => 'E-Wallet', 'icon' => 'fas fa-wallet'];
                }
            }
            if (count($cashout_ids) > 1) {
                if ($has_access('finance/bi-fast')) {
                    $this->db->reset_query()->select("c.c_invoiceNo, MAX(b.c_amount) as c_amount")
                        ->from('cashout_payment_bifast b')
                        ->join('cashout c', 'c.id = b.ref_cashoutId')
                        ->where_in('b.ref_cashoutId', $cashout_ids)
                        ->group_by('c.id');
                    foreach ($this->db->limit(3)->get()->result() as $r) {
                        $isInv = stripos($r->c_invoiceNo, $query) !== false;
                        $results[] = ['title' => ($isInv ? $r->c_invoiceNo : $query), 'url' => base_url('finance/bi-fast?' . ($isInv ? 'invoice=' : 'transid=') . ($isInv ? $r->c_invoiceNo : $query)), 'category' => 'BI-FAST', 'icon' => 'fas fa-exchange-alt'];
                    }
                }
                if ($has_access('finance/history')) {
                    $this->db->reset_query()->select("c.c_invoiceNo, MAX(p.c_amount) as c_amount")
                        ->from('cashout_payment_ppob p')
                        ->join('cashout c', 'c.id = p.ref_cashoutId')
                        ->where_in('p.ref_cashoutId', $cashout_ids)
                        ->group_by('c.id');
                    foreach ($this->db->limit(3)->get()->result() as $r) {
                        $isInv = stripos($r->c_invoiceNo, $query) !== false;
                        $results[] = ['title' => ($isInv ? $r->c_invoiceNo : $query), 'url' => base_url('finance/history?' . ($isInv ? 'invoice=' : 'transid=') . ($isInv ? $r->c_invoiceNo : $query)), 'category' => 'Purchase', 'icon' => 'fas fa-shopping-cart'];
                    }
                }
            }

            echo json_encode(array_slice($results, 0, 15));

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Exception : ' . $e->getMessage()]);
        }
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
            $sql = "
                (SELECT 'Cash-In' as type, 'QRIS' as channel, p.c_amount as amount, 'Success' as status, p.c_datetime as date, c.c_invoiceNo as transid
                 FROM cashin_payment_qris_mpm p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-In', 'Virtual Account', p.c_amount, 'Success', p.c_datetime, c.c_invoiceNo
                 FROM cashin_payment_va p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-In', 'E-Wallet', p.c_amount, 'Success', p.c_datetime, c.c_invoiceNo
                 FROM cashin_payment_ewallet p JOIN cashin c ON c.id = p.ref_cashinId WHERE p.ref_merchantId = ?)
                UNION ALL
                (SELECT 'Cash-Out', 'BI-FAST', p.c_amount, p.c_status, p.c_datetime, c.c_invoiceNo
                 FROM cashout_payment_bifast p JOIN cashout c ON c.id = p.ref_cashoutId WHERE p.ref_merchantId = ?)
                ORDER BY date DESC LIMIT 5
            ";

            $transactions = $this->db->query($sql, [$merchant_id, $merchant_id, $merchant_id, $merchant_id])->result();

            foreach ($transactions as &$t) {
                $t->amount_formatted = 'Rp ' . number_format($t->amount, 0, ',', '.');
                $t->date_formatted   = date('d M Y H:i:s', strtotime($t->date));
            }

            echo json_encode(['status' => 'success', 'data' => $transactions]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
