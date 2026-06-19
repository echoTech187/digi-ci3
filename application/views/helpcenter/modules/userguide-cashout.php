<div id="module-ug-cashout" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-desktop text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Cashout Dashboard Guide</h2>
        </div>
        <p class="doc-lead">The Cashout Fee Settings module provides granular control over the transaction costs applied to merchants for outward fund disbursements. You can define specific monetization structures and transaction boundaries per payment channel, which directly influence the net balance deducted from the merchant.</p>
                        
                        <hr class="doc-divider">

                        <h3>1. Conceptual Overview</h3>
                        <p>This module governs the commercial terms between the payment gateway and the merchant for outbound transactions (Cashout). The gateway evaluates these rules in real-time during the transaction lifecycle to calculate deductions before releasing the funds.</p>
                        
                        <div class="doc-callout callout-important">
                            <div class="callout-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="callout-content">
                                <strong>Prerequisites</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Before configuring cashout fees, ensure that:</p>
                                <ul style="margin-top: 4px; padding-left: 16px; margin-bottom: 0;">
                                    <li>The target merchant possesses an <strong>Active</strong> operational status.</li>
                                    <li>The underlying Master Channel is globally enabled at the system gateway level.</li>
                                </ul>
                            </div>
                        </div>

                        <h3>2. Parameter Reference</h3>
                        <p>When provisioning a channel, the system requires precise commercial parameters. Refer to the schema below:</p>
                        <table class="doc-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Requirement</th>
                                    <th>Description & Logic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Channel Group &amp; External ID</strong></td>
                                    <td><span class="badge badge-required">Required</span></td>
                                    <td>The macro-category (e.g., <code>bifast</code>, <code>rtol</code>, <code>ppob</code>) and the upstream service provider (e.g., <code>inacash</code>, <code>quantum</code>).</td>
                                </tr>
                                <tr>
                                    <td><strong>Specific Channel ID</strong></td>
                                    <td><span class="badge badge-required">Required</span></td>
                                    <td>The exact payment endpoint identifier (e.g., <code>bifast_bca</code>, <code>bifast_mandiri</code>).</td>
                                </tr>
                                <tr>
                                    <td><strong>Fee Type</strong></td>
                                    <td><span class="badge badge-required">Required</span></td>
                                    <td>The mathematical model for deduction: <code>Fixed</code>, <code>Percentage</code>, or <code>Both</code> (Hybrid).</td>
                                </tr>
                                <tr>
                                    <td><strong>Fixed Fee / Percentage Fee</strong></td>
                                    <td><span class="badge badge-conditional">Conditional</span></td>
                                    <td>The quantitative fee values. If a specific fee type is not applicable, its corresponding field <strong>must explicitly be set to <code>0</code></strong>. Percentage values must be between <code>0</code> and <code>100</code>.</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Min &amp; Max</strong></td>
                                    <td><span class="badge badge-optional">Optional</span></td>
                                    <td>The transactional floor and ceiling boundaries. Setting these to <code>0</code> commands the system to inherit the global master limits.</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-calculator"></i></div>
                            <div class="callout-content">
                                <strong>Fee Calculation Architecture</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">The gateway applies specific mathematical deductions based on the selected Fee Type. Unlike Cashin fees which typically reduce the incoming amount, Cashout fees are often deducted directly from the merchant's master balance on top of the principal amount:</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Fixed:</strong> <code>Total Deduction = Principal Amount + Fixed Fee</code></li>
                                    <li><strong>Percentage:</strong> <code>Total Deduction = Principal Amount + (Principal Amount × (Percentage Fee / 100))</code></li>
                                    <li><strong>Both (Hybrid):</strong> <code>Total Deduction = Principal Amount + (Principal Amount × (Percentage Fee / 100)) + Fixed Fee</code></li>
                                </ul>
                            </div>
                        </div>

                        <h3>3. Operational Procedures</h3>
                        
                        <h4>A. Provisioning a Single Channel</h4>
                        <p>Targeted provisioning establishes commercial rules for a solitary disbursement method.</p>
                        <ol>
                            <li>Navigate to the action bar and click <strong><i class="fas fa-plus"></i> Add Channel</strong>.</li>
                            <li>In the <strong>CHANNEL CONFIG</strong> block, select the routing parameters (Group, External ID, Specific ID).</li>
                            <li>Define the commercial terms in the <strong>FEE STRUCTURE</strong> block based on the parameter reference above.</li>
                            <li>Configure the optional <strong>LIMITS</strong>.</li>
                            <li>Ensure the Status is toggled to <strong>Active</strong>.</li>
                            <li>Click <strong>SAVE CONFIGURATION</strong>.</li>
                        </ol>

                        <h4>B. Rapid Provisioning via Bulk Add</h4>
                        <p>Bulk provisioning applies a uniform commercial configuration to all unconfigured channels within a specific group simultaneously.</p>
                        
                        <div class="doc-callout callout-tip">
                            <div class="callout-icon"><i class="fas fa-shield-virus"></i></div>
                            <div class="callout-content">
                                <strong>Idempotency &amp; Duplication Protection</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">The Bulk Add engine is idempotent regarding existing configurations. It intelligently scans the group and <strong>skips</strong> channels that the merchant has already configured, guaranteeing that bespoke rates are never accidentally overwritten.</p>
                            </div>
                        </div>

                        <ol>
                            <li>Click the <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> button located in the toolbar.</li>
                            <li>Select the target <strong>Cashout Channel Group</strong> and <strong>External ID Default</strong>.</li>
                            <li>Input the universal fee structure and limits.</li>
                            <li>Click <strong>APPLY BULK SETTINGS</strong>.</li>
                        </ol>

                        <h4>C. Modifying an Active Configuration (Editing)</h4>
                        <p>The gateway enforces strict data integrity. You cannot overwrite or update an existing channel's fees using the <em>Add Channel</em> form. You must utilize the inline modification feature to mutate existing parameters.</p>
                        
                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-lock"></i></div>
                            <div class="callout-content">
                                <strong>Immutable Identifiers</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">During modification, routing identifiers (Channel Group, External ID, and Specific Channel ID) are strictly locked. If these routing parameters are incorrect, you must revoke (delete) the channel and provision a new one.</p>
                            </div>
                        </div>
                        
                        <ol>
                            <li>Locate the target channel configuration within the main data grid.</li>
                            <li>Click the <strong>Action Menu (⋮)</strong> located in the rightmost column of that row.</li>
                            <li>Select <strong>Edit Setting</strong> from the dropdown menu.</li>
                            <li>In the modal window, adjust the mutable commercial terms (Fee Type, Fixed Fee, Percentage Fee, Amount Limits).</li>
                            <li>To temporarily suspend the merchant's ability to process via this channel without permanently deleting the configuration, toggle the <strong>Status</strong> to <em>Inactive</em>.</li>
                            <li>Click <strong>UPDATE CONFIGURATION</strong>.</li>
                        </ol>

                        <h4>D. Revoking Channel Access (Deletion)</h4>
                        <p>Revocation is the process of permanently purging a merchant's access to a specific disbursement endpoint.</p>
                        
                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Irreversible Action</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Deleting a channel configuration is an irreversible action. Once purged, any outbound API requests utilizing this Specific Channel ID for this merchant will be instantly rejected by the gateway with a <code>403 Forbidden</code> HTTP status code until reprovisioned.</p>
                            </div>
                        </div>
                        
                        <ol>
                            <li>Locate the target channel configuration within the main data grid.</li>
                            <li>Click the <strong>Action Menu (⋮)</strong> located in the rightmost column.</li>
                            <li>Select the <strong>Delete</strong> action.</li>
                            <li>A confirmation dialog will appear. Verify the Channel ID before confirming the deletion.</li>
                        </ol>

                        <h3>4. Lifecycle &amp; Status Management</h3>
                        <p>The operational state of a channel fundamentally alters how the gateway routes traffic and calculates fees for the merchant. Understanding these states is critical for maintaining SLA and uptime:</p>
                        
                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-play-circle"></i></div>
                            <div class="callout-content">
                                <strong>Active (Fully Provisioned)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">The channel is in a fully operational state.</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>API Generation:</strong> The API endpoint is completely open. The merchant can successfully dispatch funds (e.g., executing a BI-Fast transfer).</li>
                                    <li><strong>Callbacks:</strong> The gateway will process all inbound webhook callbacks from the provider and forward them to the merchant's endpoint.</li>
                                    <li><strong>Dashboard:</strong> Transactions will actively appear in the real-time monitoring dashboard.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-pause-circle"></i></div>
                            <div class="callout-content">
                                <strong>Inactive (Suspended/Maintenance)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">The channel is in a restricted, dormant state. This is typically used for temporary suspensions or upstream maintenance.</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>API Generation:</strong> The API endpoint is strictly locked. Any new requests to execute disbursements will be instantly rejected by the gateway with a <code>403 Forbidden</code> or <code>503 Service Unavailable</code> error code.</li>
                                    <li><strong>Resolution:</strong> The merchant must contact the administrator to toggle the status back to Active via the Action Menu (⋮).</li>
                                </ul>
                            </div>
                        </div>

                        <h3>5. Advanced Search &amp; Data Filtering</h3>
                        <p>When managing enterprise merchants with extensive channel configurations across multiple providers, utilize the robust search engine and advanced filters to rapidly isolate specific data sets.</p>
                        
                        <div class="doc-callout callout-info">
                            <div class="callout-icon"><i class="fas fa-search"></i></div>
                            <div class="callout-content">
                                <strong>Global Search Bar Operations</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">The primary search bar evaluates your input against all visible data columns simultaneously.</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Precision Lookup:</strong> Type specific keywords like a <em>Channel ID</em> (e.g., <code>bifast_bca</code>) or exact fee numbers. <strong>Condition:</strong> Use this when you need to jump straight to a single row based on exact known data.</li>
                                    <li><strong>Cross-Provider Review:</strong> Type a broad term like <code>bifast</code>. <strong>Condition:</strong> Use this when you want to compare fee structures for similar payment methods across different providers.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-sliders-h"></i></div>
                            <div class="callout-content">
                                <strong>Advanced Contextual Filters</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Click the <strong><i class="fas fa-sliders-h"></i> Filters</strong> button to open the advanced panel. These dropdowns provide strict server-side filtering for complex audits:</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Channel Group Filter:</strong> Isolates the grid to a specific payment category (e.g., <code>bifast</code>, <code>rtol</code>). <strong>Condition:</strong> Use this when you are doing a module-specific review (like adjusting all BI-Fast fees at once).</li>
                                    <li><strong>Provider / External Default:</strong> Filters the configurations by the upstream provider (e.g., <code>inacash</code>, <code>quantum</code>, <code>paylabs</code>). <strong>Condition:</strong> Critical when reconciling monthly invoices from a specific provider against your gateway's configurations.</li>
                                    <li><strong>Channel ID Filter:</strong> Selects a specific payment method from a strict dropdown. <strong>Condition:</strong> Use this to ensure there are no unintended duplicate records or conflicting fees for a single payment method.</li>
                                    <li><strong>Status Filter (Active vs Not Active):</strong> Isolates channels based on their lifecycle. <strong>Condition:</strong> Use this during incident response to quickly pinpoint if a channel was accidentally turned off (Not Active), causing transaction failures.</li>
                                </ul>
                            </div>
                        </div>

                        <h3>6. Troubleshooting &amp; Diagnostics</h3>
                        <p>Consult this matrix to resolve common configuration anomalies:</p>
                        
                        <div class="doc-callout callout-troubleshooting">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Insufficient Balance Errors</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">System Message:</span> <em>"Insufficient merchant balance"</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolution:</strong> If a merchant attempts a disbursement and receives this error, ensure that their current master balance covers both the requested cashout principal amount <strong>and</strong> the configured Fixed/Percentage cashout fee combined.</p>
                            </div>
                        </div>

                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Anomaly: Specific Channel ID dropdown is empty or unselectable</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">System Message:</span> <em>No notification (UI Limitation)</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Diagnostic Steps:</strong></p>
                                <ul style="margin-top: 4px; padding-left: 16px; margin-bottom: 0;">
                                    <li>Verify that you have sequentially selected the <em>Channel Group</em> first, and then the <em>External ID Default</em>. The form relies on cascading logic.</li>
                                    <li>If both are selected but the Specific Channel ID remains empty, it indicates that the upstream provider has not registered any endpoints for that specific group in the Master Channel configurations.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                            <div class="callout-content">
                                <strong>Event Log: "No channels found for bulk insert" after Bulk Add</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">System Message:</span> <em>No explicit warning / Success but no data added</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Context:</strong> The transaction succeeded, but the duplication protection engine skipped all channels because 100% of the channels in the selected group had already been configured individually.</p>
                            </div>
                        </div>

                        <div class="doc-callout callout-error">
                            <div class="callout-icon"><i class="fas fa-times-circle"></i></div>
                            <div class="callout-content">
                                <strong>Validation Error: Invalid Fee values rejected by payload</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">System Message:</span> <em>"Please fill out this field" / Red error message below input</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolution:</strong> The payload failed schema validation. Ensure that <em>Fixed Fee</em> and <em>Limits</em> are strictly <code>≥ 0</code>, and <em>Percentage Fee</em> is strictly bounded within <code>[0.00, 100.00]</code>.</p>
                            </div>
                        </div>

                        <div class="doc-callout callout-error">
                            <div class="callout-icon"><i class="fas fa-arrows-alt-v"></i></div>
                            <div class="callout-content">
                                <strong>Exception: "Invalid Amount Limits (Min &gt; Max)"</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">System Message:</span> <em>"Minimum amount limit exceeds maximum limit"</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolution:</strong> The inputted Minimum boundary is greater than the Maximum boundary, creating a logical paradox. Ensure Minimum is always less than or equal to Maximum. Use <code>0</code> for both to bypass custom bounds.</p>
                            </div>
                        </div>
                    </div>

                    <!-- ID CONTENT -->
                    <div class="lang-content lang-id" style="display:none;">
                        <p class="doc-lead">Modul Pengaturan Biaya Cashout memberikan kendali terperinci atas biaya transaksi yang dibebankan kepada merchant untuk pengeluaran dana (disbursement). Anda dapat menentukan struktur monetisasi spesifik dan batasan transaksi per kanal pembayaran, yang secara langsung memengaruhi saldo bersih yang akan dipotong dari merchant.</p>
                        
                        <hr class="doc-divider">

                        <h3>1. Tinjauan Konseptual (Conceptual Overview)</h3>
                        <p>Modul ini mengatur ketentuan komersial antara payment gateway dan merchant untuk transaksi keluar (Cashout). Gateway mengevaluasi aturan-aturan ini secara <em>real-time</em> selama siklus hidup transaksi untuk menghitung potongan sebelum melepaskan dana.</p>
                        
                        <div class="doc-callout callout-important">
                            <div class="callout-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="callout-content">
                                <strong>Prasyarat (Prerequisites)</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Sebelum mengonfigurasi biaya cashout, pastikan bahwa:</p>
                                <ul style="margin-top: 4px; padding-left: 16px; margin-bottom: 0;">
                                    <li>Merchant target memiliki status operasional <strong>Active</strong>.</li>
                                    <li>Master Channel yang mendasarinya telah diaktifkan secara global di tingkat sistem gateway.</li>
                                </ul>
                            </div>
                        </div>

                        <h3>2. Referensi Parameter (Parameter Reference)</h3>
                        <p>Saat mendaftarkan kanal, sistem membutuhkan parameter komersial yang presisi. Rujuk pada skema di bawah ini:</p>
                        <table class="doc-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Persyaratan</th>
                                    <th>Deskripsi &amp; Logika</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Channel Group &amp; External ID</strong></td>
                                    <td><span class="badge badge-required">Wajib</span></td>
                                    <td>Kategori makro (misal, <code>bifast</code>, <code>rtol</code>, <code>ppob</code>) dan penyedia layanan (provider) di hulu (misal, <code>inacash</code>, <code>quantum</code>).</td>
                                </tr>
                                <tr>
                                    <td><strong>Specific Channel ID</strong></td>
                                    <td><span class="badge badge-required">Wajib</span></td>
                                    <td>Pengidentifikasi <em>endpoint</em> pembayaran yang pasti (misal, <code>bifast_bca</code>, <code>bifast_mandiri</code>).</td>
                                </tr>
                                <tr>
                                    <td><strong>Fee Type</strong></td>
                                    <td><span class="badge badge-required">Wajib</span></td>
                                    <td>Model matematis untuk pemotongan biaya: <code>Fixed</code> (Tetap), <code>Percentage</code> (Persentase), atau <code>Both</code> (Keduanya/Hibrida).</td>
                                </tr>
                                <tr>
                                    <td><strong>Fixed Fee / Percentage Fee</strong></td>
                                    <td><span class="badge badge-conditional">Kondisional</span></td>
                                    <td>Nilai biaya kuantitatif. Jika suatu jenis biaya tidak berlaku, kolom yang bersangkutan <strong>harus diisi secara eksplisit dengan angka <code>0</code></strong>. Nilai persentase harus berada di antara <code>0</code> hingga <code>100</code>.</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Min &amp; Max</strong></td>
                                    <td><span class="badge badge-optional">Opsional</span></td>
                                    <td>Batas nilai transaksi paling rendah (lantai) dan tertinggi (plafon). Mengisi kolom ini dengan <code>0</code> memerintahkan sistem untuk mewarisi limit utama dari konfigurasi global.</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-calculator"></i></div>
                            <div class="callout-content">
                                <strong>Arsitektur Kalkulasi Biaya (Fee Calculation)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Gateway akan menerapkan potongan matematis spesifik berdasarkan <em>Fee Type</em> yang dipilih. Berbeda dengan biaya Cashin yang umumnya mengurangi uang yang diterima, biaya Cashout seringkali ditambahkan ke jumlah utama yang ditarik, sehingga dipotong langsung dari total saldo master merchant:</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Fixed:</strong> <code>Total Potongan Saldo = Nominal Pencairan + Fixed Fee</code></li>
                                    <li><strong>Percentage:</strong> <code>Total Potongan Saldo = Nominal Pencairan + (Nominal Pencairan × (Percentage Fee / 100))</code></li>
                                    <li><strong>Both (Hybrid):</strong> <code>Total Potongan Saldo = Nominal Pencairan + (Nominal Pencairan × (Percentage Fee / 100)) + Fixed Fee</code></li>
                                </ul>
                            </div>
                        </div>

                        <h3>3. Prosedur Operasional (Operational Procedures)</h3>
                        
                        <h4>A. Mendaftarkan Kanal Tunggal (Single Channel Provisioning)</h4>
                        <p>Pendaftaran terarah (Targeted provisioning) akan menetapkan aturan komersial untuk satu metode pengeluaran dana (disbursement) saja.</p>
                        <ol>
                            <li>Arahkan ke bilah aksi (action bar) dan klik <strong><i class="fas fa-plus"></i> Add Channel</strong>.</li>
                            <li>Pada blok <strong>CHANNEL CONFIG</strong>, pilih parameter pengarah (Channel Group, External ID, Specific ID).</li>
                            <li>Tentukan syarat komersial pada blok <strong>FEE STRUCTURE</strong> berdasarkan referensi parameter di atas.</li>
                            <li>Konfigurasikan <strong>LIMITS</strong> jika diperlukan.</li>
                            <li>Pastikan Status diaktifkan menjadi <strong>Active</strong>.</li>
                            <li>Klik <strong>SAVE CONFIGURATION</strong>.</li>
                        </ol>

                        <h4>B. Pendaftaran Cepat Massal (Bulk Add Provisioning)</h4>
                        <p>Pendaftaran massal menerapkan konfigurasi komersial yang seragam ke seluruh kanal yang belum dikonfigurasi dalam satu grup tertentu secara instan.</p>
                        
                        <div class="doc-callout callout-tip">
                            <div class="callout-icon"><i class="fas fa-shield-virus"></i></div>
                            <div class="callout-content">
                                <strong>Idempotensi &amp; Perlindungan Duplikasi Data</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Mesin <em>Bulk Add</em> memiliki sifat idempoten terhadap konfigurasi yang sudah ada. Ia akan memindai seluruh grup dengan cerdas dan <strong>melewati (skip)</strong> kanal-kanal yang telah Anda konfigurasi sebelumnya. Hal ini menjamin tarif khusus (bespoke) tidak akan tertimpa secara tidak sengaja.</p>
                            </div>
                        </div>

                        <ol>
                            <li>Klik tombol <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> yang ada di bilah alat (toolbar).</li>
                            <li>Pilih <strong>Cashout Channel Group</strong> tujuan dan <strong>External ID Default</strong>-nya.</li>
                            <li>Masukkan struktur biaya serta batas (limits) universal yang akan diaplikasikan.</li>
                            <li>Klik <strong>APPLY BULK SETTINGS</strong>.</li>
                        </ol>

                        <h4>C. Memodifikasi Konfigurasi Aktif (Editing)</h4>
                        <p>Sistem payment gateway menjalankan integritas data yang sangat ketat. Anda tidak diperkenankan menimpa (overwrite) biaya pada kanal yang sudah ada dengan menggunakan formulir <em>Add Channel</em>. Anda wajib memakai fitur modifikasi langsung (inline modification) untuk mengubah parameter yang ada.</p>
                        
                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-lock"></i></div>
                            <div class="callout-content">
                                <strong>Pengidentifikasi Tidak Dapat Diubah (Immutable Identifiers)</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Selama proses modifikasi berlangsung, pengidentifikasi arah data (Channel Group, External ID, dan Specific Channel ID) akan dikunci secara permanen. Jika terdapat kesalahan pada parameter tersebut, Anda harus mencabut (menghapus) kanal tersebut dan mendaftarkan yang baru.</p>
                            </div>
                        </div>
                        
                        <ol>
                            <li>Temukan konfigurasi kanal target pada tabel grid data utama.</li>
                            <li>Klik <strong>Menu Aksi (⋮)</strong> yang terletak pada kolom paling kanan di baris tersebut.</li>
                            <li>Pilih opsi <strong>Edit Setting</strong> pada menu yang muncul (dropdown).</li>
                            <li>Pada jendela modal, sesuaikan kembali ketentuan komersial (Fee Type, Fixed Fee, Percentage Fee, Amount Limits) yang dapat diubah.</li>
                            <li>Untuk membekukan sementara akses merchant ke kanal ini tanpa harus menghapusnya permanen, ubah <strong>Status</strong>-nya menjadi <em>Inactive</em>.</li>
                            <li>Klik <strong>UPDATE CONFIGURATION</strong>.</li>
                        </ol>

                        <h4>D. Mencabut Akses Kanal (Deletion/Penghapusan)</h4>
                        <p>Pencabutan adalah proses mencabut hak akses merchant secara permanen ke satu <em>endpoint</em> pembayaran tertentu.</p>
                        
                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Tindakan Bersifat Permanen (Irreversible Action)</strong>
                                <p style="margin-top: 4px; margin-bottom: 0;">Menghapus konfigurasi kanal tidak dapat dibatalkan. Sekali dihapus, setiap permintaan API keluar (outbound) yang memakai ID Kanal (Specific Channel ID) dari merchant bersangkutan akan seketika ditolak oleh gateway, dengan pengembalian status kode HTTP <code>403 Forbidden</code> sampai dikonfigurasi ulang.</p>
                            </div>
                        </div>
                        
                        <ol>
                            <li>Temukan konfigurasi kanal tujuan di dalam tabel data.</li>
                            <li>Klik <strong>Menu Aksi (⋮)</strong> yang berada di ujung kolom paling kanan.</li>
                            <li>Pilih aksi <strong>Delete</strong>.</li>
                            <li>Sebuah dialog konfirmasi akan muncul. Periksa kembali ID Kanal sebelum menekan setuju (confirm) untuk menghapus data.</li>
                        </ol>

                        <h3>4. Manajemen Siklus Hidup &amp; Status (Lifecycle Management)</h3>
                        <p>Status operasional pada sebuah kanal pada prinsipnya akan merombak cara kerja gateway dalam mengarahkan jalur sistem dan mengkalkulasikan biaya bagi setiap merchant. Pemahaman akan tingkatan status ini krusial demi memelihara durasi kerja server (uptime) dan SLA:</p>
                        
                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-play-circle"></i></div>
                            <div class="callout-content">
                                <strong>Aktif (Active / Fully Provisioned)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Kanal berada dalam tahap operasional secara utuh (penuh).</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>API Generation:</strong> Jalur titik akhir API (endpoint) telah terbuka penuh. Merchant bebas menyalurkan pendanaan mereka (contoh: memproses pengiriman uang BI-Fast).</li>
                                    <li><strong>Callbacks:</strong> Mesin gateway akan secara aktif memproses setiap notifikasi masuk (webhook callbacks) dari provider untuk diteruskan ke merchant.</li>
                                    <li><strong>Dashboard:</strong> Seluruh laporan log transaksi akan ditampilkan ke dasbor pengawasan waktu riil (real-time).</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-pause-circle"></i></div>
                            <div class="callout-content">
                                <strong>Tidak Aktif (Inactive / Suspended)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Status kanal sedang dikunci di masa "tidur" (dormant). Umumnya dilakukan saat terjadinya penangguhan sementara atau masa perawatan teknis hulu (upstream maintenance).</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>API Generation:</strong> Jalur endpoint terkunci ketat. Segala percobaan pengajuan penyaluran dana baru akan otomatis dipukul mundur (ditolak) dengan balasan error kode <code>403 Forbidden</code> atau <code>503 Service Unavailable</code>.</li>
                                    <li><strong>Resolusi:</strong> Merchant diwajibkan untuk berkomunikasi dengan bagian administrasi demi memulihkan statusnya lewat Menu Aksi (⋮).</li>
                                </ul>
                            </div>
                        </div>

                        <h3>5. Pencarian Lanjutan &amp; Filter Data (Advanced Search &amp; Data Filtering)</h3>
                        <p>Saat Anda mengelola data merchant <em>Enterprise</em> yang sangat besar dan melibatkan berbagai provider yang luas, gunakan kekuatan mesin pencari dengan penyaringan ketat ini demi melokalisasi data yang spesifik.</p>
                        
                        <div class="doc-callout callout-info">
                            <div class="callout-icon"><i class="fas fa-search"></i></div>
                            <div class="callout-content">
                                <strong>Operasional Mesin Pencari Global (Global Search Bar)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Bilah pencarian mengevaluasi input Anda terhadap seluruh kolom data yang terlihat secara bersamaan.</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Pencarian Presisi:</strong> Ketikkan kata kunci spesifik seperti <em>Channel ID</em> (misal, <code>bifast_bca</code>) atau nominal biaya yang pasti. <strong>Kondisi Penggunaan:</strong> Gunakan saat Anda perlu melompat langsung ke satu baris data tertentu.</li>
                                    <li><strong>Tinjauan Lintas Provider:</strong> Ketikkan kata kunci umum seperti <code>bifast</code>. <strong>Kondisi Penggunaan:</strong> Sangat berguna ketika Anda ingin membandingkan struktur biaya untuk metode pembayaran yang sama dari provider yang berbeda.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-sliders-h"></i></div>
                            <div class="callout-content">
                                <strong>Filter Kontekstual Lanjutan (Advanced Filters)</strong>
                                <p style="margin-top: 4px; margin-bottom: 8px;">Klik tombol <strong><i class="fas fa-sliders-h"></i> Filters</strong> untuk membuka panel tingkat lanjut. Pilihan <em>dropdown</em> ini memberikan penyaringan sisi-server yang ketat untuk audit mendalam:</p>
                                <ul style="margin-top: 0; padding-left: 16px; margin-bottom: 0;">
                                    <li><strong>Filter Channel Group:</strong> Mengisolasi tabel pada kategori pembayaran tertentu (misal, <code>bifast</code>, <code>rtol</code>). <strong>Kondisi Penggunaan:</strong> Gunakan saat Anda fokus merombak satu modul khusus (misalnya mengubah semua biaya BI-Fast sekaligus).</li>
                                    <li><strong>Filter Provider / External Default:</strong> Menyaring konfigurasi berdasarkan pihak penyedia (misal, <code>inacash</code>, <code>quantum</code>, <code>paylabs</code>). <strong>Kondisi Penggunaan:</strong> Sangat krusial saat Anda sedang mencocokkan tagihan (rekonsiliasi) dari sebuah provider dengan pengaturan gateway Anda.</li>
                                    <li><strong>Filter Channel ID:</strong> Memilih satu metode pembayaran secara spesifik melalui <em>dropdown</em>. <strong>Kondisi Penggunaan:</strong> Gunakan untuk memastikan tidak ada konflik atau duplikasi biaya (ganda) pada satu metode pembayaran yang sama.</li>
                                    <li><strong>Filter Status (Active vs Not Active):</strong> Mengisolasi kanal berdasarkan siklus hidupnya. <strong>Kondisi Penggunaan:</strong> Gunakan saat respons insiden untuk mengecek dengan cepat apakah ada kanal yang tidak sengaja berstatus <em>Not Active</em> sehingga menyebabkan transaksi gagal.</li>
                                </ul>
                            </div>
                        </div>

                        <h3>6. Pemecahan Masalah &amp; Diagnostik (Troubleshooting &amp; Diagnostics)</h3>
                        <p>Konsultasikan matriks ini untuk menyelesaikan anomali konfigurasi yang umum terjadi:</p>

                        <div class="doc-callout callout-troubleshooting">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Error Saldo Tidak Mencukupi (Insufficient Balance)</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">Pesan Sistem:</span> <em>"Saldo merchant tidak mencukupi"</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolusi:</strong> Jika merchant mencoba melakukan pencairan dan mendapat <em>error</em> saldo tidak cukup, pastikan saldo master mereka saat ini dapat menutupi nominal pencairan pokok yang diminta <strong>ditambah</strong> dengan total biaya Cashout (Tetap/Persentase) yang telah Anda atur.</p>
                            </div>
                        </div>
                        
                        <div class="doc-callout callout-warning">
                            <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="callout-content">
                                <strong>Anomali: Dropdown Specific Channel ID kosong atau tidak dapat dipilih</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">Pesan Sistem:</span> <em>Tidak ada notifikasi (Batasan UI)</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Langkah Diagnostik:</strong></p>
                                <ul style="margin-top: 4px; padding-left: 16px; margin-bottom: 0;">
                                    <li>Pastikan Anda telah memilih <em>Channel Group</em> terlebih dahulu, lalu dilanjutkan dengan <em>External ID Default</em> secara berurutan. Formulir ini bergantung pada logika kaskade.</li>
                                    <li>Jika keduanya sudah dipilih namun Specific Channel ID masih tetap kosong, ini menandakan bahwa provider hulu tersebut belum mendaftarkan endpoint satupun untuk grup spesifik terkait dalam daftar Master Channel.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="doc-callout callout-note">
                            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                            <div class="callout-content">
                                <strong>Event Log: "No channels found for bulk insert" setelah Bulk Add</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">Pesan Sistem:</span> <em>Tidak ada peringatan eksplisit / Berhasil tetapi tidak ada data bertambah</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Konteks:</strong> Transaksi berhasil, tetapi mesin pelindung duplikasi melewati semua kanal karena 100% kanal di grup yang dipilih telah dikonfigurasi secara individual sebelumnya.</p>
                            </div>
                        </div>

                        <div class="doc-callout callout-error">
                            <div class="callout-icon"><i class="fas fa-times-circle"></i></div>
                            <div class="callout-content">
                                <strong>Validation Error: Invalid Fee values rejected by payload</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">Pesan Sistem:</span> <em>"Harap isi bidang ini" / Pesan error berwarna merah di bawah kolom</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolusi:</strong> <em>Payload</em> gagal dalam validasi skema. Pastikan <em>Fixed Fee</em> dan <em>Limits</em> mutlak bernilai <code>≥ 0</code>, dan <em>Percentage Fee</em> dibatasi secara ketat dalam rentang <code>[0.00, 100.00]</code>.</p>
                            </div>
                        </div>

                        <div class="doc-callout callout-error">
                            <div class="callout-icon"><i class="fas fa-arrows-alt-v"></i></div>
                            <div class="callout-content">
                                <strong>Pengecualian (Exception): "Invalid Amount Limits (Min &gt; Max)"</strong>
                                <p style="margin-top: 4px; margin-bottom: 4px;"><span class="badge badge-secondary" style="font-size: 10px;">Pesan Sistem:</span> <em>"Batas nominal minimum melebihi batas maksimum"</em></p>
                                <p style="margin-top: 4px; margin-bottom: 0;"><strong>Resolusi:</strong> Batas transaksi Minimum yang dimasukkan lebih besar daripada batas Maksimum, sehingga menciptakan paradoks logika. Pastikan nilai Minimum selalu kurang dari atau sama dengan batas Maksimum. Gunakan angka <code>0</code> untuk keduanya jika ingin mengabaikan kustomisasi batas.</p>
                            </div>
                        </div>
                    </div>
                </div>


