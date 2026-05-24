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
      // Mengatur judul halaman yang akan ditampilkan di tab browser/header
      $data['title'] = 'Ewallet';
      // Mengambil data profile user yang sedang login untuk ditampilkan di topbar
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Mengambil parameter pencarian merchant dari GET (URL) atau POST (Filter Form)
      $search_name_ewallet = $this->input->get('merchant') ?: $this->input->post('search_name_ewallet');
      // Mengambil parameter filter tanggal mulai (Date From)
      $search_date_ewallet = $this->input->get('date_from') ?: $this->input->post('search_date_ewallet');
      // Mengambil parameter filter tanggal akhir (Date To)
      $search_date_ewallet_to = $this->input->get('date_to') ?: $this->input->post('search_date_ewallet_to');
      // Mengambil parameter filter tanggal settlement (penyelesaian transaksi)
      $search_date_ewallet_settlement = $this->input->get('settlement') ?: $this->input->post('search_date_ewallet_settlement');
      
      // Cek apakah request datang dari browser biasa (bukan AJAX)
      if (!$this->input->is_ajax_request()) {
         // Ambil data invoice dari URL atau Form POST, default ke string kosong jika tidak ada
         $search_invoice_no = $this->input->get('invoice') ?: ($this->input->post('search_invoice_no') ?: '');
         // Ambil data Transaction ID Merchant dari URL atau Form POST
         $search_transid_ewallet = $this->input->get('transid') ?: ($this->input->post('search_transid_ewallet') ?: '');

         // Jika semua filter utama (invoice & transid) kosong, bersihkan session pencarian lama
         if (!$this->input->get('invoice') && !$this->input->post('search_invoice_no') &&
             !$this->input->get('transid') && !$this->input->post('search_transid_ewallet')) {
            $this->session->unset_userdata('search_date_ewallet');
            $this->session->unset_userdata('search_date_ewallet_to');
            $this->session->unset_userdata('search_name_ewallet');
            $this->session->unset_userdata('search_date_ewallet_settlement');
            $this->session->unset_userdata('search_invoice_no');
            $this->session->unset_userdata('search_transid_ewallet');
            $this->session->unset_userdata('last_search_ewallet');
         }
      } else {
         // Jika melalui AJAX (DataTables), ambil filter invoice & transid dari session (Persistence)
         $search_invoice_no = $this->session->userdata('search_invoice_no');
         $search_transid_ewallet = $this->session->userdata('search_transid_ewallet');
      }

      // Logika persistensi filter Merchant
      if ($search_name_ewallet) {
         $this->session->set_userdata('search_name_ewallet', $search_name_ewallet);
      } else {
         $search_name_ewallet = $this->session->userdata('search_name_ewallet');
      }

      // Logika persistensi filter Tanggal Mulai
      if ($search_date_ewallet) {
         $this->session->set_userdata('search_date_ewallet', $search_date_ewallet);
      } else {
         $search_date_ewallet = $this->session->userdata('search_date_ewallet');
      }

      // Logika persistensi filter Tanggal Akhir
      if ($search_date_ewallet_to) {
         $this->session->set_userdata('search_date_ewallet_to', $search_date_ewallet_to);
      } else {
         $search_date_ewallet_to = $this->session->userdata('search_date_ewallet_to');
      }

      // Logika persistensi filter Tanggal Settlement
      if ($search_date_ewallet_settlement) {
         $this->session->set_userdata('search_date_ewallet_settlement', $search_date_ewallet_settlement);
      } else {
         $search_date_ewallet_settlement = $this->session->userdata('search_date_ewallet_settlement');
      }

      // Logika persistensi filter Nomor Invoice
      if ($search_invoice_no !== null) {
         $this->session->set_userdata('search_invoice_no', $search_invoice_no);
      } else {
         $search_invoice_no = $this->session->userdata('search_invoice_no');
      }

      // Logika persistensi filter Merchant Transaction ID
      if ($search_transid_ewallet !== null) {
         $this->session->set_userdata('search_transid_ewallet', $search_transid_ewallet);
      } else {
         $search_transid_ewallet = $this->session->userdata('search_transid_ewallet');
      }

      // Sinkronisasi parameter URL opsional (dari Redirect Global Search atau Deep Link)
      $getInvoice = $this->input->get('invoice');
      if ($getInvoice) {
         $this->session->set_userdata('search_invoice_no', $getInvoice);
         $this->session->set_userdata('last_search_ewallet', $getInvoice);
      }
      
      $getTransId = $this->input->get('transid');
      if ($getTransId) {
         $this->session->set_userdata('search_transid_ewallet', $getTransId);
         $this->session->set_userdata('last_search_ewallet', $getTransId);
      }

      // Menangani request AJAX dari library DataTables
      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_ewallet');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_ewallet');
               $this->session->unset_userdata('search_date_ewallet_to');
               $this->session->unset_userdata('search_name_ewallet');
               $this->session->unset_userdata('search_date_ewallet_settlement');
               $this->session->unset_userdata('search_invoice_no');
               $this->session->unset_userdata('search_transid_ewallet');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_invoice_no', $dtSearch);
               $this->session->set_userdata('last_search_ewallet', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_ewallet'),
               'date_from' => $this->session->userdata('search_date_ewallet'),
               'date_to' => $this->session->userdata('search_date_ewallet_to'),
               'settlement' => $this->session->userdata('search_date_ewallet_settlement'),
               'invoice' => $this->session->userdata('search_invoice_no'),
               'transid' => $this->session->userdata('search_transid_ewallet')
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

      $this->load->view('ewallet/ewallet_list', $data);
   }

   public function resetewallet()
   {
      $this->session->unset_userdata('search_date_ewallet');
      $this->session->unset_userdata('search_date_ewallet_to');
      $this->session->unset_userdata('search_name_ewallet');
      $this->session->unset_userdata('search_date_ewallet_settlement');
      $this->session->unset_userdata('search_invoice_no');
      $this->session->unset_userdata('search_transid_ewallet');
      $this->session->unset_userdata('last_search_ewallet');
      redirect('finance/e-wallet');
   }

   public function download_ewallet()
   {
      $search_date_ewallet = isset($_GET['search_date_ewallet']) ? $_GET['search_date_ewallet'] : '';
      $search_date_to_ewallet = isset($_GET['search_date_to_ewallet']) ? $_GET['search_date_to_ewallet'] : '';
      $search_name_ewallet = isset($_GET['search_name_ewallet']) ? $_GET['search_name_ewallet'] : '';
      $search_date_ewallet_settlement = isset($_GET['search_date_ewallet_settlement']) ? $_GET['search_date_ewallet_settlement'] : '';

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
      $data['title'] = 'E-Wallet Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      if (!$this->input->is_ajax_request()) {
         $search_transid_qd = $this->input->get('transid') ?: ($this->input->post('search_transid_qd') ?: '');
         if (!$this->input->get('transid') && !$this->input->post('search_transid_qd')) {
            $this->session->unset_userdata('search_date_qd');
            $this->session->unset_userdata('search_date_qd_to');
            $this->session->unset_userdata('search_name_qd');
            $this->session->unset_userdata('search_transid_qd');
            $this->session->unset_userdata('search_status_transaction_qd');
            $this->session->unset_userdata('last_search_ewd');
         }
      } else {
         $search_transid_qd = $this->session->userdata('search_transid_qd');
      }

      $search_date_qd = $this->input->post('search_date_qd');
      $search_date_qd_to = $this->input->post('search_date_qd_to');
      $search_name_qd = $this->input->get('merchant') ?: $this->input->post('search_name_qd');
      $search_status_transaction_qd = $this->input->post('search_status_transaction_qd');

      if ($search_date_qd !== null) $this->session->set_userdata('search_date_qd', $search_date_qd);
      else $search_date_qd = $this->session->userdata('search_date_qd');

      if ($search_date_qd_to !== null) $this->session->set_userdata('search_date_qd_to', $search_date_qd_to);
      else $search_date_qd_to = $this->session->userdata('search_date_qd_to');

      if ($search_name_qd !== null) $this->session->set_userdata('search_name_qd', $search_name_qd);
      else $search_name_qd = $this->session->userdata('search_name_qd');

      $this->session->set_userdata('search_transid_qd', $search_transid_qd);

      if ($search_status_transaction_qd !== null) $this->session->set_userdata('search_status_transaction_qd', $search_status_transaction_qd);
      else $search_status_transaction_qd = $this->session->userdata('search_status_transaction_qd');

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_ewd');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_qd');
               $this->session->unset_userdata('search_date_qd_to');
               $this->session->unset_userdata('search_name_qd');
               $this->session->unset_userdata('search_status_transaction_qd');
               $this->session->unset_userdata('search_transid_qd');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_transid_qd', $dtSearch);
               $this->session->set_userdata('last_search_ewd', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_qd'),
               'date' => $this->session->userdata('search_date_qd'),
               'date_to' => $this->session->userdata('search_date_qd_to'),
               'transid' => $this->session->userdata('search_transid_qd'),
               'status' => $this->session->userdata('search_status_transaction_qd')
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

   public function resetewallet_dynamic()
   {
      $this->session->unset_userdata('search_date_qd');
      $this->session->unset_userdata('search_date_qd_to');
      $this->session->unset_userdata('search_name_qd');
      $this->session->unset_userdata('search_transid_qd');
      $this->session->unset_userdata('search_status_transaction_qd');
      $this->session->unset_userdata('last_search_ewd');
      redirect('e-wallet/dynamic');
   }

   public function Sendnotifikasiewallet()
   {
      $ref_cashinPaymentEwalletId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

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
