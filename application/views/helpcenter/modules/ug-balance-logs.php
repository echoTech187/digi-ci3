<div id="module-ug-balance-logs" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Balance Logs (Mutation Log) page is the master audit trail for all merchants. It tracks every single financial movement — credits, debits, holds, and releases — providing a perfect, immutable timeline of all fund flows across the platform.</p>

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
                        <tr><td class="p-3 border-0"><strong>Date Time</strong></td><td class="p-3 border-0">The exact server timestamp when the balance was updated. Always in the server's local timezone.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Merchant</strong></td><td class="p-3 border-0">The merchant whose balance was affected. Used when viewing logs across all merchants (global view).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Position</strong></td><td class="p-3 border-0">Direction of the movement: <span class="badge badge-success-soft text-success">Credit</span> = adds funds to the balance. <span class="badge badge-danger-soft text-danger">Debit</span> = removes funds from the balance.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel</strong></td><td class="p-3 border-0">The product or payment route that triggered the mutation (e.g., <code>va_bca</code>, <code>qris_mpm</code>, <code>cashout</code>, <code>admin_adjustment</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Description</strong></td><td class="p-3 border-0">Internal notes that link the mutation to a specific Invoice ID, Cashout reference, or system action. Use this to trace a specific transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Amount</strong></td><td class="p-3 border-0">The exact monetary value of this specific movement in IDR.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Balance</strong></td><td class="p-3 border-0">The <strong>Running Balance</strong> of the merchant's account <em>after</em> this mutation was applied. Verify this matches the Dashboard balance at end-of-day.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Auditing the Ledger — Step-by-Step</h3>
                <p class="text-muted mb-4">Use double-entry accounting principles to trace specific financial anomalies. The Balance Log is the single source of truth for all fund movements.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to <strong>Finance &amp; Treasury → Audit Balance Logs</strong>.</li>
                        <li class="mb-3">Click <strong><i class="fas fa-sliders-h"></i> Filters</strong>. Configure your parameters like <strong>MERCHANT</strong> and <strong>REQUEST DATE</strong>, then click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                        <li class="mb-3">Read the <strong>Position</strong> column: <span class="badge badge-success-soft text-success">Credit</span> adds funds (e.g., a successful Cashin payment settled), <span class="badge badge-danger-soft text-danger">Debit</span> removes funds (e.g., a Cashout disbursement or fee deduction).</li>
                        <li class="mb-3">If tracing a missing payment, type the <strong>Invoice ID</strong> in the Quick Search box. Look for a <strong>Credit</strong> mutation with that ID in the Description column — if it exists, the payment was received and credited successfully.</li>
                        <li class="mb-3">Verify the <strong>Balance</strong> column is consistent: each row's balance should equal the previous row's balance ± the current row's Amount. Any jump indicates a data anomaly.</li>
                        <li class="mb-2">To export the ledger for accounting, use the <strong>Download Excel</strong> action (same async export mechanism as the Transaction History module).</li>
                    </ol>
                </div>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-lock"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Immutable Ledger — No Manual Edits</strong>
                        <p class="mb-0 text-muted small">The mutation log cannot be edited or deleted by any admin, including Super Admins. This is by design. All corrections must be made by adding a new <strong>reversal mutation</strong> (e.g., a Credit to reverse a wrong Debit) to maintain a complete, tamper-proof audit trail. Contact your system administrator to perform a manual adjustment mutation.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Understanding Mutation Types</h3>
                <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:30%">Channel / Type</th>
                                <th class="p-3 border-0" style="width:15%">Position</th>
                                <th class="p-3 border-0">Trigger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>va_bca</code>, <code>qris_mpm</code>, etc.</td><td class="p-3 border-0"><span class="badge badge-success-soft text-success">Credit</span></td><td class="p-3 border-0">A Cashin payment from a customer settled successfully. Funds added to merchant's Available Balance.</td></tr>
                            <tr><td class="p-3 border-0"><code>cashout</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">A Cashout disbursement was initiated. Funds removed from Available and placed into Hold, then released after bank confirmation.</td></tr>
                            <tr><td class="p-3 border-0"><code>admin_credit</code></td><td class="p-3 border-0"><span class="badge badge-success-soft text-success">Credit</span></td><td class="p-3 border-0">Manual balance top-up by an admin (e.g., settlement correction, deposit).</td></tr>
                            <tr><td class="p-3 border-0"><code>admin_debit</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">Manual balance deduction by an admin (e.g., fee collection, over-credit reversal).</td></tr>
                            <tr><td class="p-3 border-0"><code>fee_deduction</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">Automatic MDR or admin fee batch deduction at end of settlement period.</td></tr>
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
                <span>Issue 1: Can mutations be edited or deleted?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> Absolutely not. The mutation log is an immutable ledger. No admin — including Super Admins — can edit or delete existing records. All corrections must be done by adding a new <strong>reversal mutation</strong> (e.g., a Credit to cancel a wrong Debit) to ensure a complete, tamper-proof audit trail. Contact your system administrator to request a manual adjustment.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: What does a "Hold" mutation mean?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> When a merchant initiates a Cashout, the funds are immediately moved from <em>Available Balance</em> to <em>Hold</em>. This appears as a <code>cashout_hold</code> Debit mutation. Once the bank transfer succeeds and is confirmed, a second <code>cashout_release</code> Debit mutation removes the held funds permanently. If the transfer fails, a corresponding Credit mutation returns the funds to Available.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Why is there a Debit without a corresponding withdrawal request?</span>
            </div>
            <p class="hc-faq-a"><strong>Answer:</strong> This is typically an automatic <strong>admin fee</strong> or <strong>MDR deduction</strong>. If the merchant is set to gross-settlement mode, fees are batched and charged automatically at the end of the settlement period rather than deducted per-transaction. Check the Description column for <code>fee_deduction</code> or <code>mdr_batch</code> labels to confirm.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman Balance Logs (Log Mutasi) adalah jejak audit utama untuk semua merchant. Halaman ini melacak setiap pergerakan finansial — kredit, debit, penahanan (hold), dan pelepasan (release) — memberikan garis waktu yang sempurna dan tidak dapat diubah dari semua alur dana di platform.</p>

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
                        <tr><td class="p-3 border-0"><strong>Date Time</strong></td><td class="p-3 border-0">Stempel waktu server tepat saat saldo diperbarui. Selalu dalam zona waktu lokal server.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Merchant</strong></td><td class="p-3 border-0">Merchant yang saldonya terpengaruh. Digunakan saat melihat log lintas semua merchant (tampilan global).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Position</strong></td><td class="p-3 border-0">Arah pergerakan: <span class="badge badge-success-soft text-success">Credit</span> = menambah dana ke saldo. <span class="badge badge-danger-soft text-danger">Debit</span> = mengurangi dana dari saldo.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel</strong></td><td class="p-3 border-0">Produk atau rute pembayaran yang memicu mutasi (mis. <code>va_bca</code>, <code>qris_mpm</code>, <code>cashout</code>, <code>admin_adjustment</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Description</strong></td><td class="p-3 border-0">Catatan internal yang menghubungkan mutasi ke Invoice ID, referensi Cashout, atau tindakan sistem tertentu. Gunakan ini untuk melacak transaksi spesifik.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Amount</strong></td><td class="p-3 border-0">Nilai moneter persis dari pergerakan ini dalam IDR.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Balance</strong></td><td class="p-3 border-0"><strong>Running Balance</strong> (Saldo Berjalan) akun merchant <em>setelah</em> mutasi ini diterapkan. Verifikasi ini cocok dengan saldo Dashboard di akhir hari.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Mengaudit Buku Besar — Langkah-demi-Langkah</h3>
                <p class="text-muted mb-4">Gunakan prinsip akuntansi entri ganda untuk melacak anomali keuangan tertentu. Balance Log adalah sumber kebenaran tunggal untuk semua pergerakan dana.</p>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigasikan ke <strong>Finance &amp; Treasury → Audit Balance Logs</strong>.</li>
                        <li class="mb-3">Klik tombol <strong><i class="fas fa-sliders-h"></i> Filters</strong>. Atur parameter <strong>MERCHANT</strong> spesifik dan <strong>REQUEST DATE</strong>, lalu klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                        <li class="mb-3">Baca kolom <strong>Position</strong>: <span class="badge badge-success-soft text-success">Credit</span> menambah dana (mis. pembayaran Cashin ter-settle), <span class="badge badge-danger-soft text-danger">Debit</span> mengurangi dana (mis. pencairan Cashout atau pemotongan biaya).</li>
                        <li class="mb-3">Jika melacak pembayaran yang hilang, ketik <strong>Invoice ID</strong> di kotak Pencarian Cepat. Cari mutasi <strong>Credit</strong> dengan ID tersebut di kolom Description — jika ada, pembayaran berhasil diterima dan dikreditkan.</li>
                        <li class="mb-3">Verifikasi kolom <strong>Balance</strong> konsisten: saldo setiap baris harus sama dengan saldo baris sebelumnya ± Amount baris saat ini. Lompatan apapun menunjukkan anomali data.</li>
                        <li class="mb-2">Untuk mengekspor buku besar ke akuntansi, gunakan aksi <strong>Download Excel</strong> (mekanisme ekspor asinkron yang sama seperti modul Transaction History).</li>
                    </ol>
                </div>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-lock"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Buku Besar Tidak Dapat Diubah — Tidak Ada Edit Manual</strong>
                        <p class="mb-0 text-muted small">Log mutasi tidak dapat diedit atau dihapus oleh admin mana pun, termasuk Super Admin. Ini disengaja. Semua koreksi harus dilakukan dengan menambahkan <strong>mutasi pembalikan</strong> baru (mis. Credit untuk membalik Debit yang salah) guna mempertahankan jejak audit yang lengkap dan anti-manipulasi. Hubungi administrator sistem Anda untuk melakukan mutasi penyesuaian manual.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Memahami Tipe Mutasi</h3>
                <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:30%">Channel / Tipe</th>
                                <th class="p-3 border-0" style="width:15%">Posisi</th>
                                <th class="p-3 border-0">Pemicu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>va_bca</code>, <code>qris_mpm</code>, dll.</td><td class="p-3 border-0"><span class="badge badge-success-soft text-success">Credit</span></td><td class="p-3 border-0">Pembayaran Cashin dari pelanggan ter-settle berhasil. Dana ditambahkan ke Saldo Tersedia merchant.</td></tr>
                            <tr><td class="p-3 border-0"><code>cashout</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">Pencairan Cashout dimulai. Dana dipindahkan dari Tersedia ke Hold, lalu dilepaskan setelah konfirmasi bank.</td></tr>
                            <tr><td class="p-3 border-0"><code>admin_credit</code></td><td class="p-3 border-0"><span class="badge badge-success-soft text-success">Credit</span></td><td class="p-3 border-0">Top-up saldo manual oleh admin (mis. koreksi settlement, setoran dana).</td></tr>
                            <tr><td class="p-3 border-0"><code>admin_debit</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">Pemotongan saldo manual oleh admin (mis. penagihan biaya, pembalikan over-credit).</td></tr>
                            <tr><td class="p-3 border-0"><code>fee_deduction</code></td><td class="p-3 border-0"><span class="badge badge-danger-soft text-danger">Debit</span></td><td class="p-3 border-0">Pemotongan batch MDR atau biaya admin otomatis di akhir periode settlement.</td></tr>
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
                <span>Masalah 1: Bisakah mutasi diedit atau dihapus?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Sama sekali tidak. Log mutasi adalah buku besar yang tidak dapat diubah (immutable). Tidak ada admin — termasuk Super Admin — yang dapat mengedit atau menghapus record yang sudah ada. Semua koreksi harus dilakukan dengan menambahkan <strong>mutasi pembalikan</strong> baru (mis. Credit untuk membatalkan Debit yang salah) guna memastikan jejak audit yang lengkap dan anti-manipulasi. Hubungi administrator sistem Anda untuk meminta penyesuaian manual.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Apa arti mutasi "Hold"?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Saat merchant memulai Cashout, dana segera dipindahkan dari <em>Saldo Tersedia</em> ke <em>Hold</em>. Ini muncul sebagai mutasi Debit <code>cashout_hold</code>. Setelah transfer bank dikonfirmasi berhasil, mutasi Debit kedua <code>cashout_release</code> menghapus dana yang ditahan secara permanen. Jika transfer gagal, mutasi Credit yang sesuai mengembalikan dana ke Saldo Tersedia.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Mengapa ada Debit tanpa permintaan penarikan dana?</span>
            </div>
            <p class="hc-faq-a"><strong>Jawaban:</strong> Ini biasanya adalah <strong>biaya admin</strong> atau <strong>pemotongan MDR</strong> otomatis. Jika merchant diatur ke mode gross-settlement, biaya di-batch dan ditagih secara otomatis di akhir periode settlement, bukan dipotong per-transaksi. Periksa kolom Description untuk label <code>fee_deduction</code> atau <code>mdr_batch</code> untuk memastikannya.</p>
        </div>
    </div>
</div>
