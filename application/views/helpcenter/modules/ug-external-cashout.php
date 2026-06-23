<div id="module-ug-external-cashout" class="hc-doc-section">
    <div class="ug-module-content">
    
    <!-- CSS for Premium Layout -->
    <style>
        .hc-premium-overview {
            background: linear-gradient(135deg, var(--hc-sidebar-bg) 0%, var(--hc-sidebar-bg) 100%);
            border-left: 4px solid #3b82f6;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        
        .hc-step-row {
            margin-bottom: 60px;
        }
        
        .hc-step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: bold;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .hc-step-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            font-size: 20px;
        }
        
        .hc-step-desc {
            color: #64748b;
            line-height: 1.7;
            font-size: 15px;
        }

        /* Mac Window Styling */
        .mac-window {
            /* background: #ffffff; */
            border-radius: 12px;
            /* box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.25); */
            /* border: 1px solid rgba(226, 232, 240, 0.8); */
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .mac-window:hover {
            transform: translateY(-5px);
            /* box-shadow: 0 30px 60px -12px rgba(59, 130, 246, 0.35); */
        }
        .mac-header {
            /* background: #f8fafc; */
            padding: 12px 16px;
            /* border-bottom: 1px solid #e2e8f0; */
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mac-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        /* .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; } */
        
        .mac-body img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Table Styling */
        .hc-ref-table th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }
        .hc-ref-table td {
            color: #475569;
            vertical-align: middle;
        }
        
        /* FAQ Styling */
        .hc-faq-item {
            background:var(--hc-bg);
            border: 1px solid var(--hc-border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .hc-faq-q {
            font-weight: 600;
            color: var(--hc-text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hc-faq-a {
            color: var(--hc-text);
            margin-left: 30px;
            margin-bottom: 0;
            line-height: 1.6;
        }
    </style>

    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <!-- 1. Conceptual Overview -->
        <div class="hc-premium-overview">
            <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Overview</h4>
            <p class="mb-0">The <strong>External Cash-Out</strong> module is a routing configuration tool. It allows administrators to map a specific Merchant to a chosen External Payment Provider (Aggregator/Acquirer). This ensures that when a merchant creates a Cash-Out (Disbursement) request, the system knows exactly which external channel to route it through, what fees to apply, and the permitted transaction limits.</p>
        </div>

        <!-- 2. Visual Step-by-Step Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <!-- Step 1 (Image Right) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-5 order-2 order-lg-1">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Access the Module</h3>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2">From the main dashboard, locate the left sidebar navigation.</li>
                        <li class="mb-2">Under the <strong>Configurations</strong> section, click on the <strong>External Integrations</strong> menu to expand it.</li>
                        <li class="mb-2">Select the <strong>External Cash-Out</strong> sub-menu to open the mapping management interface.</li>
                    </ol>
                </div>
            </div>
            <div class="col-lg-7 order-1 order-lg-2 mb-4 mb-lg-0">
                <div class="mac-window">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/actual_dashboard_step1_premium.png') ?>" alt="Sidebar Navigation" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 (Image Left) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-8">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Use Advanced Filters</h3>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2">Click the <strong>Filters</strong> button at the top right of the table to open the panel.</li>
                        <li class="mb-2"><strong>Merchant:</strong> Select a specific client to view only their mappings.</li>
                        <li class="mb-2"><strong>Channel Group:</strong> Filter by payment category (e.g., Bi-Fast, RTGS).</li>
                        <li class="mb-2"><strong>External ID Default:</strong> Pinpoint the exact aggregator or provider.</li>
                        <li class="mb-2"><strong>Channel ID:</strong> Filter by specific bank codes.</li>
                        <li class="mb-2"><strong>Status:</strong> View only Active or Inactive configurations.</li>
                        <li class="mb-2">Click <strong>Apply Filter</strong> to refresh the data table.</li>
                    </ol>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="mac-window">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/advanced_filters_matching.png') ?>" alt="Advanced Filters" style="width: 100%; display: block;  object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Step 3 (Image Right) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12 order-2 order-xl-1">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">View Mapping Data</h3>
                <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Data Table" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
                <p class="hc-step-desc mb-3">The main table gives you a comprehensive overview of all configurations. Here is a breakdown of the key features and data points available:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ul class="hc-step-desc mb-0 list-unstyled">
                        <li class="mb-3"><i class="fas fa-search text-success mr-2"></i><strong>Global Search:</strong> Use the top search bar to quickly find specific configurations by typing Merchant Name, Channel, or ID.</li>
                        <li class="mb-3"><i class="fas fa-columns text-success mr-2"></i><strong>Data Columns:</strong> View the exact routing (Channel Config), cost structures (Fee Details), and transaction boundaries (Limits) for each merchant.</li>
                        <li class="mb-3"><i class="fas fa-toggle-on text-success mr-2"></i><strong>Status Indicator:</strong> Instantly see whether a routing configuration is currently <span class="text-success">Active</span> or <span class="text-danger">Inactive</span>.</li>
                        <li class="mb-3"><i class="fas fa-edit text-success mr-2"></i><strong>Edit Mapping:</strong> Click the dark green button at the top to modify existing configurations.</li>
                        <li class="mb-3"><i class="fas fa-plus-circle text-success mr-2"></i><strong>Add Mapping:</strong> Click the primary green button to create a brand new cashout routing for a merchant.</li>
                        <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Action Menu:</strong> Click the three dots at the end of any row to individually <strong>Edit</strong> or <strong>Delete</strong> that specific mapping.</li>
                    </ul>
                </div>
            </div>
            
        </div>

        <!-- Step 4 (Image Left) -->
        <div class="row hc-step-row align-items-center">
            
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
                <h3 class="hc-step-title">Fill the Mapping Form</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Add Mapping Form" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>The following field mappings are required to create a new external cash-out configuration:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Merchant Selection:</strong> Choose the specific merchant you want to configure. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: You cannot proceed without selecting a valid merchant.</em></li>
                        <li class="mb-3"><strong>Channel Configuration:</strong> Select the <strong>Channel Group</strong> (e.g., Bi-Fast, RTGS) and define the <strong>External ID Default</strong> to specify the provider. Choose a <strong>Specific Channel ID</strong> if required by the provider.</li>
                        <li class="mb-3"><strong>Fee Structure & Limits:</strong> Define the Fee Type (Fixed or Percentage) and set the Minimum and Maximum Transaction Limits. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: The system will strictly verify that the Minimum limit is not greater than the Maximum limit.</em></li>
                        <li class="mb-3"><strong>Status Configuration:</strong> Set the mapping status to <strong>Active</strong> to enable it immediately, or <strong>Inactive</strong> to save as a draft.</li>
                        <li class="mb-3"><strong>Final Review & Save:</strong> Review your configuration against the Mapping Guide on the right panel. Click the <strong>Save</strong> button to commit the mapping to the database.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">5</div>
                <h3 class="hc-step-title">Global Update Feature</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/bulk_update.png') ?>" alt="Bulk Update Form" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>The Global Update feature is designed to quickly migrate traffic from one provider to another. Here is a breakdown of the key features and data points available:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Triggering the Update:</strong> Click the <strong>Global Update</strong> button when you need to quickly migrate traffic during a provider downtime.</li>
                        <li class="mb-3"><strong>Define Update Scope:</strong> Select <strong>All Mapping</strong> to update the provider for every merchant in the selected group, or choose <strong>Specific Merchant</strong> to isolate the migration.</li>
                        <li class="mb-3"><strong>Current Configuration (Filter):</strong> Identify the traffic you want to move by selecting the existing <strong>Channel Group</strong> and the current <strong>External ID Default</strong> that is experiencing issues.</li>
                        <li class="mb-3"><strong>New Configuration:</strong> Specify the target provider by selecting the replacement <strong>External ID Default</strong> where the traffic should be routed.</li>
                        <li class="mb-3"><strong>Confirmation & Execution:</strong> Review your migration settings carefully. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: The system will warn you if the new configuration conflicts with existing limits. Changes take effect immediately after confirmation.</em></li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">6</div>
                <h3 class="hc-step-title">Edit Mapping Configuration</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Edit Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>You can update an existing mapping if there are changes to the fee structure or if you need to manually toggle the status.</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Open Action Menu:</strong> Click the three dots (Action Menu) on the right side of the row you want to modify.</li>
                        <li class="mb-3"><strong>Select Edit:</strong> Choose <strong>Edit</strong> from the dropdown menu to open the form.</li>
                        <li class="mb-3"><strong>Make Changes:</strong> Update the necessary fields such as Fee Type, Limits, or Status.</li>
                        <li class="mb-3"><strong>Save Updates:</strong> Click <strong>Save Changes</strong> to apply the updates immediately to the system.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">7</div>
                <h3 class="hc-step-title">Delete Mapping Configuration</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/delete_mapping.png') ?>" alt="Delete Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>If a configuration is no longer needed or was created by mistake, you can permanently remove it from the system.</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Open Action Menu:</strong> Click the three dots (Action Menu) on the right side of the relevant row.</li>
                        <li class="mb-3"><strong>Select Delete:</strong> Choose <strong>Delete</strong> from the dropdown menu.</li>
                        <li class="mb-3"><strong>Confirmation:</strong> A confirmation prompt will appear. Verify that you are deleting the correct configuration. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone.</em></li>
                        <li class="mb-3"><strong>Finalize:</strong> Click <strong>Confirm Delete</strong> to permanently remove the mapping.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- 3. Technical Reference -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
        <div class="table-responsive mb-5">
            <table class="table table-bordered hc-ref-table bg-white">
                <thead>
                    <tr>
                        <th width="25%">Field Name</th>
                        <th>Technical Description & Business Logic</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Merchant</strong></td>
                        <td>The client/merchant account that will be performing the cash-out transactions.</td>
                    </tr>
                    <tr>
                        <td><strong>Channel Group</strong></td>
                        <td>The category of the payment method (e.g., <code>bifast</code>, <code>rtgs</code>, <code>online_transfer</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>Channel ID</strong></td>
                        <td>The specific bank code or channel identifier required by the aggregator (e.g., <code>bifast_bca</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>External ID Default (Provider)</strong></td>
                        <td>The aggregator or third-party provider that will process the transaction (e.g., <code>quantum</code>, <code>paylabs</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>Fee Type</strong></td>
                        <td>Determines how the merchant is charged. Can be <strong>Fixed</strong> (a flat rate) or <strong>Percentage</strong> (calculated based on the transaction amount).</td>
                    </tr>
                    <tr>
                        <td><strong>Amount Min / Max</strong></td>
                        <td>The minimum and maximum transaction value allowed for this specific routing configuration. Transactions outside this limit will be rejected.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 4. FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Why can't I delete a mapping configuration? (Error 1451)</span>
            </div>
            <p class="hc-faq-a">Due to database relation constraints, you cannot delete a mapping if it has already been used in past transactions (to preserve historical integrity). Instead, click Edit and change the Status to <strong>Not Active</strong>.</p>
        </div>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why is the Channel ID dropdown empty when adding a mapping?</span>
            </div>
            <p class="hc-faq-a">The Channel ID dropdown is populated dynamically based on the <strong>Channel Group</strong> you select. You must select a Channel Group first before the system can load the corresponding Channel IDs.</p>
        </div>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>What happens if I set the mapping status to Inactive?</span>
            </div>
            <p class="hc-faq-a">If a mapping is set to Inactive, the system will not route any new traffic through this configuration. Existing pending transactions may still process depending on the provider, but new requests will be blocked or fallback to another active provider if available.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Can I have multiple active mappings for the same Merchant and Channel Group?</span>
            </div>
            <p class="hc-faq-a">Yes, but the system will use the routing priority logic to determine which provider to hit first. Usually, you should ensure only one primary provider is active to avoid conflicts, unless a fallback routing structure is implemented.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>What is the difference between Fixed and Percentage Fee Type?</span>
            </div>
            <p class="hc-faq-a">A <strong>Fixed</strong> fee applies a flat rate (e.g., Rp 4,000) regardless of the transaction amount. A <strong>Percentage</strong> fee calculates the cost based on the transaction value (e.g., 1.5% of the total amount). Make sure to choose the correct type as agreed with your provider.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-triangle text-warning"></i> 
                <span>Why did my Global Update fail to migrate traffic?</span>
            </div>
            <p class="hc-faq-a">Global updates might fail if the target provider (New Configuration) does not support the specific Channel IDs or if the limits set for the target provider conflict with your existing merchant configurations. Always review the validation warnings before confirming.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>How do I know which External ID Default to choose?</span>
            </div>
            <p class="hc-faq-a">The External ID Default represents the third-party aggregator or bank provider (e.g., Quantum, Paylabs). You should select the provider that you have an active contract with and that supports the chosen Channel Group. Check your technical documentation or ask your administrator if unsure.</p>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <!-- 1. Conceptual Overview -->
        <div class="hc-premium-overview">
            <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Ikhtisar (Overview)</h4>
            <p class="mb-0">Modul <strong>External Cash-Out</strong> adalah alat konfigurasi *routing*. Modul ini memungkinkan administrator untuk memetakan Merchant ke Penyedia Pembayaran Eksternal (Aggregator/Acquirer). Hal ini memastikan bahwa ketika merchant melakukan permintaan Cash-Out (Pencairan/Disbursement), sistem mengetahui secara pasti ke *channel* mana transaksi tersebut harus diteruskan, biaya apa yang dikenakan, dan batas limit yang diizinkan.</p>
        </div>

        <!-- 2. Visual Step-by-Step Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Langkah-demi-Langkah</h4>
        
        <!-- Step 1 (Image Right) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-5 order-2 order-lg-1">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Akses Modul</h3>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2">Dari dasbor utama, perhatikan navigasi sidebar kiri.</li>
                        <li class="mb-2">Di bawah bagian <strong>Configurations</strong>, klik menu <strong>External Integrations</strong> untuk meluaskannya.</li>
                        <li class="mb-2">Pilih sub-menu <strong>External Cash-Out</strong> untuk membuka halaman manajemen pemetaan.</li>
                    </ol>
                </div>
            </div>
            <div class="col-lg-7 order-1 order-lg-2 mb-4 mb-lg-0">
                <div class="mac-window">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/actual_dashboard_step1_premium.png') ?>" alt="Navigasi Sidebar" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 (Image Left) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-8">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Gunakan Filter Pencarian</h3>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-2">Klik tombol <strong>Filters</strong> di pojok kanan atas tabel untuk membuka panel.</li>
                        <li class="mb-2"><strong>Merchant:</strong> Pilih klien spesifik untuk melihat pemetaan mereka saja.</li>
                        <li class="mb-2"><strong>Channel Group:</strong> Filter berdasarkan kategori pembayaran (mis. Bi-Fast, RTGS).</li>
                        <li class="mb-2"><strong>External ID Default:</strong> Tentukan aggregator atau provider secara spesifik.</li>
                        <li class="mb-2"><strong>Channel ID:</strong> Filter berdasarkan kode bank tertentu.</li>
                        <li class="mb-2"><strong>Status:</strong> Tampilkan hanya konfigurasi yang Aktif atau Tidak Aktif.</li>
                        <li class="mb-2">Klik <strong>Apply Filter</strong> untuk menyegarkan tabel data.</li>
                    </ol>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="mac-window">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/advanced_filters_matching.png') ?>" alt="Filter Lanjutan" style="width: 100%; display: block;  object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Step 3 (Image Right) -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12 order-2 order-xl-1">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Melihat Data Mapping</h3>
                <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Tabel Data" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
                <p class="hc-step-desc mb-3">Tabel utama memberikan gambaran komprehensif mengenai seluruh konfigurasi. Berikut penjelasan detail dari fitur dan data yang tersedia:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ul class="hc-step-desc mb-0 list-unstyled">
                        <li class="mb-3"><i class="fas fa-search text-success mr-2"></i><strong>Pencarian Global:</strong> Gunakan kotak pencarian di atas untuk menemukan rute spesifik berdasarkan Nama Merchant, Channel, atau ID dengan instan.</li>
                        <li class="mb-3"><i class="fas fa-columns text-success mr-2"></i><strong>Kolom Data:</strong> Menampilkan detail rute tujuan (Channel Config), struktur biaya (Fee Details), dan batasan nominal transaksi (Limits) untuk setiap merchant.</li>
                        <li class="mb-3"><i class="fas fa-toggle-on text-success mr-2"></i><strong>Indikator Status:</strong> Memudahkan Anda melihat apakah rute transaksi untuk merchant tersebut sedang <span class="text-success">Aktif</span> atau <span class="text-danger">Tidak Aktif</span>.</li>
                        <li class="mb-3"><i class="fas fa-edit text-success mr-2"></i><strong>Edit Mapping:</strong> Klik tombol hijau gelap di atas tabel untuk mengubah konfigurasi yang sudah ada.</li>
                        <li class="mb-3"><i class="fas fa-plus-circle text-success mr-2"></i><strong>Add Mapping:</strong> Klik tombol hijau terang untuk membuat konfigurasi jalur pendaftaran baru untuk merchant.</li>
                        <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Menu Aksi (Action):</strong> Klik ikon tiga titik di ujung kanan setiap baris data untuk melakukan <strong>Edit</strong> atau <strong>Delete</strong> pada pemetaan spesifik tersebut.</li>
                    </ul>
                </div>
            </div>
            
        </div>

        <!-- Step 4 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
                <h3 class="hc-step-title">Mengisi Formulir Mapping</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Formulir Add Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>Berikut adalah pemetaan *field* yang wajib diisi untuk membuat konfigurasi cash-out eksternal baru:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Pemilihan Merchant:</strong> Pilih merchant spesifik yang ingin Anda konfigurasi. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Anda tidak dapat memproses form tanpa memilih merchant yang valid.</em></li>
                        <li class="mb-3"><strong>Konfigurasi Channel:</strong> Pilih <strong>Channel Group</strong> (mis. Bi-Fast, RTGS) dan tentukan <strong>External ID Default</strong> untuk menetapkan provider penyedia layanan. Pilih <strong>Specific Channel ID</strong> jika diwajibkan oleh provider.</li>
                        <li class="mb-3"><strong>Struktur Biaya & Limit:</strong> Tentukan Tipe Biaya (Fixed atau Percentage) dan atur Limit Transaksi Minimum serta Maksimum. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Sistem akan memverifikasi secara ketat bahwa limit Minimum tidak boleh lebih besar dari limit Maksimum.</em></li>
                        <li class="mb-3"><strong>Konfigurasi Status:</strong> Atur status pemetaan menjadi <strong>Aktif</strong> untuk langsung menyalakannya, atau <strong>Tidak Aktif</strong> untuk menyimpannya sebagai draf.</li>
                        <li class="mb-3"><strong>Review & Simpan:</strong> Periksa kembali konfigurasi Anda dengan panduan di panel kanan. Setelah yakin, klik tombol <strong>Save</strong> untuk menyimpan rute ke sistem.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">5</div>
                <h3 class="hc-step-title">Fitur Global Update</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/bulk_update.png') ?>" alt="Formulir Bulk Update" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>Fitur Global Update didesain untuk memigrasikan *traffic* secara cepat dari satu provider ke provider lain. Berikut adalah penjelasan detail dari fungsi yang tersedia:</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Memulai Update:</strong> Klik tombol <strong>Global Update</strong> di bagian atas tabel utama ketika Anda perlu memigrasikan *traffic* secara cepat (misal saat *provider* sedang *downtime*).</li>
                        <li class="mb-3"><strong>Tentukan Lingkup Update (Scope):</strong> Pilih <strong>All Mapping</strong> untuk mengubah *provider* bagi semua *merchant* di grup tersebut, atau <strong>Specific Merchant</strong> untuk membatasi migrasi hanya pada satu klien saja.</li>
                        <li class="mb-3"><strong>Konfigurasi Saat Ini (Filter):</strong> Identifikasi jalur transaksi yang ingin dipindahkan dengan memilih <strong>Channel Group</strong> dan <strong>External ID Default</strong> saat ini yang sedang bermasalah.</li>
                        <li class="mb-3"><strong>Konfigurasi Baru:</strong> Tentukan *provider* tujuan yang baru dengan memilih <strong>External ID Default</strong> pengganti ke mana *traffic* akan dialihkan.</li>
                        <li class="mb-3"><strong>Konfirmasi & Eksekusi:</strong> Tinjau ulang pengaturan migrasi Anda dengan teliti. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Sistem akan memberi peringatan jika konfigurasi baru bentrok dengan pengaturan limit yang ada. Perubahan akan langsung aktif setelah dikonfirmasi.</em></li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">6</div>
                <h3 class="hc-step-title">Edit Konfigurasi Mapping</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Edit Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>Anda dapat memperbarui pemetaan yang sudah ada jika terdapat perubahan pada struktur biaya atau jika Anda perlu mengubah statusnya secara manual.</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Buka Menu Aksi:</strong> Klik ikon tiga titik (Menu Aksi) di ujung kanan baris data yang ingin Anda ubah.</li>
                        <li class="mb-3"><strong>Pilih Edit:</strong> Pilih opsi <strong>Edit</strong> dari menu *dropdown* yang muncul untuk membuka formulir.</li>
                        <li class="mb-3"><strong>Lakukan Perubahan:</strong> Perbarui kolom yang diperlukan seperti Tipe Biaya (Fee), Limit, atau Status.</li>
                        <li class="mb-3"><strong>Simpan Perubahan:</strong> Klik <strong>Save Changes</strong> untuk menerapkan perubahan secara langsung ke dalam sistem.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="row hc-step-row align-items-center">
            <div class="col-lg-12">
                <div class="hc-step-number">7</div>
                <h3 class="hc-step-title">Menghapus Konfigurasi Mapping</h3>
                <div class="mac-window mb-4">
                    <div class="mac-body">
                        <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Delete Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                    </div>
                </div>
                <p>Jika sebuah konfigurasi sudah tidak diperlukan atau salah dibuat, Anda dapat menghapusnya secara permanen dari sistem.</p>
                <div class="pl-4 border-left border-success ml-2 mt-3">
                    <ol class="hc-step-desc mb-0">
                        <li class="mb-3"><strong>Buka Menu Aksi:</strong> Klik ikon tiga titik (Menu Aksi) di ujung kanan baris data yang terkait.</li>
                        <li class="mb-3"><strong>Pilih Delete:</strong> Pilih opsi <strong>Delete</strong> dari menu *dropdown*.</li>
                        <li class="mb-3"><strong>Konfirmasi:</strong> Sebuah *pop-up* konfirmasi akan muncul. Pastikan Anda menghapus konfigurasi yang benar. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Peringatan: Tindakan ini tidak dapat dibatalkan.</em></li>
                        <li class="mb-3"><strong>Finalisasi:</strong> Klik <strong>Confirm Delete</strong> untuk menghapus pemetaan secara permanen.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- 3. Technical Reference -->
        <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
        <div class="table-responsive mb-5">
            <table class="table table-bordered hc-ref-table bg-white">
                <thead>
                    <tr>
                        <th width="25%">Nama Kolom</th>
                        <th>Deskripsi Teknis & Logika Bisnis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Merchant</strong></td>
                        <td>Akun klien/merchant yang akan melakukan transaksi cash-out.</td>
                    </tr>
                    <tr>
                        <td><strong>Channel Group</strong></td>
                        <td>Kategori dari metode pembayaran (contoh: <code>bifast</code>, <code>rtgs</code>, <code>online_transfer</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>Channel ID</strong></td>
                        <td>Kode bank spesifik atau pengenal channel yang dibutuhkan oleh aggregator (contoh: <code>bifast_bca</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>External ID Default (Provider)</strong></td>
                        <td>Penyedia pihak ketiga atau aggregator yang akan memproses transaksi tersebut (contoh: <code>quantum</code>, <code>paylabs</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>Fee Type</strong></td>
                        <td>Menentukan cara merchant dikenakan biaya. Bisa berupa <strong>Fixed</strong> (biaya tetap) atau <strong>Percentage</strong> (dihitung berdasarkan persentase nominal transaksi).</td>
                    </tr>
                    <tr>
                        <td><strong>Amount Min / Max</strong></td>
                        <td>Nilai transaksi minimum dan maksimum yang diizinkan untuk konfigurasi routing ini. Transaksi di luar limit ini akan otomatis ditolak.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 4. FAQ / Troubleshooting -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-circle text-danger"></i> 
                <span>Mengapa saya tidak bisa menghapus (delete) konfigurasi mapping? (Error 1451)</span>
            </div>
            <p class="hc-faq-a">Karena adanya konstrain relasi database, Anda tidak dapat menghapus mapping yang sudah pernah digunakan pada transaksi sebelumnya (untuk menjaga integritas riwayat data). Sebagai gantinya, klik Edit dan ubah Status menjadi <strong>Not Active</strong>.</p>
        </div>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa dropdown Channel ID kosong saat saya ingin menambah mapping?</span>
            </div>
            <p class="hc-faq-a">Dropdown Channel ID dimuat secara dinamis berdasarkan <strong>Channel Group</strong> yang Anda pilih. Anda harus memilih Channel Group terlebih dahulu agar sistem dapat memuat daftar Channel ID yang sesuai.</p>
        </div>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Apa yang terjadi jika saya mengatur status mapping menjadi Tidak Aktif (Inactive)?</span>
            </div>
            <p class="hc-faq-a">Jika mapping diatur menjadi Tidak Aktif, sistem tidak akan mengarahkan transaksi baru melalui konfigurasi ini. Transaksi yang masih tertunda (pending) mungkin tetap diproses tergantung pada provider, tetapi permintaan baru akan ditolak atau dialihkan ke provider aktif lain jika tersedia.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Bisakah saya memiliki beberapa mapping aktif untuk Merchant dan Channel Group yang sama?</span>
            </div>
            <p class="hc-faq-a">Bisa, namun sistem akan menggunakan logika prioritas (routing priority) untuk menentukan provider mana yang digunakan lebih dulu. Sangat disarankan untuk hanya memiliki satu provider utama yang aktif untuk menghindari konflik, kecuali sistem fallback sudah diatur secara spesifik.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-info-circle text-primary"></i> 
                <span>Apa perbedaan antara Tipe Biaya (Fee Type) Fixed dan Percentage?</span>
            </div>
            <p class="hc-faq-a">Biaya <strong>Fixed</strong> menerapkan tarif tetap (misal: Rp 4.000) terlepas dari nominal transaksi. Biaya <strong>Percentage</strong> menghitung potongan berdasarkan persentase nilai transaksi (misal: 1,5% dari total). Pastikan memilih tipe yang sesuai dengan kesepakatan provider Anda.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-exclamation-triangle text-warning"></i> 
                <span>Mengapa Global Update saya gagal memigrasikan traffic?</span>
            </div>
            <p class="hc-faq-a">Global Update bisa gagal jika provider target (Konfigurasi Baru) tidak mendukung Channel ID tertentu, atau jika pengaturan limit pada provider target berbenturan dengan konfigurasi merchant Anda saat ini. Selalu periksa peringatan validasi sebelum mengonfirmasi.</p>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bagaimana cara mengetahui External ID Default mana yang harus dipilih?</span>
            </div>
            <p class="hc-faq-a">External ID Default merepresentasikan pihak ketiga, agregator, atau bank (contoh: Quantum, Paylabs). Anda harus memilih provider yang sudah menjalin kerja sama aktif dengan Anda dan mendukung Channel Group yang dipilih. Jika ragu, periksa dokumentasi teknis atau hubungi administrator Anda.</p>
        </div>

    </div>

</div>
</div>
