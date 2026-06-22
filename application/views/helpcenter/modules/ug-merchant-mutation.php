<div id="module-ug-merchant-mutation" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Mutation Log is an immutable, chronologically ordered ledger that tracks every single movement of funds into or out of a merchant's available balance. It acts as the definitive source of truth for financial reconciliation.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Ledger Columns</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Column</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Timestamp</strong></td><td class="p-3 border-0">The exact millisecond when the ledger was updated.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Position</strong></td><td class="p-3 border-0">Indicates if funds were added (<code>Credit</code> / <span class="text-success font-weight-bold">+</span>) or deducted (<code>Debit</code> / <span class="text-danger font-weight-bold">-</span>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel &amp; Description</strong></td><td class="p-3 border-0">Explains exactly <em>why</em> the mutation occurred (e.g., "Settlement from VA BCA" or "Cashout Fee Deduction"). Includes internal reference codes linking back to the transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Amount</strong></td><td class="p-3 border-0">The absolute monetary value of the mutation.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Balance</strong></td><td class="p-3 border-0">A cumulative running total showing the exact available balance of the merchant <em>immediately after</em> this specific mutation occurred.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Searching & Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Searching & Filtering</h5>
            <p class="text-muted mb-4">Use the built-in filters to track down specific mutation records.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Filters:</strong> You can filter by <strong>Filter Date</strong>, <strong>Position</strong> (Credit/Debit), and <strong>Channel Source</strong>.</li>
                    <li class="mb-2">Click the area outside the dropdown to load the data. Active filters will narrow down the ledger entries accordingly. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Section 1: Business vs Ledger -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> 1. Business Events vs Ledger Events</h5>
            <p class="text-muted mb-4">It is critical to understand the difference between a Transaction Report and a Mutation Log when diagnosing balance issues.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Unlike <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-report'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Transaction Reports</a> which list <strong>business events</strong> (e.g., "Invoice 123 was paid"), the Mutation Log exclusively lists <strong>ledger events</strong> ("Money physically moved in/out").</li>
                    <li class="mb-3">A single business transaction might generate <strong>multiple mutation logs</strong>. For example, a successful QRIS payment will generate a Credit mutation for the gross principal amount, followed instantly by a Debit mutation for the MDR system fee deduction.</li>
                    <li class="mb-2">If you add a date filter, it filters by the exact time the funds were moved, which may differ from the time the customer originally created the invoice.</li>
                </ol>
            </div>

            <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Double-Entry Ledger Flow Example</h6>
                <div class="mermaid">
                sequenceDiagram
                    participant B as Business Event (e.g. QRIS Paid)
                    participant L as Ledger System
                    participant MB as Master Balance
                    
                    B->>L: 1. Gross Principal Received
                    L->>MB: Credit (+) Amount
                    B->>L: 2. System Fee Deduction (MDR)
                    L->>MB: Debit (-) Fee
                    Note over MB: Final Balance = Previous + Amount - Fee
                </div>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-lock"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Immutability Rules</strong>
                    <p class="mb-0 text-muted small">Mutations are permanent, immutable ledger records. <strong>They cannot be deleted or edited under any circumstances</strong>. If a mistake was made (e.g., an accidental manual credit adjustment), you must create a new, opposing transaction (a manual debit) to correct the balance and balance the books.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mu_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Missing mutation for a recent transaction
                </a>
                <div id="faq_en_mu_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> A customer paid via QRIS, but the mutation log doesn't show the balance increasing.<br><br>
                        <strong>Resolution:</strong> The transaction is likely still in the Pending or Hold state. Funds only appear as a "Credit" in the mutation log once the transaction reaches the final, cleared <strong>Settled / Success</strong> state.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mu_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Unexplained negative mutations (Debits)
                </a>
                <div id="faq_en_mu_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The merchant sees unexpected money leaving their account.<br><br>
                        <strong>Resolution:</strong> Debits happen during withdrawals (disbursements), refunds, system fees (if your platform deducts fees from balance rather than from the transaction principal), or manual admin deductions. Check the "Description" column and matching Reference ID for the exact reason.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mu_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The running Balance column looks out of order
                </a>
                <div id="faq_en_mu_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The Balance column numbers don't seem to add up linearly when looking at the table.<br><br>
                        <strong>Resolution:</strong> The table is sorted by default descending (newest first). If you read it top-to-bottom, the balance will appear to go "backwards". To track a balance linearly from a specific date, export the log to Excel and sort it chronologically (oldest first).
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Log Mutasi adalah buku besar kronologis yang tidak dapat diubah, melacak setiap pergerakan dana masuk atau keluar dari saldo merchant. Ini bertindak sebagai sumber kebenaran definitif untuk rekonsiliasi keuangan.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Kolom Buku Besar</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Kolom</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Timestamp</strong></td><td class="p-3 border-0">Milidetik yang tepat saat buku besar diperbarui.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Position</strong></td><td class="p-3 border-0">Menandakan apakah dana ditambahkan (<code>Credit</code> / <span class="text-success font-weight-bold">+</span>) atau dipotong (<code>Debit</code> / <span class="text-danger font-weight-bold">-</span>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel &amp; Description</strong></td><td class="p-3 border-0">Menjelaskan secara spesifik <em>mengapa</em> mutasi terjadi (mis. "Settlement from VA BCA" atau "Cashout Fee Deduction"). Termasuk kode referensi internal yang menautkan kembali ke transaksi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Amount</strong></td><td class="p-3 border-0">Nilai nominal absolut dari mutasi tersebut.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Balance</strong></td><td class="p-3 border-0">Total kumulatif berjalan yang menunjukkan saldo tersedia yang tepat dari merchant <em>segera setelah</em> mutasi spesifik ini terjadi.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pencarian & Pemfilteran -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Pencarian & Pemfilteran</h5>
            <p class="text-muted mb-4">Gunakan filter bawaan untuk melacak catatan mutasi spesifik.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Filter:</strong> Anda dapat memfilter berdasarkan <strong>Filter Date</strong>, <strong>Position</strong> (Kredit/Debit), dan <strong>Channel Source</strong>.</li>
                    <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter yang aktif akan mempersempit entri buku besar yang ditampilkan. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Section 1: Business vs Ledger -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> 1. Event Bisnis vs Event Buku Besar</h5>
            <p class="text-muted mb-4">Sangat penting untuk memahami perbedaan antara Laporan Transaksi dan Log Mutasi saat mendiagnosis masalah saldo.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Berbeda dengan <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-report'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Laporan Transaksi</a> yang mencantumkan <strong>event bisnis</strong> (mis. "Invoice 123 telah dibayar"), Log Mutasi secara eksklusif mencantumkan <strong>event buku besar</strong> ("Uang secara fisik masuk/keluar").</li>
                    <li class="mb-3">Satu transaksi bisnis dapat menghasilkan <strong>beberapa log mutasi</strong>. Misalnya, pembayaran QRIS yang berhasil akan menghasilkan mutasi Kredit untuk jumlah pokok bruto, diikuti seketika oleh mutasi Debit untuk pemotongan biaya sistem MDR.</li>
                    <li class="mb-2">Jika Anda menambahkan filter tanggal, filter tersebut menyaring berdasarkan waktu yang tepat saat dana dipindahkan, yang mungkin berbeda dari waktu pelanggan membuat invoice awal.</li>
                </ol>
            </div>

            <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Contoh Alur Double-Entry Ledger</h6>
                <div class="mermaid">
                sequenceDiagram
                    participant B as Event Bisnis (mis. QRIS Dibayar)
                    participant L as Sistem Buku Besar
                    participant MB as Master Balance
                    
                    B->>L: 1. Pokok Bruto Diterima
                    L->>MB: Kredit (+) Nominal
                    B->>L: 2. Potongan Biaya Sistem (MDR)
                    L->>MB: Debit (-) Biaya
                    Note over MB: Saldo Akhir = Saldo Sebelumnya + Nominal - Biaya
                </div>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-lock"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Aturan Imutabilitas (Tidak Dapat Diubah)</strong>
                    <p class="mb-0 text-muted small">Mutasi adalah catatan buku besar permanen yang tidak dapat diubah. <strong>Mutasi tidak dapat dihapus atau diedit dalam keadaan apa pun</strong>. Jika ada kesalahan (mis. penyesuaian kredit manual yang tidak disengaja), Anda harus membuat transaksi berlawanan baru (debit manual) untuk mengoreksi saldo dan menyeimbangkan pembukuan.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mu_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Mutasi tidak muncul untuk transaksi terbaru
                </a>
                <div id="faq_id_mu_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Pelanggan telah membayar via QRIS, namun log mutasi tidak menunjukkan saldo bertambah.<br><br>
                        <strong>Resolusi:</strong> Transaksi kemungkinan masih berstatus Pending atau Hold. Dana hanya akan muncul sebagai "Credit" di log mutasi setelah transaksi mencapai status akhir, yakni <strong>Settled / Success</strong>.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mu_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Ada mutasi negatif (Debit) yang tidak dapat dijelaskan
                </a>
                <div id="faq_id_mu_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Merchant melihat uang keluar dari akun mereka tanpa diduga.<br><br>
                        <strong>Resolusi:</strong> Debit terjadi selama pencairan dana (withdrawal), pengembalian dana (refund), biaya sistem (jika platform Anda memotong biaya dari saldo alih-alih dari pokok transaksi), atau pemotongan manual oleh admin. Periksa kolom "Description" dan ID Referensi yang cocok untuk mengetahui alasannya.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mu_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kolom Balance berjalan terlihat tidak berurutan
                </a>
                <div id="faq_id_mu_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Angka-angka pada kolom Balance tampaknya tidak terakumulasi secara linier saat melihat tabel.<br><br>
                        <strong>Resolusi:</strong> Secara default, tabel diurutkan secara menurun/descending (yang terbaru di atas). Jika Anda membacanya dari atas ke bawah, saldo akan tampak berjalan "mundur". Untuk melacak saldo secara linier dari tanggal tertentu, ekspor log ke Excel dan urutkan secara kronologis (yang terlama di atas).
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>