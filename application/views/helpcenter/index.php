<!-- Begin Page Content -->
<div class="help-center-container">

    <!-- CSS Rules -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --hc-font: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --hc-bg: #ffffff;
            --hc-text: #475569;
            --hc-border: #e2e8f0;
            --hc-hover: #f8fafc;
            --hc-active: linear-gradient(90deg, #eff6ff 0%, transparent 100%);
            --hc-active-text: #2563eb;
            --hc-sidebar-bg: #f8fafc;
            --hc-heading: #0f172a;
            --hc-callout-note-bg: #f8fafc;
            --hc-callout-note-border: #3b82f6;
            --hc-callout-error-bg: #fef2f2;
            --hc-callout-error-border: #ef4444;
            --hc-code-bg: #f1f5f9;
            --hc-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        html[data-theme="dark"] {
            --hc-bg: #0f172a;
            --hc-text: #94a3b8;
            --hc-border: #334155;
            --hc-hover: #1e293b;
            --hc-active: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, transparent 100%);
            --hc-active-text: #60a5fa;
            --hc-sidebar-bg: #020617;
            --hc-heading: #f8fafc;
            --hc-callout-note-bg: rgba(59, 130, 246, 0.05);
            --hc-callout-note-border: #3b82f6;
            --hc-callout-error-bg: rgba(239, 68, 68, 0.05);
            --hc-callout-error-border: #ef4444;
            --hc-code-bg: #1e293b;
            --hc-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .help-center-wrapper {
            display: flex;
            background-color: var(--hc-bg);
            border: 1px solid var(--hc-border);
            /* border-radius: 16px; */
            box-shadow: var(--hc-shadow);
            overflow: hidden;
            min-height: 85vh;
            margin: -1.5rem;
            /* margin-bottom: 32px; */
            color: var(--hc-text);
            font-family: var(--hc-font);
            transition: background-color 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        /* Security Card Styling */
        .hc-security-card {
            background-color: var(--hc-bg);
            border: 1px solid var(--hc-border);
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            z-index: 1;
        }

        .hc-security-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .hc-security-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hc-shadow);
            border-color: var(--hc-active-text);
        }

        .hc-security-card:hover::before {
            opacity: 1;
        }

        .hc-security-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .hc-security-card:hover .hc-security-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        /* Sidebar Styling */
        .hc-sidebar {
            width: 280px;
            background-color: var(--hc-sidebar-bg);
            border-right: 1px solid var(--hc-border);
            padding: 32px 0;
            flex-shrink: 0;
            transition: background-color 0.4s ease, border-color 0.4s ease;
        }

        .hc-sidebar-title {
            padding: 0 32px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 16px;
            margin-top: 32px;
        }
        .hc-sidebar-title:first-child { margin-top: 0; }

        .hc-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 32px;
            color: var(--hc-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border-left: 3px solid transparent;
        }

        .hc-nav-item:hover {
            background-color: var(--hc-hover);
            color: var(--hc-heading);
            text-decoration: none;
            /* transform: translateX(4px); */
        }

        .hc-nav-item.active {
            background: var(--hc-active);
            color: var(--hc-active-text);
            border-left-color: var(--hc-active-text);
            font-weight: 600;
        }
        .hc-nav-item.active:hover { transform: none; }

        .hc-nav-item i {
            width: 28px;
            font-size: 16px;
            opacity: 0.7;
            transition: transform 0.2s;
        }
        .hc-nav-item:hover i { transform: scale(1.1); opacity: 1; }
        .hc-nav-item.active i { color: var(--hc-active-text); opacity: 1; }

        /* Content Area */
        .hc-content-area {
            flex-grow: 1;
            padding: 48px 56px;
            overflow-y: auto;
            position: relative;
        }

        .hc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--hc-border);
        }

        .hc-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--hc-heading);
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* Lang Toggle */
        .doc-lang-toggle {
            display: inline-flex;
            background: var(--hc-sidebar-bg);
            border-radius: 30px;
            padding: 6px;
            border: 1px solid var(--hc-border);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .doc-lang-btn {
            padding: 6px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            border: none;
            background: transparent;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .doc-lang-btn.active {
            background: var(--hc-active-text);
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
            transform: scale(1.05);
        }

        /* Typography & Docs Content */
        .hc-doc-section { display: none; }
        .hc-doc-section.active { display: block; animation: slideUp 0.4s ease-out; }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tech-doc-body {
            max-width: 100%;
        }
        .tech-doc-body h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--hc-heading);
            margin-top: 48px;
            margin-bottom: 20px;
            letter-spacing: -0.3px;
        }
        .tech-doc-body h2:first-child { margin-top: 0; }
        
        .tech-doc-body h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--hc-heading);
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .tech-doc-body p, .tech-doc-body li {
            font-size: 15px;
            line-height: 1.7;
            color: var(--hc-text);
            margin-bottom: 16px;
        }
        .tech-doc-body code {
            background: var(--hc-code-bg);
            color: var(--hc-heading);
            padding: 4px 8px;
            border-radius: 6px;
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 13px;
            border: 1px solid var(--hc-border);
        }
        /* Enterprise Doc Elements */
        .doc-lead {
            font-size: 17px !important;
            line-height: 1.6;
            color: var(--hc-heading);
        }
        .doc-divider {
            border: 0;
            height: 1px;
            background: var(--hc-border);
            margin: 32px 0;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            background: transparent;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 0 1px var(--hc-border);
        }
        .doc-table th, .doc-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--hc-border);
        }
        .doc-table th {
            background: rgba(255, 255, 255, 0.02);
            font-weight: 600;
            color: var(--hc-heading);
            font-size: 14px;
        }
        .doc-table td {
            font-size: 14px;
            color: var(--hc-text);
            vertical-align: top;
        }
        .doc-table tr:last-child td { border-bottom: none; }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-required { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
        .badge-optional { background: rgba(156, 163, 175, 0.1); color: #9ca3af; border: 1px solid rgba(156, 163, 175, 0.2); }
        .badge-conditional { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }


        /* Premium Callouts */
        .doc-callout {
            padding: 24px;
            border-radius: 12px;
            margin: 32px 0;
            display: flex;
            align-items: flex-start;
            border: 1px solid var(--hc-border);
            border-left: 4px solid;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease;
        }
        .doc-callout:hover {
            transform: translateY(-2px);
        }
        .callout-note {
            background-color: var(--hc-callout-note-bg);
            border-left-color: var(--hc-callout-note-border);
        }
        .callout-note .callout-icon { color: var(--hc-callout-note-border); }
        
        .callout-important {
            background-color: rgba(139, 92, 246, 0.05);
            border-left-color: #8b5cf6;
        }
        .callout-important .callout-icon { color: #8b5cf6; }

        .callout-tip {
            background-color: rgba(16, 185, 129, 0.05);
            border-left-color: #10b981;
        }
        .callout-tip .callout-icon { color: #10b981; }

        .callout-warning {
            background-color: rgba(245, 158, 11, 0.05);
            border-left-color: #f59e0b;
        }
        .callout-warning .callout-icon { color: #f59e0b; }

        .callout-error, .callout-troubleshooting {
            background-color: var(--hc-callout-error-bg);
            border-left-color: var(--hc-callout-error-border);
        }
        .callout-error .callout-icon, .callout-troubleshooting .callout-icon { color: var(--hc-callout-error-border); }
        
        .callout-icon {
            margin-right: 20px;
            font-size: 24px;
        }
        .callout-content { flex: 1; }
        .callout-content > strong {
            color: var(--hc-heading);
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        /* Akamai Grid Welcome Styling */
        .welcome-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 32px;
            max-width: 1000px;
        }
        
        .welcome-card {
            background: var(--hc-bg);
            border: 1px solid var(--hc-border);
            border-radius: 12px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
            display: block;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--hc-active-text);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .welcome-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
            border-color: var(--hc-active-text);
        }
        html[data-theme="dark"] .welcome-card:hover {
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.4);
        }
        
        .welcome-card:hover::before {
            opacity: 1;
        }
        
        .welcome-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--hc-hover);
            color: var(--hc-active-text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .welcome-card:hover .welcome-card-icon {
            background: var(--hc-active-text);
            color: #ffffff;
        }
        
        .welcome-card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--hc-heading);
            margin-bottom: 8px;
            display: block;
        }
        
        .welcome-card-desc {
            font-size: 14px;
            color: var(--hc-text);
            line-height: 1.5;
            display: block;
        }

    </style>

    <div class="help-center-wrapper">
        <!-- Sidebar Navigation -->
        <div class="hc-sidebar">
            <div class="hc-sidebar-title">Tutorials & User Guide</div>
            <a href="javascript:void(0);" class="hc-nav-item active" data-target="module-welcome">
                <i class="fas fa-home"></i> Welcome
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-getting-started">
                <i class="fas fa-rocket"></i> Getting Started
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-merchant">
                <i class="fas fa-store"></i> Merchant Setup
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-ug-cashin">
                <i class="fas fa-desktop"></i> Cashin Dashboard Guide
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-ug-cashout">
                <i class="fas fa-desktop"></i> Cashout Dashboard Guide
            </a>

            <div class="hc-sidebar-title">Security & Configuration</div>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-login">
                <i class="fas fa-sign-in-alt"></i> Authentication
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-rbac">
                <i class="fas fa-users-cog"></i> RBAC & Permissions
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-account">
                <i class="fas fa-user-circle"></i> Account Configuration
            </a>

            <div class="hc-sidebar-title">API & Technical Reference</div>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-secret">
                <i class="fas fa-key"></i> Managing Secret Keys
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-api-cashin">
                <i class="fas fa-code"></i> Cashin API Reference
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-api-cashout">
                <i class="fas fa-code"></i> Cashout API Reference
            </a>

            <div class="hc-sidebar-title">Architecture</div>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-history">
                <i class="fas fa-history"></i> Transaction History
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-qris">
                <i class="fas fa-qrcode"></i> QRIS Architecture
            </a>
        </div>

        <!-- Content Area -->
        <div class="hc-content-area">
            <div class="hc-header">
                <h1 id="hc-main-title">Welcome to Help Center</h1>
                
                <!-- Language Toggle -->
                <div class="doc-lang-toggle">
                    <button type="button" class="doc-lang-btn active" data-lang="en">EN</button>
                    <button type="button" class="doc-lang-btn" data-lang="id">ID</button>
                </div>
            </div>

            <div class="tech-doc-body">

                <!-- MODULES -->
                <?php include APPPATH . 'views/helpcenter/modules/welcome.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/getting-started.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/merchant.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/userguide-cashin.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/userguide-cashout.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/login.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/rbac.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/account.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/secret.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/api-cashin.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/api-cashout.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/history.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/qris.php'; ?>
                <script>
        (function() {
            let currentLang = 'en';
            const navItems = document.querySelectorAll('.hc-nav-item');

            // Handle Hash deep linking on load
            if(window.location.hash) {
                const targetId = window.location.hash.replace('#', '');
                let targetModule = '';
                if(targetId === 'cashin-fee-settings') targetModule = 'module-cashin';
                else if(document.getElementById('module-' + targetId)) targetModule = 'module-' + targetId;
                else targetModule = 'module-welcome'; // fallback to welcome
                
                if(targetModule) {
                    activateModule(targetModule);
                }
            } else {
                // Default to Welcome module if no hash
                activateModule('module-welcome');
            }

            // Sidebar Navigation
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    activateModule(targetId);
                });
            });

            function activateModule(targetId) {
                // Update Sidebar
                navItems.forEach(nav => nav.classList.remove('active'));
                const activeNav = document.querySelector(`.hc-nav-item[data-target="${targetId}"]`);
                if(activeNav) {
                    activeNav.classList.add('active');
                    // Update Title
                    let newTitle = activeNav.textContent.trim();
                    if(targetId === 'module-welcome') newTitle = 'Welcome to Help Center';
                    document.getElementById('hc-main-title').textContent = newTitle;
                }

                // Update Content
                document.querySelectorAll('.hc-doc-section').forEach(sec => sec.classList.remove('active'));
                const targetSec = document.getElementById(targetId);
                if(targetSec) {
                    targetSec.classList.add('active');
                }

                // Update URL Hash without jump
                if(targetId === 'module-welcome') {
                    history.pushState(null, null, ' '); // Clear hash
                } else if(targetId === 'module-cashin') {
                    history.pushState(null, null, '#cashin-fee-settings');
                } else {
                    history.pushState(null, null, '#' + targetId.replace('module-', ''));
                }

                // Dynamic Pagination Update
                const pagContainer = document.getElementById('docPagination');
                const btnPrev = document.getElementById('btnPrevDoc');
                const btnNext = document.getElementById('btnNextDoc');
                const textPrev = document.getElementById('textPrevDoc');
                const textNext = document.getElementById('textNextDoc');

                if (pagContainer) {
                    if (targetId === 'module-welcome') {
                        pagContainer.style.display = 'none';
                    } else {
                        pagContainer.style.display = 'block';
                        
                        let currentIndex = -1;
                        const validNavs = Array.from(navItems);
                        validNavs.forEach((nav, index) => {
                            if (nav.getAttribute('data-target') === targetId) {
                                currentIndex = index;
                            }
                        });

                        if (currentIndex > 1) { // Index 0 is Welcome. Let's say Prev points to previous valid item. Actually Welcome is index 0. If current is 1, Prev is Welcome.
                            const prevNav = validNavs[currentIndex - 1];
                            btnPrev.classList.remove('d-none');
                            btnPrev.setAttribute('data-target', prevNav.getAttribute('data-target'));
                            textPrev.textContent = prevNav.textContent.trim();
                        } else if (currentIndex === 1) {
                            const prevNav = validNavs[0];
                            btnPrev.classList.remove('d-none');
                            btnPrev.setAttribute('data-target', prevNav.getAttribute('data-target'));
                            textPrev.textContent = 'Welcome';
                        } else {
                            btnPrev.classList.add('d-none');
                        }

                        if (currentIndex !== -1 && currentIndex < validNavs.length - 1) {
                            const nextNav = validNavs[currentIndex + 1];
                            btnNext.classList.remove('d-none');
                            btnNext.setAttribute('data-target', nextNav.getAttribute('data-target'));
                            textNext.textContent = nextNav.textContent.trim();
                        } else {
                            btnNext.classList.add('d-none');
                        }
                    }
                }
            }

            // Pagination Click Events
            document.getElementById('btnPrevDoc').addEventListener('click', function(e) {
                e.preventDefault();
                activateModule(this.getAttribute('data-target'));
                window.scrollTo(0, 0);
            });
            document.getElementById('btnNextDoc').addEventListener('click', function(e) {
                e.preventDefault();
                activateModule(this.getAttribute('data-target'));
                window.scrollTo(0, 0);
            });

            // Language Toggle
            const langBtns = document.querySelectorAll('.doc-lang-btn');
            langBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const selectedLang = this.getAttribute('data-lang');
                    currentLang = selectedLang;
                    
                    langBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    document.querySelectorAll('.lang-content').forEach(c => c.style.display = 'none');
                    document.querySelectorAll('.lang-' + selectedLang).forEach(c => c.style.display = 'block');
                });
            });
        })();
    </script>
</div>
<!-- /.container-fluid -->
