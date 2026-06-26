<div id="module-ug-merchant-debit" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Deduct Debit Balance</strong> feature is used to manually subtract funds from a merchant's available balance. This privileged operation is typically required for charging manual admin fees, processing fee-based refunds, correcting prior over-credits, or applying penalty deductions. Every debit is permanently recorded in the audit ledger.</p>

        <hr class="my-4">

        <!-- Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Conceptual Architecture</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Architecture: Debit Flow</h5>
            <p class="text-muted mb-4">A manual debit operation directly modifies the merchant's financial ledger as an <strong>atomic transaction</strong>:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Admin submits the debit form with an amount and a mandatory description.</li>
                    <li class="mb-3">The system checks if the deduction exceeds the current available balance (to avoid deficits, though allowed in some config states).</li>
                    <li class="mb-3">The merchant's <strong>Available Balance</strong> and <strong>Total Balance</strong> are instantly <span class="text-danger font-weight-bold">reduced</span> by the specified amount.</li>
                    <li class="mb-2">A new <strong>Debit</strong> mutation record is created permanently in the merchant's Mutation Log.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-danger shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Critical Warning: Irreversible Action</strong>
                    <p class="mb-0 text-muted small">Manual debits <strong>cannot be undone</strong>. If you input an incorrect amount, you must use the <em>Add Credit Balance</em> feature to restore the incorrectly deducted funds. Always verify the merchant's current Available Balance first.</p>
                </div>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Performing a Manual Deduction</h3>
                <p class="text-muted mb-4">Perform the following steps carefully to deduct funds from a merchant's account.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to <strong>Merchant Setup</strong> and locate the target merchant.</li>
                        <li class="mb-3">Click the action menu (⋮) and select <strong>Deduct Debit Balance</strong>.</li>
                        <li class="mb-3">Check the <strong>Current Available Balance</strong> to ensure the deduction amount will not cause a negative balance.</li>
                        <li class="mb-3">Enter the deduction <strong>Amount</strong> in Rupiah.</li>
                        <li class="mb-3">Fill in the mandatory <strong>Description</strong> field with a clear reason (e.g., <em>"Penalty for TOS violation - ref #123"</em>).</li>
                        <li class="mb-2">Click <strong>Submit</strong>. The balance updates instantly.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Parameter / Constraint</th>
                        <th class="p-3 border-0">Description & System Rule</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Read-only field confirming the target merchant account for deduction.</td></tr>
                    <tr><td class="p-3 border-0"><strong>CURRENT AVAILABLE BALANCE</strong></td><td class="p-3 border-0">Displays the balance before deduction. Prevents accidental negative balance situations.</td></tr>
                    <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">The Rupiah amount to deduct from the merchant's Available Balance. Must be a valid positive integer greater than zero. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">Mandatory text field explaining the reason for the deduction. Required for audit trail. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Executes the debit immediately and irreversibly.</td></tr>
                </tbody>
            </table>
        </div>

        <!-- System Notifications -->
        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
        <div class="d-flex flex-column mb-5">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-0"><strong>Balance Deducted:</strong> <code>Balance updated successfully.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Invalid Amount:</strong> <code>The amount must be greater than zero.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ensure you input a valid positive number for the debit amount.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Failed to update balance.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> System restriction or the MySQL user lacks INSERT/UPDATE privileges. Contact the Database Administrator.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Is there a limit on the deduction amount?</span>
            </div>
            <p class="hc-faq-a">There is no hard limit, but large deductions should be backed by proper authorization. Also, deducting more than the Available Balance may lead to a negative balance.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Can I reverse or delete a manual deduction?</span>
            </div>
            <p class="hc-faq-a">No, debit mutations are permanent. To fix a mistaken deduction, use the <em>Add Credit Balance</em> feature to inject the incorrectly taken funds back.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>The form is rejected on submission, why?</span>
            </div>
            <p class="hc-faq-a">The most common causes are an empty <strong>Description</strong> field (which is strictly required for auditing) or entering a non-positive amount.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-wallet text-success"></i> 
                <span>What happens if the deduction creates a negative balance?</span>
            </div>
            <p class="hc-faq-a">The system may allow negative balances depending on your core settings. If allowed, the merchant's available balance will drop below zero, preventing them from performing out-going transactions until the deficit is cleared.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-secondary"></i> 
                <span>Will the merchant receive a notification for this deduction?</span>
            </div>
            <p class="hc-faq-a">Currently, the system does not automatically dispatch an email alert for manual debit adjustments. You should notify the merchant through an external communication channel to prevent disputes.</p>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Fitur <strong>Deduct Debit Balance</strong> digunakan untuk memotong dana dari saldo merchant secara manual. Operasi ini biasanya dipakai untuk membebankan biaya admin tambahan, denda, atau mengoreksi salah transfer dana (over-credit) yang terjadi sebelumnya. Seluruh transaksi ini dicatat rapi dalam riwayat audit mutasi.</p>

        <hr class="my-4">

        <!-- Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Arsitektur Konseptual</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Arsitektur: Alur Debit</h5>
            <p class="text-muted mb-4">Transaksi manual ini memodifikasi saldo merchant secara <strong>atomik</strong>:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Admin mengisi nominal dan menyertakan deskripsi yang jelas.</li>
                    <li class="mb-3">Sistem mengevaluasi saldo saat ini, memastikan nilainya masuk akal untuk dikurangi (kendati saldo minus dimungkinkan untuk penalti besar).</li>
                    <li class="mb-3"><strong>Available Balance</strong> dan <strong>Total Balance</strong> seketika <span class="text-danger font-weight-bold">dipotong</span> sebesar nilai yang dicantumkan.</li>
                    <li class="mb-2">Entri mutasi bernilai negatif (<strong>Debit</strong>) dibuat permanen di buku besar mutasi merchant.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-danger shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Peringatan Kritis: Tak Dapat Dibatalkan & Uang Nyata</strong>
                    <p class="mb-0 text-muted small">Debit manual <strong>tidak bisa dibatalkan</strong>. Apabila Anda kelebihan memotong, gunakan menu <em>Add Credit Balance</em> untuk mengembalikan selisih uang tersebut. Selalu perhatikan saldo awal untuk mencegah defisit saldo.</p>
                </div>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Melakukan Pemotongan Manual</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk memotong dana merchant.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Masuk ke <strong>Merchant Setup</strong> lalu cari merchant yang bersangkutan.</li>
                        <li class="mb-3">Pilih aksi (⋮) dan tekan <strong>Deduct Debit Balance</strong>.</li>
                        <li class="mb-3">Catat angka di <strong>Current Available Balance</strong>; usahakan nominal pemotongan Anda tidak melewati angka ini.</li>
                        <li class="mb-3">Masukkan nilai potongan pada field <strong>Amount</strong>.</li>
                        <li class="mb-3">Wajib isikan <strong>Description</strong>, misalnya <em>"Koreksi kelebihan transfer tanggal 11 Jan"</em>.</li>
                        <li class="mb-2">Klik <strong>Submit</strong>. Transaksi seketika tercatat di Mutation Log.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Parameter / Tipe Validasi</th>
                        <th class="p-3 border-0">Deskripsi & Aturan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Nama akun merchant tujuan untuk pemotongan.</td></tr>
                    <tr><td class="p-3 border-0"><strong>CURRENT AVAILABLE BALANCE</strong></td><td class="p-3 border-0">Menampilkan saldo terkini yang bisa dipotong guna menghindari minus tak terduga.</td></tr>
                    <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">Nominal Rupiah yang akan didebet dari Saldo merchant. Wajib berupa angka positif yang lebih besar dari nol. (Wajib)</td></tr>
                    <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">Isian wajib untuk menerangkan tujuan/alasan pemotongan saldo ini demi kepatuhan audit. (Wajib)</td></tr>
                    <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Eksekusi pemotongan. Tak bisa dibatalkan / undo.</td></tr>
                </tbody>
            </table>
        </div>

        <!-- System Notifications -->
        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
        <div class="d-flex flex-column mb-5">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-0"><strong>Saldo Terpotong:</strong> <code>Balance updated successfully.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Nominal Tidak Valid:</strong> <code>The amount must be greater than zero.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pastikan Anda memasukkan angka positif yang valid untuk nilai debit.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Failed to update balance.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Terjadi pembatasan sistem atau User MySQL tidak memiliki izin INSERT/UPDATE. Hubungi Database Administrator.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Adakah maksimal dana yang bisa dipotong?</span>
            </div>
            <p class="hc-faq-a">Tak ada batasan sistem yang kaku. Hanya saja, pastikan penarikan besar mematuhi prosedur autorisasi internal Anda agar ketika ada audit tak menjadi persoalan.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Jika terlanjur salah ketik nominal, bisa di-undo?</span>
            </div>
            <p class="hc-faq-a">Tidak bisa di-undo / delete. Gunakan modul <em>Add Credit Balance</em> untuk menambal kelebihan potongan tersebut agar transparansi mutasinya tetap terjaga.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Kenapa formulir gagal dikirim (Submit)?</span>
            </div>
            <p class="hc-faq-a">Penyebab utama adalah Anda membiarkan kolom <strong>Description</strong> kosong (ini wajib diisi) atau nominal angka mengandung karakter yang salah / bernilai negatif.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-wallet text-success"></i> 
                <span>Apa yang terjadi bila potongan membuat saldo minus?</span>
            </div>
            <p class="hc-faq-a">Sistem mungkin akan mengizinkan saldo menjadi negatif bergantung pada pengaturan inti. Jika diizinkan, saldo *Available* merchant akan turun di bawah nol, sehingga merchant tidak bisa melakukan transaksi keluar (cash-out) hingga defisit dibayarkan kembali.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-secondary"></i> 
                <span>Apakah merchant otomatis mendapat pemberitahuan email?</span>
            </div>
            <p class="hc-faq-a">Saat ini sistem tidak mengirim email peringatan secara otomatis untuk aksi potong saldo manual. Sebaiknya Anda menginformasikan kepada merchant bersangkutan (misal via WhatsApp atau Email) untuk menghindari kebingungan (dispute) di kemudian hari.</p>
        </div>

    </div>
</div>
