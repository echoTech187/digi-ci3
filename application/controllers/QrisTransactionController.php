<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi QRIS.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class QrisTransactionController extends CI_Controller
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
      $this->load->model('Qris');
      $this->load->model('QRISDynamic');
      $this->load->model('QRISRecurring');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function qris()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetqris(false);
      }

      $data['title'] = 'QRIS';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // 1. Sync from GET/POST to Session (Prioritize GET for deep-links)
      $field_map = [
         'search_qris_name'            => 'search_name_qris',
         'search_qris_date1'           => 'search_date_qris',
         'search_qris_date2'           => 'search_date_qris_to',
         'search_qris_date_settlement' => 'search_date_qris_settlement',
         'search_qris_invoice_no'      => 'search_invoice_no',
         'search_qris_rrn'             => 'search_rrn',
         'search_qris_transid'         => 'search_transactionid_ht',
      ];

      // Standard Fallback Map for GET aliases
      $get_fallback = [
         'search_qris_name'            => 'merchant',
         'search_qris_date1'           => 'date_from',
         'search_qris_date2'           => 'date_to',
         'search_qris_date_settlement' => 'settlement',
         'search_qris_invoice_no'      => 'invoice',
         'search_qris_rrn'             => 'rrn',
         'search_qris_transid'         => 'transid',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // SYNC: Update main search persistence if coming from deep-link/GET
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('rrn');
      if ($active_search) $this->session->set_userdata('last_dt_search_qris', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_qris');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->resetqris(false); // Silent reset
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_qris_invoice_no', $dtSearch);
               $this->session->set_userdata('last_dt_search_qris', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_qris_name'),
               'date_from' => $this->session->userdata('search_qris_date1'),
               'date_to' => $this->session->userdata('search_qris_date2'),
               'settlement' => $this->session->userdata('search_qris_date_settlement'),
               'rrn' => $this->session->userdata('search_qris_rrn'),
               'invoice' => $this->session->userdata('search_qris_invoice_no'),
               'transid' => $this->session->userdata('search_qris_transid')
            ];
            return $this->Qris->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS data: " . $e->getMessage()
            ));
         }
      }

      $data['qriss'] = [];
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['merchants'] = $this->Qris->get_merchant();

      $this->load->view('qris/list', $data);
   }

   public function resetqris($redirect = true)
   {
      $this->session->unset_userdata([
         'search_qris_name',
         'search_qris_date1',
         'search_qris_date2',
         'search_qris_date_settlement',
         'search_qris_invoice_no',
         'search_qris_rrn',
         'search_qris_transid',
         'last_dt_search_qris'
      ]);
      if ($redirect) redirect('finance/qris');
   }

   public function qris_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/qris');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail QRIS';
      $data['qris_data'] = $this->Qris->qris_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['qris_data'])) {
         $row = $data['qris_data'][0];
         $displayId = '#' . $row['c_invoiceNo'];
         
         // Fetch Callback Payment Log
         $data['external_log'] = $this->Qris->get_external_payment_log($id, $row['ref_cashinExternalId']);
         
         // Fetch Generation (Create) Log
         $data['create_log'] = null;
         if ($row['c_type'] == 'Dynamic' && $row['dynamic_create_log_id']) {
             $data['create_log'] = $this->QRISDynamic->getDataQrisDynamicChannelExternal($row['ref_cashinExternalId'], $row['dynamic_create_log_id'], $row['ref_cashinDynamicQrisMpmId']);
         } elseif ($row['c_type'] == 'Recurring' && $row['recurring_create_log_id']) {
             $data['create_log'] = $this->QRISRecurring->getDataQrisRecurringChannelExternal($row['ref_cashinExternalId'], $row['recurring_create_log_id'], $row['ref_cashinRecurringQrisMpmId']);
         }
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('qris/detail_qris', $data);
   }

   public function download_qris()
   {
      $search_date_qris = isset($_GET['search_qris_date1']) ? $_GET['search_qris_date1'] : '';
      $search_name_qris = isset($_GET['search_qris_name']) ? $_GET['search_qris_name'] : '';
      $search_date_qris_to = isset($_GET['search_qris_date2']) ? $_GET['search_qris_date2'] : '';
      $search_date_qris_settlement = isset($_GET['search_qris_date_settlement']) ? $_GET['search_qris_date_settlement'] : '';

      if (empty($search_name_qris) && (empty($search_date_qris) && empty($search_date_qris_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('finance/qris');
      }
      
      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_name_qris . '|' . $search_date_qris . '|' . $search_date_qris_settlement;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Qris',
         'c_status' => 'Pending',
         'c_filename' => '',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('finance/qris');
   }

   public function qris_dynamic_list()
   {
      if ($this->input->is_ajax_request()) {
         try {
            return $this->QRISDynamic->get_datatables_handler();
         } catch (Throwable $e) {
            log_message('error', 'QRIS Dynamic List AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS dynamic data: " . $e->getMessage()
            ));
         }
      }
   }

   public function qris_dynamic()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetqris_dynamic(false);
      }

      $data['title'] = 'QRIS Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_qrisdynamic_name'     => 'search_name_qd',
         'search_qrisdynamic_date1'    => 'search_date_qd',
         'search_qrisdynamic_date2'    => 'search_date_qd_to',
         'search_qrisdynamic_status'   => 'search_status_transaction_qd',
         'search_qrisdynamic_reff'     => 'search_reff_label',
         'search_qrisdynamic_transid'  => 'search_transid_qd',
         'search_qrisdynamic_channel'  => 'search_channel_qrisdynamic',
         'search_qrisdynamic_external' => 'search_external_qrisdynamic',
      ];

      $get_fallback = [
         'search_qrisdynamic_name'     => 'merchant',
         'search_qrisdynamic_transid'  => 'transid',
         'search_qrisdynamic_channel'  => 'channel',
         'search_qrisdynamic_external' => 'external',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_qrisdynamic', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_qrisdynamic');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->resetqris_dynamic(false); // Silent reset
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_qrisdynamic_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_qrisdynamic', $dtSearch);
            }

            $filters = [
               'merchant'         => $this->session->userdata('search_qrisdynamic_name'),
               'date'             => $this->session->userdata('search_qrisdynamic_date1'),
               'date_to'          => $this->session->userdata('search_qrisdynamic_date2'),
               'transid'          => $this->session->userdata('search_qrisdynamic_transid'),
               'status'           => $this->session->userdata('search_qrisdynamic_status'),
               'reff'             => $this->session->userdata('search_qrisdynamic_reff'),
               'channel'          => $this->session->userdata('search_qrisdynamic_channel'),
               'external_channel' => $this->session->userdata('search_qrisdynamic_external')
            ];
            return $this->QRISDynamic->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS Dynamic AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Dynamic data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->QRISDynamic->get_merchant();
      $data['search_reff_label'] = $this->session->userdata('search_qrisdynamic_reff');
      $data['internal_channels'] = $this->Qris->get_internal_channels();
      $data['external_channels'] = $this->Qris->get_external_channels();
      $this->load->view('qris/qrisdynamic', $data);
   }

   public function resetqris_dynamic($redirect = true)
   {
      $this->session->unset_userdata([
         'search_qrisdynamic_name',
         'search_qrisdynamic_date1',
         'search_qrisdynamic_date2',
         'search_qrisdynamic_status',
         'search_qrisdynamic_reff',
         'search_qrisdynamic_transid',
         'search_qrisdynamic_channel',
         'search_qrisdynamic_external',
         'last_dt_search_qrisdynamic'
      ]);
      if ($redirect) redirect('qris/dynamic');
   }

   public function qris_recurring()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetqris_recurring(false);
      }

      $data['title'] = 'QRIS Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_qrisrecurring_name'        => 'search_name_qr',
         'search_qrisrecurring_date1'       => 'search_date_qr',
         'search_qrisrecurring_date2'       => 'search_date_qr_to',
         'search_qrisrecurring_submerchant' => 'search_submerchant_qr',
         'search_qrisrecurring_status'      => 'search_status_transaction_qr',
         'search_qrisrecurring_transid'     => 'search_transid_qr',
         'search_qrisrecurring_channel'     => 'search_channel_qrisrecurring',
         'search_qrisrecurring_external'    => 'search_external_qrisrecurring',
      ];

      $get_fallback = [
         'search_qrisrecurring_name'        => 'merchant',
         'search_qrisrecurring_transid'     => 'transid',
         'search_qrisrecurring_channel'     => 'channel',
         'search_qrisrecurring_external'    => 'external',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_qrisrecurring', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_qrisrecurring');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->resetqris_recurring(false); // Silent reset
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_qrisrecurring_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_qrisrecurring', $dtSearch);
            }

            $filters = [
               'merchant'         => $this->session->userdata('search_qrisrecurring_name'),
               'date'             => $this->session->userdata('search_qrisrecurring_date1'),
               'date_to'          => $this->session->userdata('search_qrisrecurring_date2'),
               'transid'          => $this->session->userdata('search_qrisrecurring_transid'),
               'submerchant'      => $this->session->userdata('search_qrisrecurring_submerchant'),
               'status'           => $this->session->userdata('search_qrisrecurring_status'),
               'channel'          => $this->session->userdata('search_qrisrecurring_channel'),
               'external_channel' => $this->session->userdata('search_qrisrecurring_external')
            ];
            return $this->QRISRecurring->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS Recurring AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Recurring data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->QRISRecurring->get_merchant();
      $data['internal_channels'] = $this->Qris->get_internal_channels();
      $data['external_channels'] = $this->Qris->get_external_channels();
      $this->load->view('qris/qrisrecurring', $data);
   }

   public function resetqris_recurring($redirect = true)
   {
      $this->session->unset_userdata([
         'search_qrisrecurring_name',
         'search_qrisrecurring_date1',
         'search_qrisrecurring_date2',
         'search_qrisrecurring_submerchant',
         'search_qrisrecurring_status',
         'search_qrisrecurring_transid',
         'search_qrisrecurring_channel',
         'search_qrisrecurring_external',
         'last_dt_search_qrisrecurring'
      ]);
      if ($redirect) redirect('qris/recurring');
   }

   public function SendnotifikasiQRIS()
   {
      $ref_cashinPaymentQrisMpmId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentQrisMpmId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/qris');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_qris_mpm",
         "msgInfo" => array(
            "ref_cashinPaymentQrisMpmId" => $ref_cashinPaymentQrisMpmId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = $this->internalUrlHit . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      $this->session->set_flashdata('success', 'Notification has resend');
      redirect('finance/qris');
   }

   public function getDetailQrisDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
      echo json_encode($detailData);
   }

   public function getDetailQrisRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISRecurring->getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
      echo json_encode($detailData);
   }
}
