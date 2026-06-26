<div id="module-ug-merchant-mutation" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Mutation Log is an immutable, chronologically ordered ledger that tracks every single movement of funds into or out of a merchant's available balance. It acts as the definitive source of truth for financial reconciliation.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Navigating the Mutation Log</h3>
                <p class="text-muted mb-4">Follow these steps to search and filter specific ledger entries.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to the merchant's detail dashboard and click the <strong>Mutation Log</strong> tab at the bottom.</li>
                        <li class="mb-3">By default, the table will list all recent mutations sorted from newest to oldest.</li>
                        <li class="mb-3">To find mutations for a specific day or week, select a range using the <strong>Filter Date</strong> input.</li>
                        <li class="mb-3">To isolate only incoming funds or outgoing funds, set the <strong>Position</strong> dropdown to <code>Credit</code> or <code>Debit</code>.</li>
                        <li class="mb-2">Click outside the dropdown to apply filters and load the data. Click <strong>Clear All</strong> to reset the view.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-warning shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-lock"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Immutability Rules</strong>
                <p class="mb-0 text-muted small">Mutations are permanent, immutable ledger records. <strong>They cannot be deleted or edited under any circumstances</strong>. If a mistake was made (e.g., an accidental manual credit adjustment), you must create a new, opposing transaction (a manual debit) to correct the balance and balance the books.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ledger Columns Reference</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
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

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> Business Events vs Ledger Events</h5>
        <p class="text-muted mb-4 small">It is critical to understand the difference between a Transaction Report and a Mutation Log when diagnosing balance issues.</p>
        <div class="pl-4 border-left border-primary ml-2 mb-4">
            <ol class="text-muted small mb-0">
                <li class="mb-3">Unlike <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-report'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Transaction Reports</a> which list <strong>business events</strong> (e.g., "Invoice 123 was paid"), the Mutation Log exclusively lists <strong>ledger events</strong> ("Money physically moved in/out").</li>
                <li class="mb-3">A single business transaction might generate <strong>multiple mutation logs</strong>. For example, a successful QRIS payment will generate a Credit mutation for the gross principal amount, followed instantly by a Debit mutation for the MDR system fee deduction.</li>
                <li class="mb-2">If you add a date filter, it filters by the exact time the funds were moved, which may differ from the time the customer originally created the invoice.</li>
            </ol>
        </div>

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
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

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Missing mutation for a recent transaction</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> The transaction is likely still in the Pending or Hold state. Funds only appear as a "Credit" in the mutation log once the transaction reaches the final, cleared <strong>Settled / Success</strong> state.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Unexplained negative mutations (Debits)</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Debits happen during withdrawals (disbursements), refunds, system fees (if your platform deducts fees from balance rather than from the transaction principal), or manual admin deductions. Check the "Description" column and matching Reference ID for the exact reason.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>The running Balance column looks out of order</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> The table is sorted by default descending (newest first). If you read it top-to-bottom, the balance will appear to go "backwards". To track a balance linearly from a specific date, export the log to Excel and sort it chronologically (oldest first).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Can I export the Mutation Log for accounting software?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Yes, most implementations provide an "Export to CSV/Excel" button near the filters. This exported data is ideal for reconciliation in external accounting systems because it mirrors the exact database ledger.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why are there two entries for one single QRIS payment?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> This occurs due to "Gross Settlement". The system logs one Credit entry for the full amount paid by the customer, and a separate Debit entry for the MDR system fee. This ensures total transparency of gross revenues and expenses.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Log Mutasi adalah buku besar kronologis yang tidak dapat diubah, melacak setiap pergerakan dana masuk atau keluar dari saldo merchant. Ini bertindak sebagai sumber kebenaran definitif untuk rekonsiliasi keuangan.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Menavigasi Log Mutasi</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk mencari dan memfilter entri buku besar tertentu.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Akses dasbor detail merchant dan klik tab <strong>Mutation Log</strong> di bagian bawah.</li>
                        <li class="mb-3">Secara bawaan (default), tabel akan menampilkan semua mutasi terbaru yang diurutkan dari yang paling baru ke yang paling lama.</li>
                        <li class="mb-3">Untuk mencari mutasi pada hari atau minggu tertentu, pilih rentang tanggal pada input <strong>Filter Date</strong>.</li>
                        <li class="mb-3">Untuk mengisolasi hanya dana masuk atau dana keluar, atur dropdown <strong>Position</strong> ke <code>Credit</code> atau <code>Debit</code>.</li>
                        <li class="mb-2">Klik area di luar dropdown untuk menerapkan filter dan memuat data. Klik <strong>Clear All</strong> untuk mengatur ulang tampilan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-warning shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-lock"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Aturan Imutabilitas (Tidak Dapat Diubah)</strong>
                <p class="mb-0 text-muted small">Mutasi adalah catatan buku besar permanen yang tidak dapat diubah. <strong>Mutasi tidak dapat dihapus atau diedit dalam keadaan apa pun</strong>. Jika ada kesalahan (mis. penyesuaian kredit manual yang tidak disengaja), Anda harus membuat transaksi berlawanan baru (debit manual) untuk mengoreksi saldo dan menyeimbangkan pembukuan.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Referensi Kolom Buku Besar</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
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

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-exchange-alt text-primary mr-2"></i> Event Bisnis vs Event Buku Besar</h5>
        <p class="text-muted mb-4 small">Sangat penting untuk memahami perbedaan antara Laporan Transaksi dan Log Mutasi saat mendiagnosis masalah saldo.</p>
        <div class="pl-4 border-left border-primary ml-2 mb-4">
            <ol class="text-muted small mb-0">
                <li class="mb-3">Berbeda dengan <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-report'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Laporan Transaksi</a> yang mencantumkan <strong>event bisnis</strong> (mis. "Invoice 123 telah dibayar"), Log Mutasi secara eksklusif mencantumkan <strong>event buku besar</strong> ("Uang secara fisik masuk/keluar").</li>
                <li class="mb-3">Satu transaksi bisnis dapat menghasilkan <strong>beberapa log mutasi</strong>. Misalnya, pembayaran QRIS yang berhasil akan menghasilkan mutasi Kredit untuk jumlah pokok bruto, diikuti seketika oleh mutasi Debit untuk pemotongan biaya sistem MDR.</li>
                <li class="mb-2">Jika Anda menambahkan filter tanggal, filter tersebut menyaring berdasarkan waktu yang tepat saat dana dipindahkan, yang mungkin berbeda dari waktu pelanggan membuat invoice awal.</li>
            </ol>
        </div>

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
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

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mutasi tidak muncul untuk transaksi terbaru</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Transaksi kemungkinan masih berstatus Pending atau Hold. Dana hanya akan muncul sebagai "Credit" di log mutasi setelah transaksi mencapai status akhir, yakni <strong>Settled / Success</strong>.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Ada mutasi negatif (Debit) yang tidak dapat dijelaskan</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Debit terjadi selama pencairan dana (withdrawal), pengembalian dana (refund), biaya sistem (jika platform Anda memotong biaya dari saldo alih-alih dari pokok transaksi), atau pemotongan manual oleh admin. Periksa kolom "Description" dan ID Referensi yang cocok untuk mengetahui alasannya.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Kolom Balance berjalan terlihat tidak berurutan</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Secara default, tabel diurutkan secara menurun/descending (yang terbaru di atas). Jika Anda membacanya dari atas ke bawah, saldo akan tampak berjalan "mundur". Untuk melacak saldo secara linier dari tanggal tertentu, ekspor log ke Excel dan urutkan secara kronologis (yang terlama di atas).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bisakah saya mengekspor Log Mutasi untuk software akuntansi?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Ya, sebagian besar implementasi menyediakan tombol "Export to CSV/Excel" di dekat filter. Data yang diekspor ini sangat ideal untuk rekonsiliasi di sistem akuntansi eksternal karena mencerminkan buku besar database secara persis.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa ada dua entri mutasi untuk satu pembayaran QRIS tunggal?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Ini terjadi karena adanya "Gross Settlement". Sistem mencatat satu entri Kredit untuk jumlah penuh yang dibayarkan pelanggan, dan entri Debit terpisah untuk biaya sistem MDR. Hal ini memastikan transparansi total untuk pendapatan bruto dan pengeluaran.</p>
        </div>
    </div>
</div>