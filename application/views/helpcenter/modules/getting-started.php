<div id="module-getting-started" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-rocket text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Getting Started Guide</h2>
            <p class="text-muted lead" style="max-width: 700px; margin: 0 auto;">Follow this step-by-step guide to configure the Digi-CI3 Gateway application initially, from basic setup to your first login.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-database text-primary mr-2"></i> Step 1: Database Configuration</h3>
            <p class="text-muted mb-4">First, you need to connect the application to your database. Follow these steps:</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Open the file located at <code>application/config/database.php</code>.</li>
                <li class="mb-2">Find the configuration array and update the following values to match your environment:
                    <div class="bg-light p-3 rounded mt-2 mb-2">
                        <code>
                            'hostname' => 'localhost',<br>
                            'username' => 'your_db_username',<br>
                            'password' => 'your_db_password',<br>
                            'database' => 'your_database_name',
                        </code>
                    </div>
                </li>
                <li class="mb-2">Save the file.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-globe text-primary mr-2"></i> Step 2: Base URL Configuration</h3>
            <p class="text-muted mb-4">Next, configure the base URL so the application knows its root address.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Open the file located at <code>application/config/config.php</code>.</li>
                <li class="mb-2">Find the <code>$config['base_url']</code> setting and update it:
                    <div class="bg-light p-3 rounded mt-2 mb-2">
                        <code>$config['base_url'] = 'http://localhost/digi-ci3/';</code>
                    </div>
                </li>
                <li class="mb-2">Make sure to include the trailing slash.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-shield text-primary mr-2"></i> Step 3: Default Admin Account Setup</h3>
            <p class="text-muted mb-4">Ensure you have a default administrator account in your database to access the dashboard.</p>
            
            <ul class="pl-4 text-muted">
                <li class="mb-2">Import the provided initial SQL file to your database. This file usually contains the schema and an initial admin user.</li>
                <li class="mb-2">The default credentials are typically:
                    <ul class="mt-2 mb-2">
                        <li><strong>Email:</strong> admin@example.com</li>
                        <li><strong>Password:</strong> admin123</li>
                    </ul>
                </li>
                <li class="mb-2"><strong>Important:</strong> Please change this password immediately after your first login.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt text-primary mr-2"></i> Step 4: Ready to Login</h3>
            <p class="text-muted mb-4">The application is now configured and ready to use.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Open your web browser.</li>
                <li class="mb-2">Navigate to the login page: <code>http://localhost/digi-ci3/auth</code> (or your configured base URL).</li>
                <li class="mb-2">Enter the default admin credentials to access the Dashboard.</li>
            </ol>
            
            <div class="doc-callout callout-tip mt-4 shadow-sm">
                <div class="callout-icon"><i class="fas fa-check-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Success!</strong>
                    <p class="mb-0 text-muted small">You are now ready to start using the Digi-CI3 Gateway platform. Next, proceed to the Authentication Guide to learn more about our security features.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> Step 5: Troubleshooting</h3>
            <p class="text-muted mb-4">If you encounter issues during the initial setup, check the following common problems:</p>
            
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-database"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Database Connection Error</strong>
                    <p class="mb-2 text-muted small">If you see a blank page or a database error message:</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Double check your database credentials in <code>application/config/database.php</code>.</li>
                        <li class="mb-1">Ensure the MySQL server is actually running (e.g., via XAMPP control panel).</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-unlink"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">404 Page Not Found or Missing Styles</strong>
                    <p class="mb-2 text-muted small">If the layout is broken or links return a 404 error:</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Verify that <code>$config['base_url']</code> in <code>application/config/config.php</code> is completely correct and ends with a slash <code>/</code>.</li>
                        <li class="mb-1">Check that the <code>.htaccess</code> file is present in your root directory if URL rewriting (removing index.php) is active.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-rocket text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Panduan Memulai</h2>
            <p class="text-muted lead" style="max-width: 700px; margin: 0 auto;">Ikuti panduan langkah demi langkah ini untuk melakukan konfigurasi awal aplikasi Digi-CI3 Gateway, mulai dari pengaturan dasar hingga siap untuk login.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-database text-primary mr-2"></i> Langkah 1: Konfigurasi Database</h3>
            <p class="text-muted mb-4">Pertama, Anda perlu menghubungkan aplikasi ke database. Ikuti langkah-langkah berikut:</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Buka file yang terletak di <code>application/config/database.php</code>.</li>
                <li class="mb-2">Cari array konfigurasi dan perbarui nilai-nilai berikut agar sesuai dengan environment Anda:
                    <div class="bg-light p-3 rounded mt-2 mb-2">
                        <code>
                            'hostname' => 'localhost',<br>
                            'username' => 'username_db_anda',<br>
                            'password' => 'password_db_anda',<br>
                            'database' => 'nama_database_anda',
                        </code>
                    </div>
                </li>
                <li class="mb-2">Simpan file tersebut.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-globe text-primary mr-2"></i> Langkah 2: Konfigurasi Base URL</h3>
            <p class="text-muted mb-4">Selanjutnya, atur base URL agar aplikasi mengetahui alamat root-nya.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Buka file yang terletak di <code>application/config/config.php</code>.</li>
                <li class="mb-2">Cari pengaturan <code>$config['base_url']</code> dan perbarui nilainya:
                    <div class="bg-light p-3 rounded mt-2 mb-2">
                        <code>$config['base_url'] = 'http://localhost/digi-ci3/';</code>
                    </div>
                </li>
                <li class="mb-2">Pastikan untuk menyertakan garis miring (slash) di akhir URL.</li>
            </ol>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-user-shield text-primary mr-2"></i> Langkah 3: Setup Akun Admin Default</h3>
            <p class="text-muted mb-4">Pastikan Anda memiliki akun administrator default di database untuk mengakses dashboard.</p>
            
            <ul class="pl-4 text-muted">
                <li class="mb-2">Import file SQL awal yang disediakan ke database Anda. File ini biasanya berisi skema tabel dan satu akun admin awal.</li>
                <li class="mb-2">Kredensial default yang biasa digunakan:
                    <ul class="mt-2 mb-2">
                        <li><strong>Email:</strong> admin@example.com</li>
                        <li><strong>Password:</strong> admin123</li>
                    </ul>
                </li>
                <li class="mb-2"><strong>Penting:</strong> Harap segera ubah password ini setelah Anda berhasil login untuk pertama kalinya.</li>
            </ul>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt text-primary mr-2"></i> Langkah 4: Siap untuk Login</h3>
            <p class="text-muted mb-4">Aplikasi sekarang telah dikonfigurasi dan siap digunakan.</p>
            
            <ol class="pl-4 text-muted">
                <li class="mb-2">Buka web browser Anda.</li>
                <li class="mb-2">Arahkan ke halaman login: <code>http://localhost/digi-ci3/auth</code> (atau sesuai base URL Anda).</li>
                <li class="mb-2">Masukkan kredensial admin default untuk mengakses Dashboard.</li>
            </ol>
            
            <div class="doc-callout callout-tip mt-4 shadow-sm">
                <div class="callout-icon"><i class="fas fa-check-circle"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Berhasil!</strong>
                    <p class="mb-0 text-muted small">Anda kini siap untuk mulai menggunakan platform Digi-CI3 Gateway. Selanjutnya, pelajari Panduan Autentikasi untuk informasi keamanan lebih lanjut.</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-tools text-primary mr-2"></i> Langkah 5: Pemecahan Masalah</h3>
            <p class="text-muted mb-4">Jika Anda menemui masalah selama konfigurasi awal, periksa solusi umum berikut:</p>
            
            <div class="doc-callout callout-error mb-4 shadow-sm border-left-danger">
                <div class="callout-icon"><i class="fas fa-database"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Error Koneksi Database</strong>
                    <p class="mb-2 text-muted small">Jika layar kosong atau muncul pesan error database:</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Cek kembali kredensial database Anda di <code>application/config/database.php</code>.</li>
                        <li class="mb-1">Pastikan server MySQL berjalan (misal: di panel kontrol XAMPP).</li>
                    </ul>
                </div>
            </div>

            <div class="doc-callout callout-warning mb-4 shadow-sm border-left-warning">
                <div class="callout-icon"><i class="fas fa-unlink"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Halaman 404 Tidak Ditemukan / Gaya Tampilan Hilang</strong>
                    <p class="mb-2 text-muted small">Jika tampilan rusak atau link mengarah ke error 404:</p>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1">Verifikasi bahwa <code>$config['base_url']</code> di <code>application/config/config.php</code> benar sepenuhnya dan diakhiri dengan garis miring <code>/</code>.</li>
                        <li class="mb-1">Periksa apakah file <code>.htaccess</code> ada di direktori root jika fitur URL rewrite (menghapus index.php) sedang aktif.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

