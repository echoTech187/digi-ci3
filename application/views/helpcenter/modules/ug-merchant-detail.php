<div id="module-ug-merchant-detail" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Merchant Detail dashboard provides a comprehensive 360-degree overview of a selected merchant account. It centralizes their live balances, transaction trends, configuration, and API settings into a single pane of glass.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Accessing Merchant Details</h3>
                <p class="text-muted mb-4">How to access and navigate the comprehensive 360-degree dashboard.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to <strong>Merchant Panel → Merchant Management</strong>.</li>
                        <li class="mb-3">Locate the target merchant in the list.</li>
                        <li class="mb-3">Click on the merchant's <strong>Business Name</strong> or use the action menu (⋮) and select <strong>Details</strong>.</li>
                        <li class="mb-3">Review the top summary cards to check the merchant's <strong>Live Balances</strong>.</li>
                        <li class="mb-2">Use the <strong>five specialized tabs</strong> at the bottom to explore Audit Overview, Merchant Information, Transaction History, Mutation Log, and Sub Accounts.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Direct Shortcut to Edit</strong>
                <p class="mb-0 text-muted small">Need to update the Callback URLs or change the merchant's API status? Click the <strong>Edit Merchant</strong> button at the top right of the dashboard to jump directly into the configuration form.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-wallet text-primary mr-2"></i> Live Balances Definitions</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Balance Type</th>
                        <th class="p-3 border-0">Description & Logic</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Total Balance</strong></td><td class="p-3 border-0">The absolute total of all funds belonging to the merchant in the system ledger.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Hold Balance</strong></td><td class="p-3 border-0">Funds currently escrowed or reserved. Typically, this holds funds during a pending Cashout disbursement until the bank confirms success.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Available Balance</strong></td><td class="p-3 border-0">The instantly spendable funds ready for withdrawal (<code>Total Balance</code> minus <code>Hold Balance</code>).</td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-layer-group text-primary mr-2"></i> Dashboard Navigation Tabs</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Tab Name</th>
                        <th class="p-3 border-0">Purpose & Data Displayed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Audit Overview</strong></td><td class="p-3 border-0">Interactive charts displaying transaction volume trends, success rates, and total charged fees over time. Includes date filters and a breakdown by payment method.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Merchant Information</strong></td><td class="p-3 border-0">Displays the Core Profile (business name, level, registration date, owner details) and the OpenAPI Config panel (Callback URLs, Whitelisted IPs, OpenAPI Status).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Transaction History</strong></td><td class="p-3 border-0">A raw, chronological table of all incoming and outgoing payments involving this specific merchant.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Mutation Log</strong></td><td class="p-3 border-0">The immutable ledger of balance movements. Essential for tracing exact fund additions and deductions.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Sub Accounts</strong></td><td class="p-3 border-0">Manage child sub-merchants registered underneath this parent account (useful for franchises or multi-branch merchants).</td></tr>
                </tbody>
            </table>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Available Balance is lower than Total Balance</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> This is normal. Some funds are currently held in escrow (Hold Balance) because the merchant has initiated a Cashout disbursement that is still pending or awaiting final settlement confirmation from the banking network.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Where can I find the merchant's Callback URLs?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Select the <strong>Merchant Information</strong> tab. On the right side, under the OpenAPI &amp; Integration Config panel, you will find the registered Callback URLs for VA, QRIS, and E-Wallet events.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>A recent transaction is missing from the history tab</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Ensure you do not have any active date filters restricting the view. If it is genuinely missing, the upstream payment provider may have failed to forward the webhook, or the customer abandoned the payment before completion.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why can't I edit merchant information directly from this dashboard?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> This dashboard is strictly read-only to prevent accidental modifications while auditing. To modify data, you must click the <strong>Edit Merchant</strong> button at the top right.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Are the metrics in the Audit Overview real-time?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Yes, the metrics and charts are typically updated in real-time. However, querying very large date ranges might take a few seconds to fully aggregate from historical transaction data.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Dasbor Detail Merchant memberikan tinjauan komprehensif 360 derajat atas akun merchant yang dipilih. Dasbor ini memusatkan saldo live, tren transaksi, konfigurasi, dan pengaturan API mereka ke dalam satu tampilan utama.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Mengakses Detail Merchant</h3>
                <p class="text-muted mb-4">Cara mengakses dan menavigasi dasbor 360 derajat yang komprehensif.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigasikan ke <strong>Merchant Panel → Merchant Management</strong>.</li>
                        <li class="mb-3">Cari target merchant di dalam daftar.</li>
                        <li class="mb-3">Klik pada <strong>Nama Bisnis</strong> merchant atau gunakan menu aksi (⋮) dan pilih <strong>Details</strong>.</li>
                        <li class="mb-3">Tinjau kartu ringkasan di bagian atas untuk memeriksa <strong>Live Balances</strong> (Saldo Live) merchant.</li>
                        <li class="mb-2">Gunakan <strong>lima tab khusus</strong> di bagian bawah untuk menjelajahi Audit Overview, Merchant Information, Transaction History, Mutation Log, dan Sub Accounts.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Jalan Pintas ke Edit</strong>
                <p class="mb-0 text-muted small">Perlu memperbarui URL Callback atau mengubah status API merchant? Klik tombol <strong>Edit Merchant</strong> di kanan atas dasbor untuk langsung masuk ke formulir konfigurasi.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-wallet text-primary mr-2"></i> Definisi Saldo Live</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Tipe Saldo</th>
                        <th class="p-3 border-0">Deskripsi & Logika</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Total Balance</strong></td><td class="p-3 border-0">Total absolut dari semua dana milik merchant di buku besar sistem.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Hold Balance</strong></td><td class="p-3 border-0">Dana yang saat ini dicadangkan (escrow). Biasanya menahan dana selama proses pencairan Cashout yang masih tertunda hingga bank mengonfirmasi keberhasilannya.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Available Balance</strong></td><td class="p-3 border-0">Dana yang bisa langsung digunakan/dicairkan (<code>Total Balance</code> dikurangi <code>Hold Balance</code>).</td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-layer-group text-primary mr-2"></i> Tab Navigasi Dasbor</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Nama Tab</th>
                        <th class="p-3 border-0">Tujuan & Data yang Ditampilkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Audit Overview</strong></td><td class="p-3 border-0">Grafik interaktif yang menampilkan tren volume transaksi, tingkat keberhasilan, dan total biaya yang dikenakan dari waktu ke waktu. Terdapat filter tanggal dan rincian per metode pembayaran.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Merchant Information</strong></td><td class="p-3 border-0">Menampilkan Profil Inti (nama bisnis, level, tanggal registrasi, detail pemilik) dan panel Konfigurasi OpenAPI (URL Callback, IP Whitelist, Status OpenAPI).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Transaction History</strong></td><td class="p-3 border-0">Tabel mentah kronologis dari semua pembayaran masuk dan keluar yang melibatkan merchant spesifik ini.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Mutation Log</strong></td><td class="p-3 border-0">Buku besar pergerakan saldo yang tidak dapat diubah (immutable). Sangat penting untuk melacak penambahan dan pemotongan dana secara pasti.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Sub Accounts</strong></td><td class="p-3 border-0">Mengelola sub-merchant turunan yang terdaftar di bawah akun induk ini (berguna untuk waralaba atau merchant multi-cabang).</td></tr>
                </tbody>
            </table>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Available Balance lebih rendah dari Total Balance</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Ini normal. Sebagian dana sedang ditahan (Hold Balance) karena merchant telah memulai pencairan Cashout yang masih berstatus pending atau sedang menunggu konfirmasi penyelesaian akhir dari jaringan perbankan.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Di mana saya bisa menemukan URL Callback merchant?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Pilih tab <strong>Merchant Information</strong>. Di sisi kanan, di bawah panel OpenAPI &amp; Integration Config, Anda akan menemukan URL Callback terdaftar untuk event VA, QRIS, dan E-Wallet.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Transaksi terbaru hilang dari tab history</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Pastikan Anda tidak memiliki filter tanggal aktif yang membatasi tampilan. Jika memang hilang, provider pembayaran upstream mungkin gagal mengirimkan webhook, atau pelanggan membatalkan pembayaran sebelum selesai.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa saya tidak bisa mengedit profil merchant langsung dari dasbor ini?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Dasbor ini bersifat *read-only* murni demi mencegah modifikasi yang tidak disengaja selama proses audit. Untuk mengubah data, Anda harus mengklik tombol <strong>Edit Merchant</strong> di kanan atas.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apakah metrik di Audit Overview bersifat real-time?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Ya, metrik dan grafik umumnya diperbarui secara *real-time*. Akan tetapi, memuat rentang tanggal yang sangat besar mungkin memakan waktu beberapa detik untuk mengagregasi data dari riwayat historis.</p>
        </div>
    </div>
</div>