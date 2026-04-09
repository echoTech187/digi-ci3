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
      
      // Fetch Dashboard Data
      $data['today_stats'] = $this->Dashboard_model->get_today_stats();
      $data['monthly_overview'] = $this->Dashboard_model->get_monthly_overview();
      $data['recent_mutations'] = $this->Dashboard_model->get_recent_mutations();
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

         // ── Fetch Current Summaries ──
         $qris_summary     = $this->Qris->get_summary($date_from_qris, $date_to_qris, "");
         $disburse_summary = $this->BiFast->get_summary($date_from, $date_to, "");
         $va_summary       = $this->VirtualAccount->get_summary($date_from, $date_to, "");

         // ── Fetch Total Attempts (Success Rate Calculation) ──
         $total_cashin_attempts = $this->db->where('DATE(c_Datetime) >=', $date_from)
                                         ->where('DATE(c_Datetime) <=', $date_to)
                                         ->count_all_results('cashin');
         $total_cashout_attempts = $this->db->where('DATE(c_Datetime) >=', $date_from)
                                          ->where('DATE(c_Datetime) <=', $date_to)
                                          ->count_all_results('cashout');
         $total_attempts = ($total_cashin_attempts + $total_cashout_attempts) ?: 1; // avoid division by zero
         $total_paid = ($qris_summary[0]['qty'] + $disburse_summary[0]['qty'] + $va_summary[0]['qty']);
         $data['success_rate'] = round(($total_paid / $total_attempts) * 100, 1);

         // ── Fetch Previous Summaries ──
         $qris_summary_prev     = $this->Qris->get_summary($prev_date_from_qris, $prev_date_to_qris, "");
         $disburse_summary_prev = $this->BiFast->get_summary($prev_date_from, $prev_date_to, "");
         $va_summary_prev       = $this->VirtualAccount->get_summary($prev_date_from, $prev_date_to, "");
         
         $data['submerchants'] = $this->Merchant->get_merchant(null, null, null);
         $data['current_stats'] = [
             'qris' => $qris_summary[0],
             'disburse' => $disburse_summary[0],
             'va' => $va_summary[0]
         ];
         $data['prev_stats'] = [
             'qris' => $qris_summary_prev[0],
             'disburse' => $disburse_summary_prev[0],
             'va' => $va_summary_prev[0]
         ];
         
         // ── Legacy compatibility ──
         $data['qris_summary_last_month'] = $qris_summary; 
         $data['disburse_summary_last_month'] = $disburse_summary;
         $data['va_summary_last_month'] = $va_summary;
         
         // ── Dynamic Chart Data ──
         $data['date_range_label'] = date('d M Y', strtotime($date_from)) . ($date_from != $date_to ? ' - ' . date('d M Y', strtotime($date_to)) : '');
         $data['chart_data'] = $this->_get_period_chart_data($period, $date_from, $date_to);

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
               ->where('DATE(c_datetime)', $date_from)
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

           $query = $this->db->select('DATE(c_datetime) as day, SUM(c_amount) as amount')
               ->from('cashin_payment_qris_mpm')
               ->where('DATE(c_datetime) >=', $date_from)
               ->where('DATE(c_datetime) <=', $date_to)
               ->group_by('DATE(c_datetime)')
               ->get()->result_array();
           foreach ($query as $row) {
               if (isset($values[$row['day']])) {
                   $values[$row['day']] = (float)$row['amount'];
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
      $results = [];
      $no = 1;

      foreach ($merchants as $row1) {
         $id = $row1['id'];

         $sqlActual = "
            SELECT 
               COALESCE((SELECT SUM(c_amount) FROM cashin WHERE ref_merchantId=?), 0) -
               COALESCE((SELECT SUM(c_amount) FROM cashout WHERE ref_merchantId=?), 0) AS balanceActual
         ";
         $qActual = $this->db->query($sqlActual, [$id, $id])->row_array();
         $balanceTotalActual = round($qActual['balanceActual']);

         $sqlHold = "
            SELECT 
               COALESCE((SELECT SUM(c_amount - c_fee) FROM cashin_payment_qris_mpm WHERE ref_merchantId=? AND c_isSettlementRealtime='0'), 0) +
               COALESCE((SELECT SUM(c_amount - c_fee) FROM cashin_payment_va WHERE ref_merchantId=? AND c_isSettlementRealtime='0'), 0) +
               COALESCE((SELECT SUM(c_amount - c_fee) FROM cashin_payment_ewallet WHERE ref_merchantId=? AND c_isSettlementRealtime='0'), 0)
            AS holdActual
         ";
         $qHold = $this->db->query($sqlHold, [$id, $id, $id])->row_array();
         $balanceHoldActual = round($qHold['holdActual']);

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
