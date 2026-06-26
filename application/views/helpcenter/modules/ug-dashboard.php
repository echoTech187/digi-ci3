<div id="module-ug-dashboard" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Dashboard serves as the central command center for the Gateway Platform. It provides real-time transaction analytics, system-wide health controls, and live mutation monitoring in one unified view.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — KPI Metrics</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Metric</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Total Volume</strong></td><td class="p-3 border-0">The absolute gross Rupiah value of all successful transactions within the selected timeframe.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Total Transactions</strong></td><td class="p-3 border-0">The absolute count (quantity) of successful transactions.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Success Rate</strong></td><td class="p-3 border-0">The percentage of initiated transactions that successfully reached the <code>SUCCESS</code> state. Highly useful for monitoring Cashout stability.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Maintenance Toggle</strong></td><td class="p-3 border-0">A global kill-switch button to instantly block all incoming API traffic across all merchants.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-bolt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Technical Insight: Asynchronous Caching</strong>
                <p class="mb-0 text-muted small">To prevent database locks during peak transaction hours, the dashboard KPIs and chart data are cached locally for 300 seconds (5 minutes). If you need down-to-the-second accuracy, refer to the specific transaction report modules.</p>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Global API Maintenance Switch</h3>
                <p class="text-muted mb-4">Administrators have access to a global emergency kill-switch. This toggles the <code>OpenAPI Status</code> for all merchants simultaneously.</p>

                <div class="pl-4 border-left border-danger ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Locate the <strong>Maintenance Toggle</strong> switch at the top of the dashboard.</li>
                        <li class="mb-3">Switching to <strong>Maintenance ON</strong> instantly updates all merchant API statuses to "Not Active". Any new incoming API requests from merchants will be rejected with an <code>HTTP 503 Maintenance</code> error.</li>
                        <li class="mb-2">Switching to <strong>Maintenance OFF</strong> restores the API access to "Active" for all merchants, resuming normal operations.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Balance Synchronization Utility</h3>
                <p class="text-muted mb-4">In rare cases of database drift (e.g., a server crash during a transaction commit), a merchant's cached <code>Available Balance</code> might drift from the actual sum of their mutation ledger.</p>

                <div class="pl-4 border-left border-success ml-2">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Access the hidden sync endpoint directly via the browser URL: <code>/dashboard/syncAvailableBalanceMerchant?do_update=1</code> (requires Level 1 Super Admin privileges).</li>
                        <li class="mb-3">The system runs a heavy aggregate query, summing all historical Cash-In and Cash-Out mutation logs.</li>
                        <li class="mb-2">It compares the aggregated "true" balance against the cached <code>c_balanceTotal</code> table and forces a hard overwrite if anomalies are detected to correct the drift.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Common Issues &amp; What To Do</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 1: Dashboard numbers are not updating in real-time</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> You just processed a successful test transaction, but the Total Volume KPI hasn't changed.<br><br>
                <strong>Resolution:</strong> The dashboard utilizes a 5-minute file-based cache to reduce database load. Wait up to 5 minutes for the cache to clear, or check the "Recent Mutations" table which fetches live data.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: Success Rate shows 0% or N/A</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> The Success Rate KPI card displays an anomaly despite having successful transactions.<br><br>
                <strong>Resolution:</strong> The system calculates Success Rate based primarily on Outbound Disbursements (Cashout). If no disbursements were attempted in the selected time period, the system defaults to N/A to avoid mathematical division-by-zero errors.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Cannot toggle Maintenance Mode</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> Clicking the Global API Maintenance switch throws an HTTP 403 or silently fails to save.<br><br>
                <strong>Resolution:</strong> Only Super Administrators (Level 1) have the RBAC permission to trigger the global kill-switch. If you are a Level 2 Admin, this function is intentionally locked to prevent accidental API outages.
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Dashboard berfungsi sebagai pusat komando utama untuk Platform Gateway. Dasbor ini menyediakan analitik transaksi real-time, kontrol kesehatan sistem menyeluruh, dan pemantauan mutasi langsung dalam satu tampilan terpadu.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Metrik KPI</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Metrik</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Total Volume</strong></td><td class="p-3 border-0">Nilai kotor Rupiah absolut dari semua transaksi yang sukses dalam jangka waktu yang dipilih.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Total Transactions</strong></td><td class="p-3 border-0">Jumlah mutlak (kuantitas) transaksi yang sukses.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Success Rate</strong></td><td class="p-3 border-0">Persentase transaksi yang berhasil mencapai status <code>SUCCESS</code>. Sangat berguna untuk memantau stabilitas Cashout.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Maintenance Toggle</strong></td><td class="p-3 border-0">Sakelar darurat global (kill-switch) untuk langsung memblokir semua lalu lintas API masuk dari seluruh merchant.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="doc-callout callout-info shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-bolt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Wawasan Teknis: Caching Asinkron</strong>
                <p class="mb-0 text-muted small">Untuk mencegah penguncian (lock) database selama jam puncak transaksi, data KPI dan grafik dasbor di-cache secara lokal selama 300 detik (5 menit). Jika Anda membutuhkan akurasi hingga hitungan detik, silakan periksa modul laporan transaksi spesifik.</p>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Sakelar Pemeliharaan API Global</h3>
                <p class="text-muted mb-4">Administrator memiliki akses ke sakelar darurat global (kill-switch). Ini secara bersamaan mengubah <code>OpenAPI Status</code> untuk semua merchant.</p>

                <div class="pl-4 border-left border-danger ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Temukan sakelar <strong>Maintenance Toggle</strong> di bagian atas dasbor.</li>
                        <li class="mb-3">Beralih ke <strong>Maintenance ON</strong> secara instan memperbarui semua status API merchant menjadi "Not Active". Setiap permintaan API baru yang masuk dari merchant akan ditolak dengan error <code>HTTP 503 Maintenance</code>.</li>
                        <li class="mb-2">Beralih ke <strong>Maintenance OFF</strong> memulihkan akses API menjadi "Active" untuk semua merchant, melanjutkan operasi normal.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Utilitas Sinkronisasi Saldo</h3>
                <p class="text-muted mb-4">Dalam kasus penyimpangan (drift) database yang langka (misal, server crash saat memproses transaksi), nilai <code>Available Balance</code> merchant yang tersimpan (cache) mungkin menyimpang dari total buku besar mutasi aslinya.</p>

                <div class="pl-4 border-left border-success ml-2">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Akses endpoint sinkronisasi tersembunyi secara langsung melalui URL browser: <code>/dashboard/syncAvailableBalanceMerchant?do_update=1</code> (memerlukan hak akses Super Admin Level 1).</li>
                        <li class="mb-3">Sistem akan menjalankan kueri agregat berat, menjumlahkan semua log mutasi historis Cash-In dan Cash-Out.</li>
                        <li class="mb-2">Sistem kemudian membandingkan saldo "asli" gabungan tersebut dengan tabel <code>c_balanceTotal</code> yang di-cache, lalu memaksa penimpaan (hard overwrite) jika anomali terdeteksi untuk memperbaiki selisih.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 1: Angka dasbor tidak diperbarui secara real-time</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Anda baru saja memproses transaksi percobaan sukses, tetapi KPI Total Volume belum berubah.<br><br>
                <strong>Resolusi:</strong> Dasbor memanfaatkan cache berbasis file selama 5 menit untuk menekan beban database. Tunggu hingga 5 menit sampai cache diperbarui, atau periksa tabel "Recent Mutations" yang menarik data terbaru.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Success Rate menunjukkan angka 0% atau N/A</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Kartu KPI Tingkat Keberhasilan menampilkan anomali meskipun ada transaksi berhasil.<br><br>
                <strong>Resolusi:</strong> Sistem menghitung Success Rate terutama berdasarkan Pencairan Keluar (Cashout). Jika tidak ada pencairan yang dicoba dalam rentang waktu terpilih, sistem beralih ke standar N/A untuk menghindari error matematika pembagian-dengan-nol.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Tidak dapat mengaktifkan Mode Maintenance</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Mengklik sakelar Global API Maintenance memunculkan HTTP 403 atau diam-diam gagal tersimpan.<br><br>
                <strong>Resolusi:</strong> Hanya Super Administrator (Level 1) yang mempunyai hak RBAC untuk memicu sakelar darurat (kill-switch) ini. Jika Anda Admin Level 2, fungsi ini memang dikunci untuk mencegah pemadaman API yang tak disengaja.
            </div>
        </div>
    </div>
</div>