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
      $role_id = $this->session->userdata('role');
      if (!$role_id) $role_id = $this->session->userdata('role_id');

      // Check if user has access to Dashboard Menu
      $this->load->library('rbac');
      $menus = $this->rbac->get_menus_by_role($role_id);
      $has_dashboard_access = false;
      foreach ($menus as $m) {
          if ($m['url'] == 'admin') {
              $has_dashboard_access = true;
              break;
          }
      }

      if (!$has_dashboard_access) {
          redirect('welcome');
      }

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
         $data['last_synced'] = $this->session->userdata('dash_stats_updated');
      } else {
         $data['today_stats'] = $this->Dashboard_model->get_today_stats();
         $data['monthly_overview'] = $this->Dashboard_model->get_monthly_overview();
         $data['last_synced'] = date('H:i:s');
         
         $this->session->set_userdata('dash_today_stats', $data['today_stats']);
         $this->session->set_userdata('dash_monthly_overview', $data['monthly_overview']);
         $this->session->set_userdata('dash_stats_updated', $data['last_synced']);
         $this->session->set_userdata('dash_stats_timeout', time() + $cache_ttl);
      }

      $data['merchant_count'] = $this->Dashboard_model->get_merchant_count();
      $data['maintenance_status'] = $this->Merchant->getMaintenanceStatus();

      $this->load->view('admin/index_dashboard', $data);
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
            'type' => $row->type,
            'amount' => $row->amount,
            'status' => $row->status
         ];
      }

      echo json_encode([
         "draw" => intval($this->input->post('draw')),
         "recordsTotal" => count($data),
         "recordsFiltered" => count($data),
         "data" => $data
      ]);
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
            $previous_start = (clone $today)->modify('-2 days');
            $previous_end = clone $previous_start;
            $comparison_label = 'previous day';
            $date_range_label = $current_start->format('d M Y');
            break;

         case 'last_month':
            $current_start = (clone $today)->modify('first day of last month');
            $current_end = (clone $today)->modify('last day of last month');
            $previous_start = (clone $current_start)->modify('-1 month');
            $previous_end = (clone $current_start)->modify('-1 day');
            $comparison_label = 'previous month';
            $date_range_label = $current_start->format('d M Y') . ' - ' . $current_end->format('d M Y');
            break;

         default:
            $period = 'last_7_days';
            $current_start = (clone $today)->modify('-6 days');
            $current_end = clone $today;
            $previous_start = (clone $current_start)->modify('-7 days');
            $previous_end = (clone $current_start)->modify('-1 day');
            $comparison_label = 'previous week';
            $date_range_label = $current_start->format('d M Y') . ' - ' . $current_end->format('d M Y');
            break;
      }

      $current_from = $current_start->format('Y-m-d');
      $current_to = $current_end->format('Y-m-d');
      $prev_from = $previous_start->format('Y-m-d');
      $prev_to = $previous_end->format('Y-m-d');

      $statsTemplate = ['amount' => 0, 'fee' => 0, 'fee_external' => 0];
      $data['current_stats'] = [
         'qris' => $statsTemplate,
         'disburse' => $statsTemplate,
         'va' => $statsTemplate,
      ];
      $data['prev_stats'] = $data['current_stats'];
      $data['current_period'] = $period;
      $data['comparison_label'] = $comparison_label;
      $data['date_range_label'] = $date_range_label;

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

            foreach ($params['where'] as $field => $value) {
               $this->db->where($field, $value);
            }

            $row = $this->db->get()->row_array();
            $stats = [
               'amount' => (float) ($row['amount'] ?? 0),
               'fee' => (float) ($row['fee'] ?? 0),
               'fee_external' => (float) ($row['fee_external'] ?? 0),
            ];

            if ($window === 'current') {
               $data['current_stats'][$channel] = $stats;
            } else {
               $data['prev_stats'][$channel] = $stats;
            }
         }
      }

      $disburse_total = $this->db->select('COUNT(id) as total')->from('cashout_payment_bifast')
         ->where('c_datetime >=', $current_from . ' 00:00:00')
         ->where('c_datetime <=', $current_to . ' 23:59:59')
         ->get()->row_array();
      $disburse_success = $this->db->select('COUNT(id) as total')->from('cashout_payment_bifast')
         ->where('c_status', 'SUCCESS')
         ->where('c_datetime >=', $current_from . ' 00:00:00')
         ->where('c_datetime <=', $current_to . ' 23:59:59')
         ->get()->row_array();

      $data['success_rate'] = 0;
      if (!empty($disburse_total['total'])) {
         $data['success_rate'] = round((float) $disburse_success['total'] / (float) $disburse_total['total'] * 100, 1);
      } else {
         $data['success_rate'] = 100;
      }

      $data['chart_data'] = $this->_get_period_chart_data($period, $current_from, $current_to);
      $this->load->view('admin/index_real', $data);
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
           $start = new DateTime($date_from);
           $end   = new DateTime($date_to);
           $interval = new DateInterval('P1D');
           $range = new DatePeriod($start, $interval, $end->modify('+1 day'));

           foreach ($range as $date) {
               $lbl = $date->format('d M');
               $labels[] = $lbl;
               $values[$date->format('Y-m-d')] = 0;
           }

          $tables = ['cashin_payment_qris_mpm']; // Add other tables if needed
          foreach ($tables as $t) {
              $this->db->select("DATE(c_datetime) as date, SUM(c_amount) as amount, COUNT(id) as qty");
              $this->db->from($t);
              // OPTIMIZATION: Use range queries instead of DATE() in WHERE clause
              $this->db->where('c_datetime >=', $date_from . ' 00:00:00');
              $this->db->where('c_datetime <=', $date_to . ' 23:59:59');
              if ($t == 'cashout_payment_bifast') $this->db->where('c_status', 'SUCCESS');
              $this->db->group_by("DATE(c_datetime)");
              $res = $this->db->get()->result_array();
              foreach ($res as $row) {
                  if (isset($values[$row['date']])) {
                      $values[$row['date']] += (float)$row['amount'];
                  }
              }
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

   public function welcome()
   {
      $data['title'] = 'Welcome';
      $data['user'] = $this->Model_user->view_user()->row_array();
      
      $role_id = $this->session->userdata('role');
      if (!$role_id) $role_id = $this->session->userdata('role_id');

      // Get all permitted menus for Quick Access
      $this->load->library('rbac');
      $data['menus'] = $this->rbac->get_menus_by_role($role_id);
      
      // Determine Welcome Message based on time
      $hour = date('H');
      if ($hour < 12) {
          $data['greeting'] = 'Good Morning';
      } elseif ($hour < 17) {
          $data['greeting'] = 'Good Afternoon';
      } else {
          $data['greeting'] = 'Good Evening';
      }

      $this->load->view('admin/welcome', $data);
   }
}
