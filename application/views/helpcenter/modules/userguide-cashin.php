<div id="module-ug-cashin" class="hc-doc-section">
    <div class="ug-module-content">
        <!-- EN CONTENT -->
        <div class="lang-content lang-en" style="display:block;">

            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Overview</h4>
                <p class="mb-0">The <strong>Cashin Providers</strong> module allows you to globally configure which inbound payment channels are available across the system. It controls the underlying payment providers (Aggregators/Acquirers) and sets the baseline pricing, limits, and rules for all merchants before merchant-specific mappings are applied.</p>
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
                            <li class="mb-2">From the main dashboard, locate the left sidebar navigation.</li>
                            <li class="mb-2">Under the <strong>Configurations</strong> section, click on the <strong>Gateway Channel</strong> menu to expand it.</li>
                            <li class="mb-2">Select the <strong>Cash-In Providers</strong> sub-menu to open the global configuration interface.</li>
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
                            <li class="mb-2">Click the <strong>Filters</strong> button at the top right of the table to open the panel.</li>
                            <li class="mb-2"><strong>Channel Group:</strong> Filter by macro payment category (e.g., Virtual Account, E-Wallet, QRIS).</li>
                            <li class="mb-2"><strong>External ID:</strong> Pinpoint the exact aggregator or acquiring bank.</li>
                            <li class="mb-2">Click outside the filter area or click <strong>Apply Filter</strong> to refresh the data table.</li>
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
                    <h3 class="hc-step-title">View Global Providers Data</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Data Table" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <p class="hc-step-desc mb-3">The main table gives you a comprehensive overview of all global cash-in channels. Here is a breakdown of the key features and data points available:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ul class="hc-step-desc mb-0 list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-search text-success mr-2"></i><strong>Global Search:</strong> Use the top search bar to quickly find specific channels. You can search by:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>Channel ID</strong> (e.g., <code>bca_va</code>)</li>
                                    <li class="mb-1"><strong>Channel Group</strong> (e.g., <code>virtual_account</code>)</li>
                                    <li class="mb-0"><strong>External ID Default</strong> (e.g., <code>paylabs</code>)</li>
                                </ul>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-columns text-success mr-2"></i><strong>Data Columns:</strong> The table displays key details for each channel:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>ID & Group:</strong> The unique Channel ID and its Channel Group category.</li>
                                    <li class="mb-1"><strong>Description:</strong> A brief description detailing the channel's purpose.</li>
                                    <li class="mb-1"><strong>External ID Default:</strong> The default third-party provider key for routing.</li>
                                    <li class="mb-0"><strong>Fee Type & Fee:</strong> Indicates if the fee is Fixed, Percentage, or Both, and shows the base amount.</li>
                                </ul>
                            </li>

                            <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Action Menu:</strong> Click the (<i class="fas fa-ellipsis-v"></i>) at the end of any row to access the <strong>Edit Channel</strong> and <strong>Delete Channel</strong> options.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 (Image Left) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Register a New Channel</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Add Channel Form" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>To register a new channel, first click the <strong>+ New Cash In Channel</strong> button on the top right of the table. The following field mappings are required to create a new global cash-in configuration:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Channel Identifiers:</strong> Select or input the <strong>Channel ID</strong> (e.g. <code>bca_va</code>) to define the specific endpoint, and the <strong>Channel Group</strong> (e.g. <code>virtual_account</code>) for categorization. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: The Channel ID must exactly match the upstream provider's expected payload format.</em></li>
                            <li class="mb-3"><strong>Description:</strong> Briefly describe the channel's specific purpose or details to help other administrators understand its usage.</li>
                            <li class="mb-3"><strong>External Provider:</strong> Define the <strong>External Default</strong> to link this channel to the actual upstream aggregator (e.g. <code>paylabs</code>). This dictates where the funds are ultimately processed.</li>
                            <li class="mb-3"><strong>Fee Structure:</strong> Select the <strong>Fee Type</strong> (Fixed, Percentage, or Both) to determine the mathematical logic applied, and set the baseline <strong>Fee Value</strong> (e.g., <code>4500</code> for Fixed or <code>1.5</code> for Percentage).</li>
                            <li class="mb-3"><strong>Limits & Settlement:</strong> Configure the <strong>Amount Min/Max</strong> limits (e.g., <code>10000</code> to <code>50000000</code>) to establish absolute security boundaries, and the <strong>Settlement Interval (Days)</strong> (e.g., <code>0</code> for real-time, <code>1</code> for T+1) to determine when funds are settled. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validation: The system strictly verifies that Amount Min is not greater than Amount Max.</em></li>
                            <li class="mb-3"><strong>Final Review & Save:</strong> Review your configuration against the guide on the left panel, then click the <strong>Save New Channel</strong> button to commit it to the database.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Edit Channel Configuration</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Edit Channel" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>You can update an existing channel if there are changes to the baseline fee structure or limits.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Open Action Menu:</strong> Click the (<i class="fas fa-ellipsis-v"></i>) on the right side of the row you want to modify.</li>
                            <li class="mb-3"><strong>Select Edit:</strong> Choose <strong>Edit Channel</strong> from the dropdown menu to open the form.</li>
                            <li class="mb-3"><strong>Make Changes:</strong> Update the necessary fields to adapt to new business rules:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>External Provider:</strong> Reassign the <strong>External Default</strong> if changing aggregators (e.g., from <code>paylabs</code> to <code>quantum</code>).</li>
                                    <li class="mb-1"><strong>Fee Structure:</strong> Adjust the <strong>Fee Type</strong> and <strong>Fee Value</strong> (e.g., updating Fixed fee from <code>4000</code> to <code>4500</code>).</li>
                                    <li class="mb-1"><strong>Limits & Settlement:</strong> Modify the <strong>Amount Min/Max</strong> boundaries and <strong>Settlement Interval</strong>.</li>
                                </ul>
                                <em class="text-danger small mt-2 d-block"><i class="fas fa-lock"></i> Validation: The Channel ID field is strictly read-only to maintain ledger data integrity. Amount Min cannot exceed Amount Max.</em>
                            </li>
                            <li class="mb-3"><strong>Save Updates:</strong> Double-check the modified values, then click <strong>Update Channel</strong> to apply the changes immediately to the global configuration.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">6</div>
                    <h3 class="hc-step-title">Delete Channel Configuration</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/delete_mapping.png') ?>" alt="Delete Channel" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>If a global channel configuration is no longer needed, you can permanently remove it.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Open Action Menu:</strong> Click the (<i class="fas fa-ellipsis-v"></i>) on the right side of the relevant row.</li>
                            <li class="mb-3"><strong>Select Delete:</strong> Choose <strong>Delete Channel</strong> from the dropdown menu.</li>
                            <li class="mb-3"><strong>Finalize:</strong> Verify the prompt and click <strong>Yes, delete it!</strong> to permanently remove the channel.</li>
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
                            <td><strong>Channel Group</strong></td>
                            <td>Classifies the payment conduit into macro families (e.g., <code>bifast</code>, <code>virtual_account</code>, <code>qris</code>) for strategic categorization.</td>
                        </tr>
                        <tr>
                            <td><strong>Specific Channel ID</strong></td>
                            <td>The precise endpoint identifier (e.g., <code>bifast_bca</code>, <code>qris_dynamic</code>). This must exactly match the upstream provider's expected payload format.</td>
                        </tr>
                        <tr>
                            <td><strong>External Default</strong></td>
                            <td>The designated third-party aggregator or acquiring bank entrusted with processing the inbound funds (e.g., <code>quantum</code>, <code>paylabs</code>).</td>
                        </tr>
                        <tr>
                            <td><strong>Fee Type</strong></td>
                            <td>Determines the core mathematical logic applied to transactions. You can select <code>Fixed</code> to impose a static flat-rate deduction, <code>Percentage</code> to calculate a dynamic fee, or <code>Both</code>. This acts as the global baseline before merchant-specific mappings take over.</td>
                        </tr>
                        <tr>
                            <td><strong>Amount Min / Max</strong></td>
                            <td>Establishes the absolute security boundaries for inbound transaction amounts. If a merchant sets their own limits to 0, these global limits will be enforced by default.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. FAQ / Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Why can't I delete a channel? (Error 1451)</span>
                </div>
                <p class="hc-faq-a">Due to database relation constraints, you cannot delete a global channel if it is currently linked to existing merchant mappings or has active transactional history associated with it. You must first remove the channel from all merchants.</p>
            </div>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-warning"></i> 
                    <span>What happens if I edit a global fee?</span>
                </div>
                <p class="hc-faq-a">The global fee serves as the baseline. It does not automatically overwrite bespoke fee mappings that you have already configured for individual merchants in the External Cash-In module.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>What is the difference between Fixed and Percentage Fee Type?</span>
                </div>
                <p class="hc-faq-a">A <strong>Fixed</strong> fee applies a flat rate (e.g., Rp 4,000) regardless of the transaction amount. A <strong>Percentage</strong> fee calculates the cost based on the transaction value (e.g., 1.5% of the total amount). Make sure to choose the correct type as agreed with your provider.</p>
            </div>

        </div>

        <!-- ID CONTENT -->
        <div class="lang-content lang-id" style="display:none;">

            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Ikhtisar (Overview)</h4>
                <p class="mb-0">Modul <strong>Cashin Providers</strong> memungkinkan Anda mengatur secara global kanal penerimaan dana mana yang tersedia di seluruh sistem. Modul ini mengontrol *provider* pembayaran (*Aggregator/Acquirer*) yang mendasarinya dan menetapkan tarif dasar, limit, dan aturan untuk seluruh *merchant* sebelum pemetaan *merchant* secara spesifik dilakukan.</p>
            </div>

            <!-- 2. Visual Step-by-Step Walkthrough -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Langkah-demi-Langkah</h4>
            
            <!-- Step 1 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <div class="hc-step-number">1</div>
                    <h3 class="hc-step-title">Akses Modul</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Dari dasbor utama, perhatikan navigasi sidebar kiri.</li>
                            <li class="mb-2">Di bawah bagian <strong>Configurations</strong>, klik menu <strong>Gateway Channel</strong> untuk meluaskannya.</li>
                            <li class="mb-2">Pilih sub-menu <strong>Cash-In Providers</strong> untuk membuka halaman konfigurasi global.</li>
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
                    <h3 class="hc-step-title">Gunakan Filter Pencarian</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Klik tombol <strong>Filters</strong> di pojok kanan atas tabel untuk membuka panel.</li>
                            <li class="mb-2"><strong>Channel Group:</strong> Filter berdasarkan kategori pembayaran (mis. Virtual Account, E-Wallet).</li>
                            <li class="mb-2"><strong>External ID:</strong> Tentukan aggregator atau provider secara spesifik.</li>
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
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12 order-2 order-xl-1">
                    <div class="hc-step-number">3</div>
                    <h3 class="hc-step-title">Melihat Data Provider Global</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Tabel Data" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <p class="hc-step-desc mb-3">Tabel utama memberikan gambaran komprehensif mengenai seluruh *channel* cash-in global. Berikut penjelasan detail dari fitur dan data yang tersedia:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ul class="hc-step-desc mb-0 list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-search text-success mr-2"></i><strong>Pencarian Global:</strong> Gunakan kotak pencarian di atas untuk menemukan channel secara instan. Kolom yang dapat dicari meliputi:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>Channel ID</strong> (contoh: <code>bca_va</code>)</li>
                                    <li class="mb-1"><strong>Channel Group</strong> (contoh: <code>virtual_account</code>)</li>
                                    <li class="mb-0"><strong>External ID Default</strong> (contoh: <code>paylabs</code>)</li>
                                </ul>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-columns text-success mr-2"></i><strong>Kolom Data:</strong> Tabel ini menguraikan rincian penting untuk setiap channel:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>ID & Group:</strong> ID unik *channel* beserta kategori *Channel Group*-nya.</li>
                                    <li class="mb-1"><strong>Description:</strong> Deskripsi singkat mengenai kegunaan *channel* tersebut.</li>
                                    <li class="mb-1"><strong>External ID Default:</strong> *Key provider* pihak ketiga *default* untuk rute transaksi.</li>
                                    <li class="mb-0"><strong>Fee Type & Fee:</strong> Menunjukkan apakah tipe biayanya *Fixed* atau *Percentage*, beserta nominal tarifnya.</li>
                                </ul>
                            </li>

                            <li class="mb-3"><i class="fas fa-ellipsis-v text-success mr-2 px-1"></i><strong>Menu Aksi (Action):</strong> Klik ikon (<i class="fas fa-ellipsis-v"></i>) di ujung kanan setiap baris data untuk mengakses opsi <strong>Edit Channel</strong> atau <strong>Delete Channel</strong> pada *channel* spesifik tersebut.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Mendaftarkan Channel Baru</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/add_mapping.png') ?>" alt="Formulir Add Channel" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Untuk mendaftarkan channel baru, pertama-tama klik tombol <strong>+ New Cash In Channel</strong> di sudut kanan atas tabel. Berikut adalah pengaturan yang wajib diisi untuk mendaftarkan channel cash-in baru secara global:</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Identitas Channel:</strong> Pilih atau ketik <strong>Channel ID</strong> (misal <code>bca_va</code>) untuk menentukan *endpoint* spesifik, dan <strong>Channel Group</strong> (misal <code>virtual_account</code>) untuk kategorisasi. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Channel ID harus sama persis dengan format *payload* yang diharapkan oleh provider hulu.</em></li>
                            <li class="mb-3"><strong>Deskripsi:</strong> Masukkan deskripsi singkat mengenai kegunaan spesifik *channel* tersebut untuk membantu administrator lain memahami fungsinya.</li>
                            <li class="mb-3"><strong>Provider Eksternal:</strong> Tentukan <strong>External Default</strong> untuk mengaitkan saluran ini dengan aggregator di hulu (misal <code>paylabs</code>). Kolom ini menentukan ke mana dana pada akhirnya akan diproses.</li>
                            <li class="mb-3"><strong>Struktur Biaya:</strong> Pilih <strong>Fee Type</strong> (Fixed, Percentage, atau Both) untuk menentukan logika matematika yang diterapkan, dan masukkan nilai biaya dasar pada <strong>Fee Value</strong> (misal <code>4500</code> untuk Fixed atau <code>1.5</code> untuk Percentage).</li>
                            <li class="mb-3"><strong>Limit & Setelmen:</strong> Konfigurasikan batas transaksi <strong>Amount Min/Max</strong> (misal <code>10000</code> hingga <code>50000000</code>) untuk menetapkan batas keamanan absolut, serta <strong>Settlement Interval (Days)</strong> (misal <code>0</code> untuk real-time, <code>1</code> untuk T+1) untuk menentukan waktu penyelesaian dana. <br><em class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Validasi: Sistem memverifikasi secara ketat bahwa Amount Min tidak boleh lebih besar dari Amount Max.</em></li>
                            <li class="mb-3"><strong>Review & Simpan:</strong> Periksa kembali konfigurasi Anda dengan panduan di panel kiri, lalu klik tombol <strong>Save New Channel</strong> untuk menyimpannya ke dalam database.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Mengedit Konfigurasi Channel</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/edit_mapping.png') ?>" alt="Edit Channel" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Anda dapat memperbarui konfigurasi channel jika terdapat perubahan pada struktur biaya dasar atau limit transaksi.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Buka Menu Aksi:</strong> Klik ikon (<i class="fas fa-ellipsis-v"></i>) di ujung kanan baris data yang ingin Anda ubah.</li>
                            <li class="mb-3"><strong>Pilih Edit:</strong> Pilih opsi <strong>Edit Channel</strong> dari menu *dropdown* yang muncul.</li>
                            <li class="mb-3"><strong>Lakukan Perubahan:</strong> Perbarui kolom-kolom berikut sesuai dengan aturan bisnis terbaru:
                                <ul class="mt-2 text-muted" style="list-style-type: circle; padding-left: 35px;">
                                    <li class="mb-1"><strong>Provider Eksternal:</strong> Ubah <strong>External Default</strong> jika ingin mengganti aggregator (contoh: dari <code>paylabs</code> ke <code>quantum</code>).</li>
                                    <li class="mb-1"><strong>Struktur Biaya:</strong> Sesuaikan <strong>Fee Type</strong> dan <strong>Fee Value</strong> (contoh: menaikkan biaya Fixed dari <code>4000</code> menjadi <code>4500</code>).</li>
                                    <li class="mb-1"><strong>Limit & Setelmen:</strong> Modifikasi batas transaksi <strong>Amount Min/Max</strong> dan waktu penyelesaian dana.</li>
                                </ul>
                                <em class="text-danger small mt-2 d-block"><i class="fas fa-lock"></i> Validasi: Kolom Channel ID dikunci secara ketat (read-only) untuk menjaga integritas data riwayat. Amount Min tidak boleh melebihi Amount Max.</em>
                            </li>
                            <li class="mb-3"><strong>Simpan Perubahan:</strong> Periksa kembali nilai yang diubah, lalu klik <strong>Update Channel</strong> untuk menerapkan perubahan secara langsung pada konfigurasi global.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">6</div>
                    <h3 class="hc-step-title">Menghapus Konfigurasi Channel</h3>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/delete_mapping.png') ?>" alt="Delete Channel" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <p>Jika sebuah konfigurasi channel global sudah tidak diperlukan, Anda dapat menghapusnya secara permanen.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Buka Menu Aksi:</strong> Klik ikon (<i class="fas fa-ellipsis-v"></i>) di ujung kanan baris data terkait.</li>
                            <li class="mb-3"><strong>Pilih Delete:</strong> Pilih <strong>Delete Channel</strong> dari menu *dropdown*.</li>
                            <li class="mb-3"><strong>Finalisasi:</strong> Periksa pop-up konfirmasi dan klik <strong>Yes, delete it!</strong> untuk menghapus konfigurasi.</li>
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
                            <td><strong>Channel Group</strong></td>
                            <td>Mengklasifikasikan saluran pembayaran ke dalam rumpun makro (contoh: <code>bifast</code>, <code>virtual_account</code>, <code>qris</code>) untuk tujuan kategorisasi strategis.</td>
                        </tr>
                        <tr>
                            <td><strong>Specific Channel ID</strong></td>
                            <td>Pengenal titik akhir presisi (contoh: <code>bca_va</code>). ID ini harus sama persis dengan *payload* yang diterima/diminta oleh provider.</td>
                        </tr>
                        <tr>
                            <td><strong>External Default (Provider)</strong></td>
                            <td>*Aggregator* pihak ketiga atau bank *acquirer* definitif yang memproses dana masuk (contoh: <code>quantum</code>, <code>paylabs</code>).</td>
                        </tr>
                        <tr>
                            <td><strong>Fee Type</strong></td>
                            <td>Mendikte rincian rumus logika matematis dasar. Opsi: <code>Fixed</code> (membebankan tarif statis murni), <code>Percentage</code> (potongan dinamis berbasis volume), atau <code>Both</code> (kombinasi). Ini menjadi tarif dasar sebelum timpaan tarif per *merchant* (merchant mapping) diberlakukan.</td>
                        </tr>
                        <tr>
                            <td><strong>Amount Min / Max</strong></td>
                            <td>Garis batas mutlak perlindungan global. Jika seorang merchant menyetel limit spesifik mereka di angka 0, maka limit global inilah yang akan diandalkan dan dipaksakan oleh sistem.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. FAQ / Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Mengapa saya tidak bisa menghapus (delete) suatu channel? (Error 1451)</span>
                </div>
                <p class="hc-faq-a">Karena konstrain relasi database, Anda dilarang menghapus channel global yang saat ini masih dipetakan ke merchant atau memiliki riwayat transaksi aktif. Anda harus mencabut channel tersebut dari seluruh merchant terlebih dahulu.</p>
            </div>
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-warning"></i> 
                    <span>Apa yang terjadi jika saya mengedit nilai fee secara global?</span>
                </div>
                <p class="hc-faq-a">Nilai biaya global berfungsi sebagai *baseline*. Jika Anda mengubahnya, ia TIDAK otomatis menimpa (overwrite) konfigurasi biaya khusus yang sudah Anda buat per merchant di modul External Cash-In.</p>
            </div>

            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>Apa perbedaan antara Tipe Biaya (Fee Type) Fixed dan Percentage?</span>
                </div>
                <p class="hc-faq-a">Biaya <strong>Fixed</strong> menerapkan tarif tetap (misal: Rp 4.000) terlepas dari nominal transaksi. Biaya <strong>Percentage</strong> menghitung potongan berdasarkan persentase nilai transaksi (misal: 1,5% dari total). Pastikan memilih tipe yang sesuai dengan kesepakatan provider Anda.</p>
            </div>

        </div>
    
    </div>
</div>
