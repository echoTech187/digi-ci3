<div id="module-history" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Transaction History</strong> module provides a comprehensive ledger of every transaction that has passed through the gateway. This module features session-persistent filtering, multi-dimensional auditing, and an asynchronous bulk export engine — designed to handle millions of records without freezing your browser.</p>
        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — History Ledger</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:30%">UI Element</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Global Search Bar</strong></td><td class="p-3 border-0">Simultaneously queries <code>Invoice ID</code>, <code>Transaction ID</code>, and <code>Phone Number</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Advanced Filters</strong></td><td class="p-3 border-0">Multi-dimensional filtering (REQUEST DATE, MERCHANT, EXTERNAL CHANNEL, STATUS). The system permanently caches your filter state in your active session.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Reset Filter Button</strong></td><td class="p-3 border-0">Flushes all session caches and returns the table to the default un-filtered view.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Transaction Row</strong></td><td class="p-3 border-0">Clickable rows that reveal the full forensic detail of a transaction (including Callback statuses).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Download Excel</strong></td><td class="p-3 border-0">Dispatches an asynchronous export job to the background worker.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: High-Volume Data Management</h5>
            <p class="text-muted mb-4">Because the transaction ledger processes immense volumes of data, the module is engineered with two major technical safeguards:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([User applies Filters]) --> B[Lock params in PHP Session]
                        B --> C[AJAX Datatable Request]
                        C --> D{Server Reads Session}
                        D --> E[Build Optimized Query<br>with LIMIT & OFFSET]
                        E --> F[Fetch Chunk from DB]
                        F --> G[(Render UI Table)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Session-Locked Pagination (AJAX):</strong> Data is never fully loaded at once. The table uses server-side processing. Your search terms and filters are locked into the <code>$this->session</code> state. If you navigate away, the system remembers exactly what you were looking at.</li>
                    <li class="mb-2"><strong>Asynchronous Export Queue:</strong> Traditional "Download All" buttons freeze servers. Here, clicking Download Excel creates a job token in the <code>admin_download</code> queue. A cron worker processes this in the background, allowing you to continue using the dashboard normally.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-shield-alt text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Export Safeguards</strong>
                    <p class="mb-0 text-muted small">You are structurally prevented from initiating an export without applying at least one filter. This protects the database from accidental "Select All" queries that could lock the production database and crash the gateway.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Operating the Ledger</h5>
            <p class="text-muted mb-4">How to properly query the history and export data.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Searching & Filtering</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Transaction History</strong>. By default, the table displays the last 7 days.</li>
                    <li class="mb-3">For quick lookup, use the search bar to find an <strong>Invoice ID</strong> or <strong>Transaction ID</strong>.</li>
                    <li class="mb-3">For deep audits, click <strong><i class="fas fa-sliders-h"></i> Filters</strong>. Configure your parameters like <strong>REQUEST DATE</strong> and <strong>STATUS</strong>, then click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                    <li class="mb-2"><strong>Crucial Step:</strong> If you want to start a new search, you <strong>must</strong> click the <strong>Reset Filter</strong> button to clear your locked session memory.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Exporting to Excel</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Ensure your filters are applied (e.g., filtering for a specific merchant over the last month).</li>
                    <li class="mb-3">Click the <strong>Download Excel</strong> button in the toolbar. The system will alert you that the job has been queued.</li>
                    <li class="mb-3">Navigate to the <strong>Download Report</strong> module via the sidebar.</li>
                    <li class="mb-2">Wait for the background worker to finish processing. Once complete, click the generated download link to retrieve your file.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Reading Transaction Details</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the transaction in the table and click anywhere on the row.</li>
                    <li class="mb-3">The detail modal will appear. <strong>Status</strong> indicates if funds moved.</li>
                    <li class="mb-2">Check the <strong>Callback Status</strong> field. If it says <code>Pending</code> even when Status is <code>Success</code>, it means the gateway received the money, but failed to successfully push the notification to the Merchant's server.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_hist_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: The transaction table is completely empty
                </a>
                <div id="faq_en_hist_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This is known as the "Ghost Filter". Your previous search filter is still locked in your server session cache. Click the <strong>Reset Filter</strong> button to explicitly flush the session and restore the default view.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_hist_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: The Download Report file never appears
                </a>
                <div id="faq_en_hist_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The background worker may still be compiling the file, or the worker encountered a memory limit error while processing an extraordinarily large dataset. Wait 2–5 minutes. If it fails, retry with a narrower date range.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_hist_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Status is Success but Callback Status is Pending
                </a>
                <div id="faq_en_hist_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The payment was successful, but the gateway was unable to communicate with the merchant's server (usually due to a timeout or 500 error on the merchant's end). Ask the merchant to manually trigger a callback resend or manually complete the order.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Transaction History</strong> menyediakan buku besar komprehensif dari setiap transaksi yang melewati gateway. Modul ini dilengkapi filter persisten berbasis sesi, audit multi-dimensi, dan mesin ekspor massal asinkron — dirancang untuk menangani jutaan record tanpa membekukan browser Anda.</p>
        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Buku Besar (Ledger) History</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:30%">Elemen UI</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Global Search Bar</strong></td><td class="p-3 border-0">Mencari secara bersamaan pada <code>Invoice ID</code>, <code>Transaction ID</code>, dan <code>Nomor Telepon</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Filter Lanjutan</strong></td><td class="p-3 border-0">Filter multi-dimensi (REQUEST DATE, MERCHANT, EXTERNAL CHANNEL, STATUS). Sistem menyiman status filter Anda di sesi aktif.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tombol Reset Filter</strong></td><td class="p-3 border-0">Membersihkan seluruh memori sesi filter dan mengembalikan tabel ke tampilan default awal.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Baris Transaksi</strong></td><td class="p-3 border-0">Baris yang dapat diklik untuk membuka detail forensik lengkap dari transaksi (termasuk status Callback).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Download Excel</strong></td><td class="p-3 border-0">Mengirim instruksi ekspor data asinkron ke sistem latar belakang (*background worker*).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Manajemen Data Bervolume Tinggi</h5>
            <p class="text-muted mb-4">Karena ledger transaksi memproses volume data yang luar biasa besar, modul ini direkayasa dengan dua perlindungan teknis utama:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([User menerapkan Filter]) --> B[Kunci parameter di PHP Session]
                        B --> C[Request Datatable AJAX]
                        C --> D{Server Membaca Session}
                        D --> E[Bangun Query Optimal<br>dengan LIMIT & OFFSET]
                        E --> F[Ambil Chunk dari DB]
                        F --> G[(Render UI Tabel)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Paginasi Terkunci Sesi (AJAX):</strong> Data tidak pernah dimuat sekaligus. Tabel menggunakan pemrosesan sisi server. Kata kunci pencarian dan filter Anda dikunci ke dalam state <code>$this->session</code>. Jika Anda berpindah halaman, sistem akan mengingat persis apa yang sedang Anda cari.</li>
                    <li class="mb-2"><strong>Antrean Ekspor Asinkron:</strong> Tombol "Download All" tradisional akan membuat server hang. Di sini, mengklik Download Excel menciptakan token tugas di antrean <code>admin_download</code>. Sebuah sistem cron di latar belakang yang mengerjakannya, sehingga dasbor Anda tetap lancar.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-shield-alt text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Pengaman Ekspor</strong>
                    <p class="mb-0 text-muted small">Anda secara struktural dicegah dari memulai proses ekspor tanpa menerapkan setidaknya satu filter. Ini melindungi database dari kueri "Pilih Semua" secara tidak sengaja yang bisa mengunci database produksi dan membuat gateway lumpuh.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengoperasikan History Ledger</h5>
            <p class="text-muted mb-4">Cara menjalankan kueri riwayat dan mengekspor data dengan benar.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Mencari & Memfilter</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Arahkan ke <strong>Transaction History</strong>. Secara default, tabel menampilkan 7 hari terakhir.</li>
                    <li class="mb-3">Untuk pencarian instan, gunakan search bar untuk menemukan <strong>Invoice ID</strong> atau <strong>Transaction ID</strong>.</li>
                    <li class="mb-3">Untuk audit mendalam, klik tombol <strong><i class="fas fa-sliders-h"></i> Filters</strong>. Atur <strong>REQUEST DATE</strong> dan <strong>STATUS</strong>, lalu klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                    <li class="mb-2"><strong>Langkah Krusial:</strong> Jika Anda ingin memulai pencarian baru yang segar, Anda <strong>wajib</strong> mengklik tombol <strong>Reset Filter</strong> untuk menghapus memori sesi yang terkunci.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Mengekspor ke Excel</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Pastikan filter Anda sudah diterapkan (misalnya, memfilter merchant tertentu selama sebulan terakhir).</li>
                    <li class="mb-3">Klik tombol <strong>Download Excel</strong> di bilah alat. Sistem akan memberitahu Anda bahwa tugas ekspor sudah masuk antrean.</li>
                    <li class="mb-3">Navigasikan ke modul <strong>Download Report</strong> melalui sidebar.</li>
                    <li class="mb-2">Tunggu worker latar belakang menyelesaikan pemrosesan. Setelah selesai, klik tautan unduh yang muncul untuk mengambil file Anda.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Membaca Detail Transaksi</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Temukan transaksi di tabel dan klik baris tersebut.</li>
                    <li class="mb-3">Modal detail akan muncul. <strong>Status</strong> menunjukkan apakah pergerakan dana berhasil.</li>
                    <li class="mb-2">Periksa kolom <strong>Callback Status</strong>. Jika tertulis <code>Pending</code> padahal Status utamanya adalah <code>Success</code>, ini berarti gateway telah menerima uang, tetapi gagal meneruskan notifikasi tersebut ke server Merchant.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_hist_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Tabel transaksi kosong melompong
                </a>
                <div id="faq_id_hist_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ini dikenal sebagai "Ghost Filter". Filter pencarian Anda dari sesi sebelumnya masih terkunci di cache sesi server. Klik tombol <strong>Reset Filter</strong> untuk menghapus cache sesi tersebut dan mengembalikan tampilan default.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_hist_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: File hasil Download Report tidak pernah muncul
                </a>
                <div id="faq_id_hist_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Worker latar belakang mungkin masih sibuk menyusun file, atau worker tersebut mengalami error batas memori (memory limit) karena mencoba memproses dataset yang terlalu raksasa. Tunggu 2-5 menit. Jika masih gagal, coba lagi dengan rentang tanggal yang lebih sempit.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_hist_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Status Success tapi Callback Status malah Pending
                </a>
                <div id="faq_id_hist_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Transaksi dana berhasil, namun gateway gagal berkomunikasi dengan server merchant (biasanya karena timeout atau error 500 di server merchant). Mintalah merchant untuk memicu pengiriman ulang callback secara manual atau selesaikan pesanan tersebut secara manual di sistem mereka.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
