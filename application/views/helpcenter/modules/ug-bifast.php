<div id="module-ug-bifast" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The BI-FAST module gives the operations team a real-time monitoring dashboard for all interbank disbursements. BI-FAST is Bank Indonesia's national retail payment infrastructure — providing 24/7, sub-second, and irrevocable fund transfers to any participating bank account in Indonesia.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:28%">UI Element</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Transaction Table</strong></td><td class="p-3 border-0">Live feed of all BI-FAST disbursements sorted by most recent. Includes originating merchant, beneficiary account, amount, and current status.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Status Badge</strong></td><td class="p-3 border-0"><span class="badge badge-success">SUCCESS</span> Funds credited to beneficiary. <span class="badge badge-warning">PENDING</span> Awaiting BI-FAST switch confirmation. <span class="badge badge-danger">FAILED</span> Transfer rejected (invalid account, insufficient balance, or BI-Fast network error).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Filters Panel</strong></td><td class="p-3 border-0">Filter by REQUEST DATE, MERCHANT, Destination Bank, and STATUS to narrow down the view for specific investigations.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Row Detail</strong></td><td class="p-3 border-0">Click any row to view the full detail: Reference ID, beneficiary name, account number, channel, fee mode, timestamps, and the raw error code (for FAILED transactions).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Download Excel</strong></td><td class="p-3 border-0">Asynchronous bulk export of filtered BI-FAST records. Available from the Download Report menu when ready.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Monitoring Disbursements — Step-by-Step</h3>
                <p class="text-muted mb-4">Use this module as your primary operations tool for tracking Cashout disbursements executed via the BI-FAST network.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to <strong>Transactions → BI-FAST</strong> from the sidebar.</li>
                        <li class="mb-3">Review the <strong>Status</strong> column for real-time tracking:
                            <ul class="mt-2">
                                <li class="mb-1"><span class="badge badge-success">SUCCESS</span> — Funds instantly credited to the beneficiary. No further action needed.</li>
                                <li class="mb-1"><span class="badge badge-warning">PENDING</span> — Waiting for core-banking confirmation from the Bank Indonesia BI-FAST switch. This is normal for up to 2–5 minutes.</li>
                                <li><span class="badge badge-danger">FAILED</span> — Transfer rejected. Click the row to view the raw error code and reason before initiating a retry.</li>
                            </ul>
                        </li>
                        <li class="mb-3">If a transfer is stuck in <strong>PENDING</strong> for more than 10 minutes, click the row to get the <code>referenceId</code> and escalate to your payment gateway provider for manual investigation with Bank Indonesia.</li>
                        <li class="mb-3">For <strong>FAILED</strong> transfers: confirm the failure reason from the detail view (e.g., <code>ACCOUNT_NOT_FOUND</code>, <code>INSUFFICIENT_BALANCE</code>). The merchant must submit a new Cashout request — the failed transaction's funds are automatically returned to their Available Balance.</li>
                        <li class="mb-2">Use the <strong>Filters Panel</strong> to configure parameters like <strong>MERCHANT</strong> or <strong>REQUEST DATE</strong> for reconciliation reporting. Then click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                    </ol>
                </div>

                <div class="doc-callout callout-note shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Automatic Account Inquiry (Security Validation)</strong>
                        <p class="mb-0 text-muted small">Before a BI-FAST disbursement is executed, the API performs an automatic <strong>Account Inquiry</strong> to validate the beneficiary's name against the bank's records. If the name does not match (or the account is closed/blocked), the system automatically fails the transaction <em>before</em> any money moves. This prevents transfers to wrong or fraudulent accounts.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">BI-FAST Transaction Limits</h3>
                <div class="table-responsive shadow-sm mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:35%">Limit Type</th>
                                <th class="p-3 border-0">Default Value</th>
                                <th class="p-3 border-0">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0">Maximum per transaction</td><td class="p-3 border-0">Rp 250,000,000</td><td class="p-3 border-0">Set by Bank Indonesia's BI-FAST regulation. Cannot be increased.</td></tr>
                            <tr><td class="p-3 border-0">Minimum per transaction</td><td class="p-3 border-0">Rp 1,000</td><td class="p-3 border-0">Platform minimum. Can be configured per merchant in Cashout Settings.</td></tr>
                            <tr><td class="p-3 border-0">Daily limit per merchant</td><td class="p-3 border-0">Configurable</td><td class="p-3 border-0">Set by admin in the merchant's Cashout configuration. Default is unlimited unless explicitly restricted.</td></tr>
                            <tr><td class="p-3 border-0">Availability</td><td class="p-3 border-0">24/7/365</td><td class="p-3 border-0">BI-FAST operates around the clock, including weekends and national holidays.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Common Issues &amp; What To Do</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 1: Can a FAILED BI-FAST transaction be retried directly?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> No. A failed transaction ID cannot be reused. The merchant must submit a completely new Cashout request with a fresh <code>requestId</code> and <code>transactionId</code>. The funds from the failed transaction are <strong>automatically returned</strong> to the merchant's Available Balance within seconds — they do not need to wait for a manual refund.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: Why is a transaction stuck in PENDING for hours?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Answer:</strong> A prolonged PENDING status indicates severe network congestion or a timeout at the Bank Indonesia central switching infrastructure. The gateway automatically runs background status polling every few minutes until a final state is reached.<br><br>
                <strong>Escalation Path:</strong> If PENDING exceeds 30 minutes, collect the <code>referenceId</code> from the transaction detail and submit a dispute ticket to your payment gateway provider. They will trace the transaction at the BI-FAST interbank switch level.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Is there a daily limit for BI-FAST transfers?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Per Bank Indonesia regulation, the maximum per single BI-FAST transaction is <strong>Rp 250,000,000</strong>. There is no BI-FAST-imposed daily aggregate limit, but you can configure stricter daily limits per merchant via the <strong>Cashout Settings</strong> in the merchant profile. This is useful for risk management on high-volume disbursement accounts.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul BI-FAST memberikan tim operasional dasbor pemantauan real-time untuk semua pencairan antarbank. BI-FAST adalah infrastruktur pembayaran ritel nasional Bank Indonesia — menyediakan transfer dana 24/7, sub-detik, dan tidak dapat dibatalkan ke rekening bank peserta mana pun di Indonesia.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Antarmuka (UI)</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:28%">Elemen UI</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Tabel Transaksi</strong></td><td class="p-3 border-0">Feed langsung semua pencairan BI-FAST diurutkan berdasarkan yang terbaru. Mencakup merchant asal, rekening penerima, nominal, dan status saat ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Badge Status</strong></td><td class="p-3 border-0"><span class="badge badge-success">SUCCESS</span> Dana dikreditkan ke penerima. <span class="badge badge-warning">PENDING</span> Menunggu konfirmasi switch BI-FAST. <span class="badge badge-danger">FAILED</span> Transfer ditolak (rekening tidak valid, saldo tidak cukup, atau error jaringan BI-Fast).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Panel Filters</strong></td><td class="p-3 border-0">Filter berdasarkan REQUEST DATE, MERCHANT, Bank Tujuan, dan STATUS untuk mempersempit tampilan investigasi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Detail Baris</strong></td><td class="p-3 border-0">Klik baris mana pun untuk melihat detail lengkap: Reference ID, nama penerima, nomor rekening, channel, mode biaya, timestamp, dan kode error mentah (untuk transaksi FAILED).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Download Excel</strong></td><td class="p-3 border-0">Ekspor massal asinkron dari record BI-FAST yang difilter. Tersedia dari menu Download Report saat sudah siap.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Memantau Pencairan Dana — Langkah-demi-Langkah</h3>
                <p class="text-muted mb-4">Gunakan modul ini sebagai alat operasional utama Anda untuk melacak pencairan Cashout yang dieksekusi melalui jaringan BI-FAST.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigasikan ke <strong>Transactions → BI-FAST</strong> dari sidebar.</li>
                        <li class="mb-3">Pantau kolom <strong>Status</strong> untuk pelacakan real-time:
                            <ul class="mt-2">
                                <li class="mb-1"><span class="badge badge-success">SUCCESS</span> — Dana langsung dikreditkan ke penerima. Tidak ada tindakan lebih lanjut yang diperlukan.</li>
                                <li class="mb-1"><span class="badge badge-warning">PENDING</span> — Menunggu konfirmasi core-banking dari switch BI-FAST Bank Indonesia. Normal hingga 2–5 menit.</li>
                                <li><span class="badge badge-danger">FAILED</span> — Transfer ditolak. Klik baris untuk melihat kode error mentah dan alasannya sebelum memulai retry.</li>
                            </ul>
                        </li>
                        <li class="mb-3">Jika transfer tersangkut di <strong>PENDING</strong> lebih dari 10 menit, klik baris untuk mendapatkan <code>referenceId</code> dan eskalasikan ke penyedia payment gateway Anda untuk investigasi manual dengan Bank Indonesia.</li>
                        <li class="mb-3">Untuk transfer <strong>FAILED</strong>: konfirmasi alasan kegagalan dari tampilan detail (mis. <code>ACCOUNT_NOT_FOUND</code>, <code>INSUFFICIENT_BALANCE</code>). Merchant harus mengajukan request Cashout baru — dana dari transaksi yang gagal otomatis dikembalikan ke Saldo Tersedia mereka.</li>
                        <li class="mb-2">Gunakan <strong>Panel Filters</strong> untuk mengatur parameter seperti <strong>MERCHANT</strong> atau <strong>REQUEST DATE</strong> untuk pelaporan rekonsiliasi. Lalu klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                    </ol>
                </div>

                <div class="doc-callout callout-note shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Inquiry Rekening Otomatis (Validasi Keamanan)</strong>
                        <p class="mb-0 text-muted small">Sebelum pencairan BI-FAST dieksekusi, API melakukan <strong>Account Inquiry</strong> otomatis untuk memvalidasi nama penerima dengan catatan bank. Jika nama tidak cocok (atau rekening ditutup/diblokir), sistem otomatis menggagalkan transaksi <em>sebelum</em> uang bergerak. Ini mencegah transfer ke rekening yang salah atau akun penipuan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Batas Transaksi BI-FAST</h3>
                <div class="table-responsive shadow-sm mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:35%">Tipe Batas</th>
                                <th class="p-3 border-0">Nilai Default</th>
                                <th class="p-3 border-0">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0">Maksimum per transaksi</td><td class="p-3 border-0">Rp 250.000.000</td><td class="p-3 border-0">Ditetapkan oleh regulasi BI-FAST Bank Indonesia. Tidak dapat ditingkatkan.</td></tr>
                            <tr><td class="p-3 border-0">Minimum per transaksi</td><td class="p-3 border-0">Rp 1.000</td><td class="p-3 border-0">Minimum platform. Dapat dikonfigurasi per merchant di Cashout Settings.</td></tr>
                            <tr><td class="p-3 border-0">Batas harian per merchant</td><td class="p-3 border-0">Dapat Dikonfigurasi</td><td class="p-3 border-0">Ditetapkan oleh admin di konfigurasi Cashout merchant. Default tidak terbatas kecuali dibatasi secara eksplisit.</td></tr>
                            <tr><td class="p-3 border-0">Ketersediaan</td><td class="p-3 border-0">24/7/365</td><td class="p-3 border-0">BI-FAST beroperasi sepanjang waktu, termasuk akhir pekan dan hari libur nasional.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 1: Bisakah transaksi BI-FAST yang GAGAL langsung dicoba ulang?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Tidak. ID transaksi yang gagal tidak dapat digunakan ulang. Merchant harus mengajukan request Cashout baru dengan <code>requestId</code> dan <code>transactionId</code> yang baru. Dana dari transaksi yang gagal <strong>otomatis dikembalikan</strong> ke Saldo Tersedia merchant dalam hitungan detik — mereka tidak perlu menunggu refund manual.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Mengapa transaksi tersangkut di PENDING berjam-jam?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Jawaban:</strong> Status PENDING berkepanjangan menunjukkan kemacetan jaringan parah atau timeout di infrastruktur switching pusat Bank Indonesia. Gateway secara otomatis menjalankan polling status latar belakang setiap beberapa menit hingga status akhir tercapai.<br><br>
                <strong>Jalur Eskalasi:</strong> Jika PENDING melebihi 30 menit, kumpulkan <code>referenceId</code> dari detail transaksi dan ajukan tiket dispute ke penyedia payment gateway Anda. Mereka akan melacak transaksi di tingkat switch antarbank BI-FAST.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Apakah ada batas harian untuk transfer BI-FAST?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Sesuai regulasi Bank Indonesia, maksimum per transaksi BI-FAST tunggal adalah <strong>Rp 250.000.000</strong>. Tidak ada batas agregat harian yang diberlakukan BI-FAST, namun Anda dapat mengkonfigurasi batas harian yang lebih ketat per merchant melalui <strong>Cashout Settings</strong> di profil merchant. Ini berguna untuk manajemen risiko pada akun pencairan volume tinggi.</p>
        </div>
    </div>
</div>