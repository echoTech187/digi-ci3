<div id="module-ug-merchant-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Cashin Fee Settings</strong> module allows administrators to override the global MDR (Merchant Discount Rate) and fixed fees for incoming payment channels, specifically tailored for an individual merchant. Use this to create custom commercial agreements without affecting platform-wide defaults.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Data Columns</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Column / Field</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CUSTOM FEE TOGGLE</strong></td><td class="p-3 border-0">A master switch per channel. When ON, the system uses the fee defined here instead of the global default.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL LIST</strong></td><td class="p-3 border-0">All payment channels enabled for this merchant (e.g., `VA BCA`, `QRIS`, `OVO`). Each has its own independent fee row.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED FEE (Rp)</strong></td><td class="p-3 border-0">A flat Rupiah amount deducted per transaction, regardless of the value (e.g., Rp 2,500).</td></tr>
                        <tr><td class="p-3 border-0"><strong>PERCENTAGE FEE (%)</strong></td><td class="p-3 border-0">A percentage of the transaction value deducted as MDR (e.g., 0.7%).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SAVE BUTTON</strong></td><td class="p-3 border-0">Persists changes immediately. Only affects new transactions.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Searching -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Searching</h5>
            <p class="text-muted mb-4">Use the built-in search to track down specific fee configurations.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-2"><strong>Quick Search:</strong> Type in the <em>Search setting...</em> box to instantly filter the channel list.</li>
                </ol>
            </div>
        </div>

        <!-- Section 1: Fee Hierarchy -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sitemap text-primary mr-2"></i> 1. Architecture: The Fee Hierarchy</h5>
            <p class="text-muted mb-4">The system utilizes a <strong>two-tier fee hierarchy</strong> when calculating charges on incoming payments:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Incoming Payment]) --> B{Custom Fee<br>Enabled?}
                        B -- Yes --> C[Apply Merchant-Specific<br>Fixed + % Fee]
                        B -- No --> D[Apply Global Default<br>MDR / Fee]
                        C --> E[Calculate Final Net Amount]
                        D --> E
                        E --> F[(Credit Merchant Balance)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Tier 1 — Merchant-Specific Fee (This module):</strong> If a custom fee is toggled ON for the channel, the system uses it exclusively. The global rate is completely bypassed.</li>
                    <li class="mb-3"><strong>Tier 2 — Global Default Fee:</strong> If no custom fee is set (Custom toggle is OFF), the system naturally falls back to the platform-wide MDR configured in the main Cash-In Providers module.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Configuring Custom Fees -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Configuring Custom Commercial Agreements</h5>
            <p class="text-muted mb-4">Follow these steps to tailor a specific fee structure for a merchant (e.g., a VIP client with negotiated rates).</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Merchant Setup</strong>, open the target merchant, and click <strong>Cashin Settings</strong> from the action menu (⋮).</li>
                    <li class="mb-3">Locate the payment channel row (e.g., <code>VA BCA</code>) and toggle <strong>Custom Fee</strong> to <strong>ON</strong>.</li>
                    <li class="mb-3">Input your negotiated <strong>Fixed Fee</strong> (in Rupiah) and <strong>Percentage Fee</strong>. To create a zero-fee channel, set both to <code>0</code>.</li>
                    <li class="mb-2">Click <strong>Save</strong>. The change is immediate and applies to all new invoices generated from that exact moment.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Reverting to Global Settings</strong>
                    <p class="mb-0 text-muted small">To undo a custom agreement, simply toggle the Custom Fee switch back to OFF and save. The merchant will instantly resume using the system-wide global rates.</p>
                </div>
            </div>
        </div>

        <!-- New Section: Form Validations -->
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
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0">All fields must be populated (<code>Channel ID</code>, <code>Fee Type</code>, <code>Fee</code>, <code>Percentage</code>, <code>Interval</code>, <code>Min Amount</code>, <code>Max Amount</code>, <code>Status</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Data Types</strong></td><td class="p-3 border-0">All fee and amount fields must be valid numeric values (integers or decimals).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Unique Channel Config</strong></td><td class="p-3 border-0">Only one fee configuration per channel is allowed per merchant.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Configuration Saved:</strong> <code>Data successfully inserted</code> / <code>updated</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplicate Entry (1062):</strong> <code>Failed to insert data: A fee configuration for this channel already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You cannot create a second fee rule for the same channel. Edit the existing configuration instead.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to add cashin fee settings.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks INSERT or UPDATE privileges. Contact the Database Administrator.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: What happens to pending transactions when I change the fee?
                </a>
                <div id="faq_en_mci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolution:</strong> Fees are locked in securely at the exact moment an invoice or transaction is created. Changing the fee here will only affect brand-new transactions. Old pending invoices will retain their originally assigned fee.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Can I set a fee to zero?
                </a>
                <div id="faq_en_mci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Yes. If you set both the Fixed and Percentage fees to 0 and enable the Custom Fee toggle, the merchant will not be charged anything (useful for VIPs). However, remember that upstream gateway costs still apply to your platform.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mci_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: How do I verify the exact fee deducted?
                </a>
                <div id="faq_en_mci_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Open the <strong>Mutation Log</strong> tab for the merchant. You will see detailed entries showing the exact gross amount credited and the specific MDR deduction debited for each transaction.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Cashin Fee Settings</strong> memungkinkan Anda menimpa (<em>override</em>) MDR global dan biaya tetap untuk kanal penerimaan pembayaran, yang disesuaikan secara khusus untuk satu merchant. Gunakan fitur ini untuk membentuk perjanjian komersial khusus tanpa mengganggu tarif default seluruh platform.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Kolom Data</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Kolom / Isian</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CUSTOM FEE TOGGLE</strong></td><td class="p-3 border-0">Sakelar utama. Saat ON, sistem menggunakan biaya spesifik ini daripada tarif global.</td></tr>
                        <tr><td class="p-3 border-0"><strong>CHANNEL LIST</strong></td><td class="p-3 border-0">Daftar kanal aktif (misal: `VA BCA`, `QRIS`). Masing-masing memiliki pengaturan fee independen.</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED FEE (Rp)</strong></td><td class="p-3 border-0">Potongan tetap dalam Rupiah untuk setiap transaksi (misal: Rp 2.500).</td></tr>
                        <tr><td class="p-3 border-0"><strong>PERCENTAGE FEE (%)</strong></td><td class="p-3 border-0">Persentase MDR yang dipotong dari total transaksi (misal: 0.7%).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SAVE BUTTON</strong></td><td class="p-3 border-0">Menyimpan perubahan secara instan. Hanya berlaku untuk transaksi baru ke depannya.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pencarian -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> Pencarian</h5>
            <p class="text-muted mb-4">Gunakan pencarian bawaan untuk melacak konfigurasi biaya spesifik.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-2"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search setting...</em> untuk memfilter daftar kanal secara instan.</li>
                </ol>
            </div>
        </div>

        <!-- Section 1: Fee Hierarchy -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sitemap text-primary mr-2"></i> 1. Arsitektur: Hierarki Biaya</h5>
            <p class="text-muted mb-4">Sistem ini mengandalkan <strong>hierarki biaya dua tingkat</strong> dalam kalkulasi potongan kas masuk:</p>

            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Pembayaran Masuk]) --> B{Custom Fee<br>Aktif?}
                        B -- Ya --> C[Gunakan Tarif Kustom<br>Merchant]
                        B -- Tidak --> D[Gunakan Tarif Global<br>MDR]
                        C --> E[Kalkulasi Dana Bersih]
                        D --> E
                        E --> F[(Kredit Saldo Merchant)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Tingkat 1 — Biaya Spesifik Merchant (Modul ini):</strong> Jika sakelar Custom Fee dalam posisi ON, sistem akan menggunakannya secara eksklusif. Tarif global akan dilewati sepenuhnya.</li>
                    <li class="mb-3"><strong>Tingkat 2 — Biaya Default Global:</strong> Jika tidak ada custom fee (sakelar OFF), sistem secara alami kembali (fallback) ke tarif MDR yang berlaku global (diatur pada modul Cash-In Providers).</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Configuring Custom Fees -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengonfigurasi Kesepakatan Komersial Kustom</h5>
            <p class="text-muted mb-4">Ikuti langkah-langkah ini untuk menetapkan struktur biaya khusus bagi merchant VIP Anda.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Buka <strong>Merchant Setup</strong>, temukan merchant target, lalu klik <strong>Cashin Settings</strong> dari menu aksi (⋮).</li>
                    <li class="mb-3">Cari baris kanal pembayaran (misalnya, <code>VA BCA</code>) lalu geser tombol <strong>Custom Fee</strong> menjadi <strong>ON</strong>.</li>
                    <li class="mb-3">Masukkan nilai <strong>Fixed Fee</strong> dan <strong>Percentage Fee</strong> sesuai kesepakatan. Untuk layanan bebas biaya, atur keduanya di angka <code>0</code>.</li>
                    <li class="mb-2">Klik <strong>Save</strong>. Perubahan langsung berlaku dan diaplikasikan ke semua invoice baru yang dibuat mulai detik itu.</li>
                </ol>
            </div>

            <div class="doc-callout callout-info shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Mengembalikan ke Tarif Global</strong>
                    <p class="mb-0 text-muted small">Untuk membatalkan perjanjian kustom, cukup matikan sakelar Custom Fee (OFF) dan simpan. Merchant akan langsung kembali menggunakan tarif standar platform Anda.</p>
                </div>
            </div>
        </div>

        <!-- New Section: Form Validations -->
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
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Semua kolom harus diisi (<code>Channel ID</code>, <code>Fee Type</code>, <code>Fee</code>, <code>Percentage</code>, <code>Interval</code>, <code>Min Amount</code>, <code>Max Amount</code>, <code>Status</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tipe Data</strong></td><td class="p-3 border-0">Semua isian nilai biaya (fee) dan limit nominal transaksi harus berupa nilai numerik valid.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Unik per Channel</strong></td><td class="p-3 border-0">Setiap merchant hanya boleh memiliki satu pengaturan fee per layanan.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Pengaturan Disimpan:</strong> <code>Data successfully inserted</code> / <code>updated</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplikat Konfigurasi (1062):</strong> <code>Failed to insert data: A fee configuration for this channel already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda tidak dapat membuat pengaturan fee baru untuk kanal yang sama. Silakan edit konfigurasi yang sudah ada.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges to add cashin fee settings.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki izin INSERT. Silakan hubungi Database Administrator.</div>
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
                <a href="#faq_id_mci_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Apa yang terjadi pada transaksi pending saat fee diubah?
                </a>
                <div id="faq_id_mci_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Resolusi:</strong> Biaya dikunci secara aman pada detik pembuatan invoice. Pengubahan tarif di sini hanya berdampak pada transaksi baru yang dibuat setelahnya. Invoice lama yang berstatus pending akan tetap memakai tarif fee aslinya.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mci_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Bolehkah mengatur biaya menjadi nol?
                </a>
                <div id="faq_id_mci_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Ya. Atur Fixed Fee dan Percentage Fee ke angka 0, lalu nyalakan toggle Custom Fee. Merchant tidak akan dipotong biaya sepeser pun. Namun, ingat bahwa biaya dari gateway hulu tetap ditagihkan ke pihak platform Anda.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mci_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Cara memverifikasi potongan fee secara akurat
                </a>
                <div id="faq_id_mci_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Buka tab <strong>Mutation Log</strong> untuk merchant yang bersangkutan. Anda akan melihat entri mendetail yang memisahkan jumlah bruto yang dikreditkan ke saldo, dan jumlah MDR spesifik yang dipotong (debit) per transaksi.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
