<div id="module-api-cashout" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">Technical documentation for developers integrating with the Cashout (Outbound/Disbursement) OpenAPI. Covers balance inquiry, account name verification, BI-Fast transfer execution, and status check endpoints — with request/response structures and critical error troubleshooting for real-money disbursement flows.</p>

        <hr class="my-4">

        <!-- Section 1: Auth -->
        <!-- Section 1: Auth -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Authentication & Security</h3>
                <p class="text-muted mb-4">The Cashout API uses the same Double-SHA256 signature model as Cashin, but with a <strong>different concatenation order</strong> because it includes additional disbursement-specific parameters.</p>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Transfer Signature (Double Hashing)</strong>
                        <p class="text-muted small mb-2">For Cashout transfer endpoints (<code>/Transfer/bifast</code>), compute the <code>signature</code> as follows:</p>
                        <code class="d-block mt-2 mb-3 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(requestId + transactionId + channelId + methodFee + amount + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <p class="text-muted small mb-2">For the Account Inquiry endpoint (<code>/Transfer/inquiryAccount</code>), compute the signature as:</p>
                        <code class="d-block mt-2 mb-2 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(requestId + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <p class="text-muted small mb-0"><strong>Critical:</strong> The Transfer signature includes <code>channelId</code> and <code>methodFee</code> which are absent in the Cashin formula. Using the wrong formula is the #1 cause of <code>INVALID_SIGNATURE</code> errors on Cashout.</p>
                    </div>
                </div>

                <div class="doc-callout callout-error shadow-sm mt-4">
                    <div class="callout-icon"><i class="fas fa-skull-crossbones"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Cashout is Irreversible — Test in Sandbox First</strong>
                        <p class="mb-0 text-muted small">Unlike Cashin, a confirmed Cashout transfer immediately moves real funds from your settlement balance to the beneficiary's bank account. There is no cancellation or reversal mechanism once the transfer is in <code>PROCESSING</code> status. Always validate your integration in the sandbox/test environment before going live.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Account Inquiry -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Account Inquiry (Recommended Before Transfer)</h3>
                <p class="text-muted mb-4">Before disbursing, use the inquiry endpoint to validate the beneficiary's account number and retrieve the registered account name. This prevents transfers to wrong or closed accounts.</p>

                <div class="mt-4 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Endpoint & Workflow:</h5>
                    <ol class="text-muted hc-step-desc mb-0">
                        <li class="mb-2"><strong>Endpoint:</strong> <code>POST /Transfer/inquiryAccount</code></li>
                        <li class="mb-2"><strong>Required Payload:</strong> <code>merchantId</code>, <code>requestId</code>, <code>channelId</code> (bank code, e.g., <code>bca</code>, <code>mandiri</code>, <code>bni</code>), <code>accountNo</code>, <code>signature</code>.</li>
                        <li class="mb-2">On success, the response returns <code>accountName</code> (the registered account holder name). Display this to the operator/user for confirmation before proceeding with the transfer.</li>
                        <li class="mb-2">If the inquiry returns <code>ACCOUNT_NOT_FOUND</code>, the account number is invalid or does not exist at that bank. Do not proceed.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 3: BI-Fast Transfer -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">BI-Fast Transfer Execution</h3>
                <p class="text-muted mb-4">Endpoint: <code>POST /Transfer/bifast</code>. Used to disburse funds directly to a beneficiary's bank account via the BI-Fast interbank real-time transfer network.</p>

                <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                    <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">End-to-End Disbursement Flow</h6>
                    <div class="mermaid">
                    sequenceDiagram
                        autonumber
                        participant M as Merchant Server
                        participant G as Gidi Gateway
                        participant Q as Internal Queue
                        participant B as Bank API (BI-Fast)
                        
                        M->>G: POST /Transfer/bifast
                        G->>G: Check Signature & IP
                        G->>G: Check Settlement Balance
                        G->>G: Lock Merchant Balance (Pending)
                        G->>Q: Enqueue Transfer Request
                        G-->>M: HTTP 200 (Status: PENDING)
                        
                        Q->>B: Process Transfer
                        B-->>Q: Bank Response
                        
                        alt Success
                            Q->>G: Update Status to SUCCESS
                            G->>G: Deduct Locked Balance
                            G->>M: POST Webhook (SUCCESS)
                        else Failed
                            Q->>G: Update Status to FAILED
                            G->>G: Unlock & Restore Balance
                            G->>M: POST Webhook (FAILED)
                        end
                    </div>
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
                            <tr><td class="p-3 border-0"><code>requestId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">Unique UUID v4 per request (max 100 chars). Idempotency is enforced — never reuse.</td></tr>
                            <tr><td class="p-3 border-0"><code>transactionId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">Your internal invoice/disbursement ID (max 100 chars). Must be globally unique.</td></tr>
                            <tr><td class="p-3 border-0"><code>channelId</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">Destination bank code (e.g., <code>bca</code>, <code>mandiri</code>, <code>bni</code>, <code>bri</code>). Must match the inquiry result.</td></tr>
                            <tr><td class="p-3 border-0"><code>accountNo</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">The beneficiary's bank account number.</td></tr>
                            <tr><td class="p-3 border-0"><code>amount</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (Int)</span></td><td class="p-3 border-0">Transfer amount in IDR (no decimal). Subject to channel min/max limits.</td></tr>
                            <tr><td class="p-3 border-0"><code>methodFee</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (Enum)</span></td><td class="p-3 border-0">Who pays the transfer fee? <code>Merchant</code>: fee deducted separately from your balance. <code>Destination</code>: fee deducted from the amount sent to the beneficiary.</td></tr>
                            <tr><td class="p-3 border-0"><code>transferNote</code></td><td class="p-3 border-0"><span class="badge badge-optional">Optional (String)</span></td><td class="p-3 border-0">Transfer note/description (max 16 chars, alphanumeric and spaces only).</td></tr>
                            <tr><td class="p-3 border-0"><code>signature</code></td><td class="p-3 border-0"><span class="badge badge-required">Required (String)</span></td><td class="p-3 border-0">The Double-SHA256 signature hash computed per the Transfer formula in Section 1.</td></tr>
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
&nbsp;&nbsp;"responseMessage": "Transfer submitted successfully",<br>
&nbsp;&nbsp;"responseDetail": {<br>
&nbsp;&nbsp;&nbsp;&nbsp;"status": "PROCESSING",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"transactionId": "DISB-98765",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"referenceId": "BIF20260619XXXXXX"<br>
&nbsp;&nbsp;}<br>
}
                        </code>
                        <p class="text-muted small mb-0"><code>PROCESSING</code> status means the transfer has been accepted and queued by BI-Fast. Settlement usually completes within seconds to a few minutes. Use the status check endpoint to poll the final result.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Error Codes -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
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
                            <tr><td class="p-3 border-0"><code>INSUFFICIENT_BALANCE</code></td><td class="p-3 border-0">Your available settlement balance is lower than the transfer amount + fee. Top up your settlement balance first.</td></tr>
                            <tr><td class="p-3 border-0"><code>INVALID_SIGNATURE</code></td><td class="p-3 border-0">SHA256 hash mismatch. Ensure you are using the Transfer signature formula (not the Cashin one). Parameter order matters.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_TRANSACTION_ID</code></td><td class="p-3 border-0">A transfer with this <code>transactionId</code> was already submitted. Each disbursement must have a unique invoice ID.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCOUNT_BLOCKED / FREEZE</code></td><td class="p-3 border-0">The destination account number has been blacklisted internally (fraud flagging). The transfer cannot be processed to this account.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCOUNT_NOT_FOUND</code></td><td class="p-3 border-0">The account number does not exist at the specified bank. Verify the account number using Account Inquiry first.</td></tr>
                            <tr><td class="p-3 border-0"><code>SERVICE_NOT_ALLOWED</code></td><td class="p-3 border-0">Your account has not been granted Cashout permissions. Contact your Gateway admin to enable the Cashout service.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Common Disbursement Questions</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: The transfer response is SUCCESS but the beneficiary hasn't received the money</span>
            </div>
            <div class="hc-faq-a">
                <strong>Answer:</strong> A <code>SUCCESS</code> response from the API means your request was accepted and is now <code>PROCESSING</code> in BI-Fast — it does not mean the funds have settled yet. BI-Fast real-time transfers normally complete within seconds, but can take up to 1–2 minutes during peak hours.<br><br>
                <strong>Resolution:</strong> Use the status check endpoint with the <code>transactionId</code> to poll the current status. If the status remains <code>PROCESSING</code> for more than 5 minutes, escalate to your Gateway admin with the <code>referenceId</code> from the initial response for investigation with BI-Fast.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: What is the difference between methodFee "Merchant" and "Destination"?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Merchant (Fee Bearer = Your Business):</strong> The transfer fee is deducted from your settlement balance separately. The beneficiary receives the full <code>amount</code> you specified.<br><br>
                <strong>Destination (Fee Bearer = Beneficiary):</strong> The transfer fee is deducted from the <code>amount</code> itself. The beneficiary receives <code>amount - fee</code>.<br><br>
                <strong>Which to use?</strong> Use <code>Merchant</code> when you want to guarantee the exact amount reaches the beneficiary (e.g., salary disbursement). Use <code>Destination</code> for billing/refund scenarios where the sender pays the gross and the receiver absorbs the fee.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Q: Can I cancel or reverse a transfer that is already PROCESSING?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Answer:</strong> No. Once a transfer reaches <code>PROCESSING</code> status, it has been submitted to the BI-Fast interbank clearing network and cannot be cancelled via the API.<br><br>
                If a transfer was made to the wrong account, you must coordinate directly with the destination bank to request a recall — this is a manual banking process that can take 7–14 business days and is not guaranteed. This is why using the Account Inquiry endpoint to verify the account name before transferring is strongly recommended.
            </div>
        </div>
    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <p class="doc-lead text-muted" style="line-height: 1.7;">Dokumentasi teknis untuk developer yang berintegrasi dengan OpenAPI Cashout (Outbound/Disbursement). Mencakup inquiry saldo, verifikasi nama rekening, eksekusi transfer BI-Fast, dan endpoint pengecekan status — lengkap dengan struktur request/response dan troubleshooting error kritis untuk alur pencairan dana nyata.</p>

        <hr class="my-4">

        <!-- Section 1: Auth -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Autentikasi & Keamanan</h3>
                <p class="text-muted mb-4">API Cashout menggunakan model signature Double-SHA256 yang sama seperti Cashin, namun dengan <strong>urutan konkatenasi yang berbeda</strong> karena menyertakan parameter khusus disbursement tambahan.</p>

                <div class="doc-callout callout-important shadow-sm">
                    <div class="callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-2" style="font-size:15px;">Signature Transfer (Double Hashing)</strong>
                        <p class="text-muted small mb-2">Untuk endpoint transfer Cashout (<code>/Transfer/bifast</code>), hitung <code>signature</code> sebagai berikut:</p>
                        <code class="d-block mt-2 mb-3 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(requestId + transactionId + channelId + methodFee + amount + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <p class="text-muted small mb-2">Untuk endpoint Account Inquiry (<code>/Transfer/inquiryAccount</code>), hitung signature sebagai:</p>
                        <code class="d-block mt-2 mb-2 p-3 bg-dark text-body rounded" style="font-size:0.82rem; line-height:1.8;">
                            hash1 = SHA256(requestId + credentialKey)<br>
                            final_signature = SHA256(merchantId + hash1)
                        </code>
                        <p class="text-muted small mb-0"><strong>Penting:</strong> Signature Transfer menyertakan <code>channelId</code> dan <code>methodFee</code> yang tidak ada di formula Cashin. Menggunakan formula yang salah adalah penyebab #1 error <code>INVALID_SIGNATURE</code> di Cashout.</p>
                    </div>
                </div>

                <div class="doc-callout callout-error shadow-sm mt-4">
                    <div class="callout-icon"><i class="fas fa-skull-crossbones"></i></div>
                    <div class="callout-content">
                        <strong class="d-block mb-1 text-body" style="font-size: 16px;">Cashout Bersifat Irreversible — Uji di Sandbox Dulu</strong>
                        <p class="mb-0 text-muted small">Berbeda dari Cashin, transfer Cashout yang dikonfirmasi langsung memindahkan dana nyata dari saldo settlement Anda ke rekening bank penerima. Tidak ada mekanisme pembatalan atau pembalikan setelah transfer berstatus <code>PROCESSING</code>. Selalu validasi integrasi Anda di lingkungan sandbox/test sebelum go-live.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Account Inquiry -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Inquiry Rekening (Sangat Direkomendasikan Sebelum Transfer)</h3>
                <p class="text-muted mb-4">Sebelum mencairkan dana, gunakan endpoint inquiry untuk memvalidasi nomor rekening penerima dan mengambil nama pemilik rekening yang terdaftar. Ini mencegah transfer ke rekening yang salah atau sudah ditutup.</p>

                <div class="mt-4 mb-4">
                    <h5 class="font-weight-bold text-body mb-2">Endpoint & Alur Kerja:</h5>
                    <ol class="text-muted hc-step-desc mb-0">
                        <li class="mb-2"><strong>Endpoint:</strong> <code>POST /Transfer/inquiryAccount</code></li>
                        <li class="mb-2"><strong>Payload Wajib:</strong> <code>merchantId</code>, <code>requestId</code>, <code>channelId</code> (kode bank, mis. <code>bca</code>, <code>mandiri</code>, <code>bni</code>), <code>accountNo</code>, <code>signature</code>.</li>
                        <li class="mb-2">Jika sukses, response mengembalikan <code>accountName</code> (nama pemilik rekening yang terdaftar). Tampilkan ini kepada operator/user untuk konfirmasi sebelum melanjutkan transfer.</li>
                        <li class="mb-2">Jika inquiry mengembalikan <code>ACCOUNT_NOT_FOUND</code>, nomor rekening tidak valid atau tidak ada di bank tersebut. Jangan lanjutkan.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Section 3: BI-Fast Transfer -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">3</div>
                <h3 class="hc-step-title">Eksekusi Transfer BI-Fast</h3>
                <p class="text-muted mb-4">Endpoint: <code>POST /Transfer/bifast</code>. Digunakan untuk mencairkan dana langsung ke rekening bank penerima melalui jaringan transfer real-time antarbank BI-Fast.</p>

                <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
                    <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Alur Disbursement End-to-End</h6>
                    <div class="mermaid">
                    sequenceDiagram
                        autonumber
                        participant M as Server Merchant
                        participant G as Gidi Gateway
                        participant Q as Antrean Internal
                        participant B as API Bank (BI-Fast)
                        
                        M->>G: POST /Transfer/bifast
                        G->>G: Cek Signature & IP
                        G->>G: Cek Saldo Settlement
                        G->>G: Kunci Saldo Merchant (Pending)
                        G->>Q: Masukkan ke Antrean Transfer
                        G-->>M: HTTP 200 (Status: PENDING)
                        
                        Q->>B: Eksekusi Transfer
                        B-->>Q: Respons Bank
                        
                        alt Sukses
                            Q->>G: Update Status jadi SUCCESS
                            G->>G: Potong Saldo yang Dikunci
                            G->>M: POST Webhook (SUCCESS)
                        else Gagal
                            Q->>G: Update Status jadi FAILED
                            G->>G: Buka Kunci & Kembalikan Saldo
                            G->>M: POST Webhook (FAILED)
                        end
                    </div>
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
                            <tr><td class="p-3 border-0"><code>requestId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">UUID v4 unik per request (maks 100 karakter). Idempotency diterapkan — jangan pernah digunakan ulang.</td></tr>
                            <tr><td class="p-3 border-0"><code>transactionId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">ID invoice/disbursement internal Anda (maks 100 karakter). Harus unik secara global.</td></tr>
                            <tr><td class="p-3 border-0"><code>channelId</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">Kode bank tujuan (mis. <code>bca</code>, <code>mandiri</code>, <code>bni</code>, <code>bri</code>). Harus sesuai dengan hasil inquiry.</td></tr>
                            <tr><td class="p-3 border-0"><code>accountNo</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">Nomor rekening bank penerima.</td></tr>
                            <tr><td class="p-3 border-0"><code>amount</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (Int)</span></td><td class="p-3 border-0">Nominal transfer dalam IDR (tanpa desimal). Tunduk pada batas min/max kanal.</td></tr>
                            <tr><td class="p-3 border-0"><code>methodFee</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (Enum)</span></td><td class="p-3 border-0">Siapa yang membayar biaya transfer? <code>Merchant</code>: biaya dipotong terpisah dari saldo Anda. <code>Destination</code>: biaya dipotong dari nominal yang dikirim ke penerima.</td></tr>
                            <tr><td class="p-3 border-0"><code>transferNote</code></td><td class="p-3 border-0"><span class="badge badge-optional">Opsional (String)</span></td><td class="p-3 border-0">Catatan/keterangan transfer (maks 16 karakter, alfanumerik dan spasi saja).</td></tr>
                            <tr><td class="p-3 border-0"><code>signature</code></td><td class="p-3 border-0"><span class="badge badge-required">Wajib (String)</span></td><td class="p-3 border-0">Hash signature Double-SHA256 yang dihitung sesuai formula Transfer di Bagian 1.</td></tr>
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
&nbsp;&nbsp;"responseMessage": "Transfer submitted successfully",<br>
&nbsp;&nbsp;"responseDetail": {<br>
&nbsp;&nbsp;&nbsp;&nbsp;"status": "PROCESSING",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"transactionId": "DISB-98765",<br>
&nbsp;&nbsp;&nbsp;&nbsp;"referenceId": "BIF20260619XXXXXX"<br>
&nbsp;&nbsp;}<br>
}
                        </code>
                        <p class="text-muted small mb-0">Status <code>PROCESSING</code> berarti transfer telah diterima dan dimasukkan antrian oleh BI-Fast. Settlement biasanya selesai dalam hitungan detik hingga beberapa menit. Gunakan endpoint status check untuk memantau hasilnya.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Error Codes -->
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">4</div>
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
                            <tr><td class="p-3 border-0"><code>INSUFFICIENT_BALANCE</code></td><td class="p-3 border-0">Saldo settlement Anda lebih rendah dari nominal transfer + biaya. Top up saldo settlement Anda terlebih dahulu.</td></tr>
                            <tr><td class="p-3 border-0"><code>INVALID_SIGNATURE</code></td><td class="p-3 border-0">Hash SHA256 tidak cocok. Pastikan Anda menggunakan formula signature Transfer (bukan formula Cashin). Urutan parameter sangat penting.</td></tr>
                            <tr><td class="p-3 border-0"><code>DOUBLE_TRANSACTION_ID</code></td><td class="p-3 border-0">Transfer dengan <code>transactionId</code> ini sudah pernah diajukan. Setiap disbursement harus memiliki ID invoice yang unik.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCOUNT_BLOCKED / FREEZE</code></td><td class="p-3 border-0">Nomor rekening tujuan telah masuk daftar hitam internal (penandaan penipuan). Transfer tidak dapat diproses ke rekening ini.</td></tr>
                            <tr><td class="p-3 border-0"><code>ACCOUNT_NOT_FOUND</code></td><td class="p-3 border-0">Nomor rekening tidak ada di bank yang ditentukan. Verifikasi nomor rekening menggunakan Account Inquiry terlebih dahulu.</td></tr>
                            <tr><td class="p-3 border-0"><code>SERVICE_NOT_ALLOWED</code></td><td class="p-3 border-0">Akun Anda belum mendapat izin Cashout. Hubungi admin Gateway Anda untuk mengaktifkan layanan Cashout.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Pertanyaan Disbursement Umum (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Response transfer SUCCESS tapi penerima belum terima uangnya</span>
            </div>
            <div class="hc-faq-a">
                <strong>Jawaban:</strong> Response <code>SUCCESS</code> dari API berarti request Anda diterima dan sedang <code>PROCESSING</code> di BI-Fast — bukan berarti dana sudah ter-settle. Transfer real-time BI-Fast biasanya selesai dalam hitungan detik, namun bisa memakan waktu 1–2 menit saat jam sibuk.<br><br>
                <strong>Resolusi:</strong> Gunakan endpoint status check dengan <code>transactionId</code> untuk memantau status saat ini. Jika status tetap <code>PROCESSING</code> lebih dari 5 menit, eskalasikan ke admin Gateway Anda dengan <code>referenceId</code> dari response awal untuk investigasi dengan BI-Fast.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Apa perbedaan methodFee "Merchant" dan "Destination"?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Merchant (Penanggung Biaya = Bisnis Anda):</strong> Biaya transfer dipotong terpisah dari saldo settlement Anda. Penerima mendapatkan penuh <code>amount</code> yang Anda tentukan.<br><br>
                <strong>Destination (Penanggung Biaya = Penerima):</strong> Biaya transfer dipotong dari <code>amount</code> itu sendiri. Penerima mendapatkan <code>amount - biaya</code>.<br><br>
                <strong>Mana yang digunakan?</strong> Gunakan <code>Merchant</code> saat Anda ingin memastikan nominal penuh sampai ke penerima (mis. disbursement gaji). Gunakan <code>Destination</code> untuk skenario tagihan/refund di mana pengirim membayar gross dan penerima menanggung biaya.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>T: Apakah transfer yang sudah PROCESSING bisa dibatalkan?</span>
            </div>
            <div class="hc-faq-a">
                <strong>Jawaban:</strong> Tidak. Setelah transfer mencapai status <code>PROCESSING</code>, transfer tersebut telah diajukan ke jaringan kliring antarbank BI-Fast dan tidak dapat dibatalkan melalui API.<br><br>
                Jika transfer dikirim ke rekening yang salah, Anda harus berkoordinasi langsung dengan bank tujuan untuk meminta recall — ini adalah proses perbankan manual yang dapat memakan waktu 7–14 hari kerja dan tidak dijamin berhasil. Inilah mengapa sangat disarankan menggunakan endpoint Account Inquiry untuk memverifikasi nama rekening sebelum mentransfer.
            </div>
        </div>
    </div>
</div>
