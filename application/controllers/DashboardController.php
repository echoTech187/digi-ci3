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
          if ($m['url'] == 'dashboard' || $m['url'] == 'admin') {
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

   public function testDB() {
        echo "<pre>";
        try {
            $q = $this->db->query("DESCRIBE cashin");
            print_r($q->result_array());
            $q2 = $this->db->query("DESCRIBE cashout");
            print_r($q2->result_array());
            
            // Try testing the recent query
            $q3 = $this->db->query("
                SELECT 'Cash-In' as type, c_cashinChannelGroup as channel, c_amount as amount, c_status as status, c_created as date, c_referenceId as ref 
                FROM cashin 
                WHERE ref_merchantId = 3739 
                ORDER BY c_created DESC LIMIT 5
            ");
            print_r($q3->result_array());
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
        $error = $this->db->error();
        if ($error['code']) {
            echo "DB Error: " . print_r($error, true);
        }
        echo "</pre>";
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

}
