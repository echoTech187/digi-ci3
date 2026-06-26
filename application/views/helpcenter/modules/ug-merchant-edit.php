<div id="module-ug-merchant-edit" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Edit Merchant configuration page allows administrators to update core profile details, reset account passwords, configure OpenAPI webhook URLs, enforce IP whitelisting, and manage system-wide access states.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Updating a Merchant Profile</h3>
                <p class="text-muted mb-4">Follow these steps to safely modify merchant configurations.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigate to the <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-merchant'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Merchant Setup</a> list.</li>
                        <li class="mb-3">Find the target merchant, click the Action Menu (⋮) on the right, and select <strong>Edit Merchant</strong>.</li>
                        <li class="mb-3">Make the necessary modifications across the form sections. <em>Note: If you only need to update the OpenAPI Callback URL, you can ignore the rest of the form.</em></li>
                        <li class="mb-3">To restrict API access by IP, enter comma-separated IP addresses in the <strong>Whitelist IP</strong> field. Leave empty to allow any IP.</li>
                        <li class="mb-2">Click <strong>Save Changes</strong> at the bottom. The configuration updates take effect immediately for all subsequent API requests.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-error shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Account vs OpenAPI Status</strong>
                <p class="mb-0 text-muted small"><strong>Account Status</strong> controls their ability to log into the dashboard UI. <strong>OpenAPI Access Status</strong> controls server-to-server API calls. You can block API access (suspend transactions) while leaving the Account Status active so they can still log in to view past reports or withdraw their remaining balance.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-list text-primary mr-2"></i> Form Sections Overview</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Section</th>
                        <th class="p-3 border-0">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Account Information</strong></td><td class="p-3 border-0">Basic profile data including Name, Phone, and Level. The <strong>Email</strong> acts as the unique primary key and cannot be changed here.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Password Reset</strong></td><td class="p-3 border-0">Fields to force a new password for the merchant. Leave these blank to keep their current password unchanged.</td></tr>
                    <tr><td class="p-3 border-0"><strong>OpenAPI Configuration</strong></td><td class="p-3 border-0">Integration settings including specific Callback URLs for QRIS, VA, and E-Wallet events, and IP Whitelisting for API security.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Service Permissions</strong></td><td class="p-3 border-0">Toggles to grant or revoke the merchant's ability to process specific transaction types (e.g., disable QRIS for a single merchant).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Access Controls</strong></td><td class="p-3 border-0">Master switches defining the merchant's operational states. The database values are:<br>
                        <ul class="mb-0 mt-2 pl-3">
                            <li><strong>Account Status</strong>: <code>Active</code> (can log in normally), <code>Pending</code> (awaiting KYC), <code>Blocked</code> (permanent ban), or <code>Freeze</code> (temporary suspension).</li>
                            <li><strong>OpenAPI Status</strong>: <code>Active</code> (API allowed), <code>Not Active</code> (API disabled), <code>Pending</code>, <code>Blocked</code>, or <code>Freeze</code>.</li>
                        </ul>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Form Validations & Constraints</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                        <th class="p-3 border-0">System Enforcement Rule</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Merchant Name</code>, <code>Merchant Email</code>, and <code>OpenAPI Status</code> cannot be empty.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Email Format & Uniqueness</strong></td><td class="p-3 border-0">The <code>Merchant Email</code> must be a valid email string and uniquely identify the merchant across the database (1062 constraint).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Password Matching</strong></td><td class="p-3 border-0">If resetting the password, both the new password and confirmation password fields must be identical.</td></tr>
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
                        <li class="mb-0"><strong>Update:</strong> <code>Merchant successfully updated.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Duplicate Email (1062):</strong> <code>A merchant account with this email already exists.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ensure you are not accidentally duplicating an existing merchant. Use the search function to verify if the email is already registered.</div>
                        </li>
                        <li class="mb-3">
                            <strong>Password Mismatch:</strong> <code>Password not match</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Retype the password carefully in both fields.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to modify merchant accounts.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Contact the Database Administrator to grant UPDATE privileges to the database user.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>What is the difference between Blocked and Frozen status?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Both statuses lock the merchant out of the system. However, <strong>Frozen</strong> is typically used for temporary administrative suspensions (like a pending KYC document review or outstanding invoice), while <strong>Blocked</strong> indicates a permanent, unrecoverable ban due to fraud or severe Terms of Service violations.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>I want to reset their password but I can't see the new one</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> For strict security and compliance reasons, administrators can never see a user's raw password. If you type a new password into the form, you must manually communicate it securely to the merchant. Alternatively, tell the merchant to use the "Forgot Password" link on the login page to securely reset it themselves via email.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why is the Email field disabled / greyed out?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> The email acts as the unchangeable primary key and unique identifier for the merchant identity across the database. If a merchant completely changes their business email, they must register a brand new merchant account and undergo the KYC process again.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-triangle text-danger"></i> 
                <span>Can I delete a merchant entirely from this page?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> No. We do not support hard deletes for financial compliance and auditing reasons. Instead, to permanently disable an account, change both their Account Status and OpenAPI Status to <strong>Blocked</strong>.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Does changing the OpenAPI Callback URL affect past transactions?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> No. The new Callback URL will only be used for transactions initiated <em>after</em> you click Save. Past or pending transactions will not be automatically resent to the new URL.</p>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman konfigurasi Edit Merchant memungkinkan administrator untuk memperbarui detail profil inti, mereset kata sandi akun, mengonfigurasi URL webhook OpenAPI, menerapkan whitelist IP, dan mengelola status akses sistem secara keseluruhan.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Memperbarui Profil Merchant</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk memodifikasi konfigurasi merchant dengan aman.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Navigasikan ke daftar <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-merchant'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Merchant Setup</a>.</li>
                        <li class="mb-3">Temukan merchant target, klik Menu Aksi (⋮) di sebelah kanan, dan pilih <strong>Edit Merchant</strong>.</li>
                        <li class="mb-3">Lakukan modifikasi yang diperlukan di seluruh bagian formulir. <em>Catatan: Jika Anda hanya perlu memperbarui URL Callback OpenAPI, Anda dapat mengabaikan sisa formulir.</em></li>
                        <li class="mb-3">Untuk membatasi akses API berdasarkan IP, masukkan alamat IP yang dipisahkan koma di kolom <strong>Whitelist IP</strong>. Biarkan kosong untuk mengizinkan semua IP.</li>
                        <li class="mb-2">Klik <strong>Save Changes</strong> di bagian bawah. Pembaruan konfigurasi langsung berlaku untuk semua permintaan API berikutnya.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="doc-callout callout-error shadow-sm mb-5">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong class="d-block mb-1 text-body" style="font-size: 16px;">Status Account vs OpenAPI</strong>
                <p class="mb-0 text-muted small"><strong>Account Status</strong> mengontrol kemampuan mereka untuk login ke UI dasbor. <strong>OpenAPI Access Status</strong> mengontrol panggilan API antar-server. Anda dapat memblokir akses API (menangguhkan transaksi) tetapi membiarkan Status Akun tetap aktif sehingga mereka masih bisa login untuk melihat laporan lama atau menarik sisa saldo mereka.</p>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-list text-primary mr-2"></i> Ikhtisar Bagian Formulir</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Bagian</th>
                        <th class="p-3 border-0">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Account Information</strong></td><td class="p-3 border-0">Data profil dasar termasuk Nama, Telepon, dan Level. <strong>Email</strong> berfungsi sebagai kunci utama unik dan tidak dapat diubah di sini.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Password Reset</strong></td><td class="p-3 border-0">Kolom untuk memaksakan kata sandi baru untuk merchant. Kosongkan jika ingin mempertahankan kata sandi mereka saat ini.</td></tr>
                    <tr><td class="p-3 border-0"><strong>OpenAPI Configuration</strong></td><td class="p-3 border-0">Pengaturan integrasi termasuk URL Callback spesifik untuk event QRIS, VA, dan E-Wallet, serta Whitelist IP untuk keamanan API.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Service Permissions</strong></td><td class="p-3 border-0">Toggle untuk memberikan atau mencabut kemampuan merchant dalam memproses jenis transaksi tertentu (mis. menonaktifkan QRIS untuk satu merchant saja).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Access Controls</strong></td><td class="p-3 border-0">Switch master yang menentukan keadaan operasional merchant. Nilai dari database adalah:<br>
                        <ul class="mb-0 mt-2 pl-3">
                            <li><strong>Account Status</strong>: <code>Active</code> (dapat login normal), <code>Pending</code> (menunggu KYC), <code>Blocked</code> (blokir permanen), atau <code>Freeze</code> (penangguhan sementara).</li>
                            <li><strong>OpenAPI Status</strong>: <code>Active</code> (API diizinkan), <code>Not Active</code> (API dimatikan), <code>Pending</code>, <code>Blocked</code>, atau <code>Freeze</code>.</li>
                        </ul>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Validasi Form & Batasan (Constraints)</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                        <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>Merchant Name</code>, <code>Merchant Email</code>, dan <code>OpenAPI Status</code> tidak boleh kosong.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Format & Keunikan Email</strong></td><td class="p-3 border-0"><code>Merchant Email</code> harus berupa format email yang valid dan merupakan pengenal unik merchant di dalam database (Constraint 1062).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Kecocokan Kata Sandi</strong></td><td class="p-3 border-0">Jika Anda mereset kata sandi, isian pada kolom kata sandi baru dan kolom konfirmasi harus sama persis.</td></tr>
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
                        <li class="mb-0"><strong>Pembaruan:</strong> <code>Merchant successfully updated.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Duplikat Email (1062):</strong> <code>A merchant account with this email already exists.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pastikan Anda tidak menduplikasi merchant. Gunakan fitur pencarian untuk memverifikasi apakah email tersebut sudah terdaftar sebelumnya.</div>
                        </li>
                        <li class="mb-3">
                            <strong>Kata Sandi Berbeda:</strong> <code>Password not match</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Ketik ulang kata sandi dengan teliti di kedua kolom.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to modify merchant accounts.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Hubungi Database Administrator untuk memberikan izin UPDATE pada pengguna database MySQL.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apa perbedaan antara status Blocked dan Frozen?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Keduanya mengunci merchant dari sistem. Namun, <strong>Frozen</strong> biasanya digunakan untuk penangguhan administratif sementara (seperti menunggu tinjauan dokumen KYC atau tagihan yang belum dibayar), sementara <strong>Blocked</strong> menandakan pemblokiran permanen yang tidak dapat dipulihkan akibat penipuan atau pelanggaran Syarat &amp; Ketentuan yang parah.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Saya ingin mereset kata sandi mereka tetapi tidak bisa melihat yang baru</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Demi alasan keamanan dan kepatuhan (compliance) yang ketat, administrator tidak akan pernah bisa melihat kata sandi mentah pengguna. Jika Anda mengetik kata sandi baru ke dalam formulir, Anda harus mengomunikasikannya secara manual dan aman kepada merchant. Sebagai alternatif, minta merchant menggunakan tautan "Lupa Kata Sandi" di halaman login untuk meresetnya sendiri secara aman via email.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa kolom Email dinonaktifkan / berwarna abu-abu?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Email berfungsi sebagai kunci utama yang tidak dapat diubah dan pengenal unik untuk identitas merchant di seluruh database. Jika merchant benar-benar mengubah email bisnis mereka, mereka harus mendaftar akun merchant baru dan menjalani proses KYC lagi.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-triangle text-danger"></i> 
                <span>Bisakah saya menghapus merchant secara permanen dari halaman ini?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Tidak. Kami tidak mendukung penghapusan permanen (*hard delete*) karena alasan kepatuhan finansial dan audit. Sebagai gantinya, untuk menonaktifkan akun secara permanen, ubah *Account Status* dan *OpenAPI Status* mereka menjadi <strong>Blocked</strong>.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apakah mengubah URL Callback OpenAPI mempengaruhi transaksi masa lalu?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Tidak. URL Callback yang baru hanya akan digunakan untuk transaksi yang diinisiasi <em>setelah</em> Anda menyimpan perubahan. Transaksi masa lalu atau yang sedang tertunda tidak akan dikirim ulang secara otomatis ke URL baru.</p>
        </div>
    </div>
</div>