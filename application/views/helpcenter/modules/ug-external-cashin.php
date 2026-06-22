<div id="module-ug-external-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>External Cash-In</strong> module allows administrators to map a Merchant to a specific External Payment Provider (Aggregator/Acquirer) for inbound transactions (QRIS, E-Wallet, VA). You can configure specific fees, limits, and settlement intervals per channel.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Cash-In Mapping Directory</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Column / Filter</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Merchant</strong></td><td class="p-3 border-0">The target Active merchant mapped to the channel.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel Config</strong></td><td class="p-3 border-0">Displays the Channel Group (e.g. QRIS, VA), Channel ID, and the Provider (External ID Default).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Fee Details</strong></td><td class="p-3 border-0">The Fee Type (Flat/Percentage), Fixed Fee amount, and Percentage Fee applied per transaction.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Interval</strong></td><td class="p-3 border-0">Number of days (e.g., 0 for real-time, 1 for T+1) before funds are settled to the merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Limits</strong></td><td class="p-3 border-0">The minimum and maximum transaction amounts allowed for this mapping.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action Menu (⋮)</strong></td><td class="p-3 border-0">Direct links to Edit the mapping details or Delete the mapping.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Searching & Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Searching & Filtering</h5>
            <p class="text-muted mb-4">Use the built-in search and filters to track down specific merchant mappings.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search merchant or channel...</em> box to instantly filter the table.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                    <li class="mb-3">Configure your parameters: <strong>Merchant</strong>, <strong>Channel Group</strong>, <strong>Provider</strong>, <strong>Channel ID</strong>, or <strong>Status</strong>.</li>
                    <li class="mb-2">Click <strong>APPLY FILTER</strong> to load the data. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Action Menu -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-bolt text-primary mr-2"></i> Action Menu</h5>
            <p class="text-muted mb-4">Click the three dots (⋮) on any mapping row to access these management tools:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Action Menu Item</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-primary"></i> Edit</strong></td><td class="p-3 border-0">Update the fee parameters, limits, or status of an existing mapping.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-trash text-danger"></i> Delete</strong></td><td class="p-3 border-0">Permanently remove the mapping. Will fail if transactions are already tied to it.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Cash-In Routing</h5>
            <p class="text-muted mb-4">The Cash-In mapping engine routes inbound payments from end-users through the Gateway directly to specific aggregators based on these records.</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Inbound Payment Routing Flow</h6>
                <div class="mermaid">
                flowchart TD
                    EU((End User)) -->|Pays via QRIS/VA| G[Gateway]
                    
                    G -->|Lookup Mapping| M[Cash-In Mapping Table]
                    
                    M -.->|Provider A| PA[Paylabs]
                    M -.->|Provider B| PB[Faspay]
                    M -.->|Provider C| PC[Direct Bank]
                    
                    PA -->|Settles| MB[(Merchant Balance)]
                    PB -->|Settles| MB
                    PC -->|Settles| MB
                    
                    style EU fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                    style MB fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Entity Mapping:</strong> When an endpoint hits the Gateway, it checks the Cash-In Mapping table for the Merchant ID and Channel ID.</li>
                    <li class="mb-3"><strong>Aggregator Forwarding:</strong> The gateway uses the <code>External Default</code> to forward the request to the correct aggregator API.</li>
                    <li class="mb-2"><strong>Fee Calculation:</strong> The configured Fee and Fee Percentage are dynamically applied to the transaction before settlement.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-warning shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-warning"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Duplicate Mapping Restriction</strong>
                    <p class="mb-0 text-muted small">A merchant can only have ONE active mapping per Channel ID. If you attempt to map a channel that already exists for that merchant, the system will reject it with a duplicate warning.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Operating the Cash-In Mappings</h5>
            <p class="text-muted mb-4">How to manage channels and perform mass migrations.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Adding a New Cash-In Mapping</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> Add Mapping</strong> button.</li>
                    <li class="mb-3">Select the Merchant, the Channel Group, and the Provider (External Default).</li>
                    <li class="mb-3">Fill in the Fee rules, Settlement Interval, and nominal Min/Max limits.</li>
                    <li class="mb-2">Set Status to <code>Active</code> and click Save.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Bulk Updating Channels (Failover)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the <strong><i class="fas fa-globe"></i> Edit Mapping</strong> button to open the Bulk Update modal.</li>
                    <li class="mb-3">Choose the <strong>Update Scope</strong>: Update all merchants (Edit Mapping) or a Specific Merchant.</li>
                    <li class="mb-3">Define the <strong>Current Configuration</strong> (Group and Provider) to be migrated.</li>
                    <li class="mb-2">Define the <strong>New Configuration</strong> (Group and Provider) to migrate to, then click <strong>UPDATE ALL MERCHANTS</strong>.</li>
                </ol>
            </div>
        </div>

        <!-- Section 3: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0">All fields (Merchant, Group, Channel, Fee, Limits) must be completely filled.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Unique Channel</strong></td><td class="p-3 border-0">Merchant ID + Channel ID combination must be unique globally.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Numeric Types</strong></td><td class="p-3 border-0">Fees, percentages, and limits must be strictly numeric values.</td></tr>
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
                            <li class="mb-2"><strong>Mapping Added:</strong> <code>Configuration added successfully.</code></li>
                            <li class="mb-0"><strong>Bulk Update:</strong> <code>Global channel group update successful.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplicate Mapping (1062):</strong> <code>Failed to add configuration: This merchant already has a configuration for the selected Channel ID.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Edit the existing mapping instead of creating a new one.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Relation Constraint (1451):</strong> <code>Cannot delete this configuration because it is currently linked to existing transaction records.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Instead of deleting, edit the mapping and change its Status to 'Not Active'.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient privileges.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks privileges. Contact the Database Administrator.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_extci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: "No changes detected" error during Bulk Update
                </a>
                <div id="faq_en_extci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> This occurs when the New Configuration is completely identical to the Current Configuration. Ensure you are actually changing either the Provider or the Channel Group.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_extci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: The external provider dropdown is empty
                </a>
                <div id="faq_en_extci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Ensure you have selected a <strong>Channel Group</strong> first. The Provider and Channel ID dropdowns are populated dynamically based on the selected group via Ajax.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>External Cash-In</strong> memungkinkan administrator untuk memetakan Merchant ke Penyedia Pembayaran Eksternal (Aggregator/Acquirer) tertentu untuk transaksi masuk (QRIS, E-Wallet, VA). Anda dapat mengonfigurasi biaya (fee), limit, dan interval settlement secara spesifik per channel.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Direktori Mapping Cash-In</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Kolom / Filter</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Merchant</strong></td><td class="p-3 border-0">Merchant Aktif yang dipetakan ke channel ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel Config</strong></td><td class="p-3 border-0">Menampilkan Channel Group (mis. QRIS, VA), Channel ID, dan Provider tujuan (External ID).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Fee Details</strong></td><td class="p-3 border-0">Tipe Biaya (Flat/Persentase), nominal Fixed Fee, dan Persentase Fee per transaksi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Interval</strong></td><td class="p-3 border-0">Jumlah hari (mis. 0 untuk real-time, 1 untuk T+1) sebelum dana di-settle ke merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Limits</strong></td><td class="p-3 border-0">Batas minimum dan maksimum transaksi yang diizinkan untuk pemetaan ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Menu Aksi (⋮)</strong></td><td class="p-3 border-0">Pintasan langsung untuk Edit detail pemetaan atau Hapus pemetaan.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pencarian & Pemfilteran -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Pencarian & Pemfilteran</h5>
            <p class="text-muted mb-4">Gunakan pencarian bawaan dan filter untuk melacak mapping merchant spesifik.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search merchant or channel...</em> untuk memfilter tabel secara instan.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                    <li class="mb-3">Saring parameter Anda: <strong>Merchant</strong>, <strong>Channel Group</strong>, <strong>Provider</strong>, <strong>Channel ID</strong>, atau <strong>Status</strong>.</li>
                    <li class="mb-2">Klik <strong>APPLY FILTER</strong> untuk memuat data. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Menu Aksi (Action Menu) -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-bolt text-primary mr-2"></i> Menu Aksi (Action Menu)</h5>
            <p class="text-muted mb-4">Klik tiga titik (⋮) pada baris mapping mana pun untuk mengakses alat manajemen ini:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Menu Aksi</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-primary"></i> Edit</strong></td><td class="p-3 border-0">Perbarui parameter biaya, limit, atau status dari mapping yang ada.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-trash text-danger"></i> Delete</strong></td><td class="p-3 border-0">Hapus mapping secara permanen. Akan gagal jika transaksi sudah pernah dilakukan menggunakan mapping ini.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Perutean Cash-In</h5>
            <p class="text-muted mb-4">Mesin pemetaan Cash-In mengarahkan pembayaran masuk dari end-user melalui Gateway langsung ke aggregator spesifik berdasarkan rekam data ini.</p>

            <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Alur Perutean Pembayaran Masuk</h6>
                <div class="mermaid">
                flowchart TD
                    EU((End User)) -->|Bayar via QRIS/VA| G[Gateway]
                    
                    G -->|Cek Mapping| M[Tabel Mapping Cash-In]
                    
                    M -.->|Provider A| PA[Paylabs]
                    M -.->|Provider B| PB[Faspay]
                    M -.->|Provider C| PC[Direct Bank]
                    
                    PA -->|Settles| MB[(Saldo Merchant)]
                    PB -->|Settles| MB
                    PC -->|Settles| MB
                    
                    style EU fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                    style MB fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pemetaan Entitas:</strong> Saat endpoint mengenai Gateway, ia memeriksa tabel Cash-In Mapping untuk ID Merchant dan ID Channel.</li>
                    <li class="mb-3"><strong>Meneruskan ke Aggregator:</strong> Gateway menggunakan nilai <code>External Default</code> untuk meneruskan request ke API aggregator yang tepat.</li>
                    <li class="mb-2"><strong>Kalkulasi Biaya:</strong> Fee dan Fee Percentage yang dikonfigurasi akan diterapkan secara dinamis pada transaksi sebelum di-settle.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-warning shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-warning"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi Duplikasi Mapping</strong>
                    <p class="mb-0 text-muted small">Merchant hanya boleh memiliki SATU mapping aktif per Channel ID. Jika Anda mencoba menambahkan channel yang sudah ada untuk merchant tersebut, sistem akan menolaknya dengan peringatan duplikat.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengoperasikan Mapping Cash-In</h5>
            <p class="text-muted mb-4">Cara mengelola channel dan melakukan migrasi massal.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Menambahkan Mapping Cash-In Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> Add Mapping</strong>.</li>
                    <li class="mb-3">Pilih Merchant, Channel Group, dan Provider (External Default).</li>
                    <li class="mb-3">Isi aturan Fee, Settlement Interval, dan nominal Min/Max.</li>
                    <li class="mb-2">Atur Status menjadi <code>Active</code> lalu klik Save.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Pembaruan Massal Channel (Failover)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-globe"></i> Edit Mapping</strong> untuk membuka modal Bulk Update.</li>
                    <li class="mb-3">Pilih <strong>Update Scope</strong>: Perbarui semua merchant atau Merchant spesifik saja.</li>
                    <li class="mb-3">Tentukan <strong>Current Configuration</strong> (Group dan Provider) yang ingin dimigrasi.</li>
                    <li class="mb-2">Tentukan <strong>New Configuration</strong> (Group dan Provider) tujuan migrasi, lalu klik <strong>UPDATE ALL MERCHANTS</strong>.</li>
                </ol>
            </div>
        </div>

        <!-- Section 3: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Validasi Form & Batasan (Constraints)</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                            <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Semua kolom (Merchant, Group, Channel, Fee, Limits) wajib diisi penuh.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel Unik</strong></td><td class="p-3 border-0">Kombinasi ID Merchant + ID Channel wajib unik secara global.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tipe Angka</strong></td><td class="p-3 border-0">Fee, persentase, dan limit harus murni berformat angka numerik.</td></tr>
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
                            <li class="mb-2"><strong>Mapping Ditambahkan:</strong> <code>Configuration added successfully.</code></li>
                            <li class="mb-0"><strong>Update Massal:</strong> <code>Global channel group update successful.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplikat Mapping (1062):</strong> <code>Failed to add configuration: This merchant already has a configuration for the selected Channel ID.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Lakukan edit pada mapping yang sudah ada, jangan membuat mapping baru untuk channel yang sama.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Relasi Data (1451):</strong> <code>Cannot delete this configuration because it is currently linked to existing transaction records.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Jangan dihapus. Lakukan edit pada mapping tersebut dan ubah Statusnya menjadi 'Not Active'.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient privileges.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki hak akses yang cukup. Kontak Database Administrator Anda.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_extci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Error "No changes detected" saat Bulk Update
                </a>
                <div id="faq_id_extci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ini terjadi jika New Configuration (Tujuan) sepenuhnya sama persis dengan Current Configuration (Asal). Pastikan Anda benar-benar mengubah Provider tujuan atau Channel Group-nya.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_extci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Dropdown pilihan Provider kosong
                </a>
                <div id="faq_id_extci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Pastikan Anda telah memilih <strong>Channel Group</strong> terlebih dahulu. Data Provider dan Channel ID dimuat secara dinamis (via Ajax) berdasarkan Channel Group yang dipilih.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
