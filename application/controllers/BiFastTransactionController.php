<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * BiFastTransactionController
 * 
 * Controller ini dikhususkan untuk mengelola transaksi BI FAST (Disbursement/Pencairan Dana).
 * Sebagai bagian dari upaya refaktorisasi TransactionController yang monolitik, controller ini
 * menerapkan prinsip Single Responsibility guna meningkatkan skalabilitas dan kemudahan pemeliharaan kode.
 */
class BiFastTransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      
      // Inisialisasi library inti untuk manajemen sesi, kontrol akses (RBAC), 
      // paginasi data, dan validasi input pengguna.
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Memuat model yang berkaitan dengan data pengguna, mutasi rekening, 
      // konfigurasi channel, dan logika spesifik BI FAST.
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('BiFast');
      
      // Proteksi layer controller: Memastikan hanya pengguna yang terautentikasi yang dapat mengakses.
      is_logged_in();
      
      // Sinkronisasi variabel URL global (Internal/External) untuk kebutuhan hit API gateway.
      // Penggunaan 'global' di sini untuk menjaga kompatibilitas dengan konfigurasi sistem lama.
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   /**
    * Menampilkan halaman daftar transaksi BI FAST dan menangani pencarian berbasis server-side (AJAX).
    */
   public function bi_fast()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetbi_fast(false);
      }

      $data['title'] = 'Disbursement';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_bifast_name'               => 'search_name_bifast',
         'search_bifast_date1'              => 'search_date_bifast',
         'search_bifast_date2'              => 'search_date_bifast_to',
         'search_bifast_transid'            => 'search_transid_bifast',
         'search_bifast_external_reff'      => 'search_external_reff_id',
         'search_bifast_channel'            => 'search_channel_bifast',
         'search_bifast_internal_channel'   => 'search_internal_channel_bifast',
         'search_bifast_status'             => 'search_status_bifast',
      ];

      $get_fallback = [
         'search_bifast_name'               => 'merchant',
         'search_bifast_date1'              => 'date_from',
         'search_bifast_date2'              => 'date_to',
         'search_bifast_transid'            => 'transid',
         'search_bifast_status'             => 'status'
      ];

      // Alias parameters support for API / Swagger
      $merchant_post = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('merchant_id') ?: $this->input->get('merchant')));
      if ($merchant_post !== NULL) $_POST['search_name_bifast'] = $merchant_post;

      $date_from_post = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->get('date_from'));
      if ($date_from_post !== NULL) $_POST['search_date_bifast'] = $date_from_post;

      $date_to_post = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->get('date_to'));
      if ($date_to_post !== NULL) $_POST['search_date_bifast_to'] = $date_to_post;

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos($accept, 'json') !== false
         || strtolower($this->input->method()) === 'post'
         || $is_swagger;

      // Check external reff validation (only for web redirects)
      $search_external_reff_id = $this->session->userdata('search_bifast_external_reff');
      $search_channel_bifast = $this->session->userdata('search_bifast_channel');
      if (!$is_api && !empty($search_external_reff_id) && (empty($search_channel_bifast) || $search_channel_bifast === '' || $search_channel_bifast === null)) {
         $this->session->set_flashdata('error', 'Silakan pilih "External Channel" terlebih dahulu sebelum memasukan "External Reff ID"');
         redirect('finance/bi-fast');
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
      if ($active_search) {
         $this->session->set_userdata('last_dt_search_bifast', $active_search);
         $this->session->set_userdata('search_bifast_transid', $active_search);
      }

      if ($is_api) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_bifast');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_bifast', 'search_bifast_transid', 'search_bifast_invoice_no']);
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_bifast_transid', $dtSearch);
               $this->session->set_userdata('last_dt_search_bifast', $dtSearch);
            }

            $merchant_val = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: $this->input->post('search_name_bifast'));
            $date_from_val = $this->input->post('date_from') ?: ($this->input->post('date1') ?: $this->input->post('search_date_bifast'));
            $date_to_val   = $this->input->post('date_to') ?: ($this->input->post('date2') ?: $this->input->post('search_date_bifast_to'));

            if ($date_to_val && strlen(trim($date_to_val)) === 10) {
               $date_to_val = trim($date_to_val) . ' 23:59:59';
            }

            $filters = [
               'merchant' => $merchant_val ?: null,
               'date_from' => $date_from_val ?: null,
               'date_to' => $date_to_val ?: null,
               'transid' => $this->input->post('transid') ?: ($this->input->post('search_transid_bifast') ?: null),
               'external_reff' => $this->input->post('external_reff') ?: ($this->input->post('search_external_reff_id') ?: null),
               'channel' => $this->input->post('channel') ?: ($this->input->post('search_channel_bifast') ?: null),
               'search_status' => $this->input->post('status') ?: ($this->input->post('search_status_bifast') ?: null),
               'internal_channel' => $this->input->post('internal_channel') ?: ($this->input->post('search_internal_channel_bifast') ?: null)
            ];
            $out = $this->BiFast->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'BI-FAST AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Gagal mengambil data BI-FAST: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['merchants'] = $this->BiFast->get_merchant();
      $data['channels'] = $this->BiFast->get_channels();
      $data['internal_channels'] = $this->BiFast->get_internal_channels();
      $data['channel_mappings'] = $this->BiFast->get_channel_mappings();

      $this->load->view('bifast/list', $data);
   }

   /**
    * Menghapus semua parameter pencarian dari session dan mengarahkan kembali ke halaman utama.
    * Digunakan untuk fitur "Reset Filter" agar pengguna dapat memulai pencarian baru dengan bersih.
    */
   public function resetbi_fast($redirect = true)
   {
      $this->session->unset_userdata([
         'search_bifast_date1',
         'search_bifast_date2',
         'search_bifast_name',
         'search_bifast_transid',
         'search_bifast_external_reff',
         'search_bifast_channel',
         'search_bifast_status',
         'search_bifast_internal_channel',
         'last_dt_search_bifast'
      ]);

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'BI-FAST search filters reset successfully.'
         ]));
         return;
      }

      if ($redirect) redirect('finance/bi-fast');
   }

   /**
    * Menampilkan informasi detail dari satu transaksi BI FAST tertentu.
    * 
    * @param string $id ID Transaksi atau Segment URI ke-3.
    */
   public function bi_fast_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/bi-fast');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail BI Fast';
      $data['bifast_data'] = $this->BiFast->getBifastDetail($id);

      // Logika breadcrumb: Mengganti ID transaksi yang kurang bermakna (raw ID) 
      // menjadi nomor invoice yang lebih ramah bagi pengguna di bar navigasi.
      $displayId = '#' . $id;
      if (!empty($data['bifast_data'])) {
         $displayId = '#' . $data['bifast_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      if ($this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode([
                  'status' => true,
                  'message' => 'BI-FAST detail data retrieved successfully',
                  'data' => [
                      'bifast_data' => $data['bifast_data']
                  ]
              ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
          return;
      }

      $this->load->view('bifast/detail', $data);
   }

   /**
    * Mendaftarkan permintaan unduhan laporan BI FAST ke dalam antrean (admin_download).
    * Proses ini dilakukan secara asinkron untuk menjaga performa aplikasi saat data yang diunduh berjumlah besar.
    */
   public function download_bi_fast()
   {
      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->get($k) === NULL && $this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $referer = strtolower($this->input->get_request_header('Referer') ?: '');
      $is_swagger = (strpos($referer, 'swagger') !== false) || (strpos($this->uri->uri_string(), 'swagger') !== false);
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || strtolower($this->input->method()) === 'post' || $is_swagger;

      $search_date_bifast = $this->input->post('date_from') ?: ($this->input->post('date1') ?: ($this->input->get('search_bifast_date1') ?: $this->session->userdata('search_bifast_date1')));
      $search_name_bifast = $this->input->post('merchant_id') ?: ($this->input->post('merchant') ?: ($this->input->get('search_bifast_name') ?: $this->session->userdata('search_bifast_name')));

      if ($is_api_request) {
         $user = $this->Model_user->view_user()->row_array();
         $adminID = $user['id'] ?? 1;

         $additionalFilter = $search_date_bifast . '|' . $search_name_bifast;
         $data = array(
            'ref_adminId' => $adminID,
            'c_datetime' => date('Y-m-d H:i:s'),
            'c_additionalFilter' => $additionalFilter,
            'c_type' => 'BI Fast',
         );

         if ($this->db->insert('admin_download', $data)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => true,
               'message' => 'Your BI-FAST download request has been submitted successfully. Please check the Download Report menu to download the generated file.',
               'data' => [
                  'download_id' => $this->db->insert_id(),
                  'type' => 'BI Fast',
                  'filter' => $additionalFilter
               ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         } else {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Failed to submit BI-FAST download request.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
         }
      }

      if (empty($search_date_bifast) && empty($search_name_bifast)) {
         $this->session->set_flashdata('error_message', 'Harap isi filter tanggal atau merchant sebelum mengunduh laporan.');
         redirect('finance/bi-fast');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_date_bifast . '|' . $search_name_bifast;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'BI Fast',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Permintaan Anda sedang diproses. Silakan cek menu "Download Report".');
      } else {
         $this->session->set_flashdata('error', 'Gagal mendaftarkan permintaan unduhan.');
      }

      redirect('finance/bi-fast');
   }

   /**
    * Mengambil detail log transaksi dari channel eksternal (third-party gateway).
    * Digunakan untuk audit silang antara data di sistem internal dengan data asli dari provider.
    */
   public function getDetailBiFastChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      header('Content-Type: application/json');
      $ref_cashoutExternalId = $this->input->post('ref_cashoutExternalId') ?: 'paylabs';
      $ref_cashoutExternalLogBifastId = $this->input->post('ref_cashoutExternalLogBifastId') ?: 1;

      $detailData = $this->BiFast->getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogBifastId);
      echo json_encode($detailData ?: []);
   }

   public function SendnotifikasiBifast($ref_bifastId = NULL, $refMerchantId = NULL)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$ref_bifastId) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Transaction ID not found.']));
            return;
         }
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/bi-fast');
         return;
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_bifast",
         "msgInfo" => array(
            "ref_bifastId" => $ref_bifastId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrl = (property_exists($this, 'internalUrlHit') ? $this->internalUrlHit : 'http://127.0.0.1/gatewayservice') . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrl,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_CONNECTTIMEOUT => 5,
         CURLOPT_TIMEOUT => 15,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
      ));

      $curlRes = curl_exec($internalCurl);
      if (curl_errno($internalCurl)) {
         log_message('error', 'BI-FAST Resend Notif cURL Error: ' . curl_error($internalCurl));
      }
      curl_close($internalCurl);

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'BI-FAST notification resend queue request submitted successfully.',
            'data' => ['id' => $ref_bifastId, 'merchantId' => $refMerchantId]
         ]));
         return;
      }

      $this->session->set_flashdata('success', 'Notification has resend');
      redirect('finance/bi-fast');
   }
}
