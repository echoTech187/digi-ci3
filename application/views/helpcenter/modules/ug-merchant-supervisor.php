<div id="module-ug-merchant-supervisor" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Merchant Supervisor</strong> module allows administrators to designate supervisor agents and assign specific merchants under their coordination. Supervisors can then monitor and assist only the merchants mapped to their profile, establishing a secure, compartmentalized account management hierarchy.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Feature</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Supervisor Dashboard</strong></td><td class="p-3 border-0">Displays a list of all registered supervisors along with their credentials and the count of merchants assigned to them.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Add Supervisor</strong></td><td class="p-3 border-0">Form to register a new supervisor (requires Full Name, Username, and Email) and immediately assign merchants to their portfolio.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Edit Supervisor</strong></td><td class="p-3 border-0">Allows modifying a supervisor's details or reassigning/removing merchants from their coordination.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Assigned Merchants View</strong></td><td class="p-3 border-0">Clicking on a supervisor reveals a dedicated list of all merchant accounts currently supervised by that agent.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Name</code>, <code>Username</code>, and <code>Email</code> must be populated when creating or updating a supervisor.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Uniqueness</strong></td><td class="p-3 border-0">The <code>Username</code> and <code>Email</code> must be globally unique. Duplicate entries will be rejected.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Merchant Assignment</strong></td><td class="p-3 border-0">A supervisor can have multiple merchants. A merchant can also be assigned to multiple supervisors if necessary.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
            <div class="d-flex flex-column mb-4">
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
        </div>

        <!-- Section 2: Assigning Merchants -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Assigning Merchants to a Supervisor</h5>
            <p class="text-muted mb-4">When creating or editing a supervisor, you can map merchant accounts to their portfolio.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click <strong>Add Supervisor</strong> or the <strong>Edit</strong> button on an existing supervisor.</li>
                    <li class="mb-3">Scroll down to the <strong>Merchant Selection</strong> area.</li>
                    <li class="mb-3">Use the search bar to find merchants by name or ID.</li>
                    <li class="mb-3">Check the boxes next to the merchants you want to assign to this supervisor.</li>
                    <li class="mb-2">Click <strong>Save</strong>. The supervisor will immediately gain visibility into these merchant accounts.</li>
                </ol>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Merchant Supervisor</strong> memungkinkan administrator untuk menunjuk agen supervisor dan menetapkan merchant tertentu di bawah koordinasinya. Supervisor kemudian dapat memantau dan membantu hanya merchant yang dipetakan ke profilnya, membentuk hierarki manajemen akun yang aman dan terkotak.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Fitur</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Dasbor Supervisor</strong></td><td class="p-3 border-0">Menampilkan daftar semua supervisor yang terdaftar beserta kredensial mereka dan jumlah merchant yang ditugaskan kepada mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Add Supervisor</strong></td><td class="p-3 border-0">Formulir untuk mendaftarkan supervisor baru (memerlukan Nama Lengkap, Username, dan Email) dan langsung menetapkan merchant ke dalam portofolio mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Edit Supervisor</strong></td><td class="p-3 border-0">Memungkinkan Anda mengubah detail supervisor atau menetapkan ulang/menghapus merchant dari koordinasi mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tampilan Merchant</strong></td><td class="p-3 border-0">Mengklik supervisor akan menampilkan daftar khusus dari semua akun merchant yang saat ini diawasi oleh agen tersebut.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Validasi Form & Batasan (Constraints)</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                            <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>Name</code>, <code>Username</code>, dan <code>Email</code> wajib diisi saat membuat atau memperbarui supervisor.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Keunikan Data</strong></td><td class="p-3 border-0"><code>Username</code> dan <code>Email</code> harus unik secara global. Entri duplikat akan ditolak oleh sistem.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Penugasan Merchant</strong></td><td class="p-3 border-0">Satu supervisor dapat memiliki banyak merchant. Satu merchant juga dapat ditugaskan ke beberapa supervisor jika diperlukan.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
            <div class="d-flex flex-column mb-4">
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
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pilih username atau alamat email lain yang belum digunakan oleh user lain di dalam sistem.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Insufficient privileges.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda tidak memiliki izin untuk mengelola supervisor. Hubungi Administrator Sistem Anda.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Assigning Merchants -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Menugaskan Merchant kepada Supervisor</h5>
            <p class="text-muted mb-4">Saat membuat atau mengedit supervisor, Anda dapat memetakan akun merchant ke dalam portofolionya.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong>Add Supervisor</strong> atau tombol <strong>Edit</strong> pada supervisor yang sudah ada.</li>
                    <li class="mb-3">Gulir ke bawah ke area <strong>Merchant Selection</strong> (Pemilihan Merchant).</li>
                    <li class="mb-3">Gunakan kotak pencarian untuk menemukan merchant berdasarkan nama atau ID.</li>
                    <li class="mb-3">Centang kotak di sebelah merchant yang ingin Anda tugaskan kepada supervisor ini.</li>
                    <li class="mb-2">Klik <strong>Save</strong>. Supervisor akan langsung mendapatkan akses visibilitas ke akun merchant tersebut.</li>
                </ol>
            </div>
        </div>

    </div>
</div>
