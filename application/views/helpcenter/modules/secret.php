<div id="module-secret" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Secret Key Management</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Technical overview of how Secret Keys are generated, secured, and distributed to External Merchants.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Security Architecture</h3>
            <p class="text-muted mb-4">The Digi-CI3 Gateway implements a strict "One-Time Read" architecture for Merchant Secret Keys. This ensures maximum security against lateral movement or database breaches.</p>
            
            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Zero-Persistence Policy</strong>
                    <p class="mb-0 text-muted small">Once generated, the raw Secret Key is <b>never</b> stored in the Gateway's database. Only a BCrypt hash is persisted for subsequent API authentication.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cogs text-primary mr-2"></i> 2. Generation & Expiry Workflow</h3>
            <p class="text-muted mb-3">When a new Secret Key is issued to an External Merchant, the following workflow occurs:</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2"><b>Generation:</b> A high-entropy 64-character token is generated securely on the server.</li>
                <li class="mb-2"><b>Hashing:</b> The token is hashed via <code>password_hash()</code> and stored in the <code>Merchant</code> table.</li>
                <li class="mb-2"><b>Flash Delivery:</b> The raw token is passed to the frontend exclusively via CodeIgniter <code>flashdata</code>.</li>
                <li class="mb-2"><b>Temporary Access Window:</b> The merchant has exactly <b>24 hours</b> from the time of generation to copy and configure the Secret Key. A live countdown enforces this limit on the frontend.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-code text-primary mr-2"></i> 3. API Authentication Requirements</h3>
            <p class="text-muted mb-3">External Merchants must use the Secret Key in the authorization headers of all API requests pointing to the Gateway.</p>
            
            <div class="bg-light p-3 rounded border">
                <code>
                // Example HTTP Request Header<br>
                POST /api/v1/transaction/create HTTP/1.1<br>
                Host: gateway.digi-ci3.local<br>
                X-Api-Key: MCH-123456789<br>
                <b>X-Api-Secret: eyJ0eXAiOiJKV...</b>
                </code>
            </div>
            
            <div class="doc-callout callout-important mt-4 shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">UI Restrictions</strong>
                    <p class="mb-0 text-muted small">To prevent accidental navigation before copying, the "Back to Merchant List" button is purposely removed from the secret key display page. The developer must manually copy the key.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Manajemen Secret Key</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Tinjauan teknis tentang bagaimana Secret Key di-generate, diamankan, dan didistribusikan ke External Merchant.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Arsitektur Keamanan</h3>
            <p class="text-muted mb-4">Digi-CI3 Gateway menerapkan arsitektur "One-Time Read" yang ketat untuk Secret Key Merchant. Hal ini menjamin keamanan maksimal terhadap peretasan database.</p>
            
            <div class="doc-callout callout-warning shadow-sm">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kebijakan Zero-Persistence</strong>
                    <p class="mb-0 text-muted small">Setelah di-generate, Secret Key mentah (raw) <b>tidak pernah</b> disimpan di dalam database. Gateway hanya menyimpan hasil hash (menggunakan BCrypt) untuk keperluan autentikasi API.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cogs text-primary mr-2"></i> 2. Alur Generation & Expiry</h3>
            <p class="text-muted mb-3">Ketika Secret Key baru diterbitkan, alur berikut akan dieksekusi:</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2"><b>Generation:</b> Token dengan entropi tinggi (64 karakter) di-generate secara aman di sisi server.</li>
                <li class="mb-2"><b>Hashing:</b> Token di-hash melalui fungsi <code>password_hash()</code> lalu disimpan ke tabel <code>Merchant</code>.</li>
                <li class="mb-2"><b>Flash Delivery:</b> Token mentah dikirim ke antarmuka (frontend) secara eksklusif via CodeIgniter <code>flashdata</code>.</li>
                <li class="mb-2"><b>Waktu Akses Terbatas:</b> Merchant memiliki waktu tepat <b>24 jam</b> sejak di-generate untuk menyalin dan melakukan konfigurasi Secret Key di sistem mereka. *Countdown timer* live akan enforce limitasi ini di antarmuka web.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-code text-primary mr-2"></i> 3. Kebutuhan Autentikasi API</h3>
            <p class="text-muted mb-3">Merchant Eksternal wajib menyertakan Secret Key ke dalam *header* autorisasi pada setiap *request* API yang mengarah ke Gateway.</p>
            
            <div class="bg-light p-3 rounded border">
                <code>
                // Contoh HTTP Request Header<br>
                POST /api/v1/transaction/create HTTP/1.1<br>
                Host: gateway.digi-ci3.local<br>
                X-Api-Key: MCH-123456789<br>
                <b>X-Api-Secret: eyJ0eXAiOiJKV...</b>
                </code>
            </div>
            
            <div class="doc-callout callout-important mt-4 shadow-sm">
                <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Restriksi Antarmuka (UI)</strong>
                    <p class="mb-0 text-muted small">Untuk mencegah navigasi tidak sengaja sebelum key disalin, tombol "Kembali ke Daftar Merchant" sengaja dihilangkan dari halaman display secret key. Developer wajib menyalin key secara manual sebelum menutup tab browser.</p>
                </div>
            </div>
        </div>
    </div>
</div>
