<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk pengelolaan Merchant, Supervisor, dan Delegation.
 * Bagian dari refactoring AdminMerchant untuk mengikuti standar OOP yang lebih modular.
 */
class MerchantManagementController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Load model dasar
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('Merchant');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function merchant()
   {
      if ($this->input->is_ajax_request()) {
         try {
            $this->load->library('datatables');
            $where = ['m.c_merchantLevel' => 0];
            $ref_entity = $this->session->userdata('ref_entity');
            if (!empty($ref_entity)) {
               $where['m.ref_entity'] = $ref_entity;
            }

            $filter_status = $this->session->userdata('search_merchant_status');
            if (!empty($filter_status)) {
               $where['m.c_status'] = $filter_status;
            }

            $filter_openapi_status = $this->session->userdata('search_merchant_openapi_status');
            if (!empty($filter_openapi_status)) {
               $where['m.c_openapiStatus'] = $filter_openapi_status;
            }

            $filter_date_from = $this->session->userdata('search_merchant_date_from');
            $filter_date_to = $this->session->userdata('search_merchant_date_to');
            if (!empty($filter_date_from)) {
               $where['m.c_dateCreated >='] = $filter_date_from . ' 00:00:00';
            }
            if (!empty($filter_date_to)) {
               $where['m.c_dateCreated <='] = $filter_date_to . ' 23:59:59';
            }

            $role_id = $this->session->userdata('role');
            $hasBalancePermission = $this->rbac->has_permission($role_id, 'balance_merchant_module');
            
            $search_merchant = $this->session->userdata('search_merchant');

            session_write_close();

            return $this->Merchant->getMerchantDataTable($where, $hasBalancePermission, $search_merchant);
         } catch (Throwable $e) {
            log_message('error', 'Merchant AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => $e->getMessage()
            ]);
            return;
         }
      }

      $data['title'] = 'Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Clear session if direct access (not ajax) without parameters
      if (!$this->input->is_ajax_request() && $this->input->get('search_merchant') === null && $this->input->post('search_merchant') === null) {
         $this->session->unset_userdata('search_merchant');
      }

      $search_merchant = $this->input->get('search_merchant') ?: $this->input->post('search_merchant');
      if ($search_merchant !== null) {
         $this->session->set_userdata('search_merchant', $search_merchant);
      } else {
         $search_merchant = $this->session->userdata('search_merchant');
      }

      $search_status = $this->input->post('search_merchant_status');
      if ($search_status !== null) {
         $this->session->set_userdata('search_merchant_status', $search_status);
      } else {
         $search_status = $this->session->userdata('search_merchant_status');
      }

      $search_openapi = $this->input->post('search_merchant_openapi_status');
      if ($search_openapi !== null) {
         $this->session->set_userdata('search_merchant_openapi_status', $search_openapi);
      } else {
         $search_openapi = $this->session->userdata('search_merchant_openapi_status');
      }

      $search_date_from = $this->input->post('search_merchant_date_from');
      if ($search_date_from !== null) {
         $this->session->set_userdata('search_merchant_date_from', $search_date_from);
      } else {
         $search_date_from = $this->session->userdata('search_merchant_date_from');
      }

      $search_date_to = $this->input->post('search_merchant_date_to');
      if ($search_date_to !== null) {
         $this->session->set_userdata('search_merchant_date_to', $search_date_to);
      } else {
         $search_date_to = $this->session->userdata('search_merchant_date_to');
      }

      $data['cashin_channels'] = $this->Merchant->get_cashin_channel();
      $data['cashout_channels'] = $this->Merchant->get_cashout_channel();

      $this->db->select('SUM(c_balanceTotal) as total_balance, SUM(c_balanceHold) as total_hold, COUNT(id) as total_merchants');
      $this->db->from('merchant');
      $this->db->where('c_status', 'Active');
      $this->db->where('c_merchantLevel', 0);
      $ref_entity = $this->session->userdata('ref_entity');
      if (!empty($ref_entity)) {
         $this->db->where('ref_entity', $ref_entity);
      }
      if (!empty($search_merchant)) {
         $this->db->group_start();
         $this->db->like('c_name', $search_merchant);
         $this->db->or_like('id', $search_merchant);
         $this->db->or_like('c_email', $search_merchant);
         $this->db->group_end();
      }

      $summary = $this->db->get()->row();
      $data['total_balance'] = $summary->total_balance ?: 0;
      $data['total_hold'] = $summary->total_hold ?: 0;
      $data['total_available'] = $data['total_balance'] - $data['total_hold'];
      $data['total_merchants'] = $summary->total_merchants ?: 0;

      $this->load->view('merchant/index', $data);
   }

   public function resetMerchant()
   {
      $this->session->unset_userdata('search_merchant');
      $this->session->unset_userdata('search_merchant_status');
      $this->session->unset_userdata('search_merchant_openapi_status');
      $this->session->unset_userdata('search_merchant_date_from');
      $this->session->unset_userdata('search_merchant_date_to');
      redirect("merchant/manage");
   }

   public function addMerchant()
   {
      $data['title'] = 'Register New Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $this->load->view('merchant/add-merchant', $data);
   }

   public function registerMerchant()
   {
      $formValidationRules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_phoneNumber', 'label' => 'Merchant Phone', 'rules' => 'trim'],
         ['field' => 'c_password', 'label' => 'Merchant Password', 'rules' => 'trim|required'],
         ['field' => 'c_confirmPassword', 'label' => 'Merchant Confirm Password', 'rules' => 'trim|required|matches[c_password]'],
         ['field' => 'c_openapiIPAllow', 'label' => 'Whitelist IP', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackQrisMpm', 'label' => 'URL Callback QRIS', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackEwallet', 'label' => 'URL Callback Ewallet', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackVa', 'label' => 'URL Callback VA', 'rules' => 'trim'],
         ['field' => 'c_openapiStatus', 'label' => 'OpenAPI Status', 'rules' => 'trim'],
         ['field' => 'c_gvconnectBusinessId', 'label' => 'GVConnect Business ID', 'rules' => 'trim'],
         ['field' => 'c_gvconnectBusinessName', 'label' => 'GVConnect Business Name', 'rules' => 'trim'],
      ];

      $optionalFields = [
         'c_openapiChannelVaDynamicCreate', 'c_openapiChannelVaDynamicQuery', 'c_openapiChannelVaDynamicCancel',
         'c_openapiChannelVaRecurringCreate', 'c_openapiChannelVaRecurringCancel',
         'c_openapiChannelQrisMpmDynamicCreate', 'c_openapiChannelQrisMpmDynamicQuery', 'c_openapiChannelQrisMpmDynamicCancel',
         'c_openapiChannelQrisMpmRecurringCreate', 'c_openapiChannelQrisMpmRecurringCancel',
         'c_openapiChannelTransferToBifast', 'c_openapiChannelTransferToBifastQuery', 'c_openapiChannelTransferToBifastCancel',
         'c_openapiChannelTransferToRealtimeOnline', 'c_openapiChannelTransferToRealtimeOnlineQuery', 'c_openapiChannelTransferToRealtimeOnlineCancel',
         'c_openApiChannelTransferToEmoney', 'c_openApiChannelTransferToEmoneyQuery', 'c_openApiChannelTransferToEmoneyCancel',
         'c_allowTransferFromDashboard',
      ];

      $this->form_validation->set_rules($formValidationRules);

      if ($this->form_validation->run() == FALSE) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => validation_errors()]);
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/manage/add');
      } else {
         $this->load->library('MerchantRegistrationService', null, 'MerchantRegistrationService');
         try {
            $result = $this->MerchantRegistrationService->registerMerchant($this->input->post(), $formValidationRules, $optionalFields);
            if ($result === true) {
                 // --- INTEGRASI ONE-TIME SECRET ---
                 $postedEmail    = $this->input->post('c_email');
                 $postedPassword = $this->input->post('c_password');

                 // Ambil data merchant yang baru dibuat dari DB berdasarkan email
                 $newMerchant = $this->db
                     ->select('id, c_openapiCredentialKey')
                     ->get_where('merchant', ['c_email' => $postedEmail, 'c_merchantLevel' => 0])
                     ->row_array();

                 $merchantId     = $newMerchant ? $newMerchant['id'] : null;
                 $credentialKey  = $newMerchant ? $newMerchant['c_openapiCredentialKey'] : null;

                 // Ambil Submerchant ID dari tabel submerchant berdasarkan ref_merchantId
                 $submerchant = $this->db
                     ->select('id')
                     ->get_where('submerchant', ['ref_merchantId' => $merchantId])
                     ->row_array();
                 $submerchantId = $submerchant ? $submerchant['id'] : null;

                 // Susun isi secret: hanya 6 field yang disepakati
                 $secretContent = json_encode([
                     'Dashboard URL'     => "https://merchant.gidi.co.id",
                     'Merchant ID'       => $merchantId,
                     'Submerchant ID'    => $submerchantId,
                     'Merchant Email'    => $postedEmail,
                     'Merchant Password' => $postedPassword,
                     'Credential Key'    => $credentialKey,
                 ], JSON_PRETTY_PRINT);

                 $ch = curl_init('https://password.gidi.co.id/api/v1/share');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($ch, CURLOPT_POST, true);
                 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                     'secret' => $secretContent,
                     'ttl'    => 86400 // Kedaluwarsa 1 hari (24 jam)
                 ]));
                 curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Hindari freeze jika server down

                 $response = curl_exec($ch);
                 curl_close($ch);

                 $secretUrl = '';
                 $msg = 'Data successfully inserted';

                 if ($response) {
                     $resData = json_decode($response, true);
                     if (!empty($resData['secret_key'])) {
                         $secretUrl = 'https://password.gidi.co.id/secret/' . $resData['secret_key'];
                     }
                 }
                 // ---------------------------------

                 if ($this->input->is_ajax_request()) {
                     $this->session->set_flashdata('secret_url', $secretUrl);
                     $this->session->set_flashdata('secret_expires_at', time() + 86400);
                     echo json_encode([
                         'status'       => 'success',
                         'message'      => $msg,
                         'redirect_url' => base_url('private/secret')
                     ]);
                     return;
                 }
                 $this->session->set_flashdata('success', $msg);
                 $this->session->set_flashdata('secret_url', $secretUrl);
                 $this->session->set_flashdata('secret_expires_at', time() + 86400);
                 redirect('private/secret');
            } else {
               $code = isset($result['code']) ? $result['code'] : 0;
               $msg = 'Unable to create merchant account due to a system constraint. Please verify your input or contact technical support.';
               if ($code == 1142) {
                  $msg = 'Access Denied. You do not have sufficient database privileges to create merchant accounts.';
               } elseif ($code == 1062) {
                  $msg = 'A merchant account with this email or configuration already exists.';
               }
               if ($this->input->is_ajax_request()) {
                   echo json_encode(['status' => 'error', 'message' => $msg]);
                   return;
               }
               $this->session->set_flashdata('error', $msg);
            }
         } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                return;
            }
            $this->session->set_flashdata('error', $e->getMessage());
         }
         redirect('merchant/manage');
      }
   }

   public function editMerchant($merchant_id)
   {
      $data['title'] = 'Edit Merchant';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Merchant->get_merchant_by_id($merchant_id);

      if (empty($data['merchant'])) {
         $this->session->set_flashdata('error', 'Merchant tidak ditemukan.');
         redirect('merchant/manage');
         return;
      }

      $data['breadcrumb_replace'] = [$merchant_id => $data['merchant']['c_name']];
      $this->load->view('merchant/edit-merchant', $data);
   }

   public function detailMerchant($merchant_id)
   {
      $data['title'] = 'Merchant Detail';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchant'] = $this->Merchant->get_merchant_by_id($merchant_id);

      if (empty($data['merchant'])) {
         redirect('merchant/manage');
         return;
      }

      $data['breadcrumb_replace'] = [$merchant_id => $data['merchant']['c_name']];

      // Load models to get any additional info if needed
      $this->load->model('History');
      $this->load->model('SubMerchant');
      $this->load->model('Mutation_model');

      // Get summary stats if needed
      $data['mutation_summary'] = $this->Mutation_model->get_summary($merchant_id);
      
      $this->load->view('merchant/detail', $data);
   }

   public function detailHistoryAjax($merchant_id)
   {
      if ($this->input->is_ajax_request()) {
         try {
            $this->load->model('History');
            $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
            $end_date = $this->input->get('end_date') ?: date('Y-m-d');
            return $this->History->get_merchant_all_history_datatables_handler($merchant_id, $start_date, $end_date);
         } catch (Throwable $e) {
            log_message('error', 'Detail History AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => "Error retrieving history data: " . $e->getMessage()
            ]);
         }
      }
   }

   public function detailOverviewAjax($merchant_id)
   {
      if ($this->input->is_ajax_request()) {
         try {
            $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
            $end_date = $this->input->get('end_date') ?: date('Y-m-d');
            $merchant_id = intval($merchant_id);

            // 1. PPOB Summary
            $ppob = $this->db->query("
                SELECT 
                    COUNT(*) AS cnt, 
                    COALESCE(SUM(c_amount), 0) AS amt, 
                    0 AS fee,
                    COALESCE(SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END), 0) AS success_amt,
                    SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt
                FROM cashout_payment_ppob 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array();

            // 2. VA Summary
            $va = $this->db->query("
                SELECT 
                    COUNT(*) AS cnt, 
                    COALESCE(SUM(c_amount), 0) AS amt, 
                    COALESCE(SUM(c_fee), 0) AS fee,
                    COALESCE(SUM(c_amount), 0) AS success_amt,
                    COUNT(*) AS success_cnt
                FROM cashin_payment_va 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array();

            // 3. QRIS Summary
            $qris = $this->db->query("
                SELECT 
                    COUNT(*) AS cnt, 
                    COALESCE(SUM(c_amount), 0) AS amt, 
                    COALESCE(SUM(c_fee), 0) AS fee,
                    COALESCE(SUM(c_amount), 0) AS success_amt,
                    COUNT(*) AS success_cnt
                FROM cashin_payment_qris_mpm cpq
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array();

            // 4. E-Wallet Summary
            $ewallet = $this->db->query("
                SELECT 
                    COUNT(*) AS cnt, 
                    COALESCE(SUM(c_amount), 0) AS amt, 
                    COALESCE(SUM(c_fee), 0) AS fee,
                    COALESCE(SUM(c_amount), 0) AS success_amt,
                    COUNT(*) AS success_cnt
                FROM cashin_payment_ewallet cpe
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array();

            // 5. BI-FAST Summary
            $bifast = $this->db->query("
                SELECT 
                    COUNT(*) AS cnt, 
                    COALESCE(SUM(c_amount), 0) AS amt, 
                    COALESCE(SUM(c_fee), 0) AS fee,
                    COALESCE(SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END), 0) AS success_amt,
                     SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt
                FROM cashout_payment_bifast 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->row_array();

            // Aggregated metrics
            $summary = [
                'total_cnt' => intval($ppob['cnt'] + $va['cnt'] + $qris['cnt'] + $ewallet['cnt'] + $bifast['cnt']),
                'total_amt' => floatval($ppob['amt'] + $va['amt'] + $qris['amt'] + $ewallet['amt'] + $bifast['amt']),
                'total_fee' => floatval($ppob['fee'] + $va['fee'] + $qris['fee'] + $ewallet['fee'] + $bifast['fee']),
                'success_cnt' => intval($ppob['success_cnt'] + $va['success_cnt'] + $qris['success_cnt'] + $ewallet['success_cnt'] + $bifast['success_cnt']),
                'success_amt' => floatval($ppob['success_amt'] + $va['success_amt'] + $qris['success_amt'] + $ewallet['success_amt'] + $bifast['success_amt']),
            ];

            // Channel breakdown
            $channels = [
                'PPOB' => ['cnt' => intval($ppob['cnt']), 'amt' => floatval($ppob['amt']), 'fee' => floatval($ppob['fee']), 'success_cnt' => intval($ppob['success_cnt']), 'success_amt' => floatval($ppob['success_amt'])],
                'VA' => ['cnt' => intval($va['cnt']), 'amt' => floatval($va['amt']), 'fee' => floatval($va['fee']), 'success_cnt' => intval($va['success_cnt']), 'success_amt' => floatval($va['success_amt'])],
                'QRIS' => ['cnt' => intval($qris['cnt']), 'amt' => floatval($qris['amt']), 'fee' => floatval($qris['fee']), 'success_cnt' => intval($qris['success_cnt']), 'success_amt' => floatval($qris['success_amt'])],
                'EWallet' => ['cnt' => intval($ewallet['cnt']), 'amt' => floatval($ewallet['amt']), 'fee' => floatval($ewallet['fee']), 'success_cnt' => intval($ewallet['success_cnt']), 'success_amt' => floatval($ewallet['success_amt'])],
                'BiFast' => ['cnt' => intval($bifast['cnt']), 'amt' => floatval($bifast['amt']), 'fee' => floatval($bifast['fee']), 'success_cnt' => intval($bifast['success_cnt']), 'success_amt' => floatval($bifast['success_amt'])],
            ];

            // Trends
            $ppob_trend = $this->db->query("
                SELECT DATE(c_datetime) AS tx_date, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt 
                FROM cashout_payment_ppob 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? AND c_status = 'SUCCESS'
                GROUP BY DATE(c_datetime)
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->result_array();

            $va_trend = $this->db->query("
                SELECT DATE(c_datetime) AS tx_date, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt 
                FROM cashin_payment_va 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
                GROUP BY DATE(c_datetime)
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->result_array();

            $qris_trend = $this->db->query("
                SELECT DATE(c_datetime) AS tx_date, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt 
                FROM cashin_payment_qris_mpm 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
                GROUP BY DATE(c_datetime)
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->result_array();

            $ewallet_trend = $this->db->query("
                SELECT DATE(c_datetime) AS tx_date, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt 
                FROM cashin_payment_ewallet 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ?
                GROUP BY DATE(c_datetime)
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->result_array();

            $bifast_trend = $this->db->query("
                SELECT DATE(c_datetime) AS tx_date, COUNT(*) AS cnt, COALESCE(SUM(c_amount), 0) AS amt 
                FROM cashout_payment_bifast 
                WHERE ref_merchantId = ? AND c_datetime >= ? AND c_datetime <= ? AND c_status = 'SUCCESS'
                GROUP BY DATE(c_datetime)
            ", [$merchant_id, $start_date . ' 00:00:00', $end_date . ' 23:59:59'])->result_array();

            $dates = [];
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                (new DateTime($end_date))->modify('+1 day')
            );
            foreach ($period as $date) {
                $dates[$date->format('Y-m-d')] = [
                    'PPOB' => 0.0,
                    'VA' => 0.0,
                    'QRIS' => 0.0,
                    'EWallet' => 0.0,
                    'BiFast' => 0.0,
                    'total' => 0.0
                ];
            }

            foreach ($ppob_trend as $r) {
                if (isset($dates[$r['tx_date']])) {
                    $dates[$r['tx_date']]['PPOB'] = floatval($r['amt']);
                    $dates[$r['tx_date']]['total'] += floatval($r['amt']);
                }
            }
            foreach ($va_trend as $r) {
                if (isset($dates[$r['tx_date']])) {
                    $dates[$r['tx_date']]['VA'] = floatval($r['amt']);
                    $dates[$r['tx_date']]['total'] += floatval($r['amt']);
                }
            }
            foreach ($qris_trend as $r) {
                if (isset($dates[$r['tx_date']])) {
                    $dates[$r['tx_date']]['QRIS'] = floatval($r['amt']);
                    $dates[$r['tx_date']]['total'] += floatval($r['amt']);
                }
            }
            foreach ($ewallet_trend as $r) {
                if (isset($dates[$r['tx_date']])) {
                    $dates[$r['tx_date']]['EWallet'] = floatval($r['amt']);
                    $dates[$r['tx_date']]['total'] += floatval($r['amt']);
                }
            }
            foreach ($bifast_trend as $r) {
                if (isset($dates[$r['tx_date']])) {
                    $dates[$r['tx_date']]['BiFast'] = floatval($r['amt']);
                    $dates[$r['tx_date']]['total'] += floatval($r['amt']);
                }
            }

            $labels = array_keys($dates);
            $dataset_ppob = [];
            $dataset_va = [];
            $dataset_qris = [];
            $dataset_ewallet = [];
            $dataset_bifast = [];
            $dataset_total = [];

            foreach ($dates as $d => $val) {
                $dataset_ppob[] = $val['PPOB'];
                $dataset_va[] = $val['VA'];
                $dataset_qris[] = $val['QRIS'];
                $dataset_ewallet[] = $val['EWallet'];
                $dataset_bifast[] = $val['BiFast'];
                $dataset_total[] = $val['total'];
            }

            // 6. Sub-merchant transaction report
            $sub_merchants = $this->db->query("
                SELECT 
                    m.id AS sub_merchant_id,
                    m.c_name AS sub_merchant_name,
                    m.c_email AS sub_merchant_email,
                    COALESCE(tx.total_cnt, 0) AS total_cnt,
                    COALESCE(tx.success_cnt, 0) AS success_cnt,
                    COALESCE(tx.success_amt, 0) AS success_amt
                FROM merchant m
                LEFT JOIN (
                    SELECT 
                        ref_merchantId,
                        SUM(cnt) AS total_cnt,
                        SUM(success_cnt) AS success_cnt,
                        SUM(success_amt) AS success_amt
                    FROM (
                        SELECT ref_merchantId, COUNT(*) AS cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END) AS success_amt
                        FROM cashout_payment_ppob
                        WHERE c_datetime >= ? AND c_datetime <= ?
                        GROUP BY ref_merchantId
                        
                        UNION ALL
                        
                        SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt
                        FROM cashin_payment_va
                        WHERE c_datetime >= ? AND c_datetime <= ?
                        GROUP BY ref_merchantId
                        
                        UNION ALL
                        
                        SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt
                        FROM cashin_payment_qris_mpm
                        WHERE c_datetime >= ? AND c_datetime <= ?
                        GROUP BY ref_merchantId
                        
                        UNION ALL
                        
                        SELECT ref_merchantId, COUNT(*) AS cnt, COUNT(*) AS success_cnt, SUM(c_amount) AS success_amt
                        FROM cashin_payment_ewallet
                        WHERE c_datetime >= ? AND c_datetime <= ?
                        GROUP BY ref_merchantId
                        
                        UNION ALL
                        
                        SELECT ref_merchantId, COUNT(*) AS cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success_cnt, SUM(CASE WHEN c_status = 'SUCCESS' THEN c_amount ELSE 0 END) AS success_amt
                        FROM cashout_payment_bifast
                        WHERE c_datetime >= ? AND c_datetime <= ?
                        GROUP BY ref_merchantId
                    ) unified_tx
                    GROUP BY ref_merchantId
                ) tx ON m.id = tx.ref_merchantId
                WHERE m.parent_merchant_id = ? AND m.c_merchantLevel > 0
                ORDER BY success_amt DESC, total_cnt DESC
            ", [
                $start_date . ' 00:00:00', $end_date . ' 23:59:59',
                $start_date . ' 00:00:00', $end_date . ' 23:59:59',
                $start_date . ' 00:00:00', $end_date . ' 23:59:59',
                $start_date . ' 00:00:00', $end_date . ' 23:59:59',
                $start_date . ' 00:00:00', $end_date . ' 23:59:59',
                $merchant_id
            ])->result_array();

            // 7. Get status distribution for parent + sub-merchants
            $merchant_ids = [$merchant_id];
            $sub_merchants_ids_query = $this->db->select('id')->get_where('merchant', ['parent_merchant_id' => $merchant_id])->result_array();
            foreach ($sub_merchants_ids_query as $sm) {
                $merchant_ids[] = intval($sm['id']);
            }

            $placeholders = implode(',', array_fill(0, count($merchant_ids), '?'));
            $status_dist = $this->db->query("
                SELECT c_status, COUNT(*) AS cnt FROM (
                    SELECT LOWER(c_status) AS c_status FROM cashout_payment_ppob 
                    WHERE ref_merchantId IN ($placeholders) AND c_datetime >= ? AND c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT LOWER(c_status) AS c_status FROM cashout_payment_bifast 
                    WHERE ref_merchantId IN ($placeholders) AND c_datetime >= ? AND c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 'success' AS c_status FROM cashin_payment_va 
                    WHERE ref_merchantId IN ($placeholders) AND c_datetime >= ? AND c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 'success' AS c_status FROM cashin_payment_qris_mpm 
                    WHERE ref_merchantId IN ($placeholders) AND c_datetime >= ? AND c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 'success' AS c_status FROM cashin_payment_ewallet 
                    WHERE ref_merchantId IN ($placeholders) AND c_datetime >= ? AND c_datetime <= ?
                ) unified_status
                GROUP BY c_status
            ", array_merge(
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']
            ))->result_array();

            $statuses = [];
            foreach ($status_dist as $row) {
                $status_key = empty($row['c_status']) ? 'unknown' : $row['c_status'];
                $statuses[ucfirst($status_key)] = intval($row['cnt']);
            }

            // 8. Prepend parent merchant direct details to sub-merchants list
            $parent_details = $this->db->get_where('merchant', ['id' => $merchant_id])->row_array();
            $parent_tx = [
                'sub_merchant_id' => $merchant_id,
                'sub_merchant_name' => ($parent_details['c_name'] ?: 'Main Account') . ' (Main Account)',
                'sub_merchant_email' => $parent_details['c_email'] ?: '-',
                'total_cnt' => intval($ppob['cnt'] + $va['cnt'] + $qris['cnt'] + $ewallet['cnt'] + $bifast['cnt']),
                'success_cnt' => intval($ppob['success_cnt'] + $va['success_cnt'] + $qris['success_cnt'] + $ewallet['success_cnt'] + $bifast['success_cnt']),
                'success_amt' => floatval($ppob['success_amt'] + $va['success_amt'] + $qris['success_amt'] + $ewallet['success_amt'] + $bifast['success_amt'])
            ];
            
            array_unshift($sub_merchants, $parent_tx);

            // 9. Get latest 5 transactions for recent activity timeline
            $recent_activity = $this->db->query("
                SELECT 
                    m.c_name AS merchant_name,
                    combined.channel,
                    combined.c_datetime,
                    combined.c_amount,
                    combined.c_status,
                    combined.c_invoiceNo
                FROM (
                    SELECT 
                        cpp.ref_merchantId,
                        'PPOB' AS channel,
                        cpp.c_datetime,
                        cpp.c_amount,
                        cpp.c_status,
                        c.c_invoiceNo
                    FROM cashout_payment_ppob cpp
                    LEFT JOIN cashout c ON cpp.ref_cashoutId = c.id
                    WHERE cpp.ref_merchantId IN ($placeholders) AND cpp.c_datetime >= ? AND cpp.c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 
                        cpv.ref_merchantId,
                        'VA' AS channel,
                        cpv.c_datetime,
                        cpv.c_amount,
                        'SUCCESS' AS c_status,
                        c.c_invoiceNo
                    FROM cashin_payment_va cpv
                    LEFT JOIN cashin c ON cpv.ref_cashinId = c.id
                    WHERE cpv.ref_merchantId IN ($placeholders) AND cpv.c_datetime >= ? AND cpv.c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 
                        cpq.ref_merchantId,
                        'QRIS' AS channel,
                        cpq.c_datetime,
                        cpq.c_amount,
                        'SUCCESS' AS c_status,
                        c.c_invoiceNo
                    FROM cashin_payment_qris_mpm cpq
                    LEFT JOIN cashin c ON cpq.ref_cashinId = c.id
                    WHERE cpq.ref_merchantId IN ($placeholders) AND cpq.c_datetime >= ? AND cpq.c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 
                        cpe.ref_merchantId,
                        'EWallet' AS channel,
                        cpe.c_datetime,
                        cpe.c_amount,
                        'SUCCESS' AS c_status,
                        c.c_invoiceNo
                    FROM cashin_payment_ewallet cpe
                    LEFT JOIN cashin c ON cpe.ref_cashinId = c.id
                    WHERE cpe.ref_merchantId IN ($placeholders) AND cpe.c_datetime >= ? AND cpe.c_datetime <= ?
                    
                    UNION ALL
                    
                    SELECT 
                        cpb.ref_merchantId,
                        'BiFast' AS channel,
                        cpb.c_datetime,
                        cpb.c_amount,
                        cpb.c_status,
                        c.c_invoiceNo
                    FROM cashout_payment_bifast cpb
                    LEFT JOIN cashout c ON cpb.ref_cashoutId = c.id
                    WHERE cpb.ref_merchantId IN ($placeholders) AND cpb.c_datetime >= ? AND cpb.c_datetime <= ?
                ) combined
                LEFT JOIN merchant m ON combined.ref_merchantId = m.id
                ORDER BY combined.c_datetime DESC
                LIMIT 5
            ", array_merge(
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59'],
                $merchant_ids, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']
            ))->result_array();

            echo json_encode([
                'status' => 'success',
                'summary' => $summary,
                'channels' => $channels,
                'trend' => [
                    'labels' => $labels,
                    'datasets' => [
                        'PPOB' => $dataset_ppob,
                        'VA' => $dataset_va,
                        'QRIS' => $dataset_qris,
                        'EWallet' => $dataset_ewallet,
                        'BiFast' => $dataset_bifast,
                        'total' => $dataset_total
                    ]
                ],
                'sub_merchants' => $sub_merchants,
                'statuses' => $statuses,
                'recent_activity' => $recent_activity
            ]);
            return;
         } catch (Throwable $e) {
            log_message('error', 'Detail Overview AJAX error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            return;
         }
      }
   }

   public function detailMutationAjax($merchant_id)
   {
      if ($this->input->is_ajax_request()) {
         try {
            $this->load->model('Mutation_model');
            $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
            $end_date = $this->input->get('end_date') ?: date('Y-m-d');
            $filters = [
               'date' => $start_date,
               'date_to' => $end_date
            ];
            return $this->Mutation_model->get_datatables_handler($merchant_id, $filters);
         } catch (Throwable $e) {
            log_message('error', 'Detail Mutation AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => "Error retrieving mutation data: " . $e->getMessage()
            ]);
         }
      }
   }

   public function detailSubmerchantAjax($merchant_id)
   {
      if ($this->input->is_ajax_request()) {
         try {
            $this->load->model('SubMerchant');
            return $this->SubMerchant->get_datatables_handler($merchant_id);
         } catch (Throwable $e) {
            log_message('error', 'Detail Submerchant AJAX error: ' . $e->getMessage());
            echo json_encode([
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => [],
               "error" => "Error retrieving submerchant data: " . $e->getMessage()
            ]);
         }
      }
   }

   public function updateMerchant($merchant_id)
   {
      if (!$merchant_id) {
         $this->session->set_flashdata('error', 'Merchant ID not found.');
         redirect('merchant/manage');
      }

      $rules = [
         ['field' => 'c_name', 'label' => 'Merchant Name', 'rules' => 'trim|required'],
         ['field' => 'c_email', 'label' => 'Merchant Email', 'rules' => 'trim|required|valid_email'],
         ['field' => 'c_phoneNumber', 'label' => 'Merchant Phone', 'rules' => 'trim'],
         ['field' => 'c_openapiIPAllow', 'label' => 'Whitelist IP', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackQrisMpm', 'label' => 'URL Callback QRIS', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackEwallet', 'label' => 'URL Callback Ewallet', 'rules' => 'trim'],
         ['field' => 'c_openapiUrlCallbackVa', 'label' => 'URL Callback VA', 'rules' => 'trim'],
         ['field' => 'c_openapiStatus', 'label' => 'OpenAPI Status', 'rules' => 'required'],
         ['field' => 'c_gvconnectBusinessId', 'label' => 'GVConnect Business ID', 'rules' => 'trim'],
         ['field' => 'c_gvconnectBusinessName', 'label' => 'GVConnect Business Name', 'rules' => 'trim'],
      ];
      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == FALSE) {
         if ($this->input->is_ajax_request()) {
             echo json_encode(['status' => 'error', 'message' => validation_errors()]);
             return;
         }
         $errors = validation_errors('<li>', '</li>');
         $this->session->set_flashdata('error', '<ul>' . $errors . '</ul>');
         redirect('merchant/manage/edit/' . $merchant_id);
      } else {
         $data = [
            'c_name' => $this->input->post('c_name'),
            'c_email' => $this->input->post('c_email'),
            'c_phoneNumber' => $this->input->post('c_phoneNumber'),
            'c_openapiUrlCallbackQrisMpm' => $this->input->post('c_openapiUrlCallbackQrisMpm'),
            'c_openapiUrlCallbackVa' => $this->input->post('c_openapiUrlCallbackVa'),
            'c_openapiUrlCallbackEwallet' => $this->input->post('c_openapiUrlCallbackEwallet'),
            'c_openapiIPAllow' => $this->input->post('c_openapiIPAllow'),
            'c_openapiStatus' => $this->input->post('c_openapiStatus'),
            'c_status' => $this->input->post('c_status'),
            'c_openapiChannelVaDynamicCreate' => $this->input->post('c_openapiChannelVaDynamicCreate') ? '1' : '0',
            'c_openapiChannelVaDynamicQuery' => $this->input->post('c_openapiChannelVaDynamicQuery') ? '1' : '0',
            'c_openapiChannelVaDynamicCancel' => $this->input->post('c_openapiChannelVaDynamicCancel') ? '1' : '0',
            'c_openapiChannelVaRecurringCreate' => $this->input->post('c_openapiChannelVaRecurringCreate') ? '1' : '0',
            'c_openapiChannelVaRecurringCancel' => $this->input->post('c_openapiChannelVaRecurringCancel') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicCreate' => $this->input->post('c_openapiChannelQrisMpmDynamicCreate') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicQuery' => $this->input->post('c_openapiChannelQrisMpmDynamicQuery') ? '1' : '0',
            'c_openapiChannelQrisMpmDynamicCancel' => $this->input->post('c_openapiChannelQrisMpmDynamicCancel') ? '1' : '0',
            'c_openapiChannelEwalletDynamicCreate' => $this->input->post('c_openapiChannelEwalletDynamicCreate') ? '1' : '0',
            'c_openapiChannelEwalletDynamicQuery' => $this->input->post('c_openapiChannelEwalletDynamicQuery') ? '1' : '0',
            'c_openapiChannelEwalletDynamicCancel' => $this->input->post('c_openapiChannelEwalletDynamicCancel') ? '1' : '0',
            'c_openapiChannelTransferToBifast' => $this->input->post('c_openapiChannelTransferToBifast') ? '1' : '0',
            'c_openapiChannelTransferToRealtimeOnline' => $this->input->post('c_openapiChannelTransferToRealtimeOnline') ? '1' : '0',
            'c_allowTransferFromDashboard' => $this->input->post('c_allowTransferFromDashboard') ? '1' : '0',
         ];
         if(!empty($this->input->post('c_password'))) {
            if($this->input->post('c_password') == $this->input->post('c_confirmPassword')) {
               $data['c_password'] = password_hash($this->input->post('c_password'), PASSWORD_DEFAULT);
            } else {
               if ($this->input->is_ajax_request()) {
                   echo json_encode(['status' => 'error', 'message' => 'Password not match']);
                   return;
               }
               $this->session->set_flashdata('message', 'Password not match');
               redirect('merchant/manage/edit/' . $merchant_id);
            }
         }
         $data['c_openapiSecurityType'] = !empty($data['c_openapiIPAllow']) ? 'Whitelist IP' : 'Not Both';


         $this->db->trans_begin();
         $updMerchant = $this->Merchant->update_merchant($merchant_id, $data);
         $errMerchant = ($updMerchant !== true) ? $updMerchant : null;
         
         $gvId = $this->input->post('c_gvconnectBusinessId');
         $gvName = $this->input->post('c_gvconnectBusinessName');
         $subq = $this->db->where('ref_merchantId', $merchant_id)->get('submerchant');
         $errSub = null;
         if ($subq->num_rows() > 0) {
            $updSub = [
               'c_name' => $this->input->post('c_name'),
               'c_email' => $this->input->post('c_email'),
               'c_status' => $this->input->post('c_status'),
            ];
            if ($gvId !== null) $updSub['c_gvconnectBusinessId'] = $gvId;
            if ($gvName !== null) $updSub['c_gvconnectBusinessName'] = $gvName;
            $this->db->where('ref_merchantId', $merchant_id);
            if (!$this->db->update('submerchant', $updSub)) {
               $errSub = $this->db->error();
            }
         } else {
            $insSub = [
               'ref_merchantId' => $merchant_id,
               'c_name' => $this->input->post('c_name'),
               'c_email' => $this->input->post('c_email'),
               'c_status' => $this->input->post('c_status'),
               'c_gvconnectBusinessId' => $gvId,
               'c_gvconnectBusinessName' => $gvName ?: $this->input->post('c_name'),
            ];
            if (!$this->db->insert('submerchant', $insSub)) {
               $errSub = $this->db->error();
            }
         }

         if ($this->db->trans_status() === FALSE || $errMerchant || $errSub) {
            $err = $errMerchant ?: ($errSub ?: ['code' => 0]);
            $this->db->trans_rollback();
            $code = isset($err['code']) ? $err['code'] : 0;
            $msg = 'Unable to update merchant details due to a system constraint. Please verify your input or contact technical support.';
            if ($code == 1142) {
               $msg = 'Access Denied. You do not have sufficient database privileges to modify merchant accounts.';
            } elseif ($code == 1062) {
               $msg = 'A merchant account with this email already exists.';
            }
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $msg]);
                return;
            }
            $this->session->set_flashdata('error', $msg);
         } else {
            $this->db->trans_commit();
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Merchant successfully updated.']);
                return;
            }
            $this->session->set_flashdata('success', 'Merchant successfully updated.');
         }
         redirect('merchant/manage/edit/' . $merchant_id);
      }
   }

   public function searchMerchants()
   {
      if (ob_get_length()) ob_clean();
      $q = $this->input->get('q');
      $supervisor_id = $this->input->get('supervisor_id');

      $this->db->select('m.id, m.c_name, s.c_name as supervisor_name');
      $this->db->from('merchant m');
      $this->db->join('merchant_supervisor s', 'm.c_refSupervisor = s.id', 'left');
      
      if (!empty($supervisor_id)) {
         $this->db->where('(m.c_refSupervisor IS NULL OR m.c_refSupervisor = ' . $this->db->escape($supervisor_id) . ')', null, false);
      } else {
         $this->db->where('m.c_refSupervisor IS NULL');
      }

      if (!empty($q)) {
         $this->db->group_start();
         $this->db->like('m.c_name', $q);
         $this->db->or_like('m.id', $q);
         $this->db->group_end();
      }
      $this->db->limit(30); 
      $results = $this->db->get()->result();
      $data = [];
      foreach ($results as $row) {
         $data[] = ['id' => $row->id, 'name' => $row->c_name];
      }
      $this->output->set_content_type('application/json')->set_output(json_encode($data));
   }

   private function _internalCurl($url, $data)
   {
      $ch = curl_init();
      curl_setopt_array($ch, [
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($data),
         CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
      ]);
      $response = curl_exec($ch);
      curl_close($ch);
      return $response;
   }


   public function secretPublished()
   {
      // Ambil secretUrl dari flashdata. Jika kosong, asumsikan testing atau redirect back.
      $secretUrl = $this->session->flashdata('secret_url');
      if (!$secretUrl) {
         redirect('merchant/manage');
         return;
      }

      $data['title'] = 'Secret URL Published';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['secretUrl'] = $secretUrl;
      $data['secretExpiresAt'] = $this->session->flashdata('secret_expires_at') ?: (time() + 86400);

      // Render the new view
      $this->load->view('templates/user_header', $data);
      $this->load->view('templates/user_sidebar', $data);
      $this->load->view('templates/user_topbar', $data);
      $this->load->view('merchant/secret-published', $data);
      $this->load->view('templates/user_footer');
   }
}
