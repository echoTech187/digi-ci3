<div id="module-merchant" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Merchant Management</strong> page is the central hub for onboarding new business clients, managing their operational lifecycle, and monitoring their global account health. Every entity that transacts through the payment gateway must be registered here.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Merchant Directory</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Column / Filter</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Merchant Name & Email</strong></td><td class="p-3 border-0">The registered business entity. The email is their unique login identifier.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Account Status</strong></td><td class="p-3 border-0">Governs Dashboard login. <span style="color:#16a34a;font-weight:600;">Active</span> = Login allowed. <span style="color:#dc2626;font-weight:600;">Blocked/Frozen</span> = Login denied.</td></tr>
                        <tr><td class="p-3 border-0"><strong>OpenAPI Status</strong></td><td class="p-3 border-0">Governs API endpoints. If Blocked, all programmatic payment requests are rejected (even if Account Status is Active).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Master Balance</strong></td><td class="p-3 border-0">The real-time global settlement balance. This is updated synchronously with every transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu (⋮)</strong></td><td class="p-3 border-0">Provides direct links to Edit Profile, view Mutation Logs, set Cashin/Cashout Fees, or adjust Master Balance manually.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: The Merchant Entity</h5>
            <p class="text-muted mb-4">A Merchant record is the root node of the payment hierarchy. All transactions, sub-accounts, api keys, and webhook configurations are child objects linked to this Master Merchant ID.</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
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

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Entity Creation:</strong> When a merchant is registered, a Master Merchant ID and Master Balance ledger are created.</li>
                    <li class="mb-3"><strong>Security Initialization:</strong> A secure API Secret Key is immediately generated and linked to their OpenAPI Status.</li>
                    <li class="mb-2"><strong>Fee Hierarchy:</strong> Merchants inherit global gateway fee rules unless specific channel overrides are created in the Cashin/Cashout modules.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-key text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Secret URL Expiry</strong>
                    <p class="mb-0 text-muted small">Upon registration, a <strong>Secret URL</strong> containing the initial login credentials is generated. This link is extremely volatile and automatically self-destructs after <strong>24 hours</strong>. If it expires before the merchant uses it, an Admin must manually reset the password via the Edit Merchant menu.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Operating the Merchant Directory</h5>
            <p class="text-muted mb-4">How to onboard new businesses and manage their lifecycle states.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Onboarding a New Merchant</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> Register Merchant</strong> button.</li>
                    <li class="mb-3">Input the Merchant's Business Name, Email (must be unique), and Contact details.</li>
                    <li class="mb-3">Set both <strong>Account Status</strong> and <strong>OpenAPI Status</strong> to <code>Active</code> to enable full operations.</li>
                    <li class="mb-3">Optionally assign them to a Supervisor Group if they belong to a parent franchise.</li>
                    <li class="mb-2">Click <strong>Register</strong> and immediately copy the generated Secret URL to securely transmit to the client.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Manual Balance Adjustments (Credit/Debit)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the merchant and click the <strong>Action Menu (⋮)</strong>.</li>
                    <li class="mb-3">Select <strong><i class="fas fa-plus-circle text-success"></i> Add Credit Balance</strong> for manual top-ups/settlements, or <strong><i class="fas fa-minus-circle text-danger"></i> Deduct Debit Balance</strong> for corrections/fee collections.</li>
                    <li class="mb-3">Input the nominal amount and a required descriptive note.</li>
                    <li class="mb-2">Submit the form. This injects a permanent ledger entry into the <code>Mutation Log</code> tied to your Admin ID.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Suspending API Access</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the Action Menu (⋮) and select <strong><i class="fas fa-edit"></i> Edit Merchant</strong>.</li>
                    <li class="mb-3">Change the <strong>OpenAPI Status</strong> to <code>Blocked</code>.</li>
                    <li class="mb-2">Save changes. The merchant can still log in to view their dashboard, but all live payment processing and webhook dispatches are immediately halted.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_merchant_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: "Email already registered" error during onboarding
                </a>
                <div id="faq_en_merchant_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The email address is already bound to another merchant entity. Emails act as unique global identifiers in the gateway. Use the Global Search to find the existing account, or ask the client for a different organizational email.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_merchant_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Merchant can log in but API payments are failing
                </a>
                <div id="faq_en_merchant_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This implies an asymmetry between their statuses. Open the Edit Merchant form and verify that the <strong>OpenAPI Status</strong> is <code>Active</code>. If it is active, check their API Secret Key and IP Whitelist in the Secret configurations.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_merchant_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Cannot find a specific merchant in the list
                </a>
                <div id="faq_en_merchant_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Your previous search or status filters are likely still active in your session. Click the <strong>Reset Filter</strong> button above the table to clear all session variables and reload the complete, unfiltered directory.
                    </div>
                </div>
            </div>
        </div>

        <!-- What's Next -->
        <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
            <h6 class="font-weight-bold mb-3 text-muted">What's Next?</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Step 2: Sub-Accounts <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Learn how to create branch accounts and assign supervisors.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Step 3: Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Configure API keys, webhooks, and IP whitelists for this merchant.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Halaman <strong>Merchant Management</strong> adalah pusat utama untuk melakukan onboarding klien bisnis baru, mengelola siklus operasional mereka, dan memantau kesehatan akun secara global. Setiap entitas yang bertransaksi melalui payment gateway wajib terdaftar di sini.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Direktori Merchant</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Kolom / Filter</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Nama & Email Merchant</strong></td><td class="p-3 border-0">Entitas bisnis yang terdaftar. Email berfungsi sebagai pengidentifikasi login unik mereka.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Account Status</strong></td><td class="p-3 border-0">Mengatur hak login Dashboard. <span style="color:#16a34a;font-weight:600;">Active</span> = Bisa login. <span style="color:#dc2626;font-weight:600;">Blocked/Frozen</span> = Login ditolak.</td></tr>
                        <tr><td class="p-3 border-0"><strong>OpenAPI Status</strong></td><td class="p-3 border-0">Mengatur endpoint API. Jika Blocked, semua request transaksi via sistem akan ditolak (walaupun Account Status berstatus Active).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Master Balance</strong></td><td class="p-3 border-0">Saldo penyelesaian (settlement) global secara real-time. Diperbarui seketika seiring terjadinya transaksi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Menu Aksi (⋮)</strong></td><td class="p-3 border-0">Pintasan langsung untuk Edit Profil, melihat Mutation Log, mengatur Biaya Cashin/Cashout, atau menyesuaikan saldo manual.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Entitas Merchant</h5>
            <p class="text-muted mb-4">Data Merchant adalah inti dari hierarki sistem pembayaran. Semua transaksi, sub-akun, API key, dan konfigurasi webhook merupakan objek turunan (child) yang terhubung ke Master Merchant ID ini.</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
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

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pembuatan Entitas:</strong> Saat merchant didaftarkan, Master Merchant ID dan buku besar (ledger) saldo dibuat.</li>
                    <li class="mb-3"><strong>Inisialisasi Keamanan:</strong> Sebuah API Secret Key aman langsung dibuat dan ditautkan dengan status OpenAPI mereka.</li>
                    <li class="mb-2"><strong>Hierarki Biaya:</strong> Merchant mewarisi aturan biaya gateway global kecuali jika ada modifikasi spesifik di modul Cashin/Cashout mereka.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-key text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Masa Berlaku Secret URL</strong>
                    <p class="mb-0 text-muted small">Setelah pendaftaran, sistem menghasilkan <strong>Secret URL</strong> berisi kredensial login awal. Tautan ini sangat sensitif dan otomatis hancur dengan sendirinya setelah <strong>24 jam</strong>. Jika kedaluwarsa sebelum dipakai merchant, Admin harus mereset password secara manual dari menu Edit Merchant.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengoperasikan Direktori Merchant</h5>
            <p class="text-muted mb-4">Cara mendaftarkan klien baru dan mengelola status operasional mereka.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Onboarding Merchant Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> Register Merchant</strong>.</li>
                    <li class="mb-3">Masukkan Nama Bisnis, Email (harus belum pernah dipakai), dan detail Kontak klien.</li>
                    <li class="mb-3">Set <strong>Account Status</strong> dan <strong>OpenAPI Status</strong> ke <code>Active</code> agar operasi berjalan penuh.</li>
                    <li class="mb-3">Pilih Grup Supervisor jika klien merupakan cabang dari perusahaan induk (opsional).</li>
                    <li class="mb-2">Klik <strong>Register</strong> lalu segera salin Secret URL yang muncul untuk dikirimkan secara aman ke klien.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Penyesuaian Saldo Manual (Kredit/Debit)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari merchant lalu klik <strong>Menu Aksi (⋮)</strong> di ujung kanan.</li>
                    <li class="mb-3">Pilih <strong><i class="fas fa-plus-circle text-success"></i> Add Credit Balance</strong> untuk penambahan saldo (top-up/settlement), atau <strong><i class="fas fa-minus-circle text-danger"></i> Deduct Debit Balance</strong> untuk pengurangan (koreksi).</li>
                    <li class="mb-3">Masukkan nominal dan catatan/alasan penyesuaian (wajib).</li>
                    <li class="mb-2">Simpan. Aksi ini akan mencetak rekam jejak permanen di <code>Mutation Log</code> yang terhubung dengan akun Admin Anda.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Menangguhkan Akses API</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik Menu Aksi (⋮) dan pilih <strong><i class="fas fa-edit"></i> Edit Merchant</strong>.</li>
                    <li class="mb-3">Ubah opsi <strong>OpenAPI Status</strong> menjadi <code>Blocked</code>.</li>
                    <li class="mb-2">Simpan perubahan. Merchant masih tetap bisa login ke Dasbor mereka, tetapi seluruh proses transaksi API live dan pengiriman webhook akan langsung dihentikan.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_merchant_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Error "Email already registered" saat pendaftaran
                </a>
                <div id="faq_id_merchant_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Alamat email sudah digunakan oleh entitas merchant lain. Email bersifat sebagai identitas tunggal di gateway. Gunakan pencarian untuk melacak akun yang lama, atau mintalah email yang berbeda kepada klien.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_merchant_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Merchant bisa login dasbor tapi transaksi API ditolak
                </a>
                <div id="faq_id_merchant_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Terdapat perbedaan status. Buka formulir Edit Merchant dan pastikan <strong>OpenAPI Status</strong> tersetel ke <code>Active</code>. Jika sudah aktif, masalah mungkin terletak pada API Secret Key mereka atau IP Whitelist yang salah.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_merchant_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Saya tidak menemukan merchant tertentu di tabel
                </a>
                <div id="faq_id_merchant_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Pencarian (Search) atau filter status dari sesi Anda sebelumnya kemungkinan masih aktif. Klik tombol <strong>Reset Filter</strong> di atas tabel untuk membersihkan sesi dan memuat ulang seluruh direktori.
                    </div>
                </div>
            </div>
        </div>

        <!-- What's Next -->
        <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
            <h6 class="font-weight-bold mb-3 text-muted">Selanjutnya</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Langkah 2: Sub-Accounts <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Pelajari cara membuat akun cabang dan menetapkan supervisor.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                        <div class="font-weight-bold text-primary mb-1">Langkah 3: Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                        <div class="small text-muted">Konfigurasikan API key, webhook, dan whitelist IP untuk merchant ini.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
