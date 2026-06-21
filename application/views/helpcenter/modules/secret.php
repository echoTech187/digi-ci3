<div id="module-secret" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">The Secret Key is the master credential that authenticates an External Merchant's API calls to the Gateway. This guide covers the complete lifecycle — from generation and secure delivery to revocation — and explains the Zero-Persistence security model behind it.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:30%">UI Element</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Generate Secret Key</strong> button</td><td class="p-3 border-0">Triggers server-side key generation. Button is only available for merchants with status <code>Active</code>. The raw key is shown once immediately after generation.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Countdown Timer</strong></td><td class="p-3 border-0">A live 24-hour countdown displayed on the secret key page. When it expires, the key becomes inaccessible and must be regenerated.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Revoke / Regenerate</strong> button</td><td class="p-3 border-0">Immediately invalidates the current Secret Key and issues a new one. The old key is permanently and irreversibly destroyed.</td></tr>
                        <tr><td class="p-3 border-0"><strong>IP Whitelist</strong> field</td><td class="p-3 border-0">Comma-separated IP addresses that are permitted to use this Secret Key. Requests from unlisted IPs are automatically blocked by the Gateway.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Security Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Security Architecture (Zero-Persistence Model)</h5>
            <p class="text-muted mb-4">The Digi-CI3 Gateway implements a strict "One-Time Read" architecture for Merchant Secret Keys. This ensures maximum security against lateral movement or database breaches.</p>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Zero-Persistence Policy</strong>
                    <p class="mb-0 text-muted small">Once generated, the raw Secret Key is <b>never</b> stored in the Gateway's database. Only a BCrypt hash is persisted for subsequent API authentication. If you close the page without copying the key, it is <strong>permanently lost</strong> and must be regenerated (which invalidates the old one).</p>
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mt-4 mb-4">
                <h5 class="font-weight-bold text-body mb-2">How the Key is Generated Internally:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-2"><strong>Generation:</strong> A high-entropy 64-character token is generated securely on the server using <code>random_bytes()</code>.</li>
                    <li class="mb-2"><strong>Hashing:</strong> The token is hashed via <code>password_hash()</code> (BCrypt) and stored in the <code>Merchant</code> table.</li>
                    <li class="mb-2"><strong>Flash Delivery:</strong> The raw token is passed to the frontend <em>exclusively</em> via CodeIgniter <code>flashdata</code> — a session variable that is automatically destroyed after a single page load.</li>
                    <li class="mb-2"><strong>24-Hour Window:</strong> The merchant has exactly <b>24 hours</b> to copy and configure the Secret Key. A live countdown enforces this on the frontend.</li>
                </ol>
            </div>

            <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Zero-Persistence Generation Flow</h6>
                <div class="mermaid">
                sequenceDiagram
                    actor Admin
                    participant S as Server
                    participant DB as Database
                    
                    Admin->>S: Request Generate Key
                    S->>S: Generate 64-char Raw Key
                    S->>S: Hash Key (BCrypt)
                    S->>DB: Save Hash Only
                    S->>Admin: Return Raw Key (via Session Flashdata)
                    Note over Admin,S: Raw key is destroyed from server memory.<br>Admin MUST copy it now.
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step Generate -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-key text-primary mr-2"></i> 2. Generating a Secret Key</h5>
            <p class="text-muted mb-4">Follow these steps precisely. The raw key is only displayed <strong>once</strong>.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Step-by-Step Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Merchant Management</strong> → locate the target merchant → click the action menu <i class="fas fa-ellipsis-v"></i> → <strong>Secret Key</strong>.</li>
                    <li class="mb-3">Click the <strong>Generate Secret Key</strong> button. The system will validate that the merchant status is <code>Active</code> before proceeding.</li>
                    <li class="mb-3">The page refreshes and displays the raw 64-character key in a highlighted box. <strong>Copy it immediately</strong> using the copy button or manually. There is no "show again" option.</li>
                    <li class="mb-3">Provide the key securely to the External Merchant's technical team via a secure, encrypted channel (never via plain email or chat). The countdown timer starts now.</li>
                    <li class="mb-2">Optionally, fill in the <strong>IP Whitelist</strong> field with the merchant's server IP addresses (comma-separated) to restrict which systems can use the key.</li>
                </ol>
            </div>

            <div class="doc-callout callout-important shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">UI Restriction: No "Back" Button</strong>
                    <p class="mb-0 text-muted small">To prevent accidental navigation before copying, the "Back to Merchant List" button is intentionally removed from the secret key display page. The developer must manually navigate away only after confirming the key has been securely stored.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Revoke -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-ban text-primary mr-2"></i> 3. Revoking or Regenerating a Key</h5>
            <p class="text-muted mb-4">Use revocation when a merchant's key is compromised, or when a merchant requests a key refresh.</p>

            <div class="pl-4 border-left border-danger ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Step-by-Step Workflow:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to the target merchant's <strong>Secret Key</strong> page.</li>
                    <li class="mb-3">Click <strong>Revoke / Regenerate Key</strong>. A confirmation popup will appear warning that the current key will be permanently destroyed.</li>
                    <li class="mb-3">Click <strong>Yes, Revoke It</strong>. The system immediately hashes and saves a new token, invalidating the previous one.</li>
                    <li class="mb-2">The new raw key is displayed. Communicate the key rotation to the merchant's team so they can update their integration immediately to avoid API downtime.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-skull-crossbones"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Revocation is Immediate & Irreversible</strong>
                    <p class="mb-0 text-muted small">The moment you confirm revocation, all live API calls using the old key will begin returning <code>INVALID_CREDENTIAL</code> errors. Coordinate with the merchant's technical team <em>before</em> revoking to schedule a maintenance window.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & What To Do</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_sec_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: The merchant says the key doesn't work (INVALID_CREDENTIAL)
                </a>
                <div id="faq_en_sec_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Cause:</strong> The merchant may have implemented the key incorrectly, or the 24-hour window has expired and the key is no longer valid in the system.<br><br>
                        <strong>Resolution:</strong> Check the Secret Key page to confirm whether the countdown has expired. If so, revoke and regenerate. Also confirm the merchant is sending the key in the correct request header (<code>X-Api-Secret</code>) and that their server IP is in the whitelist.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_sec_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: I closed the page before copying the key
                </a>
                <div id="faq_en_sec_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Cause:</strong> The raw key is delivered via flashdata and is destroyed on the next page load. There is no way to recover it from the database.<br><br>
                        <strong>Resolution:</strong> You must revoke the current key and generate a new one. Inform the merchant's team that the key has been rotated so they can update their integration.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_sec_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: The "Generate Key" button is grayed out / not clickable
                </a>
                <div id="faq_en_sec_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Cause:</strong> The merchant's account status is not <code>Active</code>. The system blocks key generation for Pending, Blocked, or Frozen merchants as a security measure.<br><br>
                        <strong>Resolution:</strong> Navigate to the Merchant profile and verify their status. If their KYC and onboarding is complete, change their status to <strong>Active</strong> first, then return to the Secret Key page.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">Secret Key adalah kredensial utama yang mengautentikasi panggilan API dari External Merchant ke Gateway. Panduan ini mencakup siklus hidup lengkap — mulai dari pembuatan dan pengiriman aman hingga pencabutan — dan menjelaskan model keamanan Zero-Persistence di baliknya.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Antarmuka (UI)</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:30%">Elemen UI</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Tombol Generate Secret Key</strong></td><td class="p-3 border-0">Memicu pembuatan key di sisi server. Hanya tersedia untuk merchant berstatus <code>Active</code>. Key mentah ditampilkan sekali langsung setelah generasi.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Countdown Timer</strong></td><td class="p-3 border-0">Hitungan mundur 24 jam langsung di halaman secret key. Ketika habis, key tidak lagi dapat diakses dan harus di-generate ulang.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tombol Revoke / Regenerate</strong></td><td class="p-3 border-0">Langsung mencabut Secret Key saat ini dan menerbitkan yang baru. Key lama dihancurkan secara permanen dan tidak dapat dipulihkan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Kolom IP Whitelist</strong></td><td class="p-3 border-0">Daftar alamat IP (dipisah koma) yang diizinkan menggunakan Secret Key ini. Permintaan dari IP yang tidak terdaftar akan diblokir otomatis oleh Gateway.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Security Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Arsitektur Keamanan (Model Zero-Persistence)</h5>
            <p class="text-muted mb-4">Digi-CI3 Gateway menerapkan arsitektur "One-Time Read" yang ketat untuk Secret Key Merchant, menjamin keamanan maksimal terhadap peretasan atau kebocoran database.</p>

            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Kebijakan Zero-Persistence</strong>
                    <p class="mb-0 text-muted small">Setelah di-generate, Secret Key mentah (raw) <b>tidak pernah</b> disimpan di database Gateway. Hanya hash BCrypt yang tersimpan untuk autentikasi API. Jika Anda menutup halaman sebelum menyalin key, key tersebut <strong>hilang selamanya</strong> dan harus di-generate ulang (yang berarti key lama langsung tidak valid).</p>
                </div>
            </div>

            <div class="pl-4 border-left border-primary ml-2 mt-4 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Cara Key Dibuat Secara Internal:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-2"><strong>Generation:</strong> Token 64 karakter dengan entropi tinggi di-generate aman di server menggunakan <code>random_bytes()</code>.</li>
                    <li class="mb-2"><strong>Hashing:</strong> Token di-hash via <code>password_hash()</code> (BCrypt) dan disimpan ke tabel <code>Merchant</code>.</li>
                    <li class="mb-2"><strong>Flash Delivery:</strong> Token mentah dikirim ke frontend <em>secara eksklusif</em> via CodeIgniter <code>flashdata</code> — variabel sesi yang otomatis hancur setelah satu kali page load.</li>
                    <li class="mb-2"><strong>Window 24 Jam:</strong> Merchant memiliki tepat <b>24 jam</b> untuk menyalin dan mengonfigurasi Secret Key. Countdown timer live mem-enforce batas ini di frontend.</li>
                </ol>
            </div>

            <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Alur Zero-Persistence Generation</h6>
                <div class="mermaid">
                sequenceDiagram
                    actor Admin
                    participant S as Server
                    participant DB as Database
                    
                    Admin->>S: Request Generate Key
                    S->>S: Buat 64-char Raw Key
                    S->>S: Hash Key (BCrypt)
                    S->>DB: Simpan Hash Saja
                    S->>Admin: Return Raw Key (via Session Flashdata)
                    Note over Admin,S: Raw key langsung dihapus dari memori server.<br>Admin WAJIB menyalinnya sekarang.
                </div>
            </div>
        </div>

        <!-- Section 2: Generate Steps -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-key text-primary mr-2"></i> 2. Membuat Secret Key</h5>
            <p class="text-muted mb-4">Ikuti langkah-langkah ini dengan tepat. Key mentah hanya ditampilkan <strong>satu kali</strong>.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Panduan Langkah-demi-Langkah:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke <strong>Merchant Management</strong> → cari merchant target → klik menu aksi <i class="fas fa-ellipsis-v"></i> → <strong>Secret Key</strong>.</li>
                    <li class="mb-3">Klik tombol <strong>Generate Secret Key</strong>. Sistem akan memvalidasi bahwa status merchant adalah <code>Active</code> sebelum melanjutkan.</li>
                    <li class="mb-3">Halaman refresh dan menampilkan raw key 64 karakter di kotak yang disorot. <strong>Salin segera</strong> menggunakan tombol salin atau secara manual. Tidak ada opsi "tampilkan lagi".</li>
                    <li class="mb-3">Sampaikan key kepada tim teknis External Merchant melalui saluran terenkripsi yang aman (jangan via email biasa atau chat). Countdown timer mulai berjalan sekarang.</li>
                    <li class="mb-2">Opsional: isi kolom <strong>IP Whitelist</strong> dengan alamat IP server merchant (dipisah koma) untuk membatasi sistem mana saja yang bisa menggunakan key ini.</li>
                </ol>
            </div>

            <div class="doc-callout callout-important shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Restriksi UI: Tidak Ada Tombol "Kembali"</strong>
                    <p class="mb-0 text-muted small">Untuk mencegah navigasi tidak sengaja sebelum key disalin, tombol "Kembali ke Daftar Merchant" sengaja dihilangkan dari halaman tampilan secret key. Developer wajib menyalin key secara manual sebelum menutup tab browser.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Revoke -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-ban text-primary mr-2"></i> 3. Mencabut atau Membuat Ulang Key</h5>
            <p class="text-muted mb-4">Gunakan revokasi saat key merchant dikompromikan, atau ketika merchant meminta rotasi key.</p>

            <div class="pl-4 border-left border-danger ml-2 mb-4">
                <h5 class="font-weight-bold text-body mb-2">Panduan Langkah-demi-Langkah:</h5>
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke halaman <strong>Secret Key</strong> merchant target.</li>
                    <li class="mb-3">Klik <strong>Revoke / Regenerate Key</strong>. Popup konfirmasi akan muncul untuk memperingatkan bahwa key saat ini akan dihancurkan secara permanen.</li>
                    <li class="mb-3">Klik <strong>Yes, Revoke It</strong>. Sistem langsung membuat hash token baru dan menyimpannya, sehingga key lama tidak valid seketika.</li>
                    <li class="mb-2">Key mentah baru ditampilkan. Komunikasikan rotasi key kepada tim merchant agar mereka bisa memperbarui integrasi sesegera mungkin untuk menghindari downtime API.</li>
                </ol>
            </div>

            <div class="doc-callout callout-error shadow-sm">
                <div class="callout-icon"><i class="fas fa-skull-crossbones"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Revokasi Bersifat Seketika & Tidak Dapat Dibatalkan</strong>
                    <p class="mb-0 text-muted small">Begitu Anda mengonfirmasi revokasi, semua panggilan API live yang menggunakan key lama akan langsung mengembalikan error <code>INVALID_CREDENTIAL</code>. Koordinasikan dengan tim teknis merchant <em>sebelum</em> melakukan revokasi untuk menjadwalkan jendela maintenance.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_sec_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Merchant bilang key tidak berfungsi (INVALID_CREDENTIAL)
                </a>
                <div id="faq_id_sec_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Penyebab:</strong> Merchant mungkin mengimplementasikan key secara salah, atau window 24 jam telah habis sehingga key tidak lagi valid di sistem.<br><br>
                        <strong>Resolusi:</strong> Cek halaman Secret Key untuk memastikan apakah countdown sudah expired. Jika iya, revoke dan generate ulang. Konfirmasi juga bahwa merchant mengirim key di header request yang benar (<code>X-Api-Secret</code>) dan IP server mereka sudah terdaftar di whitelist.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_sec_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Saya menutup halaman sebelum menyalin key
                </a>
                <div id="faq_id_sec_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Penyebab:</strong> Key mentah dikirim via flashdata dan dihancurkan pada page load berikutnya. Tidak ada cara untuk memulihkannya dari database.<br><br>
                        <strong>Resolusi:</strong> Anda harus melakukan revokasi key saat ini dan membuat yang baru. Informasikan kepada tim merchant bahwa key telah dirotasi agar mereka dapat memperbarui integrasi mereka.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_sec_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Tombol "Generate Key" abu-abu / tidak bisa diklik
                </a>
                <div id="faq_id_sec_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Penyebab:</strong> Status akun merchant bukan <code>Active</code>. Sistem memblokir pembuatan key untuk merchant Pending, Blocked, atau Frozen sebagai tindakan keamanan.<br><br>
                        <strong>Resolusi:</strong> Navigasikan ke profil Merchant dan verifikasi statusnya. Jika KYC dan onboarding mereka sudah selesai, ubah status menjadi <strong>Active</strong> terlebih dahulu, lalu kembali ke halaman Secret Key.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
