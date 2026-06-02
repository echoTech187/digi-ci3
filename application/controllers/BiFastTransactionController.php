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
         'search_bifast_status'             => 'search_status_transaction_bifast',
         'search_bifast_transid'            => 'search_transid_bifast',
         'search_bifast_external_reff'      => 'search_external_reff_id',
         'search_bifast_channel'            => 'search_channel_bifast',
         'search_bifast_internal_channel'   => 'search_internal_channel_bifast',
      ];

      $get_fallback = [
         'search_bifast_name'               => 'merchant',
         'search_bifast_date1'              => 'date_from',
         'search_bifast_date2'              => 'date_to',
         'search_bifast_status'             => 'status',
         'search_bifast_transid'            => 'transid',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Check external reff validation
      $search_external_reff_id = $this->session->userdata('search_bifast_external_reff');
      $search_channel_bifast = $this->session->userdata('search_bifast_channel');
      if (!empty($search_external_reff_id) && (empty($search_channel_bifast) || $search_channel_bifast === '' || $search_channel_bifast === null)) {
         $this->session->set_flashdata('error', 'Silakan pilih "External Channel" terlebih dahulu sebelum memasukan "External Reff ID"');
         redirect('finance/bi-fast');
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid');
      if ($active_search) {
         $this->session->set_userdata('last_dt_search_bifast', $active_search);
         $this->session->set_userdata('search_bifast_transid', $active_search);
      }

      if ($this->input->is_ajax_request()) {
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

            $filters = [
               'merchant' => $this->session->userdata('search_bifast_name'),
               'date_from' => $this->session->userdata('search_bifast_date1'),
               'date_to' => $this->session->userdata('search_bifast_date2'),
               'transid' => $this->session->userdata('search_bifast_transid'),
               'external_reff' => $this->session->userdata('search_bifast_external_reff'),
               'channel' => $this->session->userdata('search_bifast_channel'),
               'internal_channel' => $this->session->userdata('search_bifast_internal_channel'),
               'status' => $this->session->userdata('search_bifast_status')
            ];
            return $this->BiFast->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'BI-FAST AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Gagal mengambil data BI-FAST: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->BiFast->get_merchant();
      $data['channels'] = $this->BiFast->get_channels();
      $data['internal_channels'] = $this->BiFast->get_internal_channels();
      $data['search_status_transaction_bifast'] = $this->session->userdata('search_bifast_status') ?: '';

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
         'search_bifast_status',
         'search_bifast_external_reff',
         'search_bifast_channel',
         'search_bifast_internal_channel',
         'last_dt_search_bifast'
      ]);
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

      $this->load->view('bifast/detail', $data);
   }

   /**
    * Mendaftarkan permintaan unduhan laporan BI FAST ke dalam antrean (admin_download).
    * Proses ini dilakukan secara asinkron untuk menjaga performa aplikasi saat data yang diunduh berjumlah besar.
    */
   public function download_bi_fast()
   {
      $search_date_bifast = isset($_GET['search_bifast_date1']) ? $_GET['search_bifast_date1'] : '';
      $search_name_bifast = isset($_GET['search_bifast_name']) ? $_GET['search_bifast_name'] : '';

      // Validasi: Memastikan setidaknya ada satu filter utama untuk membatasi cakupan data laporan.
      if (empty($search_date_bifast) && empty($search_name_bifast)) {
         $this->session->set_flashdata('error_message', 'Harap isi filter tanggal atau merchant sebelum mengunduh laporan.');
         redirect('finance/bi-fast');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      // Menggabungkan filter menjadi string pipe-separated untuk disimpan sebagai metadata unduhan.
      $additionalFilter = $search_date_bifast . '|' . $search_name_bifast;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'BI Fast',
      );

      // Memasukkan permintaan ke tabel antrean. Task background (Cron) akan memproses ini menjadi file CSV/XLS.
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
      // Validasi sesi tambahan untuk endpoint API publik internal.
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashoutExternalId = $this->input->post('ref_cashoutExternalId');
      $ref_cashoutExternalLogBifastId = $this->input->post('ref_cashoutExternalLogBifastId');

      if (empty($ref_cashoutExternalId)) {
         echo json_encode(['error' => 'Data yang dikirimkan tidak valid']);
         return;
      }

      // Melakukan sinkronisasi data dengan gateway eksternal melalui model.
      $detailData = $this->BiFast->getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogBifastId);
      echo json_encode($detailData);
   }
}
