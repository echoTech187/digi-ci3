<div id="module-ug-financial-exports" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Financial Exports (Download Report) page is the shared staging area for all asynchronously generated Excel/CSV reconciliation files. Because large datasets take time to compile without freezing the UI, every export request is queued in the background and made available here once the compilation is complete.</p>

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
                        <tr><td class="p-3 border-0"><strong>DATE TIME</strong></td><td class="p-3 border-0">The exact server timestamp when the report generation was <em>requested</em>. Sort by this to find your most recent exports.</td></tr>
                        <tr><td class="p-3 border-0"><strong>REQUESTED BY</strong></td><td class="p-3 border-0">The admin account that triggered this export. Useful for auditing who generated which report.</td></tr>
                        <tr><td class="p-3 border-0"><strong>TYPE</strong></td><td class="p-3 border-0">The category of the report (e.g., <code>QRIS</code>, <code>BI FAST</code>, <code>EWALLET</code>, <code>Mutation</code>, <code>VA</code>, <code>Transaction</code>). Use the filter to quickly find a specific type.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FILE NAME</strong></td><td class="p-3 border-0">The actual name of the generated file. When status is <span class="badge badge-success">SUCCESS</span>, this text becomes a <strong>clickable download link</strong>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">Current compilation state: <span class="badge badge-warning">PENDING</span> = still being compiled. <span class="badge badge-success">SUCCESS</span> = ready to download. <span class="badge badge-danger">FAILED</span> = compilation error.</td></tr>
                        <tr><td class="p-3 border-0"><strong>REMARK</strong></td><td class="p-3 border-0">System notes on the report (e.g., date range applied, error reason if FAILED, or processing time).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Architecture: Asynchronous Data Pipeline</h3>
                <p class="text-muted mb-4">Generating heavy financial reports is decoupled from the web interface to prevent timeouts. Here is how the ETL (Extract, Transform, Load) pipeline works:</p>

                <div class="mermaid-container mb-4">
                    <div class="mermaid">
                        flowchart TD
                            A([Admin clicks<br>Download Excel]) --> B[System inserts job<br>into Export Queue]
                            B --> C[UI displays PENDING]
                            D[Background Worker] --> E{Check Queue}
                            E -- Job Found --> F[Extract Data<br>from DB]
                            F --> G[Transform into<br>CSV / Excel]
                            G --> H[Save File to<br>Server Storage]
                            H --> I[Update Job Status<br>to SUCCESS]
                            I --> J[(UI displays Download Link)]
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Downloading Reports — Step-by-Step</h3>
                <p class="text-muted mb-4">Follow these steps to securely retrieve a generated report file.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">First, trigger a report export from the relevant module (e.g., <strong>Transaction History → Download Excel</strong>, or <strong>BI-FAST → Download Excel</strong>). The export is queued immediately in the background.</li>
                        <li class="mb-3">Navigate to <strong>Finance &amp; Treasury → Financial Exports</strong> (or <strong>Download Report</strong>).</li>
                        <li class="mb-3">Find your report in the table. Use the <strong>TYPE</strong> filter to narrow down if there are many exports queued. You can also sort by <strong>DATE TIME</strong> to find the most recent.</li>
                        <li class="mb-3">Wait until the <strong>STATUS</strong> shows <span class="badge badge-success">SUCCESS</span>. For large datasets, this can take 2–5 minutes. Refresh the page to check.</li>
                        <li class="mb-2">Click the blue, underlined <strong>FILE NAME</strong> link to start the download directly to your browser.</li>
                    </ol>
                </div>

                <div class="doc-callout callout-warning shadow-sm">
                    <div class="callout-icon"><i class="fas fa-archive"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Archive Retention — Download Promptly</strong>
                        <p class="mb-0 text-muted small">Generated report files consume significant server disk space. They are automatically cleaned up after a retention period (typically 7–30 days depending on server configuration). Download your required reports promptly after they are generated. Do not rely on this page as a permanent file archive.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Common Issues &amp; What To Do</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 1: Report status is FAILED — what should I do?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Cause:</strong> A FAILED compilation usually means the date range queried contained too much data, causing a server memory exhaustion timeout (PHP memory limit exceeded).<br><br>
                <strong>Resolution:</strong> Re-request the report with a narrower date range — for example, 7 days instead of 30 days, or split a monthly report into weekly batches. Check the <strong>REMARK</strong> column for the specific error message to identify the root cause.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: Report is still PENDING after 10+ minutes</span>
            </div>
            <div class="hc-faq-a">
                <strong>Cause:</strong> Heavy reports are placed in a background queue. If multiple admins are generating reports simultaneously, yours will wait in line until the workers are free.<br><br>
                <strong>Resolution:</strong> Wait patiently and refresh the page every 2–3 minutes. <strong>Do not</strong> repeatedly click "Download Excel" for the same report — each click adds a new job to the queue, making the wait worse for everyone. If PENDING exceeds 15 minutes, the background worker may have crashed — contact your engineering team to restart the worker process.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Can I download a report generated by another administrator?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Yes. The Financial Exports page acts as a <strong>shared hub</strong> for all administrators who have the appropriate RBAC access to this module. All reports — regardless of who triggered them — are visible and downloadable by any authorized admin. The <strong>REQUESTED BY</strong> column shows who originally generated each report.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman Ekspor Keuangan (Download Report) adalah area staging bersama untuk semua file rekonsiliasi Excel/CSV yang di-generate secara asinkron. Karena dataset besar membutuhkan waktu untuk dikompilasi tanpa membekukan UI, setiap request ekspor diantrekan di latar belakang dan tersedia di sini setelah kompilasi selesai.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Referensi Kolom</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Kolom</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>DATE TIME</strong></td><td class="p-3 border-0">Stempel waktu server tepat saat pembuatan laporan <em>diminta</em>. Urutkan berdasarkan ini untuk menemukan ekspor terbaru Anda.</td></tr>
                        <tr><td class="p-3 border-0"><strong>REQUESTED BY</strong></td><td class="p-3 border-0">Akun admin yang memicu ekspor ini. Berguna untuk mengaudit siapa yang men-generate laporan mana.</td></tr>
                        <tr><td class="p-3 border-0"><strong>TYPE</strong></td><td class="p-3 border-0">Kategori laporan (mis. <code>QRIS</code>, <code>BI FAST</code>, <code>EWALLET</code>, <code>Mutation</code>, <code>VA</code>, <code>Transaction</code>). Gunakan filter untuk menemukan tipe tertentu dengan cepat.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FILE NAME</strong></td><td class="p-3 border-0">Nama pasti file yang di-generate. Saat status <span class="badge badge-success">SUCCESS</span>, teks ini menjadi <strong>tautan unduhan yang dapat diklik</strong>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>STATUS</strong></td><td class="p-3 border-0">Status kompilasi saat ini: <span class="badge badge-warning">PENDING</span> = masih dikompilasi. <span class="badge badge-success">SUCCESS</span> = siap diunduh. <span class="badge badge-danger">FAILED</span> = error kompilasi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>REMARK</strong></td><td class="p-3 border-0">Catatan sistem tentang laporan (mis. rentang tanggal yang diterapkan, alasan error jika FAILED, atau waktu pemrosesan).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Arsitektur: Pipeline Data Asinkron</h3>
                <p class="text-muted mb-4">Pembuatan laporan keuangan yang berat dipisahkan dari antarmuka web untuk mencegah terjadinya timeout. Berikut adalah cara kerja pipeline ETL (Extract, Transform, Load):</p>

                <div class="mermaid-container mb-4">
                    <div class="mermaid">
                        flowchart TD
                            A([Admin klik<br>Download Excel]) --> B[Sistem memasukkan job<br>ke Antrean Ekspor]
                            B --> C[UI menampilkan PENDING]
                            D[Background Worker] --> E{Cek Antrean}
                            E -- Job Ditemukan --> F[Ekstrak Data<br>dari DB]
                            F --> G[Transformasi ke<br>CSV / Excel]
                            G --> H[Simpan File ke<br>Storage Server]
                            H --> I[Update Status Job<br>menjadi SUCCESS]
                            I --> J[(UI menampilkan Link Unduhan)]
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Mengunduh Laporan — Langkah-demi-Langkah</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk mengambil file laporan yang sudah jadi dengan aman.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Pertama, picu ekspor laporan dari modul yang relevan (mis. <strong>Transaction History → Download Excel</strong>, atau <strong>BI-FAST → Download Excel</strong>). Ekspor langsung diantrekan di latar belakang.</li>
                        <li class="mb-3">Navigasikan ke <strong>Finance &amp; Treasury → Financial Exports</strong> (atau <strong>Download Report</strong>).</li>
                        <li class="mb-3">Temukan laporan Anda di tabel. Gunakan filter <strong>TYPE</strong> untuk mempersempit jika banyak ekspor dalam antrean. Anda juga bisa mengurutkan berdasarkan <strong>DATE TIME</strong> untuk menemukan yang terbaru.</li>
                        <li class="mb-3">Tunggu hingga <strong>STATUS</strong> menampilkan <span class="badge badge-success">SUCCESS</span>. Untuk dataset besar, ini bisa memakan waktu 2–5 menit. Refresh halaman untuk mengecek.</li>
                        <li class="mb-2">Klik tautan <strong>FILE NAME</strong> yang bergaris bawah berwarna biru untuk memulai unduhan langsung ke browser Anda.</li>
                    </ol>
                </div>

                <div class="doc-callout callout-warning shadow-sm">
                    <div class="callout-icon"><i class="fas fa-archive"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Retensi Arsip — Unduh Segera</strong>
                        <p class="mb-0 text-muted small">File laporan yang di-generate mengonsumsi ruang disk server yang signifikan. File dibersihkan secara otomatis setelah periode retensi (biasanya 7–30 hari tergantung konfigurasi server). Unduh laporan yang Anda butuhkan segera setelah di-generate. Jangan mengandalkan halaman ini sebagai arsip file permanen.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 1: Status laporan adalah FAILED — apa yang harus dilakukan?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Penyebab:</strong> Kompilasi yang gagal biasanya berarti rentang tanggal yang ditarik mengandung terlalu banyak data, menyebabkan timeout kehabisan memori server (PHP memory limit terlampaui).<br><br>
                <strong>Resolusi:</strong> Request ulang laporan dengan rentang tanggal yang lebih sempit — misalnya 7 hari alih-alih 30 hari, atau bagi laporan bulanan menjadi batch mingguan. Periksa kolom <strong>REMARK</strong> untuk pesan error spesifik guna mengidentifikasi akar masalah.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Laporan masih PENDING setelah 10+ menit</span>
            </div>
            <div class="hc-faq-a">
                <strong>Penyebab:</strong> Laporan berat ditempatkan dalam antrean latar belakang. Jika beberapa admin me-generate laporan secara bersamaan, laporan Anda akan mengantre hingga worker tersedia.<br><br>
                <strong>Resolusi:</strong> Tunggu dengan sabar dan refresh halaman setiap 2–3 menit. <strong>Jangan</strong> berulang kali mengklik "Download Excel" untuk laporan yang sama — setiap klik menambahkan job baru ke antrean, memperburuk situasi. Jika PENDING melebihi 15 menit, background worker mungkin crash — hubungi tim engineering Anda untuk merestart proses worker.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Bisakah saya mengunduh laporan yang di-generate oleh admin lain?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Ya. Halaman Ekspor Keuangan berfungsi sebagai <strong>hub bersama</strong> untuk semua administrator yang memiliki akses RBAC yang sesuai untuk modul ini. Semua laporan — terlepas dari siapa yang memicunya — dapat dilihat dan diunduh oleh admin yang berwenang. Kolom <strong>REQUESTED BY</strong> menampilkan siapa yang awalnya men-generate setiap laporan.</p>
        </div>
    </div>
</div>
