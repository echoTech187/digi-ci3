<div id="module-rbac" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-users-cog text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Role-Based Access Control (RBAC)</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">A comprehensive, enterprise-grade guide to managing the Role-Based Access Control system. Learn how to securely create menus, map privileges via live synchronization, and enforce absolute protection at both the UI and Backend routing levels.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sitemap text-primary mr-2"></i> 1. System Architecture &amp; Hierarchy</h3>
            <p class="text-muted mb-4">The RBAC system restricts administrative access based on modular privilege grants. It consists of three tightly integrated layers:</p>
            <ul class="pl-4 text-muted">
                <li class="mb-2"><strong>Roles:</strong> The security groups defined in the system (e.g., Administrator, Member). Roles dictate the maximum boundary of access a user possesses.</li>
                <li class="mb-2"><strong>Menus &amp; Submenus:</strong> The physical routing paths of the application, categorized under `Group Modules` to form the dynamic sidebar navigation.</li>
                <li class="mb-2"><strong>Access Mapping:</strong> The bridge table (`user_access_menu`) connecting a Role ID to a Menu ID. This mapping is evaluated in real-time during session authentication.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-bars text-primary mr-2"></i> 2. Managing Menus and Navigation Structures</h3>
            <p class="text-muted mb-4">Before granting access, the application route must be officially registered within the Menu Management system. This ensures the backend controllers can recognize and protect the route.</p>

            <div class="pl-3">
                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Step 1: Accessing Menu Configurations</h5>
                    <p class="text-muted mb-0 ml-4">Navigate to the <strong>Manage Account</strong> section on the sidebar and select the <strong>Menu Management</strong> or <strong>Submenu Management</strong> items. This page displays the master list of all registered routing paths.</p>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Step 2: Registering a New Menu</h5>
                    <div class="text-muted mb-0 ml-4">
                        Click the <strong>+ Add Menu</strong> button. This opens the `New Menu` modal window where you must define the physical properties of the route:
                        <ul class="mt-2">
                            <li><strong>Menu Title:</strong> The exact human-readable label that will appear on the sidebar (e.g., <em>Merchant Management</em>).</li>
                            <li><strong>Group Module:</strong> Select an existing grouping header (e.g., <em>Transaction</em>) under which this menu falls. To create a new group header, click the <strong>+ Add</strong> button next to the dropdown and define its name.</li>
                            <li><strong>URL:</strong> The relative controller routing path (e.g., <code>merchant/manage</code>). <strong class="text-danger">Do not prefix with a slash</strong> and do not include the base domain.</li>
                            <li><strong>Icon:</strong> Provide the CSS class for the FontAwesome icon (e.g., <code>fas fa-users</code>) to visually represent the item.</li>
                            <li><strong>Parent Menu:</strong> If this is a primary menu, select <strong>None (Main Menu)</strong>. If this menu acts as a sub-navigation tier, select its Parent Menu from the list.</li>
                            <li><strong>Order:</strong> A numeric value (e.g., <code>0</code>, <code>1</code>, <code>2</code>) that dictates the vertical display sequence of the menu relative to its siblings.</li>
                        </ul>
                        Submit the form via the <strong>Create</strong> button. The menu is instantly saved via AJAX and officially registered in the database.
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Step 3: Editing and Cascading Updates</h5>
                    <p class="text-muted mb-0 ml-4">Modifying a menu via the <strong>Edit Menu</strong> action will globally cascade the changes. For instance, updating a URL will immediately update the routing access for all roles attached to that menu. This guarantees consistency across the entire user base without needing to modify individual role configurations.</p>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Step 4: Safe Deletion &amp; Database Constraint Protection</h5>
                    <div class="text-muted mb-0 ml-4">
                        Attempting to use the <strong>Delete Menu</strong> action invokes strict database constraint checks. The system will gracefully block the deletion and throw an error if:
                        <ul class="mt-2">
                            <li><strong>Error 1451 (Foreign Key Constraint):</strong> The menu cannot be deleted because it acts as a Parent to active submenus, or because it is currently assigned to existing Roles in the `user_access_menu` table. You must revoke access and delete submenus first.</li>
                            <li><strong>Error 1142 (Privilege Constraint):</strong> The database user executing the query lacks `DELETE` privileges, ensuring absolute data integrity against unauthorized structural modifications.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-shield text-primary mr-2"></i> 3. Interactive Access Mapping (Live Synchronization)</h3>
            <p class="text-muted mb-4">Once menus are registered, you must dictate which Roles can access them using the interactive Matrix.</p>

            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Accessing the Role Matrix</h5>
                    <p class="text-muted mb-0 ml-4">Navigate to <strong>Access Control</strong> under the Manage Account section. Click the <strong>Configure</strong> button on any Role card (e.g., Administrator) to enter the `Role Access` interface.</p>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>The Access Granted Toggle</h5>
                    <div class="text-muted mb-0 ml-4">
                        The matrix displays all system menus. Under the <strong>ACCESS GRANTED</strong> column, you will find dynamic toggle switches:
                        <ul class="mt-2">
                            <li><span class="badge badge-success">Green (Active)</span>: The role has explicit permission to render the menu in the sidebar and execute the URL in the backend.</li>
                            <li><span class="badge badge-secondary">Grey (Inactive)</span>: The menu is strictly concealed, and any direct URL navigation is forcefully blocked.</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>AJAX Real-Time Synchronization</h5>
                    <div class="text-muted mb-0 ml-4">
                        There is no universal "Save" button on this page. Clicking a toggle triggers an immediate, asynchronous AJAX request to the server via the `changeAccess` controller method. The system will instantly:
                        <ul class="mt-2">
                            <li>Insert or delete the relationship in the `user_access_menu` table.</li>
                            <li>Trigger the <code>$this-&gt;rbac-&gt;clear_menu_cache()</code> method to aggressively wipe cached permissions, ensuring security updates are instantaneous.</li>
                            <li>Display a toast notification confirming the matrix update.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-lock text-primary mr-2"></i> 4. Enforcing the RBAC Protections</h3>
            <p class="text-muted mb-4">Permissions mean nothing without strict enforcement layers. The system utilizes a dual-layer protection mechanism.</p>
            
            <div class="p-4 border rounded bg-light mb-4 shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-desktop text-success mr-2"></i> Layer 1: UI Rendering Protection (Frontend)</h5>
                <p class="text-muted mt-2 mb-3">While the sidebar is dynamically rebuilt based on session data, specific action buttons within a page (e.g., `Edit`, `Delete`, `Export`) must be manually obscured if the user lacks the specific permission. Developers achieve this by wrapping UI elements in the <code>has_permission()</code> helper.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-warning">&lt;?php if</span> (<span class="text-info">has_permission</span>(<span class="text-success">'merchant/delete'</span>)): <span class="text-warning">?&gt;</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&lt;button class="btn btn-danger"&gt;Delete Merchant&lt;/button&gt;<br>
                    <span class="text-warning">&lt;?php endif; ?&gt;</span>
                </div>
            </div>

            <div class="p-4 border rounded bg-light shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-server text-info mr-2"></i> Layer 2: Controller Routing Protection (Backend)</h5>
                <p class="text-muted mt-2 mb-3">Hiding a button visually does not prevent a malicious user from manually typing the URL (e.g., <code>/merchant/delete/1</code>). Thus, <strong>every secured controller</strong> must invoke the <code>is_logged_in()</code> middleware directly inside its constructor. This intercepts the incoming HTTP request and cross-references the targeted URI against the user's active RBAC mappings.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-warning">public function</span> <span class="text-info">__construct</span>() {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-warning">parent::</span>__construct();<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-secondary">// Validates session and intercepts unauthorized access attempts</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-info">is_logged_in</span>(); <br>
                    }
                </div>
                <p class="small text-danger mt-3 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> If the URI fails validation, the system instantly halts the controller execution and triggers a hard <strong>403 Forbidden</strong> termination response. There is no bypass.</p>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 5. Troubleshooting &amp; Diagnostics</h3>
            <p class="text-muted mb-4">Refer to these common diagnostics when dealing with RBAC integration faults:</p>
            
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-sitemap"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Orphaned Submenus (Parent Missing)</strong>
                    <p class="mb-2 text-muted small">A submenu toggle is Green, but it does not appear in the sidebar navigation.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> A submenu strictly relies on its Parent Menu to render the hierarchical dropdown. Check the `Role Access` table and verify the Parent Menu's toggle is also activated.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-link"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Unexpected 404 (Not Found) or 403 Forbidden Exceptions</strong>
                    <p class="mb-2 text-muted small">The menu is clicked, but the system terminates the request unexpectedly.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> A desynchronization has occurred between the database and the physical file architecture. Check the `URL` field in the Menu Registration. It must exactly match the controller route (e.g., <code>transaction/qris</code>, no leading slash). If a developer renamed the controller or modified `routes.php`, the Menu URL must be updated to match the new route.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-code"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">has_permission() Suppresses Elements Erroneously</strong>
                    <p class="mb-2 text-muted small">A developer wrapped a button with `has_permission()`, but it refuses to render even for Super Admins.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> The string argument provided to the helper does not strictly equal the URL string registered in the Menu database. Do not use leading/trailing slashes if they aren't registered that way. Ensure the helper function is formally loaded via `autoload.php`.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-bug"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Catastrophic Infinite Redirect Loop (Login vs Dashboard)</strong>
                    <p class="mb-2 text-muted small">Users are aggressively bounced between the Login page and Dashboard repeatedly without end.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis (Developer Issue):</strong> The <code>is_logged_in()</code> middleware was erroneously executed within the `Auth/Login` controller's constructor. This triggers a cyclical redirect check. The Auth controller must be excluded from this middleware evaluation.</li>
                    </ul>
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
            <h2 class="font-weight-bold">Role-Based Access Control (RBAC)</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Panduan komprehensif berstandar enterprise untuk mengelola sistem Role-Based Access Control. Pelajari cara mengonfigurasi menu, memetakan hak akses secara real-time, dan menegakkan perlindungan mutlak pada lapisan UI maupun Backend.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sitemap text-primary mr-2"></i> 1. Arsitektur Sistem &amp; Hierarki</h3>
            <p class="text-muted mb-4">Sistem RBAC membatasi akses administratif berdasarkan pemberian hak akses modular. Sistem ini terdiri dari tiga lapisan yang terintegrasi erat:</p>
            <ul class="pl-4 text-muted">
                <li class="mb-2"><strong>Peran (Roles):</strong> Kelompok keamanan yang didefinisikan dalam sistem (misal, Administrator, Member). Peran menentukan batas maksimal akses yang dimiliki pengguna.</li>
                <li class="mb-2"><strong>Menu &amp; Submenu:</strong> Jalur rute fisik (routing path) aplikasi, yang dikategorikan di bawah `Group Modules` untuk membentuk navigasi sidebar yang dinamis.</li>
                <li class="mb-2"><strong>Pemetaan Akses:</strong> Tabel penghubung (`user_access_menu`) yang mengaitkan Role ID dengan Menu ID. Pemetaan ini dievaluasi secara real-time setiap kali autentikasi sesi berlangsung.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-bars text-primary mr-2"></i> 2. Manajemen Menu dan Struktur Navigasi</h3>
            <p class="text-muted mb-4">Sebelum hak akses diberikan, rute aplikasi harus diregistrasikan secara resmi di dalam sistem Menu Management. Hal ini memastikan kontroler backend dapat mengenali dan melindungi rute tersebut.</p>

            <div class="pl-3">
                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Langkah 1: Mengakses Konfigurasi Menu</h5>
                    <p class="text-muted mb-0 ml-4">Navigasikan kursor ke bagian <strong>Manage Account</strong> di sidebar lalu pilih <strong>Menu Management</strong> atau <strong>Submenu Management</strong>. Halaman ini menampilkan daftar induk seluruh jalur rute yang telah diregistrasi.</p>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Langkah 2: Registrasi Menu Baru</h5>
                    <div class="text-muted mb-0 ml-4">
                        Klik tombol <strong>+ Add Menu</strong>. Ini akan memunculkan jendela modal `New Menu` di mana Anda harus mendefinisikan atribut fisik dari rute aplikasi:
                        <ul class="mt-2">
                            <li><strong>Menu Title:</strong> Nama label persis yang akan ditampilkan kepada manusia di sidebar (contoh: <em>Merchant Management</em>).</li>
                            <li><strong>Group Module:</strong> Pilih header pengelompokan yang sudah ada (contoh: <em>Transaction</em>) sebagai tempat menampung menu ini. Untuk membuat header grup baru, klik tombol <strong>+ Add</strong> di sebelah dropdown lalu definisikan nama grupnya.</li>
                            <li><strong>URL:</strong> Jalur rute relatif kontroler (contoh: <code>merchant/manage</code>). <strong class="text-danger">Dilarang memberikan garis miring (slash) di awal</strong> dan jangan sertakan domain situs.</li>
                            <li><strong>Icon:</strong> Berikan nama class CSS dari FontAwesome (contoh: <code>fas fa-users</code>) sebagai representasi ikon grafis untuk menu tersebut.</li>
                            <li><strong>Parent Menu:</strong> Jika ini adalah menu level atas, pilih <strong>None (Main Menu)</strong>. Jika menu ini beroperasi sebagai sub-navigasi, pilih Parent Menu-nya dari daftar.</li>
                            <li><strong>Order:</strong> Nilai angka urut (contoh: <code>0</code>, <code>1</code>, <code>2</code>) yang menentukan posisi vertikal menu di antara menu-menu lainnya.</li>
                        </ul>
                        Klik tombol <strong>Create</strong>. Data menu akan disimpan secara instan via AJAX dan terdaftar secara resmi di dalam database.
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Langkah 3: Mengubah &amp; Mendistribusikan Pembaruan</h5>
                    <p class="text-muted mb-0 ml-4">Memodifikasi menu via fitur <strong>Edit Menu</strong> akan langsung mendistribusikan perubahannya secara global. Contohnya, jika Anda mengubah URL sebuah menu, maka rute hak akses seluruh Roles yang terkait akan ikut diperbarui tanpa perlu mengubah pengaturan peran (role) satu per satu. Ini menjamin konsistensi absolut.</p>
                </div>

                <div class="mb-5">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Langkah 4: Penghapusan Aman &amp; Proteksi Constraint Database</h5>
                    <div class="text-muted mb-0 ml-4">
                        Mencoba menggunakan fitur <strong>Delete Menu</strong> akan memicu pengecekan kendala (constraint) database yang ketat. Sistem akan menggagalkan penghapusan secara terstruktur jika:
                        <ul class="mt-2">
                            <li><strong>Error 1451 (Foreign Key Constraint):</strong> Menu tidak dapat dihapus karena ia bertindak sebagai Parent dari submenu-submenu yang masih aktif, atau karena menu tersebut masih digunakan oleh Role yang aktif di dalam tabel `user_access_menu`. Anda harus mencabut akses dan menghapus submenunya terlebih dahulu.</li>
                            <li><strong>Error 1142 (Privilege Constraint):</strong> Pengguna database yang mengeksekusi query tidak memiliki izin operasi `DELETE`, hal ini memberikan garansi keamanan terhadap perubahan struktur aplikasi oleh entitas yang tidak sah.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-shield text-primary mr-2"></i> 3. Matriks Pemetaan Akses Interaktif (Live Synchronization)</h3>
            <p class="text-muted mb-4">Setelah menu terdaftar, Anda harus mendikte Peran (Role) mana saja yang diizinkan mengaksesnya melalui Matriks interaktif.</p>

            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Mengakses Role Matrix</h5>
                    <p class="text-muted mb-0 ml-4">Masuk ke bagian <strong>Access Control</strong> di menu Manage Account. Klik tombol <strong>Configure</strong> pada salah satu kartu Peran (misal: Administrator) untuk masuk ke halaman <code>Role Access</code>.</p>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Tombol Sakelar Hak Akses (Toggle)</h5>
                    <div class="text-muted mb-0 ml-4">
                        Matriks ini menampilkan seluruh struktur menu sistem. Di bawah kolom <strong>ACCESS GRANTED</strong>, terdapat sakelar (toggle) dinamis:
                        <ul class="mt-2">
                            <li><span class="badge badge-success">Hijau (Aktif)</span>: Peran secara eksplisit diberikan izin untuk menampilkan menu di sidebar dan mengeksekusi URL tersebut di level backend.</li>
                            <li><span class="badge badge-secondary">Abu-abu (Nonaktif)</span>: Menu tersebut dikunci sepenuhnya, dan semua pengetikan URL secara paksa akan langsung diblokir.</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Sinkronisasi AJAX Real-Time</h5>
                    <div class="text-muted mb-0 ml-4">
                        Tidak ada tombol "Save" (Simpan) umum di halaman ini. Setiap kali Anda mengeklik toggle, sistem langsung melempar permintaan asinkron (AJAX) ke server melalui fungsi <code>changeAccess</code>. Sistem akan langsung:
                        <ul class="mt-2">
                            <li>Menyisipkan (insert) atau menghapus hubungan tersebut di tabel <code>user_access_menu</code>.</li>
                            <li>Memicu metode <code>$this-&gt;rbac-&gt;clear_menu_cache()</code> untuk membersihkan seluruh data izin pada memori singgah (cache), sehingga keamanan langsung diperbarui secara seketika.</li>
                            <li>Memunculkan notifikasi (toast) untuk mengonfirmasi matriks berhasil diperbarui.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-lock text-primary mr-2"></i> 4. Penegakan Perlindungan RBAC</h3>
            <p class="text-muted mb-4">Pengaturan hak akses tidak akan berguna tanpa lapisan penegakan (enforcement) yang ketat. Sistem ini menggunakan mekanisme proteksi ganda (dual-layer).</p>
            
            <div class="p-4 border rounded bg-light mb-4 shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-desktop text-success mr-2"></i> Lapisan 1: Proteksi Render Antarmuka (UI Frontend)</h5>
                <p class="text-muted mt-2 mb-3">Meskipun sidebar dirender sesuai akses, elemen-elemen aksi spesifik di dalam suatu halaman (seperti tombol Edit, Hapus, Export) harus secara manual disembunyikan jika pengguna tidak memiliki izin. Tim <em>developer</em> mencapainya dengan membungkus elemen UI menggunakan <em>helper</em> <code>has_permission()</code>.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-warning">&lt;?php if</span> (<span class="text-info">has_permission</span>(<span class="text-success">'merchant/delete'</span>)): <span class="text-warning">?&gt;</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&lt;button class="btn btn-danger"&gt;Hapus Merchant&lt;/button&gt;<br>
                    <span class="text-warning">&lt;?php endif; ?&gt;</span>
                </div>
            </div>

            <div class="p-4 border rounded bg-light shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-server text-info mr-2"></i> Lapisan 2: Proteksi Rute Kontroler (Backend)</h5>
                <p class="text-muted mt-2 mb-3">Hanya menyembunyikan tombol secara visual tidaklah mencegah pengguna iseng untuk mencoba menebak URL di browser (contoh: <code>/merchant/delete/1</code>). Oleh sebab itu, <strong>setiap kontroler yang diamankan</strong> wajib memanggil <em>middleware</em> <code>is_logged_in()</code> tepat di dalam konstruktornya. Ini akan mencegat permintaan HTTP dan menyandingkan URL yang diminta dengan matriks RBAC milik pengguna di database.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-warning">public function</span> <span class="text-info">__construct</span>() {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-warning">parent::</span>__construct();<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-secondary">// Memvalidasi sesi dan secara mutlak mencegat semua penetrasi akses ilegal</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-info">is_logged_in</span>(); <br>
                    }
                </div>
                <p class="small text-danger mt-3 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Jika URL gagal tervalidasi, sistem akan langsung memberhentikan paksa eksekusi skrip kontroler tersebut dan menampilkan halaman terminasi <strong>403 Forbidden</strong>. Tidak ada jalur belakang.</p>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 5. Pemecahan Masalah &amp; Diagnostik Terpadu</h3>
            <p class="text-muted mb-4">Gunakan rujukan diagnostik berikut jika Anda menemui kendala anomali saat integrasi RBAC:</p>
            
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-sitemap"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Submenu Terasing / Hilang (Parent Missing)</strong>
                    <p class="mb-2 text-muted small">Toggle submenu sudah Hijau, tetapi tidak dirender di sidebar.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Sebuah submenu berstatus hierarkis dan secara absolut mengandalkan Menu Induk (Parent) untuk merender elemen dropdown-nya. Periksa tabel `Role Access` dan verifikasi bahwa toggle Menu Induknya juga sudah dalam keadaan aktif (hijau).</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-link"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Insiden Error 404 (Not Found) atau 403 Forbidden yang Tak Terduga</strong>
                    <p class="mb-2 text-muted small">Menu tampil di sidebar dan dapat diklik, namun sistem langsung melakukan terminasi error.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Telah terjadi desinkronisasi antara database dengan wujud fisik file arsitektur. Periksa kolom `URL` pada pendaftaran menu. URL ini harus identik 100% dengan rute kontroler sistem. Apabila tim pengembang mengganti nama kontroler (atau mengubah isi `routes.php`), maka isian URL di Database Menu Management harus segera diperbarui agar selaras.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-code"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">has_permission() Salah Melakukan Pemblokiran Visibilitas</strong>
                    <p class="mb-2 text-muted small">Tim pengembang sudah mengaplikasikan perlindungan `has_permission()` pada tombol, tetapi tombol tersebut tidak pernah muncul sekalipun di layar Super Admin.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Parameter argumen string yang diberikan ke <em>helper</em> tidak selaras dengan pendaftaran string URL di database Menu. Dilarang memberikan garis miring tambahan jika di database tidak diregistrasi dengan format tersebut. Pastikan juga <em>file helper</em> sudah dimasukkan ke dalam rantai `autoload.php` sistem.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-bug"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Looping Redirect Bencana (Perang Login vs Dasbor)</strong>
                    <p class="mb-2 text-muted small">Pengguna secara agresif terus dilempar bolak-balik antara layar Login dan Dasbor tanpa henti ketika mencoba membuka sistem.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa (Kesalahan Developer):</strong> <em>Middleware</em> pelindung <code>is_logged_in()</code> tanpa sengaja dieksekusi di dalam blok konstruktor milik kontroler `Auth/Login`. Ini memicu pengecekan rute tanpa henti yang saling memantulkan satu sama lain. Kontroler Auth mutlak harus dikecualikan dari intersep ini.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

                