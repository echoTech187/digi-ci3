<div id="module-merchant" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-store text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Merchant Management</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">The central command center for overseeing all registered business entities. Orchestrate merchant lifecycles, API access, hierarchical delegation, and robust financial auditing.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-filter text-primary mr-2"></i> 1. Global Search &amp; DataTables Filtering</h3>
            <p class="text-muted mb-4">Managing enterprise merchants requires high-performance lookup tools. The system utilizes asynchronous Server-Side DataTables with persistent session filtering.</p>
            
            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Live Search Persistence</h5>
                    <p class="text-muted mb-0 ml-4">The global search bar queries <code>Business Name</code>, <code>Registered Email</code>, and <code>Exact ID</code> simultaneously. Your search strings are immediately saved into the system's Session. If you navigate away to investigate a merchant's mutation log and return, your exact filter state is flawlessly preserved. Click <strong>Reset Filter</strong> to clear the session.</p>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Advanced Lifecycle Constraints</h5>
                    <div class="text-muted mb-0 ml-4">
                        Use the top filter panels to isolate merchants by operational states:
                        <ul class="mt-2">
                            <li><strong>Account Status:</strong> Filters by dashboard access. <em>Active</em> merchants can log in, while <em>Blocked</em> or <em>Frozen</em> merchants are locked out.</li>
                            <li><strong>OpenAPI Status:</strong> Filters by server-to-server capability. A merchant may be <em>Active</em> in their Account Status but <em>Blocked</em> in OpenAPI, dropping all their automated API requests at the Web Application Firewall (WAF).</li>
                            <li><strong>Registration Date:</strong> Isolates cohorts created between specific `From` and `To` timestamps.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Registration &amp; One-Time Secret Workflow</h3>
            <p class="text-muted mb-4">The merchant onboarding process enforces strict security protocols, completely preventing passwords from being transmitted in plain text or permanently logged.</p>

            <div class="p-4 border rounded bg-light mb-4 shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-key text-warning mr-2"></i> The One-Time Secret Handshake</h5>
                <p class="text-muted mt-2 mb-3">When you register a new merchant via the <strong>Register Merchant</strong> form, the backend communicates with the external Vault API (<code>https://password.gidi.co.id/api/v1/share</code>). The raw password and credentials are encrypted and stored in the vault with a strict 24-hour Time-To-Live (TTL). The database only stores the hashed password.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-secondary">// Example Response Upon Successful Registration</span><br>
                    <span class="text-success">Status:</span> Success<br>
                    <span class="text-info">Secret URL:</span> https://password.gidi.co.id/secret/a1b2c3d4e5f6...
                </div>
                <p class="small text-danger mt-3 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> You must immediately copy the Secret URL and securely hand it to the merchant. Once the merchant opens the link, the credentials are permanently destroyed from the vault and can never be retrieved again.</p>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-users text-primary mr-2"></i> 3. Supervisor Hierarchy</h3>
            <p class="text-muted mb-4">The platform supports grouping merchants under specialized Supervisor entities for aggregated reporting and monitoring.</p>
            
            <ul class="pl-4 text-muted mb-4">
                <li class="mb-2"><strong>Supervisor Creation:</strong> Navigate to the <em>Merchant Supervisor</em> sub-menu to create a Supervisor profile.</li>
                <li class="mb-2"><strong>Merchant Assignment:</strong> Edit a Merchant's profile and assign them to a specific Supervisor from the dropdown.</li>
                <li class="mb-2"><strong>Performance Auditing:</strong> Clicking <em>List Merchants</em> on a Supervisor's card reveals a dedicated DataTables view tracking the combined balance and transaction volumes strictly for the sub-merchants assigned to that supervisor.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cogs text-primary mr-2"></i> 4. Action Menu Workflows (⋮)</h3>
            <p class="text-muted mb-4">Every merchant row features an Action Menu providing deep administrative capabilities:</p>

            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Detail Merchant (Information Gathering)</h5>
                    <p class="text-muted mb-0 ml-4">Displays comprehensive KYC data, Business identifiers, Webhook Callback URLs, and API Keys. Also displays a live aggregation of PPOB, VA, QRIS, E-Wallet, and BiFast transaction volumes.</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Mutation Log (Financial Audit)</h5>
                    <p class="text-muted mb-0 ml-4">Transports you to the Finance module, pre-filtered for the merchant. Displays an immutable ledger of all inbound and outbound funds.</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Cashin &amp; Cashout Fee Settings</h5>
                    <p class="text-muted mb-0 ml-4">Acts as a direct hyperlink to the Commercial Configuration modules, pre-selecting the merchant so you can instantly define their bespoke Fixed or Percentage transaction fees.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 5. Troubleshooting &amp; Database Diagnostics</h3>
            <p class="text-muted mb-4">When managing merchants, the backend aggressively enforces data integrity. You may encounter the following constraint errors:</p>
            
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-database"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Error 1062: Duplicate Entry (Registration Failure)</strong>
                    <p class="mb-2 text-muted small">Occurs when attempting to register a new Merchant or update an existing one.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> The system enforces a strict <code>UNIQUE</code> constraint on the <code>c_email</code> column. A merchant account with this email address already exists in the system. You must use a different email or edit the existing merchant.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Error 1142: Privilege Constraint Denied</strong>
                    <p class="mb-2 text-muted small">The system flashes "Access Denied. You do not have sufficient database privileges."</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> While your application RBAC Role allows you to see the "Register Merchant" button, the underlying MySQL user authenticating the PHP application has been stripped of <code>INSERT</code> or <code>UPDATE</code> privileges for the <code>merchant</code> table by the DevOps team, preventing unauthorized state changes.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-ban"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">API Requests Return 403 Forbidden</strong>
                    <p class="mb-2 text-muted small">The merchant reports their API integrations are failing, but they can log into the dashboard.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> Check their <em>OpenAPI Status</em> via the Edit form. If it is set to <code>Blocked</code> or <code>Freeze</code>, the Web Application Firewall is dropping their server requests. Change the OpenAPI Status back to <code>Active</code> to restore integration connectivity.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    
    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-store text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Merchant Management</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Pusat komando utama untuk mengawasi seluruh entitas bisnis yang terdaftar. Atur siklus hidup merchant, akses API, hierarki supervisor, serta audit finansial dengan tingkat presisi yang tinggi.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-filter text-primary mr-2"></i> 1. Pencarian Global &amp; Filter DataTables</h3>
            <p class="text-muted mb-4">Mengelola merchant tingkat enterprise memerlukan alat pencarian berkinerja tinggi. Sistem memanfaatkan Server-Side DataTables asinkron dengan persistensi filter berbasis Sesi (Session).</p>
            
            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Persistensi Pencarian Otomatis</h5>
                    <p class="text-muted mb-0 ml-4">Bilah pencarian global menginterogasi kolom <code>Nama Bisnis</code>, <code>Email Terdaftar</code>, dan <code>ID Spesifik</code> secara bersamaan. String pencarian Anda seketika disimpan di memori Sesi. Jika Anda berpindah halaman untuk mengaudit mutasi lalu kembali lagi ke halaman merchant, kondisi filter Anda tidak akan hilang. Klik tombol <strong>Reset Filter</strong> untuk menghapus memori pencarian.</p>
                </div>

                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Filter Siklus Hidup Lanjutan</h5>
                    <div class="text-muted mb-0 ml-4">
                        Gunakan panel atas untuk mengisolasi merchant berdasarkan status operasionalnya:
                        <ul class="mt-2">
                            <li><strong>Account Status:</strong> Memfilter berdasarkan akses dashboard. Merchant <em>Active</em> dapat masuk (login), sedangkan merchant <em>Blocked</em> atau <em>Frozen</em> dikunci secara paksa.</li>
                            <li><strong>OpenAPI Status:</strong> Memfilter berdasarkan kapabilitas server-to-server. Seorang merchant bisa jadi <em>Active</em> di akun mereka, namun <em>Blocked</em> di OpenAPI, yang mana menyebabkan seluruh tembakan API mereka ditangkal oleh Web Application Firewall (WAF).</li>
                            <li><strong>Registration Date:</strong> Mengisolasi entitas merchant yang mendaftar pada rentang waktu `Dari` (From) dan `Sampai` (To) tertentu.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-plus text-primary mr-2"></i> 2. Registrasi &amp; Alur Kerja One-Time Secret</h3>
            <p class="text-muted mb-4">Proses pendaftaran merchant memberlakukan protokol keamanan ekstrem, mencegah sepenuhnya pengiriman password secara teks terang (plain text) atau pencatatan permanen pada log.</p>

            <div class="p-4 border rounded bg-light mb-4 shadow-sm">
                <h5 class="font-weight-bold text-dark"><i class="fas fa-key text-warning mr-2"></i> Jabat Tangan Rahasia Sekali Pakai (One-Time Secret)</h5>
                <p class="text-muted mt-2 mb-3">Ketika Anda mendaftarkan merchant baru via form <strong>Register Merchant</strong>, sistem *backend* berkomunikasi dengan Vault API eksternal (<code>https://password.gidi.co.id/api/v1/share</code>). Password mentah dan kredensial dienkripsi dan disimpan di dalam brankas dengan batas umur pakai (Time-To-Live) tepat 24 jam. Database utama hanya menyimpan versi *hash*-nya saja.</p>
                <div class="bg-dark p-3 rounded text-light mb-0" style="font-family: monospace; font-size: 14px;">
                    <span class="text-secondary">// Respons Sistem Saat Registrasi Berhasil</span><br>
                    <span class="text-success">Status:</span> Success<br>
                    <span class="text-info">Secret URL:</span> https://password.gidi.co.id/secret/a1b2c3d4e5f6...
                </div>
                <p class="small text-danger mt-3 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Anda diwajibkan menyalin Secret URL tersebut seketika dan menyerahkannya secara aman ke pihak merchant. Segera setelah merchant mengeklik tautan tersebut, kredensial di dalam brankas akan dihancurkan secara permanen dan tidak dapat dipulihkan oleh siapapun.</p>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-users text-primary mr-2"></i> 3. Hierarki Supervisor</h3>
            <p class="text-muted mb-4">Platform ini mendukung pengelompokan beberapa merchant di bawah kendali satu entitas Supervisor untuk keperluan pemantauan dan pelaporan gabungan.</p>
            
            <ul class="pl-4 text-muted mb-4">
                <li class="mb-2"><strong>Pembuatan Supervisor:</strong> Buka sub-menu <em>Merchant Supervisor</em> untuk mendaftarkan profil Supervisor baru.</li>
                <li class="mb-2"><strong>Penugasan Merchant:</strong> Edit profil Merchant bersangkutan, lalu pilih nama Supervisor mereka melalui menu <em>dropdown</em>.</li>
                <li class="mb-2"><strong>Audit Kinerja:</strong> Mengeklik tombol <em>List Merchants</em> pada kartu profil Supervisor akan menampilkan tabel DataTables khusus yang menyajikan kalkulasi saldo gabungan serta volume transaksi merchant yang menjadi bawahannya.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cogs text-primary mr-2"></i> 4. Alur Kerja Menu Aksi (⋮)</h3>
            <p class="text-muted mb-4">Setiap baris merchant dilengkapi dengan Menu Aksi yang menyediakan kapabilitas administrasi tingkat lanjut:</p>

            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Detail Merchant (Pengumpulan Informasi)</h5>
                    <p class="text-muted mb-0 ml-4">Menampilkan profil KYC komprehensif, identitas Bisnis, pengaturan URL Webhook Callback, dan API Key. Tampilan ini juga menghidangkan agregasi mutakhir atas jumlah transaksi PPOB, VA, QRIS, E-Wallet, dan BiFast.</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Mutation Log (Audit Finansial)</h5>
                    <p class="text-muted mb-0 ml-4">Membawa Anda langsung ke dalam modul Finance yang otomatis ter-filter khusus untuk merchant ini. Menyajikan buku besar (ledger) tak terbantahkan atas semua aliran dana masuk dan keluar.</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Cashin &amp; Cashout Fee Settings</h5>
                    <p class="text-muted mb-0 ml-4">Jalan pintas langsung menuju modul Konfigurasi Komersial. Merchant terkait akan langsung terpilih otomatis di dalam form, sehingga Anda bisa seketika mendaftarkan aturan biaya (Fixed atau Persentase) khusus untuk mereka.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 5. Pemecahan Masalah &amp; Diagnostik Database</h3>
            <p class="text-muted mb-4">Saat mengelola merchant, sistem *backend* akan melindungi integritas data dengan sangat agresif. Berikut adalah kendala constraint yang mungkin Anda temui:</p>
            
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-database"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Error 1062: Duplicate Entry (Gagal Pendaftaran)</strong>
                    <p class="mb-2 text-muted small">Muncul saat mencoba mendaftarkan Merchant baru atau memperbarui data merchant lama.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Sistem menegakkan constraint <code>UNIQUE</code> pada kolom <code>c_email</code>. Sudah ada akun merchant dengan alamat email yang sama persis di dalam database. Anda wajib menggunakan email lain atau mengubah data merchant yang sudah eksis tersebut.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Error 1142: Privilege Constraint Denied</strong>
                    <p class="mb-2 text-muted small">Sistem memunculkan pesan "Access Denied. You do not have sufficient database privileges."</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Meskipun pengaturan RBAC di antarmuka Anda mengizinkan Anda melihat tombol "Register", namun pengguna MySQL yang menjalankan koneksi aplikasi web saat ini telah dicabut hak <code>INSERT</code> atau <code>UPDATE</code>-nya oleh tim DevOps untuk tabel <code>merchant</code>. Tindakan Anda dihalau seketika demi keamanan.</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-ban"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">API Requests Mengembalikan 403 Forbidden</strong>
                    <p class="mb-2 text-muted small">Merchant melaporkan integrasi API mereka selalu ditolak, padahal mereka bisa masuk ke dashboard dengan normal.</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Periksa kolom <em>OpenAPI Status</em> milik mereka melalui form <em>Edit Merchant</em>. Jika statusnya adalah <code>Blocked</code> atau <code>Freeze</code>, Web Application Firewall akan menyaring dan membuang semua permintaan server mereka dari luar. Kembalikan OpenAPI Status menjadi <code>Active</code> untuk memulihkan konektivitas integrasi.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>







