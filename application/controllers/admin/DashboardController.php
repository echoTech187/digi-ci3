<?php defined('BASEPATH') or exit('No direct script access allowed');

class DashboardController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->model('Model_user');
      $this->load->model('Model_menu');
      $this->load->model('Mutation_model');

      is_logged_in();
   }

   public function index()
   {
      $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');

      $this->load->library('rbac');
      $menus = $this->rbac->get_menus_by_role($role_id);
      $has_dashboard_access = false;
      foreach ($menus as $m) {
          if ($m['url'] == 'admin') {
              $has_dashboard_access = true;
              break;
          }
      }

      if (!$has_dashboard_access) redirect('welcome');

      $this->load->model('Dashboard_model');
      $this->load->model('Merchant');
      
      $data['title'] = 'Dashboard';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['recent_mutations'] = []; 

      // We no longer calculate stats or metadata here to ensure INSTANT page load
      $this->load->view('admin/index_dashboard', $data);
   }

   public function ajax_dashboard_metadata_json()
   {
      if (!$this->input->is_ajax_request()) return;
      session_write_close();
      $this->load->model('Dashboard_model');
      $this->load->model('Merchant');
      
      $data = [
         'merchant_count' => $this->Dashboard_model->get_merchant_count(),
         'maintenance_status' => $this->Merchant->getMaintenanceStatus()
      ];

      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
   }

   public function ajax_today_stats_json()
   {
      if (!$this->input->is_ajax_request()) return;
      session_write_close();
      $this->load->model('Dashboard_model');
      $this->load->driver('cache', array('adapter' => 'file'));
      
      $cache_key = 'dashboard_today_stats_v3';
      $cached_data = $this->cache->get($cache_key);
      if ($cached_data !== FALSE) {
         return $this->output->set_content_type('application/json')->set_output(json_encode($cached_data));
      }

      $stats = [
         'today_stats' => $this->Dashboard_model->get_today_stats(),
         'last_synced' => date('H:i:s')
      ];

      $this->cache->save($cache_key, $stats, 300); 
      return $this->output->set_content_type('application/json')->set_output(json_encode($stats));
   }

   public function ajax_monthly_stats_json()
   {
      if (!$this->input->is_ajax_request()) return;
      session_write_close();
      $this->load->model('Dashboard_model');
      $this->load->driver('cache', array('adapter' => 'file'));
      
      $cache_key = 'dashboard_monthly_stats_v3';
      $cached_data = $this->cache->get($cache_key);
      if ($cached_data !== FALSE) {
         return $this->output->set_content_type('application/json')->set_output(json_encode($cached_data));
      }

      $stats = [
         'monthly_overview' => $this->Dashboard_model->get_monthly_overview()
      ];

      $this->cache->save($cache_key, $stats, 300); 
      return $this->output->set_content_type('application/json')->set_output(json_encode($stats));
   }

   public function recent_mutations_json()
   {
      session_write_close();
      $this->load->model('Dashboard_model');
      $this->load->library('datatables');
      
      $mutations = $this->Dashboard_model->get_recent_mutations(50); 
      
      return $this->datatables
         ->set_data($mutations)
         ->set_recordsTotal(count($mutations))
         ->set_recordsFiltered(count($mutations))
         ->editColumn('date', function($row) {
             return date('H:i:s d/m/Y', strtotime($row->date));
         })
         ->editColumn('amount', function($row) {
             return round($row->amount);
         })
         ->make();
   }

   public function analytics()
   {
      $data['title'] = 'Analytics';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $role_id = $this->session->userdata('role');

      if ($this->rbac->has_permission($role_id, "no_action")) {
         $this->load->view('admin/index', $data);
         return;
      }

      $period = $this->input->get('period') ?: 'last_7_days';
      $today = new DateTime('today');

      switch ($period) {
         case 'yesterday':
            $current_start = (clone $today)->modify('-1 day');
            $current_end = clone $current_start;
            $date_range_label = $current_start->format('d M Y');
            $comparison_label = 'prev. day';
            break;

         case 'last_month':
            $current_start = (clone $today)->modify('first day of last month');
            $current_end = (clone $today)->modify('last day of last month');
            $date_range_label = $current_start->format('d M Y') . ' - ' . $current_end->format('d M Y');
            $comparison_label = 'prev. month';
            break;

         default:
            $period = 'last_7_days';
            $current_start = (clone $today)->modify('-6 days');
            $current_end = clone $today;
            $date_range_label = $current_start->format('d M Y') . ' - ' . $current_end->format('d M Y');
            $comparison_label = 'prev. week';
            break;
      }

      $data['current_period'] = $period;
      $data['date_range_label'] = $date_range_label;
      $data['comparison_label'] = $comparison_label;

      $this->load->view('admin/index_real', $data);
   }

   public function ajax_analytics_data_json()
   {
      if (!$this->input->is_ajax_request()) return;
      session_write_close();
      
      $period = $this->input->get('period') ?: 'last_7_days';
      $today = new DateTime('today');

      switch ($period) {
         case 'yesterday':
            $current_start = (clone $today)->modify('-1 day');
            $current_end = clone $current_start;
            $previous_start = (clone $today)->modify('-2 days');
            $previous_end = clone $previous_start;
            $comparison_label = 'prev. day';
            break;

         case 'last_month':
            $current_start = (clone $today)->modify('first day of last month');
            $current_end = (clone $today)->modify('last day of last month');
            $previous_start = (clone $current_start)->modify('-1 month');
            $previous_end = (clone $current_start)->modify('-1 day');
            $comparison_label = 'prev. month';
            break;

         default:
            $period = 'last_7_days';
            $current_start = (clone $today)->modify('-6 days');
            $current_end = clone $today;
            $previous_start = (clone $current_start)->modify('-7 days');
            $previous_end = (clone $current_start)->modify('-1 day');
            $comparison_label = 'prev. week';
            break;
      }

      $current_from = $current_start->format('Y-m-d');
      $current_to = $current_end->format('Y-m-d');
      $prev_from = $previous_start->format('Y-m-d');
      $prev_to = $previous_end->format('Y-m-d');

      $statsTemplate = ['amount' => 0, 'fee' => 0, 'fee_external' => 0];
      $current_stats = ['qris' => $statsTemplate, 'disburse' => $statsTemplate, 'va' => $statsTemplate];
      $prev_stats = $current_stats;

      $channels = [
         'qris' => ['table' => 'cashin_payment_qris_mpm', 'dateField' => 'c_datetimePayment', 'where' => []],
         'va' => ['table' => 'cashin_payment_va', 'dateField' => 'c_datetimePayment', 'where' => []],
         'disburse' => ['table' => 'cashout_payment_bifast', 'dateField' => 'c_datetime', 'where' => ['c_status' => 'SUCCESS']],
      ];

      foreach (['current' => ['from' => $current_from, 'to' => $current_to], 'prev' => ['from' => $prev_from, 'to' => $prev_to]] as $window => $range) {
         foreach ($channels as $channel => $params) {
            $this->db->select('COALESCE(SUM(c_amount), 0) as amount, COALESCE(SUM(c_fee), 0) as fee, COALESCE(SUM(c_feeExternal), 0) as fee_external');
            $this->db->from($params['table']);
            $this->db->where($params['dateField'] . ' >=', $range['from'] . ' 00:00:00');
            $this->db->where($params['dateField'] . ' <=', $range['to'] . ' 23:59:59');
            foreach ($params['where'] as $field => $value) $this->db->where($field, $value);

            $row = $this->db->get()->row_array();
            $res = ['amount' => (float)($row['amount'] ?? 0), 'fee' => (float)($row['fee'] ?? 0), 'fee_external' => (float)($row['fee_external'] ?? 0)];
            if ($window === 'current') $current_stats[$channel] = $res;
            else $prev_stats[$channel] = $res;
         }
      }

      // Success Rate calculation (Based on Disburse stability as per original logic)
      $this->db->select('count(*) as total');
      $this->db->from('cashout_payment_bifast');
      $this->db->where('c_datetime >=', $current_from . ' 00:00:00');
      $this->db->where('c_datetime <=', $current_to . ' 23:59:59');
      $total_disburse = $this->db->get()->row()->total;

      $this->db->select('count(*) as success');
      $this->db->from('cashout_payment_bifast');
      $this->db->where('c_datetime >=', $current_from . ' 00:00:00');
      $this->db->where('c_datetime <=', $current_to . ' 23:59:59');
      $this->db->where('c_status', 'SUCCESS');
      $success_disburse = $this->db->get()->row()->success;

      $success_rate = ($total_disburse > 0) ? round(($success_disburse / $total_disburse) * 100, 1) : 100;

      // Chart Data
      $chart_data = $this->_get_period_chart_data($period, $current_from, $current_to);

      $response = [
         'current_stats' => $current_stats,
         'prev_stats' => $prev_stats,
         'success_rate' => $success_rate,
         'chart_data' => $chart_data,
         'comparison_label' => $comparison_label
      ];

      return $this->output->set_content_type('application/json')->set_output(json_encode($response));
   }

   private function _get_period_chart_data($period, $date_from, $date_to)
   {
      $labels = []; $values = [];
      if ($period == 'yesterday') {
         for ($i = 0; $i < 24; $i++) { 
            $labels[] = str_pad($i, 2, "0", STR_PAD_LEFT) . ":00"; 
            $values[$i] = 0; 
         }
         $query = $this->db->select('HOUR(c_datetime) as hour, SUM(c_amount) as amount')
               ->from('cashin_payment_qris_mpm')
               ->where('c_datetime >=', $date_from . ' 00:00:00')
               ->where('c_datetime <=', $date_from . ' 23:59:59')
               ->group_by('HOUR(c_datetime)')
               ->get()
               ->result_array();
         foreach ($query as $row) $values[(int)$row['hour']] = (float)$row['amount'];
      } else {
         $start = new DateTime($date_from); 
         $end = new DateTime($date_to); 
         $range = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));
         foreach ($range as $date) { 
            $labels[] = $date->format('d M'); 
            $values[$date->format('Y-m-d')] = 0; 
         }
         $this->db->select("DATE(c_datetime) as date, SUM(c_amount) as amount")
                  ->from('cashin_payment_qris_mpm')
                  ->where('c_datetime >=', $date_from . ' 00:00:00')
                  ->where('c_datetime <=', $date_to . ' 23:59:59')
                  ->group_by("DATE(c_datetime)");
         foreach ($this->db->get()->result_array() as $row) {
            if (isset($values[$row['date']])) {
               $values[$row['date']] = (float)$row['amount'];
            }
         }
         $values = array_values($values);
      }
      return ['labels' => $labels, 'values' => $values];
   }

   public function toggleOpenApiStatus()
   {
      if ($this->input->method() === 'post') {
         $status = $this->input->post('status');
         if (!in_array($status, ['Not Active', 'Active'])) { http_response_code(400); echo json_encode(['message' => 'Invalid status']); return; }
         $this->load->model('Merchant');
         if ($status === 'Not Active') { $this->Merchant->setAllOpenApiStatus('Not Active'); $this->Merchant->setMaintenanceStatus('Not Active'); $message = 'Maintenance ON'; $action = 'Maintenance ON'; }
         else { $this->Merchant->setActiveMerchantsOpenApiStatus('Active'); $this->Merchant->setMaintenanceStatus('Active'); $message = 'Maintenance OFF'; $action = 'Maintenance OFF'; }
         $email = $this->session->userdata('c_email') ?: 'Unknown';
         $this->db->insert('maintenance_log', ['username' => $email, 'action' => $action, 'status_set' => $status, 'timestamp' => date('Y-m-d H:i:s')]);
         echo json_encode(['message' => $message]);
      } else show_404();
   }

   public function getMaintenanceStatus()
   {
       $this->load->model('Merchant');
       return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => $this->Merchant->getMaintenanceStatus()]));
   }

   public function syncAvailableBalanceMerchant()
   {
      ini_set('max_execution_time', 600); ini_set('memory_limit', '1024M');
      $merchant_id = $this->input->get('merchant_id'); $do_update = $this->input->get('do_update') == '1';
      $this->db->select('id, c_name, c_balanceTotal, c_balanceHold')->from('merchant')->where('c_status', 'Active');
      if (!empty($merchant_id)) $this->db->where('id', $merchant_id);
      $merchants = $this->db->get()->result_array();
      
      $this->db->select('m.id, (COALESCE(cin.total, 0) - COALESCE(cout.total, 0)) as balanceActual')->from('merchant m')->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashin GROUP BY ref_merchantId) cin', 'cin.ref_merchantId = m.id', 'left')->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashout GROUP BY ref_merchantId) cout', 'cout.ref_merchantId = m.id', 'left');
      if (!empty($merchant_id)) $this->db->where('m.id', $merchant_id);
      $actualBalances = array_column($this->db->where('m.c_status', 'Active')->get()->result_array(), 'balanceActual', 'id');

      $this->db->select('m.id, (COALESCE(q.total, 0) + COALESCE(v.total, 0) + COALESCE(e.total, 0)) as holdActual')->from('merchant m')->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_qris_mpm WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) q', 'q.ref_merchantId = m.id', 'left')->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_va WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) v', 'v.ref_merchantId = m.id', 'left')->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_ewallet WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) e', 'e.ref_merchantId = m.id', 'left');
      if (!empty($merchant_id)) $this->db->where('m.id', $merchant_id);
      $actualHolds = array_column($this->db->where('m.c_status', 'Active')->get()->result_array(), 'holdActual', 'id');

      $results = []; $no = 1;
      foreach ($merchants as $row) {
         $id = $row['id']; $balA = round($actualBalances[$id] ?? 0); $holdA = round($actualHolds[$id] ?? 0);
         $upT = false; $upH = false;
         if ($do_update) {
            $this->db->trans_start();
            $curr = $this->db->query("SELECT id, c_balanceTotal, c_balanceHold FROM merchant WHERE id = ? FOR UPDATE", [$id])->row_array();
            if ($curr) {
               if (round($curr['c_balanceTotal']) != $balA) { $this->db->where('id', $id)->update('merchant', ['c_balanceTotal' => $balA]); $upT = true; }
               if (round($curr['c_balanceHold']) != $holdA) { $this->db->where('id', $id)->update('merchant', ['c_balanceHold' => $holdA]); $upH = true; }
            }
            $this->db->trans_complete();
         }
         $results[] = ['no' => $no++, 'id' => $id, 'name' => $row['c_name'], 'balance_actual' => $balA, 'balance_system' => $upT ? $balA : round($row['c_balanceTotal']), 'hold_actual' => $holdA, 'hold_system' => $upH ? $holdA : round($row['c_balanceHold']), 'updated_total' => $upT, 'updated_hold' => $upH];
      }
      $this->load->view('admin/balance_sync_view', ['sync_results' => $results, 'do_update' => $do_update]);
   }

   public function welcome()
   {
      $data['title'] = 'Welcome'; $data['user'] = $this->Model_user->view_user()->row_array();
      $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
      $this->load->library('rbac'); $data['menus'] = $this->rbac->get_menus_by_role($role_id);
      $hour = date('H');
      if ($hour < 12) $data['greeting'] = 'Good Morning';
      elseif ($hour < 17) $data['greeting'] = 'Good Afternoon';
      else $data['greeting'] = 'Good Evening';
      $this->load->view('admin/welcome', $data);
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
          foreach ($merchants as $m) $results[] = ['title' => $m->c_name . " (#$m->id)", 'url' => base_url('admin/merchant?search_merchant=' . $m->id), 'category' => 'Merchant', 'icon' => 'fas fa-store'];

          // 2. IDs
          $cashin_ids = [-1]; $cashout_ids = [-1]; $rrn_ids = [-1];
          if (strlen($query) >= 6) {
             $op = (strlen($query) >= 15) ? '=' : 'LIKE';
             $val = (strlen($query) >= 15) ? "'$safeQuery'" : "'$safeQuery%'";

             $res_dyn = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId $op $val LIMIT 1")->result();
             if ($res_dyn) {
                $results[] = ['title' => $query, 'url' => base_url('admin/qris_dynamic?transid=' . $query), 'category' => 'QRIS Dynamic', 'icon' => 'fas fa-qrcode'];
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
          // Skip scanning 80M+ row tables if we already found the transaction as a Trans ID/RRN in Section 2
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
                $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/qris?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'QRIS', 'icon' => 'fas fa-qrcode'];
             }
          }
          if (count($cashin_ids) > 1 || strlen($query) >= 10) {
             $this->db->reset_query(); $this->db->select("MAX(v.id) as id, c.c_invoiceNo, MAX(v.c_vaNumber) as c_vaNumber, MAX(v.c_amount) as c_amount")->from('cashin_payment_va v')->join('cashin c', 'c.id = v.ref_cashinId')->group_start()->where_in('v.ref_cashinId', $cashin_ids);
             if (is_numeric($query)) $this->db->or_like('v.c_vaNumber', $query, 'after');
             $this->db->group_end()->group_by('c.id');
             foreach ($this->db->limit(3)->get()->result() as $r) {
                $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/virtual_account?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'VA', 'icon' => 'fas fa-university'];
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
                $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query), 'url' => base_url('admin/bi_fast?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'BI-FAST', 'icon' => 'fas fa-exchange-alt'];
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
                $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query), 'url' => base_url('admin/history?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'Purchase', 'icon' => 'fas fa-shopping-cart'];
             }
          }
          if (count($cashin_ids) > 1) {
             $this->db->reset_query(); $this->db->select("MAX(e.id) as id, c.c_invoiceNo, MAX(e.c_amount) as c_amount")->from('cashin_payment_ewallet e')->join('cashin c', 'c.id = e.ref_cashinId')->where_in('e.ref_cashinId', $cashin_ids)->group_by('c.id');
             foreach ($this->db->limit(3)->get()->result() as $r) {
                $isInvoice = (stripos($r->c_invoiceNo, $query) !== false);
                $results[] = ['title' => ($isInvoice ? $r->c_invoiceNo : $query) . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/ewallet?' . ($isInvoice ? 'invoice=' : 'transid=') . ($isInvoice ? $r->c_invoiceNo : $query)), 'category' => 'E-Wallet', 'icon' => 'fas fa-wallet'];
             }
          }

          // 5. Dynamic & Recurring Modules
          $safeQuery = $this->db->escape_str($query);
          
          // VA Dynamic
          $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_dynamic_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
          foreach ($res as $r) {
             $results[] = ['title' => $r->c_vaNumber . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/Va_dynamic?transid=' . $query), 'category' => 'VA Dynamic', 'icon' => 'fas fa-university'];
          }
          
          // VA Recurring
          $res = $this->db->query("SELECT c_merchantTransactionId, c_vaNumber, c_amount FROM cashin_recurring_va WHERE c_vaNumber LIKE '$safeQuery%' OR c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
          foreach ($res as $r) {
             $results[] = ['title' => $r->c_vaNumber . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/VA_recurring?transid=' . $query), 'category' => 'VA Recurring', 'icon' => 'fas fa-history'];
          }
          
          // QRIS Dynamic
          $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
          foreach ($res as $r) {
             $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/qris_dynamic?transid=' . $query), 'category' => 'QRIS Dynamic', 'icon' => 'fas fa-qrcode'];
          }
          
          // QRIS Recurring
          $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
          foreach ($res as $r) {
             $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/qris_recurring?transid=' . $query), 'category' => 'QRIS Recurring', 'icon' => 'fas fa-redo'];
          }
          
          // E-Wallet Dynamic
          $res = $this->db->query("SELECT c_merchantTransactionId, c_amount FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeQuery%' LIMIT 3")->result();
          foreach ($res as $r) {
             $results[] = ['title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount), 'url' => base_url('admin/ewallet_dynamic?transid=' . $query), 'category' => 'E-Wallet Dynamic', 'icon' => 'fas fa-wallet'];
          }

          echo json_encode(array_slice($results, 0, 15));
       } catch (Exception $e) { echo json_encode([]); }
    }
}
