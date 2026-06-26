<div id="module-ug-merchant-supervisor" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Merchant Supervisor</strong> module allows administrators to designate supervisor agents and assign specific merchants under their coordination. Supervisors can then monitor and assist only the merchants mapped to their profile, establishing a secure, compartmentalized account management hierarchy.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Creating a Supervisor & Assigning Merchants</h3>
                <p class="text-muted mb-4">Follow these steps to register a new supervisor and map merchant accounts to their portfolio.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">On the Supervisor dashboard, click the <strong>Add Supervisor</strong> button.</li>
                        <li class="mb-3">Fill in the required profile fields (SPV Name, Username, and Email Address).</li>
                        <li class="mb-3">Enter a secure password in the <strong>Password</strong> and <strong>Confirm Password</strong> fields.</li>
                        <li class="mb-3">In the <strong>Assigned Merchants</strong> field, search and select one or more merchants to link to this supervisor.</li>
                        <li class="mb-3">Select the <strong>Account Status</strong> (e.g., Active, Pending, Blocked, or Freeze).</li>
                        <li class="mb-2">Click <strong>Register Supervisor</strong> to save the configuration. The supervisor will immediately gain access based on these settings.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Searching & Filtering Supervisors</h3>
                <p class="text-muted mb-4">Locate specific supervisors quickly using the built-in search and filter tools.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Use the global <strong>Search</strong> bar to find supervisors by Name, Username, or Email.</li>
                        <li class="mb-3">To apply advanced criteria, click the <strong>Filters</strong> dropdown menu.</li>
                        <li class="mb-3">Set the <strong>Registration Date</strong> range or select a specific <strong>Account Status</strong>.</li>
                        <li class="mb-2">Click <strong>Apply Filter</strong> to refresh the data table.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Viewing Assigned Merchants</h3>
                <p class="text-muted mb-4">Check the list of merchants currently mapped to a specific supervisor.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Locate the supervisor in the data table.</li>
                        <li class="mb-3">Click the Action menu (⋮) on the right side of the row.</li>
                        <li class="mb-2">Select <strong>View Merchants</strong> to be redirected to the dedicated merchant list managed by this supervisor.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
                <h3 class="hc-step-title">Editing Supervisor Profile & Assignments</h3>
                <p class="text-muted mb-4">Modify a supervisor's credentials, status, or update their merchant assignments.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Click the Action menu (⋮) on the supervisor's row and select <strong>Edit Supervisor</strong>.</li>
                        <li class="mb-3">Update their profile details, password (leave blank to keep current), or account status.</li>
                        <li class="mb-3">In the <strong>Assigned Merchants</strong> field, add new merchants or remove existing ones.</li>
                        <li class="mb-2">Click <strong>Save Changes</strong> to apply the updates immediately.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">5</div>
                <h3 class="hc-step-title">Deleting a Supervisor</h3>
                <p class="text-muted mb-4">Permanently remove a supervisor and unlink them from all merchants.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Click the Action menu (⋮) on the targeted supervisor's row.</li>
                        <li class="mb-3">Select <strong>Delete</strong> from the dropdown list.</li>
                        <li class="mb-2">Confirm the deletion prompt. The action cannot be undone, and their assigned merchants will be unlinked.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Interface Overview</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Feature</th>
                        <th class="p-3 border-0">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Supervisor Dashboard</strong></td><td class="p-3 border-0">Displays a list of all registered supervisors along with their credentials and the count of merchants assigned to them.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Edit Supervisor</strong></td><td class="p-3 border-0">Allows modifying a supervisor's details or reassigning/removing merchants from their coordination.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Assigned Merchants View</strong></td><td class="p-3 border-0">Clicking on a supervisor reveals a dedicated list of all merchant accounts currently supervised by that agent.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Live Search & Filters</strong></td><td class="p-3 border-0">Instantly search supervisors by name/email, and filter results based on Account Status (Active, Pending, Blocked, Freeze) or Registration Date.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Delete Supervisor</strong></td><td class="p-3 border-0">Permanently removes a supervisor's account and unlinks them from all previously assigned merchants.</td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Form Validations & Constraints</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                        <th class="p-3 border-0">System Enforcement Rule</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Name</code>, <code>Username</code>, <code>Email</code>, and <code>Account Status</code> must be populated. <code>Password</code> is required when creating a new supervisor, but optional during updates.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Uniqueness</strong></td><td class="p-3 border-0">The <code>Username</code> and <code>Email</code> must be globally unique. Duplicate entries will be rejected.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Merchant Assignment</strong></td><td class="p-3 border-0">A supervisor can have multiple merchants. A merchant can also be assigned to multiple supervisors if necessary.</td></tr>
                </tbody>
            </table>
        </div>

        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
        <div class="d-flex flex-column mb-5">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-0"><strong>Registration/Update:</strong> <code>Supervisor successfully saved/updated.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Duplicate Key (1062):</strong> <code>Username or Email already exists.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Choose a different username or email address for the supervisor.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Insufficient privileges.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You lack permission to manage supervisors. Contact your system administrator.</div>
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
                <span>Can a merchant be assigned to more than one supervisor?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Yes, the platform supports assigning multiple supervisors to a single merchant if your organizational structure requires overlapping coordination or backup coverage.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>What happens to a merchant's data if their assigned supervisor is deleted?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> The merchant's data, transactions, and balances remain completely unaffected. The merchant simply loses the supervisor-level mapping until they are explicitly assigned to another active agent.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Can a supervisor view merchants that aren't assigned to them?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> No. Supervisor access is strictly compartmentalized. When a supervisor logs in, they can only view, monitor, and assist the merchants that are explicitly checked in their portfolio.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>How do I remove a merchant from a supervisor's portfolio?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> Click the "Edit" button next to the relevant supervisor, scroll to the Merchant Selection area, uncheck the box next to the merchant's name you wish to remove, and click Save.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why do I get a "Duplicate Key (1062)" error when adding a supervisor?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolution:</strong> This error means the Username or Email Address you entered is already in use by another supervisor or user in the system. You must choose a globally unique identifier for both fields.</p>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Merchant Supervisor</strong> memungkinkan administrator untuk menunjuk agen supervisor dan menetapkan merchant tertentu di bawah koordinasinya. Supervisor kemudian dapat memantau dan membantu hanya merchant yang dipetakan ke profilnya, membentuk hierarki manajemen akun yang aman dan terkotak.</p>

        <hr class="my-4">

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Membuat Supervisor & Menugaskan Merchant</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk mendaftarkan supervisor baru dan memetakan akun merchant ke portofolionya.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Pada dasbor Supervisor, klik tombol <strong>Add Supervisor</strong>.</li>
                        <li class="mb-3">Isi kolom profil yang diwajibkan (SPV Name, Username, dan Email Address).</li>
                        <li class="mb-3">Masukkan kata sandi yang aman pada kolom <strong>Password</strong> dan <strong>Confirm Password</strong>.</li>
                        <li class="mb-3">Pada bagian <strong>Assigned Merchants</strong>, cari dan pilih satu atau lebih merchant yang akan ditugaskan kepada supervisor ini.</li>
                        <li class="mb-3">Pilih <strong>Account Status</strong> (misalnya Active, Pending, Blocked, atau Freeze).</li>
                        <li class="mb-2">Klik <strong>Register Supervisor</strong> untuk menyimpan konfigurasi. Supervisor akan langsung mendapatkan akses sesuai pengaturan tersebut.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Mencari & Memfilter Supervisor</h3>
                <p class="text-muted mb-4">Temukan supervisor spesifik dengan cepat menggunakan fitur pencarian dan filter bawaan.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Gunakan kolom <strong>Search</strong> global untuk mencari supervisor berdasarkan Nama, Username, atau Email.</li>
                        <li class="mb-3">Untuk menerapkan kriteria spesifik, klik menu <em>dropdown</em> <strong>Filters</strong>.</li>
                        <li class="mb-3">Atur rentang <strong>Registration Date</strong> atau pilih <strong>Account Status</strong> tertentu.</li>
                        <li class="mb-2">Klik <strong>Apply Filter</strong> untuk memperbarui data pada tabel.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Melihat Daftar Merchant yang Ditugaskan</h3>
                <p class="text-muted mb-4">Periksa daftar merchant yang saat ini dipetakan ke supervisor tertentu.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Cari supervisor pada tabel data.</li>
                        <li class="mb-3">Klik menu Aksi (⋮) di sisi kanan baris tersebut.</li>
                        <li class="mb-2">Pilih <strong>View Merchants</strong> untuk diarahkan ke daftar merchant khusus yang dikelola oleh supervisor ini.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
                <h3 class="hc-step-title">Memperbarui Profil & Penugasan Supervisor</h3>
                <p class="text-muted mb-4">Ubah kredensial supervisor, status, atau perbarui penugasan merchant mereka.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Klik menu Aksi (⋮) pada baris supervisor lalu pilih <strong>Edit Supervisor</strong>.</li>
                        <li class="mb-3">Perbarui detail profil, kata sandi (biarkan kosong untuk mempertahankan yang lama), atau status akun.</li>
                        <li class="mb-3">Pada bagian <strong>Assigned Merchants</strong>, tambahkan merchant baru atau hapus yang sudah ada.</li>
                        <li class="mb-2">Klik <strong>Save Changes</strong> untuk menerapkan perubahan secara instan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">5</div>
                <h3 class="hc-step-title">Menghapus Supervisor</h3>
                <p class="text-muted mb-4">Hapus supervisor secara permanen dan putuskan tautan mereka dari semua merchant.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Klik menu Aksi (⋮) pada baris supervisor yang dituju.</li>
                        <li class="mb-3">Pilih <strong>Delete</strong> dari daftar <em>dropdown</em>.</li>
                        <li class="mb-2">Konfirmasi penghapusan pada prompt yang muncul. Tindakan ini tidak dapat dibatalkan, dan merchant yang ditugaskan akan dilepas.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        
        <h5 class="font-weight-bold mb-3 mt-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Antarmuka</h5>
        <div class="table-responsive shadow-sm mb-5" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Fitur</th>
                        <th class="p-3 border-0">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Dasbor Supervisor</strong></td><td class="p-3 border-0">Menampilkan daftar semua supervisor yang terdaftar beserta kredensial mereka dan jumlah merchant yang ditugaskan kepada mereka.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Edit Supervisor</strong></td><td class="p-3 border-0">Memungkinkan Anda mengubah detail supervisor atau menetapkan ulang/menghapus merchant dari koordinasinya.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Tampilan Merchant</strong></td><td class="p-3 border-0">Mengklik supervisor akan menampilkan daftar khusus dari semua akun merchant yang saat ini diawasi oleh agen tersebut.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Live Search & Filter Lanjutan</strong></td><td class="p-3 border-0">Pencarian cepat supervisor berdasarkan nama/email, serta filter data berdasarkan Status Akun (Aktif, Pending, Diblokir, Dibekukan) atau Tanggal Registrasi.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Hapus Supervisor</strong></td><td class="p-3 border-0">Menghapus akun supervisor secara permanen dan memutuskan tautan mereka dari semua merchant yang sebelumnya ditugaskan.</td></tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-3 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Validasi Form & Batasan (Constraints)</h5>
        <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                        <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>Name</code>, <code>Username</code>, <code>Email</code>, dan <code>Account Status</code> wajib diisi. <code>Password</code> diwajibkan saat membuat supervisor baru, namun opsional saat melakukan pembaruan (update).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Keunikan Data</strong></td><td class="p-3 border-0"><code>Username</code> dan <code>Email</code> harus unik secara global. Entri duplikat akan ditolak oleh sistem.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Penugasan Merchant</strong></td><td class="p-3 border-0">Satu supervisor dapat memiliki banyak merchant. Satu merchant juga dapat ditugaskan ke beberapa supervisor jika diperlukan.</td></tr>
                </tbody>
            </table>
        </div>

        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
        <div class="d-flex flex-column mb-5">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-0"><strong>Registrasi/Update:</strong> <code>Supervisor successfully saved/updated.</code></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-3">
                            <strong>Duplikat Data (1062):</strong> <code>Username or Email already exists.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pilih username atau alamat email lain yang belum digunakan oleh pengguna lain di dalam sistem.</div>
                        </li>
                        <li class="mb-0">
                            <strong>Access Denied (1142):</strong> <code>Insufficient privileges.</code>
                            <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda tidak memiliki izin untuk mengelola supervisor. Hubungi Administrator Sistem Anda.</div>
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
                <span>Bisakah satu merchant ditugaskan ke lebih dari satu supervisor?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Ya, platform mendukung penugasan beberapa supervisor untuk satu merchant jika struktur organisasi Anda memerlukan koordinasi yang tumpang tindih atau cakupan pencadangan (backup).</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apa yang terjadi pada data merchant jika supervisor yang ditugaskan dihapus?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Data, transaksi, dan saldo merchant tetap tidak terpengaruh sama sekali. Merchant hanya kehilangan pemetaan tingkat supervisor tersebut hingga mereka ditugaskan secara eksplisit ke agen aktif lainnya.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bisakah supervisor melihat merchant yang tidak ditugaskan kepadanya?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Tidak. Akses supervisor sangat dikotak-kotakkan. Saat supervisor login, mereka hanya dapat melihat, memantau, dan membantu merchant yang dicentang secara eksplisit di portofolio mereka.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bagaimana cara menghapus merchant dari portofolio supervisor?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Klik tombol "Edit" di sebelah supervisor yang relevan, gulir ke area Merchant Selection (Pemilihan Merchant), hapus centang kotak di sebelah nama merchant yang ingin Anda hapus, lalu klik Save.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa saya mendapatkan error "Duplicate Key (1062)" saat menambahkan supervisor?</span>
            </div>
            <p class="hc-faq-a"><strong>Resolusi:</strong> Error ini berarti Username atau Alamat Email yang Anda masukkan sudah digunakan oleh supervisor atau pengguna lain di sistem. Anda harus memilih pengidentifikasi yang unik secara global untuk kedua kolom tersebut.</p>
        </div>

    </div>
</div>
