<div id="module-ug-merchant-debit" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Deduct Debit Balance</strong> feature is used to manually subtract funds from a merchant's available balance. This privileged operation is typically required for charging manual admin fees, processing fee-based refunds, correcting prior over-credits, or applying penalty deductions. Every debit is permanently recorded in the audit ledger.</p>

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
                        <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Read-only field confirming the target merchant account for deduction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CURRENT AVAILABLE BALANCE</strong></td><td class="p-3 border-0">Displays the balance before deduction. Prevents accidental negative balance situations.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">The Rupiah amount to deduct from the merchant's Available Balance. Must be positive.</td></tr>
                        <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">Mandatory text field explaining the reason for the deduction. Required for audit trail.</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Executes the debit immediately and irreversibly.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Debit Flow</h5>
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

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Performing a Manual Deduction</h5>
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

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mdb_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Is there a limit on the deduction amount?
                </a>
                <div id="faq_en_mdb_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> There is no hard limit, but large deductions should be backed by proper authorization. Also, deducting more than the Available Balance may lead to a negative balance.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mdb_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can I reverse or delete a manual deduction?
                </a>
                <div id="faq_en_mdb_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No, debit mutations are permanent. To fix a mistaken deduction, use the <em>Add Credit Balance</em> feature to inject the incorrectly taken funds back.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mdb_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The form is rejected on submission
                </a>
                <div id="faq_en_mdb_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The most common causes are an empty <strong>Description</strong> field (which is required) or entering a non-positive amount.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Fitur <strong>Deduct Debit Balance</strong> digunakan untuk memotong dana dari saldo merchant secara manual. Operasi ini biasanya dipakai untuk membebankan biaya admin tambahan, denda, atau mengoreksi salah transfer dana (over-credit) yang terjadi sebelumnya. Seluruh transaksi ini dicatat rapi dalam riwayat audit mutasi.</p>

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
                        <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Nama akun merchant tujuan untuk pemotongan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CURRENT AVAILABLE BALANCE</strong></td><td class="p-3 border-0">Menampilkan saldo terkini yang bisa dipotong guna menghindari minus tak terduga.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">Nominal Rupiah yang akan didebet dari Saldo merchant. Wajib angka positif.</td></tr>
                        <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">Isian keterangan yang wajib disertakan agar dapat diverifikasi oleh auditor nantinya.</td></tr>
                        <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Eksekusi pemotongan. Tak bisa dibatalkan / undo.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Alur Debit</h5>
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

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Melakukan Pemotongan Manual</h5>
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

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mdb_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Adakah maksimal dana yang bisa dipotong?
                </a>
                <div id="faq_id_mdb_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tak ada batasan sistem yang kaku. Hanya saja, pastikan penarikan besar mematuhi prosedur autorisasi internal Anda agar ketika ada audit tak menjadi persoalan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mdb_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Jika terlanjur salah ketik nominal, bisa di-undo?
                </a>
                <div id="faq_id_mdb_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak bisa di-undo / delete. Gunakan modul <em>Add Credit Balance</em> untuk menambal kelebihan potongan tersebut agar transparansi mutasinya tetap terjaga.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mdb_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kenapa formulir gagal dikirim (Submit)?
                </a>
                <div id="faq_id_mdb_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Penyebab utama adalah Anda membiarkan kolom <strong>Description</strong> kosong (ini wajib diisi) atau nominal angka mengandung karakter yang salah / bernilai negatif.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
