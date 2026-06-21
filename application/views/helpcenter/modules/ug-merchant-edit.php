<div id="module-ug-merchant-edit" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Edit Merchant configuration page allows administrators to update core profile details, reset account passwords, configure OpenAPI webhook URLs, enforce IP whitelisting, and manage system-wide access states.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Form Sections</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
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
        </div>

        <!-- Section 1: Updating Profile -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-primary mr-2"></i> 1. Updating a Merchant Profile</h5>
            <p class="text-muted mb-4">Follow these steps to safely modify merchant configurations.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to the <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-merchant'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Merchant Setup</a> list.</li>
                    <li class="mb-3">Find the target merchant, click the Action Menu (⋮) on the right, and select <strong>Edit Merchant</strong>.</li>
                    <li class="mb-3">Make the necessary modifications across the form sections. <em>Note: If you only need to update the OpenAPI Callback URL, you can ignore the rest of the form.</em></li>
                    <li class="mb-3">To restrict API access by IP, enter comma-separated IP addresses in the <strong>Whitelist IP</strong> field. Leave empty to allow any IP.</li>
                    <li class="mb-2">Click <strong>Save Changes</strong> at the bottom. The configuration updates take effect immediately for all subsequent API requests.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Account vs OpenAPI Status</strong>
                    <p class="mb-0 text-muted small"><strong>Account Status</strong> controls their ability to log into the dashboard UI. <strong>OpenAPI Access Status</strong> controls server-to-server API calls. You can block API access (suspend transactions) while leaving the Account Status active so they can still log in to view past reports or withdraw their remaining balance.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_me_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Blocked vs Frozen status
                </a>
                <div id="faq_en_me_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> You need to restrict a merchant but aren't sure which status to pick.<br><br>
                        <strong>Resolution:</strong> Both statuses lock the merchant out of the system. However, <strong>Frozen</strong> is typically used for temporary administrative suspensions (like a pending KYC document review or outstanding invoice), while <strong>Blocked</strong> indicates a permanent, unrecoverable ban due to fraud or severe Terms of Service violations.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_me_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: I want to reset their password but I can't see the new one
                </a>
                <div id="faq_en_me_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> You need to reset a merchant's password but there is no way to view it in plain text.<br><br>
                        <strong>Resolution:</strong> For strict security and compliance reasons, administrators can never see a user's raw password. If you type a new password into the form, you must manually communicate it securely to the merchant. Alternatively, tell the merchant to use the "Forgot Password" link on the login page to securely reset it themselves via email.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_me_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The Email field is disabled / greyed out
                </a>
                <div id="faq_en_me_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> A merchant requested an email change, but you cannot type in the Email field.<br><br>
                        <strong>Resolution:</strong> The email acts as the unchangeable primary key and unique identifier for the merchant identity across the database. If a merchant completely changes their business email, they must register a brand new merchant account and undergo the KYC process again.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman konfigurasi Edit Merchant memungkinkan administrator untuk memperbarui detail profil inti, mereset kata sandi akun, mengonfigurasi URL webhook OpenAPI, menerapkan whitelist IP, dan mengelola status akses sistem secara keseluruhan.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Bagian Formulir</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
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
        </div>

        <!-- Section 1: Updating Profile -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-primary mr-2"></i> 1. Memperbarui Profil Merchant</h5>
            <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk memodifikasi konfigurasi merchant dengan aman.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Alur Kerja:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke daftar <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-merchant'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Merchant Setup</a>.</li>
                    <li class="mb-3">Temukan merchant target, klik Menu Aksi (⋮) di sebelah kanan, dan pilih <strong>Edit Merchant</strong>.</li>
                    <li class="mb-3">Lakukan modifikasi yang diperlukan di seluruh bagian formulir. <em>Catatan: Jika Anda hanya perlu memperbarui URL Callback OpenAPI, Anda dapat mengabaikan sisa formulir.</em></li>
                    <li class="mb-3">Untuk membatasi akses API berdasarkan IP, masukkan alamat IP yang dipisahkan koma di kolom <strong>Whitelist IP</strong>. Biarkan kosong untuk mengizinkan semua IP.</li>
                    <li class="mb-2">Klik <strong>Save Changes</strong> di bagian bawah. Pembaruan konfigurasi langsung berlaku untuk semua permintaan API berikutnya.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Status Account vs OpenAPI</strong>
                    <p class="mb-0 text-muted small"><strong>Account Status</strong> mengontrol kemampuan mereka untuk login ke UI dasbor. <strong>OpenAPI Access Status</strong> mengontrol panggilan API antar-server. Anda dapat memblokir akses API (menangguhkan transaksi) tetapi membiarkan Status Akun tetap aktif sehingga mereka masih bisa login untuk melihat laporan lama atau menarik sisa saldo mereka.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_me_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Perbedaan status Blocked dan Frozen
                </a>
                <div id="faq_id_me_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Anda ingin membatasi merchant namun bingung memilih status.<br><br>
                        <strong>Resolusi:</strong> Keduanya mengunci merchant dari sistem. Namun, <strong>Frozen</strong> biasanya digunakan untuk penangguhan administratif sementara (seperti menunggu tinjauan dokumen KYC atau tagihan yang belum dibayar), sementara <strong>Blocked</strong> menandakan pemblokiran permanen yang tidak dapat dipulihkan akibat penipuan atau pelanggaran Syarat &amp; Ketentuan yang parah.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_me_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Saya ingin mereset kata sandi mereka tetapi tidak bisa melihat yang baru
                </a>
                <div id="faq_id_me_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Anda perlu mereset kata sandi merchant tetapi tidak ada cara untuk melihatnya dalam teks biasa.<br><br>
                        <strong>Resolusi:</strong> Demi alasan keamanan dan kepatuhan (compliance) yang ketat, administrator tidak akan pernah bisa melihat kata sandi mentah pengguna. Jika Anda mengetik kata sandi baru ke dalam formulir, Anda harus mengomunikasikannya secara manual dan aman kepada merchant. Sebagai alternatif, minta merchant menggunakan tautan "Lupa Kata Sandi" di halaman login untuk meresetnya sendiri secara aman via email.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_me_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Kolom Email dinonaktifkan / berwarna abu-abu
                </a>
                <div id="faq_id_me_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Merchant meminta perubahan email, tetapi Anda tidak dapat mengetik di kolom Email.<br><br>
                        <strong>Resolusi:</strong> Email berfungsi sebagai kunci utama yang tidak dapat diubah dan pengenal unik untuk identitas merchant di seluruh database. Jika merchant benar-benar mengubah email bisnis mereka, mereka harus mendaftar akun merchant baru dan menjalani proses KYC lagi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>