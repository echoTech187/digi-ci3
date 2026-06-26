<div id="module-ug-merchant-credit" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Add Credit Balance</strong> feature allows administrators to manually inject funds directly into a merchant's account. This is a privileged financial operation typically used for manual top-ups, dispute resolutions, system corrections, or promotional bonus disbursements. Every credit is permanently recorded in the mutation ledger for full auditability.</p>

        <hr class="my-4">

        <!-- Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Conceptual Architecture</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Architecture: Credit Flow</h5>
            <p class="text-muted mb-4">When a manual credit is submitted, the system processes it as an <strong>atomic double-entry ledger operation</strong>:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Admin submits the credit form with an amount and a mandatory description.</li>
                    <li class="mb-3">The merchant's <strong>Available Balance</strong> and <strong>Total Balance</strong> are instantly increased by the specified amount.</li>
                    <li class="mb-2">A new <strong>Credit</strong> mutation record is created in the merchant's Mutation Log with the exact timestamp, amount, and the description you provided. This record is immutable.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-danger shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Critical Warning: Irreversible Action</strong>
                    <p class="mb-0 text-muted small">Manual credits <strong>cannot be undone</strong>. Double-check the amount and merchant before submitting. If you input an incorrect amount, use the <em>Deduct Debit Balance</em> feature to subtract the excess, leaving a clear correction trail.</p>
                </div>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Adding a Manual Credit</h3>
                <p class="text-muted mb-4">Perform the following steps carefully to inject funds into a merchant's account.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to <strong>Merchant Setup</strong> and find the target merchant.</li>
                        <li class="mb-3">Click the action menu (⋮) and select <strong>Add Credit Balance</strong>.</li>
                        <li class="mb-3">Enter the credit <strong>Amount</strong> in Rupiah (e.g., <code>500000</code>).</li>
                        <li class="mb-3">Fill in the mandatory <strong>Description</strong> field with a clear reason (e.g., <em>"Manual top-up per client request - approved by Finance"</em>).</li>
                        <li class="mb-2">Click <strong>Submit</strong>. The balance updates instantly. Verify the result via the <em>Mutation Log</em> tab on the merchant's detail page.</li>
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
                    <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Read-only field displaying the target merchant account that will receive the credit.</td></tr>
                    <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">The Rupiah amount to inject into the merchant's Available Balance. Must be a valid positive integer greater than zero. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">A clear text explanation for this manual credit. Critical for audit compliance. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Executes the credit transaction immediately and irreversibly.</td></tr>
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
                        <li class="mb-0"><strong>Balance Updated:</strong> <code>Balance updated successfully.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Invalid Amount:</strong> <code>The amount must be greater than zero.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ensure you input a valid positive number for the credit amount.</div>
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
                <span>Is there a limit on the manual credit amount?</span>
            </div>
            <p class="hc-faq-a">There is no hard system limit. However, large manual credits should always be backed by external financial evidence (such as a bank transfer receipt) for auditing purposes.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Can I reverse or delete a manual credit?</span>
            </div>
            <p class="hc-faq-a">No, you cannot delete a credit mutation as it is permanent in the ledger. To fix a mistake, you must use the <em>Deduct Debit Balance</em> feature to subtract the incorrect amount.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>The form is rejected on submission, why?</span>
            </div>
            <p class="hc-faq-a">This usually happens if the <strong>Description</strong> field is empty, which is strictly required for audit compliance, or if the amount isn't a valid positive integer.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-wallet text-success"></i> 
                <span>Does this credit affect the master balance?</span>
            </div>
            <p class="hc-faq-a">Yes, it directly increases the merchant's <strong>Available Balance</strong>, which is part of the system's overall ledger structure. Finance teams should continuously monitor these manual adjustments.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-secondary"></i> 
                <span>Will the merchant receive a notification?</span>
            </div>
            <p class="hc-faq-a">Currently, the system does not automatically dispatch an email alert for manual ledger corrections. If required, you must notify the merchant through an external communication channel.</p>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Fitur <strong>Add Credit Balance</strong> memungkinkan administrator untuk secara manual menyuntikkan dana langsung ke akun merchant. Ini adalah operasi finansial dengan hak akses khusus untuk keperluan top-up manual, penyelesaian masalah, koreksi, atau bonus. Setiap penambahan tercatat permanen di buku mutasi demi keperluan audit.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <!-- Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Arsitektur Konseptual</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Arsitektur: Alur Kredit</h5>
            <p class="text-muted mb-4">Begitu formulir diajukan, sistem memprosesnya sebagai operasi <strong>buku besar entri ganda atomik</strong>:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Admin melakukan submit dengan nominal tertentu dan menyertakan deskripsi wajib.</li>
                    <li class="mb-3">Sistem menambahkan <strong>Available Balance</strong> dan <strong>Total Balance</strong> merchant secara instan.</li>
                    <li class="mb-2">Entri mutasi <strong>Credit</strong> yang bersifat <em>immutable</em> (tak bisa diubah) langsung tercatat di Mutation Log merchant.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-danger shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Peringatan Kritis: Tak Dapat Dibatalkan</strong>
                    <p class="mb-0 text-muted small">Transaksi manual ini <strong>tidak bisa dibatalkan / dihapus</strong>. Pastikan kembali jumlah dan nama merchant-nya. Jika terjadi salah ketik jumlah, gunakan fitur <em>Deduct Debit Balance</em> untuk menarik kelebihan dana agar jejak audit tetap rapi.</p>
                </div>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Melakukan Penambahan Saldo Manual</h3>
                <p class="text-muted mb-4">Ikuti langkah berikut dengan hati-hati untuk menyuntikkan dana ke akun merchant.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Buka <strong>Merchant Setup</strong> dan cari merchant tujuan.</li>
                        <li class="mb-3">Klik tombol aksi (⋮) lalu pilih <strong>Add Credit Balance</strong>.</li>
                        <li class="mb-3">Masukkan <strong>Amount</strong> dalam Rupiah tanpa simbol (misal <code>500000</code>).</li>
                        <li class="mb-3">Isi kotak <strong>Description</strong> dengan alasan yang sangat jelas (misal <em>"Manual top-up ACC Finance Dept"</em>).</li>
                        <li class="mb-2">Klik <strong>Submit</strong>. Saldo bertambah detik itu juga. Cek tab <em>Mutation Log</em> di profil merchant untuk memastikan statusnya.</li>
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
                    <tr><td class="p-3 border-0"><strong>MERCHANT NAME</strong></td><td class="p-3 border-0">Field hanya-baca menampilkan akun merchant tujuan penambahan dana.</td></tr>
                    <tr><td class="p-3 border-0"><strong>AMOUNT (Rp)</strong></td><td class="p-3 border-0">Nominal Rupiah yang akan disuntikkan ke saldo merchant. Wajib berupa angka positif yang lebih besar dari nol. (Wajib)</td></tr>
                    <tr><td class="p-3 border-0"><strong>DESCRIPTION / REASON</strong></td><td class="p-3 border-0">Isian wajib untuk menerangkan tujuan/alasan penambahan saldo ini demi kepatuhan audit. (Wajib)</td></tr>
                    <tr><td class="p-3 border-0"><strong>SUBMIT BUTTON</strong></td><td class="p-3 border-0">Mengeksekusi transaksi secara instan dan tidak bisa di-undo (dibatalkan).</td></tr>
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
                        <li class="mb-0"><strong>Saldo Diperbarui:</strong> <code>Balance updated successfully.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Nominal Tidak Valid:</strong> <code>The amount must be greater than zero.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pastikan Anda memasukkan angka positif yang valid untuk nilai kredit.</div>
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
                <span>Apakah ada limit nominal top-up manual?</span>
            </div>
            <p class="hc-faq-a">Secara sistem tidak ada batasan. Namun, secara prosedural, penambahan dana yang besar harus disertai bukti finansial eksternal (misal bukti transfer bank) apabila kelak diaudit.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bisakah saya menghapus (delete) mutasi ini kalau salah?</span>
            </div>
            <p class="hc-faq-a">Tidak. Menghapus rekaman transaksi melanggar integritas data. Gunakan fitur <em>Deduct Debit Balance</em> untuk memotong/mengembalikan nominal yang tidak sengaja Anda tambahkan.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Mengapa form selalu gagal disubmit?</span>
            </div>
            <p class="hc-faq-a">Kemungkinan terbesar Anda membiarkan kolom <strong>Description</strong> kosong. Isian ini sangat diwajibkan agar tim auditor tahu alasan penambahan dana tersebut. Periksa juga apakah angka yang dimasukkan sudah valid (lebih dari nol).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-wallet text-success"></i> 
                <span>Apakah ini akan mengubah Master Balance?</span>
            </div>
            <p class="hc-faq-a">Ya, tindakan ini akan langsung menambahkan <strong>Available Balance</strong> merchant, yang merupakan bagian dari buku besar (ledger) keseluruhan sistem. Tim keuangan harus memantau penyesuaian manual ini dengan seksama.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-secondary"></i> 
                <span>Apakah merchant akan mendapat notifikasi saat di-topup?</span>
            </div>
            <p class="hc-faq-a">Saat ini, sistem tidak secara otomatis mengirim email notifikasi untuk koreksi manual dari admin. Apabila dibutuhkan, Anda harus menginformasikan kepada merchant secara mandiri (misalnya melalui email eksternal/WhatsApp).</p>
        </div>

    </div>
</div>
