<div id="module-ug-va-recurring" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The VA Recurring module monitors automated subscription payments made via Virtual Accounts. It tracks the scheduled, repeated transaction attempts generated for subscriber merchant accounts.</p>

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
                        <tr><td class="p-3 border-0"><strong>DATE TIME REQUEST</strong></td><td class="p-3 border-0">The exact server timestamp when the recurring cycle triggered the VA creation API.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">The merchant tenant that owns the subscription (Format: <code>[ID] - Name</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">If applicable, the specific branch or sub-entity of the merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">The unique recurring Invoice Number provided by the merchant's application.</td></tr>
                        <tr><td class="p-3 border-0"><strong>VA NUMBER</strong></td><td class="p-3 border-0">The virtual account number assigned for this specific billing cycle.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL ID</strong></td><td class="p-3 border-0">The internal routing channel used (e.g., `va_bca`) and its description.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">The reference ID given back to us by the upstream bank or aggregator.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">The exact gross amount billed for this subscription cycle.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            The current state of the recurring registration. The precise database values are:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Registration request received, waiting for aggregator response.</li>
                                <li><code>Created</code>: Recurring VA successfully registered and active.</li>
                                <li><code>Failed</code>: Registration request rejected by the upstream aggregator.</li>
                                <li><code>Cancel</code>: Recurring VA was manually deactivated/cancelled by the merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Search and Filter Subscriptions</h5>
            <p class="text-muted mb-4">Use the built-in tools to monitor active subscription cycles and scheduled billings.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type the <em>Channel</em>, <em>Merchant</em>, or <em>ID</em> (like Merchant Trans ID or VA Number) directly into the search box.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> Filters button to open the dropdown menu.</li>
                    <li class="mb-3">Filter your parameters: <strong>Request Date</strong>, <strong>Merchant</strong>, <strong>External Channel</strong>, <strong>Channel ID</strong>, or <strong>Status</strong>.</li>
                    <li class="mb-2">Click <strong>APPLY FILTER</strong> to load the data. Click <strong>Clear All</strong> to reset the view.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Inspecting the External Log (API Payloads)</h5>
            <p class="text-muted mb-4">You can inspect the raw API request and response data to troubleshoot subscription callbacks.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Find the recurring transaction row you want to inspect.</li>
                    <li class="mb-3">Check the <strong>STATUS</strong> column. If it shows <span class="badge badge-success">PAID</span> or <span class="badge badge-success">SUCCESS</span>, click the badge.</li>
                    <li class="mb-2">In the <em>External Log Details</em> modal, use the <strong>REQUEST Tab</strong> to see the subscription trigger payload, and the <strong>RESPONSE Tab</strong> for the settlement callback payload.</li>
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
                <a href="#faq_en_var_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: What happens if a recurring billing fails?
                </a>
                <div id="faq_en_var_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The system will automatically log the failure. Depending on the merchant's API configuration, it may attempt to retry the billing on the next cycle, or the subscription mandate will be suspended until manual intervention.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_var_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can the merchant change the recurring amount mid-cycle?
                </a>
                <div id="faq_en_var_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No, the recurring amount is strictly locked when the initial subscription mandate is created. To change the price, the old subscription must be cancelled and a new one created by the customer.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_var_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Are customers notified before the auto-debit?
                </a>
                <div id="faq_en_var_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Our system acts only as the payment processor/gateway. The merchant's own application is fully responsible for sending reminder emails or SMS to their customers prior to the scheduled billing date.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul VA Recurring memantau pembayaran langganan (subscription) otomatis yang dilakukan via Virtual Account. Halaman ini melacak upaya transaksi berulang terjadwal yang dibuat untuk akun pelanggan merchant.</p>

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
                        <tr><td class="p-3 border-0"><strong>DATE TIME REQUEST</strong></td><td class="p-3 border-0">Stempel waktu server saat siklus berulang memicu API pembuatan VA.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">Tenant merchant yang memiliki langganan tersebut (Format: <code>[ID] - Nama</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">Jika ada, cabang atau sub-entitas spesifik dari merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">Nomor Invoice berulang unik dari aplikasi merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>VA NUMBER</strong></td><td class="p-3 border-0">Nomor virtual account yang ditetapkan untuk siklus penagihan spesifik ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL ID</strong></td><td class="p-3 border-0">Channel perutean internal yang digunakan (misal: `va_bca`) dan deskripsinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">ID referensi yang dikembalikan ke sistem kita oleh bank hulu/agregator.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">Jumlah kotor pasti yang ditagih untuk siklus langganan ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            Keadaan pendaftaran recurring saat ini. Nilai presisi dari database adalah:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Permintaan pendaftaran diterima, menunggu respons dari agregator.</li>
                                <li><code>Created</code>: Pembayaran recurring berhasil didaftarkan dan aktif.</li>
                                <li><code>Failed</code>: Permintaan pendaftaran ditolak oleh agregator hulu.</li>
                                <li><code>Cancel</code>: Pembayaran recurring dinonaktifkan/dibatalkan secara manual oleh merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Mencari dan Memfilter Langganan</h5>
            <p class="text-muted mb-4">Gunakan alat ini untuk memantau siklus langganan aktif dan penagihan terjadwal.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik <em>Channel</em>, <em>Merchant</em>, atau <em>ID</em> (seperti Merchant Trans ID atau VA Number) langsung ke kotak pencarian.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> Filters untuk membuka menu dropdown.</li>
                    <li class="mb-3">Saring data Anda berdasarkan: <strong>Request Date</strong>, <strong>Merchant</strong>, <strong>External Channel</strong>, <strong>Channel ID</strong>, atau <strong>Status</strong>.</li>
                    <li class="mb-2">Klik <strong>APPLY FILTER</strong> untuk memuat data. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Menginspeksi Log Eksternal (Payload API)</h5>
            <p class="text-muted mb-4">Anda dapat memeriksa data mentah permintaan dan respons API untuk menganalisis masalah callback langganan.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari baris transaksi berulang yang ingin diinspeksi.</li>
                    <li class="mb-3">Cek kolom <strong>STATUS</strong>. Jika terbaca <span class="badge badge-success">PAID</span> atau <span class="badge badge-success">SUCCESS</span>, klik badge tersebut.</li>
                    <li class="mb-2">Pada modal <em>External Log Details</em>, pakai <strong>Tab REQUEST</strong> untuk melihat pemicu penagihan langganan, dan <strong>Tab RESPONSE</strong> untuk melihat payload callback penyelesaian.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Klik Badge</strong>
                    <p class="mb-0 text-muted small">Inspektur log eksternal hanya bisa diakses untuk transaksi sukses yang punya External ID. Badge untuk transaksi PENDING atau FAILED tidak bisa diklik karena tidak ada respons sukses yang bisa ditampilkan.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_var_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Apa yang terjadi jika penagihan berulang gagal?
                </a>
                <div id="faq_id_var_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Sistem otomatis mencatat kegagalan. Bergantung pada konfigurasi API merchant, sistem mungkin mencoba menagih ulang pada siklus berikutnya, atau menangguhkan mandat langganan tersebut sampai ada intervensi manual.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_var_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bisakah merchant mengubah jumlah tagihan di tengah siklus?
                </a>
                <div id="faq_id_var_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak, jumlah penagihan dikunci ketat saat mandat langganan awal dibuat. Untuk mengubah harga paket, langganan lama wajib dibatalkan dan yang baru dibuat ulang oleh pelanggan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_var_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Apakah pelanggan diberi tahu sebelum auto-debit?
                </a>
                <div id="faq_id_var_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Sistem kami hanya bertindak sebagai gateway/pemroses pembayaran. Aplikasi milik merchant bertanggung jawab penuh untuk mengirimkan email pengingat atau SMS peringatan ke nasabah mereka sebelum tanggal penagihan.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
