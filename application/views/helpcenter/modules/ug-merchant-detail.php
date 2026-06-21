<div id="module-ug-merchant-detail" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Merchant Detail dashboard provides a comprehensive 360-degree overview of a selected merchant account. It centralizes their live balances, transaction trends, configuration, and API settings into a single pane of glass.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Live Balances</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Balance Type</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Total Balance</strong></td><td class="p-3 border-0">The absolute total of all funds belonging to the merchant in the system ledger.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Hold Balance</strong></td><td class="p-3 border-0">Funds currently escrowed or reserved. Typically, this holds funds during a pending Cashout disbursement until the bank confirms success.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Available Balance</strong></td><td class="p-3 border-0">The instantly spendable funds ready for withdrawal (<code>Total Balance</code> minus <code>Hold Balance</code>).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Navigation Tabs -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-layer-group text-primary mr-2"></i> 1. Dashboard Navigation Tabs</h5>
            <p class="text-muted mb-4">The bottom half of the dashboard is organized into five specialized tabs to help you manage the merchant effectively.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Audit Overview:</strong> Interactive charts displaying transaction volume trends, success rates, and total charged fees over time. Includes date filters and a breakdown by payment method.</li>
                    <li class="mb-3"><strong>Merchant Information:</strong> Displays the Core Profile (business name, level, registration date, owner details) and the OpenAPI Config panel (Callback URLs, Whitelisted IPs, OpenAPI Status).</li>
                    <li class="mb-3"><strong>Transaction History:</strong> A raw, chronological table of all incoming and outgoing payments involving this specific merchant.</li>
                    <li class="mb-3"><strong>Mutation Log:</strong> The immutable ledger of balance movements. Essential for tracing exact fund additions and deductions.</li>
                    <li class="mb-2"><strong>Sub Accounts:</strong> Manage child sub-merchants registered underneath this parent account (useful for franchises or multi-branch merchants).</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Direct Shortcut to Edit</strong>
                    <p class="mb-0 text-muted small">Need to update the Callback URLs or change the merchant's API status? Click the <strong>Edit Merchant</strong> button at the top right of the dashboard to jump directly into the configuration form.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_md_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Available Balance is lower than Total Balance
                </a>
                <div id="faq_en_md_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The merchant complains their spendable money doesn't match their gross total.<br><br>
                        <strong>Resolution:</strong> This is normal. Some funds are currently held in escrow (Hold Balance) because the merchant has initiated a Cashout disbursement that is still pending or awaiting final settlement confirmation from the banking network.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_md_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Where can I find the merchant's Callback URLs?
                </a>
                <div id="faq_en_md_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolution:</strong> Select the <strong>Merchant Information</strong> tab. On the right side, under the OpenAPI &amp; Integration Config panel, you will find the registered Callback URLs for VA, QRIS, and E-Wallet events.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_md_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: A recent transaction is missing from the history tab
                </a>
                <div id="faq_en_md_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The merchant claims a payment was made but it does not appear in the Transaction History tab.<br><br>
                        <strong>Resolution:</strong> Ensure you do not have any active date filters restricting the view. If it is genuinely missing, the upstream payment provider may have failed to forward the webhook, or the customer abandoned the payment before completion.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Dasbor Detail Merchant memberikan tinjauan komprehensif 360 derajat atas akun merchant yang dipilih. Dasbor ini memusatkan saldo live, tren transaksi, konfigurasi, dan pengaturan API mereka ke dalam satu tampilan utama.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Saldo Live</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Tipe Saldo</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Total Balance</strong></td><td class="p-3 border-0">Total absolut dari semua dana milik merchant di buku besar sistem.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Hold Balance</strong></td><td class="p-3 border-0">Dana yang saat ini dicadangkan (escrow). Biasanya menahan dana selama proses pencairan Cashout yang masih tertunda hingga bank mengonfirmasi keberhasilannya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Available Balance</strong></td><td class="p-3 border-0">Dana yang bisa langsung digunakan/dicairkan (<code>Total Balance</code> dikurangi <code>Hold Balance</code>).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Navigation Tabs -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-layer-group text-primary mr-2"></i> 1. Tab Navigasi Dasbor</h5>
            <p class="text-muted mb-4">Bagian bawah dasbor diatur ke dalam lima tab khusus untuk membantu Anda mengelola merchant secara efektif.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Audit Overview:</strong> Grafik interaktif yang menampilkan tren volume transaksi, tingkat keberhasilan, dan total biaya yang dikenakan dari waktu ke waktu. Terdapat filter tanggal dan rincian per metode pembayaran.</li>
                    <li class="mb-3"><strong>Merchant Information:</strong> Menampilkan Profil Inti (nama bisnis, level, tanggal registrasi, detail pemilik) dan panel Konfigurasi OpenAPI (URL Callback, IP Whitelist, Status OpenAPI).</li>
                    <li class="mb-3"><strong>Transaction History:</strong> Tabel mentah kronologis dari semua pembayaran masuk dan keluar yang melibatkan merchant spesifik ini.</li>
                    <li class="mb-3"><strong>Mutation Log:</strong> Buku besar pergerakan saldo yang tidak dapat diubah (immutable). Sangat penting untuk melacak penambahan dan pemotongan dana secara pasti.</li>
                    <li class="mb-2"><strong>Sub Accounts:</strong> Mengelola sub-merchant turunan yang terdaftar di bawah akun induk ini (berguna untuk waralaba atau merchant multi-cabang).</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Jalan Pintas ke Edit</strong>
                    <p class="mb-0 text-muted small">Perlu memperbarui URL Callback atau mengubah status API merchant? Klik tombol <strong>Edit Merchant</strong> di kanan atas dasbor untuk langsung masuk ke formulir konfigurasi.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_md_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Available Balance lebih rendah dari Total Balance
                </a>
                <div id="faq_id_md_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Merchant mengeluh uang yang bisa mereka pakai tidak cocok dengan total bruto mereka.<br><br>
                        <strong>Resolusi:</strong> Ini normal. Sebagian dana sedang ditahan (Hold Balance) karena merchant telah memulai pencairan Cashout yang masih berstatus pending atau sedang menunggu konfirmasi penyelesaian akhir dari jaringan perbankan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_md_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Di mana saya bisa menemukan URL Callback merchant?
                </a>
                <div id="faq_id_md_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolusi:</strong> Pilih tab <strong>Merchant Information</strong>. Di sisi kanan, di bawah panel OpenAPI &amp; Integration Config, Anda akan menemukan URL Callback terdaftar untuk event VA, QRIS, dan E-Wallet.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_md_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Transaksi terbaru hilang dari tab history
                </a>
                <div id="faq_id_md_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Merchant mengklaim ada pembayaran tetapi tidak muncul di tab Transaction History.<br><br>
                        <strong>Resolusi:</strong> Pastikan Anda tidak memiliki filter tanggal aktif yang membatasi tampilan. Jika memang hilang, provider pembayaran upstream mungkin gagal mengirimkan webhook, atau pelanggan membatalkan pembayaran sebelum selesai.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>