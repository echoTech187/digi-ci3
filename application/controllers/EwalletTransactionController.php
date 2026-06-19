<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi E-Wallet.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class EwalletTransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar yang dibutuhkan
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Load model dasar
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('Ewallet');
      $this->load->model('EwalletDynamic');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit (Internal/External)
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   /**
    * Menampilkan halaman daftar transaksi E-Wallet dan menangani request DataTables.
    */
   public function ewallet()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetewallet(false);
      }

      $data['title'] = 'Ewallet';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_ewallet_name'            => 'search_name_ewallet',
         'search_ewallet_date1'           => 'search_date_ewallet',
         'search_ewallet_date2'           => 'search_date_ewallet_to',
         'search_ewallet_date_settlement' => 'search_date_ewallet_settlement',
         'search_ewallet_invoice_no'      => 'search_invoice_no',
         'search_ewallet_transid'         => 'search_transid_ewallet',
         'search_ewallet_channel'         => 'search_channel_ewallet',
      ];

      $get_fallback = [
         'search_ewallet_name'            => 'merchant',
         'search_ewallet_date1'           => 'date_from',
         'search_ewallet_date2'           => 'date_to',
         'search_ewallet_date_settlement' => 'settlement',
         'search_ewallet_invoice_no'      => 'invoice',
         'search_ewallet_transid'         => 'transid',
         'search_ewallet_channel'         => 'channel',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_ewallet', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_ewallet');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_ewallet', 'search_ewallet_invoice_no', 'search_ewallet_transid']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_ewallet_invoice_no', $dtSearch);
               $this->session->set_userdata('last_dt_search_ewallet', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_ewallet_name'),
               'date_from' => $this->session->userdata('search_ewallet_date1'),
               'date_to' => $this->session->userdata('search_ewallet_date2'),
               'settlement' => $this->session->userdata('search_ewallet_date_settlement'),
               'invoice' => $this->session->userdata('search_ewallet_invoice_no'),
               'transid' => $this->session->userdata('search_ewallet_transid'),
               'channel' => $this->session->userdata('search_ewallet_channel')
            ];
            return $this->Ewallet->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'E-Wallet AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving E-Wallet data: " . $e->getMessage()
            ));
         }
      }    
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['ewallets'] = [];
      $data['merchants'] = $this->Ewallet->get_merchant();
      $data['internal_channels'] = $this->Ewallet->get_internal_channels();

      $this->load->view('ewallet/ewallet_list', $data);
   }

   public function resetewallet($redirect = true)
   {
      $this->session->unset_userdata([
         'search_ewallet_name',
         'search_ewallet_date1',
         'search_ewallet_date2',
         'search_ewallet_date_settlement',
         'search_ewallet_invoice_no',
         'search_ewallet_transid',
         'search_ewallet_channel',
         'last_dt_search_ewallet'
      ]);
      if ($redirect) redirect('finance/e-wallet');
   }

   public function download_ewallet()
   {
      $search_date_ewallet = isset($_GET['search_ewallet_date1']) ? $_GET['search_ewallet_date1'] : '';
      $search_date_to_ewallet = isset($_GET['search_ewallet_date2']) ? $_GET['search_ewallet_date2'] : '';
      $search_name_ewallet = isset($_GET['search_ewallet_name']) ? $_GET['search_ewallet_name'] : '';
      $search_date_ewallet_settlement = isset($_GET['search_ewallet_date_settlement']) ? $_GET['search_ewallet_date_settlement'] : '';

      if (
         empty($search_name_ewallet) &&
         empty($search_date_ewallet) &&
         empty($search_date_to_ewallet) &&
         empty($search_date_ewallet_settlement)
      ) {
         redirect('finance/e-wallet');
      }

      $additionalFilter = $search_name_ewallet . '|' . $search_date_ewallet . '|' . $search_date_to_ewallet . '|' . $search_date_ewallet_settlement;
      $downloadData = [
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'ewallet',
         'c_createBy' => $this->session->userdata('c_email')
      ];

      if ($this->Ewallet->requestDownload($downloadData)) {
         $this->session->set_flashdata('success', 'Request download has been sent, please check in report menu');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('finance/e-wallet');
   }

   public function ewallet_dynamic()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetewallet_dynamic(false);
      }

      $data['title'] = 'E-Wallet Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_ewalletdynamic_name'     => 'search_name_qd',
         'search_ewalletdynamic_date1'    => 'search_date_qd',
         'search_ewalletdynamic_date2'    => 'search_date_qd_to',
         'search_ewalletdynamic_status'   => 'search_status_transaction_qd',
         'search_ewalletdynamic_transid'  => 'search_transid_qd',
         'search_ewalletdynamic_channel'  => 'search_channel_ewalletdynamic',
         'search_ewalletdynamic_external' => 'search_external_ewalletdynamic',
      ];

      $get_fallback = [
         'search_ewalletdynamic_name'     => 'merchant',
         'search_ewalletdynamic_transid'  => 'transid',
         'search_ewalletdynamic_channel'  => 'channel',
         'search_ewalletdynamic_external' => 'external',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid');
      if ($active_search) $this->session->set_userdata('last_dt_search_ewalletdynamic', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_ewalletdynamic');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_ewalletdynamic', 'search_ewalletdynamic_transid']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_ewalletdynamic_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_ewalletdynamic', $dtSearch);
            }

            $filters = [
               'merchant'         => $this->session->userdata('search_ewalletdynamic_name'),
               'date'             => $this->session->userdata('search_ewalletdynamic_date1'),
               'date_to'          => $this->session->userdata('search_ewalletdynamic_date2'),
               'transid'          => $this->session->userdata('search_ewalletdynamic_transid'),
               'status'           => $this->session->userdata('search_ewalletdynamic_status'),
               'channel'          => $this->session->userdata('search_ewalletdynamic_channel'),
               'external_channel' => $this->session->userdata('search_ewalletdynamic_external')
            ];
            return $this->EwalletDynamic->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'E-Wallet Dynamic AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving E-Wallet Dynamic data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->EwalletDynamic->get_merchant();
      $data['internal_channels'] = $this->Ewallet->get_internal_channels();
      $data['external_channels'] = $this->Ewallet->get_external_channels();
      $this->load->view('ewallet/ewallet_dynamic', $data);
   }

   public function ewallet_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/e-wallet');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail Ewallet';
      $data['saldo'] = $this->Model_user->saldo();
      $data['ewallet_data'] = $this->Ewallet->ewallet_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['ewallet_data'])) {
         $displayId = '#' . $data['ewallet_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('ewallet/ewallet_detail', $data);
   }

   public function resetewallet_dynamic($redirect = true)
   {
      $this->session->unset_userdata([
         'search_ewalletdynamic_name',
         'search_ewalletdynamic_date1',
         'search_ewalletdynamic_date2',
         'search_ewalletdynamic_status',
         'search_ewalletdynamic_transid',
         'search_ewalletdynamic_channel',
         'search_ewalletdynamic_external',
         'last_dt_search_ewalletdynamic'
      ]);
      if ($redirect) redirect('e-wallet/dynamic');
   }

   public function Sendnotifikasiewallet($ref_cashinPaymentEwalletId = NULL, $refMerchantId = NULL)
   {
      if (!$ref_cashinPaymentEwalletId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/e-wallet');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_ewallet",
         "msgInfo" => array(
            "ref_cashinPaymentEwalletId" => $ref_cashinPaymentEwalletId,
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
      redirect('finance/e-wallet');
   }

   public function getDetailEwalletDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $ref_cashinExternalLogEwalletIdCreate = $this->input->post('ref_cashinExternalLogEwalletIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->EwalletDynamic->getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate);
      echo json_encode($detailData);
   }

   public function getDetailEwalletChannelExternal()
   {
      // WARNING: This method in original TransactionController was loading QRISDynamic
      // We keep it for compatibility but label it correctly if needed.
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $this->load->model('QRISDynamic');

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
}
