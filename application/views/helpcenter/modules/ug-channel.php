<div id="module-ug-channel" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Channel Management module is the master control panel for all upstream payment acquiring connections. Each "channel" represents a live gateway connection to a bank, e-wallet provider, or payment aggregator. Manage routing logic, toggle channel availability, and respond instantly to upstream outages without a code deployment.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:28%">UI Element</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Channel List</strong></td><td class="p-3 border-0">Complete list of all registered payment channels (e.g., BCA VA, BNI VA, OVO, GoPay, QRIS MPM, BI-FAST). Each shows its current global status: <code>Active</code> (enabled for all) or <code>Not Active</code> (disabled for all).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Active Toggle</strong></td><td class="p-3 border-0">A switch that enables or disables the channel <strong>globally</strong> across all merchants. When disabled, all merchants receive a <code>MAINTENANCE</code> error for that payment method via API.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Channel Type</strong></td><td class="p-3 border-0">Indicates whether the channel is <code>Cashin</code> (inbound payment collection), <code>Cashout</code> (outbound disbursement), or <code>Both</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Provider / Aggregator</strong></td><td class="p-3 border-0">The upstream bank or aggregator that powers this channel (e.g., Paylabs, GV, Midtrans, direct bank API).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Min / Max Amount</strong></td><td class="p-3 border-0">The transaction amount limits enforced at the channel level for all merchants using this channel.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Edit</strong> button</td><td class="p-3 border-0">Opens the channel configuration form to update limits, provider credentials, routing endpoints, and maintenance status.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sitemap text-primary mr-2"></i> 1. System Architecture</h5>
            <p class="text-muted mb-4">The channel layer is the intermediary between merchant API requests and upstream payment providers. Understanding this architecture is essential for diagnosing routing failures.</p>
            
            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Merchant API Request]) --> B[Gateway Router]
                        B --> C{Check Channel Status}
                        C -- Inactive --> D[Return MAINTENANCE Error]
                        C -- Active --> E{Check Min/Max Limits}
                        E -- Out of Bounds --> F[Return LIMIT_EXCEEDED]
                        E -- Valid --> G[Forward to Upstream Provider API]
                        G --> H[(Return Response to Merchant)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2">
                <p class="text-muted mt-2 mb-0">If the Channel Status is <strong>Inactive</strong>, the Gateway Router short-circuits the request and returns <code>MAINTENANCE</code> immediately — the upstream provider is never contacted. This allows instant, zero-downtime maintenance responses.</p>
            </div>
        </div>

        <!-- Section 2: Enable/Disable -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-toggle-on text-primary mr-2"></i> 2. Enabling / Disabling a Channel</h5>
            <p class="text-muted mb-4">Use this when a payment provider announces maintenance, experiences a sustained outage, or when you need to temporarily block a specific payment method.</p>

            <div class="pl-4 border-left border-warning ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Step-by-Step Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Settings → Channel Management</strong> from the sidebar.</li>
                    <li class="mb-3">Locate the target channel from the list (e.g., <em>BCA Virtual Account</em>). You can use the search/filter to find it quickly.</li>
                    <li class="mb-3">Click the <strong>Active Toggle</strong> switch to disable the channel. The switch will turn red and the status badge will update to <code>INACTIVE</code>.</li>
                    <li class="mb-3">All subsequent API calls from any merchant attempting to use this channel will immediately receive a <code>SERVICE_NOT_ALLOWED</code> or <code>MAINTENANCE</code> error response.</li>
                    <li class="mb-2">When the upstream provider is back online and stable, click the toggle again to re-enable the channel. Monitor the first few transactions to confirm routing is restored.</li>
                </ol>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Global Impact — All Merchants Affected</strong>
                    <p class="mb-0 text-muted small">Disabling a channel affects <strong>all merchants</strong> on the platform simultaneously, not just a specific merchant. If you need to disable a payment method for only one merchant, use the per-merchant Cashin/Cashout configuration instead (in the Merchant profile settings).</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Edit Channel Config -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-cog text-primary mr-2"></i> 3. Editing Channel Configuration</h5>
            <div class="pl-4 border-left border-info ml-2">
                <ol class="text-muted mb-0">
                    <li class="mb-3">In the Channel list, click the <strong><i class="fas fa-pencil-alt"></i> Edit</strong> button next to the target channel.</li>
                    <li class="mb-3">Modify the required fields: <strong>Min Amount</strong>, <strong>Max Amount</strong>, <strong>Provider endpoint URL</strong>, or <strong>Aggregator credentials</strong>.</li>
                    <li class="mb-3">Click <strong>Save Changes</strong>. The new configuration takes effect immediately for all subsequent API requests — no server restart required.</li>
                    <li class="mb-2">If adding a completely new channel (new bank/aggregator integration), it must first be seeded into the <code>master_channels</code> table by the engineering team and cache cleared before it appears in this list.</li>
                </ol>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 4. Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>ID</code>, <code>Channel Group</code>, <code>External Default</code>, <code>Fee Type</code>, <code>Fee</code>, <code>Amount Min</code>, and <code>Amount Max</code> must be populated. (Cashin also requires <code>Settlement Interval</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Data Types</strong></td><td class="p-3 border-0"><code>Fee</code>, <code>Settlement Interval</code>, <code>Amount Min</code>, and <code>Amount Max</code> must be valid numeric values.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Deletion Protection</strong></td><td class="p-3 border-0">A channel cannot be deleted if it is still linked to active merchant configurations or transactions.</td></tr>
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
                            <li class="mb-0"><strong>Configuration Saved/Deleted:</strong> <code>Data successfully inserted/updated</code> or <code>Channel successfully deleted</code>.</li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplicate Entry (1062):</strong> <code>Failed to insert/update data: A channel with this ID or configuration already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ensure you are using a unique ID or edit the existing channel configuration instead.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Dependency Constraint (1451):</strong> <code>Cannot delete this channel because it is currently linked to existing merchant fee configurations or transactions.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You must first disable the channel or remove its references from all merchant accounts before attempting deletion.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks INSERT/UPDATE/DELETE privileges. Contact the Database Administrator.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ch_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Merchants getting "MAINTENANCE" error on a specific payment method
                </a>
                <div id="faq_en_ch_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> All transactions for a specific payment method (e.g., BCA VA) fail with a <code>MAINTENANCE</code> or <code>SERVICE_NOT_ALLOWED</code> error.<br><br>
                        <strong>Resolution:</strong> Go to <strong>Settings → Channel Management</strong> and verify the channel's toggle status. If it is set to <code>INACTIVE</code> and the upstream provider is actually healthy, re-enable it by clicking the toggle. If upstream is genuinely down, leave it disabled and communicate a maintenance window to affected merchants.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ch_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Intermittent timeouts and errors for a specific channel
                </a>
                <div id="faq_en_ch_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Transactions via a specific bank/aggregator channel are randomly timing out or returning provider-side errors.<br><br>
                        <strong>Resolution:</strong> This indicates an unstable upstream connection — not a gateway issue. <strong>Disable the channel immediately</strong> to prevent new transactions from getting stuck in PENDING state. Monitor the provider's status page and re-enable once they confirm stability. Affected pending transactions will be resolved by the background status polling mechanism.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_ch_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: A newly integrated channel is not appearing in the Channel list
                </a>
                <div id="faq_en_ch_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Engineering has finished integrating a new payment method, but it does not appear in the Channel Management dashboard.<br><br>
                        <strong>Resolution:</strong> The new channel must be properly seeded into the <code>master_channels</code> database table AND the application/server-side cache must be cleared (run <code>php index.php cli/cache/clear</code>). If it still doesn't appear after clearing cache, verify the seeder ran successfully and there are no database constraint errors.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul Channel Management adalah panel kontrol utama untuk semua koneksi akuisisi pembayaran upstream. Setiap "channel" mewakili koneksi gateway langsung ke bank, penyedia e-wallet, atau agregator pembayaran. Kelola logika routing, toggle ketersediaan channel, dan respons instan terhadap gangguan upstream tanpa deployment kode.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Antarmuka (UI)</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:28%">Elemen UI</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Daftar Channel</strong></td><td class="p-3 border-0">Daftar lengkap semua channel pembayaran yang terdaftar (mis. BCA VA, BNI VA, OVO, GoPay, QRIS MPM, BI-FAST). Setiap channel menampilkan status globalnya: <code>Active</code> (aktif untuk semua) atau <code>Not Active</code> (dinonaktifkan untuk semua).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Toggle Aktif</strong></td><td class="p-3 border-0">Switch yang mengaktifkan atau menonaktifkan channel secara <strong>global</strong> di semua merchant. Saat dinonaktifkan, semua merchant menerima error <code>MAINTENANCE</code> untuk metode pembayaran tersebut melalui API.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tipe Channel</strong></td><td class="p-3 border-0">Menunjukkan apakah channel berjenis <code>Cashin</code> (pengumpulan pembayaran masuk), <code>Cashout</code> (pencairan keluar), atau <code>Both</code> (keduanya).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Provider / Agregator</strong></td><td class="p-3 border-0">Bank upstream atau agregator yang menjalankan channel ini (mis. Paylabs, GV, Midtrans, API bank langsung).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Min / Max Amount</strong></td><td class="p-3 border-0">Batas nominal transaksi yang diberlakukan di level channel untuk semua merchant yang menggunakan channel ini.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tombol Edit</strong></td><td class="p-3 border-0">Membuka formulir konfigurasi channel untuk memperbarui batas, kredensial provider, endpoint routing, dan status maintenance.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sitemap text-primary mr-2"></i> 1. Arsitektur Sistem</h5>
            <p class="text-muted mb-4">Layer channel adalah perantara antara request API merchant dan penyedia pembayaran upstream. Memahami arsitektur ini sangat penting untuk mendiagnosis kegagalan routing.</p>
            
            <div class="mermaid-container mb-4">
                <div class="mermaid">
                    flowchart TD
                        A([Request API Merchant]) --> B[Gateway Router]
                        B --> C{Cek Status Channel}
                        C -- Inactive --> D[Kembalikan Error MAINTENANCE]
                        C -- Active --> E{Cek Limit Min/Max}
                        E -- Di Luar Batas --> F[Kembalikan LIMIT_EXCEEDED]
                        E -- Valid --> G[Teruskan ke API Provider Upstream]
                        G --> H[(Kembalikan Response ke Merchant)]
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2">
                <p class="text-muted mt-2 mb-0">Jika Status Channel adalah <strong>Inactive</strong>, Gateway Router langsung memutus request dan mengembalikan <code>MAINTENANCE</code> — provider upstream tidak pernah dihubungi. Ini memungkinkan respons maintenance instan tanpa downtime.</p>
            </div>
        </div>

        <!-- Section 2: Enable/Disable -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-toggle-on text-primary mr-2"></i> 2. Mengaktifkan / Menonaktifkan Channel</h5>
            <p class="text-muted mb-4">Gunakan ini saat penyedia pembayaran mengumumkan maintenance, mengalami gangguan berkepanjangan, atau saat Anda perlu memblokir sementara metode pembayaran tertentu.</p>

            <div class="pl-4 border-left border-warning ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Panduan Langkah-demi-Langkah:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke <strong>Settings → Channel Management</strong> dari sidebar.</li>
                    <li class="mb-3">Temukan channel target dari daftar (mis. <em>BCA Virtual Account</em>). Gunakan fitur search/filter untuk menemukannya dengan cepat.</li>
                    <li class="mb-3">Klik switch <strong>Toggle Aktif</strong> untuk menonaktifkan channel. Switch akan berubah merah dan badge status akan berubah menjadi <code>INACTIVE</code>.</li>
                    <li class="mb-3">Semua panggilan API berikutnya dari merchant mana pun yang mencoba menggunakan channel ini akan langsung menerima respons error <code>SERVICE_NOT_ALLOWED</code> atau <code>MAINTENANCE</code>.</li>
                    <li class="mb-2">Saat provider upstream sudah kembali online dan stabil, klik toggle lagi untuk mengaktifkan ulang channel. Pantau beberapa transaksi pertama untuk memastikan routing telah pulih.</li>
                </ol>
            </div>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Dampak Global — Semua Merchant Terpengaruh</strong>
                    <p class="mb-0 text-muted small">Menonaktifkan channel berdampak pada <strong>semua merchant</strong> di platform secara bersamaan, bukan hanya merchant tertentu. Jika Anda perlu menonaktifkan metode pembayaran hanya untuk satu merchant, gunakan konfigurasi Cashin/Cashout per-merchant (di pengaturan profil Merchant) sebagai gantinya.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Edit Config -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-cog text-primary mr-2"></i> 3. Mengedit Konfigurasi Channel</h5>
            <div class="pl-4 border-left border-info ml-2">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Di daftar Channel, klik tombol <strong><i class="fas fa-pencil-alt"></i> Edit</strong> di samping channel target.</li>
                    <li class="mb-3">Modifikasi field yang diperlukan: <strong>Min Amount</strong>, <strong>Max Amount</strong>, <strong>URL endpoint Provider</strong>, atau <strong>Kredensial Agregator</strong>.</li>
                    <li class="mb-3">Klik <strong>Save Changes</strong>. Konfigurasi baru langsung berlaku untuk semua request API berikutnya — tidak perlu restart server.</li>
                    <li class="mb-2">Jika menambahkan channel baru (integrasi bank/agregator baru), harus di-seed terlebih dahulu ke tabel <code>master_channels</code> oleh tim teknik dan cache dibersihkan sebelum muncul di daftar ini.</li>
                </ol>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 4. Validasi Form & Batasan (Constraints)</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                            <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>ID</code>, <code>Channel Group</code>, <code>External Default</code>, <code>Fee Type</code>, <code>Fee</code>, <code>Amount Min</code>, dan <code>Amount Max</code> harus diisi. (Khusus Cashin memerlukan <code>Settlement Interval</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tipe Data</strong></td><td class="p-3 border-0">Kolom <code>Fee</code>, <code>Settlement Interval</code>, <code>Amount Min</code>, dan <code>Amount Max</code> harus berupa angka numerik.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Proteksi Penghapusan</strong></td><td class="p-3 border-0">Channel tidak dapat dihapus jika masih terikat dengan konfigurasi fee merchant aktif atau rekam jejak transaksi.</td></tr>
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
                            <li class="mb-0"><strong>Aksi Berhasil:</strong> <code>Data successfully inserted/updated</code> atau <code>Channel successfully deleted</code>.</li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Duplikat Konfigurasi (1062):</strong> <code>Failed to insert/update data: A channel with this ID or configuration already exists.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pastikan Anda menggunakan ID yang unik atau ubah data channel yang sudah ada.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Dependensi (1451):</strong> <code>Cannot delete this channel because it is currently linked to existing merchant fee configurations or transactions.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda harus menonaktifkan channel atau menghapus referensi penggunaannya pada akun merchant sebelum Anda bisa menghapusnya.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Access Denied (1142):</strong> <code>Access Denied. You do not have sufficient database privileges.</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki izin modifikasi data. Silakan hubungi Database Administrator.</div>
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
                <a href="#faq_id_ch_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Merchant mendapat error "MAINTENANCE" pada metode pembayaran tertentu
                </a>
                <div id="faq_id_ch_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Semua transaksi untuk metode pembayaran tertentu (mis. BCA VA) gagal dengan error <code>MAINTENANCE</code> atau <code>SERVICE_NOT_ALLOWED</code>.<br><br>
                        <strong>Resolusi:</strong> Buka <strong>Settings → Channel Management</strong> dan periksa status toggle channel. Jika diatur ke <code>INACTIVE</code> dan provider upstream sebenarnya sehat, aktifkan kembali dengan mengklik toggle. Jika upstream memang sedang down, biarkan dinonaktifkan dan komunikasikan jendela maintenance kepada merchant yang terdampak.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ch_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Timeout intermittent dan error pada channel tertentu
                </a>
                <div id="faq_id_ch_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Transaksi melalui channel bank/agregator tertentu secara acak mengalami timeout atau mengembalikan error dari sisi provider.<br><br>
                        <strong>Resolusi:</strong> Ini menunjukkan koneksi upstream tidak stabil — bukan masalah gateway. <strong>Nonaktifkan channel segera</strong> untuk mencegah transaksi baru tersangkut di status PENDING. Pantau halaman status provider dan aktifkan kembali setelah mereka mengonfirmasi stabilitas. Transaksi PENDING yang terdampak akan diselesaikan oleh mekanisme polling status latar belakang.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_ch_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Channel yang baru diintegrasikan tidak muncul di daftar Channel
                </a>
                <div id="faq_id_ch_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Tim teknik telah menyelesaikan integrasi metode pembayaran baru, namun tidak muncul di dasbor Channel Management.<br><br>
                        <strong>Resolusi:</strong> Channel baru harus di-seed dengan benar ke tabel database <code>master_channels</code> DAN cache aplikasi/server harus dibersihkan (jalankan <code>php index.php cli/cache/clear</code>). Jika masih tidak muncul setelah membersihkan cache, verifikasi seeder berhasil dijalankan dan tidak ada error constraint database.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
