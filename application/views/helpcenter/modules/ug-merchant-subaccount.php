<div id="module-ug-merchant-subaccount" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Sub Accounts Management</strong> feature allows you to build a multi-level organizational hierarchy (up to 4 levels deep) for a merchant. It is designed to represent complex business structures like regional divisions, city branches, or individual store terminals, giving each unit its own credentials while maintaining top-down control.</p>

        <hr class="my-4">

        <!-- Core Mechanics Callout -->
        <div class="p-4 mb-5 rounded shadow-sm d-flex" style="background: rgba(13, 110, 253, 0.05); border-left: 4px solid #0d6efd;">
            <div class="mr-3 text-primary"><i class="fas fa-sitemap fa-lg"></i></div>
            <div>
                <strong class="text-primary mb-2 d-block" style="font-size: 16px;">Hierarchy Rules & Mechanics</strong>
                <ul class="text-muted small mb-0 pl-3" style="line-height: 1.6;">
                    <li class="mb-1"><strong>Multi-Level Depth:</strong> You can create sub-accounts inside sub-accounts (e.g., HQ → Region → City → Store), up to a maximum depth of Level 3 (4 levels total including the parent).</li>
                    <li class="mb-1"><strong>Dedicated Credentials:</strong> Every sub-account receives its own login credentials, unique GVConnect keys, and dedicated Static Virtual Accounts (BNI, BCA, CIMB, Permata).</li>
                    <li class="mb-1"><strong>Financial Tracking:</strong> Each sub-account tracks its own isolated mutation logs and balances, allowing for precise branch-level financial reporting.</li>
                    <li class="mb-0"><strong>Permission Inheritance:</strong> All sub-accounts operate strictly under the permission ceiling set by their ultimate parent merchant.</li>
                </ul>
            </div>
        </div>

        <!-- Section 1: Creating a Sub Account -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-user-plus text-primary mr-2"></i> 1. Registering a New Sub Account</h5>
            <p class="text-muted mb-4">Follow these steps to provision a new branch or business unit.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to the <strong>Merchant Panel → Merchant Management</strong> list.</li>
                    <li class="mb-3">Click the action menu (⋮) on the parent merchant and select <strong>Sub Accounts</strong>.</li>
                    <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> Add Sub Account</strong> button at the top right of the toolbar. <em>(Note: This button will be disabled if the maximum hierarchy depth has been reached).</em></li>
                    <li class="mb-3">Fill in the Basic Information: <strong>Sub Account Name</strong>, <strong>Email Address</strong>, and initial <strong>Status</strong>.</li>
                    <li class="mb-2">Click <strong>Save Account</strong>. The system will automatically generate the required hierarchy linkages and default roles.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Managing Sub Accounts -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-users-cog text-primary mr-2"></i> 2. Sub Account Action Menu</h5>
            <p class="text-muted mb-4">Click the three dots (⋮) on any sub-account row to access these management tools:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Action Menu Item</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-users text-success"></i> Sub Accounts</strong></td><td class="p-3 border-0">Drill down further into the hierarchy. This opens a new dashboard to manage the sub-accounts <em>belonging to this specific sub-account</em>.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-info"></i> Edit Details</strong></td><td class="p-3 border-0">Update the sub-account's profile name, email, status, or Callback Transfer configurations instantly.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-exchange-alt text-warning"></i> Mutations</strong></td><td class="p-3 border-0">Directly jump to the Mutation Logs filtered specifically for this sub-account to audit its individual financial movements.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Sub Account Name</code>, <code>Email Address</code>, and initial <code>Password</code> cannot be empty.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Email Uniqueness</strong></td><td class="p-3 border-0">Even though it is a sub-account, the <code>Email Address</code> must be completely unique across the entire system (cannot be the same as the parent or any other merchant).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Depth Limit</strong></td><td class="p-3 border-0">The system prevents creation if the hierarchy depth exceeds Level 3.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Creation:</strong> <code>Merchant successfully registered.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplicate Email (1062):</strong> <code>A merchant account with this email already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You must use a unique email for every sub-account. Try adding an alias, e.g., <code>contact+branch1@merchant.com</code>.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to create merchant accounts.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks INSERT privileges. Contact the Database Administrator.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_sa_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Why is the "Add Sub Account" button missing?
                </a>
                <div id="faq_en_sa_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The system enforces a maximum hierarchy depth of 4 levels (Parent + Level 1 + Level 2 + Level 3). If you are currently viewing a Level 3 sub-account, you cannot create any further sub-accounts beneath it, and the button will be replaced with a restriction badge.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_sa_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Do sub-accounts have their own balances?
                </a>
                <div id="faq_en_sa_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Yes. Unlike simple cashier logins, Sub Accounts are treated as distinct entities in the ledger. They receive their own funds and maintain their own balance sheets, which can be audited via the "Mutations" action.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Fitur <strong>Sub Accounts Management</strong> memungkinkan Anda membangun hierarki organisasi multi-tingkat (hingga kedalaman 4 level) untuk sebuah merchant. Fitur ini dirancang untuk mewakili struktur bisnis yang kompleks seperti divisi regional, cabang kota, atau terminal toko individual, memberikan setiap unit kredensial mereka sendiri sambil tetap mempertahankan kontrol dari pusat (induk).</p>

        <hr class="my-4">

        <!-- Core Mechanics Callout -->
        <div class="p-4 mb-5 rounded shadow-sm d-flex" style="background: rgba(13, 110, 253, 0.05); border-left: 4px solid #0d6efd;">
            <div class="mr-3 text-primary"><i class="fas fa-sitemap fa-lg"></i></div>
            <div>
                <strong class="text-primary mb-2 d-block" style="font-size: 16px;">Aturan & Mekanika Hierarki</strong>
                <ul class="text-muted small mb-0 pl-3" style="line-height: 1.6;">
                    <li class="mb-1"><strong>Kedalaman Multi-Level:</strong> Anda dapat membuat sub-akun di dalam sub-akun (misal: Pusat → Regional → Kota → Toko), hingga kedalaman maksimum Level 3 (total 4 level termasuk induk).</li>
                    <li class="mb-1"><strong>Kredensial Khusus:</strong> Setiap sub-akun menerima kredensial loginnya sendiri, kunci GVConnect yang unik, serta Virtual Account Statis (BNI, BCA, CIMB, Permata) yang terdedikasi.</li>
                    <li class="mb-1"><strong>Pelacakan Finansial:</strong> Setiap sub-akun melacak log mutasi dan saldonya sendiri secara terisolasi, memungkinkan pelaporan keuangan tingkat cabang yang presisi.</li>
                    <li class="mb-0"><strong>Pewarisan Izin:</strong> Semua sub-akun beroperasi secara ketat di bawah batas izin (permission ceiling) yang ditetapkan oleh merchant induk tertinggi mereka.</li>
                </ul>
            </div>
        </div>

        <!-- Section 1: Creating a Sub Account -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-user-plus text-primary mr-2"></i> 1. Mendaftarkan Sub Account Baru</h5>
            <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk menyediakan cabang atau unit bisnis baru.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Akses menu <strong>Merchant Panel → Merchant Management</strong>.</li>
                    <li class="mb-3">Klik tombol aksi (⋮) pada merchant induk dan pilih <strong>Sub Accounts</strong>.</li>
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> Add Sub Account</strong> di kanan atas toolbar. <em>(Catatan: Tombol ini akan dinonaktifkan jika kedalaman hierarki maksimum telah tercapai).</em></li>
                    <li class="mb-3">Isi Informasi Dasar: <strong>Nama Sub Akun</strong>, <strong>Alamat Email</strong>, dan <strong>Status</strong> awal.</li>
                    <li class="mb-2">Klik <strong>Save Account</strong>. Sistem akan secara otomatis membuat tautan hierarki dan peran default yang diperlukan.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Managing Sub Accounts -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-users-cog text-primary mr-2"></i> 2. Menu Aksi Sub Account</h5>
            <p class="text-muted mb-4">Klik ikon tiga titik (⋮) pada baris sub-akun mana pun untuk mengakses alat manajemen ini:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Item Menu Aksi</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-users text-success"></i> Sub Accounts</strong></td><td class="p-3 border-0">Menelusuri hierarki lebih dalam. Ini membuka dasbor baru untuk mengelola sub-akun <em>yang dimiliki oleh sub-akun spesifik ini</em>.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-info"></i> Edit Details</strong></td><td class="p-3 border-0">Memperbarui nama profil, email, status, atau konfigurasi Callback Transfer sub-akun secara instan.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-exchange-alt text-warning"></i> Mutations</strong></td><td class="p-3 border-0">Langsung melompat ke Log Mutasi yang difilter khusus untuk sub-akun ini guna mengaudit pergerakan finansial individualnya.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Validasi Form & Batasan (Constraints)</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                            <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>Sub Account Name</code>, <code>Email Address</code>, dan <code>Password</code> awal tidak boleh kosong.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Keunikan Email</strong></td><td class="p-3 border-0">Meskipun hanya sub-akun, <code>Email Address</code> harus benar-benar unik di seluruh sistem (tidak boleh sama dengan induk atau merchant lainnya).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Batas Kedalaman</strong></td><td class="p-3 border-0">Sistem mencegah pembuatan akun jika kedalaman hierarki telah mencapai Level 3.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Pembuatan:</strong> <code>Merchant successfully registered.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplikat Email (1062):</strong> <code>A merchant account with this email already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda wajib menggunakan email yang unik untuk setiap sub-akun. Coba tambahkan alias, misal <code>contact+cabang1@merchant.com</code>.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to create merchant accounts.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki izin INSERT. Silakan hubungi Database Administrator.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_sa_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Mengapa tombol "Add Sub Account" tidak muncul?
                </a>
                <div id="faq_id_sa_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Sistem memberlakukan kedalaman hierarki maksimum 4 level (Induk + Level 1 + Level 2 + Level 3). Jika Anda saat ini melihat sub-akun Level 3, Anda tidak dapat membuat sub-akun lagi di bawahnya, dan tombol tersebut akan diganti dengan lencana pembatasan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_sa_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Apakah sub-akun memiliki saldo mereka sendiri?
                </a>
                <div id="faq_id_sa_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ya. Berbeda dengan login kasir biasa, Sub Akun diperlakukan sebagai entitas berbeda dalam buku besar. Mereka menerima dana mereka sendiri dan memelihara lembar saldo mereka sendiri, yang dapat diaudit melalui aksi "Mutations".
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>