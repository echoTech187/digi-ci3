<div id="module-account" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Administrative Accounts</strong> module provides tools to manage the lifecycle of internal gateway staff. Learn how to securely create, update, monitor, and revoke dashboard access for your team members.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Account Directory</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Column / Component</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Full Name & Email</strong></td><td class="p-3 border-0">The staff member's identity. The email is their unique login identifier and cannot be duplicated across the system.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Role</strong></td><td class="p-3 border-0">The assigned RBAC role (e.g., Administrator, Member) which governs what sidebar menus they can see and access.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Status</strong></td><td class="p-3 border-0">Controls login ability. <span style="color:#16a34a;font-weight:600;">Active</span> = Login allowed. <span style="color:#dc2626;font-weight:600;">Blocked/Freeze</span> = Login denied.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Global Search & Filters</strong></td><td class="p-3 border-0">Real-time AJAX search. The system remembers your search and filter parameters in the session so they persist if you navigate away.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu (⋮)</strong></td><td class="p-3 border-0">Provides links to Edit the account or permanently Delete it.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: ERD & Authentication Flow</h5>
            <p class="text-muted mb-4">When managing administrative accounts, the gateway interacts with multiple internal security layers linked by a strict RBAC (Role-Based Access Control) architecture:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    erDiagram
                        ADMIN {
                            int id PK
                            string email
                            int role_id FK
                            string status
                        }
                        ROLES {
                            int id PK
                            string role_name
                        }
                        ROLE_ACCESS {
                            int id PK
                            int role_id FK
                            int menu_id FK
                        }
                        MENUS {
                            int id PK
                            string title
                        }
                        ADMIN }o--|| ROLES : "has"
                        ROLES ||--o{ ROLE_ACCESS : "grants"
                        ROLE_ACCESS }o--|| MENUS : "unlocks"
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>API Registration:</strong> Creating a new admin is not a simple local database insert. The dashboard securely transmits the payload via an internal API (<code>/Internal/registerAdmin</code>). If the API detects a duplicate email, it aborts the process safely.</li>
                    <li class="mb-3"><strong>Privilege Constraints:</strong> Modifying or deleting accounts relies heavily on MySQL constraints. If your configured database user lacks <code>UPDATE</code> or <code>DELETE</code> privileges, the action will fail with a Database Error.</li>
                    <li class="mb-2"><strong>Data Integrity Lock:</strong> You cannot delete an admin account that has historical transaction logs tied to it. This guarantees audit integrity.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-user-shield text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Self-Deletion Protection</strong>
                    <p class="mb-0 text-muted small">The system actively monitors your <code>Session ID</code> against the target <code>Admin ID</code>. You are structurally prevented from deleting your own account while logged in to avoid accidental permanent lockouts.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Operating the Account Directory</h5>
            <p class="text-muted mb-4">How to create and manage your administrative staff accounts.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Provisioning a New Account</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> New Admin Account</strong> button on the toolbar.</li>
                    <li class="mb-3">Fill in the official <strong>Email Address</strong> and <strong>Full Name</strong>.</li>
                    <li class="mb-3">Select the <strong>Role</strong>. This links the user directly to the RBAC matrices.</li>
                    <li class="mb-3">Select the <strong>Level</strong>. You must select either Level 1 (Primary) or Level 2 (Restricted). The system will reject anything else.</li>
                    <li class="mb-3">Input an initial strong <strong>Password</strong> and confirm it.</li>
                    <li class="mb-2">Click <strong>Save Changes</strong> to dispatch the API creation request.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Resetting Passwords</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the user and click the Action Menu (⋮), then select <strong><i class="fas fa-edit"></i> Manage Account</strong>.</li>
                    <li class="mb-3">Input the new password in both the <strong>New Password</strong> and <strong>Confirm Password</strong> fields.</li>
                    <li class="mb-3">If you only want to change their Role or Name and keep their password the same, leave the password fields completely empty.</li>
                    <li class="mb-2">Click <strong>Save Changes</strong>.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Suspending vs Deleting</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>To Suspend:</strong> Open <strong>Manage Account</strong> and change their Status to <code>Blocked</code> or <code>Freeze</code>. This denies login instantly but preserves their audit trail.</li>
                    <li class="mb-2"><strong>To Delete:</strong> Click the Action Menu (⋮) and select <strong><i class="fas fa-trash-alt"></i> Delete Account</strong>. Only use this if the admin was created by mistake and has zero activity logs.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_acc_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: "Error 1451: Cannot delete or update a parent row"
                </a>
                <div id="faq_en_acc_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The admin has already performed operational actions linked in the system (like processing manual merchant top-ups). You cannot delete them because it would orphan their audit logs. Instead, edit their account and change their Status to "Blocked".
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_acc_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: "EMAIL_HAS_TAKEN" during creation
                </a>
                <div id="faq_en_acc_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The email address is already registered in the Internal Gateway database. Ask the staff member if they already have an account, or use the global search to find their existing profile and update it.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_acc_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: I cannot find the Delete button for my own account
                </a>
                <div id="faq_en_acc_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This is a hardcoded safety feature. The system hides the delete trigger for the currently active session. If your account truly needs to be deleted, another authorized Administrator must log in and execute the deletion.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Administrative Accounts</strong> menyediakan alat untuk mengelola siklus hidup staf internal gateway. Pelajari cara membuat, memperbarui, memantau, dan mencabut akses dasbor bagi anggota tim Anda secara aman.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Direktori Akun</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Kolom / Komponen</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Nama Lengkap & Email</strong></td><td class="p-3 border-0">Identitas staf. Email merupakan ID login yang unik dan tidak dapat diduplikasi di dalam sistem.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Role</strong></td><td class="p-3 border-0">Peran RBAC (mis. Administrator, Member) yang mengatur menu sidebar apa saja yang bisa mereka akses.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Status</strong></td><td class="p-3 border-0">Mengatur kemampuan login. <span style="color:#16a34a;font-weight:600;">Active</span> = Bisa login. <span style="color:#dc2626;font-weight:600;">Blocked/Freeze</span> = Login ditolak.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Pencarian & Filter</strong></td><td class="p-3 border-0">Pencarian AJAX real-time. Sistem mengingat kata kunci dan filter Anda dalam sesi (session), sehingga tidak hilang saat Anda berpindah halaman.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Menu Aksi (⋮)</strong></td><td class="p-3 border-0">Menyediakan tautan untuk Mengedit akun atau Menghapusnya secara permanen.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: ERD & Alur Autentikasi Internal</h5>
            <p class="text-muted mb-4">Saat mengelola akun administratif, gateway berinteraksi dengan beberapa lapisan keamanan internal yang terikat oleh arsitektur RBAC (Role-Based Access Control) ketat:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    erDiagram
                        ADMIN {
                            int id PK
                            string email
                            int role_id FK
                            string status
                        }
                        ROLES {
                            int id PK
                            string role_name
                        }
                        ROLE_ACCESS {
                            int id PK
                            int role_id FK
                            int menu_id FK
                        }
                        MENUS {
                            int id PK
                            string title
                        }
                        ADMIN }o--|| ROLES : "memiliki"
                        ROLES ||--o{ ROLE_ACCESS : "memberi"
                        ROLE_ACCESS }o--|| MENUS : "membuka"
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Registrasi API:</strong> Membuat admin baru bukanlah proses insert database lokal biasa. Dasbor mentransmisikan data payload secara aman melalui API internal (<code>/Internal/registerAdmin</code>). Jika API mendeteksi email duplikat, proses dibatalkan dengan aman.</li>
                    <li class="mb-3"><strong>Batasan Hak Akses:</strong> Memodifikasi atau menghapus akun sangat bergantung pada batasan MySQL. Jika user database yang dikonfigurasi tidak memiliki hak <code>UPDATE</code> atau <code>DELETE</code>, tindakan akan gagal dan memunculkan Database Error.</li>
                    <li class="mb-2"><strong>Kunci Integritas Data:</strong> Anda tidak bisa menghapus akun admin yang sudah memiliki riwayat log transaksi historis. Ini menjamin integritas jejak audit sistem.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-user-shield text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Proteksi Penghapusan Akun Sendiri</strong>
                    <p class="mb-0 text-muted small">Sistem terus memantau <code>Session ID</code> Anda dan mencocokkannya dengan <code>Admin ID</code> target. Anda secara struktural dicegah dari aksi menghapus akun Anda sendiri saat sedang login untuk menghindari insiden salah klik yang membuat Anda terkunci secara permanen.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengoperasikan Direktori Akun</h5>
            <p class="text-muted mb-4">Cara membuat dan mengelola akun staf administratif Anda.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Mempersiapkan Akun Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> New Admin Account</strong> di bilah alat.</li>
                    <li class="mb-3">Isi <strong>Alamat Email</strong> resmi dan <strong>Nama Lengkap</strong> staf.</li>
                    <li class="mb-3">Pilih <strong>Role</strong> (Peran). Ini menghubungkan pengguna langsung dengan matriks menu RBAC.</li>
                    <li class="mb-3">Pilih <strong>Level</strong>. Anda wajib memilih Level 1 (Primary) atau Level 2 (Restricted). Sistem akan menolak opsi selain itu.</li>
                    <li class="mb-3">Masukkan <strong>Password</strong> awal yang kuat lalu konfirmasikan ulang.</li>
                    <li class="mb-2">Klik <strong>Save Changes</strong> untuk mengirim permintaan pembuatan ke API.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Mereset Password</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari pengguna lalu klik Menu Aksi (⋮), lalu pilih <strong><i class="fas fa-edit"></i> Manage Account</strong>.</li>
                    <li class="mb-3">Ketikkan password baru di kolom <strong>New Password</strong> dan <strong>Confirm Password</strong>.</li>
                    <li class="mb-3">Jika Anda hanya ingin mengubah Role atau Nama dan tetap mempertahankan password lama, biarkan kedua kolom password kosong melompong.</li>
                    <li class="mb-2">Klik <strong>Save Changes</strong>.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Menangguhkan vs Menghapus</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Untuk Menangguhkan:</strong> Buka <strong>Manage Account</strong> dan ubah Statusnya menjadi <code>Blocked</code> atau <code>Freeze</code>. Ini langsung menolak akses login mereka tetapi mempertahankan riwayat audit mereka.</li>
                    <li class="mb-2"><strong>Untuk Menghapus:</strong> Klik Menu Aksi (⋮) dan pilih <strong><i class="fas fa-trash-alt"></i> Delete Account</strong>. Gunakan opsi ini HANYA jika admin tersebut dibuat karena kesalahan ketik dan belum memiliki log aktivitas apa pun.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_acc_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: "Error 1451: Cannot delete or update a parent row"
                </a>
                <div id="faq_id_acc_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Admin tersebut sudah pernah melakukan aktivitas operasional yang tercatat di sistem (misal: menyetujui penambahan saldo merchant manual). Anda tidak bisa menghapusnya karena akan membuat log audit sistem menjadi tanpa induk (orphan). Sebagai solusinya, edit akun mereka dan ubah Status menjadi "Blocked".
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_acc_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Muncul error "EMAIL_HAS_TAKEN" saat pembuatan
                </a>
                <div id="faq_id_acc_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Alamat email tersebut sudah terdaftar di database Internal Gateway. Tanyakan kepada anggota staf apakah mereka sudah punya akun lama, atau gunakan pencarian global untuk menemukan profil lama mereka lalu perbarui datanya.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_acc_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Saya tidak menemukan tombol Delete untuk akun saya sendiri
                </a>
                <div id="faq_id_acc_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ini adalah fitur pengaman *hardcoded*. Sistem menyembunyikan tombol penghapusan untuk sesi yang sedang aktif. Jika akun Anda benar-benar harus dihapus, Administrator lain yang berwenang harus login terlebih dahulu lalu mengeksekusi penghapusan tersebut.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
