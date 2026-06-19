<div id="module-login" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-fingerprint text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Authentication Guide</h2>
            <p class="text-muted lead" style="max-width: 700px; margin: 0 auto;">Security begins at the perimeter. This guide details the strict authentication protocols required to access the Digi-CI3 Payment Gateway and how to resolve common access issues.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Pre-Authentication Security Checks</h3>
            <p class="text-muted mb-4">Before attempting to log in, always verify that your environment is secure and that you are communicating with the legitimate gateway server. Bypassing these checks compromises your account.</p>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(40, 167, 69, 0.1);">
                            <i class="fas fa-lock text-success"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Verify SSL Certificate</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Always ensure the URL begins with <code class="px-2 py-1 rounded" style="background: var(--hc-code-bg); color: #28a745;">https://</code> and features the padlock icon in your browser's address bar. Do not bypass SSL warnings.</p>
                        <div class="position-absolute" style="top: 20px; right: 20px;">
                            <span class="badge badge-success px-2 py-1" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Required</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(23, 162, 184, 0.1);">
                            <i class="fas fa-desktop text-info"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Session Hygiene</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">If accessing the gateway from a shared corporate terminal, ensure you are using an Incognito/Private window or clear your browser cache before proceeding.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-network-wired text-warning"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Network Security</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Never log in over public or unsecured Wi-Fi networks (like cafes or airports). Always use a secure corporate VPN when accessing the system remotely.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt text-primary mr-2"></i> 2. Standard Login Procedure</h3>
            <p class="text-muted mb-4">The gateway utilizes a secure email-based authentication system backed by Google reCAPTCHA. Follow these exact steps to ensure a smooth login process.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-3">
                    <strong class="text-dark d-block">Access the Authentication Portal</strong>
                    Navigate to the official admin login URL. Ensure that your browser displays a secure connection padlock.
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Enter your Registered Email Address</strong>
                    Input the email address associated with your admin account. The system will verify if this exact email exists in the database.
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Input your Secure Password</strong>
                    Type your alphanumeric password carefully. Please note that passwords are strictly case-sensitive. Check your Caps Lock key status. If you forget your password, you can utilize the "Forgot Password" link to receive a secure reset token via email.
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Complete the reCAPTCHA Verification</strong>
                    Before clicking login, you must check the "I'm not a robot" box. Depending on Google's security assessment, you may be prompted to select specific images (e.g., traffic lights, crosswalks). This step is mandatory to block automated bot attacks.
                </li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 3. Comprehensive Troubleshooting</h3>
            <p class="text-muted mb-4">If authentication fails, the system will provide specific error messages. Follow the corresponding resolution protocols below.</p>

            <!-- Case 1 -->
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-envelope"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Case 1: "This email is not registered!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Symptom</span> <span class="text-muted small">The system cannot find your email in the Admin database.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-warning mr-1"></i> Resolution Protocol:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Double-check your email address for any typographical errors (e.g., missing letters, incorrect domain).</li>
                        <li class="mb-1">If you are a new employee, your account might not have been provisioned yet. Contact your division manager or the IT Helpdesk to request Admin access.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 2 -->
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-key"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Case 2: "Wrong password!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Symptom</span> <span class="text-muted small">Your email is recognized, but the password provided is incorrect.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-warning mr-1"></i> Resolution Protocol:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Verify that your Caps Lock is disabled, as passwords are case-sensitive.</li>
                        <li class="mb-1">If you pasted your password, ensure you didn't accidentally include trailing spaces.</li>
                        <li class="mb-1">If you still cannot remember your password, click the <strong>"Forgot Password"</strong> link on the login page. The system will send a secure password reset link to your email, which you must access within the required time limit.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 3 -->
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-user-times"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Case 3: "This email has not been activated!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Symptom</span> <span class="text-muted small">Your credentials are valid, but your account status is currently set to inactive.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-danger mr-1"></i> Resolution Protocol:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">If you just registered, you must check your inbox and click the <strong>Activation Link</strong> sent by the system. Note that the activation token strictly <strong>expires within 24 hours</strong>.</li>
                        <li class="mb-1">If your 24-hour window has expired, the activation will fail with a "Token expired" message, and your unactivated account data will be wiped. You must re-register.</li>
                        <li class="mb-1">If you are an existing user and suddenly receive this, a Super Administrator may have manually suspended your access. Escalate to HR or IT Support.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 4 -->
            <div class="doc-callout callout-info mb-4 shadow-sm border-left-info">
                <div class="callout-icon"><i class="fas fa-robot"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Case 4: "Please complete the reCAPTCHA verification!" / "reCAPTCHA validation failed!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Symptom</span> <span class="text-muted small">Login is halted because the anti-bot verification was either skipped or failed.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-info mr-1"></i> Resolution Protocol:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Ensure you check the "I'm not a robot" box. Wait for the green checkmark before clicking the 'Login' button.</li>
                        <li class="mb-1">If the login page was left idle for too long, the reCAPTCHA token might have expired. Reload the page (F5) and try again.</li>
                        <li class="mb-1">If the reCAPTCHA widget does not appear at all, disable aggressive ad-blockers (like uBlock Origin) or tracking-prevention extensions, as they may block Google's scripts.</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-fingerprint text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Panduan Autentikasi</h2>
            <p class="text-muted lead" style="max-width: 700px; margin: 0 auto;">Keamanan dimulai di perimeter. Panduan ini merinci protokol autentikasi ketat yang diperlukan untuk mengakses Payment Gateway Digi-CI3 dan cara menyelesaikan masalah akses umum.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-shield-alt text-primary mr-2"></i> 1. Pemeriksaan Keamanan Pra-Autentikasi</h3>
            <p class="text-muted mb-4">Sebelum mencoba login, selalu verifikasi bahwa lingkungan Anda aman dan Anda berkomunikasi dengan server gateway yang sah. Mengabaikan pemeriksaan ini dapat membahayakan akun Anda.</p>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(40, 167, 69, 0.1);">
                            <i class="fas fa-lock text-success"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Verifikasi Sertifikat SSL</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Selalu pastikan URL diawali dengan <code class="px-2 py-1 rounded" style="background: var(--hc-code-bg); color: #28a745;">https://</code> dan menampilkan ikon gembok di bilah alamat browser Anda. Jangan abaikan peringatan SSL.</p>
                        <div class="position-absolute" style="top: 20px; right: 20px;">
                            <span class="badge badge-success px-2 py-1" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Wajib</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(23, 162, 184, 0.1);">
                            <i class="fas fa-desktop text-info"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Kebersihan Sesi (Session)</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Jika mengakses sistem dari komputer korporat bersama, pastikan Anda menggunakan mode <em>Incognito/Private</em> atau bersihkan <em>cache</em> browser sebelum melanjutkan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="hc-security-card">
                        <div class="hc-security-icon-wrapper" style="background: rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-network-wired text-warning"></i>
                        </div>
                        <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Keamanan Jaringan</h6>
                        <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Jangan pernah login melalui jaringan Wi-Fi publik (kafe/bandara). Selalu gunakan VPN resmi perusahaan saat mengakses sistem secara jarak jauh.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt text-primary mr-2"></i> 2. Prosedur Login Standar</h3>
            <p class="text-muted mb-4">Gateway ini menggunakan sistem autentikasi berbasis email yang aman, didukung oleh Google reCAPTCHA. Ikuti langkah-langkah detail berikut untuk memastikan proses login berjalan lancar.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-3">
                    <strong class="text-dark d-block">Akses Portal Autentikasi</strong>
                    Buka URL resmi untuk login admin. Pastikan browser Anda menampilkan ikon gembok koneksi aman (HTTPS).
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Masukkan Alamat Email Terdaftar</strong>
                    Ketikkan alamat email yang didaftarkan untuk akun admin Anda. Sistem akan memverifikasi ketersediaan email tersebut di database.
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Masukkan Password Anda</strong>
                    Ketikkan <em>password</em> alfanumerik Anda dengan teliti. Password sangat peka terhadap huruf besar-kecil (<em>case-sensitive</em>). Pastikan Caps Lock mati. Jika Anda lupa password, Anda dapat menggunakan tautan "Lupa Password" untuk mendapatkan token reset via email.
                </li>
                <li class="mb-3">
                    <strong class="text-dark d-block">Selesaikan Verifikasi reCAPTCHA</strong>
                    Sebelum menekan tombol masuk, Anda wajib mencentang kotak "I'm not a robot". Tergantung pada penilaian keamanan Google, Anda mungkin diminta memilih gambar tertentu (misal: rambu lalu lintas atau zebra cross). Langkah ini wajib untuk menangkis serangan bot.
                </li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> 3. Panduan Pemecahan Masalah (Troubleshooting)</h3>
            <p class="text-muted mb-4">Jika proses autentikasi gagal, sistem akan menampilkan pesan error yang spesifik. Ikuti protokol resolusi yang sesuai di bawah ini.</p>

            <!-- Case 1 -->
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-envelope"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kasus 1: "This email is not registered!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Gejala</span> <span class="text-muted small">Sistem tidak dapat menemukan email Anda di database Admin.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-warning mr-1"></i> Protokol Resolusi:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Periksa kembali alamat email yang Anda ketik dari kemungkinan salah eja (typo).</li>
                        <li class="mb-1">Jika Anda pegawai baru, akun Anda mungkin belum diprovisi. Silakan hubungi manajer divisi Anda atau Helpdesk IT untuk meminta akses Admin.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 2 -->
            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-key"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kasus 2: "Wrong password!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Gejala</span> <span class="text-muted small">Email dikenali, namun password yang dimasukkan salah.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-warning mr-1"></i> Protokol Resolusi:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Pastikan tombol Caps Lock Anda tidak aktif, karena sistem membedakan huruf besar dan kecil.</li>
                        <li class="mb-1">Jika Anda menempelkan (copy-paste) password, berhati-hatilah agar spasi di akhir teks tidak ikut tersalin.</li>
                        <li class="mb-1">Jika Anda benar-benar lupa password, klik tautan <strong>"Forgot Password" / "Lupa Password"</strong> di halaman login. Sistem akan mengirimkan link reset yang aman ke email Anda, yang harus diakses dalam batas waktu tertentu.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 3 -->
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-user-times"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kasus 3: "This email has not been activated!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Gejala</span> <span class="text-muted small">Kredensial sudah benar, tetapi status akun Anda saat ini tidak aktif.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-danger mr-1"></i> Protokol Resolusi:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Jika Anda baru mendaftar, periksa kotak masuk (inbox) Anda dan klik <strong>Link Aktivasi</strong> dari sistem. Perlu dicatat bahwa token aktivasi tersebut <strong>kedaluwarsa dalam 24 jam</strong>.</li>
                        <li class="mb-1">Jika lewat dari 24 jam, aktivasi akan memunculkan pesan "Token expired", dan data akun Anda yang belum aktif akan dihapus oleh sistem. Anda harus mendaftar ulang.</li>
                        <li class="mb-1">Bagi pengguna lama, error ini berarti Super Administrator telah menangguhkan akses Anda secara manual. Segera lapor ke HR atau Tim IT.</li>
                    </ul>
                </div>
            </div>

            <!-- Case 4 -->
            <div class="doc-callout callout-info mb-4 shadow-sm border-left-info">
                <div class="callout-icon"><i class="fas fa-robot"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Kasus 4: "Please complete the reCAPTCHA verification!" / "reCAPTCHA validation failed!"</strong>
                    <p style="margin-top: 4px; margin-bottom: 8px;"><span class="badge badge-secondary" style="font-size: 11px; padding: 4px 8px;">Gejala</span> <span class="text-muted small">Proses login dihentikan karena verifikasi anti-bot dilewati atau gagal memvalidasi.</span></p>
                    <p style="margin-top: 8px; margin-bottom: 4px;"><strong><i class="fas fa-wrench text-info mr-1"></i> Protokol Resolusi:</strong></p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Pastikan Anda mencentang kotak "I'm not a robot" dan menunggu hingga muncul tanda centang hijau sebelum mengklik tombol Login.</li>
                        <li class="mb-1">Jika halaman login dibiarkan diam terlalu lama, token reCAPTCHA dapat kedaluwarsa. Muat ulang halaman (F5) dan coba lagi.</li>
                        <li class="mb-1">Jika widget reCAPTCHA tidak muncul, matikan pemblokir iklan yang terlalu agresif (seperti uBlock Origin) karena dapat memblokir skrip Google.</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>

