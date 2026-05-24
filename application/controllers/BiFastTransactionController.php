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
      $data['title'] = 'Disbursement';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Logika Persistensi Pencarian:
      // Kami menggunakan Session untuk menyimpan parameter pencarian agar pengguna tidak kehilangan
      // konteks saat melakukan navigasi halaman atau melakukan refresh.
      // [1] GLOBAL SEARCH / SEARCH UTAMA (Invoice & Transaction ID):
      // Filter ini adalah entry point utama untuk mencari transaksi spesifik secara cepat. 
      // Sumber data bisa berasal dari:
      // a. Top Bar Search: Input teks di navigasi atas yang mengirimkan POST 'search_transid_bifast'.
      // b. Deep Link / URL: Parameter GET 'transid' atau 'invoice' (misalnya dari klik notifikasi atau tautan email).
      //
      // Strategi: Jika diakses bukan via AJAX (halaman baru/refresh), kita cek parameter input.
      // Jika input kosong sama sekali, sistem mengasumsikan pengguna ingin "membersihkan" konteks pencarian ID spesifik.
      if (!$this->input->is_ajax_request()) {
         // Prioritas resolusi: GET 'transid' -> GET 'invoice' -> POST 'search_transid_bifast'.
         $search_transid_bifast = $this->input->get('transid') ?: ($this->input->get('invoice') ?: ($this->input->post('search_transid_bifast') ?: ''));
         
         if (!$this->input->get('transid') && !$this->input->get('invoice') && !$this->input->post('search_transid_bifast')) {
            $this->session->unset_userdata('last_search_bifast');
         }
      } else {
         // Pada request AJAX (DataTables redraw), kita menarik nilai dari session.
         // Hal ini krusial agar saat tabel melakukan paging atau sorting, filter pencarian awal tidak hilang.
         $search_transid_bifast = $this->session->userdata('search_transid_bifast');
      }

      // [2] ADVANCE FILTERS / FILTER LANJUTAN (Metadata & Analysis):
      // Filter ini digunakan untuk analisis data yang lebih kompleks menggunakan kombinasi kriteria (Multi-Criteria Search).
      // Biasanya diinputkan melalui Modal Form "Advance Filter".
      //
      // Pola Implementasi: "POST-over-SESSION"
      // 1. Jika ada input POST baru (pengguna menekan tombol 'Search' di modal), gunakan nilai tersebut dan simpan ke session.
      // 2. Jika tidak ada POST (misal: navigasi halaman), ambil nilai terakhir dari session (Fallback).
      // Hal ini memungkinkan pengguna untuk berpindah modul dan kembali lagi dengan filter yang tetap aktif (Sticky Filter).
      $search_name_bifast = $this->input->post('search_name_bifast') != NULL ? $this->input->post('search_name_bifast') : $this->session->userdata('search_name_bifast');
      $search_date_bifast = $this->input->post('search_date_bifast') != NULL ? $this->input->post('search_date_bifast') : $this->session->userdata('search_date_bifast');
      $search_date_bifast_to = $this->input->post('search_date_bifast_to') != NULL ? $this->input->post('search_date_bifast_to') : $this->session->userdata('search_date_bifast_to');
      $search_external_reff_id = $this->input->post('search_external_reff_id') != NULL ? $this->input->post('search_external_reff_id') : $this->session->userdata('search_external_reff_id');
      $search_channel_bifast = $this->input->post('search_channel_bifast') != NULL ? $this->input->post('search_channel_bifast') : $this->session->userdata('search_channel_bifast');
      $search_internal_channel_bifast = $this->input->post('search_internal_channel_bifast') != NULL ? $this->input->post('search_internal_channel_bifast') : $this->session->userdata('search_internal_channel_bifast');
      $search_status_transaction_bifast = $this->input->post('search_status_transaction_bifast') != NULL ? $this->input->post('search_status_transaction_bifast') : $this->session->userdata('search_status_transaction_bifast');

      // Validasi Relasional: External Reff ID hanya valid jika channel eksternal telah dipilih.
      // Hal ini mencegah ambiguitas data karena Reff ID eksternal bergantung pada provider channel tertentu.
      if (!empty($search_external_reff_id) && (empty($search_channel_bifast) || $search_channel_bifast === '' || $search_channel_bifast === null)) {
         $this->session->set_flashdata('error', 'Silakan pilih "External Channel" terlebih dahulu sebelum memasukan "External Reff ID"');
         redirect('finance/bi-fast');
      }

      // Simpan filter aktif ke session untuk persistensi antar request.
      $this->session->set_userdata([
         'search_name_bifast' => $search_name_bifast,
         'search_date_bifast' => $search_date_bifast,
         'search_date_bifast_to' => $search_date_bifast_to,
         'search_transid_bifast' => $search_transid_bifast,
         'search_external_reff_id' => $search_external_reff_id,
         'search_channel_bifast' => $search_channel_bifast,
         'search_internal_channel_bifast' => $search_internal_channel_bifast,
         'search_status_transaction_bifast' => $search_status_transaction_bifast,
      ]);

      // Penanganan DataTables AJAX
      if ($this->input->is_ajax_request()) {
         try {
            // [3] LOCAL DATATABLES SEARCH (Interactive Table UI):
            // Diambil dari input 'search[value]' yang dikirim otomatis oleh library DataTables saat pengguna mengetik 
            // di kotak pencarian yang ada tepat di atas tabel.
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_bifast');

            // Logika "Reset Shortcut":
            // Kami mengimplementasikan fitur di mana jika pengguna menghapus isi kotak pencarian DataTables (menjadi kosong),
            // sistem akan otomatis membersihkan SEMUA kriteria filter lain di session (Advance & Global Filter).
            // Ini memberikan cara cepat bagi pengguna untuk kembali ke tampilan default tanpa harus membuka modal filter.
            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_bifast');
               $this->session->unset_userdata('search_date_bifast_to');
               $this->session->unset_userdata('search_name_bifast');
               $this->session->unset_userdata('search_status_transaction_bifast');
               $this->session->unset_userdata('search_transid_bifast');
               $this->session->unset_userdata('search_channel_bifast');
               $this->session->unset_userdata('search_internal_channel_bifast');
               $this->session->unset_userdata('search_external_reff_id');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_transid_bifast', $dtSearch);
               $this->session->set_userdata('last_search_bifast', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_bifast'),
               'date_from' => $this->session->userdata('search_date_bifast'),
               'date_to' => $this->session->userdata('search_date_bifast_to'),
               'transid' => $this->session->userdata('search_transid_bifast'),
               'external_reff' => $this->session->userdata('search_external_reff_id'),
               'channel' => $this->session->userdata('search_channel_bifast'),
               'internal_channel' => $this->session->userdata('search_internal_channel_bifast'),
               'status' => $this->session->userdata('search_status_transaction_bifast')
            ];
            
            // Mendelegasikan pengambilan data ke model untuk memisahkan logika bisnis dari kontroler.
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

      // Persiapan data untuk tampilan view (initial load)
      $data['merchants'] = $this->BiFast->get_merchant();
      $data['channels'] = $this->BiFast->get_channels();
      $data['internal_channels'] = $this->BiFast->get_internal_channels();
      $data['search_status_transaction_bifast'] = $search_status_transaction_bifast ?: '';

      $this->load->view('bifast/list', $data);
   }

   /**
    * Menghapus semua parameter pencarian dari session dan mengarahkan kembali ke halaman utama.
    * Digunakan untuk fitur "Reset Filter" agar pengguna dapat memulai pencarian baru dengan bersih.
    */
   public function resetbi_fast()
   {
      $this->session->unset_userdata('search_date_bifast');
      $this->session->unset_userdata('search_date_bifast_to');
      $this->session->unset_userdata('search_name_bifast');
      $this->session->unset_userdata('search_transid_bifast');
      $this->session->unset_userdata('search_status_transaction_bifast');
      $this->session->unset_userdata('search_external_reff_id');
      $this->session->unset_userdata('search_channel_bifast');
      $this->session->unset_userdata('search_internal_channel_bifast');
      $this->session->unset_userdata('last_search_bifast');
      redirect('finance/bi-fast');
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
      $search_date_bifast = isset($_GET['search_date_bifast']) ? $_GET['search_date_bifast'] : '';
      $search_name_bifast = isset($_GET['search_name_bifast']) ? $_GET['search_name_bifast'] : '';

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
