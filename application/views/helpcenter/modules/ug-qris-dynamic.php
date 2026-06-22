<div id="module-ug-qris-dynamic" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The QRIS Dynamic module is a real-time ledger designed for tracking dynamically generated QR codes. Unlike static QRIS (which is printed once and fixed), a dynamic QRIS is generated uniquely per transaction with an exact amount. This page allows you to monitor the lifecycle of these QR codes from creation to settlement.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">End-to-End QRIS Lifecycle</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Merchant App
                participant G as Gidi Gateway
                participant A as Aggregator (Alto/NSP)
                participant C as Customer E-Wallet
                
                M->>G: POST /QrisMpm/generateDynamic
                G->>A: Forward Request
                A-->>G: Return QR String (000201010212...)
                G-->>M: Return QR String
                M->>M: Render QR Code on Checkout Page
                
                C->>M: Scans QR Code
                C->>A: Authorize Payment
                A->>G: Realtime Webhook (PAID)
                G->>G: Credit Merchant Balance (minus MDR)
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
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">The exact server timestamp when the merchant called the API to generate the QR code.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">The main merchant tenant that generated the QRIS (Format: <code>[ID] - Name</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">If applicable, the specific branch or sub-entity of the merchant that processed the transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">The unique Invoice Number or Order ID provided by the merchant's own application.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">The internal routing channel used (e.g., `qris_mpm`) and its description.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">The reference ID given back to us by the upstream provider (e.g., Alto) when they generated the QR string.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">The exact gross amount the customer must scan and pay.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            The current state of the QR Code. The precise database values are:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Request received, waiting for aggregator response.</li>
                                <li><code>Created</code>: QR Code successfully generated, waiting for customer payment.</li>
                                <li><code>Paid</code>: Customer has successfully paid the QR Code.</li>
                                <li><code>Failed</code>: Request rejected by the upstream aggregator.</li>
                                <li><code>Expired</code>: Time limit exceeded without payment.</li>
                                <li><code>Cancel</code>: QR Code was manually cancelled by the merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Search and Filter Transactions</h5>
            <p class="text-muted mb-4">Because dynamic QR codes are generated for every single checkout session, this table grows rapidly. Use the built-in tools to locate specific data.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by Channel, Merchant, or ID...</em> box to instantly filter the table. Extremely useful if a merchant gives you their `Merchant Trans ID`.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                    <li class="mb-3">Configure your parameters: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, or <strong>STATUS</strong>.</li>
                    <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Inspecting the External Log (API Payloads)</h5>
            <p class="text-muted mb-4">If a transaction succeeds but the merchant claims their webhook callback failed, you can inspect the raw API data directly from this table.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Find the transaction row you want to inspect.</li>
                    <li class="mb-3">Look at the <strong>STATUS</strong> column. If it shows <span class="badge badge-success">PAID</span> or <span class="badge badge-success">SUCCESS</span>, click the badge.</li>
                    <li class="mb-2">In the <em>External Log Details</em> modal, use the <strong>REQUEST Tab</strong> to view the JSON sent to generate the QR, and the <strong>RESPONSE Tab</strong> for the settlement callback.</li>
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
                <a href="#faq_en_qrd_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Customer scanned the QRIS but it says "Invalid QR"
                </a>
                <div id="faq_en_qrd_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolution:</strong> Dynamic QRIS codes have a hard expiration time. If the customer attempts to scan an expired QR code, their banking or e-wallet app will reject it immediately. They must go back to the merchant and generate a new checkout session.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qrd_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can a customer edit the payment amount?
                </a>
                <div id="faq_en_qrd_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No. Dynamic QRIS has the exact invoice amount securely embedded into the QR string. When scanned, the customer's e-wallet will lock the input field to that specific amount, preventing underpayments or overpayments.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qrd_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Is the MDR fee deducted instantly?
                </a>
                <div id="faq_en_qrd_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Yes. QRIS transactions use a nett-settlement model. The moment a transaction is marked PAID, the merchant's Available Balance will be credited with the gross amount minus the applicable MDR and any fixed platform fees.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul QRIS Dynamic adalah buku besar real-time yang dirancang khusus untuk melacak kode QR yang di-generate secara dinamis. Tidak seperti QRIS statis (yang dicetak mati), QRIS dinamis dibuat unik per transaksi dengan jumlah nominal yang pasti. Halaman ini memungkinkan Anda memantau seluruh siklus hidup kode QR ini dari pembuatan hingga pembayaran sukses.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Siklus Hidup End-to-End QRIS</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Aplikasi Merchant
                participant G as Gidi Gateway
                participant A as Agregator (Alto/NSP)
                participant C as E-Wallet Pelanggan
                
                M->>G: POST /QrisMpm/generateDynamic
                G->>A: Teruskan Permintaan
                A-->>G: Kembalikan String QR (000201010212...)
                G-->>M: Kembalikan String QR
                M->>M: Render QR Code di Halaman Checkout
                
                C->>M: Scan QR Code
                C->>A: Otorisasi Pembayaran
                A->>G: Webhook Realtime (PAID)
                G->>G: Tambahkan Saldo Merchant (dikurangi MDR)
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
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">Stempel waktu server saat merchant memanggil API untuk membuat kode QR.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT INFO</strong></td><td class="p-3 border-0">Tenant merchant utama yang membuat QRIS (Format: <code>[ID] - Nama</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB-MERCHANT INFO</strong></td><td class="p-3 border-0">Jika ada, cabang atau sub-entitas spesifik merchant yang memproses transaksi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">Nomor Invoice atau Order ID unik yang diberikan oleh aplikasi milik merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL</strong></td><td class="p-3 border-0">Channel perutean internal yang digunakan (misal: `qris_mpm`) beserta deskripsinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">ID referensi yang dikembalikan oleh penyedia hulu (misal: Alto) saat QR dibuat.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">Jumlah kotor pasti yang wajib dipindai dan dibayar oleh pelanggan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            Keadaan Kode QR saat ini. Nilai presisi dari database adalah:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Permintaan diterima, menunggu respons dari agregator.</li>
                                <li><code>Created</code>: Kode QR berhasil dibuat, menunggu pelanggan melakukan pembayaran.</li>
                                <li><code>Paid</code>: Pelanggan telah berhasil membayar Kode QR.</li>
                                <li><code>Failed</code>: Permintaan ditolak oleh agregator hulu.</li>
                                <li><code>Expired</code>: Batas waktu pembayaran telah habis tanpa ada pembayaran.</li>
                                <li><code>Cancel</code>: Kode QR dibatalkan secara manual oleh merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Mencari dan Memfilter Transaksi</h5>
            <p class="text-muted mb-4">Karena kode QR dinamis dibuat untuk setiap sesi checkout satuan, tabel ini akan berkembang dengan cepat. Gunakan alat bawaan untuk menemukan data dengan akurat.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by Channel, Merchant, or ID...</em> untuk memfilter tabel secara instan. Sangat berguna bila merchant memberi Anda `Merchant Trans ID` mereka.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                    <li class="mb-3">Saring parameter Anda: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, atau <strong>STATUS</strong>.</li>
                    <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Inspecting External Log -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-success mr-2"></i> 2. Menginspeksi Log Eksternal (Payload API)</h5>
            <p class="text-muted mb-4">Jika transaksi sukses namun merchant komplain webhook mereka gagal, Anda dapat menginspeksi data mentah API secara langsung untuk verifikasi.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari baris transaksi yang dituju.</li>
                    <li class="mb-3">Cek kolom <strong>STATUS</strong>. Jika terbaca <span class="badge badge-success">PAID</span> atau <span class="badge badge-success">SUCCESS</span>, klik badge tersebut.</li>
                    <li class="mb-2">Pada modal <em>External Log Details</em>, pakai <strong>Tab REQUEST</strong> untuk melihat JSON pembuatan QR, dan <strong>Tab RESPONSE</strong> untuk melihat payload callback saat dibayar.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Klik Badge</strong>
                    <p class="mb-0 text-muted small">Inspektur log eksternal murni ditujukan untuk transaksi berhasil yang mempunyai External ID. Badge untuk transaksi PENDING/EXPIRED dimatikan karena belum ada callback sukses yang dicatat.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrd_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Pelanggan scan QRIS tapi muncul "QR Tidak Valid"
                </a>
                <div id="faq_id_qrd_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolusi:</strong> Kode QRIS Dinamis memiliki batas waktu yang ketat. Jika pelanggan mencoba scan kode QR yang sudah expired, aplikasi perbankan mereka otomatis akan menolaknya. Pelanggan wajib mengulang checkout di website merchant.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrd_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bisakah pelanggan mengedit nominal bayar?
                </a>
                <div id="faq_id_qrd_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak. QRIS Dinamis memiliki jumlah tagihan yang tertanam permanen di dalam string QR tersebut. Saat di-scan, aplikasi e-wallet pelanggan akan mengunci nominal tersebut, mencegah kurang bayar atau lebih bayar.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qrd_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Apakah biaya MDR langsung dipotong (nett-settled)?
                </a>
                <div id="faq_id_qrd_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ya. Seluruh transaksi QRIS kita menggunakan model nett-settlement. Saat transaksi berstatus PAID, Saldo Aktif merchant otomatis bertambah sebesar nominal kotor dikurangi biaya MDR dan biaya layanan sistem.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
