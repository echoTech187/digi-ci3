<div id="module-ug-global-search" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Global Search Engine is an ultra-optimized, high-performance lookup tool accessible from the top navigation bar. It acts as an omni-search that scans menus, merchants, users, and massive transaction tables simultaneously in a single network roundtrip.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Search Capabilities Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Search Target</th>
                            <th class="p-3 border-0">What It Matches</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Menu Navigation</strong></td><td class="p-3 border-0">Menu titles (e.g., "Report") or URL paths (e.g., "finance/qris") to get direct links.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Merchant Identity</strong></td><td class="p-3 border-0">Merchant ID, Merchant Name, or Email. Automatically distinguishes between Masters and Sub-accounts.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Administrative Accounts</strong></td><td class="p-3 border-0">Admin Name or Admin Email to quickly jump to their access control profile.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel Configs</strong></td><td class="p-3 border-0">Provider descriptions or external IDs (e.g., "Xendit", "OVO") to locate their settings.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Deep Transactions</strong></td><td class="p-3 border-0">Exact TxID, VA Number, QRIS RRN, Bank Account, or Invoice Number (Requires 6+ characters).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Architecture &amp; How to Use Global Search</h3>
                <p class="text-muted mb-4">You can trigger the search instantly from anywhere in the dashboard without touching the mouse.</p>

                <div class="mermaid-container mb-4">
                    <div class="mermaid">
                        flowchart TD
                            A([User types in Search Bar]) --> B{Length >= 2?}
                            B -- No --> C[Wait for input]
                            B -- Yes --> D[Query Menus & Merchants]
                            D --> E{Length >= 6?}
                            E -- No --> F[Return Shallow Results]
                            E -- Yes --> G[Deep Scan Transactions]
                            F --> H[Filter via RBAC]
                            G --> H
                            H --> I[(Display Results)]
                    </div>
                </div>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Press <kbd>Ctrl</kbd> + <kbd>/</kbd> on your keyboard (or click the Search Icon <i class="fas fa-search"></i>) to focus the top search bar.</li>
                        <li class="mb-3">Type your query. The system requires a minimum of 2 characters to start the preliminary scan (menus and merchants).</li>
                        <li class="mb-3">To trigger the <strong>Deep Transaction Scan</strong>, continue typing until you reach at least <strong>6 characters</strong>.</li>
                        <li class="mb-2">Click on the desired result. The system will automatically route you to the correct page and pre-apply any necessary data filters.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Smart Redirection &amp; Permissions</h3>
                
                <div class="doc-callout callout-success shadow-sm">
                    <div class="callout-icon"><i class="fas fa-user-shield"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">RBAC-Aware Filtering</strong>
                        <p class="mb-0 text-muted small">The Global Search is tightly integrated with the Role-Based Access Control (RBAC) system. You will <strong>only</strong> see search results for menus, channels, and transactions that your specific role is authorized to view. Unauthorized data is automatically stripped from the search index before it reaches your browser.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Common Issues &amp; Troubleshooting</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 1: A transaction I know exists isn't showing up</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> You paste an Invoice ID and the search returns no results.<br><br>
                <strong>Resolution:</strong> (1) Ensure the query is at least <strong>6 characters long</strong> — shorter queries do not trigger the deep scan. (2) Verify your RBAC role grants access to that transaction type. (3) Double-check for invisible leading/trailing spaces in the copied text.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 2: The search results appear slow or lag significantly</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> Typing in the search bar causes noticeable delay.<br><br>
                <strong>Resolution:</strong> The Global Search has a built-in <strong>debounce</strong> of ~300ms to prevent excessive API spam while typing. If the slowdown is beyond that, it may indicate high server load during peak hours. Use the keyboard shortcut <kbd>Ctrl</kbd>+<kbd>/</kbd> to paste queries directly instead of typing them slowly.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Issue 3: Clicking a merchant result opens the wrong page</span>
            </div>
            <div class="hc-faq-a">
                <strong>Symptom:</strong> You clicked a merchant but landed on a sub-account page instead.<br><br>
                <strong>Resolution:</strong> The system automatically distinguishes between <strong>Master Merchants</strong> and <strong>Sub-Accounts</strong>. If you searched using a sub-account email, it routes to the sub-account editor. Search using the Master Merchant's ID directly if you want the master profile.
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Global Search Engine adalah alat pencarian yang sangat dioptimalkan yang dapat diakses dari bilah navigasi atas. Fitur ini bertindak sebagai pencarian universal yang memindai menu, merchant, pengguna, dan tabel transaksi raksasa secara bersamaan dalam satu siklus.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Kemampuan Pencarian</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Target Pencarian</th>
                            <th class="p-3 border-0">Data yang Dicocokkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Navigasi Menu</strong></td><td class="p-3 border-0">Judul menu (misal: "Report") atau jalur URL (misal: "finance/qris") untuk tautan langsung.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Identitas Merchant</strong></td><td class="p-3 border-0">ID Merchant, Nama Merchant, atau Email. Membedakan Master dan Sub-akun secara otomatis.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Akun Administratif</strong></td><td class="p-3 border-0">Nama atau Email Admin untuk melompat cepat ke profil kontrol akses mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Konfigurasi Channel</strong></td><td class="p-3 border-0">Deskripsi provider atau ID eksternal (misal: "Xendit", "OVO") untuk mencari pengaturan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Transaksi Mendalam</strong></td><td class="p-3 border-0">ID Transaksi persis, No VA, RRN QRIS, Rekening Bank, atau No Invoice (Butuh 6+ karakter).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Arsitektur &amp; Cara Menggunakan Global Search</h3>
                <p class="text-muted mb-4">Anda dapat memicu pencarian seketika dari bagian mana pun di dasbor tanpa menyentuh mouse.</p>

                <div class="mermaid-container mb-4">
                    <div class="mermaid">
                        flowchart TD
                            A([User mengetik di Search Bar]) --> B{Panjang >= 2?}
                            B -- Tidak --> C[Tunggu input]
                            B -- Ya --> D[Query Menu & Merchant]
                            D --> E{Panjang >= 6?}
                            E -- Tidak --> F[Kembalikan Hasil Dangkal]
                            E -- Ya --> G[Deep Scan Transaksi]
                            F --> H[Saring via RBAC]
                            G --> H
                            H --> I[(Tampilkan Hasil)]
                    </div>
                </div>

                <div class="pl-4 border-left border-primary ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Tekan <kbd>Ctrl</kbd> + <kbd>/</kbd> di keyboard Anda (atau klik Ikon Pencarian <i class="fas fa-search"></i>) untuk memfokuskan bilah pencarian atas.</li>
                        <li class="mb-3">Ketikkan kata kunci Anda. Sistem memerlukan minimal 2 karakter untuk memulai pemindaian awal (menu dan merchant).</li>
                        <li class="mb-3">Untuk memicu <strong>Pemindaian Transaksi Mendalam (Deep Scan)</strong>, terus ketik hingga mencapai minimal <strong>6 karakter</strong>.</li>
                        <li class="mb-2">Klik pada hasil yang diinginkan. Sistem akan secara otomatis mengarahkan Anda ke halaman yang benar dan menerapkan filter data yang sesuai.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Pengalihan Cerdas &amp; Hak Akses</h3>
                
                <div class="doc-callout callout-success shadow-sm">
                    <div class="callout-icon"><i class="fas fa-user-shield"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Filter Berbasis RBAC</strong>
                        <p class="mb-0 text-muted small">Pencarian Global terintegrasi sangat erat dengan sistem Role-Based Access Control (RBAC). Anda <strong>hanya</strong> akan melihat hasil pencarian yang diizinkan untuk dilihat oleh role/peran Anda. Data yang tidak diotorisasi secara otomatis dibuang dari indeks pencarian sebelum mencapai browser Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 1: Transaksi yang saya tahu ada tidak muncul</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Anda menempel ID Invoice dan pencarian tidak menemukan hasil.<br><br>
                <strong>Resolusi:</strong> (1) Pastikan query minimal <strong>6 karakter</strong> — query lebih pendek tidak memicu deep scan. (2) Verifikasi peran RBAC Anda memiliki izin untuk transaksi tersebut. (3) Periksa apakah ada spasi tersembunyi (spasi kosong) yang ikut tersalin.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 2: Hasil pencarian lambat muncul atau terasa lag</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Mengetik di search bar menyebabkan penundaan yang terasa.<br><br>
                <strong>Resolusi:</strong> Global Search memiliki <em>debounce</em> bawaan ~300ms untuk mencegah spam API berlebihan saat Anda mengetik. Jika lambat sekali, server mungkin beban puncak. Gunakan <kbd>Ctrl</kbd>+<kbd>/</kbd> untuk menyalin-tempel query secara langsung agar lebih cepat.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Masalah 3: Mengklik hasil merchant membuka halaman yang salah</span>
            </div>
            <div class="hc-faq-a">
                <strong>Gejala:</strong> Anda mengklik merchant tetapi diarahkan ke halaman sub-akun.<br><br>
                <strong>Resolusi:</strong> Sistem secara otomatis membedakan <strong>Merchant Master</strong> dan <strong>Sub-Akun</strong>. Jika Anda mencari memakai email sub-akun, sistem merutekan ke editor sub-akun. Cari memakai ID Merchant Master secara langsung jika Anda menginginkan profil utama.
            </div>
        </div>

    </div>
</div>
