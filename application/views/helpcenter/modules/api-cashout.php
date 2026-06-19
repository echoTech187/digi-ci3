<div id="module-api-cashout" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-hand-holding-usd text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Cashout API Reference</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Technical documentation for developers integrating with the Cashout (Outbound/Disbursement) OpenAPI, including BI-Fast transfers.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-lock text-warning mr-2"></i> 1. Authentication & Security</h3>
        <p>The OpenAPI gateway uses a double-SHA256 hashing mechanism combined with IP Whitelisting.</p>

        <div class="doc-callout callout-important">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong>Signature Generation (Double Hashing) for Transfers</strong>
                <p>For Cashout transfer endpoints, the <code>signature</code> parameter must be formulated strictly according to this exact order:</p>
                <code class="d-block mt-2 mb-2 p-3 bg-dark text-white rounded">
                    hash1 = SHA256(requestId + transactionId + channelId + methodFee + amount + credentialKey)<br>
                    final_signature = SHA256(merchantId + hash1)
                </code>
            </div>
        </div>

        <h3 class="mb-4 mt-5"><i class="fas fa-search-dollar text-info mr-2"></i> 2. Account Inquiry</h3>
        <p>Before transferring, you can validate the beneficiary's bank account name via <code>/Transfer/inquiryAccount</code>.</p>
        
        <div class="doc-callout callout-note">
            <div class="callout-icon"><i class="fas fa-info-circle"></i></div>
            <div class="callout-content">
                <strong>Inquiry Signature Structure</strong>
                <code class="d-block mt-2 p-2 bg-dark text-white rounded">
                    hash1 = SHA256(requestId + credentialKey)<br>
                    signature = SHA256(merchantId + hash1)
                </code>
            </div>
        </div>
        <p class="mt-2 text-muted"><strong>Required Payload:</strong> <code>merchantId</code>, <code>requestId</code>, <code>channelId</code> (e.g., <code>bca</code>, <code>mandiri</code>), <code>accountNo</code>, and <code>signature</code>.</p>

        <h3 class="mb-4 mt-5"><i class="fas fa-paper-plane text-success mr-2"></i> 3. BI-Fast Transfer Execution</h3>
        <p>Endpoint: <code>/Transfer/bifast</code>. Used to disburse funds directly to a beneficiary's bank account.</p>

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
                    <td><code>requestId</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>Unique UUID per request (Max 100 chars). Idempotency enforced.</td>
                </tr>
                <tr>
                    <td><code>transactionId</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>Your internal invoice/billing ID (Max 100 chars).</td>
                </tr>
                <tr>
                    <td><code>channelId</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>The destination bank code (e.g., <code>bca</code>).</td>
                </tr>
                <tr>
                    <td><code>accountNo</code></td>
                    <td><span class="badge badge-required">Required (String)</span></td>
                    <td>The beneficiary's account number.</td>
                </tr>
                <tr>
                    <td><code>amount</code></td>
                    <td><span class="badge badge-required">Required (Int)</span></td>
                    <td>The transaction amount in IDR.</td>
                </tr>
                <tr>
                    <td><code>methodFee</code></td>
                    <td><span class="badge badge-required">Required (Enum)</span></td>
                    <td>Who pays the fee? Use <code>Merchant</code> (deducted from your balance) or <code>Destination</code> (deducted from amount sent).</td>
                </tr>
                <tr>
                    <td><code>transferNote</code></td>
                    <td><span class="badge badge-optional">Optional (String)</span></td>
                    <td>Max 16 chars (Alphanumeric and spaces only).</td>
                </tr>
            </tbody>
        </table>

        <h3 class="mb-4 mt-5"><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 4. Common Error Codes</h3>
        <ul class="text-muted" style="line-height: 1.8;">
            <li><strong>INSUFFICIENT_BALANCE</strong>: Your available settlement balance is lower than the amount + fees requested.</li>
            <li><strong>INVALID_SIGNATURE</strong>: The SHA256 hash does not match.</li>
            <li><strong>DOUBLE_TRANSACTION_ID</strong>: Transfer with this invoice ID was already triggered.</li>
            <li><strong>ACCOUNT_BLOCKED / FREEZE</strong>: The destination account has been blacklisted internally.</li>
        </ul>
    </div>
    
    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-hand-holding-usd text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Referensi API Cashout</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Dokumentasi teknis untuk developer yang berintegrasi dengan OpenAPI Cashout (Outbound/Disbursement), termasuk transfer BI-Fast.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-lock text-warning mr-2"></i> 1. Autentikasi & Keamanan</h3>
        <p>Gateway OpenAPI menggunakan mekanisme hashing ganda SHA256 yang dikombinasikan dengan Whitelist IP.</p>

        <div class="doc-callout callout-important">
            <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="callout-content">
                <strong>Pembuatan Signature (Double Hashing) untuk Transfer</strong>
                <code class="d-block mt-2 mb-2 p-3 bg-dark text-white rounded">
                    hash1 = SHA256(requestId + transactionId + channelId + methodFee + amount + credentialKey)<br>
                    final_signature = SHA256(merchantId + hash1)
                </code>
            </div>
        </div>

        <h3 class="mb-4 mt-5"><i class="fas fa-paper-plane text-success mr-2"></i> 2. Eksekusi Transfer BI-Fast</h3>
        <p>Endpoint: <code>/Transfer/bifast</code>. Digunakan untuk mencairkan dana langsung ke rekening bank penerima.</p>
        <p class="text-muted">Parameter yang wajib dikirimkan melalui POST Body (JSON) meliputi: <code>merchantId</code>, <code>requestId</code>, <code>transactionId</code>, <code>channelId</code> (kode bank), <code>accountNo</code>, <code>amount</code>, <code>methodFee</code> (opsi: <code>Merchant</code> atau <code>Destination</code>), dan <code>signature</code>.</p>

        <h3 class="mb-4 mt-5"><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 3. Kode Error Umum</h3>
        <ul class="text-muted" style="line-height: 1.8;">
            <li><strong>INSUFFICIENT_BALANCE</strong>: Saldo settlement Anda tidak mencukupi untuk nominal transfer + biaya admin.</li>
            <li><strong>INVALID_SIGNATURE</strong>: Hash SHA256 tidak cocok. Periksa urutan string.</li>
            <li><strong>DOUBLE_TRANSACTION_ID</strong>: Transfer dengan ID invoice ini sudah diproses sebelumnya.</li>
            <li><strong>ACCOUNT_BLOCKED</strong>: Rekening tujuan masuk daftar hitam internal (Blacklist).</li>
        </ul>
    </div>
</div>
