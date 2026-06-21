<div id="module-rbac" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Role-Based Access Control (RBAC)</strong> module is a centralized security and configuration system. It governs the structural hierarchy of the sidebar menus and defines the exact modules that each registered Role (e.g., Administrator, Member) is permitted to access.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Role Access Matrix</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">UI Component</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Role Cards</strong></td><td class="p-3 border-0">Displays all active user roles in the system. Clicking <strong>Configure</strong> opens the detailed access matrix for that specific role.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Menu Hierarchy Table</strong></td><td class="p-3 border-0">The core matrix displaying Main Menus and their nested Submenus.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Access Granted Toggle</strong></td><td class="p-3 border-0">An AJAX-powered switch to instantly grant (ON) or revoke (OFF) a role's access to a specific menu route.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Add Menu Button</strong></td><td class="p-3 border-0">Allows developers and admins to inject new routing modules into the dashboard directly from the UI without modifying the source code manually.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu (⋮)</strong></td><td class="p-3 border-0">Options to Edit (change Title, Icon, URL, Parent) or Delete a menu globally.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Menu and Access Flow</h5>
            <p class="text-muted mb-4">The RBAC system dynamically generates the sidebar for each logged-in user based on their assigned role. The process works as follows:</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">RBAC Evaluation Flowchart</h6>
                <div class="mermaid">
                flowchart TD
                    A[User Logs In] --> B{Check role_id}
                    B --> C[Query user_access_menu]
                    C --> D[Retrieve Allowed menu_ids]
                    D --> E[Join with user_menu table]
                    E --> F{Is it a Submenu?}
                    F -- Yes --> G{Has access to Parent Menu?}
                    G -- Yes --> H[Render Submenu]
                    G -- No --> I[Hide Entire Menu Branch]
                    F -- No --> J[Render Main Menu]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">User logs into the dashboard.</li>
                    <li class="mb-3">The application queries the <code>user_access_menu</code> table using the user's <code>role_id</code> to fetch a list of permitted <code>menu_id</code>s.</li>
                    <li class="mb-3">The system then joins these IDs with the <code>user_menu</code> table to retrieve the Menu Titles, Icons, and URLs.</li>
                    <li class="mb-2">The sidebar is fully re-rendered. If a user is not granted access to a specific Submenu, it is hidden. If all Submenus under a Main Menu are hidden, the Main Menu itself collapses entirely.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Critical Warning: Admin Lockout</strong>
                    <p class="mb-0 text-muted small">Never revoke the "Role Access" menu privilege for the administrative role you are currently using. If this happens, you will immediately lose access to the RBAC matrix and will be unable to re-enable it. Only a Database Administrator can fix this via direct SQL intervention.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Operating the RBAC Matrix</h5>
            <p class="text-muted mb-4">How to manage access permissions and create new navigation items.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Granting or Revoking Access</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Go to <strong>Configurations &gt; Role Access</strong> in the sidebar.</li>
                    <li class="mb-3">Identify the target role (e.g., Member) and click the <strong><i class="fas fa-cog"></i> Configure</strong> button.</li>
                    <li class="mb-3">In the matrix, locate the menu/submenu you wish to modify.</li>
                    <li class="mb-2">Click the toggle switch under the <strong>Access Granted</strong> column. Changes are applied instantly. A green success toast will appear on the bottom right.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Creating a New Submenu</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">While viewing any Role Access matrix, click the green <strong><i class="fas fa-plus"></i> Add Menu</strong> button.</li>
                    <li class="mb-3">Enter the <strong>Menu Title</strong> (e.g., "Cashout Reports").</li>
                    <li class="mb-3">From the <strong>Parent Menu</strong> dropdown, select the Main Menu where this should be nested (e.g., "Reports").</li>
                    <li class="mb-3">Enter the <strong>URL</strong> path matching your CodeIgniter controller (e.g., <code>report/cashout</code>).</li>
                    <li class="mb-3">Enter a FontAwesome 5 class for the <strong>Icon</strong> (e.g., <code>fas fa-file-invoice-dollar</code>).</li>
                    <li class="mb-2">Click <strong>Create</strong>. The new menu is now available globally and can be granted to specific roles.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Deleting a Menu Globally</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the vertical ellipsis (⋮) on the target menu.</li>
                    <li class="mb-3">Select the red <strong>Delete Menu</strong> option.</li>
                    <li class="mb-3">A confirmation pop-up will appear. Click <strong>Yes, delete it!</strong></li>
                    <li class="mb-2"><em>Note: Deleting a Main Menu will also delete all of its Submenus automatically via SQL Cascade.</em></li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rbac_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Why can't I delete a specific menu? (Error 1451)
                </a>
                <div id="faq_en_rbac_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The system protects data integrity via Foreign Keys. If you attempt to delete a menu that is still actively assigned to roles in the <code>user_access_menu</code> table, the database prevents the deletion. You must toggle access OFF for this menu across <em>all</em> roles before deleting it globally.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rbac_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: I toggled access ON, but the menu is still missing from the sidebar
                </a>
                <div id="faq_en_rbac_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Ensure that you have granted access to <strong>both</strong> the Main Menu and the specific Submenu. If a user only has access to a Submenu but their Role is denied access to its Parent Menu, the entire menu branch will remain hidden due to hierarchical logic.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rbac_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: I click a menu but it shows a "404 Page Not Found" error
                </a>
                <div id="faq_en_rbac_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This occurs if the <strong>URL</strong> defined in the Menu Configuration does not match an actual Controller and Method in the application's source code. RBAC handles UI visibility, but a developer must still create the underlying code route.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_rbac_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 4: The menu icon looks like a broken square
                </a>
                <div id="faq_en_rbac_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Ensure you are using a valid <a href="https://fontawesome.com/v5/search?m=free" target="_blank" class="text-primary">FontAwesome 5</a> class. For example, you must type the full class name like <code>fas fa-users</code> or <code>fas fa-chart-line</code> in the Icon field.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Role-Based Access Control (RBAC)</strong> adalah sistem konfigurasi dan keamanan terpusat. Sistem ini mengatur hierarki struktural menu sidebar dan menentukan modul pasti apa saja yang diizinkan untuk diakses oleh setiap Role (mis. Administrator, Member).</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Matriks Role Access</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Komponen UI</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kartu Role</strong></td><td class="p-3 border-0">Menampilkan semua peran (role) pengguna yang aktif. Klik <strong>Configure</strong> untuk membuka detail matriks akses untuk peran spesifik tersebut.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tabel Hierarki Menu</strong></td><td class="p-3 border-0">Matriks inti yang menampilkan Menu Utama dan Submenu bersarang (nested).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Toggle Access Granted</strong></td><td class="p-3 border-0">Saklar berbasis AJAX untuk seketika memberikan (ON) atau mencabut (OFF) akses sebuah role terhadap rute menu tertentu.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tombol Add Menu</strong></td><td class="p-3 border-0">Memungkinkan developer dan admin menyuntikkan modul rute baru ke dashboard langsung dari UI tanpa memodifikasi kode sumber secara manual.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Menu Aksi (⋮)</strong></td><td class="p-3 border-0">Opsi untuk mengedit (Judul, Ikon, URL, Parent) atau menghapus menu secara global.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Alur Menu dan Akses</h5>
            <p class="text-muted mb-4">Sistem RBAC membangun sidebar secara dinamis untuk setiap pengguna yang login berdasarkan perannya. Prosesnya bekerja sebagai berikut:</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Flowchart Evaluasi RBAC</h6>
                <div class="mermaid">
                flowchart TD
                    A[User Login] --> B{Cek role_id}
                    B --> C[Query user_access_menu]
                    C --> D[Ambil menu_id yang diizinkan]
                    D --> E[Join dengan tabel user_menu]
                    E --> F{Apakah ini Submenu?}
                    F -- Ya --> G{Punya akses ke Menu Induk?}
                    G -- Ya --> H[Render Submenu]
                    G -- Tidak --> I[Sembunyikan Seluruh Cabang Menu]
                    F -- Bukan --> J[Render Menu Utama]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Pengguna login ke dashboard.</li>
                    <li class="mb-3">Aplikasi melakukan query ke tabel <code>user_access_menu</code> menggunakan <code>role_id</code> milik pengguna untuk mendapatkan daftar <code>menu_id</code> yang diizinkan.</li>
                    <li class="mb-3">Sistem kemudian menggabungkan (JOIN) ID ini dengan tabel <code>user_menu</code> untuk mengambil Judul Menu, Ikon, dan URL.</li>
                    <li class="mb-2">Sidebar dirender ulang. Jika pengguna tidak diberi akses ke Submenu tertentu, menu tersebut disembunyikan. Jika semua Submenu di bawah suatu Menu Utama disembunyikan, Menu Utama tersebut ikut hilang seutuhnya.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Peringatan Kritis: Admin Terkunci (Lockout)</strong>
                    <p class="mb-0 text-muted small">Jangan pernah mencabut hak akses menu "Role Access" untuk peran administratif yang sedang Anda gunakan. Jika ini terjadi, Anda akan kehilangan akses ke matriks RBAC seketika dan tidak bisa mengaktifkannya kembali. Hanya Database Administrator yang dapat memperbaiki ini melalui intervensi SQL langsung.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengoperasikan Matriks RBAC</h5>
            <p class="text-muted mb-4">Cara mengelola izin akses dan membuat navigasi baru.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Memberi atau Mencabut Akses</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Masuk ke <strong>Configurations &gt; Role Access</strong> di sidebar.</li>
                    <li class="mb-3">Cari role target (mis. Member) lalu klik tombol <strong><i class="fas fa-cog"></i> Configure</strong>.</li>
                    <li class="mb-3">Di dalam matriks, temukan menu/submenu yang ingin dimodifikasi.</li>
                    <li class="mb-2">Klik tombol toggle (saklar) di bawah kolom <strong>Access Granted</strong>. Perubahan langsung tersimpan seketika. Notifikasi hijau akan muncul di kanan bawah.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Membuat Submenu Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Saat melihat matriks akses mana pun, klik tombol hijau <strong><i class="fas fa-plus"></i> Add Menu</strong>.</li>
                    <li class="mb-3">Masukkan <strong>Menu Title</strong> (Judul Menu, mis. "Laporan Cashout").</li>
                    <li class="mb-3">Dari dropdown <strong>Parent Menu</strong>, pilih Menu Utama tempat submenu ini akan bersarang (mis. "Reports").</li>
                    <li class="mb-3">Masukkan path <strong>URL</strong> yang cocok dengan controller CodeIgniter (mis. <code>report/cashout</code>).</li>
                    <li class="mb-3">Masukkan kelas FontAwesome 5 untuk <strong>Icon</strong> (mis. <code>fas fa-file-invoice-dollar</code>).</li>
                    <li class="mb-2">Klik <strong>Create</strong>. Menu baru sekarang tersedia secara global dan bisa diberikan izin aksesnya ke berbagai role.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Menghapus Menu Secara Global</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik ikon titik tiga vertikal (⋮) di ujung kanan baris menu yang ingin dihapus.</li>
                    <li class="mb-3">Pilih opsi merah <strong>Delete Menu</strong>.</li>
                    <li class="mb-3">Akan muncul pop-up konfirmasi. Klik <strong>Yes, delete it!</strong></li>
                    <li class="mb-2"><em>Catatan: Menghapus Menu Utama juga akan secara otomatis menghapus semua Submenu di bawahnya via SQL Cascade.</em></li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rbac_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Mengapa saya gagal menghapus menu tertentu? (Error 1451)
                </a>
                <div id="faq_id_rbac_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Sistem melindungi integritas data melalui batasan <em>Foreign Key</em>. Jika Anda mencoba menghapus menu yang saat ini masih ditugaskan kepada role aktif di dalam tabel <code>user_access_menu</code>, database akan memblokir penghapusan. Anda harus mematikan (Toggle OFF) akses menu ini dari <em>semua</em> role yang memilikinya sebelum bisa menghapusnya secara global.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rbac_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Toggle akses sudah ON, tapi menu tetap tidak muncul di sidebar
                </a>
                <div id="faq_id_rbac_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Pastikan Anda telah memberikan akses ke <strong>kedua</strong> bagian: Menu Utama dan Submenu spesifiknya. Jika pengguna hanya memiliki akses ke Submenu tetapi Role mereka dilarang melihat Menu Utama (induknya), maka seluruh cabang menu tersebut akan tetap tersembunyi secara hierarki.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rbac_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Saat menu diklik, muncul error "404 Page Not Found"
                </a>
                <div id="faq_id_rbac_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Hal ini terjadi jika <strong>URL</strong> yang dikonfigurasi pada menu tidak memiliki Controller dan Method yang sesuai di dalam kode sumber aplikasi. RBAC hanya mengatur visibilitas UI; developer tetap harus membuat rute kodenya agar halaman tersebut ada.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_rbac_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 4: Ikon menu hilang atau hanya berupa kotak kosong
                </a>
                <div id="faq_id_rbac_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Pastikan Anda menggunakan kelas <a href="https://fontawesome.com/v5/search?m=free" target="_blank" class="text-primary">FontAwesome 5</a> yang valid. Anda harus mengetikkan nama kelas penuh, seperti <code>fas fa-users</code> atau <code>fas fa-chart-line</code> pada kolom Ikon.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>