<div id="module-welcome" class="hc-doc-section active">

    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">Welcome to the GIDI Payment Gateway Admin Panel. This platform gives your operations team full control over merchant accounts, payment channel configurations, and transaction monitoring — all from one single dashboard.</p>

        <hr class="my-4">

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-sitemap text-primary mr-3"></i> Core Payment Flows
        </h5>
        
        <p class="text-muted mb-4" style="line-height: 1.7;">As an administrator, you manage two primary transaction ecosystems:</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:20%">Flow Type</th>
                        <th class="p-3 border-0" style="width:30%">Description</th>
                        <th class="p-3 border-0">Your Responsibility</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong><i class="fas fa-arrow-circle-down text-success mr-2"></i> Cashin</strong></td>
                        <td class="p-3 border-0 text-muted">A merchant's customer sends money <em>in</em> to the system via QRIS, VA, or E-Wallet.</td>
                        <td class="p-3 border-0 text-muted">Set up which payment channels each merchant can receive through, configure dynamic QRIS/VA settings, and manage transaction fees.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong><i class="fas fa-arrow-circle-up text-danger mr-2"></i> Cashout</strong></td>
                        <td class="p-3 border-0 text-muted">A merchant sends money <em>out</em> from the system to a bank account or endpoint.</td>
                        <td class="p-3 border-0 text-muted">Manage the master list of available disbursement channels, bank routing, and outbound processing fees.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-users text-primary mr-3"></i> Role-Based Access Control (RBAC)
        </h5>

        <p class="text-muted mb-4" style="line-height: 1.7;">This platform implements a strict permission system. What you can see and do depends on your assigned role.</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Role</th>
                        <th class="p-3 border-0">Primary Responsibilities</th>
                        <th class="p-3 border-0" style="width:35%">Key Menus Accessible</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Super Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Full platform access — can configure system settings, users, and all features.</td>
                        <td class="p-3 border-0 text-muted">All menus available</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Operations Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Merchant onboarding, payment channel configuration, and transaction monitoring.</td>
                        <td class="p-3 border-0 text-muted">Merchant Setup, Gateway Channel, Payment & Services</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Finance Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Transaction auditing, balance reviews, mutation logs, and financial reconciliations.</td>
                        <td class="p-3 border-0 text-muted">Analytics & Reports, Finance & Treasury</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Merchant Supervisor</strong></td>
                        <td class="p-3 border-0 text-muted">Monitor sub-merchant performance, view specific assigned merchant accounts.</td>
                        <td class="p-3 border-0 text-muted">Merchant Setup, Dashboard</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-book text-primary mr-3"></i> Gateway Glossary
        </h5>
        
        <p class="text-muted mb-4" style="line-height: 1.7;">A quick reference guide for common payment gateway terminology used throughout this dashboard.</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Term</th>
                        <th class="p-3 border-0">Definition</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>MDR (Merchant Discount Rate)</strong></td>
                        <td class="p-3 border-0 text-muted">The percentage or fixed fee deducted from a transaction amount as payment processing cost.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Callback / Webhook</strong></td>
                        <td class="p-3 border-0 text-muted">An automated HTTP POST request sent by the gateway to the merchant's server to notify them of a payment status change.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Settlement</strong></td>
                        <td class="p-3 border-0 text-muted">The process where funds are officially transferred from the gateway/aggregator's holding account to the merchant's available balance.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Disbursement</strong></td>
                        <td class="p-3 border-0 text-muted">Also known as Cashout. The act of sending funds from the merchant's balance to an external bank account.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-compass text-primary mr-3"></i> Quick Navigation
        </h5>

        <div class="row mb-5">
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-getting-started\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-rocket fa-2x text-primary mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Getting Started</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Learn how to log in, navigate the dashboard, and understand the main menu sections.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-merchant\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-store fa-2x text-primary mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2"><a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-merchant'); window.scrollTo(0,0); }" class="text-primary text-decoration-none">Merchant Setup</a></h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Register new merchants, manage their status, sub-accounts, and API access.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-cashin\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-arrow-circle-down fa-2x text-success mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Cashin Configuration</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Configure incoming payment channels, set dynamic service fees, and routing.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-cashout\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-arrow-circle-up fa-2x text-danger mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Cashout Configuration</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Manage master disbursement banks, E-Wallets, and outbound transaction fees.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Frequently Asked Questions</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why can't I see some menus in the sidebar?</span>
            </div>
            <div class="hc-faq-a">
                This platform uses a strict <strong>Role-Based Access Control (RBAC)</strong>. If you are assigned as a Finance Admin, you will only see menus relevant to transaction auditing. If you believe you are missing a menu, please contact your Super Admin to adjust your role access.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>How do I switch the theme (Dark/Light Mode)?</span>
            </div>
            <div class="hc-faq-a">
                You can toggle the aesthetic of the entire admin panel by clicking the <strong><i class="fas fa-sun"></i> / <i class="fas fa-moon"></i></strong> icon located in the top-right corner of the top navigation bar.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>I forgot my password or am locked out of my account?</span>
            </div>
            <div class="hc-faq-a">
                For security reasons, password resets must be initiated through the <strong>Administrative Accounts</strong> menu by a Super Admin. Please contact your internal IT support or Super Admin to generate a temporary password.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Why is my Dashboard data (charts) not updating instantly?</span>
            </div>
            <div class="hc-faq-a">
                To maintain system performance during high transaction volumes, aggregate data on the main Dashboard and charts is <strong>cached for 5-10 minutes</strong>. For real-time data, please check the Transaction History directly.
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">Selamat datang di Panel Admin GIDI Payment Gateway. Platform ini memberikan tim operasional Anda kendali penuh atas akun merchant, konfigurasi kanal pembayaran, dan pemantauan transaksi — semua dari satu dasbor tunggal.</p>

        <hr class="my-4">

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-sitemap text-primary mr-3"></i> Alur Pembayaran Utama
        </h5>
        
        <p class="text-muted mb-4" style="line-height: 1.7;">Sebagai administrator, Anda mengelola dua ekosistem transaksi utama:</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:20%">Tipe Alur</th>
                        <th class="p-3 border-0" style="width:30%">Deskripsi</th>
                        <th class="p-3 border-0">Tanggung Jawab Anda</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong><i class="fas fa-arrow-circle-down text-success mr-2"></i> Cashin</strong></td>
                        <td class="p-3 border-0 text-muted">Pelanggan merchant mengirim uang <em>masuk</em> ke sistem via QRIS, VA, atau E-Wallet.</td>
                        <td class="p-3 border-0 text-muted">Mengatur kanal pembayaran apa yang bisa diterima setiap merchant, konfigurasi dinamis, dan biaya transaksi.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong><i class="fas fa-arrow-circle-up text-danger mr-2"></i> Cashout</strong></td>
                        <td class="p-3 border-0 text-muted">Merchant mengirim uang <em>keluar</em> dari sistem ke rekening bank atau endpoint.</td>
                        <td class="p-3 border-0 text-muted">Mengelola daftar master kanal pencairan, rute bank, dan biaya pemrosesan keluar.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-users text-primary mr-3"></i> Kontrol Akses Berbasis Peran (RBAC)
        </h5>

        <p class="text-muted mb-4" style="line-height: 1.7;">Platform ini menerapkan sistem izin yang ketat. Apa yang dapat Anda lihat dan lakukan bergantung pada peran yang diberikan kepada Anda.</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Peran (Role)</th>
                        <th class="p-3 border-0">Tanggung Jawab Utama</th>
                        <th class="p-3 border-0" style="width:35%">Menu Utama yang Dapat Diakses</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Super Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Akses penuh ke platform — dapat mengonfigurasi pengaturan sistem, pengguna, dan semua fitur.</td>
                        <td class="p-3 border-0 text-muted">Semua menu tersedia</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Operations Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Onboarding merchant, konfigurasi kanal pembayaran, dan pemantauan transaksi.</td>
                        <td class="p-3 border-0 text-muted">Merchant Setup, Gateway Channel, Payment & Services</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Finance Admin</strong></td>
                        <td class="p-3 border-0 text-muted">Audit transaksi, tinjauan saldo, catatan mutasi, dan rekonsiliasi keuangan.</td>
                        <td class="p-3 border-0 text-muted">Analytics & Reports, Finance & Treasury</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Merchant Supervisor</strong></td>
                        <td class="p-3 border-0 text-muted">Memantau performa sub-merchant, melihat akun merchant tertentu yang ditugaskan.</td>
                        <td class="p-3 border-0 text-muted">Merchant Setup, Dashboard</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-book text-primary mr-3"></i> Glosarium Gateway
        </h5>
        
        <p class="text-muted mb-4" style="line-height: 1.7;">Panduan referensi cepat untuk istilah-istilah payment gateway yang sering digunakan di seluruh dasbor ini.</p>

        <div class="table-responsive mb-5 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                <thead style="background: rgba(0,0,0,0.4);">
                    <tr>
                        <th class="p-3 border-0" style="width:25%">Istilah</th>
                        <th class="p-3 border-0">Definisi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>MDR (Merchant Discount Rate)</strong></td>
                        <td class="p-3 border-0 text-muted">Persentase atau biaya tetap yang dipotong dari jumlah transaksi sebagai biaya pemrosesan pembayaran.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Callback / Webhook</strong></td>
                        <td class="p-3 border-0 text-muted">Permintaan HTTP POST otomatis yang dikirim oleh gateway ke server merchant untuk memberitahu perubahan status pembayaran.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Settlement</strong></td>
                        <td class="p-3 border-0 text-muted">Proses ketika dana secara resmi dipindahkan dari akun penampungan gateway/agregator ke saldo tersedia milik merchant.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="p-3 border-0"><strong>Disbursement</strong></td>
                        <td class="p-3 border-0 text-muted">Dikenal juga sebagai Cashout. Tindakan mengirimkan dana dari saldo merchant ke rekening bank eksternal.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-weight-bold mb-4 d-flex align-items-center">
            <i class="fas fa-compass text-primary mr-3"></i> Navigasi Cepat
        </h5>

        <div class="row mb-5">
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-getting-started\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-rocket fa-2x text-primary mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Memulai (Getting Started)</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Pelajari cara login, menavigasi dasbor, dan memahami bagian menu utama.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-merchant\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-store fa-2x text-primary mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Setup Merchant</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Daftarkan merchant baru, kelola status, sub-akun, dan akses API mereka.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-cashin\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-arrow-circle-down fa-2x text-success mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Konfigurasi Cashin</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Atur kanal pembayaran yang masuk, atur biaya layanan dinamis, dan rute (routing).</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border border-secondary rounded h-100" style="background: transparent; cursor: pointer; transition: all 0.2s;" onclick="document.querySelector('.hc-nav-item[data-target=\'module-ug-cashout\']').click()" onmouseover="this.style.borderColor='#60a5fa'; this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.borderColor=''; this.style.backgroundColor='transparent'">
                    <i class="fas fa-arrow-circle-up fa-2x text-danger mb-3"></i>
                    <h6 class="font-weight-bold text-body mt-2">Konfigurasi Cashout</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Kelola bank pencairan utama, E-Wallet, dan biaya transaksi keluar.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h4 class="font-weight-bold mb-4 mt-5 border-bottom pb-2">Pertanyaan yang Sering Diajukan (FAQ)</h4>
        
        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa saya tidak bisa melihat beberapa menu di sidebar?</span>
            </div>
            <div class="hc-faq-a">
                Platform ini menggunakan sistem <strong>Role-Based Access Control (RBAC)</strong> yang ketat. Jika Anda ditugaskan sebagai Finance Admin, Anda hanya akan melihat menu yang relevan dengan audit transaksi. Jika Anda merasa kehilangan sebuah menu, silakan hubungi Super Admin untuk menyesuaikan akses peran Anda.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Bagaimana cara mengubah tema (Mode Gelap/Terang)?</span>
            </div>
            <div class="hc-faq-a">
                Anda dapat mengubah estetika seluruh panel admin dengan mengeklik ikon <strong><i class="fas fa-sun"></i> / <i class="fas fa-moon"></i></strong> yang terletak di sudut kanan atas pada bilah navigasi.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Saya lupa kata sandi atau terkunci dari akun saya?</span>
            </div>
            <div class="hc-faq-a">
                Demi alasan keamanan, pengaturan ulang kata sandi (reset password) harus dilakukan melalui menu <strong>Administrative Accounts</strong> oleh Super Admin. Harap hubungi dukungan IT atau Super Admin Anda untuk membuat kata sandi sementara.
            </div>
        </div>

        <div class="hc-faq-item">
            <div class="hc-faq-q">
                <i class="fas fa-question-circle text-warning"></i> 
                <span>Mengapa data grafik/chart di Dasbor saya tidak update langsung?</span>
            </div>
            <div class="hc-faq-a">
                Untuk menjaga performa sistem saat volume transaksi sedang tinggi, data agregat di Dasbor utama dan grafik <strong>disimpan di cache selama 5-10 menit</strong>. Jika Anda membutuhkan data real-time detik ini juga, silakan cek langsung di menu Transaction History.
            </div>
        </div>

    </div>

</div>