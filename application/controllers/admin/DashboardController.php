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
      $this->load->model('Mutation_model'); // required if needed

      is_logged_in();
   }

   public function index()
   {
      $this->load->model('Dashboard_model');
      $this->load->model('Merchant');
      
      $data['title'] = 'Dashboard';
      $data['user'] = $this->Model_user->view_user()->row_array();
      
      // Optimized: Deferred loading for Recent Mutations 
      // It will be loaded via AJAX DataTables in the view for faster initial page load
      $data['recent_mutations'] = []; 

      // OPTIMIZATION: Implement 5-minute Caching for Dashboard Stats
      // This prevents scanning 160M rows on every page hit
      $cache_ttl = 300; // 5 minutes
      $last_cache = $this->session->userdata('dash_stats_timeout');
      
      if ($last_cache && time() < $last_cache) {
         $data['today_stats'] = $this->session->userdata('dash_today_stats');
         $data['monthly_overview'] = $this->session->userdata('dash_monthly_overview');
      } else {
         $data['today_stats'] = $this->Dashboard_model->get_today_stats();
         $data['monthly_overview'] = $this->Dashboard_model->get_monthly_overview();
         
         $this->session->set_userdata('dash_today_stats', $data['today_stats']);
         $this->session->set_userdata('dash_monthly_overview', $data['monthly_overview']);
         $this->session->set_userdata('dash_stats_timeout', time() + $cache_ttl);
      }

      $data['merchant_count'] = $this->Dashboard_model->get_merchant_count();
      $data['maintenance_status'] = $this->Merchant->getMaintenanceStatus();

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      $this->load->view('admin/index_dashboard', $data);
      $this->load->view('templates/user_footer.php');
   }

   public function recent_mutations_json()
   {
      $this->load->model('Dashboard_model');
      $mutations = $this->Dashboard_model->get_recent_mutations(50); // Fetch more for scrolling/paging if needed
      
      $data = [];
      $no = 1;
      foreach ($mutations as $row) {
         $data[] = [
            'date' => date('H:i:s d/m/Y', strtotime($row->date)),
            'merchant' => $row->merchant,
            'type' => '<span class="badge text-dark border px-3 py-1 font-weight-bold small shadow-none">' . $row->type . '</span>',
            'amount' => '<span class="font-weight-bold text-primary">Rp ' . number_format($row->amount, 0, ',', '.') . '</span>',
            'status' => $this->_get_status_badge($row->status)
         ];
      }

      echo json_encode([
         "draw" => intval($this->input->post('draw')),
         "recordsTotal" => count($data),
         "recordsFiltered" => count($data),
         "data" => $data
      ]);
   }

   private function _get_status_badge($status)
   {
      $status_class = 'secondary';
      if (in_array($status, ['Success', 'PAID', 'SETTLEMENT'])) $status_class = 'success';
      if (in_array($status, ['Pending', 'PROCESS'])) $status_class = 'warning';
      if (in_array($status, ['Failed', 'REJECTED'])) $status_class = 'danger';
      
      return '<span class="badge badge-'.$status_class.' rounded-pill px-3 py-1 shadow-none small font-weight-bold">'.$status.'</span>';
   }

   public function analytics()
   {
      $data['title'] = 'Analytics';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $role_id = $this->session->userdata('role');

      $this->load->view('templates/user_header.php', $data);
      $this->load->view('templates/user_sidebar.php', $data);
      $this->load->view('templates/user_topbar.php', $data);
      
      if ($this->rbac->has_permission($role_id, "no_action")) {
         $this->load->view('admin/index', $data);
      } else {
         $this->load->model('Qris');
         $this->load->model('BiFast');
         $this->load->model('VirtualAccount');
         $this->load->model('Merchant');

         // ── Period Calculation ──
         $period = $this->input->get('period') ? $this->input->get('period') : 'last_month';
         $data['current_period'] = $period;

         switch ($period) {
            case 'yesterday':
                $date_from = date('Y-m-d', strtotime('-1 day'));
                $date_to   = $date_from; 
                // Previous: the day before yesterday
                $prev_date_from = date('Y-m-d', strtotime('-2 days'));
                $prev_date_to   = $prev_date_from;
                $data['comparison_label'] = 'yesterday';
                break;
            case 'last_7_days':
                $date_from = date('Y-m-d', strtotime('-7 days'));
                $date_to   = date('Y-m-d');
                // Previous: 7 days before those
                $prev_date_from = date('Y-m-d', strtotime('-14 days'));
                $prev_date_to   = date('Y-m-d', strtotime('-8 days'));
                $data['comparison_label'] = 'last week';
                break;
            case 'last_month':
            default:
                $date_from = date('Y-m-01', strtotime('first day of last month'));
                $date_to   = date('Y-m-d', strtotime('last day of last month'));
                // Previous: the month before that
                $prev_date_from = date('Y-m-01', strtotime('first day of -2 month'));
                $prev_date_to   = date('Y-m-t', strtotime('last day of -2 month'));
                $data['comparison_label'] = 'last month';
                break;
         }

         // Format for QRIS (String YmdHis)
         $date_from_qris = date('Ymd', strtotime($date_from))."000001";
         $date_to_qris   = date('Ymd', strtotime($date_to))."235959";

         $prev_date_from_qris = date('Ymd', strtotime($prev_date_from))."000001";
         $prev_date_to_qris   = date('Ymd', strtotime($prev_date_to))."235959";

         // Always set Date Range Label (Lightweight)
         $data['date_range_label'] = date('d M Y', strtotime($date_from)) . ($date_from != $date_to ? ' - ' . date('d M Y', strtotime($date_to)) : '');
         
         // Always set Submerchants (Required for filter dropdown)
         $data['submerchants'] = $this->Merchant->get_merchant(null, null, null);

         // OPTIMIZATION: Implement 5-minute Period-Based Caching for Analytics
         $cache_key = 'dash_analytics_' . $period;
         $cache_timeout_key = $cache_key . '_timeout';
         $cache_ttl = 300; // 5 minutes
         
         $last_cache_time = $this->session->userdata($cache_timeout_key);
         
         if ($last_cache_time && time() < $last_cache_time) {
             // Load from Cache
             $cached_data = $this->session->userdata($cache_key);
             $data['current_stats'] = $cached_data['current_stats'];
             $data['prev_stats'] = $cached_data['prev_stats'];
             $data['success_rate'] = $cached_data['success_rate'];
             $data['chart_data'] = $cached_data['chart_data'];
             // Legacy support
             $data['qris_summary_last_month'] = $cached_data['current_stats']['qris_raw'];
             $data['disburse_summary_last_month'] = $cached_data['current_stats']['disburse_raw'];
             $data['va_summary_last_month'] = $cached_data['current_stats']['va_raw'];
         } else {
             // ── Fetch Current Summaries ──
             $qris_summary     = $this->Qris->get_summary($date_from_qris, $date_to_qris, "");
             $disburse_summary = $this->BiFast->get_summary($date_from, $date_to, "");
             $va_summary       = $this->VirtualAccount->get_summary($date_from, $date_to, "");

             // ── Fetch Total Attempts (Success Rate Calculation) ──
             $start_dt = $date_from . ' 00:00:00';
             $end_dt   = $date_to . ' 23:59:59';

             $total_cashin_attempts = $this->db->where('c_datetime >=', $start_dt)
                                               ->where('c_datetime <=', $end_dt)
                                               ->count_all_results('cashin');
             $total_cashout_attempts = $this->db->where('c_datetime >=', $start_dt)
                                                ->where('c_datetime <=', $end_dt)
                                                ->count_all_results('cashout');
             $total_attempts = ($total_cashin_attempts + $total_cashout_attempts) ?: 1; 
             $total_paid = ($qris_summary[0]['qty'] + $disburse_summary[0]['qty'] + $va_summary[0]['qty']);
             $data['success_rate'] = round(($total_paid / $total_attempts) * 100, 1);

             // ── Fetch Previous Summaries ──
             $qris_summary_prev     = $this->Qris->get_summary($prev_date_from_qris, $prev_date_to_qris, "");
             $disburse_summary_prev = $this->BiFast->get_summary($prev_date_from, $prev_date_to, "");
             $va_summary_prev       = $this->VirtualAccount->get_summary($prev_date_from, $prev_date_to, "");
             
             $data['current_stats'] = [
                 'qris' => $qris_summary[0],
                 'disburse' => $disburse_summary[0],
                 'va' => $va_summary[0],
                 'qris_raw' => $qris_summary,
                 'disburse_raw' => $disburse_summary,
                 'va_raw' => $va_summary
             ];
             $data['prev_stats'] = [
                 'qris' => $qris_summary_prev[0],
                 'disburse' => $disburse_summary_prev[0],
                 'va' => $va_summary_prev[0]
             ];
             
             // Legacy compatibility 
             $data['qris_summary_last_month'] = $qris_summary; 
             $data['disburse_summary_last_month'] = $disburse_summary;
             $data['va_summary_last_month'] = $va_summary;
             
             $data['chart_data'] = $this->_get_period_chart_data($period, $date_from, $date_to);

             // Save to Cache
             $cache_data = [
                 'current_stats' => $data['current_stats'],
                 'prev_stats' => $data['prev_stats'],
                 'success_rate' => $data['success_rate'],
                 'chart_data' => $data['chart_data']
             ];
             $this->session->set_userdata($cache_key, $cache_data);
             $this->session->set_userdata($cache_timeout_key, time() + $cache_ttl);
         }

         $this->load->view('admin/index_real', $data);
      }
      
      $this->load->view('templates/user_footer.php');
   }

   private function _get_period_chart_data($period, $date_from, $date_to)
   {
       $labels = [];
       $values = [];

       if ($period == 'yesterday') {
           // Hourly
           for ($i = 0; $i < 24; $i++) {
               $hour = str_pad($i, 2, "0", STR_PAD_LEFT);
               $labels[] = $hour . ":00";
               $values[$i] = 0;
           }
            $query = $this->db->select('HOUR(c_datetime) as hour, SUM(c_amount) as amount')
                ->from('cashin_payment_qris_mpm')
                ->where('c_datetime >=', $date_from . ' 00:00:00')
                ->where('c_datetime <=', $date_from . ' 23:59:59')
                ->group_by('HOUR(c_datetime)')
                ->get()->result_array();
           foreach ($query as $row) {
               $values[(int)$row['hour']] = (float)$row['amount'];
           }
       } else {
           // Daily for 7 days or month
           $startStr = $date_from;
           $endStr   = $date_to;
           $today    = date('Y-m-d');

           $start = new DateTime($startStr);
           $end   = new DateTime($endStr);
           $interval = new DateInterval('P1D');
           $range = new DatePeriod($start, $interval, $end->modify('+1 day'));

           foreach ($range as $date) {
               $lbl = $date->format('d M');
               $labels[] = $lbl;
               $values[$date->format('Y-m-d')] = 0;
           }

           // 1. Fetch Historical Totals from Summary Table
           if ($startStr < $today) {
                $hist_end = $endStr >= $today ? date('Y-m-d', strtotime('-1 day')) : $endStr;
                
                $this->db->select("summary_date, SUM(total_amount) as amount");
                $this->db->from('tr_summary_daily');
                $this->db->where('summary_date >=', $startStr);
                $this->db->where('summary_date <=', $hist_end);
                $this->db->group_by("summary_date");
                $res = $this->db->get()->result_array();
                
                foreach ($res as $row) {
                    if (isset($values[$row['summary_date']])) {
                        $values[$row['summary_date']] += (float)$row['amount'];
                    }
                }
           }

           // 2. Add Live Today Data if in range
           if ($endStr >= $today) {
                $this->load->model('Dashboard_model');
                $live_stats = $this->Dashboard_model->get_today_stats();
                $values[$today] += (float)$live_stats['total_volume'];
           }
           
           $values = array_values($values);
       }

       return ['labels' => $labels, 'values' => $values];
   }

   public function toggleOpenApiStatus()
   {
      if ($this->input->method() === 'post') {
         $input = json_decode($this->input->raw_input_stream, true);
         $status = isset($input['status']) ? $input['status'] : null;

         if (!in_array($status, ['Not Active', 'Active'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid status received.']);
            return;
         }

         $this->load->model('Merchant');

         if ($status === 'Not Active') {
            $this->Merchant->setAllOpenApiStatus('Not Active');
            $this->Merchant->setMaintenanceStatus('Not Active');
            $message = 'All merchants API are Not Active (Maintenance ON)';
            $action = 'Maintenance ON';
        } elseif ($status === 'Active') {
            $this->Merchant->setActiveMerchantsOpenApiStatus('Active');
            $this->Merchant->setMaintenanceStatus('Active');
            $message = 'All merchants API are Active (Maintenance OFF)';
            $action = 'Maintenance OFF';
        }

         $email = $this->session->userdata('c_email') !== NULL ? $this->session->userdata('c_email') : 'Unknown';

         $logData = [
               'username'   => $email,
               'action'     => $action,
               'status_set' => $status,
               'timestamp'  => date('Y-m-d H:i:s')
         ];

         if (!$this->db->insert('maintenance_log', $logData)) {
               log_message('error', 'Failed to insert maintenance log: ' . print_r($this->db->error(), true));
               echo json_encode(['message' => 'Failed to log maintenance action']);
               return;
         }

         echo json_encode(['message' => $message]);
      } else {
         show_404();
      }
   }

   public function getMaintenanceStatus()
   {
       $this->load->model('Merchant');
       $status = $this->Merchant->getMaintenanceStatus();
   
       return $this->output
           ->set_content_type('application/json')
           ->set_output(json_encode(['status' => $status]));
   }

   public function syncAvailableBalanceMerchant()
   {
      ini_set('max_execution_time', 300); 
      ini_set('memory_limit', '1024M');

      $merchant_id = $this->input->get('merchant_id');
      $do_update   = $this->input->get('do_update') == '1';

      $this->db->select('id, c_name, c_balanceTotal, c_balanceHold');
      $this->db->from('merchant');
      $this->db->where('c_status', 'Active');
      
      if (!empty($merchant_id)) {
         $this->db->where('id', $merchant_id);
      }
      
      $merchants = $this->db->get()->result_array();
      
      // OPTIMIZATION: Fetch all actual balances and hold amounts in bulk to avoid N+1 queries
      // 1. Calculate Actual Balance (Cashin - Cashout)
      $this->db->select('m.id, 
          (COALESCE(cin.total, 0) - COALESCE(cout.total, 0)) as balanceActual');
      $this->db->from('merchant m');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashin GROUP BY ref_merchantId) cin', 'cin.ref_merchantId = m.id', 'left');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashout GROUP BY ref_merchantId) cout', 'cout.ref_merchantId = m.id', 'left');
      if (!empty($merchant_id)) $this->db->where('m.id', $merchant_id);
      $this->db->where('m.c_status', 'Active');
      $actualBalancesRaw = $this->db->get()->result_array();
      $actualBalances = array_column($actualBalancesRaw, 'balanceActual', 'id');

      // 2. Calculate Actual Hold (QRIS + VA + Ewallet where settlement is not realtime)
      $this->db->select('m.id, 
          (COALESCE(q.total, 0) + COALESCE(v.total, 0) + COALESCE(e.total, 0)) as holdActual');
      $this->db->from('merchant m');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_qris_mpm WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) q', 'q.ref_merchantId = m.id', 'left');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_va WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) v', 'v.ref_merchantId = m.id', 'left');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_ewallet WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) e', 'e.ref_merchantId = m.id', 'left');
      if (!empty($merchant_id)) $this->db->where('m.id', $merchant_id);
      $this->db->where('m.c_status', 'Active');
      $actualHoldsRaw = $this->db->get()->result_array();
      $actualHolds = array_column($actualHoldsRaw, 'holdActual', 'id');

      $results = [];
      $no = 1;

      foreach ($merchants as $row1) {
         $id = $row1['id'];

         $balanceTotalActual = round($actualBalances[$id] ?? 0);
         $balanceHoldActual  = round($actualHolds[$id] ?? 0);

         $balanceTotalSystem = round($row1['c_balanceTotal']);
         $balanceHoldSystem  = round($row1['c_balanceHold']);

         $directUpdateBalanceTotal = false;
         $directUpdateBalanceHold  = false;

         if ($balanceTotalSystem != $balanceTotalActual && $do_update) {
               $this->db->where('id', $id)->update('merchant', ['c_balanceTotal' => $balanceTotalActual]);
               $directUpdateBalanceTotal = true;
               $balanceTotalSystem = $balanceTotalActual; 
         }

         if ($balanceHoldSystem != $balanceHoldActual && $do_update) {
               $this->db->where('id', $id)->update('merchant', ['c_balanceHold' => $balanceHoldActual]);
               $directUpdateBalanceHold = true;
               $balanceHoldSystem = $balanceHoldActual; 
         }

         $results[] = [
               'no' => $no++,
               'id' => $id,
               'name' => $row1['c_name'],
               'balance_actual' => $balanceTotalActual,
               'balance_system' => $balanceTotalSystem,
               'hold_actual'    => $balanceHoldActual,
               'hold_system'    => $balanceHoldSystem,
               'updated_total'  => $directUpdateBalanceTotal,
               'updated_hold'   => $directUpdateBalanceHold
         ];
      }

      $data['sync_results'] = $results;
      $data['do_update']    = $do_update;

      $this->load->view('admin/balance_sync_view', $data);
   }
}
