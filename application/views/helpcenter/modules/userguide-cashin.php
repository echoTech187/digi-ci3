<div id="module-ug-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Cashin Dashboard</strong> is where you configure which inbound payment channels a merchant can use to accept money from their customers. Learn how to manage Cashin Fee settings, execute Bulk Updates, and control channel lifecycles.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Configuration Parameters</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Parameter</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CHANNEL GROUP & EXTERNAL ID</strong></td><td class="p-3 border-0">The macro-category (e.g., <code>bifast</code>, <code>qris</code>) and the upstream provider (e.g., <code>quantum</code>, <code>paylabs</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SPECIFIC CHANNEL ID</strong></td><td class="p-3 border-0">The exact payment endpoint identifier (e.g., <code>bifast_bca</code>, <code>qris_dynamic</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FEE TYPE</strong></td><td class="p-3 border-0">The deduction model: <strong>Fixed</strong>, <strong>Percentage</strong>, or <strong>Both</strong> (Hybrid).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED / PERCENTAGE FEE</strong></td><td class="p-3 border-0">The numeric fee value. Must be explicitly <code>0</code> if not used. Percentage is between <code>0</code> and <code>100</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT MIN & MAX</strong></td><td class="p-3 border-0">Optional transactional floor/ceiling. Use <code>0</code> to inherit global gateway limits.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Action Menu -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-bolt text-primary mr-2"></i> Action Menu</h5>
            <p class="text-muted mb-4">Click the three dots (⋮) on any cashin channel row to access these management tools:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Action Menu Item</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-info"></i> Edit Channel</strong></td><td class="p-3 border-0">Update the fee values, fee types, limits, or settlement interval for this specific channel.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-trash-alt text-danger"></i> Delete Channel</strong></td><td class="p-3 border-0">Permanently remove this channel configuration. The merchant will no longer be able to receive funds through this specific channel ID.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step by Step Add Channel -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-plus-circle text-success mr-2"></i> Adding a New Cash In Channel</h5>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> New Cash In Channel</strong> button at the top right of the table.</li>
                    <li class="mb-3">In the modal, select or input the <strong>CHANNEL ID</strong> and <strong>CHANNEL GROUP</strong>.</li>
                    <li class="mb-3">Set the <strong>FEE TYPE</strong> (e.g., PERCENTAGE, FIXED, or BOTH) and input the respective fee values.</li>
                    <li class="mb-3">Optionally configure the minimum and maximum amount limits, and the settlement interval.</li>
                    <li class="mb-2">Click <strong>Save New Channel</strong> to apply the configuration immediately.</li>
                </ol>
            </div>
        </div>


        <!-- Searching & Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Searching & Filtering</h5>
            <p class="text-muted mb-4">Use the built-in search and filters to track down specific configuration parameters.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by Channel, ID, or Category...</em> box to instantly filter the table.</li>
                    <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                    <li class="mb-3">Configure your parameters: <strong>CHANNEL GROUP</strong> or <strong>EXTERNAL ID</strong>.</li>
                    <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                </ol>
            </div>
        </div>

        <!-- Form Validations & Constraints -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-danger mr-2"></i> Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Unique Channel</strong></td><td class="p-3 border-0">A merchant can only have one configuration per <code>Channel Group</code> and <code>Specific Channel ID</code>. Duplicates trigger an error.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Fee Type</code>, <code>Fixed Fee</code>, <code>Percentage Fee</code>, <code>Amount Min</code>, and <code>Amount Max</code> cannot be empty.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Numeric Bounds</strong></td><td class="p-3 border-0">Percentage fee must be between <code>0</code> and <code>100</code>. Amount Min must not be greater than Amount Max.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Notifications -->
        <div class="mb-5">
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-1"><strong>Creation:</strong> <code>Cashin channel mapped successfully.</code></li>
                            <li class="mb-1"><strong>Bulk Add:</strong> <code>Bulk cashin channels added successfully. Skipped X existing channels.</code></li>
                            <li class="mb-1"><strong>Update:</strong> <code>Cashin channel updated successfully.</code></li>
                            <li class="mb-0"><strong>Deletion:</strong> <code>Cashin channel mapping removed.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplicate (1062):</strong> <code>This channel mapping already exists for the selected merchant.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Find the existing mapping in the table and edit it, rather than creating a new one.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Foreign Key (1451):</strong> <code>Cannot delete or update a parent row: a foreign key constraint fails.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You cannot delete this channel if it has active transactional history associated with it. Disable the channel instead by setting its status to Inactive.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Invalid Limits:</strong> <code>Minimum amount cannot be greater than Maximum amount.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Correct the amount thresholds so that Min &lt;= Max, or set both to 0 to inherit global limits.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Cashin Processing</h5>
            <p class="text-muted mb-4">This module governs the commercial terms between the payment gateway and the merchant for inbound transactions. The gateway evaluates these rules in real-time:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">A customer pays the merchant. The gateway receives the inbound callback.</li>
                    <li class="mb-3">The system checks if the merchant has an <strong>Active</strong> Cashin configuration for that specific channel.</li>
                    <li class="mb-3">The fee is calculated based on the <strong>Fee Type</strong> configuration.</li>
                    <li class="mb-2">The calculated fee is deducted, and the net amount is credited to the merchant's Available Balance.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-clipboard-check text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Prerequisites</strong>
                    <p class="mb-0 text-muted small">Before configuring cashin fees, ensure the target merchant has an <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-merchant-edit'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Active</a> operational status, and the underlying Master Channel is globally enabled in the <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-channel'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Gateway Channel</a> settings.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Provisioning & Managing Channels</h5>
            <p class="text-muted mb-4">How to manage the payment channels a merchant can accept.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Add a Single Mapping</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click <strong><i class="fas fa-plus"></i> Add Channel</strong> on the action bar.</li>
                    <li class="mb-3">Select the <strong>Channel Group</strong>, <strong>External ID</strong>, and <strong>Specific Channel ID</strong>.</li>
                    <li class="mb-3">Define the <strong>Fee Structure</strong> (Type, Fixed, Percentage).</li>
                    <li class="mb-3">Set the Status to <strong>Active</strong>.</li>
                    <li class="mb-2">Click <strong>Save Configuration</strong>.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Bulk Provisioning</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> on the toolbar.</li>
                    <li class="mb-3">Select the target <strong>Cashin Channel Group</strong> and <strong>External ID Default</strong>.</li>
                    <li class="mb-3">Input the universal fee structure that will apply to all channels in the group.</li>
                    <li class="mb-2">Click <strong>Apply Bulk Settings</strong>. The system will safely skip any channels you have already configured individually.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Editing or Deleting</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the target configuration in the data table.</li>
                    <li class="mb-3">Click the action menu (⋮) and select <strong>Edit Setting</strong> to modify fees/status, or <strong>Delete</strong> to permanently remove access.</li>
                    <li class="mb-2">Routing identifiers (Channel Group, External ID) cannot be edited. If they are wrong, delete the entry and create a new one.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Specific Channel ID dropdown is empty
                </a>
                <div id="faq_en_ci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The form relies on cascading logic. Ensure you select the Channel Group and External ID first. If it's still empty, it means the upstream provider hasn't registered any endpoints for that group in the global Master Channel settings.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Bulk Add returned "No channels found"
                </a>
                <div id="faq_en_ci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The Bulk Add engine is idempotent. This message means 100% of the channels in that group have already been configured individually, so the system skipped them to protect your existing bespoke rates.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ci_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: "Invalid Amount Limits (Min > Max)" error
                </a>
                <div id="faq_en_ci_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The Minimum boundary you entered is larger than the Maximum boundary, which is logically impossible. Use <code>0</code> for both if you just want to use the global default limits.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ci_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 4: What happens if I set a channel to "Inactive"?
                </a>
                <div id="faq_en_ci_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The API endpoint is strictly locked. Any new payment generation requests for this channel will be instantly rejected by the gateway with a <code>403 Forbidden</code> or <code>503 Service Unavailable</code> error.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Dasbor <strong>Cashin</strong> adalah tempat Anda mengatur kanal pembayaran masuk apa saja yang bisa digunakan merchant untuk menerima uang dari pelanggannya. Pelajari cara mengelola pengaturan Biaya Cashin, melakukan Bulk Update, dan mengontrol siklus hidup kanal.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Parameter Konfigurasi</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Parameter</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CHANNEL GROUP & EXTERNAL ID</strong></td><td class="p-3 border-0">Kategori makro (misal, <code>bifast</code>) dan penyedia layanan di hulu (misal, <code>quantum</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SPECIFIC CHANNEL ID</strong></td><td class="p-3 border-0">Pengidentifikasi <em>endpoint</em> pembayaran pasti (misal, <code>bifast_bca</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FEE TYPE</strong></td><td class="p-3 border-0">Model pemotongan biaya: <strong>Fixed</strong> (Tetap), <strong>Percentage</strong> (Persentase), atau <strong>Both</strong> (Keduanya).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED / PERCENTAGE FEE</strong></td><td class="p-3 border-0">Nilai biaya. Harus diisi <code>0</code> jika tidak digunakan. Persentase harus antara <code>0</code> hingga <code>100</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT MIN & MAX</strong></td><td class="p-3 border-0">Batas nilai transaksi paling rendah/tinggi opsional. Isi <code>0</code> untuk mewarisi limit global.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Action Menu -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-bolt text-primary mr-2"></i> Menu Aksi (Action Menu)</h5>
            <p class="text-muted mb-4">Klik tiga titik (⋮) pada baris kanal cashin mana pun untuk mengakses alat manajemen ini:</p>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Menu Aksi</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-edit text-info"></i> Edit Channel</strong></td><td class="p-3 border-0">Perbarui nilai biaya, jenis biaya, limit, atau interval setelmen untuk kanal spesifik ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong><i class="fas fa-trash-alt text-danger"></i> Delete Channel</strong></td><td class="p-3 border-0">Hapus konfigurasi kanal ini secara permanen. Merchant tidak akan lagi bisa menerima dana melalui ID kanal ini.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step by Step Add Channel -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-plus-circle text-success mr-2"></i> Menambahkan Kanal Cash In Baru</h5>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> New Cash In Channel</strong> di kanan atas tabel.</li>
                    <li class="mb-3">Di dalam modal, pilih atau ketik <strong>CHANNEL ID</strong> dan <strong>CHANNEL GROUP</strong>.</li>
                    <li class="mb-3">Atur <strong>FEE TYPE</strong> (misal, PERCENTAGE, FIXED, atau BOTH) lalu masukkan nominal biaya masing-masing.</li>
                    <li class="mb-3">Secara opsional, konfigurasikan limit jumlah minimum dan maksimum, serta interval setelmen.</li>
                    <li class="mb-2">Klik <strong>Save New Channel</strong> untuk segera menerapkan konfigurasi.</li>
                </ol>
            </div>
        </div>


        <!-- Searching & Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Pencarian & Pemfilteran</h5>
            <p class="text-muted mb-4">Gunakan pencarian bawaan dan filter untuk melacak parameter konfigurasi spesifik.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by Channel, ID, or Category...</em> untuk memfilter tabel secara instan.</li>
                    <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                    <li class="mb-3">Saring parameter Anda: <strong>CHANNEL GROUP</strong> atau <strong>EXTERNAL ID</strong>.</li>
                    <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                </ol>
            </div>
        </div>

        <!-- Form Validations & Constraints -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-danger mr-2"></i> Validasi Formulir & Kendala Sistem</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Kendala</th>
                            <th class="p-3 border-0">Aturan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kanal Unik</strong></td><td class="p-3 border-0">Merchant hanya dapat memiliki satu konfigurasi per <code>Channel Group</code> dan <code>Specific Channel ID</code>. Duplikat akan memicu error.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0"><code>Fee Type</code>, <code>Fixed Fee</code>, <code>Percentage Fee</code>, <code>Amount Min</code>, dan <code>Amount Max</code> tidak boleh kosong.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Batas Numerik</strong></td><td class="p-3 border-0">Biaya persentase harus antara <code>0</code> dan <code>100</code>. Amount Min tidak boleh lebih besar dari Amount Max.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Notifications -->
        <div class="mb-5">
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Event Berhasil</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-1"><strong>Pembuatan:</strong> <code>Cashin channel mapped successfully.</code></li>
                            <li class="mb-1"><strong>Penambahan Massal:</strong> <code>Bulk cashin channels added successfully. Skipped X existing channels.</code></li>
                            <li class="mb-1"><strong>Pembaruan:</strong> <code>Cashin channel updated successfully.</code></li>
                            <li class="mb-0"><strong>Penghapusan:</strong> <code>Cashin channel mapping removed.</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Event Error & Solusi</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplikat (1062):</strong> <code>This channel mapping already exists for the selected merchant.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Cari mapping yang sudah ada di tabel lalu edit, jangan membuat yang baru.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Foreign Key (1451):</strong> <code>Cannot delete or update a parent row: a foreign key constraint fails.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda tidak dapat menghapus kanal ini jika sudah memiliki riwayat transaksi aktif. Nonaktifkan (Inactive) kanal tersebut sebagai gantinya.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Limit Tidak Valid:</strong> <code>Minimum amount cannot be greater than Maximum amount.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Perbaiki ambang batas jumlah sehingga Min &lt;= Max, atau set keduanya ke 0 untuk mewarisi limit global.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Pemrosesan Cashin</h5>
            <p class="text-muted mb-4">Modul ini mengatur ketentuan komersial transaksi masuk. Gateway mengevaluasi aturan-aturan ini secara real-time:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Pelanggan membayar ke merchant. Gateway menerima callback dana masuk.</li>
                    <li class="mb-3">Sistem mengecek apakah merchant memiliki konfigurasi Cashin berstatus <strong>Active</strong> untuk kanal tersebut.</li>
                    <li class="mb-3">Biaya dihitung secara matematis berdasarkan konfigurasi <strong>Fee Type</strong>.</li>
                    <li class="mb-2">Biaya dipotong dari pokok, lalu sisa uang (net) ditambahkan ke Available Balance merchant.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-clipboard-check text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Prasyarat</strong>
                    <p class="mb-0 text-muted small">Sebelum mengatur biaya cashin, pastikan merchant memiliki status operasional <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-merchant-edit'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Active</a>, dan Master Channel yang mendasarinya sudah diaktifkan secara global di pengaturan <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-channel'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Gateway Channel</a>.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mendaftarkan & Mengelola Kanal</h5>
            <p class="text-muted mb-4">Cara mengatur kanal penerimaan yang bisa dipakai oleh merchant.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Menambahkan Satu Mapping Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik <strong><i class="fas fa-plus"></i> Add Channel</strong> di bilah aksi.</li>
                    <li class="mb-3">Pilih <strong>Channel Group</strong>, <strong>External ID</strong>, dan <strong>Specific Channel ID</strong>.</li>
                    <li class="mb-3">Tentukan <strong>Fee Structure</strong> (Tipe, Fixed, Persentase).</li>
                    <li class="mb-3">Pastikan Status disetel ke <strong>Active</strong>.</li>
                    <li class="mb-2">Klik <strong>Save Configuration</strong>.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Registrasi Massal (Bulk Update)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> di toolbar.</li>
                    <li class="mb-3">Pilih <strong>Cashin Channel Group</strong> tujuan dan <strong>External ID Default</strong>-nya.</li>
                    <li class="mb-3">Masukkan struktur biaya universal yang akan diaplikasikan.</li>
                    <li class="mb-2">Klik <strong>Apply Bulk Settings</strong>. Sistem cerdas secara otomatis akan melewati (skip) kanal-kanal yang sudah Anda atur manual sebelumnya.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Mengedit atau Menghapus</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari konfigurasi yang ingin diubah di tabel data.</li>
                    <li class="mb-3">Klik menu aksi (⋮) lalu pilih <strong>Edit Setting</strong> untuk merombak biaya/status, atau <strong>Delete</strong> untuk mencabut hak akses permanen.</li>
                    <li class="mb-2">Pengidentifikasi (Channel Group, External ID) terkunci dan tak bisa diedit. Jika salah, hapus baris tersebut lalu buat ulang yang baru.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Dropdown Specific Channel ID kosong (tak ada pilihan)
                </a>
                <div id="faq_id_ci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Formulir ini punya logika bertingkat. Pastikan Anda memilih Channel Group dan External ID lebih dulu. Jika tetap kosong, berarti provider hulu (upstream) memang belum mendaftarkan satupun endpoint di master global gateway.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Pesan "No channels found" saat melakukan Bulk Add
                </a>
                <div id="faq_id_ci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Bulk Add memproteksi data Anda. Notifikasi ini berarti seluruh (100%) kanal di grup bersangkutan sudah Anda daftarkan secara individual, sehingga sistem men-skip semuanya agar tarif khusus tidak tertimpa percuma.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ci_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Error "Invalid Amount Limits (Min > Max)"
                </a>
                <div id="faq_id_ci_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Batas Minimum yang Anda input lebih besar daripada batas Maksimum; hal ini mustahil secara logika. Masukkan angka <code>0</code> untuk keduanya jika Anda hanya ingin memakai limit bawaan sistem.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ci_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 4: Apa akibat jika status kanal saya set "Inactive"?
                </a>
                <div id="faq_id_ci_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Endpoint API tersebut seketika dikunci. Permintaan (request) pembayaran baru dari pelanggan merchant akan langsung ditendang oleh gateway dengan error <code>403 Forbidden</code> atau <code>503 Service Unavailable</code>.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
