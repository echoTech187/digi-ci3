<div id="module-qris" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The QRIS Management module allows you to monitor and control Dynamic, Static, and Recurring QR Code transactions. Administer RRN reconciliation, session unlocks, and broadcast RabbitMQ payment notifications.</p>

        <hr class="my-4">

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
                        <tr><td class="p-3 border-0"><strong>Transaction Date</strong></td><td class="p-3 border-0">When the QR code was generated or the transaction was initiated.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Settlement Date</strong></td><td class="p-3 border-0">When the funds physically cleared the Acquirer Bank into our aggregator Nostro account.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Modality</strong></td><td class="p-3 border-0">Indicates if the QR is <code>Dynamic</code>, <code>Static (MPM)</code>, or <code>Recurring</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>RRN</strong></td><td class="p-3 border-0">Retrieval Reference Number. The unique bank-side identifier for the payment, crucial for tracing customer disputes.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu</strong></td><td class="p-3 border-0">Contains buttons to View Details or manually Resend Notifications to the merchant via RabbitMQ.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Architectural Modalities</h5>
            <p class="text-muted mb-4">QRIS is integrated into three distinct workflows, handling unique merchant needs:</p>
            
            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ul class="text-muted mb-0">
                    <li class="mb-3"><strong>Dynamic QRIS:</strong> A unique QR generated on-the-fly via API for a specific invoice amount. It contains an intrinsic timeout (e.g., 15 minutes). Once the customer pays, the exact invoice is cleared.</li>
                    <li class="mb-3"><strong>Static QRIS (MPM):</strong> A permanent QR code registered to the merchant (often printed as a standee). The customer scans the code and manually inputs the total payment amount.</li>
                    <li class="mb-2"><strong>Recurring QRIS:</strong> A specialized subscription-based implementation enabling automated tokenized deductions upon subsequent approvals.</li>
                </ul>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-bolt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">XHR Unblocking (session_write_close)</strong>
                    <p class="mb-0 text-muted small">Because QRIS queries pull heavy relational datasets, the Controller executes <code>session_write_close()</code> just before rendering the view. This releases the PHP Session lock immediately, allowing the dashboard to process parallel AJAX requests simultaneously without forming a blocking queue.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Resending Notifications -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-primary mr-2"></i> 2. Resending RabbitMQ Notifications</h5>
            <p class="text-muted mb-4">If a merchant's server experiences downtime, they might miss the webhook. You can manually re-broadcast the notification.</p>

            <div class="pl-4 border-left border-info ml-2">
                <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the target transaction in the QRIS table.</li>
                    <li class="mb-3">Click the Action menu and select <strong>Resend Notification</strong>.</li>
                    <li class="mb-2">The system dispatches a <code>consumer_notification_qris_mpm</code> payload to the RabbitMQ cluster, ensuring asynchronous and resilient delivery to the merchant's endpoint.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qr_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Customer paid but transaction shows as Pending
                </a>
                <div id="faq_en_qr_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The customer shows their mobile banking receipt, but the system hasn't updated.<br><br>
                        <strong>Resolution:</strong> Use the <strong>Search</strong> box to query the exact <strong>RRN (Retrieval Reference Number)</strong> printed on the customer's receipt. If it doesn't exist, the funds haven't reached the upstream aggregator yet. If it exists but is stuck, check the upstream aggregator's dashboard for delays.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qr_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: What is the difference between Transaction Date and Settlement Date?
                </a>
                <div id="faq_en_qr_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Transaction Date is when the QR was generated or scanned. Settlement Date is when the Acquirer Bank actually cleared the physical funds into the aggregator's Nostro account. Filtering by Settlement Date is crucial for accurate financial reconciliation.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_qr_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Merchant didn't receive the webhook
                </a>
                <div id="faq_en_qr_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolution:</strong> First, ensure the merchant has configured their QRIS Callback URL in their profile. Second, use the <strong>Resend Notification</strong> action. If it still fails, the merchant's endpoint might be returning a non-200 HTTP status code, or blocking our IPs.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul Manajemen QRIS memungkinkan Anda memantau dan mengontrol transaksi QR Code Dinamis, Statis, dan Berulang (Recurring). Kelola rekonsiliasi RRN, pembukaan kunci sesi (session unlocks), dan siarkan notifikasi pembayaran RabbitMQ.</p>

        <hr class="my-4">

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
                        <tr><td class="p-3 border-0"><strong>Transaction Date</strong></td><td class="p-3 border-0">Kapan kode QR dihasilkan atau transaksi dimulai.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Settlement Date</strong></td><td class="p-3 border-0">Kapan dana secara fisik ter-kliring dari Bank Acquirer ke rekening Nostro agregator kita.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Modality</strong></td><td class="p-3 border-0">Menunjukkan apakah QR tersebut <code>Dynamic</code>, <code>Static (MPM)</code>, atau <code>Recurring</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>RRN</strong></td><td class="p-3 border-0">Retrieval Reference Number. Pengidentifikasi unik dari sisi bank untuk pembayaran, sangat penting untuk melacak komplain pelanggan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu</strong></td><td class="p-3 border-0">Berisi tombol untuk Melihat Detail atau mengirim ulang notifikasi secara manual ke merchant melalui RabbitMQ.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Modalitas Arsitektur</h5>
            <p class="text-muted mb-4">QRIS terintegrasi ke dalam tiga alur kerja berbeda yang menangani kebutuhan merchant secara unik:</p>
            
            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ul class="text-muted mb-0">
                    <li class="mb-3"><strong>Dynamic QRIS:</strong> Kode QR yang dihasilkan secara on-the-fly melalui API untuk nominal tagihan spesifik. Terdapat batas waktu bawaan (misal, 15 menit). Setelah pelanggan membayar, tagihan spesifik tersebut langsung selesai.</li>
                    <li class="mb-3"><strong>Static QRIS (MPM):</strong> Kode QR permanen yang terdaftar pada merchant (sering dicetak sebagai standee). Pelanggan memindai kode dan memasukkan total pembayaran secara manual.</li>
                    <li class="mb-2"><strong>Recurring QRIS:</strong> Implementasi khusus berbasis langganan yang memungkinkan pemotongan ter-tokenisasi otomatis pada persetujuan berikutnya.</li>
                </ul>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-bolt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Pembebasan XHR (session_write_close)</strong>
                    <p class="mb-0 text-muted small">Karena kueri QRIS menarik dataset relasional yang amat berat, Controller mengeksekusi <code>session_write_close()</code> tepat sebelum merender tampilan. Ini segera melepaskan kunci (lock) Sesi PHP, memungkinkan dasbor memproses permintaan AJAX paralel secara bersamaan tanpa membentuk antrean yang menghambat kinerja.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Resending Notifications -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-satellite-dish text-primary mr-2"></i> 2. Mengirim Ulang Notifikasi RabbitMQ</h5>
            <p class="text-muted mb-4">Jika server merchant mengalami downtime, mereka mungkin melewatkan webhook. Anda dapat menyiarkan ulang (re-broadcast) notifikasi secara manual.</p>

            <div class="pl-4 border-left border-info ml-2">
                <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari transaksi yang dituju di tabel QRIS.</li>
                    <li class="mb-3">Klik menu Aksi dan pilih <strong>Resend Notification</strong>.</li>
                    <li class="mb-2">Sistem mengirimkan payload <code>consumer_notification_qris_mpm</code> ke kluster RabbitMQ, memastikan pengiriman yang tangguh dan asinkron ke endpoint merchant.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qr_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Pelanggan sudah bayar tapi transaksi masih Pending
                </a>
                <div id="faq_id_qr_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Pelanggan menunjukkan bukti transfer mobile banking, namun sistem belum diperbarui.<br><br>
                        <strong>Resolusi:</strong> Gunakan kotak <strong>Search</strong> untuk mencari <strong>RRN (Retrieval Reference Number)</strong> persis yang tercetak di bukti bayar pelanggan. Jika tidak ditemukan, dana belum sampai ke agregator hulu. Jika ada namun macet, periksa dasbor agregator hulu jika ada penundaan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qr_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Apa bedanya Transaction Date dan Settlement Date?
                </a>
                <div id="faq_id_qr_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Transaction Date adalah saat QR dihasilkan atau dipindai. Settlement Date adalah saat Bank Acquirer secara fisik menyetorkan dana ke rekening Nostro agregator. Memfilter berdasarkan Settlement Date sangat penting untuk rekonsiliasi keuangan yang akurat.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_qr_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Merchant tidak menerima webhook notifikasi
                </a>
                <div id="faq_id_qr_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolusi:</strong> Pertama, pastikan merchant telah mengonfigurasi Callback URL QRIS di profil mereka. Kedua, gunakan aksi <strong>Resend Notification</strong>. Jika masih gagal, endpoint merchant mungkin mengembalikan kode status HTTP selain 200, atau server mereka memblokir IP kita.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
