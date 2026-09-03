<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * DashboardController
 * Handles statistics, analytics, balance synchronization, and system maintenance.
 */
class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac']);
        $this->load->model(['Model_user', 'Model_menu', 'Mutation_model', 'NotificationModel']);
        is_logged_in();
    }

    public function index()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        $menus = $this->rbac->get_menus_by_role($role_id);
        $has_access = false;

        foreach ($menus as $m) {
            if ($m['url'] == 'dashboard' || $m['url'] == 'admin') {
                $has_access = true;
                break;
            }
        }
        if (!$has_access) {
            redirect('welcome');
        }

        $this->load->model(['Dashboard_model', 'Merchant']);
        $data = [
            'title'            => 'Dashboard',
            'user'             => $this->Model_user->view_user()->row_array(),
            'recent_mutations' => []
        ];
        $this->load->view('admin/index_dashboard', $data);
    }

    public function ajax_dashboard_metadata_json()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }
        session_write_close();
        $this->load->model(['Dashboard_model', 'Merchant']);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'merchant_count'     => $this->Dashboard_model->get_merchant_count(),
            'maintenance_status' => $this->Merchant->getMaintenanceStatus()
        ]));
    }

    public function ajax_dlq_health_json()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }
        session_write_close();
        $this->load->driver('cache', ['adapter' => 'file']);
        $data = $this->cache->get('dashboard_dlq_health_v1');

        if ($data === false) {
            $this->load->model('Dashboard_model');
            $data = $this->Dashboard_model->get_dlq_stats();
            $this->cache->save('dashboard_dlq_health_v1', $data, 30);
        }
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function ajax_today_stats_json()
    {
        session_write_close();
        $this->load->driver('cache', ['adapter' => 'file']);
        $cached = $this->cache->get('dashboard_today_stats_v3');
        if ($cached !== false) {
            return $this->output->set_content_type('application/json')->set_output(json_encode($cached));
        }

        $this->load->model('Dashboard_model');
        $stats = [
            'today_stats' => $this->Dashboard_model->get_today_stats(),
            'last_synced' => date('H:i:s')
        ];
        $this->cache->save('dashboard_today_stats_v3', $stats, 300);

        return $this->output->set_content_type('application/json')->set_output(json_encode($stats));
    }

    public function ajax_monthly_stats_json()
    {
        session_write_close();
        $this->load->driver('cache', ['adapter' => 'file']);
        $cached = $this->cache->get('dashboard_monthly_stats_v3');
        if ($cached !== false) {
            return $this->output->set_content_type('application/json')->set_output(json_encode($cached));
        }

        $this->load->model('Dashboard_model');
        $stats = [
            'monthly_overview' => $this->Dashboard_model->get_monthly_overview()
        ];
        $this->cache->save('dashboard_monthly_stats_v3', $stats, 300);

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
            ->editColumn('date', function ($row) {
                return date('H:i:s d/m/Y', strtotime($row->date));
            })
            ->editColumn('amount', function ($row) {
                return round($row->amount);
            })
            ->make();
    }

    public function analytics()
    {
        $data = [
            'title' => 'Analytics',
            'user'  => $this->Model_user->view_user()->row_array()
        ];

        if ($this->rbac->has_permission($this->session->userdata('role'), "no_action")) {
            $this->load->view('admin/index', $data);
            return;
        }

        $period = $this->input->get('period') ?: 'last_7_days';
        $today = new DateTime('today');

        if ($period == 'yesterday') {
            $d = (clone $today)->modify('-1 day');
            $data['date_range_label'] = $d->format('d M Y');
            $data['comparison_label'] = 'prev. day';
        } elseif ($period == 'last_month') {
            $s = (clone $today)->modify('first day of last month');
            $e = (clone $today)->modify('last day of last month');
            $data['date_range_label'] = $s->format('d M Y') . ' - ' . $e->format('d M Y');
            $data['comparison_label'] = 'prev. month';
        } else {
            $period = 'last_7_days';
            $s = (clone $today)->modify('-6 days');
            $data['date_range_label'] = $s->format('d M Y') . ' - ' . $today->format('d M Y');
            $data['comparison_label'] = 'prev. week';
        }

        $data['current_period'] = $period;
        $this->load->view('admin/index_real', $data);
    }

    public function ajax_analytics_data_json()
    {
        session_write_close();
        $period = $this->input->get('period') ?: 'last_7_days';
        $today = new DateTime('today');

        if ($period == 'yesterday') {
            $c_s = (clone $today)->modify('-1 day');
            $c_e = clone $c_s;
            $p_s = (clone $today)->modify('-2 days');
            $p_e = clone $p_s;
            $comparison_label = 'prev. day';
        } elseif ($period == 'last_month') {
            $c_s = (clone $today)->modify('first day of last month');
            $c_e = (clone $today)->modify('last day of last month');
            $p_s = (clone $c_s)->modify('-1 month');
            $p_e = (clone $c_s)->modify('-1 day');
            $comparison_label = 'prev. month';
        } else {
            $period = 'last_7_days';
            $c_s = (clone $today)->modify('-6 days');
            $c_e = clone $today;
            $p_s = (clone $c_s)->modify('-7 days');
            $p_e = (clone $c_s)->modify('-1 day');
            $comparison_label = 'prev. week';
        }

        $c_from = $c_s->format('Y-m-d') . ' 00:00:00';
        $c_to = $c_e->format('Y-m-d') . ' 23:59:59';
        $p_from = $p_s->format('Y-m-d') . ' 00:00:00';
        $p_to = $p_e->format('Y-m-d') . ' 23:59:59';

        $fetchWindow = function ($table, $field, $where = '') use ($c_from, $c_to, $p_from, $p_to) {
            $sql = "SELECT 
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_amount ELSE 0 END), 0) AS curr_amount,
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_fee ELSE 0 END), 0) AS curr_fee,
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_feeExternal ELSE 0 END), 0) AS curr_fee_external,
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_amount ELSE 0 END), 0) AS prev_amount,
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_fee ELSE 0 END), 0) AS prev_fee,
                COALESCE(SUM(CASE WHEN $field >= ? AND $field <= ? THEN c_feeExternal ELSE 0 END), 0) AS prev_fee_external
                FROM $table WHERE (($field >= ? AND $field <= ?) OR ($field >= ? AND $field <= ?)) $where";
            $params = [
                $c_from, $c_to, $c_from, $c_to, $c_from, $c_to,
                $p_from, $p_to, $p_from, $p_to, $p_from, $p_to,
                $c_from, $c_to, $p_from, $p_to
            ];
            return $this->db->query($sql, $params)->row_array() ?: [];
        };

        $qrisRow = $fetchWindow('cashin_payment_qris_mpm', 'c_datetimePayment');
        $vaRow = $fetchWindow('cashin_payment_va', 'c_datetimePayment');
        $disbRow = $fetchWindow('cashout_payment_bifast', 'c_datetime', "AND c_status = 'SUCCESS'");

        $total_disburse = $this->db->where('c_datetime >=', $c_from)->where('c_datetime <=', $c_to)->count_all_results('cashout_payment_bifast');
        $success_disburse = $this->db->where('c_datetime >=', $c_from)->where('c_datetime <=', $c_to)->where('c_status', 'SUCCESS')->count_all_results('cashout_payment_bifast');
        $success_rate = ($total_disburse > 0) ? round(($success_disburse / $total_disburse) * 100, 1) : 100;

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'current_stats'    => [
                'qris'     => ['amount' => (float) ($qrisRow['curr_amount'] ?? 0), 'fee' => (float) ($qrisRow['curr_fee'] ?? 0), 'fee_external' => (float) ($qrisRow['curr_fee_external'] ?? 0)],
                'va'       => ['amount' => (float) ($vaRow['curr_amount'] ?? 0), 'fee' => (float) ($vaRow['curr_fee'] ?? 0), 'fee_external' => (float) ($vaRow['curr_fee_external'] ?? 0)],
                'disburse' => ['amount' => (float) ($disbRow['curr_amount'] ?? 0), 'fee' => (float) ($disbRow['curr_fee'] ?? 0), 'fee_external' => (float) ($disbRow['curr_fee_external'] ?? 0)]
            ],
            'prev_stats'       => [
                'qris'     => ['amount' => (float) ($qrisRow['prev_amount'] ?? 0), 'fee' => (float) ($qrisRow['prev_fee'] ?? 0), 'fee_external' => (float) ($qrisRow['prev_fee_external'] ?? 0)],
                'va'       => ['amount' => (float) ($vaRow['prev_amount'] ?? 0), 'fee' => (float) ($vaRow['prev_fee'] ?? 0), 'fee_external' => (float) ($vaRow['prev_fee_external'] ?? 0)],
                'disburse' => ['amount' => (float) ($disbRow['prev_amount'] ?? 0), 'fee' => (float) ($disbRow['prev_fee'] ?? 0), 'fee_external' => (float) ($disbRow['prev_fee_external'] ?? 0)]
            ],
            'success_rate'     => $success_rate,
            'chart_data'       => $this->_get_period_chart_data($period, $c_s->format('Y-m-d'), $c_e->format('Y-m-d')),
            'comparison_label' => $comparison_label
        ]));
    }

    private function _get_period_chart_data($period, $date_from, $date_to)
    {
        $labels = [];
        $values = [];

        if ($period == 'yesterday') {
            $rows = $this->db->select('HOUR(c_datetime) as hour, SUM(c_amount) as amount')
                ->from('cashin_payment_qris_mpm')
                ->where('c_datetime >=', $date_from . ' 00:00:00')
                ->where('c_datetime <=', $date_from . ' 23:59:59')
                ->group_by('HOUR(c_datetime)')
                ->get()
                ->result_array();

<<<<<<< HEAD
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
         $qris_rows = $this->db->get()->result_array();
         foreach ($qris_rows as $row) {
            if (isset($values[$row['date']])) {
               $values[$row['date']] = (float)$row['amount'];
=======
            $hourMap = array_column($rows, 'amount', 'hour');
            for ($i = 0; $i < 24; $i++) {
                $labels[] = str_pad($i, 2, "0", STR_PAD_LEFT) . ":00";
                $values[] = isset($hourMap[$i]) ? (float) $hourMap[$i] : 0.0;
>>>>>>> a49b5fe1bf4e6664c99daaa483c66bfbc1e0d4f7
            }
        } else {
            $rows = $this->db->select("DATE(c_datetime) as date, SUM(c_amount) as amount")
                ->from('cashin_payment_qris_mpm')
                ->where('c_datetime >=', $date_from . ' 00:00:00')
                ->where('c_datetime <=', $date_to . ' 23:59:59')
                ->group_by("DATE(c_datetime)")
                ->get()
                ->result_array();

            $dateMap = array_column($rows, 'amount', 'date');
            $range = new DatePeriod(new DateTime($date_from), new DateInterval('P1D'), (new DateTime($date_to))->modify('+1 day'));

<<<<<<< HEAD
         // ── Notifikasi Maintenance ───────────────────────────────────
         $notif_title   = $status === 'Not Active' ? 'Maintenance Mode Aktif' : 'Maintenance Mode Dinonaktifkan';
         $notif_message = $status === 'Not Active'
             ? 'Maintenance mode diaktifkan oleh ' . $email . '. Semua transaksi merchant dinonaktifkan sementara.'
             : 'Maintenance mode dinonaktifkan oleh ' . $email . '. Transaksi merchant kembali aktif.';
         $this->NotificationModel->insert_notification(
             'maintenance',
             $notif_title,
             $notif_message,
             ['admin_email' => $email, 'status' => $status, 'action' => $action]
         );
         // ───────────────────────────────────────────────

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
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
          foreach ($raw_json as $k => $v) {
              if ($this->input->get($k) === null && $this->input->post($k) === null) {
                  $_GET[$k] = $v;
              }
          }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      $merchant_id = $this->input->get('merchant_id') ?: $this->input->post('merchant_id'); 
      $do_update = ($this->input->get('do_update') == '1' || $this->input->post('do_update') == '1');

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
         $sysBal = round($row['c_balanceTotal'] ?? 0);
         $sysHold = round($row['c_balanceHold'] ?? 0);
         $upT = false; $upH = false;

         if ($do_update) {
            $update_fields = [];
            if ($sysBal != $balA) {
               $update_fields['c_balanceTotal'] = $balA;
               $upT = true;
            }
            if ($sysHold != $holdA) {
               $update_fields['c_balanceHold'] = $holdA;
               $upH = true;
            }
            if (!empty($update_fields)) {
               $this->db->where('id', $id)->update('merchant', $update_fields);
            }
         }
         $results[] = ['no' => $no++, 'id' => $id, 'name' => $row['c_name'], 'balance_actual' => $balA, 'balance_system' => $upT ? $balA : $sysBal, 'hold_actual' => $holdA, 'hold_system' => $upH ? $holdA : $sysHold, 'updated_total' => $upT, 'updated_hold' => $upH];
      }
=======
            foreach ($range as $date) {
                $dKey = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $values[] = isset($dateMap[$dKey]) ? (float) $dateMap[$dKey] : 0.0;
            }
        }
        return ['labels' => $labels, 'values' => $values];
    }
>>>>>>> a49b5fe1bf4e6664c99daaa483c66bfbc1e0d4f7

    public function toggleOpenApiStatus()
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }

        $status = $this->input->post('status');
        if (!in_array($status, ['Not Active', 'Active'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid status']);
            return;
        }

        $this->load->model('Merchant');
        if ($status === 'Not Active') {
            $this->Merchant->setAllOpenApiStatus('Not Active');
            $this->Merchant->setMaintenanceStatus('Not Active');
            $msg = 'Maintenance ON';
        } else {
            $this->Merchant->setActiveMerchantsOpenApiStatus('Active');
            $this->Merchant->setMaintenanceStatus('Active');
            $msg = 'Maintenance OFF';
        }

        $email = $this->session->userdata('c_email') ?: 'Unknown';
        $this->db->insert('maintenance_log', [
            'username'   => $email,
            'action'     => $msg,
            'status_set' => $status,
            'timestamp'  => date('Y-m-d H:i:s')
        ]);

        $this->NotificationModel->insert_notification(
            'maintenance',
            $status === 'Not Active' ? 'Maintenance Mode Aktif' : 'Maintenance Mode Dinonaktifkan',
            'Maintenance mode diubah oleh ' . $email,
            ['admin_email' => $email, 'status' => $status]
        );

        echo json_encode(['message' => $msg]);
    }

    public function getMaintenanceStatus()
    {
        $this->load->model('Merchant');
        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => $this->Merchant->getMaintenanceStatus()
        ]));
    }

    public function syncAvailableBalanceMerchant()
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $is_api = $this->input->is_ajax_request()
            || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false
            || $this->input->get('json') == '1'
            || $this->input->method() === 'post';

        $merchant_id = $this->input->get('merchant_id') ?: $this->input->post('merchant_id');
        $do_update = ($this->input->get('do_update') == '1' || $this->input->post('do_update') == '1');

        $this->db->select('id, c_name, c_balanceTotal, c_balanceHold')->from('merchant')->where('c_status', 'Active');
        if (!empty($merchant_id)) {
            $this->db->where('id', $merchant_id);
        }
        $merchants = $this->db->get()->result_array();

        $this->db->select('m.id, (COALESCE(cin.total, 0) - COALESCE(cout.total, 0)) as balanceActual')
            ->from('merchant m')
            ->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashin GROUP BY ref_merchantId) cin', 'cin.ref_merchantId = m.id', 'left')
            ->join('(SELECT ref_merchantId, SUM(c_amount) as total FROM cashout GROUP BY ref_merchantId) cout', 'cout.ref_merchantId = m.id', 'left');

        if (!empty($merchant_id)) {
            $this->db->where('m.id', $merchant_id);
        }
        $actualBalances = array_column($this->db->where('m.c_status', 'Active')->get()->result_array(), 'balanceActual', 'id');

        $this->db->select('m.id, (COALESCE(q.total, 0) + COALESCE(v.total, 0) + COALESCE(e.total, 0)) as holdActual')
            ->from('merchant m')
            ->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_qris_mpm WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) q', 'q.ref_merchantId = m.id', 'left')
            ->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_va WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) v', 'v.ref_merchantId = m.id', 'left')
            ->join('(SELECT ref_merchantId, SUM(c_amount - c_fee) as total FROM cashin_payment_ewallet WHERE c_isSettlementRealtime=\'0\' GROUP BY ref_merchantId) e', 'e.ref_merchantId = m.id', 'left');

        if (!empty($merchant_id)) {
            $this->db->where('m.id', $merchant_id);
        }
        $actualHolds = array_column($this->db->where('m.c_status', 'Active')->get()->result_array(), 'holdActual', 'id');

        if ($do_update && !empty($merchants)) {
            $batch = [];
            foreach ($merchants as $row) {
                $id = $row['id'];
                $balA = round($actualBalances[$id] ?? 0);
                $holdA = round($actualHolds[$id] ?? 0);
                if (round($row['c_balanceTotal']) != $balA || round($row['c_balanceHold']) != $holdA) {
                    $batch[] = [
                        'id'             => $id,
                        'c_balanceTotal' => $balA,
                        'c_balanceHold'  => $holdA
                    ];
                }
            }
            if (!empty($batch)) {
                $this->db->update_batch('merchant', $batch, 'id');
            }
        }

        $results = [];
        $no = 1;
        foreach ($merchants as $row) {
            $id = $row['id'];
            $balA = round($actualBalances[$id] ?? 0);
            $holdA = round($actualHolds[$id] ?? 0);
            $upT = ($do_update && round($row['c_balanceTotal']) != $balA);
            $upH = ($do_update && round($row['c_balanceHold']) != $holdA);
            $results[] = [
                'no'             => $no++,
                'id'             => $id,
                'name'           => $row['c_name'],
                'balance_actual' => $balA,
                'balance_system' => $upT ? $balA : round($row['c_balanceTotal']),
                'hold_actual'    => $holdA,
                'hold_system'    => $upH ? $holdA : round($row['c_balanceHold']),
                'updated_total'  => $upT,
                'updated_hold'   => $upH
            ];
        }

        if ($is_api) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'do_update'    => $do_update,
                    'sync_results' => $results
                ]
            ]));
        }
        $this->load->view('admin/balance_sync_view', ['sync_results' => $results, 'do_update' => $do_update]);
    }

    public function welcome()
    {
        $role_id = $this->session->userdata('role') ?: $this->session->userdata('role_id');
        $hour = (int) date('H');
        $greeting = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');
        $data = [
            'title'    => 'Welcome',
            'user'     => $this->Model_user->view_user()->row_array(),
            'menus'    => $this->rbac->get_menus_by_role($role_id),
            'greeting' => $greeting
        ];
        $this->load->view('admin/welcome', $data);
    }
}
