<div id="module-qris" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-qrcode text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">QRIS Management</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Control Dynamic, Static, and Recurring QR Code transactions. Administer RRN reconciliation, session unlocks, and broadcast RabbitMQ payment notifications.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Architectural Modalities</h3>
            <p class="text-muted mb-4">The Quick Response Code Indonesian Standard (QRIS) is deeply integrated into three distinct controllers, handling unique merchant workflows:</p>
            
            <table class="table table-bordered bg-white shadow-sm mb-4">
                <thead class="thead-light">
                    <tr>
                        <th>Modality</th>
                        <th>Workflow &amp; Characteristics</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Dynamic QRIS</strong></td>
                        <td>A unique QR generated on-the-fly via API for a specific invoice amount. It contains an intrinsic timeout (e.g., 15 minutes). Once the customer pays, the exact invoice is cleared.</td>
                    </tr>
                    <tr>
                        <td><strong>Static QRIS (MPM)</strong></td>
                        <td>A permanent QR code registered to the merchant (often printed as a standee). The customer scans the code and manually inputs the total payment amount.</td>
                    </tr>
                    <tr>
                        <td><strong>Recurring QRIS</strong></td>
                        <td>A specialized subscription-based implementation enabling automated tokenized deductions upon subsequent approvals.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 2. Deep Linking &amp; High-Performance Audit</h3>
            <p class="text-muted mb-4">The QRIS module possesses the most aggressive indexing capabilities, tracking external Bank RRNs alongside internal invoice numbers.</p>
            
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-bolt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">XHR Unblocking (session_write_close)</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Context:</strong> Because QRIS queries pull heavy relational datasets, the Controller executes <code>session_write_close()</code> just before rendering the view.</li>
                        <li class="mb-1"><strong>Why it matters:</strong> This releases the PHP Session lock immediately, allowing the merchant dashboard to process parallel AJAX (XHR) requests simultaneously without forming a blocking queue.</li>
                    </ul>
                </div>
            </div>

            <ol class="text-muted ml-3">
                <li class="mb-2"><strong>Settlement Syncing:</strong> Filtering by <code>Settlement Date</code> isolates transactions that have physically cleared the Acquirer Bank into the aggregator's Nostro account, distinct from the <code>Transaction Date</code>.</li>
                <li class="mb-2"><strong>RRN Tracing:</strong> Type the Retrieval Reference Number (RRN) from a customer's banking app receipt to definitively verify if a disputed transaction reached the system.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-satellite-dish text-primary mr-2"></i> 3. Consumer Notifications (RabbitMQ Integration)</h3>
            <p class="text-muted mb-4">When a Static QRIS (MPM) is paid, the system normally sends a webhook to the merchant. If the merchant experiences downtime, you can re-broadcast the notification manually.</p>
            
            <div class="doc-callout callout-note mb-4 shadow-sm border-left-success">
                <div class="callout-icon"><i class="fas fa-paper-plane"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Resend Notification via Message Queue</strong>
                    <ol class="small text-muted mb-0 pl-3 mt-2">
                        <li class="mb-1">Locate the transaction in the QRIS grid and select <strong>Resend Notification</strong>.</li>
                        <li class="mb-1">The Controller initiates a <code>cURL POST</code> request to the internal gateway <code>/Rabbitmq/createQueue</code>.</li>
                        <li class="mb-1">A JSON payload containing the <code>msgType: consumer_notification_qris_mpm</code> is dispatched to the high-availability RabbitMQ cluster, ensuring asynchronous and resilient delivery to the merchant's endpoint.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-qrcode text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Manajemen QRIS</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Kendalikan transaksi QRIS Dinamis, Statis, dan Berulang (Recurring). Lakukan rekonsiliasi RRN, pembukaan kunci sesi (session unlock), dan kirim ulang notifikasi pembayaran via RabbitMQ.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Modalitas Arsitektur</h3>
            <p class="text-muted mb-4">Integrasi QRIS dipisah secara spesifik ke dalam tiga <em>controller</em> berbeda, menangani alur kerja merchant yang unik:</p>
            
            <table class="table table-bordered bg-white shadow-sm mb-4">
                <thead class="thead-light">
                    <tr>
                        <th>Modalitas</th>
                        <th>Karakteristik &amp; Alur Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Dynamic QRIS</strong></td>
                        <td>Kode QR yang diproduksi secara instan (API) dengan nominal tagihan spesifik. Terdapat batas waktu (misal, 15 menit). Saat terbayar, tagihan otomatis diselesaikan.</td>
                    </tr>
                    <tr>
                        <td><strong>Static QRIS (MPM)</strong></td>
                        <td>Kode QR permanen milik merchant (biasanya dicetak di atas akrilik meja kasir). Pembeli memindainya (scan) lalu secara manual mengetikkan nominal tagihan di HP mereka.</td>
                    </tr>
                    <tr>
                        <td><strong>Recurring QRIS</strong></td>
                        <td>Implementasi khusus untuk sistem langganan (subscription) yang memungkinkan deduksi (pemotongan) tokenisasi secara otomatis ke depannya.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 2. Deep Linking &amp; Audit Berkinerja Tinggi</h3>
            <p class="text-muted mb-4">Modul QRIS memiliki kapabilitas pengindeksan paling diagresifkan, melacak nomor RRN (Retrieval Reference Number) bank eskternal sekaligus nomor tagihan (invoice) internal.</p>
            
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-bolt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Pembebasan XHR (session_write_close)</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Konteks:</strong> Karena kueri QRIS menarik dataset relasional yang amat berat, Controller mengeksekusi <code>session_write_close()</code> persis sebelum merender (memuat) halaman UI.</li>
                        <li class="mb-1"><strong>Signifikansi:</strong> Ini seketika membebaskan ikatan/kuncian (<em>lock</em>) dari PHP Session, sehingga dasbor merchant bisa mengeksekusi beberapa tembakan permintaan AJAX (XHR) paralel di saat bersamaan tanpa terjadi antrean (<em>blocking</em>) yang membuat sistem macet (<em>hang</em>).</li>
                    </ul>
                </div>
            </div>

            <ol class="text-muted ml-3">
                <li class="mb-2"><strong>Sinkronisasi Pencairan (Settlement):</strong> Memfilter tabel lewat kolom <code>Settlement Date</code> untuk mengisolasi transaksi yang dananya telah ter-kliring fisik dari Bank Acquirer ke rekening Nostro agregator (berbeda dari Tanggal Transaksi biasa).</li>
                <li class="mb-2"><strong>Pelacakan RRN:</strong> Ketikkan RRN (biasanya tercetak di bukti transfer m-banking pembeli) di kotak pencarian untuk memastikan apakah dana transaksi siluman tersebut sukses masuk ke sistem gateway atau tidak.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-satellite-dish text-primary mr-2"></i> 3. Notifikasi Konsumen (Integrasi RabbitMQ)</h3>
            <p class="text-muted mb-4">Bila Static QRIS (MPM) sukses dibayar, gateway akan menembakkan <em>webhook</em> ke merchant. Jika server merchant sedang gangguan (<em>downtime</em>), Anda dapat menyiarkan ulang (re-broadcast) notifikasi secara manual.</p>
            
            <div class="doc-callout callout-note mb-4 shadow-sm border-left-success">
                <div class="callout-icon"><i class="fas fa-paper-plane"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kirim Ulang Notifikasi via Antrean (Message Queue)</strong>
                    <ol class="small text-muted mb-0 pl-3 mt-2">
                        <li class="mb-1">Pilih transaksi mandek pada tabel QRIS, lalu klik opsi <strong>Resend Notification</strong>.</li>
                        <li class="mb-1">Controller memicu perintah <code>cURL POST</code> internal ke *endpoint* gateway <code>/Rabbitmq/createQueue</code>.</li>
                        <li class="mb-1">Beban data JSON yang mengandung label <code>msgType: consumer_notification_qris_mpm</code> ditembakkan ke dalam <em>cluster</em> RabbitMQ agar diantrekan secara asinkron, memastikan notifikasi tersebut kebal terhadap gangguan dan pasti tersalurkan.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
                <!-- Pagination Footer -->
                <div class="doc-pagination mt-5 pt-4 border-top" id="docPagination" style="display:none;">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" id="btnPrevDoc" class="text-decoration-none d-none" style="display:block; padding: 16px; border: 1px solid var(--border-color); border-radius: 8px; transition: all 0.2s;">
                                <div class="text-muted small mb-1">Previous</div>
                                <div class="font-weight-bold" style="color: var(--primary-color); font-size: 16px;"><i class="fas fa-chevron-left mr-2"></i> <span id="textPrevDoc"></span></div>
                            </a>
                        </div>
                        <div class="col-6 text-right">
                            <a href="javascript:void(0);" id="btnNextDoc" class="text-decoration-none d-none" style="display:block; padding: 16px; border: 1px solid var(--border-color); border-radius: 8px; transition: all 0.2s;">
                                <div class="text-muted small mb-1">Next</div>
                                <div class="font-weight-bold" style="color: var(--primary-color); font-size: 16px;"><span id="textNextDoc"></span> <i class="fas fa-chevron-right ml-2"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Interactivity Script -->
    