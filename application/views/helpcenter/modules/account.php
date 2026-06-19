<div id="module-account" class="hc-doc-section">
                    <!-- EN CONTENT -->
                    <div class="lang-content lang-en" style="display:block;">
                        <div class="text-center mb-5 mt-3">
                            <div class="mb-3">
                                <i class="fas fa-users-cog text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h2 class="font-weight-bold">Account Administrative</h2>
                            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">A comprehensive, step-by-step guide to managing the lifecycle of administrator accounts. Learn how to securely create, update, monitor, and revoke staff access to the payment gateway.</p>
                        </div>
                        
                        <hr class="doc-divider">

                        <!-- FEATURE 1: VIEWING & SEARCHING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-search text-primary mr-2"></i> 1. Viewing & Filtering Administrators</h3>
                            <p class="text-muted mb-4">The main dashboard of the Account Administrative module provides a powerful, real-time data table to monitor all registered staff members.</p>
                            
                            <div class="pl-4 border-left border-primary ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Step-by-Step Workflow:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-2">Navigate to the <strong>Manage Account</strong> section in the sidebar and click on <strong>Administrative Accounts</strong>.</li>
                                    <li class="mb-2">You will see a list of all admins. Use the search bar labeled <strong>"Search by email, name, or role..."</strong> at the top left of the table to instantly find an admin.</li>
                                    <li class="mb-2">Click the <strong><i class="fas fa-sliders-h"></i> Filters</strong> button to open the advanced filters dropdown.</li>
                                    <li class="mb-2">Use the <strong>Role</strong> filter to view only users belonging to a specific department (e.g., Finance, Support), or use the <strong>Status</strong> filter to isolate Active, Pending, Blocked, or Freeze accounts, then click <strong>APPLY FILTER</strong>.</li>
                                </ol>
                            </div>
                            <div class="doc-callout callout-note">
                                <div class="callout-icon"><i class="fas fa-microchip"></i></div>
                                <div class="callout-content">
                                    <strong>Technical Insight: AJAX DataTables & Session Memory</strong>
                                    <p style="margin:0;" class="small text-muted">The list is populated using server-side processing via AJAX (<code>get_datatables_handler</code>). The system intelligently remembers your search queries and filters in the session (<code>search_admin</code>), so if you navigate away and come back, your exact search results are preserved.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 2: CREATION -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Creating a New Admin</h3>
                            <p class="text-muted mb-4">Registering a new staff member requires strict security parameters to ensure they are assigned the correct hierarchical and access levels.</p>
                            
                            <div class="pl-4 border-left border-success ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Step-by-Step Workflow:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3">Click the <button class="btn btn-sm btn-primary px-3" style="pointer-events:none;"><i class="fas fa-plus"></i> New Admin Account</button> button located at the top right of the toolbar. This will open the <strong>ADD NEW ADMIN ACCOUNT</strong> modal.</li>
                                    <li class="mb-3">
                                        <strong>Fill in Identity Details:</strong> Enter the official <code>Email Address</code> and <code>Full Name</code> of the staff member.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Assign RBAC Role:</strong> Select the exact <code>Role</code> for the user. This determines which menus they can see and access, linked directly to the Role-Based Access Control module.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Assign Hierarchical Level:</strong> Select the <code>Level</code>. 
                                        <br><span class="badge badge-info mt-1">System Rule:</span> You must strictly select either <strong>Level 1 (Primary / Full Access)</strong> or <strong>Level 2 (Secondary / Restricted)</strong>. The system will reject any other values.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Set Status:</strong> Ensure the account is set to <strong>Active</strong> so they can log in immediately.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Set Security Credentials:</strong> Enter a strong initial <code>Password</code> and confirm it in the <code>Confirm Password</code> field. 
                                        <em>(Note: The system will reject the form if the passwords do not match).</em>
                                    </li>
                                    <li class="mb-2">Click <strong>SAVE CHANGES</strong> to provision the account.</li>
                                </ol>
                            </div>
                            
                            <div class="doc-callout callout-warning">
                                <div class="callout-icon"><i class="fas fa-network-wired"></i></div>
                                <div class="callout-content">
                                    <strong>Technical Workflow: Internal API Integration</strong>
                                    <p style="margin:0;" class="small text-muted">Account creation is <strong>not</strong> a simple local database insertion. When you click Save, the controller first verifies the email is not taken locally. If clear, it packages the payload and transmits it via cURL (POST) to the <code>/Internal/registerAdmin</code> endpoint on the Internal Gateway API. If the API returns <code>EMAIL_HAS_TAKEN</code> or <code>INVALID_PARAMETER</code>, the system securely aborts the process and displays the exact validation error.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 3: UPDATING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-edit text-primary mr-2"></i> 3. Updating Profiles & Resetting Passwords</h3>
                            <p class="text-muted mb-4">You can modify an existing admin's details, change their access level, or reset their password if they lose access to their account.</p>
                            
                            <div class="pl-4 border-left border-info ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Step-by-Step Workflow:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3">In the Admin list, find the target user and click the <strong>three vertical dots (<i class="fas fa-ellipsis-v"></i>)</strong> in the ACTION column to open the dropdown menu.</li>
                                    <li class="mb-3">Click on <strong><i class="fas fa-edit text-primary"></i> Manage Account</strong>. This will open the <strong>UPDATE ADMIN ACCOUNT</strong> modal.</li>
                                    <li class="mb-3"><strong>To Change Details:</strong> You can safely alter their Full Name, Level, Role, or Status.</li>
                                    <li class="mb-3"><strong>To Change Email:</strong> If you change the email address, the system will instantly verify that the new email does not belong to another existing user in the database.</li>
                                    <li class="mb-3"><strong>To Reset Password:</strong> Type a new password in the <code>New Password (Optional)</code> and <code>Confirm New Password (Optional)</code> fields. <br><em>Note: If you do not want to change the password, simply leave the password fields completely blank. The system will preserve the current password.</em></li>
                                    <li class="mb-2">Click <strong>SAVE CHANGES</strong>.</li>
                                </ol>
                            </div>

                            <div class="doc-callout callout-error">
                                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                                <div class="callout-content">
                                    <strong>Technical Constraint: Database Privileges</strong>
                                    <p style="margin:0;" class="small text-muted">Unlike creation, updates happen directly via the local <code>AdminModel</code>. The controller intercepts specific MySQL errors to protect system integrity. For example, if you see a <code>1142 Error</code>, the system is informing you that your database connection user lacks sufficient <code>UPDATE</code> privileges.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 4: DELETING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-times text-primary mr-2"></i> 4. Deleting or Suspending an Admin</h3>
                            <p class="text-muted mb-4">Removing access is a critical security action. The system provides secure protocols for both permanent deletion and temporary suspension.</p>
                            
                            <div class="pl-4 border-left border-danger ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Step-by-Step Workflow:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3"><strong>Temporary Suspension (Recommended):</strong> Instead of deleting, use <strong>Manage Account</strong> and change their Status to <strong>Blocked</strong> or <strong>Freeze</strong>. This instantly blocks them from logging in while preserving their historical activity logs.</li>
                                    <li class="mb-3"><strong>Permanent Deletion:</strong> Click the <strong>three vertical dots (<i class="fas fa-ellipsis-v"></i>)</strong> next to the user and select <strong><i class="fas fa-trash-alt text-danger"></i> Delete Account</strong>. Click "Yes, delete it!" in the warning popup to confirm.</li>
                                </ol>
                            </div>

                            <div class="doc-callout callout-note mb-3">
                                <div class="callout-icon"><i class="fas fa-user-shield"></i></div>
                                <div class="callout-content">
                                    <strong>Protection: Self-Deletion Block</strong>
                                    <p style="margin:0;" class="small text-muted">The system constantly monitors your active session. It strictly compares your <code>Session ID</code> with the target <code>Admin ID</code>. You are physically prevented from deleting your own account while currently logged in to prevent accidental lockouts.</p>
                                </div>
                            </div>

                            <div class="doc-callout callout-warning">
                                <div class="callout-icon"><i class="fas fa-link"></i></div>
                                <div class="callout-content">
                                    <strong>Protection: Foreign Key Constraints (Error 1451)</strong>
                                    <p style="margin:0;" class="small text-muted">If an admin has already been active (e.g., they have processed transactions or are linked to system audit logs), the database enforces a strict Foreign Key Constraint (MySQL Error 1451). The system gracefully catches this error and blocks the deletion to prevent database corruption. In this case, you <strong>must</strong> use the Suspension method instead.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- ID CONTENT -->
                    <div class="lang-content lang-id" style="display:none;">
                        <div class="text-center mb-5 mt-3">
                            <div class="mb-3">
                                <i class="fas fa-users-cog text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h2 class="font-weight-bold">Manajemen Akun Administratif</h2>
                            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Panduan lengkap dan terstruktur (step-by-step) untuk mengelola siklus hidup akun administrator. Pelajari cara membuat, memperbarui, memantau, dan mencabut akses staf secara aman di sistem gateway.</p>
                        </div>
                        
                        <hr class="doc-divider">

                        <!-- FEATURE 1: VIEWING & SEARCHING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-search text-primary mr-2"></i> 1. Melihat & Memfilter Administrator</h3>
                            <p class="text-muted mb-4">Dasbor utama modul ini menyediakan tabel data real-time yang sangat kuat untuk memantau semua anggota staf yang terdaftar.</p>
                            
                            <div class="pl-4 border-left border-primary ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Panduan Langkah-demi-Langkah:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-2">Arahkan kursor ke bagian <strong>Manage Account</strong> di sidebar, lalu klik <strong>Administrative Accounts</strong>.</li>
                                    <li class="mb-2">Anda akan melihat daftar semua admin. Gunakan bilah pencarian berlabel <strong>"Search by email, name, or role..."</strong> di kiri atas tabel untuk menemukan admin secara instan.</li>
                                    <li class="mb-2">Klik tombol <strong><i class="fas fa-sliders-h"></i> Filters</strong> untuk membuka menu *dropdown* filter lanjutan.</li>
                                    <li class="mb-2">Gunakan filter <strong>Role</strong> untuk hanya menampilkan departemen tertentu, atau gunakan filter <strong>Status</strong> untuk memisahkan akun Active, Pending, Blocked, atau Freeze, lalu klik <strong>APPLY FILTER</strong>.</li>
                                </ol>
                            </div>
                            <div class="doc-callout callout-note">
                                <div class="callout-icon"><i class="fas fa-microchip"></i></div>
                                <div class="callout-content">
                                    <strong>Wawasan Teknis: AJAX DataTables & Memori Sesi</strong>
                                    <p style="margin:0;" class="small text-muted">Daftar ini dimuat menggunakan pemrosesan sisi server via AJAX (<code>get_datatables_handler</code>). Sistem dengan cerdas mengingat pencarian dan filter Anda di dalam sesi (<code>search_admin</code>). Jadi, jika Anda berpindah halaman dan kembali lagi, hasil pencarian spesifik Anda akan tetap dipertahankan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 2: CREATION -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Membuat Akun Admin Baru</h3>
                            <p class="text-muted mb-4">Mendaftarkan anggota staf baru memerlukan pengaturan keamanan yang ketat untuk memastikan mereka mendapatkan hierarki dan tingkat akses yang tepat.</p>
                            
                            <div class="pl-4 border-left border-success ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Panduan Langkah-demi-Langkah:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3">Klik tombol <button class="btn btn-sm btn-primary px-3" style="pointer-events:none;"><i class="fas fa-plus"></i> New Admin Account</button> yang terletak di kanan atas bilah alat (*toolbar*). Ini akan membuka modal <strong>ADD NEW ADMIN ACCOUNT</strong>.</li>
                                    <li class="mb-3">
                                        <strong>Isi Identitas:</strong> Masukkan <code>Email Address</code> resmi dan <code>Full Name</code> (Nama Lengkap) dari anggota staf.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Tautkan Peran RBAC:</strong> Pilih <code>Role</code> (Peran) spesifik untuk pengguna tersebut. Ini akan menentukan menu mana saja yang boleh mereka akses, karena terhubung langsung dengan sistem RBAC.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Tetapkan Tingkat Hierarki:</strong> Pilih <code>Level</code>. 
                                        <br><span class="badge badge-info mt-1">Aturan Sistem:</span> Anda wajib dan hanya boleh memilih <strong>Level 1 (Primary / Full Access)</strong> atau <strong>Level 2 (Secondary / Restricted)</strong>. Nilai selain itu akan ditolak.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Atur Status:</strong> Pastikan status diatur ke <strong>Active</strong> agar mereka bisa langsung login.
                                    </li>
                                    <li class="mb-3">
                                        <strong>Atur Kredensial Keamanan:</strong> Masukkan <code>Password</code> awal yang kuat dan konfirmasi ulang pada kolom <code>Confirm Password</code>. 
                                        <em>(Catatan: Sistem akan otomatis menolak formulir jika kedua password tidak persis sama).</em>
                                    </li>
                                    <li class="mb-2">Klik <strong>SAVE CHANGES</strong> untuk mencetak akun tersebut.</li>
                                </ol>
                            </div>
                            
                            <div class="doc-callout callout-warning">
                                <div class="callout-icon"><i class="fas fa-network-wired"></i></div>
                                <div class="callout-content">
                                    <strong>Alur Teknis (Workflow): Integrasi API Internal</strong>
                                    <p style="margin:0;" class="small text-muted">Pembuatan akun <strong>bukanlah</strong> insert database lokal biasa. Saat Anda menekan Save, kontroler mengecek database lokal terlebih dahulu untuk memastikan email belum dipakai. Jika aman, kontroler membungkus data (payload) dan mentransmisikannya via cURL (POST) ke *endpoint* <code>/Internal/registerAdmin</code> di Internal Gateway API. Jika API mendeteksi masalah (merespons <code>EMAIL_HAS_TAKEN</code> atau <code>INVALID_PARAMETER</code>), proses akan dibatalkan secara aman dan menampilkan detail error tersebut di layar Anda.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 3: UPDATING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-edit text-primary mr-2"></i> 3. Memperbarui Profil & Reset Kata Sandi</h3>
                            <p class="text-muted mb-4">Anda dapat mengubah detail admin yang sudah ada, menaikkan/menurunkan akses levelnya, atau me-reset password mereka jika mereka terkunci dari akunnya.</p>
                            
                            <div class="pl-4 border-left border-info ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Panduan Langkah-demi-Langkah:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3">Pada daftar Admin, cari pengguna target dan klik <strong>ikon tiga titik vertikal (<i class="fas fa-ellipsis-v"></i>)</strong> pada kolom ACTION untuk membuka menu *dropdown*.</li>
                                    <li class="mb-3">Klik tombol <strong><i class="fas fa-edit text-primary"></i> Manage Account</strong>. Ini akan membuka modal <strong>UPDATE ADMIN ACCOUNT</strong>.</li>
                                    <li class="mb-3"><strong>Mengubah Detail:</strong> Anda bisa mengubah Full Name, Level, Role, atau Status secara bebas dan aman.</li>
                                    <li class="mb-3"><strong>Mengubah Email:</strong> Jika Anda mengubah alamat email, sistem akan langsung memverifikasi ke seluruh database untuk memastikan email baru tersebut tidak digunakan oleh akun admin lainnya.</li>
                                    <li class="mb-3"><strong>Me-Reset Password:</strong> Ketik password baru di kolom <code>New Password (Optional)</code> dan <code>Confirm New Password (Optional)</code>. <br><em>Catatan Penting: Jika Anda TIDAK ingin mengubah password, biarkan saja kedua kolom password kosong melompong. Sistem cukup pintar untuk tetap mempertahankan password yang lama.</em></li>
                                    <li class="mb-2">Klik <strong>SAVE CHANGES</strong> (Simpan Perubahan).</li>
                                </ol>
                            </div>

                            <div class="doc-callout callout-error">
                                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                                <div class="callout-content">
                                    <strong>Kendala Teknis: Hak Akses Database (Privileges)</strong>
                                    <p style="margin:0;" class="small text-muted">Berbeda dengan pembuatan, proses *update* dieksekusi langsung ke <code>AdminModel</code> lokal. Kontroler menangkap pesan error MySQL tertentu demi integritas. Contoh, jika Anda melihat <code>Error 1142</code>, sistem sedang memberitahu Anda bahwa user koneksi database saat ini tidak memiliki hak `UPDATE` yang cukup.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURE 4: DELETING -->
                        <div class="mb-5">
                            <h3 class="mb-4"><i class="fas fa-user-times text-primary mr-2"></i> 4. Menghapus atau Menangguhkan Admin</h3>
                            <p class="text-muted mb-4">Mencabut hak akses adalah tindakan keamanan kritis. Sistem menyediakan protokol yang aman, baik untuk penghapusan permanen maupun penangguhan sementara.</p>
                            
                            <div class="pl-4 border-left border-danger ml-2 mb-4">
                                <h5 class="font-weight-bold text-dark mb-2">Panduan Langkah-demi-Langkah:</h5>
                                <ol class="text-muted mb-0">
                                    <li class="mb-3"><strong>Penangguhan Sementara (Sangat Direkomendasikan):</strong> Daripada menghapus, gunakan fitur <strong>Manage Account</strong> lalu ubah statusnya menjadi <strong>Blocked</strong> atau <strong>Freeze</strong>. Ini secara instan memblokir mereka dari login ulang, namun riwayat aktivitas masa lalu mereka tetap aman di sistem.</li>
                                    <li class="mb-3"><strong>Penghapusan Permanen:</strong> Klik <strong>ikon tiga titik vertikal (<i class="fas fa-ellipsis-v"></i>)</strong> di sebelah pengguna dan pilih <strong><i class="fas fa-trash-alt text-danger"></i> Delete Account</strong>. Konfirmasi peringatan pada *popup* yang muncul dengan menekan "Yes, delete it!".</li>
                                </ol>
                            </div>

                            <div class="doc-callout callout-note mb-3">
                                <div class="callout-icon"><i class="fas fa-user-shield"></i></div>
                                <div class="callout-content">
                                    <strong>Proteksi: Blokir Hapus Diri Sendiri (Self-Deletion Block)</strong>
                                    <p style="margin:0;" class="small text-muted">Sistem secara konstan memantau sesi aktif Anda. Ia membandingkan <code>Session ID</code> Anda saat ini dengan <code>Admin ID</code> target. Anda secara fisik diblokir dari tindakan menghapus akun Anda sendiri untuk menghindari insiden salah klik yang membuat Anda terkunci dari sistem (*accidental lockout*).</p>
                                </div>
                            </div>

                            <div class="doc-callout callout-warning">
                                <div class="callout-icon"><i class="fas fa-link"></i></div>
                                <div class="callout-content">
                                    <strong>Proteksi: Foreign Key Constraints (MySQL Error 1451)</strong>
                                    <p style="margin:0;" class="small text-muted">Jika admin tersebut telah aktif bekerja (misalnya pernah memproses transaksi atau tercatat dalam log audit sistem), database akan memberlakukan *Foreign Key Constraint* secara ketat. Sistem dengan mulus menangkap error ini (Error 1451) dan menggagalkan penghapusan demi menghindari korupsi database. Dalam kasus ini, Anda <strong>wajib</strong> menggunakan metode Penangguhan (Ubah status ke Inactive/Blocked).</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                