<div id="module-ug-qris-recurring" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The QRIS Recurring module monitors subscription payments made via dynamic billing cycles. This page allows you to track the scheduled, repeated transaction attempts automatically generated for subscriber merchant accounts via QRIS tokenization.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Data Columns</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Column</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">The exact server timestamp when the recurring cycle triggered the QR billing API.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">The main merchant tenant that owns the subscription (Format: <code>[ID] - Name</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">If applicable, the specific branch or sub-entity of the merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">The unique recurring Invoice Number provided by the merchant's application.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">The internal routing channel used (e.g., `qris_mpm`) and its description.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">The reference ID given back to us by the upstream provider.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">The exact gross amount billed for this subscription cycle.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            The current state of the recurring registration. The precise database values are:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Registration request received, waiting for aggregator response.</li>
                                <li><code>Created</code>: Recurring QRIS successfully registered and active.</li>
                                <li><code>Failed</code>: Registration request rejected by the upstream aggregator.</li>
                                <li><code>Cancel</code>: Recurring QRIS was manually deactivated/cancelled by the merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Search and Filter Subscriptions</h5>
            <p class="text-muted mb-4">Use the built-in search and filter tools to monitor active subscription cycles and pinpoint specific billing outcomes.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by Channel, Merchant, or ID...</em> box to instantly filter the table. This is the fastest way to check why a customer's monthly payment didn't process.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                    <li class="mb-3">Configure your parameters: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, or <strong>STATUS</strong>.</li>
                    <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Inspecting the External Log (API Payloads)</h5>
            <p class="text-muted mb-4">You can inspect the raw API request and response data directly from the table to troubleshoot subscription callbacks.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Find the recurring transaction row you want to inspect.</li>
                    <li class="mb-3">Check the <strong>STATUS</strong> column. If it shows <span class="badge badge-success">PAID</span> or <span class="badge badge-success">SUCCESS</span>, click the badge.</li>
                    <li class="mb-2">In the <em>External Log Details</em> modal, use the <strong>REQUEST Tab</strong> to view the subscription billing trigger payload, and the <strong>RESPONSE Tab</strong> for the settlement confirmation.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Clickable Badges Restriction</strong>
                    <p class="mb-0 text-muted small">The external log inspector is only available for successful transactions that have an associated External ID. Badges for PENDING or FAILED transactions are not clickable because there is no success callback to display.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qrr_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: How does QRIS Recurring work if the customer has to scan?
                </a>
                <div id="faq_en_qrr_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> QRIS Recurring (Tokenization) requires the customer to authorize a mandate during their very first scan. Future billings will automatically pull funds from their linked e-wallet without the customer needing to scan anything again.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qrr_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Which e-wallets support QRIS Recurring?
                </a>
                <div id="faq_en_qrr_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Currently, only a select few premium e-wallets (like ShopeePay, DANA, or GoPay Premium) support true QRIS tokenization. Standard bank apps typically do not support auto-debit via QRIS yet.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qrr_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Why was a recurring deduction rejected by the e-wallet?
                </a>
                <div id="faq_en_qrr_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This almost always happens if the customer's e-wallet balance is insufficient, or if the customer manually went into their e-wallet app settings and revoked the mandate authorization for the merchant.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul QRIS Recurring memantau pembayaran langganan (subscription) yang dilakukan melalui siklus penagihan dinamis. Halaman ini memungkinkan Anda melacak upaya transaksi berulang yang otomatis dibuat untuk akun merchant pelanggan via tokenisasi QRIS.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Kolom Data</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Kolom</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">Stempel waktu server saat siklus berulang memicu API penagihan QR.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">Tenant merchant utama yang memiliki langganan tersebut (Format: <code>[ID] - Nama</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">Jika ada, cabang atau sub-entitas spesifik merchant terkait.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">Nomor Invoice berulang unik yang diberikan oleh aplikasi merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">Channel perutean internal yang digunakan (misal: `qris_mpm`) beserta deskripsinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">ID referensi yang dikembalikan ke sistem kita oleh penyedia hulu.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">Jumlah kotor pasti yang ditagih untuk siklus langganan berjalan ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            Keadaan pendaftaran recurring saat ini. Nilai presisi dari database adalah:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Permintaan pendaftaran diterima, menunggu respons dari agregator.</li>
                                <li><code>Created</code>: Recurring QRIS berhasil didaftarkan dan aktif.</li>
                                <li><code>Failed</code>: Permintaan pendaftaran ditolak oleh agregator hulu.</li>
                                <li><code>Cancel</code>: Recurring QRIS dinonaktifkan/dibatalkan secara manual oleh merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Mencari dan Memfilter Langganan</h5>
            <p class="text-muted mb-4">Gunakan alat pencarian dan filter bawaan untuk memantau siklus langganan aktif dan memecahkan masalah penagihan gagal.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by Channel, Merchant, or ID...</em> untuk memfilter tabel secara instan. Sangat berguna jika merchant bertanya mengapa pembayaran bulanan pelanggan mereka tidak terproses.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                    <li class="mb-3">Saring parameter Anda: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, atau <strong>STATUS</strong>.</li>
                    <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Menginspeksi Log Eksternal (Payload API)</h5>
            <p class="text-muted mb-4">Anda dapat menginspeksi data mentah API secara langsung dari tabel untuk investigasi isu webhook callback.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari baris transaksi berulang yang ingin Anda periksa.</li>
                    <li class="mb-3">Cek kolom <strong>STATUS</strong>. Jika terbaca <span class="badge badge-success">PAID</span> atau <span class="badge badge-success">SUCCESS</span>, klik badge tersebut.</li>
                    <li class="mb-2">Pada modal <em>External Log Details</em>, gunakan <strong>Tab REQUEST</strong> untuk melihat trigger API penagihan berulang, dan <strong>Tab RESPONSE</strong> untuk melihat payload kesuksesan.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Klik Badge</strong>
                    <p class="mb-0 text-muted small">Inspektur log eksternal hanya disediakan untuk transaksi sukses (mempunyai External ID). Badge untuk transaksi PENDING atau FAILED tidak bisa diklik karena belum ada respons sukses yang bisa ditampilkan.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrr_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Bagaimana QRIS Recurring menagih tanpa scan berulang?
                </a>
                <div id="faq_id_qrr_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> QRIS Recurring (Tokenisasi) mengharuskan pelanggan menyetujui mandat pada pemindaian pertama. Penagihan bulan-bulan berikutnya akan langsung menarik dana dari dompet terhubung secara otomatis via API tanpa pelanggan perlu memindai kode QR lagi.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrr_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: E-wallet mana saja yang mendukung QRIS Recurring?
                </a>
                <div id="faq_id_qrr_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Saat ini, baru beberapa e-wallet tier atas (seperti ShopeePay, DANA, GoPay Premium) yang menyokong infrastruktur tokenisasi QRIS. Sebagian besar aplikasi Mobile Banking belum mendukung fitur auto-debit QRIS.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrr_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kenapa penagihan berulang ditolak oleh sistem e-wallet?
                </a>
                <div id="faq_id_qrr_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Kasus ini mayoritas terjadi jika saldo e-wallet nasabah tidak mencukupi untuk dipotong, atau nasabah diam-diam masuk ke pengaturan e-wallet mereka dan mencabut (revoke) izin mandat berlangganan untuk merchant tersebut.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
