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

      // OPTIMIZATION: Implement 5-minute Global Caching for Dashboard Stats
      // This prevents scanning 160M rows on every page hit, and shares the cache across ALL users.
      $this->load->driver('cache', array('adapter' => 'file'));
      $cache_key = 'global_dashboard_stats';
      $cached_data = $this->cache->get($cache_key);
      
      if ($cached_data !== FALSE) {
         $data['today_stats'] = $cached_data['today_stats'];
         $data['monthly_overview'] = $cached_data['monthly_overview'];
         $data['last_synced'] = $cached_data['last_synced'];
      } else {
         $data['today_stats'] = $this->Dashboard_model->get_today_stats();
         $data['monthly_overview'] = $this->Dashboard_model->get_monthly_overview();
         $data['last_synced'] = date('H:i:s');
         
         $cache_payload = [
            'today_stats' => $data['today_stats'],
            'monthly_overview' => $data['monthly_overview'],
            'last_synced' => $data['last_synced']
         ];
         // Cache for 300 seconds (5 minutes)
         $this->cache->save($cache_key, $cache_payload, 300);
      }

      $data['merchant_count'] = $this->Dashboard_model->get_merchant_count();
      $data['maintenance_status'] = $this->Merchant->getMaintenanceStatus();

      $this->load->view('admin/index_dashboard', $data);
   }

   public function recent_mutations_json()
   {
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
         $status = $this->input->post('status');

         if (!in_array($status, ['Not Active', 'Active'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid status received.']);
            return;
         }

         $this->load->model('Merchant');

         if ($status === 'Not Active') {
            $this->Merchant->setAllOpenApiStatus('Not Active');
            $this->Merchant->setMaintenanceStatus('Not Active');
            $message = 'All merchants API are Active (Maintenance ON)';
            $action = 'Maintenance ON';
        } elseif ($status === 'Active') {
            $this->Merchant->setActiveMerchantsOpenApiStatus('Active');
            $this->Merchant->setMaintenanceStatus('Active');
            $message = 'All merchants API are Not Active (Maintenance OFF)';
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
      ini_set('max_execution_time', 600); 
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
      
      // Calculate Actuals (Read-only initially for the report)
      // 1. Calculate Actual Balance (Cashin - Cashout)
      $this->db->select('m.id, (COALESCE(cin.total, 0) - COALESCE(cout.total, 0)) as balanceActual');
      $this->db->from('merchant m');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashin GROUP BY ref_merchantId) cin', 'cin.ref_merchantId = m.id', 'left');
      $this->db->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashout GROUP BY ref_merchantId) cout', 'cout.ref_merchantId = m.id', 'left');
      if (!empty($merchant_id)) $this->db->where('m.id', $merchant_id);
      $this->db->where('m.c_status', 'Active');
      $actualBalancesRaw = $this->db->get()->result_array();
      $actualBalances = array_column($actualBalancesRaw, 'balanceActual', 'id');

      // 2. Calculate Actual Hold
      $this->db->select('m.id, (COALESCE(q.total, 0) + COALESCE(v.total, 0) + COALESCE(e.total, 0)) as holdActual');
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

         if ($do_update) {
            /**
             * CRITICAL: Using Transaction and Row-Level Locking (SELECT FOR UPDATE)
             * to prevent balance corruption during concurrent updates.
             */
            $this->db->trans_start();
            
            // Lock the row
            $currentRow = $this->db->query("SELECT id, c_balanceTotal, c_balanceHold FROM merchant WHERE id = ? FOR UPDATE", [$id])->row_array();
            
            if ($currentRow) {
               // Re-calculate ONLY if it still differs from the locked current row value
               if (round($currentRow['c_balanceTotal']) != $balanceTotalActual) {
                  $this->db->where('id', $id)->update('merchant', ['c_balanceTotal' => $balanceTotalActual]);
                  $directUpdateBalanceTotal = true;
                  $balanceTotalSystem = $balanceTotalActual;
               }
               if (round($currentRow['c_balanceHold']) != $balanceHoldActual) {
                  $this->db->where('id', $id)->update('merchant', ['c_balanceHold' => $balanceHoldActual]);
                  $directUpdateBalanceHold = true;
                  $balanceHoldSystem = $balanceHoldActual;
               }
            }
            
            $this->db->trans_complete();
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

   /**
    * Unified Global Search Handler
    * Searches across Menus, Merchants, QRIS, VA, and E-Wallet.
    */
   public function globalSearch()
   {
      $query = $this->input->get('q');
      if (!$query || strlen($query) < 2) {
         echo json_encode([]);
         return;
      }

      $results = [];
      $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');

      // 1. Search Menus & Submenus
      $this->db->select('m.title as menu_title, m.url as menu_url, m.icon as menu_icon');
      $this->db->from('user_menu m');
      $this->db->join('user_access_menu a', 'a.menu_id = m.id');
      $this->db->where('a.role_id', $role_id);
      $this->db->where('m.is_active', 1);
      $this->db->group_start();
      $this->db->like('m.title', $query);
      $this->db->or_like('m.url', $query);
      $this->db->group_end();
      $this->db->limit(10);
      $menus = $this->db->get()->result();
      
      foreach ($menus as $m) {
         if ($m->menu_url === '#') continue;
         $results[] = [
            'title' => $m->menu_title,
            'url' => base_url($m->menu_url),
            'category' => 'Navigation',
            'icon' => $m->menu_icon ?: 'fas fa-link'
         ];
      }

      // 2. Search Merchants & Submerchants (Name, ID, Email, Business ID)
      $this->db->select('id, c_name, c_email');
      $this->db->from('merchant');
      $this->db->group_start();
      $this->db->like('c_name', $query);
      $this->db->or_like('id', $query);
      $this->db->or_like('c_email', $query);
      $this->db->group_end();
      $this->db->order_by('id', 'DESC');
      $this->db->limit(5);
      $merchants = $this->db->get()->result();
      foreach ($merchants as $m) {
         $results[] = [
            'title' => $m->c_name . " (#" . $m->id . ")",
            'url' => base_url('admin/merchant?search_merchant=' . $m->id),
            'category' => 'Merchant',
            'icon' => 'fas fa-store'
         ];
      }

      // Search Submerchants (Level > 0)
      $this->db->select('m.id, m.c_name, m.c_email, m.parent_merchant_id, s.c_gvconnectBusinessId');
      $this->db->from('merchant m');
      $this->db->join('submerchant s', 's.ref_merchantId = m.id', 'left');
      $this->db->where('m.c_merchantLevel >', 0);
      $this->db->group_start();
      $this->db->like('m.c_name', $query);
      $this->db->or_like('m.id', $query);
      $this->db->or_like('s.c_gvconnectBusinessId', $query);
      $this->db->group_end();
      $this->db->order_by('m.id', 'DESC');
      $this->db->limit(5);
      $submerchants = $this->db->get()->result();
      foreach ($submerchants as $sm) {
         $results[] = [
            'title' => $sm->c_name . " (Sub of #" . $sm->parent_merchant_id . ")",
            'url' => base_url('admin/submerchant/' . $sm->parent_merchant_id . '?search_val=' . $sm->id),
            'category' => 'Sub Merchant',
            'icon' => 'fas fa-store-alt'
         ];
      }

      // 3. Search E-Wallet (Invoice, Merchant Trans ID)
      if (strlen($query) >= 4 && $this->db->table_exists('cashin_payment_ewallet')) {
         $this->db->select("cpe.id, cde.c_merchantTransactionId, cpe.c_amount, c.c_invoiceNo");
         $this->db->from('cashin_payment_ewallet cpe');
         $this->db->join('cashin c', 'c.id = cpe.ref_cashinId', 'left');
         $this->db->join('cashin_dynamic_ewallet cde', 'cde.id = cpe.ref_cashinDynamicEwalletId', 'left');
         $this->db->group_start();
         $this->db->like('cde.c_merchantTransactionId', $query);
         $this->db->or_like('c.c_invoiceNo', $query);
         $this->db->group_end();
         $this->db->order_by('cpe.id', 'DESC');
         $this->db->limit(3);
         $q_ew = $this->db->get();
         if ($q_ew) {
            foreach ($q_ew->result() as $r) {
               if (stripos($r->c_invoiceNo, $query) !== false) {
                  $url_id = $r->c_invoiceNo;
                  $url_param = 'invoice';
                  $title = $r->c_invoiceNo;
               } else {
                  $url_id = $r->c_merchantTransactionId;
                  $url_param = 'transid';
                  $title = $r->c_merchantTransactionId;
               }

               $results[] = [
                  'title' => $title . " - Rp " . number_format($r->c_amount),
                  'url' => base_url('admin/ewallet?' . $url_param . '=' . $url_id),
                  'category' => 'E-Wallet',
                  'icon' => 'fas fa-wallet'
               ];
            }
         }
      }

      // 4. Search QRIS
      if (strlen($query) >= 4 && ($this->db->table_exists('cashin_payment_qris_mpm') || $this->db->table_exists('cashin_payment_qris'))) {
         $table_qris = $this->db->table_exists('cashin_payment_qris_mpm') ? 'cashin_payment_qris_mpm' : 'cashin_payment_qris';
         $table_dyn = $table_qris . '_dynamic';
         if ($table_qris == 'cashin_payment_qris_mpm') $table_dyn = 'cashin_dynamic_qris_mpm';
         $table_rec = $table_qris . '_recurring';
         if ($table_qris == 'cashin_payment_qris_mpm') $table_rec = 'cashin_recurring_qris_mpm';

         $this->db->select("MAX(q.id) as id, MAX(c.c_invoiceNo) as c_invoiceNo, MAX(q.c_amount) as c_amount, MAX(IF(q.c_type='Dynamic', cdq.c_merchantTransactionId, crq.c_merchantTransactionId)) AS Merchant_Transaction_Id");
         $this->db->from($table_qris . ' q');
         $this->db->join('cashin c', 'c.id = q.ref_cashinId', 'left');
         
         if ($table_qris == 'cashin_payment_qris_mpm') {
            $this->db->join($table_dyn . ' cdq', 'cdq.id = q.ref_cashinDynamicQrisMpmId', 'left');
            $this->db->join($table_rec . ' crq', 'crq.id = q.ref_cashinRecurringQrisMpmId', 'left');
         } else {
            $this->db->join($table_dyn . ' cdq', 'cdq.id = q.ref_cashinDynamicQrisId', 'left');
            $this->db->join($table_rec . ' crq', 'crq.id = q.ref_cashinRecurringQrisId', 'left');
         }
         
         // RRN Pre-lookup
         $rrn_ids = [-1];
         if (is_numeric($query) && strlen($query) >= 8) {
            $tables_cb = [
               'external_paydgn_qris_mpm_callback', 'external_gvconnect_snap_qris_mpm_callback',
               'external_inacash_qris_mpm_callback', 'external_paylabs_qris_mpm_callback_payment',
               'external_quantum_qris_mpm_calback_payment'
            ];
            foreach ($tables_cb as $t_cb) {
               if (!$this->db->table_exists($t_cb)) continue;
               $col_cb = ($t_cb == 'external_quantum_qris_mpm_calback_payment') ? 'c_transactionId' : 'c_issuerRrn';
               $q_cb = $this->db->query("SELECT ref_cashinPaymentQrisMpmId FROM $t_cb WHERE $col_cb LIKE '$query%' LIMIT 5");
               if ($q_cb) {
                  foreach ($q_cb->result() as $row_cb) if ($row_cb->ref_cashinPaymentQrisMpmId) $rrn_ids[] = $row_cb->ref_cashinPaymentQrisMpmId;
               }
            }
         }

         $this->db->group_start();
         $this->db->like('c.c_invoiceNo', $query);
         $this->db->or_like('q.id', $query);
         $this->db->or_like('cdq.c_merchantTransactionId', $query);
         $this->db->or_like('crq.c_merchantTransactionId', $query);
         if (count($rrn_ids) > 1) $this->db->or_where_in('q.id', $rrn_ids);
         $this->db->group_end();
         $this->db->group_by('c.c_invoiceNo');
         $this->db->order_by('id', 'DESC');
         $this->db->limit(3);
         $q_qris = $this->db->get();
         if ($q_qris) {
            foreach ($q_qris->result() as $r) {
               if (stripos($r->c_invoiceNo, $query) !== false) {
                  $title = $r->c_invoiceNo;
                  $url_param = 'invoice=' . $r->c_invoiceNo;
               } else if (stripos($r->Merchant_Transaction_Id, $query) !== false) {
                  $title = $r->Merchant_Transaction_Id;
                  $url_param = 'transid=' . $r->Merchant_Transaction_Id;
               } else if (isset($rrn_ids) && in_array($r->id, $rrn_ids)) {
                  $title = $query; // Show the RRN that was searched
                  $url_param = 'rrn=' . $query;
               } else {
                  $title = $r->Merchant_Transaction_Id ?: $r->c_invoiceNo;
                  $url_param = 'invoice=' . $r->c_invoiceNo;
               }

               $results[] = [
                  'title' => $title . " - Rp " . number_format($r->c_amount),
                  'url' => base_url('admin/qris?' . $url_param),
                  'category' => 'QRIS Transaction',
                  'icon' => 'fas fa-qrcode'
               ];
            }
         }
      }

      // 5. Search VA
      if (strlen($query) >= 5 && $this->db->table_exists('cashin_payment_va')) {
         $this->db->select("MAX(cpv.id) as id, c.c_invoiceNo, MAX(cpv.c_vaNumber) as c_vaNumber, MAX(cpv.c_amount) as c_amount, MAX(IF(cpv.c_type='Dynamic', cdv.c_merchantTransactionId, crv.c_merchantTransactionId)) AS Merchant_Transaction_Id");
         $this->db->from('cashin_payment_va cpv');
         $this->db->join('cashin c', 'c.id = cpv.ref_cashinId', 'left');
         $this->db->join('cashin_dynamic_va cdv', 'cdv.id = cpv.ref_cashinDynamicVaId', 'left');
         $this->db->join('cashin_recurring_va crv', 'crv.id = cpv.ref_cashinRecurringVaId', 'left');
         $this->db->group_start();
         $this->db->like('cpv.c_vaNumber', $query);
         $this->db->or_like('c.c_invoiceNo', $query);
         $this->db->or_like('cdv.c_merchantTransactionId', $query);
         $this->db->or_like('crv.c_merchantTransactionId', $query);
         $this->db->group_end();
         $this->db->group_by('c.c_invoiceNo');
         $this->db->order_by('id', 'DESC');
         $this->db->limit(5);
          $q_va = $this->db->get();
          if ($q_va) {
             foreach ($q_va->result() as $r) {
                if (stripos($r->c_invoiceNo, $query) !== false) {
                   $url_id = $r->c_invoiceNo;
                   $url_param = 'invoice';
                   $title = $r->c_invoiceNo;
                } else if ($r->Merchant_Transaction_Id && stripos($r->Merchant_Transaction_Id, $query) !== false) {
                   $url_id = $r->Merchant_Transaction_Id;
                   $url_param = 'transid';
                   $title = $r->Merchant_Transaction_Id;
                } else {
                   $url_id = $r->c_vaNumber;
                   $url_param = 'va_number';
                   $title = $r->c_vaNumber;
                }
                
                $results[] = [
                   'title' => $title . " - Rp " . number_format($r->c_amount),
                   'url' => base_url('admin/virtual_account?' . $url_param . '=' . $url_id),
                   'category' => 'Virtual Account',
                   'icon' => 'fas fa-university'
                ];
             }
          }
       }

       // 5.1 Search VA Dynamic (Pending/All)
       if (strlen($query) >= 5 && $this->db->table_exists('cashin_dynamic_va')) {
          $this->db->select("id, c_merchantTransactionId, c_vaNumber, c_amount");
          $this->db->from('cashin_dynamic_va');
          $this->db->group_start();
          $this->db->like('c_vaNumber', $query);
          $this->db->or_like('c_merchantTransactionId', $query);
          $this->db->group_end();
          $this->db->order_by('id', 'DESC');
          $this->db->limit(3);
          $q_vad = $this->db->get();
          if ($q_vad) {
             foreach ($q_vad->result() as $r) {
                // Contextual logic: if search matches VA, use VA as title and param. Else use Trans ID.
                $matchValue = (stripos($r->c_vaNumber, $query) !== false) ? $r->c_vaNumber : $r->c_merchantTransactionId;
                
                $results[] = [
                   'title' => $matchValue . " - Rp " . number_format($r->c_amount),
                   'url' => base_url('admin/Va_dynamic?transid=' . $matchValue),
                   'category' => 'VA Dynamic',
                   'icon' => 'fas fa-university'
                ];
             }
          }
       }

       // 5.2 Search VA Recurring (Pending/All)
       if (strlen($query) >= 5 && $this->db->table_exists('cashin_recurring_va')) {
          $this->db->select("id, c_merchantTransactionId, c_vaNumber, c_amount");
          $this->db->from('cashin_recurring_va');
          $this->db->group_start();
          $this->db->like('c_vaNumber', $query);
          $this->db->or_like('c_merchantTransactionId', $query);
          $this->db->group_end();
          $this->db->order_by('id', 'DESC');
          $this->db->limit(3);
          $q_var = $this->db->get();
          if ($q_var) {
             foreach ($q_var->result() as $r) {
                $matchValue = (stripos($r->c_vaNumber, $query) !== false) ? $r->c_vaNumber : $r->c_merchantTransactionId;
                
                $results[] = [
                   'title' => $matchValue . " - Rp " . number_format($r->c_amount),
                   'url' => base_url('admin/VA_recurring?transid=' . $matchValue),
                   'category' => 'VA Recurring',
                   'icon' => 'fas fa-university'
                ];
             }
          }
       }

      // 6. Search BI-FAST
      if (strlen($query) >= 4 && $this->db->table_exists('cashout_payment_bifast')) {
         $this->db->select("cpb.id, cpb.c_merchantTransactionId, cpb.c_amount, cpb.c_accountNo, c.c_invoiceNo");
         $this->db->from('cashout_payment_bifast cpb');
         $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId', 'left');
         $this->db->group_start();
         $this->db->like('cpb.c_merchantTransactionId', $query);
         $this->db->or_like('c.c_invoiceNo', $query);
         $this->db->or_like('cpb.c_accountNo', $query);
         $this->db->group_end();
         $this->db->order_by('cpb.id', 'DESC');
         $this->db->limit(5);
         $q_bf = $this->db->get();
         if ($q_bf) {
            foreach ($q_bf->result() as $r) {
               if (stripos($r->c_invoiceNo, $query) !== false) {
                  $url_id = $r->c_invoiceNo;
                  $url_param = 'invoice';
                  $title = $r->c_invoiceNo;
               } else if ($r->c_merchantTransactionId && stripos($r->c_merchantTransactionId, $query) !== false) {
                  $url_id = $r->c_merchantTransactionId;
                  $url_param = 'transid';
                  $title = $r->c_merchantTransactionId;
               } else if ($r->c_accountNo && stripos($r->c_accountNo, $query) !== false) {
                  $url_id = $r->c_accountNo;
                  $url_param = 'transid';
                  $title = $r->c_accountNo;
               } else {
                  $url_id = $r->c_invoiceNo;
                  $url_param = 'invoice';
                  $title = $r->c_merchantTransactionId ?: $r->c_invoiceNo;
               }
               
               $results[] = [
                  'title' => $title . " - Rp " . number_format($r->c_amount),
                  'url' => base_url('admin/bi_fast?' . $url_param . '=' . $url_id),
                  'category' => 'BI-FAST Disbursement',
                  'icon' => 'fas fa-exchange-alt'
               ];
            }
         }
      }

      // 7. Search PPOB / Services (History)
      if (strlen($query) >= 4 && $this->db->table_exists('cashout_payment_ppob')) {
         $this->db->select("cpp.id, c.c_invoiceNo, cpp.c_amount, cpp.c_phone");
         $this->db->from('cashout_payment_ppob cpp');
         $this->db->join('cashout c', 'c.id = cpp.ref_cashoutId', 'left');
         $this->db->group_start();
         $this->db->like('c.c_invoiceNo', $query);
         $this->db->or_like('cpp.c_phone', $query);
         $this->db->group_end();
         $this->db->order_by('cpp.id', 'DESC');
         $this->db->limit(5);
         $q_ppob = $this->db->get();
         if ($q_ppob) {
            foreach ($q_ppob->result() as $r) {
               if (stripos($r->c_invoiceNo, $query) !== false) {
                  $title = $r->c_invoiceNo;
               } else if (stripos($r->c_phone, $query) !== false) {
                  $title = $r->c_phone;
               } else {
                  $title = $r->c_invoiceNo;
               }
               $results[] = [
                  'title' => $title . " - Rp " . number_format($r->c_amount),
                  'url' => base_url('admin/history?invoice=' . $r->c_invoiceNo),
                  'category' => 'Services / PPOB',
                  'icon' => 'fas fa-history'
               ];
            }
         }
      }

      // 8. Search Dynamic / Recurring Requests (Pre-Payment Logs)
      $dynamic_tables = [
         'cashin_dynamic_qris_mpm' => ['url' => 'admin/qris_dynamic', 'cat' => 'QRIS Dynamic Req'],
         'cashin_dynamic_va' => ['url' => 'admin/Va_dynamic', 'cat' => 'VA Dynamic Req'],
         'cashin_dynamic_ewallet' => ['url' => 'admin/ewallet_dynamic', 'cat' => 'E-Wallet Dynamic Req'],
         'cashin_recurring_qris_mpm' => ['url' => 'admin/qris_recurring', 'cat' => 'QRIS Recurring Req'],
         'cashin_recurring_va' => ['url' => 'admin/VA_recurring', 'cat' => 'VA Recurring Req']
      ];

      foreach ($dynamic_tables as $table => $info) {
         if (strlen($query) >= 6 && $this->db->table_exists($table)) {
            $this->db->select("id, c_merchantTransactionId, c_amount");
            $this->db->from($table);
            $this->db->like('c_merchantTransactionId', $query);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(2);
            $q_dyn = $this->db->get();
            if ($q_dyn) {
               foreach ($q_dyn->result() as $r) {
                  $results[] = [
                     'title' => $r->c_merchantTransactionId . " - Rp " . number_format($r->c_amount),
                     'url' => base_url($info['url'] . '?transid=' . $r->c_merchantTransactionId),
                     'category' => $info['cat'],
                     'icon' => 'fas fa-file-invoice'
                  ];
               }
            }
         }
      }

      echo json_encode(array_slice($results, 0, 15));
   }
}
