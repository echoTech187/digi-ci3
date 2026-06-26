<div id="module-ug-merchant-cashin" class="hc-doc-section">
    <div class="ug-module-content">
        <!-- EN CONTENT -->
        <div class="lang-content lang-en" style="display:block;">

            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Overview</h4>
                <p class="mb-0">The <strong>Cash-In Fee Settings</strong> module allows administrators to define custom payment gateway fees, transaction size limits, and settlement intervals exclusively for individual merchants. This robust system overrides global defaults and supports bulk assignments, granting granular control over inbound transaction commercials to accommodate complex B2B agreements.</p>
            </div>

            <!-- 2. Visual Step-by-Step Walkthrough -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
            
            <!-- Step 1 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <div class="hc-step-number">1</div>
                    <h3 class="hc-step-title">Access the Module</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">From the main dashboard, navigate to the <strong>Merchant Management</strong> menu on the left sidebar.</li>
                            <li class="mb-2">Locate the specific merchant you wish to configure in the data table.</li>
                            <li class="mb-2">Click the (<i class="fas fa-ellipsis-v"></i>) on the right side of the row and select <strong>Cashin Fee Settings</strong> to launch the interface.</li>
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
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-8">
                    <div class="hc-step-number">2</div>
                    <h3 class="hc-step-title">Use Advanced Filters</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Click the <strong>Filters</strong> button located at the top right of the data table to toggle the advanced filtering panel.</li>
                            <li class="mb-2"><strong>Channel Group:</strong> Isolate configurations by payment method families (e.g., Virtual Account, Retail, E-Wallet).</li>
                            <li class="mb-2"><strong>Provider / External Default:</strong> View configurations routed through a specific upstream aggregator or payment provider.</li>
                            <li class="mb-2"><strong>Channel ID:</strong> Pinpoint exact bank endpoints or specific payment channels.</li>
                            <li class="mb-2"><strong>Status:</strong> Filter to see only Active configurations or those that are currently Inactive.</li>
                            <li class="mb-2">Click the <strong>Apply Filter</strong> button to refine the data table results dynamically.</li>
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
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12 order-2 order-xl-1">
                    <div class="hc-step-number">3</div>
                    <h3 class="hc-step-title">View Fee Configuration Data</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Data Table" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <p class="hc-step-desc mb-3">The main table gives you a comprehensive overview of all custom inbound fee configurations for the selected merchant. Here is a detailed breakdown of the key elements and functionalities:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ul class="hc-step-desc mb-0 list-unstyled">
                            <li class="mb-3"><i class="fas fa-search text-success mr-2"></i><strong>Global Search:</strong> Use the search bar at the top right of the table. You can instantly filter the view by typing in the Channel Group, specific Channel ID, or Provider Name to locate a particular fee structure without scrolling.</li>
                            <li class="mb-3"><i class="fas fa-columns text-success mr-2"></i><strong>Data Columns Breakdown:</strong> The table provides a complete anatomy of each fee setup. <strong>Channel Config</strong> displays the routing path, <strong>Fee Details</strong> shows whether the cost is fixed (Flat) or dynamic (Percentage), <strong>Interval</strong> indicates settlement periods, and <strong>Limits (Min/Max)</strong> define the transaction boundaries where the fee applies.</li>
                            <li class="mb-3"><i class="fas fa-toggle-on text-success mr-2"></i><strong>Visual Status Indicator:</strong> Each row features a status badge. A green <span class="text-success">Active</span> badge means the fee override is actively calculating costs for new transactions, while a <span class="text-danger">Not Active</span> badge means the system ignores this custom setting and defaults back to the global fee.</li>
                            <li class="mb-3"><i class="fas fa-layer-group text-success mr-2"></i><strong>Bulk Add Functionality:</strong> Click the 'Bulk Add' button to rapidly assign the exact same fee structure and limits to multiple channels within a group (e.g., all Virtual Accounts) in a single operation, saving significant administrative time.</li>
                            <li class="mb-3"><i class="fas fa-plus text-success mr-2"></i><strong>Add Channel (Individual):</strong> Use the primary green button when you need to configure a highly customized fee rule for one specific payment channel (e.g., exclusively modifying the fee for BCA Virtual Account).</li>
                            <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Row Action Menu:</strong> The three vertical dots (<i class="fas fa-ellipsis-v"></i>) on the far right provide individual row controls. Select <strong>Edit Setting</strong> to modify the existing fee values or limits, or choose <strong>Delete</strong> to permanently remove this custom fee configuration from the merchant.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Add New Fee Configuration</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Add Mapping Form" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>To establish a custom fee for a new inbound channel, click "Add Channel" and carefully define the following properties:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Channel Selection:</strong> Select the <strong>Channel Group</strong> and <strong>External ID Default</strong>. This will unlock the <strong>Specific Channel ID</strong> dropdown. Select the exact channel you wish to configure. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: The system enforces a strict Unique constraint (Error 1062) to ensure a merchant does not have duplicate active fees for the same Channel ID.</em></li>
                            <li class="mb-3"><strong>Fee Structure:</strong> Choose a <strong>Fee Type</strong>: <code>Fixed</code> (flat Rupiah deduction), <code>Percentage</code> (dynamic deduction based on volume), or <code>Both</code>. Enter the corresponding nominal values precisely.</li>
                            <li class="mb-3"><strong>Settlement Interval:</strong> Define the number of days the funds will be held in a Pending state before transitioning to Available Balance. Enter <code>0</code> for instant real-time clearing (H+0).</li>
                            <li class="mb-3"><strong>Transaction Limits:</strong> Define the absolute <strong>Amount Min</strong> and <strong>Amount Max</strong>. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: Inbound transaction requests that fall outside this designated boundary will be categorically rejected. The Minimum amount cannot exceed the Maximum amount.</em></li>
                            <li class="mb-3"><strong>Status Configuration:</strong> Set the configuration to <strong>Active</strong> and hit <strong>Save Configuration</strong>. The fee rules take effect immediately for all subsequent transactions.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Bulk Add Capabilities</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Bulk Update Form" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>When onboarding a merchant who requires a flat fee structure across an entire category of payment methods (e.g., a flat 1.5% for all E-Wallets), utilize the Bulk Add modal to save time and prevent manual entry errors.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Triggering the Tool:</strong> Click the <strong>Bulk Add</strong> button located alongside the advanced filters.</li>
                            <li class="mb-3"><strong>Define Target Scope:</strong> Select the target <strong>Cashin Channel Group</strong> and the target <strong>External ID Default</strong>. The system will prepare to apply identical settings to every single channel within this intersection.</li>
                            <li class="mb-3"><strong>Uniform Fee & Limits:</strong> Input the universal Fee Type, Settlement Interval, and Transaction Limits that will be replicated across the targeted channels.</li>
                            <li class="mb-3"><strong>Duplicate Protection Mechanism:</strong> Before execution, the system scans the merchant's current fee configurations. <br><em class="text-primary small"><i class="fas fa-shield-alt"></i> If a channel within the group already possesses a custom fee setting, the Bulk Add operation will silently bypass it to preserve its existing specific rules. It only provisions unconfigured channels.</em></li>
                            <li class="mb-3"><strong>Execution:</strong> Click <strong>Apply Bulk Settings</strong>. The batch operation is processed securely within a single database transaction.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">6</div>
                    <h3 class="hc-step-title">Edit Fee Configuration</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Edit Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Commercial agreements change over time. You can seamlessly renegotiate and update existing fee structures without interrupting service availability.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Open Action Menu:</strong> Locate the configuration row you wish to modify and click the (<i class="fas fa-ellipsis-v"></i>) on the far right.</li>
                            <li class="mb-3"><strong>Select Edit:</strong> Choose <strong>Edit Setting</strong> from the dropdown menu to open the modification modal.</li>
                            <li class="mb-3"><strong>Make Adjustments:</strong> The form will be pre-populated with the current rules. You can alter the Fee Type, adjust the settlement duration, or widen/restrict the transaction limits. <em class="text-muted small">Note: The target Channel ID itself is locked and cannot be changed during an edit.</em></li>
                            <li class="mb-3"><strong>Save Updates:</strong> Click <strong>Save Configuration</strong>. The revised logic will apply exclusively to newly generated invoices; old pending transactions retain their historical fee data.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">7</div>
                    <h3 class="hc-step-title">Delete Fee Configuration</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/delete_mapping.png') ?>" alt="Delete Mapping" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>If a merchant's custom fee privilege is revoked or was created erroneously, it can be permanently deleted, forcing the merchant to fall back to the system's global default rates.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Open Action Menu:</strong> Click the (<i class="fas fa-ellipsis-v"></i>) on the relevant configuration row.</li>
                            <li class="mb-3"><strong>Select Delete:</strong> Choose the red <strong>Delete</strong> option from the dropdown menu.</li>
                            <li class="mb-3"><strong>Confirmation:</strong> A confirmation prompt will appear asking you to verify the removal of the override. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Warning: Deletion is permanent and removes the custom commercial agreement from the system.</em></li>
                            <li class="mb-3"><strong>Fallback Activation:</strong> Click <strong>Confirm Delete</strong>. The merchant will instantly revert to the standard global fee structure for that specific channel.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- 3. Technical Reference -->
            <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
            <div class="table-responsive">
                <table class="table table-bordered hc-ref-table bg-white">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Parameter</th>
                            <th>Description & Validation Mechanism</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Fee Type</strong></td>
                            <td>Determines the core mathematical logic applied to each incoming transaction. You can select <code>Fixed</code> to impose a static, flat-rate deduction regardless of the transaction volume, <code>Percentage</code> to calculate a dynamic, volume-dependent fee, or <code>Both</code> to strictly enforce a combination of a base flat rate plus a volumetric deduction.</td>
                        </tr>
                        <tr>
                            <td><strong>Settlement Interval</strong></td>
                            <td>Defines the strict latency period (measured in days) during which incoming funds are deliberately held in a Pending state before being safely transferred to the Merchant's Available Balance. Injecting a value of <code>0</code> commands the system to execute an instant, real-time clearing process (H+0).</td>
                        </tr>
                        <tr>
                            <td><strong>Amount Min & Max</strong></td>
                            <td>Establishes the absolute security boundaries for inbound transaction amounts. Any invoice generation request that falls outside of this strict volumetric zone will be categorically intercepted and sabotaged by the upstream payment gateway to prevent unauthorized processing.</td>
                        </tr>
                        <tr>
                            <td><strong>Duplicate Entry Constraint</strong></td>
                            <td>A rigid database-level security policy ensuring each merchant can only possess a maximum of one active fee configuration per specific Channel ID. Any attempt to pile up duplicate settings will immediately trigger a strict constraint violation (Error 1062).</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. FAQ / Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-primary"></i> 
                    <span>What happens to pending transactions when I update a fee?</span>
                </div>
                <p class="hc-faq-a">Fees are locked securely at the exact moment an invoice or transaction request is generated. Editing the fee structure in this module will only affect brand-new transactions. Old pending invoices will retain their original fee rules regardless of subsequent updates.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-primary"></i> 
                    <span>Can I configure a channel to have zero fees?</span>
                </div>
                <p class="hc-faq-a">Yes. Set the Fee Type to Fixed or Percentage, and enter 0 as the nominal value. The merchant will not be charged anything for inbound transactions. However, standard upstream provider costs may still apply and be billed to your platform's master account.</p>
            </div>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Why do I get a "Duplicate Entry (1062)" error when adding a channel?</span>
                </div>
                <p class="hc-faq-a">This database-level error occurs because you are attempting to use "Add Channel" for a channel ID that is already configured for this merchant. If you want to modify the existing channel's fee, you must use the <strong>Edit Setting</strong> button on its specific row instead of creating a new entry.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Why does the Bulk Add operation skip certain channels?</span>
                </div>
                <p class="hc-faq-a">Bulk Add is intentionally designed with <strong>Duplicate Protection</strong>. If you run a Bulk Add on a group of 10 channels, but 8 of them already have their own highly specific custom fees configured, the Bulk Add operation will preserve those 8 and only apply the new settings to the 2 remaining unconfigured channels.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>How do I delete a fee so the merchant returns to the global settings?</span>
                </div>
                <p class="hc-faq-a">Locate the specific channel configuration in the table, click the (<i class="fas fa-ellipsis-v"></i>), and select <strong>Delete</strong>. Once the custom fee override is removed, the merchant will instantly fall back to the platform's global default rates for that channel.</p>
            </div>

        </div>

        <!-- ID CONTENT -->
        <div class="lang-content lang-id" style="display:none;">

            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Ikhtisar (Overview)</h4>
                <p class="mb-0">Modul <strong>Cash-In Fee Settings</strong> memungkinkan administrator untuk mendefinisikan biaya layanan *payment gateway* (MDR), rentang limit transaksi, serta interval penyelesaian dana (settlement) secara eksklusif untuk tiap merchant. Sistem tangguh ini menimpa pengaturan standar global dan mendukung pengaturan massal (Bulk), memberikan kontrol penuh atas struktur tarif (Fixed, Percentage, Both) untuk memfasilitasi ragam kesepakatan komersial B2B.</p>
            </div>

            <!-- 2. Visual Step-by-Step Walkthrough -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Langkah-demi-Langkah</h4>
            
            <!-- Step 1 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <div class="hc-step-number">1</div>
                    <h3 class="hc-step-title">Mengakses Modul</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Dari dasbor utama, navigasikan kursor ke menu <strong>Merchant Management</strong> di bilah sisi kiri.</li>
                            <li class="mb-2">Cari dan temukan merchant yang dituju pada tabel data utama.</li>
                            <li class="mb-2">Klik menu aksi (titik tiga <strong>⋮</strong>) di ujung kanan baris merchant tersebut, lalu pilih <strong>Cashin Fee Settings</strong>.</li>
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
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-8">
                    <div class="hc-step-number">2</div>
                    <h3 class="hc-step-title">Gunakan Filter Tingkat Lanjut</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Klik tombol <strong>Filters</strong> yang terletak di pojok kanan atas tabel data untuk memunculkan panel filter lanjutan.</li>
                            <li class="mb-2"><strong>Channel Group:</strong> Sortir konfigurasi berdasarkan rumpun metode pembayaran (mis. Virtual Account, Retail, E-Wallet).</li>
                            <li class="mb-2"><strong>Provider / External Default:</strong> Tampilkan konfigurasi yang dirutekan melalui *aggregator* atau penyedia layanan tertentu.</li>
                            <li class="mb-2"><strong>Channel ID:</strong> Persempit pencarian hingga ke kode bank atau kanal pembayaran yang paling spesifik.</li>
                            <li class="mb-2"><strong>Status:</strong> Filter tabel untuk hanya menampilkan konfigurasi yang Aktif atau Tidak Aktif.</li>
                            <li class="mb-2">Klik tombol <strong>Apply Filter</strong> untuk menyaring data tabel secara dinamis.</li>
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
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12 order-2 order-xl-1">
                    <div class="hc-step-number">3</div>
                    <h3 class="hc-step-title">Memahami Tabel Konfigurasi Biaya</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Tabel Data" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <p class="hc-step-desc mb-3">Tabel utama memberikan gambaran komprehensif mengenai seluruh konfigurasi biaya masuk khusus untuk merchant terpilih. Berikut adalah rincian detail dari elemen dan fungsionalitas utama:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ul class="hc-step-desc mb-0 list-unstyled">
                            <li class="mb-3"><i class="fas fa-search text-success mr-2"></i><strong>Pencarian Global (Global Search):</strong> Gunakan kotak pencarian di kanan atas tabel. Anda dapat dengan instan menyaring tampilan dengan mengetikkan Channel Group, spesifik Channel ID, atau Nama Provider untuk menemukan struktur biaya tertentu tanpa perlu menggulir halaman.</li>
                            <li class="mb-3"><i class="fas fa-columns text-success mr-2"></i><strong>Kolom Data Utama:</strong> Tabel ini memberikan anatomi lengkap setiap pengaturan biaya. <strong>Channel Config</strong> menampilkan rute pembayaran, <strong>Fee Details</strong> menunjukkan apakah biayanya bersifat tetap (Flat) atau dinamis (Persentase), <strong>Interval</strong> menandakan periode settlement, dan <strong>Limits (Min/Max)</strong> membatasi nominal transaksi di mana aturan biaya ini berlaku.</li>
                            <li class="mb-3"><i class="fas fa-toggle-on text-success mr-2"></i><strong>Indikator Status Visual:</strong> Setiap baris memiliki label status visual. Label hijau <span class="text-success">Active</span> berarti biaya kustom ini secara aktif menghitung potongan untuk transaksi baru, sementara label <span class="text-danger">Not Active</span> berarti sistem mengabaikan pengaturan ini dan mengembalikan perhitungan ke biaya global bawaan sistem.</li>
                            <li class="mb-3"><i class="fas fa-layer-group text-success mr-2"></i><strong>Fungsionalitas Bulk Add:</strong> Klik tombol 'Bulk Add' untuk mengaplikasikan struktur biaya dan limit yang persis sama ke beberapa saluran sekaligus di dalam satu grup (contoh: seluruh metode Virtual Account) dalam satu kali operasi, yang sangat menghemat waktu administrasi.</li>
                            <li class="mb-3"><i class="fas fa-plus text-success mr-2"></i><strong>Tambah Channel Individual:</strong> Gunakan tombol hijau utama apabila Anda perlu membuat konfigurasi biaya yang sangat khusus hanya untuk satu rute pembayaran tunggal (misal: secara eksklusif memodifikasi biaya untuk Virtual Account BCA).</li>
                            <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Menu Aksi Baris (Action Menu):</strong> Ikon (<i class="fas fa-ellipsis-v"></i>) di ujung kanan baris memberikan kontrol spesifik untuk data tersebut. Pilih <strong>Edit Setting</strong> untuk memodifikasi nilai biaya atau limit yang ada, atau pilih <strong>Delete</strong> untuk menghapus aturan biaya kustom ini secara permanen dari akun merchant.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Menambah Konfigurasi Biaya Baru</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Formulir Tambah Biaya" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Untuk memberlakukan tarif kustom bagi kanal baru, klik "Add Channel" dan tetapkan parameter berikut dengan seksama:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Pemilihan Kanal:</strong> Pilih <strong>Channel Group</strong> beserta <strong>External ID Default</strong>-nya. Tindakan ini akan membuka kunci opsi <strong>Specific Channel ID</strong>. Pilih kanal yang benar-benar ingin Anda atur. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Sistem menerapkan konstrain unik (Error 1062) secara ketat untuk memastikan merchant tidak memiliki biaya ganda yang saling bertumpuk pada satu Channel ID.</em></li>
                            <li class="mb-3"><strong>Struktur Biaya:</strong> Tentukan <strong>Fee Type</strong>: <code>Fixed</code> (potongan Rupiah murni), <code>Percentage</code> (potongan dinamis berbasis volume), atau <code>Both</code> (membebankan keduanya). Masukkan nominalnya secara akurat.</li>
                            <li class="mb-3"><strong>Interval Settlement:</strong> Tetapkan jumlah hari dana akan tertahan (Pending) sebelum beralih ke Saldo Tersedia (Available Balance). Masukkan angka <code>0</code> untuk proses pencairan seketika (H+0).</li>
                            <li class="mb-3"><strong>Limit Transaksi:</strong> Tentukan batas mutlak <strong>Amount Min</strong> dan <strong>Amount Max</strong>. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: *Invoice* bernilai di luar rentang ini akan otomatis ditolak (*rejected*). Batas minimum jelas tidak boleh melebihi batas maksimum.</em></li>
                            <li class="mb-3"><strong>Konfigurasi Status:</strong> Ubah status menjadi <strong>Active</strong> dan tekan <strong>Save Configuration</strong>. Segala transaksi yang masuk sedetik setelahnya akan langsung tunduk pada aturan baru ini.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Fungsionalitas Bulk Add (Massal)</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Formulir Bulk Update" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Saat melayani *merchant* yang menghendaki keseragaman struktur biaya pada satu kategori pembayaran utuh (misal: flat 1,5% untuk seluruh E-Wallet), maksimalkan fungsi Bulk Add guna menghemat waktu dan meminimalisir salah input manual.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Akses Fitur:</strong> Klik tombol <strong>Bulk Add</strong> yang berdampingan dengan fungsi filter tingkat lanjut.</li>
                            <li class="mb-3"><strong>Tentukan Target Lingkup:</strong> Pilih <strong>Cashin Channel Group</strong> tujuan dan <strong>External ID Default</strong>-nya. Sistem seketika bersiap memproyeksikan pengaturan yang sama rata kepada seluruh kanal di bawahnya.</li>
                            <li class="mb-3"><strong>Keseragaman Biaya & Limit:</strong> Isikan Tipe Biaya, Interval Settlement, dan Limit Transaksi seragam yang hendak direplikasi ke seluruh kanal target.</li>
                            <li class="mb-3"><strong>Mekanisme Proteksi Duplikat:</strong> Sebelum eksekusi, sistem memindai konfigurasi biaya *merchant* saat ini. <br><em class="text-primary small"><i class="fas fa-shield-alt"></i> Bila ada satu kanal yang sebelumnya telah memiliki tarif khusus, operasi Bulk Add akan dengan cerdas melewatinya secara diam-diam. Fitur ini hanya mendaftarkan kanal-kanal yang sama sekali belum tersentuh.</em></li>
                            <li class="mb-3"><strong>Eksekusi:</strong> Tekan <strong>Apply Bulk Settings</strong>. Pemrosesan massal dilakukan secara aman dalam satu bingkai transaksi *database*.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">6</div>
                    <h3 class="hc-step-title">Mengedit Konfigurasi Biaya</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Edit Konfigurasi" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Perjanjian komersial lumrah berubah seiring waktu. Anda dapat melakukan negosiasi ulang dan memutakhirkan struktur tarif secara mulus tanpa membuat layanan terputus.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Buka Menu Aksi:</strong> Sorot baris konfigurasi yang hendak Anda ubah, lalu klik ikon (<i class="fas fa-ellipsis-v"></i>) di palung sisi kanannya.</li>
                            <li class="mb-3"><strong>Pilih Edit:</strong> Tekan opsi <strong>Edit Setting</strong> dari *dropdown* guna membuka modal formulir modifikasi.</li>
                            <li class="mb-3"><strong>Lakukan Penyesuaian:</strong> Formulir akan berisi pedoman biaya terakhir. Anda bebas menukar Tipe Fee, mengatur durasi settlement, atau melonggarkan/menyempitkan batas transaksi. <em class="text-muted small">Catatan: Inti Channel ID sudah terkunci dan tidak bisa ditukar saat proses edit.</em></li>
                            <li class="mb-3"><strong>Simpan Perubahan:</strong> Klik <strong>Save Configuration</strong>. Logika baru bakal menyasar *invoice* terbitan anyar; sementara *invoice* lampau yang masih *pending* tetap menggenggam data *fee* historisnya.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">7</div>
                    <h3 class="hc-step-title">Menghapus Konfigurasi Biaya</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/delete_mapping.png') ?>" alt="Hapus Konfigurasi" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Apabila hak istimewa *merchant* dicabut atau sewaktu-waktu data keliru diinput, konfigurasi dapat dibongkar total, memaksa *merchant* untuk kembali merujuk ke rasio tarif acuan sistem (*global default*).</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Buka Menu Aksi:</strong> Klik ikon (<i class="fas fa-ellipsis-v"></i>) pada deret konfigurasi yang bersangkutan.</li>
                            <li class="mb-3"><strong>Pilih Delete:</strong> Tekan opsi merah berlabel <strong>Delete</strong> pada menu *dropdown*.</li>
                            <li class="mb-3"><strong>Konfirmasi:</strong> Notifikasi peringatan akan muncul meminta Anda meyakinkan niat penghapusan pengaturan tersebut. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Peringatan: Proses ini bersifat permanen dan memusnahkan kesepakatan spesifik tersebut dari sirkulasi.</em></li>
                            <li class="mb-3"><strong>Aktivasi Standar Baku (Fallback):</strong> Tekan <strong>Confirm Delete</strong>. Sistem otomatis menyingkirkan tarif tersebut dan seketika menjatuhkan perlindungan *fallback* tarif global kepada *merchant*.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- 3. Technical Reference -->
            <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
            <div class="table-responsive">
                <table class="table table-bordered hc-ref-table bg-white">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Parameter</th>
                            <th>Deskripsi & Validasi Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Fee Type</strong></td>
                            <td>Mendikte rincian rumus logika matematis inti yang akan diterapkan pada setiap transaksi masuk. Anda dapat memilih <code>Fixed</code> untuk membebankan tarif statis murni yang mengabaikan volume transaksi, <code>Percentage</code> untuk menghitung potongan dinamis yang bergantung penuh pada volume, atau <code>Both</code> untuk mengeksekusi kombinasi ketat antara tarif dasar (flat) yang ditambah dengan irisan volumetrik.</td>
                        </tr>
                        <tr>
                            <td><strong>Settlement Interval</strong></td>
                            <td>Menentukan fase latensi ketat (dalam wujud hitungan Hari) di mana dana yang hinggap akan sengaja ditahan di beranda Pending sebelum akhirnya dipindah secara aman ke pangkuan Available Balance Merchant. Menyuntikkan angka <code>0</code> akan memerintahkan sistem untuk mengeksekusi proses pencairan mutlak secara real-time (Seketika/H+0).</td>
                        </tr>
                        <tr>
                            <td><strong>Amount Min & Max</strong></td>
                            <td>Menciptakan garis batas mutlak keamanan untuk rentang nominal transaksi masuk. Setiap permintaan pencetakan *invoice* dengan jumlah yang melanggar dan berada di luar zona perlindungan tersebut akan langsung dicegat dan disabotase secara sepihak oleh hulu *payment gateway* guna mencegah pemrosesan ilegal.</td>
                        </tr>
                        <tr>
                            <td><strong>Duplicate Entry Constraint</strong></td>
                            <td>Kebijakan keamanan tingkat *database* yang kaku, menjamin bahwa tiap *merchant* hanya berhak memiliki maksimal satu susunan konfigurasi aktif per spesifik Channel ID. Segala bentuk percobaan nekat untuk menambah tumpukan data ganda akan secara instan memicu letupan pelanggaran konstrain sistem (*Error* 1062).</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. FAQ / Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-primary"></i> 
                    <span>Apa imbasnya bagi transaksi pending jika saya mengubah fee?</span>
                </div>
                <p class="hc-faq-a">Biaya admin dikunci dengan aman pada sepersekian detik permohonan pembayaran (invoice) diterbitkan. Penyuntingan tarif di sini murni hanya berdampak pada sirkulasi transaksi mutakhir. Invoice tertunda (pending) di ruang tunggu tetap tunduk pada tarif purbakalanya.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-primary"></i> 
                    <span>Apakah mungkin membuat biaya layanan menjadi nol rupiah?</span>
                </div>
                <p class="hc-faq-a">Sangat bisa. Anda cukup memilah Fee Type *Fixed* atau *Percentage* lalu menabung angka 0 di kolom nominal. Merchant luput sepenuhnya dari jerat biaya *cashin*. Meski demikian, tagihan asli dari hulu *provider* tetap mampir merongrong akun *master* platform Anda.</p>
            </div>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Mengapa muncul notifikasi pesan error "Duplicate Entry (1062)" saat menambah kanal?</span>
                </div>
                <p class="hc-faq-a">Letupan *error* tingkat *database* ini menandakan kepanikan sistem saat Anda bersikeras menekan "Add Channel" pada wujud kanal yang sejatinya sudah hadir di dalam pelukan tabel. Jika niat tulus Anda sekadar merevisi, tolong gunakan tombol <strong>Edit Setting</strong> pada jajaran baris bersangkutan alih-alih melempar entri data baru.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Mengapa sebagian kanal seakan diremehkan (di-skip) sewaktu proses Bulk Add?</span>
                </div>
                <p class="hc-faq-a">Jangan panik, sistem Bulk Add memang dianugerahi insting <strong>Duplicate Protection</strong>. Misalkan Anda menyerbu grup berisi 10 kanal, namun ternyata 8 di antaranya sudah berbekal zirah biaya khusus; operasi Bulk Add tak akan menjamah ke-8 kanal itu. Ia hanya sudi memakaikan pengaturan baru pada 2 sisa kanal telanjang yang benar-benar belum dikonfigurasi.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>Bagaimana cara mengakhiri riwayat biaya khusus merujuk merchant kembali ke naungan tarif global?</span>
                </div>
                <p class="hc-faq-a">Selidiki baris persembunyian kanal tersebut di dalam tabel, sentuh ikon (<i class="fas fa-ellipsis-v"></i>), dan tekan opsi beringas <strong>Delete</strong>. Sesudah *override* musnah, *merchant* itu takkan lagi menikmati keistimewaan dan seketika membaur (fallback) ke pedoman tarif wajar sedunia (*global default rates*).</p>
            </div>

        </div>

    </div>
</div>
