<div id="module-ug-ewallet-dynamic" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The E-Wallet Dynamic module is a real-time monitor for dynamically generated e-wallet transactions (such as OVO, DANA, LinkAja). Unlike static payments, dynamic e-wallet transactions require the user to complete the payment via push notification or deep link within a specific timeframe.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">End-to-End E-Wallet Lifecycle</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Merchant App
                participant G as Gidi Gateway
                participant A as E-Wallet Provider
                participant C as Customer Phone
                
                M->>G: POST /EwalletDynamic/generate
                G->>A: Create E-Wallet Charge (Push/Redirect)
                A-->>G: Return Deep-Link or trigger Push
                G-->>M: Return Charge details
                
                alt Redirect Method
                    M->>C: Redirect User to Deep-link
                    C->>C: Opens E-Wallet App
                else Push Method
                    A->>C: Send Push Notification
                    C->>C: Tap Notification
                end
                
                C->>A: Authorize & Enter PIN
                A->>G: Realtime Webhook (PAID)
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
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">The exact server timestamp when the merchant called the API to trigger the e-wallet payment.</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB MERCHANT INFO</strong></td><td class="p-3 border-0">If applicable, the specific branch or sub-entity of the merchant that processed the transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL ID</strong></td><td class="p-3 border-0">The internal routing channel used (e.g., `ewallet_ovo`) and its description.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">The unique Invoice Number or Order ID provided by the merchant's own application.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">The reference ID given back to us by the upstream provider (e.g., Xendit) when they generated the charge.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">The exact gross amount the customer must authorize in their e-wallet app.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXPIRED DATE</strong></td><td class="p-3 border-0">The exact deadline. If the customer does not open their app and authorize the payment before this time, it fails.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            The current state of the transaction. The precise database values are:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Request received, waiting for aggregator response.</li>
                                <li><code>Created</code>: Payment link/request successfully generated, waiting for customer payment.</li>
                                <li><code>Paid</code>: Customer has successfully paid via their E-Wallet app.</li>
                                <li><code>Failed</code>: Request rejected by the upstream aggregator.</li>
                                <li><code>Expired</code>: Time limit exceeded without payment.</li>
                                <li><code>Cancel</code>: Transaction was manually cancelled by the merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Searching and Filtering Transactions</h3>
                <p class="text-muted mb-4">Use the built-in search and filters to track down specific e-wallet payments.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by Channel, Merchant, or ID...</em> box to instantly filter the table. If a customer provides a screenshot of a failed OVO payment, type their Invoice ID here.</li>
                        <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                        <li class="mb-3">Configure your parameters: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, or <strong>STATUS</strong>.</li>
                        <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Inspecting the External Log (API Payloads)</h3>
                <p class="text-muted mb-4">If a customer successfully paid in their e-wallet app but the merchant claims they did not receive the webhook callback, you can inspect the raw API data to verify the upstream response.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Find the transaction row you want to inspect.</li>
                        <li class="mb-3">Look at the <strong>STATUS</strong> column. If it shows <span class="badge badge-success">PAID</span> or <span class="badge badge-success">SUCCESS</span>, click the badge.</li>
                        <li class="mb-2">In the <em>External Log Details</em> modal, use the <strong>REQUEST Tab</strong> to view the e-wallet charge trigger payload, and the <strong>RESPONSE Tab</strong> to view the settlement confirmation payload.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mt-4 mb-5">
            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Clickable Badges Restriction</strong>
                <p class="mb-0 text-muted small">The external log inspector is only available for successful transactions that have an associated External ID. Badges for PENDING or EXPIRED transactions are not clickable because there is no success callback to display.</p>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Common Issues &amp; Troubleshooting</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 1: Why did the OVO push-to-pay notification fail to appear?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Push notifications depend on the customer's phone connectivity, battery optimization settings, and OVO app permissions. If the notification fails to pop up, the merchant should instruct the customer to open the OVO app directly and check their "Notifications/Inbox" tab to approve the payment.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: What is the difference between Push-to-Pay and Redirect?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Push-to-Pay sends a prompt directly to the customer's phone number (requires the phone number input during checkout). Redirect requires the customer to click a link on the merchant's site that opens the e-wallet app via a deep-link (no phone number required upfront).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Are e-wallet refunds supported?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Yes, partial and full refunds are generally supported for e-wallet transactions (unlike VA). The merchant can trigger a refund via the API, which will credit the balance back to the user's e-wallet app.</p>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul E-Wallet Dynamic adalah monitor real-time untuk transaksi e-wallet yang di-generate secara dinamis (seperti OVO, DANA, LinkAja). Tidak seperti pembayaran statis, transaksi e-wallet dinamis mengharuskan pengguna menyelesaikan pembayaran melalui notifikasi <em>push</em> atau <em>deep link</em> dalam batas waktu tertentu.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Siklus Hidup End-to-End E-Wallet</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Aplikasi Merchant
                participant G as Gidi Gateway
                participant A as Provider E-Wallet
                participant C as HP Pelanggan
                
                M->>G: POST /EwalletDynamic/generate
                G->>A: Buat Tagihan E-Wallet (Push/Redirect)
                A-->>G: Kembalikan Deep-link atau picu Push
                G-->>M: Kembalikan detail Tagihan
                
                alt Metode Redirect
                    M->>C: Redirect User ke Deep-link
                    C->>C: Membuka Aplikasi E-Wallet
                else Metode Push
                    A->>C: Kirim Notifikasi Push
                    C->>C: Buka Notifikasi
                end
                
                C->>A: Otorisasi & Masukkan PIN
                A->>G: Webhook Realtime (PAID)
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
                        <tr><td class="p-3 border-0"><strong>DATE REQUEST</strong></td><td class="p-3 border-0">Stempel waktu server saat merchant memanggil API untuk memicu pembayaran e-wallet.</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUB MERCHANT INFO</strong></td><td class="p-3 border-0">Jika ada, cabang atau sub-entitas merchant spesifik yang memproses transaksi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL ID</strong></td><td class="p-3 border-0">Channel perutean internal yang digunakan (misal: `ewallet_ovo`) beserta deskripsinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>MERCHANT TRANS ID</strong></td><td class="p-3 border-0">Nomor Invoice atau Order ID unik yang diberikan oleh aplikasi merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXTERNAL ID</strong></td><td class="p-3 border-0">ID referensi yang diberikan oleh penyedia hulu (misal: Xendit) saat mereka membuat tagihan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT</strong></td><td class="p-3 border-0">Jumlah kotor pasti yang wajib diotorisasi pelanggan di aplikasi e-wallet mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>EXPIRED DATE</strong></td><td class="p-3 border-0">Batas waktu pasti. Jika lewat dan pelanggan belum mengotorisasi di aplikasi, transaksi gagal.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">
                            Keadaan transaksi saat ini. Nilai presisi dari database adalah:
                            <ul class="mb-0 mt-2 pl-3">
                                <li><code>Pending</code>: Permintaan diterima, menunggu respons dari agregator.</li>
                                <li><code>Created</code>: Tautan/permintaan pembayaran berhasil dibuat, menunggu pelanggan melakukan pembayaran.</li>
                                <li><code>Paid</code>: Pelanggan telah berhasil melakukan pembayaran via aplikasi E-Wallet mereka.</li>
                                <li><code>Failed</code>: Permintaan ditolak oleh agregator hulu.</li>
                                <li><code>Expired</code>: Batas waktu pembayaran telah habis tanpa ada pembayaran.</li>
                                <li><code>Cancel</code>: Transaksi dibatalkan secara manual oleh merchant.</li>
                            </ul>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Mencari dan Memfilter Transaksi</h3>
                <p class="text-muted mb-4">Gunakan fitur pencarian dan filter untuk melacak pembayaran e-wallet yang spesifik.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by Channel, Merchant, or ID...</em> untuk memfilter tabel secara instan. Jika pelanggan mengirim bukti error, salin-tempel Invoice ID mereka di sini.</li>
                        <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                        <li class="mb-3">Saring parameter Anda: <strong>REQUEST DATE</strong>, <strong>MERCHANT</strong>, <strong>EXTERNAL CHANNEL</strong>, <strong>CHANNEL ID</strong>, atau <strong>STATUS</strong>.</li>
                        <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Menginspeksi Log Eksternal (Payload API)</h3>
                <p class="text-muted mb-4">Jika pelanggan berhasil membayar di aplikasi e-wallet tapi merchant merasa tidak mendapat webhook callback, Anda dapat menginspeksi data API mentah untuk memverifikasi respons hulu.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Cari baris transaksi yang ingin diperiksa.</li>
                        <li class="mb-3">Cek kolom <strong>STATUS</strong>. Jika terbaca <span class="badge badge-success">PAID</span> atau <span class="badge badge-success">SUCCESS</span>, klik badge tersebut.</li>
                        <li class="mb-2">Pada modal <em>External Log Details</em>, pakai <strong>Tab REQUEST</strong> untuk melihat payload pemicu tagihan e-wallet, dan <strong>Tab RESPONSE</strong> untuk melihat payload konfirmasi kesuksesan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mt-4 mb-5">
            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Klik Badge</strong>
                <p class="mb-0 text-muted small">Inspektur log eksternal hanya bisa diakses untuk transaksi berhasil yang mempunyai External ID. Badge transaksi PENDING atau EXPIRED tidak dapat diklik karena tidak ada callback sukses yang bisa ditampilkan.</p>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 1: Kenapa notifikasi push-to-pay OVO tidak muncul?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Notifikasi push sangat bergantung pada kualitas sinyal internet, setelan penghemat baterai ponsel, dan izin notifikasi aplikasi OVO pelanggan. Jika gagal muncul, merchant wajib menyuruh nasabah membuka aplikasi OVO secara manual lalu cek tab "Kotak Masuk / Notifikasi" untuk menyetujui pembayaran.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Apa bedanya metode Push-to-Pay dan Redirect?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Push-to-Pay mengirimkan prompt persetujuan langsung ke nomor HP yang diinput (wajib input nomor saat checkout). Sedangkan Redirect mengharuskan nasabah mengklik tombol "Bayar" di website yang akan otomatis membuka aplikasi e-wallet via <em>deep-link</em> (tidak perlu input nomor HP di awal).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Apakah transaksi e-wallet bisa di-refund?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Ya, tidak seperti VA, refund parsial dan penuh umumnya didukung oleh channel e-wallet. Merchant dapat memicu proses refund melalui pemanggilan API, dan dana akan dikembalikan langsung ke saldo e-wallet pengguna.</p>
        </div>

    </div>
</div>
