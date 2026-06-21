<div id="module-ug-report" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Business Intelligence Report module provides comprehensive data extraction and analytical summaries. Monitor your business performance through interactive charts and generate multi-format reports for auditing purposes.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Interface Components</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Component</th>
                            <th class="p-3 border-0">Function &amp; Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Global Analytics Chart</strong></td><td class="p-3 border-0">Visual line/bar charts representing transaction volume, success rates, and revenue trends over the selected time period.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Filter Engine</strong></td><td class="p-3 border-0">Advanced form controls allowing you to slice data by Date Range, Merchant Entity, and Payment Channel.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Data Table</strong></td><td class="p-3 border-0">A paginated list of aggregated data summarizing transactions per day, per channel, or per merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Export Actions</strong></td><td class="p-3 border-0">Buttons situated above the table to export the current filtered view to Excel (.xlsx) or CSV formats.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Callout -->
        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-bolt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Technical Insight: Cached Aggregation</strong>
                <p class="mb-0 text-muted small">To ensure lightning-fast dashboard loading times even during peak transaction volumes, the Business Intelligence module queries <strong>cached aggregate data</strong> rather than calculating sums directly from live transactional tables. This data is refreshed every 5-10 minutes.</p>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-filter text-primary mr-2"></i> 1. Filtering Data</h5>
            <p class="text-muted mb-4">Slice and dice your transactional history to find exact analytical figures.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>DASHBOARD &rarr; Business Intelligence</strong> on the left sidebar.</li>
                    <li class="mb-3">Click the <strong>Date Picker</strong> input to select a specific Date Range (e.g., Last 7 Days, This Month, or Custom Range).</li>
                    <li class="mb-3">Use the dropdowns to optionally narrow down the data by a specific <strong>Merchant</strong> or <strong>Payment Channel</strong>.</li>
                    <li class="mb-2">Click the <strong>Filter</strong> button. The charts and data table will refresh to reflect your selection.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Workflow Exporting -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-file-export text-success mr-2"></i> 2. Exporting Reports</h5>
            <p class="text-muted mb-4">Download your filtered data for external auditing or accounting software integration.</p>

            <div class="pl-4 border-left border-success ml-2">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Once your data is filtered, locate the export buttons at the top right of the data table.</li>
                    <li class="mb-3">Click <strong>Export Excel</strong> for a formatted spreadsheet, or <strong>Export CSV</strong> for fast, raw data processing.</li>
                    <li class="mb-2"><strong>Background Jobs:</strong> For very large datasets (>10,000 rows), the export will be queued automatically as a background job to prevent browser timeout. You will receive an email/notification when the file is ready to be downloaded.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rep_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Chart data is not matching live transactions
                </a>
                <div id="faq_en_rep_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> A major transaction just occurred, but the revenue chart hasn't spiked.<br><br>
                        <strong>Resolution:</strong> The Business Intelligence module aggregates data in 5-10 minute cached intervals. Wait a few minutes for the cron job to update the aggregates. For absolute real-time tracking, visit the <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-history\']').click()" class="font-weight-bold text-info" style="text-decoration: underline;">Transaction History</a> module.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rep_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: The "Export Excel" process is taking too long
                </a>
                <div id="faq_en_rep_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Clicking the Excel button causes the browser tab to spin infinitely.<br><br>
                        <strong>Resolution:</strong> If you are exporting data spanning multiple months with hundreds of thousands of rows, generating an `.xlsx` file uses massive server RAM. Try narrowing your Date Range filter (e.g., export week by week) or use the <strong>Export CSV</strong> format which processes substantially faster.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rep_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Where do I download daily settlement reports?
                </a>
                <div id="faq_en_rep_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This module provides general Business Intelligence. For official daily settlement statements (mutasi bank equivalents), you must navigate to the <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-financial-exports\']').click()" class="font-weight-bold text-info" style="text-decoration: underline;">Financial Exports</a> module instead.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul Laporan Business Intelligence menyediakan ekstraksi data yang komprehensif dan ringkasan analitis. Pantau kinerja bisnis Anda melalui grafik interaktif dan buat laporan berbagai format untuk keperluan audit.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Komponen Antarmuka</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Komponen</th>
                            <th class="p-3 border-0">Fungsi &amp; Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Grafik Analitik Global</strong></td><td class="p-3 border-0">Grafik garis/batang visual yang merepresentasikan volume transaksi, rasio sukses, dan tren pendapatan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Mesin Filter</strong></td><td class="p-3 border-0">Kontrol formulir tingkat lanjut untuk membedah data berdasar Rentang Tanggal, Merchant, dan Saluran Pembayaran.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tabel Data</strong></td><td class="p-3 border-0">Daftar agregat berhalaman (paginated) yang merangkum transaksi per hari, per saluran, atau per merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Aksi Ekspor</strong></td><td class="p-3 border-0">Tombol di atas tabel untuk mengekspor tampilan ke format Excel (.xlsx) atau CSV.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Callout -->
        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-bolt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Wawasan Teknis: Agregasi Cached</strong>
                <p class="mb-0 text-muted small">Untuk memastikan waktu muat dasbor secepat kilat bahkan saat jam sibuk, modul ini melakukan kueri terhadap <strong>data agregat yang di-cache</strong> alih-alih menghitung jumlah total secara langsung dari tabel live. Data ini disegarkan setiap 5-10 menit.</p>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-filter text-primary mr-2"></i> 1. Memfilter Data</h5>
            <p class="text-muted mb-4">Gunakan alat filter untuk membedah riwayat transaksi demi mendapatkan angka analitis yang pasti.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Arahkan ke menu <strong>DASHBOARD &rarr; Business Intelligence</strong> di sidebar sebelah kiri.</li>
                    <li class="mb-3">Klik input <strong>Date Picker</strong> untuk memilih Rentang Tanggal spesifik (mis. 7 Hari Terakhir, Bulan Ini, atau Rentang Kustom).</li>
                    <li class="mb-3">Gunakan dropdown untuk menyaring data lebih detail berdasarkan <strong>Merchant</strong> atau <strong>Channel Pembayaran</strong> tertentu.</li>
                    <li class="mb-2">Klik tombol <strong>Filter</strong>. Grafik dan tabel data akan memuat ulang sesuai parameter Anda.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Workflow Exporting -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-file-export text-success mr-2"></i> 2. Mengekspor Laporan</h5>
            <p class="text-muted mb-4">Unduh data Anda untuk keperluan audit eksternal atau integrasi software akuntansi.</p>

            <div class="pl-4 border-left border-success ml-2">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Setelah data berhasil difilter, temukan deretan tombol ekspor di kanan atas tabel data.</li>
                    <li class="mb-3">Klik <strong>Export Excel</strong> untuk spreadsheet berformat rapi, atau <strong>Export CSV</strong> untuk pemrosesan data mentah yang lebih cepat.</li>
                    <li class="mb-2"><strong>Pekerjaan Latar (Background Jobs):</strong> Untuk dataset raksasa (>10.000 baris), pengeksporan diantrekan secara otomatis di latar belakang agar browser tidak terputus (timeout). Anda akan mendapat email/notifikasi saat file siap diunduh.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rep_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Data di grafik tidak sama persis dengan transaksi live
                </a>
                <div id="faq_id_rep_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Sebuah transaksi besar baru saja sukses, tapi grafik pendapatan belum melonjak.<br><br>
                        <strong>Resolusi:</strong> Modul BI menggabungkan data ke dalam cache bervolume 5-10 menit. Tunggu beberapa menit agar skrip cron memperbarui agregat. Untuk pelacakan yang real-time seketika itu juga, kunjungi modul <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-history\']').click()" class="font-weight-bold text-info" style="text-decoration: underline;">Transaction History</a>.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rep_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Proses "Export Excel" memakan waktu sangat lama
                </a>
                <div id="faq_id_rep_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Klik tombol Excel menyebabkan tab browser terus-terusan loading.<br><br>
                        <strong>Resolusi:</strong> Jika mengekspor rentang bulan dengan ratusan ribu baris, pembuatan `.xlsx` sangat memakan RAM server. Persempit filter Anda (mis. ekspor per minggu) atau gunakan format <strong>Export CSV</strong> yang jauh lebih ringan dan gesit.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rep_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Di mana saya mengunduh laporan mutasi settlement harian?
                </a>
                <div id="faq_id_rep_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Modul ini memberikan Laporan Business Intelligence umum. Untuk laporan settlement resmi harian (setara mutasi bank), Anda wajib membuka modul <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-financial-exports\']').click()" class="font-weight-bold text-info" style="text-decoration: underline;">Financial Exports</a>.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
