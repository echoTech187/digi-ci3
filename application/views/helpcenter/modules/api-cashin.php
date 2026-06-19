<div id="module-api-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-code text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Cashin API Reference</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Technical documentation for developers integrating with the Cashin (Inbound) OpenAPI, including QRIS, Virtual Accounts (VA), and E-Wallet.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-lock text-warning mr-2"></i> 1. Authentication & Security</h3>
        <p>The OpenAPI gateway uses a robust double-SHA256 hashing mechanism combined with IP Whitelisting.</p>

        <div class="doc-callout callout-important">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong>Signature Generation (Double Hashing)</strong>
                <p>For all Cashin generation endpoints, the <code>signature</code> parameter in the JSON body must be formulated as follows:</p>
                <code class="d-block mt-2 mb-2 p-3 bg-dark text-white rounded">
                    hash1 = SHA256(subMerchantId + requestId + transactionId + amount + credentialKey)<br>
                    final_signature = SHA256(merchantId + hash1)
                </code>
                <ul class="mb-0 pl-3">
                    <li><code>credentialKey</code>: Your private secret key obtained from the Admin Dashboard.</li>
                    <li>If you are not using Sub-Merchants, ensure <code>subMerchantId</code> is still correctly passed as <code>0</code> or an empty string depending on your account setup.</li>
                </ul>
            </div>
        </div>

        <h3 class="mb-4 mt-5"><i class="fas fa-qrcode text-info mr-2"></i> 2. QRIS Endpoints</h3>
        <p>Endpoints located at <code>/QrisMpm/generateDynamic</code> and <code>/QrisMpm/generateRecurring</code>.</p>

        <table class="doc-table">
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>merchantId</code></td>
                    <td><span class="badge badge-required">Required (Int)</span></td>
                    <td>Your Master Merchant ID.</td>
                </tr>
                <tr>
                    <td><code>subMerchantId</code></td>
                    <td><span class="badge badge-required">Required (Int)</span></td>
                    <td>Your Sub-Merchant ID (Max 12 chars).</td>
                </tr>
                <tr>
                    <td><code>requestId</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>Unique identifier for this specific API hit (Max 100 chars). The gateway blocks duplicate Request IDs.</td>
                </tr>
                <tr>
                    <td><code>transactionId</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>Your internal system's billing or invoice ID (Max 100 chars).</td>
                </tr>
                <tr>
                    <td><code>amount</code></td>
                    <td><span class="badge badge-required">Required (Int)</span></td>
                    <td>The transaction amount in IDR. Must pass Min/Max limits.</td>
                </tr>
                <tr>
                    <td><code>datetimeExpired</code></td>
                    <td><span class="badge badge-optional">Optional (String)</span></td>
                    <td>Format: <code>YYYY-MM-DD HH:MM:SS</code>. If omitted, defaults to current time + 4 hours.</td>
                </tr>
                <tr>
                    <td><code>signature</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>The SHA256 signature hash.</td>
                </tr>
            </tbody>
        </table>

        <div class="doc-callout callout-note">
            <div class="callout-icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="callout-content">
                <strong>Response Structure</strong>
                <p>A successful request (HTTP 200) will return:</p>
                <code class="d-block mt-2 mb-2 p-3 bg-dark text-white rounded">
{<br>
&nbsp;&nbsp;"responseCode": "SUCCESS",<br>
&nbsp;&nbsp;"responseMessage": "Request Successful",<br>
&nbsp;&nbsp;"responseDetail": {<br>
&nbsp;&nbsp;&nbsp;&nbsp;"statusGenerate": "Created",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"transactionId": "INV-12345",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"datetimeExpired": "2026-06-19 19:00:00",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"rawData": "00020101021226... (The QR String or URL)"<br>
&nbsp;&nbsp;}<br>
}
                </code>
            </div>
        </div>

        <h3 class="mb-4 mt-5"><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 3. Common Error Codes</h3>
        <ul class="text-muted" style="line-height: 1.8;">
            <li><strong>INVALID_SIGNATURE</strong>: The SHA256 hash does not match. Check your parameter order and <code>credentialKey</code>.</li>
            <li><strong>DOUBLE_REQUEST_ID</strong>: You have already used this <code>requestId</code>. Generate a new UUID.</li>
            <li><strong>DOUBLE_TRANSACTION_ID</strong>: A transaction with this invoice ID already exists.</li>
            <li><strong>SERVICE_NOT_ALLOWED</strong>: Your account is either inactive, or the specific Cashin channel hasn't been configured in the Dashboard.</li>
            <li><strong>ACCESS_DENIED</strong>: Your server's IP is not registered in the Whitelist IP settings.</li>
        </ul>
    </div>
    
    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-code text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Referensi API Cashin</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Dokumentasi teknis untuk developer yang berintegrasi dengan OpenAPI Cashin (Inbound), termasuk QRIS, Virtual Account (VA), dan E-Wallet.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-lock text-warning mr-2"></i> 1. Autentikasi & Keamanan</h3>
        <p>Gateway OpenAPI menggunakan mekanisme hashing ganda SHA256 yang dikombinasikan dengan Whitelist IP.</p>

        <div class="doc-callout callout-important">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong>Pembuatan Signature (Double Hashing)</strong>
                <p>Untuk semua endpoint Cashin, parameter <code>signature</code> pada payload JSON harus diformulasikan sebagai berikut:</p>
                <code class="d-block mt-2 mb-2 p-3 bg-dark text-white rounded">
                    hash1 = SHA256(subMerchantId + requestId + transactionId + amount + credentialKey)<br>
                    final_signature = SHA256(merchantId + hash1)
                </code>
            </div>
        </div>

        <h3 class="mb-4 mt-5"><i class="fas fa-qrcode text-info mr-2"></i> 2. Endpoint QRIS</h3>
        <p>Endpoint terletak pada <code>/QrisMpm/generateDynamic</code> dan <code>/QrisMpm/generateRecurring</code>.</p>
        
        <p class="text-muted">Parameter yang wajib dikirimkan melalui POST Body (JSON) meliputi: <code>merchantId</code>, <code>subMerchantId</code>, <code>requestId</code>, <code>transactionId</code>, <code>amount</code>, dan <code>signature</code>. <code>datetimeExpired</code> bersifat opsional (default +4 jam dari waktu request).</p>

        <h3 class="mb-4 mt-5"><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 3. Kode Error Umum</h3>
        <ul class="text-muted" style="line-height: 1.8;">
            <li><strong>INVALID_SIGNATURE</strong>: Hash SHA256 tidak cocok. Periksa urutan parameter dan <code>credentialKey</code>.</li>
            <li><strong>DOUBLE_REQUEST_ID</strong>: ID Request sudah pernah digunakan.</li>
            <li><strong>DOUBLE_TRANSACTION_ID</strong>: ID transaksi (Invoice) sudah terdaftar.</li>
            <li><strong>SERVICE_NOT_ALLOWED</strong>: Akun belum aktif atau kanal belum diatur di Dashboard.</li>
            <li><strong>ACCESS_DENIED</strong>: IP server Anda tidak terdaftar di Whitelist.</li>
        </ul>
    </div>
</div>
