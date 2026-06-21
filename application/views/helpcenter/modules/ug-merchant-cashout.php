<div id="module-ug-merchant-cashout" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Cashout Fee Settings</strong> module controls how much a merchant is charged when they withdraw their available balance to an external bank account or perform outward disbursements via <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-bifast\']').click()" class="text-primary font-weight-bold text-decoration-none">BI-FAST Transfer</a>. Like Cashin, this provides a per-merchant override of the global cashout fee structure.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Data Columns</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Column / Field</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CUSTOM FEE TOGGLE</strong></td><td class="p-3 border-0">A per-channel master switch. When ON, the merchant-specific fee defined here overrides the global rate.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CASHOUT CHANNEL LIST</strong></td><td class="p-3 border-0">All outbound channels available for this merchant (e.g., `BI-FAST`, `Manual Transfer`). Each has its own fee row.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED FEE (Rp)</strong></td><td class="p-3 border-0">A flat Rupiah fee charged each time a withdrawal/disbursement is processed (e.g., Rp 5,000).</td></tr>
                        <tr><td class="p-3 border-0"><strong>PERCENTAGE FEE (%)</strong></td><td class="p-3 border-0">A percentage of the total cashout amount charged as a service fee (e.g., 0.3%).</td></tr>
                        <tr><td class="p-3 border-0"><strong>MINIMUM FEE (Rp)</strong></td><td class="p-3 border-0">The floor amount for the percentage fee calculation. If the percentage calculation falls below this, this amount is charged instead.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Cashout Fee Lifecycle</h5>
            <p class="text-muted mb-4">The cashout fee is securely deducted from the merchant's <strong>Available Balance</strong> the moment a request is submitted. Here is the flow:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Cashout Request]) --> B{Custom Fee<br>Enabled?}
                        B -- Yes --> C[Calculate % Fee + Fixed Fee]
                        B -- No --> D[Apply Global Cashout Fee]
                        C --> E{Calculated Fee<br>< Minimum Fee?}
                        E -- Yes --> F[Apply Minimum Fee]
                        E -- No --> G[Apply Calculated Fee]
                        D --> H[Deduct Principal + Fee<br>Put on HOLD]
                        F --> H
                        G --> H
                        H --> I{Bank Callback}
                        I -- SUCCESS --> J[(Release Hold, Send Funds)]
                        I -- FAILED --> K[(Refund Principal + Fee<br>to Balance)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Merchant submits a cashout request (withdrawal to bank / BI-FAST disbursement).</li>
                    <li class="mb-3">System checks if a <strong>Custom Cashout Fee</strong> is enabled for this specific merchant and channel. If YES, it applies the custom fee; if NO, it falls back to the global platform fee.</li>
                    <li class="mb-3">Both the principal amount AND the calculated fee are immediately deducted from Available Balance and placed on <strong>Hold</strong> while the bank transfer runs.</li>
                    <li class="mb-2">Upon final resolution from the banking network, the Hold is released. If SUCCESS, the principal is sent out. If FAILURE, both principal and fee are returned to Available Balance.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Configuring a Custom Cashout Fee</h5>
            <p class="text-muted mb-4">You can set customized outbound fees for high-volume merchants to encourage greater liquidity utilization.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Merchant Setup</strong>, find the target merchant, and open their detail page.</li>
                    <li class="mb-3">In the action menu (⋮), click <strong>Cashout Settings</strong>.</li>
                    <li class="mb-3">Locate the desired cashout channel row (e.g., <code>BI-FAST</code>) and toggle the <strong>Custom Fee</strong> switch to <strong>ON</strong>.</li>
                    <li class="mb-3">Enter your negotiated <strong>Fixed Fee</strong> (Rp), <strong>Percentage Fee</strong> (%), and optionally the <strong>Minimum Fee</strong> (Rp).</li>
                    <li class="mb-2">Click <strong>Save</strong>. The updated fee takes effect instantly for all new cashout requests.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Verifying Fee Deductions</strong>
                    <p class="mb-0 text-muted small">After a merchant performs a cashout, check their <strong>Mutation Log</strong>. You will see two separate <span class="text-danger font-weight-bold">Debit</span> entries: one for the principal cashout amount, and one labeled <em>Cashout Fee</em> representing the fee deducted.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mco_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: What happens to pending disbursements when I change the fee?
                </a>
                <div id="faq_en_mco_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolution:</strong> Fees are locked in at the exact moment the cashout request is created. Changing the fee here will only affect brand-new cashout requests generated after you click save. Existing pending requests are untouched.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mco_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can I set a cashout fee to zero?
                </a>
                <div id="faq_en_mco_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Yes. Set both Fixed and Percentage fees to 0, and ensure Custom Fee is ON. The merchant will not be charged. However, the upstream banking network may still charge the platform for the transfer.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mco_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The minimum fee field — when does it exactly apply?
                </a>
                <div id="faq_en_mco_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The Minimum Fee acts as a safety floor. For example, if you set the Minimum Fee to Rp 2,500 and the percentage fee on a tiny cashout calculates to only Rp 1,500, the system overrides the percentage calculation and charges the flat Rp 2,500 minimum instead.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Cashout Fee Settings</strong> mengontrol seberapa besar biaya yang dikenakan ke merchant saat mereka menarik saldo ke bank eksternal atau melakukan pencairan dana (<em>disbursement</em>) via <a href="javascript:void(0);" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-bifast\']').click()" class="text-primary font-weight-bold text-decoration-none">BI-FAST Transfer</a>. Seperti modul Cashin, ini mengatur kustomisasi (<em>override</em>) khusus per merchant atas biaya global.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Kolom Data</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Kolom / Isian</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CUSTOM FEE TOGGLE</strong></td><td class="p-3 border-0">Sakelar utama per kanal. Saat ON, tarif khusus merchant akan menggantikan tarif global.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CASHOUT CHANNEL LIST</strong></td><td class="p-3 border-0">Daftar kanal pencairan (misal: `BI-FAST`, `Manual Transfer`). Setiap baris mengatur satu kanal spesifik.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED FEE (Rp)</strong></td><td class="p-3 border-0">Potongan tetap dalam Rupiah setiap kali cashout diproses (misal: Rp 5.000).</td></tr>
                        <tr><td class="p-3 border-0"><strong>PERCENTAGE FEE (%)</strong></td><td class="p-3 border-0">Persentase dari total nilai cashout yang dijadikan biaya layanan (misal: 0.3%).</td></tr>
                        <tr><td class="p-3 border-0"><strong>MINIMUM FEE (Rp)</strong></td><td class="p-3 border-0">Batas bawah untuk perhitungan biaya persentase. Jika kalkulasi persentase lebih rendah dari ini, sistem memakai angka minimum ini.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Siklus Biaya Cashout</h5>
            <p class="text-muted mb-4">Biaya cashout langsung dipotong dari <strong>Available Balance</strong> merchant saat pengajuan (request) dibuat. Berikut alurnya:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Permintaan Cashout]) --> B{Custom Fee<br>Aktif?}
                        B -- Ya --> C[Kalkulasi Fee % + Fee Tetap]
                        B -- Tidak --> D[Gunakan Tarif Global]
                        C --> E{Hasil Kalkulasi<br>< Minimum Fee?}
                        E -- Ya --> F[Terapkan Minimum Fee]
                        E -- Tidak --> G[Terapkan Fee Kalkulasi]
                        D --> H[Potong Pokok + Biaya<br>Lalu status HOLD]
                        F --> H
                        G --> H
                        H --> I{Respons Bank}
                        I -- SUCCESS --> J[(Lepas Hold, Dana Cair)]
                        I -- FAILED --> K[(Refund Pokok + Biaya<br>ke Saldo)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Merchant mengajukan permintaan cashout (penarikan atau disbursement BI-FAST).</li>
                    <li class="mb-3">Sistem mengecek <strong>Custom Cashout Fee</strong>. Jika aktif (YA) pada kanal dan merchant terkait, sistem pakai tarif tersebut. Jika tidak aktif (TIDAK), sistem memakai tarif global platform.</li>
                    <li class="mb-3">Nilai pokok transfer DAN biaya admin langsung dipotong dari Available Balance dan masuk ke status <strong>Hold</strong> selama bank memproses transaksi.</li>
                    <li class="mb-2">Setelah bank memberikan status akhir, Hold dilepas. Jika BERHASIL (SUCCESS), dana pokok terkirim ke tujuan. Jika GAGAL (FAILURE), seluruh nilai pokok beserta biayanya dikembalikan utuh ke Available Balance.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengatur Custom Cashout Fee</h5>
            <p class="text-muted mb-4">Anda bisa mengatur tarif khusus untuk merchant bervolume tinggi agar lebih kompetitif di sisi pencairan dana.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Buka <strong>Merchant Setup</strong>, cari merchant target, lalu masuk ke laman detail merchant tersebut.</li>
                    <li class="mb-3">Di menu aksi (⋮), klik <strong>Cashout Settings</strong>.</li>
                    <li class="mb-3">Temukan baris kanal pencairan yang dituju (misal: <code>BI-FAST</code>) lalu geser tombol <strong>Custom Fee</strong> ke posisi <strong>ON</strong>.</li>
                    <li class="mb-3">Ketikkan <strong>Fixed Fee</strong> (Rp), <strong>Percentage Fee</strong> (%), dan isikan juga <strong>Minimum Fee</strong> (Rp) bila diperlukan.</li>
                    <li class="mb-2">Klik <strong>Save</strong>. Tarif baru akan aktif seketika untuk semua perintah cashout baru.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Memeriksa Detail Potongan Biaya</strong>
                    <p class="mb-0 text-muted small">Setelah merchant melakukan cashout, periksa tab <strong>Mutation Log</strong> mereka. Anda akan mendapati dua entri <span class="text-danger font-weight-bold">Debit</span> terpisah: satu untuk dana pokok transfer, dan satu lagi dengan keterangan <em>Cashout Fee</em> sebagai potongan layanan.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mco_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Apa yang terjadi pada pending disbursement saat biaya diubah?
                </a>
                <div id="faq_id_mco_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolusi:</strong> Tarif dikunci di detik permintaan dibuat. Merubah fee di sini cuma berpengaruh pada pengajuan (request) baru setelah Anda menekan Save. Pengajuan pending tak akan terpengaruh sama sekali.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mco_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bolehkah menyetel biaya admin cashout ke 0?
                </a>
                <div id="faq_id_mco_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ya. Atur isian Fixed dan Percentage ke angka 0, pastikan sakelar Custom Fee dalam posisi ON. Merchant akan dibebaskan dari biaya. Tetapi perlu diingat, tagihan dari gateway upstream bank tetap menjadi beban platform.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mco_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kapan persisnya batas isian "Minimum Fee" ini diaktifkan?
                </a>
                <div id="faq_id_mco_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Minimum Fee ibarat jaring pengaman pendapatan platform. Contoh: jika Minimum Fee Rp 2.500 dan hasil kalkulasi persentase dari cashout kecil merchant cuma berbuah Rp 1.500, maka sistem akan mengabaikan hitungan persentase dan langsung membebankan biaya tetap Rp 2.500.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
