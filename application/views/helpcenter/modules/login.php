<div id="module-login" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">Security begins at the perimeter. This guide details the strict authentication protocols required to access the GIDI Payment Gateway Admin Panel and how to resolve common access issues safely.</p>
        
        <hr class="my-4">

        <!-- UI Overview -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Authentication Portal</h5>
        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">UI Element</th>
                        <th class="p-3 border-0">Description & Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Email Address Field</strong></td>
                        <td class="p-3 border-0 text-muted">The primary identifier for your administrative account.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Password Field</strong></td>
                        <td class="p-3 border-0 text-muted">Case-sensitive alphanumeric credential used to securely authenticate your session.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>reCAPTCHA Widget</strong></td>
                        <td class="p-3 border-0 text-muted">Google's automated anti-bot verification to prevent brute-force attacks on the admin panel.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Forgot Password Link</strong></td>
                        <td class="p-3 border-0 text-muted">Redirects to a secure flow to request a time-sensitive password reset token via registered email.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Workflow -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sign-in-alt text-primary mr-2"></i> Step-by-Step Login Procedure</h5>
        <p class="text-muted mb-4">The gateway utilizes a secure email-based authentication system backed by reCAPTCHA. Follow these exact steps to log in.</p>
        
        <div class="pl-4 border-left border-info ml-2 mb-4">
            <ol class="text-muted mb-0">
                <li class="mb-3">
                    <strong class="text-body d-block">Access the Authentication Portal</strong>
                    Navigate to the official admin login URL. Ensure that your browser displays a secure connection padlock.
                </li>
                <li class="mb-3">
                    <strong class="text-body d-block">Enter Credentials</strong>
                    Input the email address and your case-sensitive password. 
                </li>
                <li class="mb-3">
                    <strong class="text-body d-block">Complete Verification</strong>
                    Check the "I'm not a robot" box. Depending on Google's security assessment, select specific images (e.g., traffic lights) if prompted.
                </li>
                <li class="mb-0">
                    <strong class="text-body d-block">Execute Login</strong>
                    Click the <strong>Login</strong> button. If successful, you will be redirected to the main Dashboard.
                </li>
            </ol>
        </div>

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Authentication Architecture Flow</h6>
            <div class="mermaid">
            flowchart TD
                U((User)) -->|Enters Credentials| L[Login Form]
                L --> C{reCAPTCHA Valid?}
                C -- No --> E1[Reject: Bot Detected]
                C -- Yes --> Auth{Credentials Match?}
                Auth -- No --> E2[Reject: Invalid Login]
                Auth -- Yes --> Act{Account Active?}
                Act -- No --> E3[Reject: Not Activated]
                Act -- Yes --> Sess[Create Secure Session]
                Sess --> D[Redirect to Dashboard]
                
                style U fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                style D fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                style E1 fill:#dc2626,color:#fff
                style E2 fill:#dc2626,color:#fff
                style E3 fill:#dc2626,color:#fff
            </div>
        </div>

        <!-- Architecture / Security Checks -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Architecture: Pre-Authentication Security Checks</h5>
        <p class="text-muted mb-4">Before attempting to log in, always verify that your environment is secure. Bypassing these checks compromises the platform.</p>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-lock text-success"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Verify SSL Certificate</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Always ensure the URL begins with <code class="px-2 py-1 rounded" style="background: var(--hc-code-bg); color: #28a745;">https://</code>. Do not bypass browser SSL warnings.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-desktop text-info"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Session Hygiene</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">If accessing the gateway from a shared terminal, ensure you are using an Incognito/Private window.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-network-wired text-warning"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Network Security</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Never log in over public or unsecured Wi-Fi networks. Always use a secure corporate VPN.</p>
                </div>
            </div>
        </div>

        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_log_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Case 1: "This email is not registered!"
                </a>
                <div id="faq_en_log_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> The system cannot find your email in the Admin database.<br><br>
                        <strong>Resolution Protocol:</strong> Double-check your email address for any typographical errors. If you are a new employee, your account might not have been provisioned yet. Contact IT Helpdesk.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_log_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Case 2: "Wrong password!"
                </a>
                <div id="faq_en_log_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Your email is recognized, but the password provided is incorrect.<br><br>
                        <strong>Resolution Protocol:</strong> Verify that your Caps Lock is disabled, as passwords are case-sensitive. If you still cannot remember your password, utilize the "Forgot Password" link.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_log_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Case 3: "This email has not been activated!"
                </a>
                <div id="faq_en_log_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Your credentials are valid, but your account status is inactive.<br><br>
                        <strong>Resolution Protocol:</strong> If you just registered, you must check your inbox and click the Activation Link sent by the system (expires in 24 hours). If you are an existing user, your access may have been suspended manually by a Super Admin.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_log_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Case 4: "Please complete the reCAPTCHA verification!"
                </a>
                <div id="faq_en_log_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Symptom:</strong> Login is halted because anti-bot verification failed.<br><br>
                        <strong>Resolution Protocol:</strong> Ensure you check the "I'm not a robot" box. If it does not appear, disable aggressive ad-blockers, as they may block Google's scripts. Reload the page and try again.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">Keamanan dimulai di perimeter. Panduan ini merinci protokol autentikasi ketat yang diperlukan untuk mengakses Panel Admin GIDI Payment Gateway dan cara menyelesaikan masalah akses umum dengan aman.</p>
        
        <hr class="my-4">

        <!-- UI Overview -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar Antarmuka — Portal Autentikasi</h5>
        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Elemen Antarmuka</th>
                        <th class="p-3 border-0">Deskripsi & Tujuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Kolom Alamat Email</strong></td>
                        <td class="p-3 border-0 text-muted">Pengenal utama untuk akun administratif Anda.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Kolom Kata Sandi</strong></td>
                        <td class="p-3 border-0 text-muted">Kredensial alfanumerik yang membedakan huruf besar-kecil, digunakan untuk memverifikasi sesi Anda secara aman.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Widget reCAPTCHA</strong></td>
                        <td class="p-3 border-0 text-muted">Verifikasi anti-bot otomatis dari Google untuk mencegah serangan <em>brute-force</em> pada panel admin.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Tautan Lupa Kata Sandi</strong></td>
                        <td class="p-3 border-0 text-muted">Mengarahkan ke alur aman untuk meminta token pengaturan ulang kata sandi (berbatas waktu) melalui email terdaftar.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Workflow -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-sign-in-alt text-primary mr-2"></i> Prosedur Login Langkah demi Langkah</h5>
        <p class="text-muted mb-4">Gateway ini menggunakan sistem autentikasi berbasis email yang didukung oleh reCAPTCHA. Ikuti langkah-langkah detail ini untuk masuk.</p>
        
        <div class="pl-4 border-left border-info ml-2 mb-4">
            <ol class="text-muted mb-0">
                <li class="mb-3">
                    <strong class="text-body d-block">Akses Portal Autentikasi</strong>
                    Buka URL resmi untuk login admin. Pastikan browser Anda menampilkan ikon gembok koneksi aman (HTTPS).
                </li>
                <li class="mb-3">
                    <strong class="text-body d-block">Masukkan Kredensial</strong>
                    Ketikkan alamat email dan password Anda (huruf besar-kecil berpengaruh).
                </li>
                <li class="mb-3">
                    <strong class="text-body d-block">Selesaikan Verifikasi</strong>
                    Centang kotak "I'm not a robot". Tergantung keamanan Google, pilih gambar tertentu (misal rambu lalu lintas) jika diminta.
                </li>
                <li class="mb-0">
                    <strong class="text-body d-block">Eksekusi Login</strong>
                    Klik tombol <strong>Login</strong>. Jika berhasil, Anda akan langsung dialihkan ke Dasbor utama.
                </li>
            </ol>
        </div>

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Alur Arsitektur Autentikasi</h6>
            <div class="mermaid">
            flowchart TD
                U((User)) -->|Input Kredensial| L[Form Login]
                L --> C{reCAPTCHA Valid?}
                C -- Tidak --> E1[Tolak: Terdeteksi Bot]
                C -- Ya --> Auth{Kredensial Cocok?}
                Auth -- Tidak --> E2[Tolak: Login Tidak Valid]
                Auth -- Ya --> Act{Akun Aktif?}
                Act -- Tidak --> E3[Tolak: Belum Diaktivasi]
                Act -- Ya --> Sess[Buat Sesi Aman]
                Sess --> D[Arahkan ke Dashboard]
                
                style U fill:#2563eb,stroke:#1d4ed8,stroke-width:2px,color:#fff
                style D fill:#16a34a,stroke:#15803d,stroke-width:2px,color:#fff
                style E1 fill:#dc2626,color:#fff
                style E2 fill:#dc2626,color:#fff
                style E3 fill:#dc2626,color:#fff
            </div>
        </div>

        <!-- Architecture / Security Checks -->
        <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> Arsitektur: Pemeriksaan Keamanan Pra-Autentikasi</h5>
        <p class="text-muted mb-4">Sebelum mencoba login, selalu verifikasi bahwa lingkungan Anda aman. Mengabaikan langkah ini dapat membahayakan platform.</p>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-lock text-success"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Verifikasi SSL</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Selalu pastikan URL diawali dengan <code class="px-2 py-1 rounded" style="background: var(--hc-code-bg); color: #28a745;">https://</code>. Jangan abaikan peringatan SSL di browser.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-desktop text-info"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Kebersihan Sesi</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Jika mengakses dari komputer bersama, pastikan Anda menggunakan mode Incognito/Private.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="hc-security-card">
                    <div class="hc-security-icon-wrapper" style="background: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-network-wired text-warning"></i>
                    </div>
                    <h6 class="font-weight-bold" style="color: var(--hc-heading); font-size: 16px; margin-bottom: 12px; letter-spacing: -0.3px;">Keamanan Jaringan</h6>
                    <p style="font-size: 13px; line-height: 1.6; color: var(--hc-text); margin: 0;">Jangan login melalui Wi-Fi publik tanpa pelindung. Selalu gunakan VPN resmi korporat.</p>
                </div>
            </div>
        </div>

        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Masalah Umum (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_log_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Kasus 1: "This email is not registered!"
                </a>
                <div id="faq_id_log_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Sistem tidak dapat menemukan email Anda di database Admin.<br><br>
                        <strong>Protokol Resolusi:</strong> Periksa kembali dari kemungkinan salah eja. Jika Anda pegawai baru, akun Anda mungkin belum diprovisi. Silakan hubungi Helpdesk IT.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_log_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Kasus 2: "Wrong password!"
                </a>
                <div id="faq_id_log_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Email dikenali, namun password yang dimasukkan salah.<br><br>
                        <strong>Protokol Resolusi:</strong> Pastikan tombol Caps Lock Anda mati, karena sistem membedakan huruf besar dan kecil. Jika Anda benar-benar lupa, gunakan tautan Lupa Password.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_log_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Kasus 3: "This email has not been activated!"
                </a>
                <div id="faq_id_log_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Kredensial benar, tetapi status akun Anda tidak aktif.<br><br>
                        <strong>Protokol Resolusi:</strong> Jika baru mendaftar, periksa inbox Anda dan klik Link Aktivasi (berlaku 24 jam). Bagi pengguna lama, ini berarti Super Admin telah menangguhkan akses Anda. Segera lapor ke Tim IT.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_log_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Kasus 4: "Please complete the reCAPTCHA verification!"
                </a>
                <div id="faq_id_log_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Gejala:</strong> Login gagal karena verifikasi anti-bot gagal divalidasi.<br><br>
                        <strong>Protokol Resolusi:</strong> Pastikan Anda mencentang "I'm not a robot". Jika widget tidak muncul, matikan ekstensi pemblokir iklan (Ad-blockers). Muat ulang halaman dan coba lagi.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
