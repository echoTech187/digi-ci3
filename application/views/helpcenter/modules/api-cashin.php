<div id="module-api-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">Technical documentation for developers integrating with the Cashin (Inbound) OpenAPI. Covers the authentication model, QRIS dynamic & recurring endpoints, Virtual Account (VA) generation, and E-Wallet payment links — with request/response structures and troubleshooting for the most common integration errors.</p>

        <hr class="my-4">

        <!-- Section 1: Authentication -->
        <!-- Section 1: Authentication -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Authentication & Security</h3>
                <p class="text-muted mb-4">Every API call must be authenticated using a Double-SHA256 signature combined with IP Whitelisting. Requests from non-whitelisted IPs are immediately rejected before the signature is even evaluated.</p>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Signature Generation (Double Hashing)</strong>
                        <p class="text-muted small mb-2">For all Cashin generation endpoints, compute the <code>signature</code> field in the JSON body as follows:</p>
                        <code class="d-block mt-2 mb-3 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(subMerchantId + requestId + transactionId + amount + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <ul class="mb-0 pl-3 text-muted small">
                            <li class="mb-1"><code>credentialKey</code>: Your private secret key obtained from the Merchant Secret Key page in the Admin Dashboard.</li>
                            <li class="mb-1">All values must be concatenated as <strong>strings</strong> (no separators, no spaces) before hashing.</li>
                            <li>If you are not using Sub-Merchants, pass <code>subMerchantId</code> as <code>0</code> (integer zero as string: <code>"0"</code>).</li>
                        </ul>
                    </div>
                </div>

                <!-- Auth steps -->
                <div class="mt-4 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Pre-Integration Checklist:</h5>
                    <ol class="text-muted hc-step-desc mb-0">
                        <li class="mb-2">Obtain your <code>merchantId</code> and <code>credentialKey</code> from the <strong>Secret Key</strong> page in the Admin Dashboard.</li>
                        <li class="mb-2">Register your server's static IP address in the <strong>IP Whitelist</strong> field on the same page. Failure to do this will result in <code>ACCESS_DENIED</code> errors.</li>
                        <li class="mb-2">Build the signature using the exact parameter order above — order matters. A single character difference produces a completely different hash.</li>
                        <li class="mb-2">Set the <code>Content-Type: application/json</code> header on all requests.</li>
                    </ol>
                </div>

                <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                    <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Double-SHA256 Authentication Flow</h6>
                    <div class="mermaid">
                    sequenceDiagram
                        autonumber
                        participant M as Merchant System
                        participant G as Gidi Gateway
                        participant B as Banking Network
                        
                        M->>M: Concatenate params (subMerchantId+requestId+...)
                        M->>M: Hash 1 = SHA256(params + credentialKey)
                        M->>M: Signature = SHA256(merchantId + Hash 1)
                        M->>G: POST /generate with Signature & IP
                        
                        alt IP Not Whitelisted
                            G-->>M: HTTP 403 (ACCESS_DENIED)
                        else IP Whitelisted
                            G->>G: Gateway calculates expected Signature
                            alt Signature Mismatch
                                G-->>M: HTTP 401 (INVALID_SIGNATURE)
                            else Signature Valid
                                G->>B: Forward Transaction
                                B-->>G: Bank Response (QRIS/VA)
                                G-->>M: HTTP 200 (SUCCESS)
                            end
                        end
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: QRIS Endpoints -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">QRIS Endpoints</h3>
                <p class="text-muted mb-3">Two QRIS endpoints are available. Use <strong>Dynamic</strong> for one-time payments with a fixed amount and expiry. Use <strong>Recurring</strong> for reusable QR codes linked to a merchant's account.</p>

                <div class="table-responsive mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:25%">Endpoint</th>
                                <th class="p-3 border-0">Method</th>
                                <th class="p-3 border-0">Use Case</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>/QrisMpm/generateDynamic</code></td><td class="p-3 border-0"><span class="badge badge-required">POST</span></td><td class="p-3 border-0">Generate a one-time QRIS with a fixed amount and expiry time. Ideal for e-commerce checkouts.</td></tr>
                            <tr><td class="p-3 border-0"><code>/QrisMpm/generateRecurring</code></td><td class="p-3 border-0"><span class="badge badge-required">POST</span></td><td class="p-3 border-0">Generate a persistent QRIS linked to a sub-merchant account for repeated use (POS terminals, print-and-display).</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:28%">Parameter</th>
                                <th class="p-3 border-0" style="width:25%">Type</th>
                                <th class="p-3 border-0">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>merchantId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (Int)</span></td><td class="p-3 border-0">Your Master Merchant ID from the dashboard.</td></tr>
                            <tr><td class="p-3 border-0"><code>subMerchantId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (Int)</span></td><td class="p-3 border-0">Sub-Merchant ID (max 12 chars). Pass <code>0</code> if not using sub-merchants.</td></tr>
                            <tr><td class="p-3 border-0"><code>requestId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">Unique identifier per API call (max 100 chars). Use UUID v4. The gateway blocks duplicate Request IDs — never reuse.</td></tr>
                            <tr><td class="p-3 border-0"><code>transactionId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">Your internal billing/invoice ID (max 100 chars). Must be unique per transaction.</td></tr>
                            <tr><td class="p-3 border-0"><code>amount</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (Int)</span></td><td class="p-3 border-0">Transaction amount in IDR (no decimal). Must be within configured Min/Max limits.</td></tr>
                            <tr><td class="p-3 border-0"><code>datetimeExpired</code></td><td class="p-3 border-0"><span class="badge badge-optional">Optional (String)</span></td><td class="p-3 border-0">Format: <code>YYYY-MM-DD HH:MM:SS</code>. If omitted, defaults to current time + 4 hours.</td></tr>
                            <tr><td class="p-3 border-0"><code>signature</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">The Double-SHA256 signature hash computed per Section 1.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-callout callout-note shadow-sm">
                    <div class="callout-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Success Response (HTTP 200)</strong>
                        <code class="d-block mt-2 mb-2 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
{<br>
&nbsp;&nbsp;"responseCode": "SUCCESS",<br>
&nbsp;&nbsp;"responseMessage": "Request Successful",<br>
&nbsp;&nbsp;"responseDetail": {<br>
&nbsp;&nbsp;&nbsp;&nbsp;"statusGenerate": "Created",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"transactionId": "INV-12345",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"datetimeExpired": "2026-06-19 19:00:00",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"rawData": "00020101021226... (The QR String)"<br>
&nbsp;&nbsp;}<br>
}
                        </code>
                        <p class="text-muted small mb-0">The <code>rawData</code> field contains the QRIS string. Render this into a QR code image on your payment page using any standard QRIS library.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Error Codes -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Common Error Codes</h3>
                <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:32%">Error Code</th>
                                <th class="p-3 border-0">Cause & Fix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>INVALID_SIGNATURE</code></td><td class="p-3 border-0">SHA256 hash mismatch. Check the exact parameter concatenation order. Ensure all values are strings, no type coercion.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_REQUEST_ID</code></td><td class="p-3 border-0">This <code>requestId</code> was already used. Always generate a fresh UUID v4 for every new API call.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_TRANSACTION_ID</code></td><td class="p-3 border-0">A transaction with this <code>transactionId</code> already exists. Use a unique invoice ID per transaction.</td></tr>
                            <tr><td class="p-3 border-0"><code>SERVICE_NOT_ALLOWED</code></td><td class="p-3 border-0">Your account is inactive, or the specific Cashin channel (QRIS/VA/E-Wallet) has not been enabled in the Admin Dashboard.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCESS_DENIED</code></td><td class="p-3 border-0">Your server IP is not in the Whitelist. Ask your admin to add it on the Secret Key settings page.</td></tr>
                            <tr><td class="p-3 border-0"><code>AMOUNT_NOT_ALLOWED</code></td><td class="p-3 border-0">The requested amount is below the minimum or above the maximum configured for your merchant account.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Common Integration Questions</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: I keep getting INVALID_SIGNATURE even though I'm using the correct key</span>
            </div>
            <div class="hc-faq-a">
                <strong>Most Common Cause:</strong> Parameter order in the concatenation is wrong, or one of the values is being passed as an integer instead of a string.<br><br>
                <strong>Debugging steps:</strong>
                <ol class="mt-2">
                    <li>Print the raw concatenated string <em>before</em> hashing: <code>subMerchantId + requestId + transactionId + amount + credentialKey</code></li>
                    <li>Ensure all values are coerced to strings (e.g., amount <code>50000</code> must be the string <code>"50000"</code>, not the integer <code>50000</code>).</li>
                    <li>Hash with SHA256 to get <code>hash1</code>, then concatenate <code>merchantId + hash1</code> and hash again.</li>
                    <li>If still failing, regenerate the Secret Key from the dashboard — the old key may have been rotated or expired.</li>
                </ol>
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: How do I know if a payment has been completed (webhook / callback)?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Answer:</strong> The gateway sends a POST callback to your configured <strong>Callback URL</strong> when the transaction status changes to <code>SUCCESS</code>. The callback payload includes <code>transactionId</code>, <code>amount</code>, <code>status</code>, and a verification signature.<br><br>
                Make sure your callback endpoint:<ul class="mt-2"><li>Returns HTTP <code>200 OK</code> upon successful receipt — otherwise the gateway will retry.</li><li>Is publicly accessible (not behind a firewall or localhost).</li><li>Validates the callback signature before marking the order as paid.</li></ul>
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: What happens if my customer pays after the QRIS has expired?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Answer:</strong> Payments made after the <code>datetimeExpired</code> are automatically rejected at the payment provider level. The transaction status is updated to <code>EXPIRED</code> and no funds are transferred.<br><br>
                If a customer accidentally pays after expiry, the payment provider typically reverses the funds to the payer's account within 1–3 business days. You do not need to take any manual action on the gateway side.
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">Dokumentasi teknis untuk developer yang berintegrasi dengan OpenAPI Cashin (Inbound). Mencakup model autentikasi, endpoint QRIS dynamic & recurring, pembuatan Virtual Account (VA), dan payment link E-Wallet — lengkap dengan struktur request/response dan panduan troubleshooting error integrasi yang paling umum.</p>

        <hr class="my-4">

        <!-- Section 1: Auth -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Autentikasi & Keamanan</h3>
                <p class="text-muted mb-4">Setiap panggilan API harus diautentikasi menggunakan signature Double-SHA256 yang dikombinasikan dengan Whitelist IP. Permintaan dari IP yang tidak terdaftar langsung ditolak sebelum signature dievaluasi.</p>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Pembuatan Signature (Double Hashing)</strong>
                        <p class="text-muted small mb-2">Untuk semua endpoint Cashin, hitung field <code>signature</code> di JSON body sebagai berikut:</p>
                        <code class="d-block mt-2 mb-3 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(subMerchantId + requestId + transactionId + amount + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <ul class="mb-0 pl-3 text-muted small">
                            <li class="mb-1"><code>credentialKey</code>: Secret Key pribadi Anda dari halaman Secret Key di Admin Dashboard.</li>
                            <li class="mb-1">Semua nilai harus digabungkan sebagai <strong>string</strong> (tanpa pemisah, tanpa spasi) sebelum di-hash.</li>
                            <li>Jika tidak menggunakan Sub-Merchant, kirimkan <code>subMerchantId</code> sebagai <code>"0"</code> (string nol).</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Daftar Periksa Pra-Integrasi:</h5>
                    <ol class="text-muted hc-step-desc mb-0">
                        <li class="mb-2">Dapatkan <code>merchantId</code> dan <code>credentialKey</code> dari halaman <strong>Secret Key</strong> di Admin Dashboard.</li>
                        <li class="mb-2">Daftarkan IP statis server Anda di kolom <strong>IP Whitelist</strong> pada halaman yang sama. Kegagalan ini akan mengakibatkan error <code>ACCESS_DENIED</code>.</li>
                        <li class="mb-2">Bangun signature menggunakan urutan parameter yang persis seperti di atas — urutannya penting. Satu karakter berbeda menghasilkan hash yang sepenuhnya berbeda.</li>
                        <li class="mb-2">Atur header <code>Content-Type: application/json</code> pada semua request.</li>
                    </ol>
                </div>

                <div class="mb-4 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                    <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Alur Autentikasi Double-SHA256</h6>
                    <div class="mermaid">
                    sequenceDiagram
                        autonumber
                        participant M as Sistem Merchant
                        participant G as Gidi Gateway
                        participant B as Jaringan Bank
                        
                        M->>M: Gabungkan param (subMerchantId+requestId+...)
                        M->>M: Hash 1 = SHA256(params + credentialKey)
                        M->>M: Signature = SHA256(merchantId + Hash 1)
                        M->>G: POST /generate dengan Signature & IP
                        
                        alt IP Tidak Terdaftar
                            G-->>M: HTTP 403 (ACCESS_DENIED)
                        else IP Terdaftar
                            G->>G: Gateway menghitung ekspektasi Signature
                            alt Signature Tidak Cocok
                                G-->>M: HTTP 401 (INVALID_SIGNATURE)
                            else Signature Valid
                                G->>B: Teruskan Transaksi
                                B-->>G: Respons Bank (QRIS/VA)
                                G-->>M: HTTP 200 (SUCCESS)
                            end
                        end
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: QRIS Endpoints -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Endpoint QRIS</h3>
                <p class="text-muted mb-3">Dua endpoint QRIS tersedia. Gunakan <strong>Dynamic</strong> untuk pembayaran sekali pakai dengan nominal tetap dan waktu kadaluarsa. Gunakan <strong>Recurring</strong> untuk QR yang dapat digunakan berulang kali dan terhubung ke akun merchant.</p>

                <div class="table-responsive mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:25%">Endpoint</th>
                                <th class="p-3 border-0">Metode</th>
                                <th class="p-3 border-0">Kasus Penggunaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>/QrisMpm/generateDynamic</code></td><td class="p-3 border-0"><span class="badge badge-required">POST</span></td><td class="p-3 border-0">Buat QRIS sekali pakai dengan nominal tetap dan waktu kadaluarsa. Ideal untuk checkout e-commerce.</td></tr>
                            <tr><td class="p-3 border-0"><code>/QrisMpm/generateRecurring</code></td><td class="p-3 border-0"><span class="badge badge-required">POST</span></td><td class="p-3 border-0">Buat QRIS persisten yang terhubung ke akun sub-merchant untuk penggunaan berulang (terminal POS, cetak-dan-tampilkan).</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:28%">Parameter</th>
                                <th class="p-3 border-0" style="width:25%">Tipe</th>
                                <th class="p-3 border-0">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>merchantId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (Int)</span></td><td class="p-3 border-0">ID Master Merchant Anda dari dashboard.</td></tr>
                            <tr><td class="p-3 border-0"><code>subMerchantId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (Int)</span></td><td class="p-3 border-0">ID Sub-Merchant (maks 12 karakter). Kirim <code>0</code> jika tidak menggunakan sub-merchant.</td></tr>
                            <tr><td class="p-3 border-0"><code>requestId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">Pengenal unik per panggilan API (maks 100 karakter). Gunakan UUID v4. Gateway memblokir Request ID duplikat — jangan pernah digunakan ulang.</td></tr>
                            <tr><td class="p-3 border-0"><code>transactionId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">ID tagihan/invoice internal Anda (maks 100 karakter). Harus unik per transaksi.</td></tr>
                            <tr><td class="p-3 border-0"><code>amount</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (Int)</span></td><td class="p-3 border-0">Nominal transaksi dalam IDR (tanpa desimal). Harus dalam batas Min/Max yang dikonfigurasi.</td></tr>
                            <tr><td class="p-3 border-0"><code>datetimeExpired</code></td><td class="p-3 border-0"><span class="badge badge-optional">Opsional (String)</span></td><td class="p-3 border-0">Format: <code>YYYY-MM-DD HH:MM:SS</code>. Jika tidak diisi, default ke waktu saat ini + 4 jam.</td></tr>
                            <tr><td class="p-3 border-0"><code>signature</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">Hash signature Double-SHA256 yang dihitung sesuai Bagian 1.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-callout callout-note shadow-sm">
                    <div class="callout-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Contoh Response Sukses (HTTP 200)</strong>
                        <code class="d-block mt-2 mb-2 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
{<br>
&nbsp;&nbsp;"responseCode": "SUCCESS",<br>
&nbsp;&nbsp;"responseMessage": "Request Successful",<br>
&nbsp;&nbsp;"responseDetail": {<br>
&nbsp;&nbsp;&nbsp;&nbsp;"statusGenerate": "Created",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"transactionId": "INV-12345",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"datetimeExpired": "2026-06-19 19:00:00",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"rawData": "00020101021226... (QR String)"<br>
&nbsp;&nbsp;}<br>
}
                        </code>
                        <p class="text-muted small mb-0">Field <code>rawData</code> berisi string QRIS. Render menjadi gambar QR code di halaman pembayaran Anda menggunakan library QRIS standar apa pun.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Error Codes -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Kode Error Umum</h3>
                <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width:32%">Kode Error</th>
                                <th class="p-3 border-0">Penyebab & Solusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><code>INVALID_SIGNATURE</code></td><td class="p-3 border-0">Hash SHA256 tidak cocok. Periksa urutan konkatenasi parameter. Pastikan semua nilai berupa string, tidak ada konversi tipe.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_REQUEST_ID</code></td><td class="p-3 border-0"><code>requestId</code> ini sudah pernah digunakan. Selalu buat UUID v4 baru untuk setiap panggilan API baru.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_TRANSACTION_ID</code></td><td class="p-3 border-0">Transaksi dengan <code>transactionId</code> ini sudah ada. Gunakan ID invoice unik per transaksi.</td></tr>
                            <tr><td class="p-3 border-0"><code>SERVICE_NOT_ALLOWED</code></td><td class="p-3 border-0">Akun Anda tidak aktif, atau kanal Cashin spesifik (QRIS/VA/E-Wallet) belum diaktifkan di Admin Dashboard.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCESS_DENIED</code></td><td class="p-3 border-0">IP server Anda tidak ada di Whitelist. Minta admin Anda untuk menambahkannya di halaman pengaturan Secret Key.</td></tr>
                            <tr><td class="p-3 border-0"><code>AMOUNT_NOT_ALLOWED</code></td><td class="p-3 border-0">Nominal yang diminta berada di bawah minimum atau di atas maksimum yang dikonfigurasi untuk akun merchant Anda.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Pertanyaan Integrasi Umum (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Terus mendapat INVALID_SIGNATURE meski sudah menggunakan key yang benar</span>
            </div>
            <div class="hc-faq-a">
                <strong>Penyebab Paling Umum:</strong> Urutan parameter dalam konkatenasi salah, atau salah satu nilai dikirim sebagai integer bukan string.<br><br>
                <strong>Langkah debugging:</strong>
                <ol class="mt-2">
                    <li>Print string konkatenasi mentah <em>sebelum</em> di-hash: <code>subMerchantId + requestId + transactionId + amount + credentialKey</code></li>
                    <li>Pastikan semua nilai dikonversi ke string (mis. amount <code>50000</code> harus berupa string <code>"50000"</code>, bukan integer <code>50000</code>).</li>
                    <li>Hash dengan SHA256 untuk mendapat <code>hash1</code>, lalu gabungkan <code>merchantId + hash1</code> dan hash lagi.</li>
                    <li>Jika masih gagal, regenerate Secret Key dari dashboard — key lama mungkin sudah dirotasi atau expired.</li>
                </ol>
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Bagaimana cara mengetahui pembayaran sudah selesai (callback/webhook)?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Jawaban:</strong> Gateway mengirimkan callback POST ke <strong>Callback URL</strong> yang Anda konfigurasi saat status transaksi berubah menjadi <code>SUCCESS</code>. Payload callback menyertakan <code>transactionId</code>, <code>amount</code>, <code>status</code>, dan signature verifikasi.<br><br>
                Pastikan endpoint callback Anda:<ul class="mt-2"><li>Mengembalikan HTTP <code>200 OK</code> setelah berhasil menerima — jika tidak, gateway akan mencoba ulang.</li><li>Dapat diakses publik (tidak di balik firewall atau localhost).</li><li>Memvalidasi signature callback sebelum menandai pesanan sebagai lunas.</li></ul>
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Apa yang terjadi jika pelanggan membayar setelah QRIS kadaluarsa?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Jawaban:</strong> Pembayaran yang dilakukan setelah <code>datetimeExpired</code> secara otomatis ditolak di level penyedia pembayaran. Status transaksi diperbarui menjadi <code>EXPIRED</code> dan tidak ada dana yang ditransfer.<br><br>
                Jika pelanggan tidak sengaja membayar setelah kadaluarsa, penyedia pembayaran biasanya mengembalikan dana ke akun pembayar dalam 1–3 hari kerja. Anda tidak perlu mengambil tindakan manual apa pun di sisi gateway.
            </div>
        </div>
    </div>
</div>
