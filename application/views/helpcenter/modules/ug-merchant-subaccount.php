<div id="module-ug-merchant-subaccount" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Sub Accounts Management</strong> feature allows you to build a multi-level organizational hierarchy (up to 4 levels deep) for a merchant. It is designed to represent complex business structures like regional divisions, city branches, or individual store terminals, giving each unit its own credentials while maintaining top-down control.</p>

        <hr class="my-4">

        <!-- Conceptual Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Conceptual Architecture</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Architecture: The Merchant Entity</h5>
            <p class="text-muted mb-4">A Merchant record is the root node of the payment hierarchy. All transactions, sub-accounts, api keys, and webhook configurations are child objects linked to this Master Merchant ID.</p>

            <div class="p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Merchant Routing Hierarchy</h6>
                <div class="mermaid">
                flowchart TD
                    M((Master Merchant)) --> S1(Sub-Merchant A)
                    M --> S2(Sub-Merchant B)
                    M --> API[API Keys & Webhooks]
                    
                    S1 --> T1[Transaction 1]
                    S1 --> T2[Transaction 2]
                    S2 --> T3[Transaction 3]
                    
                    T1 -.->|Settlement| MB[(Master Balance)]
                    T2 -.->|Settlement| MB
                    T3 -.->|Settlement| MB
                    
                    style M fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                    style MB fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                </div>
            </div>
        </div>

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

        <!-- Searching -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Searching</h3>
                <p class="text-muted mb-4">Use the built-in search to track down specific sub-accounts.</p>

                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2"><strong>Quick Search:</strong> Type in the <em>Search by name, ID, or email...</em> box to instantly filter the sub-accounts list.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 1: Creating a Sub Account -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Registering a New Sub Account</h3>
                <p class="text-muted mb-4">Follow these steps to provision a new branch or business unit.</p>

                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3">Navigate to the <strong>Merchant Panel → Merchant Management</strong> list.</li>
                        <li class="mb-3">Click the action menu (⋮) on the parent merchant and select <strong>Sub Accounts</strong>.</li>
                        <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> Add Sub Account</strong> button at the top right of the toolbar. <em>(Note: This button will be disabled if the maximum hierarchy depth has been reached).</em></li>
                        <li class="mb-3">
                            Fill in the following required fields in the modal:
                            <ul class="text-muted mt-2" style="list-style-type: disc;">
                                <li class="mb-1"><strong>Sub Account Name:</strong> The display name for this specific branch or terminal. Required.</li>
                                <li class="mb-1"><strong>Email Address:</strong> The login ID for this sub-account. <em>Must be unique across the entire system.</em> Required.</li>
                                <li class="mb-1"><strong>Password & Confirm Password:</strong> The initial login credential. Required.</li>
                                <li class="mb-1"><strong>Status:</strong> Controls dashboard access. <code>Active</code> allows login, <code>Blocked</code> denies login.</li>
                            </ul>
                        </li>
                        <li class="mb-2">Click <strong>Save Account</strong>. The system will automatically generate the required hierarchy linkages and default roles.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 2: Managing Sub Accounts -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Sub Account Action Menu</h3>
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
                            <tr><td class="p-3 border-0"><strong><i class="fas fa-exchange-alt text-warning"></i> Mutations</strong></td><td class="p-3 border-0">Directly jump to the Mutation Logs filtered specifically for this sub-account to audit its individual financial movements.
                            <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-mutation'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Constraint Type / Parameter</th>
                        <th class="p-3 border-0">System Enforcement Rule</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Sub Account Name</strong></td><td class="p-3 border-0">The display name for the branch or terminal. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>Email Address</strong></td><td class="p-3 border-0">Even though it is a sub-account, the <code>Email Address</code> must be completely unique across the entire system (cannot be the same as the parent or any other merchant).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Password</strong></td><td class="p-3 border-0">The initial login credential. (Required)</td></tr>
                    <tr><td class="p-3 border-0"><strong>Status</strong></td><td class="p-3 border-0">Controls dashboard access. <code>Active</code> allows login, <code>Blocked</code> denies login.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Depth Limit</strong></td><td class="p-3 border-0">The system prevents creation if the hierarchy depth exceeds Level 3.</td></tr>
                </tbody>
            </table>
        </div>

        <!-- System Notifications -->
        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
        <div class="d-flex flex-column mb-4">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-3"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                    <div class="d-flex align-items-center mb-0 small text-muted">
                        <i class="fas fa-info-circle text-success mr-2"></i>
                        <div><strong>Creation:</strong> <code class="ml-1">Merchant successfully registered.</code></div>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-3"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                    
                    <div class="mb-3 small">
                        <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Duplicate Email (1062):</strong> <code class="ml-1">A merchant account with this email already exists.</code></div>
                        <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You must use a unique email for every sub-account. Try adding an alias, e.g., <code>contact+branch1@merchant.com</code>.
                        </div>
                    </div>
                    
                    <div class="mb-0 small">
                        <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Access Denied (1142):</strong> <code class="ml-1">Access Denied. You do not have sufficient database privileges to create merchant accounts.</code></div>
                        <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks INSERT privileges. Contact the Database Administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Why is the "Add Sub Account" button missing?</span>
            </div>
            <p class="hc-faq-a">The system enforces a maximum hierarchy depth of 4 levels (Parent + Level 1 + Level 2 + Level 3). If you are currently viewing a Level 3 sub-account, you cannot create any further sub-accounts beneath it, and the button will be disabled.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Do sub-accounts have their own balances?</span>
            </div>
            <p class="hc-faq-a">Yes. Unlike simple cashier logins, Sub Accounts are treated as distinct entities in the ledger. They receive their own funds and maintain their own balance sheets, which can be audited via the "Mutations" action.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Can sub-accounts access OpenAPI endpoints?</span>
            </div>
            <p class="hc-faq-a">No, OpenAPI access is generally configured at the master merchant level. Sub-accounts are mainly utilized for dashboard access by cashiers, regional managers, or branch operators.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-key text-success"></i> 
                <span>How do I reset a sub-account password?</span>
            </div>
            <p class="hc-faq-a">You can use the <strong>Edit Details</strong> action menu on the sub-account row to manually overwrite and reset their password instantly.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-trash-alt text-secondary"></i> 
                <span>Can I delete a sub-account?</span>
            </div>
            <p class="hc-faq-a">No, sub-accounts cannot be permanently deleted once created in order to maintain ledger and transaction integrity. You can only change their Status to <strong>Blocked</strong> to restrict dashboard access.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-info"></i> 
                <span>Why can't I edit a sub-account's email?</span>
            </div>
            <p class="hc-faq-a">The email address acts as the primary unique identifier for the account across the system. If you need to change the email address, you must create a new sub-account and block the old one.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-percentage text-primary"></i> 
                <span>Do sub-accounts inherit the parent's fee settings?</span>
            </div>
            <p class="hc-faq-a">Yes, by default, sub-accounts inherit all transaction fees (MDR) and routing rules configured at the parent level, unless explicitly overridden by the system administrator.</p>
        </div>

        <!-- What's Next -->
        <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
            <h6 class="font-weight-bold mb-3 text-muted">What's Next?</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-delegate'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Delegate Access <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Learn how to assign roles and manage administrative access.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Configure API keys, webhooks, and IP whitelists.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Fitur <strong>Sub Accounts Management</strong> memungkinkan Anda membangun hierarki organisasi multi-tingkat (hingga kedalaman 4 level) untuk sebuah merchant. Fitur ini dirancang untuk mewakili struktur bisnis yang kompleks seperti divisi regional, cabang kota, atau terminal toko individual, memberikan setiap unit kredensial mereka sendiri sambil tetap mempertahankan kontrol dari pusat (induk).</p>

        <hr class="my-4">

        <!-- Conceptual Architecture -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Arsitektur Konseptual</h4>
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> Arsitektur: Entitas Merchant</h5>
            <p class="text-muted mb-4">Data Merchant adalah inti dari hierarki sistem pembayaran. Semua transaksi, sub-akun, API key, dan konfigurasi webhook merupakan objek turunan (child) yang terhubung ke Master Merchant ID ini.</p>

            <div class="p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Hierarki Perutean Merchant</h6>
                <div class="mermaid">
                flowchart TD
                    M((Master Merchant)) --> S1(Sub-Merchant A)
                    M --> S2(Sub-Merchant B)
                    M --> API[API Key & Webhook]
                    
                    S1 --> T1[Transaksi 1]
                    S1 --> T2[Transaksi 2]
                    S2 --> T3[Transaksi 3]
                    
                    T1 -.->|Settlement| MB[(Master Balance)]
                    T2 -.->|Settlement| MB
                    T3 -.->|Settlement| MB
                    
                    style M fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                    style MB fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                </div>
            </div>
        </div>

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

        <!-- Pencarian -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Pencarian</h3>
                <p class="text-muted mb-4">Gunakan pencarian bawaan untuk melacak sub-akun spesifik.</p>

                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by name, ID, or email...</em> untuk memfilter daftar sub-akun secara instan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 1: Creating a Sub Account -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Mendaftarkan Sub Account Baru</h3>
                <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk menyediakan cabang atau unit bisnis baru.</p>

                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3">Akses menu <strong>Merchant Panel → Merchant Management</strong>.</li>
                        <li class="mb-3">Klik tombol aksi (⋮) pada merchant induk dan pilih <strong>Sub Accounts</strong>.</li>
                        <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> Add Sub Account</strong> di kanan atas toolbar. <em>(Catatan: Tombol ini akan dinonaktifkan jika kedalaman hierarki maksimum telah tercapai).</em></li>
                        <li class="mb-3">
                            Isi detail berikut pada form pendaftaran:
                            <ul class="text-muted mt-2" style="list-style-type: disc;">
                                <li class="mb-1"><strong>Sub Account Name:</strong> Nama tampilan untuk cabang atau terminal ini. Wajib.</li>
                                <li class="mb-1"><strong>Email Address:</strong> ID login untuk sub-akun ini. <em>Harus benar-benar unik di seluruh sistem.</em> Wajib.</li>
                                <li class="mb-1"><strong>Password & Confirm Password:</strong> Kredensial awal untuk login. Wajib.</li>
                                <li class="mb-1"><strong>Status:</strong> Mengontrol akses dasbor. <code>Active</code> mengizinkan login, <code>Blocked</code> menolak login.</li>
                            </ul>
                        </li>
                        <li class="mb-2">Klik <strong>Save Account</strong>. Sistem akan secara otomatis membuat tautan hierarki dan peran default yang diperlukan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 2: Managing Sub Accounts -->
        <div class="row hc-step-row align-items-center mb-5">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Menu Aksi Sub Account</h3>
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
                            <tr><td class="p-3 border-0"><strong><i class="fas fa-exchange-alt text-warning"></i> Mutations</strong></td><td class="p-3 border-0">Langsung melompat ke Log Mutasi yang difilter khusus untuk sub-akun ini guna mengaudit pergerakan finansial individualnya.
                            <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-mutation'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Parameter Reference & Validations -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width: 25%;">Parameter / Tipe Validasi</th>
                        <th class="p-3 border-0">Deskripsi & Aturan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="p-3 border-0"><strong>Sub Account Name</strong></td><td class="p-3 border-0">Nama tampilan untuk cabang atau terminal ini. (Wajib diisi)</td></tr>
                    <tr><td class="p-3 border-0"><strong>Email Address</strong></td><td class="p-3 border-0">ID login untuk sub-akun. <em>Harus benar-benar unik di seluruh sistem</em> (tidak boleh sama dengan induk atau merchant lainnya).</td></tr>
                    <tr><td class="p-3 border-0"><strong>Password</strong></td><td class="p-3 border-0">Kredensial awal untuk login. (Wajib diisi)</td></tr>
                    <tr><td class="p-3 border-0"><strong>Status</strong></td><td class="p-3 border-0">Mengontrol akses dasbor. <code>Active</code> mengizinkan login, <code>Blocked</code> menolak login.</td></tr>
                    <tr><td class="p-3 border-0"><strong>Batas Kedalaman (Depth)</strong></td><td class="p-3 border-0">Sistem mencegah pembuatan akun jika kedalaman hierarki telah mencapai Level 3 (Induk + Level 1 + Level 2 + Level 3).</td></tr>
                </tbody>
            </table>
        </div>

        <!-- System Notifications -->
        <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
        <div class="d-flex flex-column mb-4">
            <div class="mb-3">
                <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                    <strong class="text-success d-block mb-3"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                    <div class="d-flex align-items-center mb-0 small text-muted">
                        <i class="fas fa-info-circle text-success mr-2"></i>
                        <div><strong>Pembuatan:</strong> <code class="ml-1">Merchant successfully registered.</code></div>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                    <strong class="text-danger d-block mb-3"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                    
                    <div class="mb-3 small">
                        <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Duplikat Email (1062):</strong> <code class="ml-1">A merchant account with this email already exists.</code></div>
                        <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda wajib menggunakan email yang unik untuk setiap sub-akun. Coba tambahkan alias, misal <code>contact+cabang1@merchant.com</code>.
                        </div>
                    </div>
                    
                    <div class="mb-0 small">
                        <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Access Denied (1142):</strong> <code class="ml-1">Access Denied. You do not have sufficient database privileges to create merchant accounts.</code></div>
                        <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki izin INSERT. Silakan hubungi Database Administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Mengapa tombol "Add Sub Account" tidak muncul?</span>
            </div>
            <p class="hc-faq-a">Sistem memberlakukan kedalaman hierarki maksimum 4 level (Induk + Level 1 + Level 2 + Level 3). Jika Anda saat ini melihat sub-akun Level 3, Anda tidak dapat membuat sub-akun lagi di bawahnya, dan tombol tersebut akan dinonaktifkan secara otomatis.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apakah sub-akun memiliki saldo mereka sendiri?</span>
            </div>
            <p class="hc-faq-a">Ya. Berbeda dengan login kasir biasa, Sub Akun diperlakukan sebagai entitas yang mandiri di dalam buku besar (ledger). Mereka menerima dana mereka sendiri dan memelihara lembar saldo tersendiri yang dapat diaudit melalui aksi "Mutations".</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Apakah sub-akun bisa mengakses endpoint OpenAPI?</span>
            </div>
            <p class="hc-faq-a">Tidak, akses OpenAPI pada umumnya dikonfigurasi pada tingkat merchant induk (Master Merchant). Sub-akun lebih ditujukan untuk akses Dasbor oleh kasir, manajer regional, atau operator cabang.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-key text-success"></i> 
                <span>Bagaimana cara mereset password sub-akun?</span>
            </div>
            <p class="hc-faq-a">Anda bisa menggunakan menu aksi <strong>Edit Details</strong> pada baris sub-akun terkait untuk melakukan *overwrite* dan mereset password mereka secara instan.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-trash-alt text-secondary"></i> 
                <span>Bisakah saya menghapus sub-akun?</span>
            </div>
            <p class="hc-faq-a">Tidak, sub-akun tidak dapat dihapus secara permanen setelah dibuat untuk menjaga integritas pembukuan (ledger) dan riwayat transaksi. Anda hanya dapat mengubah Status mereka menjadi <strong>Blocked</strong> untuk membatasi akses dasbor.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-envelope text-info"></i> 
                <span>Mengapa saya tidak bisa mengedit email sub-akun?</span>
            </div>
            <p class="hc-faq-a">Alamat email berfungsi sebagai pengidentifikasi unik utama untuk akun di seluruh sistem. Jika Anda perlu mengubah alamat email, Anda harus membuat sub-akun baru dan memblokir sub-akun yang lama.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-percentage text-primary"></i> 
                <span>Apakah sub-akun mewarisi pengaturan biaya (MDR) dari induk?</span>
            </div>
            <p class="hc-faq-a">Ya, secara default, sub-akun mewarisi semua biaya transaksi (MDR) dan aturan perutean yang dikonfigurasi di tingkat induk (parent), kecuali jika ditimpa (di-override) secara eksplisit oleh administrator sistem.</p>
        </div>

        <!-- What's Next -->
        <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
            <h6 class="font-weight-bold mb-3 text-muted">Langkah Selanjutnya?</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-delegate'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Delegasi Akses <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Pelajari cara menetapkan peran dan memberikan akses kepada staf.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Atur API key, webhook, dan IP whitelist untuk akun Anda.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>