<div id="module-ug-external-balance-log" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The External Balance Log page provides the finance team with a daily snapshot comparing your internal platform ledger against the actual balances reported by each external payment aggregator. This is the first line of defense for detecting delayed <span class="hc-tooltip" data-tooltip="The process of transferring funds from user to merchant">settlements</span>, <span class="hc-tooltip" data-tooltip="The difference between actual cash and recorded balance">float</span> discrepancies, or <span class="hc-tooltip" data-tooltip="Process of matching internal records with external statements">reconciliation</span> mismatches between your internal books and upstream reality.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Column Reference</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Column</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Snapshot Date</strong></td><td class="p-3 border-0">The date and time when the balance snapshot was captured by the background cron job (typically end-of-day).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Gidi (Internal)</strong></td><td class="p-3 border-0">The total aggregate balance recorded in our own internal ledger system at the time of the snapshot. This is the sum of all merchant settlement balances.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Paylabs</strong></td><td class="p-3 border-0">The balance reported by the Paylabs aggregator API at the time of the snapshot. Represents funds held by Paylabs on behalf of the platform.</td></tr>
                        <tr><td class="p-3 border-0"><strong>GV (GoPay / OVO)</strong></td><td class="p-3 border-0">The balance reported by the GV aggregator API. Used for QRIS and e-wallet channels routed via GV.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Faspay (OVO)</strong></td><td class="p-3 border-0">The balance reported by the Faspay aggregator API, specifically for OVO transactions routed through them.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Faspay (ShopeePay)</strong></td><td class="p-3 border-0">The balance reported by the Faspay aggregator API, specifically for ShopeePay transactions routed through them.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Difference</strong></td><td class="p-3 border-0">The calculated variance between Internal (Gidi) and the sum of all external aggregator balances. A non-zero value here requires investigation.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Remark</strong></td><td class="p-3 border-0">System-generated note. If an aggregator's API was unreachable during the snapshot, this will show <code>FETCH_ERROR</code>. The last known balance is used in that case.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Audit Steps -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> 1. Auditing Balance Snapshots — Step-by-Step</h5>
            <p class="text-muted mb-4">This tool helps the finance team spot discrepancies between internal records and upstream realities. Use it as a daily reconciliation check.</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A[Cron Job<br>Daily Trigger] --> B[Calculate Total Internal<br>Merchant Balances]
                        A --> C[Fetch External Balances<br>via APIs]
                        C --> D[Paylabs API]
                        C --> E[GV API]
                        C --> F[Faspay API]
                        B --> G[Reconciliation Engine]
                        D --> G
                        E --> G
                        F --> G
                        G --> H{Internal == External?}
                        H -- Yes --> I[Log Snapshot<br>Difference = 0]
                        H -- No --> J[Log Snapshot<br>Difference > 0]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Finance &amp; Treasury → External Balance Log</strong>.</li>
                    <li class="mb-3">View the most recent snapshot row. The <strong>Gidi</strong> column shows our internal system's total aggregate balance.</li>
                    <li class="mb-3">Compare it against the <strong>Paylabs</strong>, <strong>GV</strong>, and <strong>Faspay</strong> columns, which show the actual aggregator-side balances.</li>
                    <li class="mb-3">Check the <strong>Difference</strong> column. A value close to zero is expected. A large positive difference (Internal > External) suggests delayed settlement — the aggregator hasn't cleared funds to our account yet. A negative difference (External > Internal) may indicate an over-credit or double-settlement issue.</li>
                    <li class="mb-3">Use the <strong>Search logs...</strong> box to filter for a specific date range (format <code>YYYY-MM-DD</code>) for historical reconciliation.</li>
                    <li class="mb-2">If a discrepancy is found, cross-reference with the <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-balance-logs'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Balance Logs</a> module for individual transaction-level verification on that specific date.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Read-Only Data — No Manual Edits</strong>
                    <p class="mb-0 text-muted small">This page is a reporting snapshot only. You <strong>cannot</strong> manually edit these balance figures. All data is fetched from aggregator APIs and stored by the automated background process. If you find a massive discrepancy, investigate at the individual transaction level in the <strong>Balance Logs</strong> and <strong>Transaction History</strong> modules for that specific date.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Reconciliation Guide -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-balance-scale text-primary mr-2"></i> 2. Interpreting Discrepancies</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:35%">Scenario</th>
                            <th class="p-3 border-0">Likely Cause</th>
                            <th class="p-3 border-0">Action Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0">Internal slightly higher than External</td><td class="p-3 border-0">Delayed settlement — aggregator hasn't completed their clearing cycle yet.</td><td class="p-3 border-0">Wait 1 business day. Usually self-corrects overnight.</td></tr>
                        <tr><td class="p-3 border-0">Internal significantly lower than External</td><td class="p-3 border-0">Possible over-settlement from aggregator, or funds received that weren't captured in internal ledger.</td><td class="p-3 border-0">Investigate individual transactions on that date. Escalate to finance team.</td></tr>
                        <tr><td class="p-3 border-0">Aggregator column shows <code>0</code> or <code>N/A</code></td><td class="p-3 border-0">Aggregator API was unreachable during the cron snapshot (API downtime, credential expiry).</td><td class="p-3 border-0">Check the <strong>Remark</strong> column for <code>FETCH_ERROR</code>. Verify aggregator API credentials are still valid.</td></tr>
                        <tr><td class="p-3 border-0">No snapshot for today</td><td class="p-3 border-0">Background cron job did not run (server cron issue or server downtime).</td><td class="p-3 border-0">Contact the engineering/DevOps team to verify cron job health and trigger a manual snapshot run.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_fg_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: External balance is lower than our internal balance
                </a>
                <div id="faq_en_fg_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This is most commonly a <strong>delayed settlement</strong>. We have credited the merchant in our internal ledger (because the payment was confirmed by the payment provider), but the upstream aggregator hasn't fully cleared the funds to their API balance reporting endpoint yet. This typically resolves within 1 business day during the aggregator's nightly clearing cycle.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_fg_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can I trigger a snapshot manually?
                </a>
                <div id="faq_en_fg_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No — snapshots are generated automatically via a background cron job at specific times each day (usually at end-of-day, e.g., 23:00). There is no manual trigger button in the UI.<br><br>
                        If a snapshot is urgently needed outside the scheduled time (e.g., for an audit), the engineering team can run the snapshot cron manually via the CLI: <code>php index.php cli/financialgateway/snapshot</code>. Contact your DevOps team for this.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_fg_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: An aggregator column shows 0 or FETCH_ERROR in today's snapshot
                </a>
                <div id="faq_en_fg_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This means the background cron could not reach that specific aggregator's balance API when the snapshot ran — likely due to the aggregator's API being down, or the API credentials (API Key / Secret) having expired.<br><br>
                        <strong>Resolution:</strong> Check the aggregator's system status page. If they are operational, verify the API credentials in the server configuration are still valid and have not been rotated. Update them if necessary and wait for the next scheduled snapshot.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman External Balance Log memberikan tim keuangan snapshot harian yang membandingkan buku besar platform internal Anda dengan saldo aktual yang dilaporkan oleh setiap agregator pembayaran eksternal. Ini adalah garis pertahanan pertama untuk mendeteksi <span class="hc-tooltip" data-tooltip="Proses pemindahan dana aktual dari pihak ketiga ke kita">settlement</span> yang tertunda, selisih <span class="hc-tooltip" data-tooltip="Selisih antara saldo tunai aktual vs saldo tercatat">float</span>, atau ketidaksesuaian <span class="hc-tooltip" data-tooltip="Pencocokan data antara internal dan eksternal">rekonsiliasi</span> antara pembukuan internal dan realita upstream.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ringkasan UI — Referensi Kolom</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Kolom</th>
                            <th class="p-3 border-0">Arti / Penjelasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Snapshot Date</strong></td><td class="p-3 border-0">Tanggal dan waktu kapan snapshot saldo diambil oleh cron job di latar belakang (biasanya pada akhir hari).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Gidi (Internal)</strong></td><td class="p-3 border-0">Total agregat saldo yang tercatat di sistem buku besar internal kita pada saat snapshot. Ini adalah jumlah total dari semua saldo settlement merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Paylabs</strong></td><td class="p-3 border-0">Saldo yang dilaporkan oleh API agregator Paylabs pada saat snapshot. Merupakan dana yang dipegang oleh Paylabs atas nama platform.</td></tr>
                        <tr><td class="p-3 border-0"><strong>GV (GoPay / OVO)</strong></td><td class="p-3 border-0">Saldo yang dilaporkan oleh API agregator GV. Digunakan untuk QRIS dan channel e-wallet yang dialihkan melalui GV.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Faspay (OVO)</strong></td><td class="p-3 border-0">Saldo yang dilaporkan oleh API agregator Faspay, khusus untuk transaksi OVO yang dialihkan melalui mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Faspay (ShopeePay)</strong></td><td class="p-3 border-0">Saldo yang dilaporkan oleh API agregator Faspay, khusus untuk transaksi ShopeePay yang dialihkan melalui mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Difference</strong></td><td class="p-3 border-0">Selisih yang dihitung antara Internal (Gidi) dan total jumlah dari semua saldo agregator eksternal. Nilai yang bukan nol di sini memerlukan investigasi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Remark</strong></td><td class="p-3 border-0">Catatan yang dihasilkan oleh sistem. Jika API agregator tidak dapat dihubungi selama snapshot, ini akan menampilkan <code>FETCH_ERROR</code>. Saldo terakhir yang diketahui akan digunakan dalam kasus tersebut.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Audit Steps -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> 1. Mengaudit Snapshot Saldo — Langkah-demi-Langkah</h5>
            <p class="text-muted mb-4">Alat ini membantu tim keuangan menemukan selisih antara catatan internal dan realita upstream. Gunakan sebagai pengecekan rekonsiliasi harian.</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A[Cron Job<br>Harian] --> B[Kalkulasi Total Saldo<br>Internal Merchant]
                        A --> C[Ambil Saldo Eksternal<br>via APIs]
                        C --> D[API Paylabs]
                        C --> E[API GV]
                        C --> F[API Faspay]
                        B --> G[Mesin Rekonsiliasi]
                        D --> G
                        E --> G
                        F --> G
                        G --> H{Internal == Eksternal?}
                        H -- Ya --> I[Log Snapshot<br>Selisih = 0]
                        H -- Tidak --> J[Log Snapshot<br>Selisih > 0]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke <strong>Finance &amp; Treasury → External Balance Log</strong>.</li>
                    <li class="mb-3">Lihat baris snapshot terbaru. Kolom <strong>Gidi</strong> menampilkan total saldo agregat sistem internal kita.</li>
                    <li class="mb-3">Bandingkan dengan kolom <strong>Paylabs</strong>, <strong>GV</strong>, dan <strong>Faspay</strong>, yang menampilkan saldo aktual dari sisi agregator.</li>
                    <li class="mb-3">Periksa kolom <strong>Difference</strong>. Nilai mendekati nol adalah hal yang diharapkan. Selisih positif besar (Internal &gt; Eksternal) menunjukkan settlement tertunda — agregator belum menyelesaikan clearing dana ke akun kita. Selisih negatif (Eksternal &gt; Internal) mungkin menunjukkan over-credit atau double-settlement.</li>
                    <li class="mb-3">Gunakan kotak <strong>Search logs...</strong> untuk memfilter rentang tanggal tertentu (format <code>YYYY-MM-DD</code>) untuk rekonsiliasi historis.</li>
                    <li class="mb-2">Jika selisih ditemukan, cross-reference dengan modul <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-balance-logs'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Balance Logs</a> untuk verifikasi level transaksi individual pada tanggal tersebut.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Data Hanya Baca — Tidak Ada Edit Manual</strong>
                    <p class="mb-0 text-muted small">Halaman ini adalah snapshot pelaporan saja. Anda <strong>tidak dapat</strong> mengedit angka saldo ini secara manual. Semua data diambil dari API agregator dan disimpan oleh proses latar belakang otomatis. Jika menemukan selisih besar, investigasi di level transaksi individual di modul <strong>Balance Logs</strong> dan <strong>Transaction History</strong> untuk tanggal tersebut.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Reconciliation Guide -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-balance-scale text-primary mr-2"></i> 2. Menginterpretasikan Selisih (Discrepancy)</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:35%">Skenario</th>
                            <th class="p-3 border-0">Kemungkinan Penyebab</th>
                            <th class="p-3 border-0">Tindakan yang Diperlukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0">Internal sedikit lebih tinggi dari Eksternal</td><td class="p-3 border-0">Settlement tertunda — agregator belum menyelesaikan siklus clearing mereka.</td><td class="p-3 border-0">Tunggu 1 hari kerja. Biasanya terkoreksi sendiri semalaman.</td></tr>
                        <tr><td class="p-3 border-0">Internal jauh lebih rendah dari Eksternal</td><td class="p-3 border-0">Kemungkinan over-settlement dari agregator, atau dana diterima yang tidak tercatat di buku besar internal.</td><td class="p-3 border-0">Investigasi transaksi individual pada tanggal tersebut. Eskalasikan ke tim keuangan.</td></tr>
                        <tr><td class="p-3 border-0">Kolom agregator menampilkan <code>0</code> atau <code>N/A</code></td><td class="p-3 border-0">API agregator tidak dapat dijangkau saat cron snapshot (API down, kredensial kadaluarsa).</td><td class="p-3 border-0">Periksa kolom <strong>Remark</strong> untuk <code>FETCH_ERROR</code>. Verifikasi kredensial API agregator masih valid.</td></tr>
                        <tr><td class="p-3 border-0">Tidak ada snapshot untuk hari ini</td><td class="p-3 border-0">Background cron job tidak berjalan (masalah cron server atau server down).</td><td class="p-3 border-0">Hubungi tim engineering/DevOps untuk memverifikasi kesehatan cron job dan menjalankan snapshot manual.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_fg_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Saldo eksternal lebih rendah dari saldo internal kita
                </a>
                <div id="faq_id_fg_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ini paling sering adalah <strong>settlement yang tertunda</strong>. Kita telah mengkredit merchant di buku besar internal (karena pembayaran dikonfirmasi oleh penyedia pembayaran), namun agregator upstream belum sepenuhnya menyelesaikan dana ke endpoint pelaporan saldo API mereka. Biasanya terselesaikan dalam 1 hari kerja selama siklus clearing malam hari agregator.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_fg_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bisakah saya memicu snapshot secara manual?
                </a>
                <div id="faq_id_fg_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak — snapshot di-generate secara otomatis melalui background cron job pada waktu tertentu setiap hari (biasanya akhir hari, mis. pukul 23:00). Tidak ada tombol trigger manual di UI.<br><br>
                        Jika snapshot segera dibutuhkan di luar jadwal terjadwal (mis. untuk audit), tim engineering dapat menjalankan cron snapshot secara manual via CLI: <code>php index.php cli/financialgateway/snapshot</code>. Hubungi tim DevOps Anda untuk ini.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_fg_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kolom agregator menampilkan 0 atau FETCH_ERROR di snapshot hari ini
                </a>
                <div id="faq_id_fg_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ini berarti background cron tidak dapat menjangkau API saldo agregator tertentu saat snapshot dijalankan — kemungkinan karena API agregator sedang down, atau kredensial API (API Key / Secret) telah kadaluarsa.<br><br>
                        <strong>Resolusi:</strong> Periksa halaman status sistem agregator. Jika mereka beroperasi normal, verifikasi bahwa kredensial API di konfigurasi server masih valid dan belum dirotasi. Perbarui jika perlu dan tunggu snapshot terjadwal berikutnya.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>