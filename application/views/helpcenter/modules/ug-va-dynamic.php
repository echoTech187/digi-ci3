<div id="module-ug-va-dynamic" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The VA Dynamic module is a real-time monitor for dynamically generated Virtual Accounts. Unlike a static VA (which is assigned permanently to a user), a dynamic VA is generated per-checkout with a specific expiration time and an exact payment amount.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">End-to-End VA Lifecycle</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Merchant App
                participant G as Gidi Gateway
                participant B as Banking Network
                participant C as Customer
                
                M->>G: POST /VaDynamic/generate
                G->>B: Request VA Number
                B-->>G: Return VA Number (e.g. 800123...)
                G-->>M: Return VA details
                M->>M: Display VA to Customer
                
                C->>B: Transfer exact Amount via ATM/Mobile
                B->>G: Realtime Webhook (PAID)
                G->>G: Credit Merchant Balance (minus Fee)
                G->>M: Realtime Callback (PAID)
                M->>M: Update Invoice to Success
            </div>
        </div>

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
                        <tr><td class="p-3 border-0"><strong>DATE TIME REQUEST</strong></td><td class="p-3 border-0">The exact server timestamp when the API request was made to generate the Virtual Account.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">The merchant tenant that generated the VA (Format: <code>[ID] - Name</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>TRANS ID</strong></td><td class="p-3 border-0">The unique Invoice Number or Order ID provided by the merchant's own application.</td></tr>
                        <tr><td class="p-3 border-0"><strong>VA NUMBER</strong></td><td class="p-3 border-0">The actual virtual account number the customer must transfer to (e.g., `800123456789`).</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">The internal routing channel used (e.g., `va_bca`) and its description.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">The reference ID given back to us by the upstream bank or aggregator when they generated the VA.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">The exact gross amount the customer must pay to trigger a success.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXPIRED</strong></td><td class="p-3 border-0">The exact deadline. If unpaid past this time, the VA automatically closes.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            The current state of the VA. The precise database values are:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Request received, waiting for aggregator response.</li>
                                <li><code>Created</code>: VA successfully generated, waiting for customer payment.</li>
                                <li><code>Paid</code>: Customer has successfully paid the VA.</li>
                                <li><code>Failed</code>: Request rejected by the upstream aggregator.</li>
                                <li><code>Expired</code>: Time limit exceeded without payment.</li>
                                <li><code>Cancel</code>: VA was manually cancelled by the merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Search and Filter Transactions</h5>
            <p class="text-muted mb-4">Use the built-in search and filters to track down specific VA payments.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by Channel, Merchant, or ID...</em> box to instantly filter the table. If a customer provides a transfer receipt, paste the VA Number here.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                    <li class="mb-3">Configure your parameters: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, or <strong>STATUS</strong>.</li>
                    <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Inspecting the External Log (API Payloads)</h5>
            <p class="text-muted mb-4">If a customer has successfully transferred the money but the merchant claims they did not receive the webhook callback, you can inspect the raw API data directly from this table.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Find the transaction row you want to inspect.</li>
                    <li class="mb-3">Look at the <strong>STATUS</strong> column. If the status is <span class="badge badge-success">PAID</span> or <span class="badge badge-success">SUCCESS</span>, the badge itself acts as a clickable link.</li>
                    <li class="mb-3">Click the <strong>Status Badge</strong>. An <em>External Log Details</em> modal will appear.</li>
                    <li class="mb-2">Use the <strong>REQUEST Tab</strong> to view the JSON sent to the provider, and the <strong>RESPONSE Tab</strong> to view the callback payload sent back to us upon settlement.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Clickable Badges Restriction</strong>
                    <p class="mb-0 text-muted small">The external log inspector is only available for successful transactions that have an associated External ID. Badges for PENDING or EXPIRED transactions are not clickable because there is no success callback to display.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_vad_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Why is a paid transaction still marked as PENDING?
                </a>
                <div id="faq_en_vad_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Customer provided a receipt, but dashboard shows Pending.<br><br>
                        <strong>Resolution:</strong> This usually happens if the bank's callback was delayed or failed due to network timeouts. You can check the External Log to see if we ever received the callback payload. If not, wait for the upstream aggregator's cron job to sync it.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_vad_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can I manually mark a PENDING transaction as PAID?
                </a>
                <div id="faq_en_vad_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No. VA Dynamic transactions rely entirely on upstream bank confirmations to ensure financial integrity. Manual overrides are restricted to prevent fraud and settlement discrepancies.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_vad_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The customer paid exactly at the expiration minute, but it failed
                </a>
                <div id="faq_en_vad_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Funds were deducted from the customer's account, but the invoice expired.<br><br>
                        <strong>Resolution:</strong> Bank systems often cut off the VA exactly on the second of expiration. If the customer transfers at the last second, the bank might reject it while the funds briefly float (mengendap). The bank will usually auto-refund the customer within 1x24 hours.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul VA Dynamic adalah monitor real-time untuk Virtual Account yang di-generate secara dinamis. Tidak seperti VA statis (yang ditetapkan secara permanen), VA dinamis dibuat per-checkout dengan waktu kedaluwarsa spesifik dan jumlah pembayaran yang pasti.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Siklus Hidup End-to-End VA</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Aplikasi Merchant
                participant G as Gidi Gateway
                participant B as Jaringan Bank
                participant C as Pelanggan
                
                M->>G: POST /VaDynamic/generate
                G->>B: Permintaan Nomor VA
                B-->>G: Kembalikan VA (mis. 800123...)
                G-->>M: Kembalikan detail VA
                M->>M: Tampilkan VA ke Pelanggan
                
                C->>B: Transfer nominal via ATM/M-Banking
                B->>G: Webhook Realtime (PAID)
                G->>G: Tambahkan Saldo Merchant (dipotong Fee)
                G->>M: Callback Realtime (PAID)
                M->>M: Update Invoice menjadi Sukses
            </div>
        </div>

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
                        <tr><td class="p-3 border-0"><strong>DATE TIME REQUEST</strong></td><td class="p-3 border-0">Stempel waktu server persis saat permintaan API dibuat untuk men-generate VA.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">Tenant merchant yang membuat VA (Format: <code>[ID] - Nama</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>TRANS ID</strong></td><td class="p-3 border-0">Nomor Invoice atau Order ID unik dari aplikasi merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>VA NUMBER</strong></td><td class="p-3 border-0">Nomor virtual account aktual yang harus dibayar pelanggan (misal: `800123456789`).</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">Channel perutean internal yang digunakan (misal: `va_bca`) beserta deskripsinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">ID referensi yang dikembalikan oleh bank hulu/agregator saat pembuatan VA.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">Jumlah kotor pasti yang wajib dibayar pelanggan agar status sukses.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXPIRED</strong></td><td class="p-3 border-0">Batas waktu pasti (deadline). Jika lewat, VA otomatis kedaluwarsa.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            Keadaan VA saat ini. Nilai presisi dari database adalah:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Permintaan diterima, menunggu respons dari agregator.</li>
                                <li><code>Created</code>: VA berhasil dibuat, menunggu pelanggan melakukan pembayaran.</li>
                                <li><code>Paid</code>: Pelanggan telah berhasil membayar VA.</li>
                                <li><code>Failed</code>: Permintaan ditolak oleh agregator hulu.</li>
                                <li><code>Expired</code>: Batas waktu pembayaran telah habis tanpa ada pembayaran.</li>
                                <li><code>Cancel</code>: VA dibatalkan secara manual oleh merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Mencari dan Memfilter Transaksi</h5>
            <p class="text-muted mb-4">Gunakan pencarian dan filter bawaan untuk melacak pembayaran VA secara spesifik.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by Channel, Merchant, or ID...</em> untuk memfilter tabel secara instan. Jika pelanggan memberikan bukti transfer, tempel Nomor VA di sini.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                    <li class="mb-3">Saring parameter Anda: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, atau <strong>STATUS</strong>.</li>
                    <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Menginspeksi Log Eksternal (Payload API)</h5>
            <p class="text-muted mb-4">Jika pelanggan sudah transfer tapi merchant mengaku webhook tidak masuk, Anda dapat menginspeksi data mentah API secara langsung dari tabel ini.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Temukan baris transaksi yang dituju.</li>
                    <li class="mb-3">Lihat kolom <strong>STATUS</strong>. Jika statusnya <span class="badge badge-success">PAID</span> atau <span class="badge badge-success">SUCCESS</span>, badge tersebut dapat diklik.</li>
                    <li class="mb-3">Klik <strong>Badge Status</strong>. Jendela <em>External Log Details</em> akan muncul.</li>
                    <li class="mb-2">Gunakan <strong>Tab REQUEST</strong> untuk melihat JSON pembuatan VA, dan <strong>Tab RESPONSE</strong> untuk melihat payload callback saat pembayaran sukses.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Klik Badge</strong>
                    <p class="mb-0 text-muted small">Inspektur log eksternal hanya bisa diakses untuk transaksi berhasil yang mempunyai External ID. Badge transaksi PENDING atau EXPIRED tidak dapat diklik karena belum ada callback sukses yang bisa ditampilkan.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_vad_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Transaksi sudah dibayar tapi masih PENDING?
                </a>
                <div id="faq_id_vad_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Pelanggan mengirimkan bukti struk sah, namun dasbor tetap Pending.<br><br>
                        <strong>Resolusi:</strong> Hal ini wajar terjadi apabila callback dari bank tertunda atau gagal akibat timeout jaringan. Cek Log Eksternal; jika kosong, tunggu skrip cron dari agregator hulu menyinkronkan data secara otomatis.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_vad_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bisakah saya mengubah status PENDING jadi PAID manual?
                </a>
                <div id="faq_id_vad_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak. Transaksi VA Dynamic murni bergantung pada konfirmasi sistem perbankan hulu. Akses manual dimatikan untuk menghindari penipuan (fraud) dan mencegah selisih uang masuk (settlement).
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_vad_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Pelanggan bayar pas di menit kedaluwarsa tapi gagal
                </a>
                <div id="faq_id_vad_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Saldo rekening pelanggan terpotong, tapi invoice malah expired.<br><br>
                        <strong>Resolusi:</strong> Sistem bank seringkali memutus akses VA tepat di detik jatuh tempo. Jika nasabah transfer di detik terakhir, uang tersebut terlanjur masuk namun VA ditolak bank (mengendap sementara). Bank umumnya akan mengembalikan (auto-refund) dana tersebut dalam 1x24 jam.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
