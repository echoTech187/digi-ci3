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
            overflow-y: auto; /* Added to handle taller content */
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

        /* Submenu Styling */
        .hc-nav-subitem {
            padding: 10px 32px 10px 56px;
            font-size: 13px;
            color: #64748b;
        }
        html[data-theme="dark"] .hc-nav-subitem {
            color: #94a3b8;
        }
        .hc-nav-subitem i {
            font-size: 12px;
            width: 22px;
            opacity: 0.6;
        }
        .hc-nav-subitem:hover i {
            opacity: 0.9;
        }

        .hc-submenu-icon {
            margin-left: auto;
            transition: transform 0.2s ease;
        }
        .hc-nav-item.expanded .hc-submenu-icon {
            transform: rotate(180deg);
        }
        .hc-submenu-container {
            overflow: hidden;
        }

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
        .hc-doc-section { 
            position: absolute;
            top: 0; left: 0; right: 0;
            opacity: 0;
            pointer-events: none;
            z-index: -1;
            visibility: hidden;
        }
        .hc-doc-section.active { 
            position: relative;
            opacity: 1;
            pointer-events: auto;
            z-index: 1;
            visibility: visible;
            animation: slideUp 0.4s ease-out forwards; 
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tech-doc-body {
            max-width: 100%;
            position: relative;
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
        
        /* Force code blocks with Bootstrap classes to adapt to the theme */
        .tech-doc-body code.bg-dark {
            background-color: var(--hc-sidebar-bg) !important;
            color: var(--hc-heading) !important;
        }
        .tech-doc-body code.text-body {
            color: var(--hc-heading) !important;
        }

        /* Force table headers to adapt to the theme */
        .tech-doc-body table thead {
            background-color: var(--hc-sidebar-bg) !important;
            color: var(--hc-heading) !important;
        }
        
        .tech-doc-body table thead th {
            color: var(--hc-heading) !important;
            font-weight: 600;
        }

        /* Custom Code Block with Copy Button */
        .hc-code-block {
            position: relative;
            background: var(--hc-sidebar-bg);
            border: 1px solid var(--hc-border);
            border-radius: 8px;
            margin-top: 12px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .hc-code-block pre {
            margin: 0;
            padding: 16px;
            padding-top: 42px; /* space for button */
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 13px;
            color: var(--hc-heading) !important;
            line-height: 1.6;
            overflow-x: auto;
            background: transparent !important;
            border: none;
        }
        .hc-code-block .btn-copy-code {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--hc-bg);
            border: 1px solid var(--hc-border);
            color: var(--hc-text);
            padding: 4px 10px;
            font-size: 11px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }
        .hc-code-block .btn-copy-code:hover {
            background: var(--hc-hover);
            color: var(--hc-heading);
        }
        .hc-code-block .btn-copy-code.copied {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.2);
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
        .tech-doc-body table thead {
            background: var(--hc-sidebar-bg) !important;
        }
        .doc-table th, .doc-table td, .tech-doc-body table th, .tech-doc-body table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--hc-border);
        }
        .doc-table th, .tech-doc-body table th {
            font-weight: 600;
            color: var(--hc-heading) !important;
            font-size: 14px;
        }
        .doc-table td, .tech-doc-body table td {
            font-size: 14px;
            color: var(--hc-text);
            vertical-align: top;
        }
        .doc-table tr:last-child td, .tech-doc-body table tr:last-child td { border-bottom: none; }
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
        /* Glossary Tooltips */
        .hc-tooltip {
            position: relative;
            border-bottom: 1px dotted var(--hc-active-text);
            cursor: help;
            color: var(--hc-active-text);
        }
        .hc-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1e293b;
            color: #f8fafc;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            box-shadow: var(--hc-shadow);
            z-index: 100;
        }
        .hc-tooltip::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 100;
        }
        .hc-tooltip:hover::after,
        .hc-tooltip:hover::before {
            opacity: 1;
            visibility: visible;
        }

        /* Code Snippets */
        .hc-code-block {
            position: relative;
            background: #1e1e1e;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .hc-code-block pre {
            margin: 0;
            color: #d4d4d4;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }
        .btn-copy-code {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            opacity: 0;
        }
        .hc-code-block:hover .btn-copy-code {
            opacity: 1;
        }
        .btn-copy-code:hover {
            background: rgba(255,255,255,0.2);
        }
        .btn-copy-code.copied {
            background: #10b981;
            color: #fff;
        }

        /* Callouts */
        .doc-callout {
            display: flex;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
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

            <!-- Search Bar -->
            <div class="hc-search-wrapper" style="position: relative; width: calc(100% - 40px); margin: 10px auto 20px auto;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 11px; color: #94a3b8; font-size: 13px;"></i>
                    <input type="text" id="hc-doc-search" class="form-control" placeholder="Search docs..." style="background: var(--hc-bg); border: 1px solid var(--hc-border); border-radius: 20px; padding-left: 36px; padding-right: 14px; color: var(--hc-text); height: 36px; font-size: 13px; width: 100%; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); transition: border-color 0.2s, box-shadow 0.2s;">
                </div>
                <!-- Dropdown pops out to the right to avoid clipping -->
                <div id="hc-search-results" style="display:none; position: fixed; top: 140px; left: 300px; width: 450px; background: var(--hc-bg); border: 1px solid var(--hc-border); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); max-height: 400px; overflow-y: auto; z-index: 1050; padding: 6px 0;">
                    <!-- Results injected here -->
                </div>
            </div>

            <div class="hc-sidebar-title" style="margin-top: 0;">Tutorials & User Guide</div>
            <a href="javascript:void(0);" class="hc-nav-item active" data-target="module-welcome">
                <i class="fas fa-home"></i> Welcome
            </a>
            <a href="javascript:void(0);" class="hc-nav-item" data-target="module-getting-started">
                <i class="fas fa-rocket"></i> Getting Started
            </a>

            <!-- Dashboards & Analytics -->
            <a href="javascript:void(0);" class="hc-nav-item hc-has-submenu" data-submenu="submenu-analytics">
                <i class="fas fa-chart-line"></i> <span>Monitoring & Analytics</span>
                <i class="fas fa-chevron-down hc-submenu-icon" style="width:auto; font-size:10px; opacity:0.5;"></i>
            </a>
            <div id="submenu-analytics" class="hc-submenu-container" style="display: none;">
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-dashboard">
                    <i class="fas fa-chart-pie"></i> Executive Dashboard
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-report">
                    <i class="fas fa-file-excel"></i> Transaction Reports
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-global-search">
                    <i class="fas fa-search"></i> Global Search Engine
                </a>
            </div>

            <!-- Merchant Administration -->
            <a href="javascript:void(0);" class="hc-nav-item hc-has-submenu" data-target="module-merchant" data-submenu="submenu-merchant">
                <i class="fas fa-store"></i> <span>Merchant Setup</span>
                <i class="fas fa-chevron-down hc-submenu-icon" style="width:auto; font-size:10px; opacity:0.5;"></i>
            </a>
            <div id="submenu-merchant" class="hc-submenu-container" style="display: none;">
                <!-- Profile & Access -->
                <div style="padding: 12px 32px 4px 56px; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Profile & Access</div>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-detail">
                    Detail Merchant
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-edit">
                    Edit Merchant
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-subaccount">
                    Sub Accounts
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-delegate">
                    Delegate
                </a>

                <!-- Financial Balances -->
                <div style="padding: 12px 32px 4px 56px; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Financial Balances</div>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-mutation">
                    Mutation Log
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-credit">
                    Add Credit Balance
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-debit">
                    Deduct Debit Balance
                </a>

                <!-- Payment Settings -->
                <div style="padding: 12px 32px 4px 56px; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Payment Settings</div>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-cashin">
                    Cashin Settings
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-merchant-cashout">
                    Cashout Settings
                </a>
            </div>

            <!-- Payment Channels & Products -->
            <a href="javascript:void(0);" class="hc-nav-item hc-has-submenu" data-submenu="submenu-products">
                <i class="fas fa-layer-group"></i> <span>Payment & Services</span>
                <i class="fas fa-chevron-down hc-submenu-icon" style="width:auto; font-size:10px; opacity:0.5;"></i>
            </a>
            <div id="submenu-products" class="hc-submenu-container" style="display: none;">
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-cashin">
                    <i class="fas fa-arrow-circle-down"></i> Cash-In Providers
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-cashout">
                    <i class="fas fa-arrow-circle-up"></i> Cash-Out Providers
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-va-dynamic">
                    <i class="fas fa-university"></i> VA Dynamic
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-va-recurring">
                    <i class="fas fa-history"></i> VA Recurring
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-qris-dynamic">
                    <i class="fas fa-qrcode"></i> QRIS Dynamic
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-qris-recurring">
                    <i class="fas fa-redo"></i> QRIS Recurring
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-ewallet-dynamic">
                    <i class="fas fa-wallet"></i> E-Wallet Dynamic
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-prepaid-products">
                    <i class="fas fa-mobile-alt"></i> Prepaid Products
                </a>
            </div>

            <!-- Finance & Treasury -->
            <a href="javascript:void(0);" class="hc-nav-item hc-has-submenu" data-submenu="submenu-finance">
                <i class="fas fa-coins"></i> <span>Finance & Treasury</span>
                <i class="fas fa-chevron-down hc-submenu-icon" style="width:auto; font-size:10px; opacity:0.5;"></i>
            </a>
            <div id="submenu-finance" class="hc-submenu-container" style="display: none;">
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-external-balance-log">
                    <i class="fas fa-balance-scale"></i> External Balance Log
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-bifast">
                    <i class="fas fa-fighter-jet"></i> Bi-Fast Transfer
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-financial-exports">
                    <i class="fas fa-file-export"></i> Financial Exports
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-balance-logs">
                    <i class="fas fa-book"></i> Audit Balance Logs
                </a>
            </div>

            <!-- System Administration -->
            <a href="javascript:void(0);" class="hc-nav-item hc-has-submenu" data-submenu="submenu-system">
                <i class="fas fa-cogs"></i> <span>System Admin</span>
                <i class="fas fa-chevron-down hc-submenu-icon" style="width:auto; font-size:10px; opacity:0.5;"></i>
            </a>
            <div id="submenu-system" class="hc-submenu-container" style="display: none;">
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-login">
                    <i class="fas fa-sign-in-alt"></i> Authentication
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-rbac">
                    <i class="fas fa-users-cog"></i> RBAC & Permissions
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-account">
                    <i class="fas fa-user-circle"></i> Account Setup
                </a>
                <a href="javascript:void(0);" class="hc-nav-item hc-nav-subitem" data-target="module-ug-channel">
                    <i class="fas fa-exchange-alt"></i> Gateway Channel
                </a>
            </div>

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


            <div class="hc-header" style="align-items: flex-end; display: flex; justify-content: space-between;">
                <div style="flex-shrink: 0; padding-right: 20px;">
                    <div id="hc-breadcrumbs" class="text-muted small mb-2" style="font-weight: 500; font-size: 0.85rem;">Home > Welcome</div>
                    <h1 id="hc-main-title">Welcome to Help Center</h1>
                </div>
                
                <div style="display: flex; align-items: center; gap: 16px; flex-grow: 1; justify-content: flex-end; position: relative;">
                    <!-- Language Toggle -->
                    <div class="doc-lang-toggle" style="flex-shrink: 0;">
                        <button type="button" class="doc-lang-btn active" data-lang="en">EN</button>
                        <button type="button" class="doc-lang-btn" data-lang="id">ID</button>
                    </div>
                </div>
            </div>

            <div class="tech-doc-body">

                <!-- MODULES -->
                <?php include APPPATH . 'views/helpcenter/modules/welcome.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/getting-started.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-dashboard.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-global-search.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/merchant.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/userguide-cashin.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/userguide-cashout.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-detail.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-edit.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-mutation.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-subaccount.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-delegate.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-credit.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-debit.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-cashin.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-merchant-cashout.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-va-dynamic.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-qris-recurring.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-va-recurring.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-ewallet-dynamic.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-qris-dynamic.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-prepaid-products.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-financial-gateway.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-balance-logs.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-financial-exports.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/login.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/rbac.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/account.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/secret.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/api-cashin.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/api-cashout.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/history.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/qris.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-bifast.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-channel.php'; ?>
                <?php include APPPATH . 'views/helpcenter/modules/ug-report.php'; ?>
                <!-- Universal Feedback Widget -->
                <div id="hc-feedback-widget" class="mt-5 pt-4 pb-4 border-top text-center" style="border-color: var(--hc-border) !important;">
                    <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Was this page helpful?</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm mx-1 px-3 feedback-btn" data-value="yes" style="border-radius: 20px; transition: all 0.2s;"><i class="far fa-thumbs-up mr-1"></i> Yes</button>
                        <button class="btn btn-outline-secondary btn-sm mx-1 px-3 feedback-btn" data-value="no" style="border-radius: 20px; transition: all 0.2s;"><i class="far fa-thumbs-down mr-1"></i> No</button>
                    </div>
                    <div id="hc-feedback-thanks" class="text-success small mt-3" style="display: none; font-weight: 500;">
                        <i class="fas fa-check-circle mr-1"></i> Thank you for your feedback!
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="doc-pagination mt-5 pt-4" id="docPagination" style="display:none; border-top: 1px solid var(--hc-border);">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" id="btnPrevDoc" class="text-decoration-none" style="display:none; padding: 16px; border: 1px solid var(--hc-border); border-radius: 8px; transition: all 0.2s;">
                                <div class="text-muted small mb-1">Previous</div>
                                <div class="font-weight-bold" style="color: var(--hc-active-text); font-size: 16px;"><i class="fas fa-chevron-left mr-2"></i> <span id="textPrevDoc"></span></div>
                            </a>
                        </div>
                        <div class="col-6 text-right">
                            <a href="javascript:void(0);" id="btnNextDoc" class="text-decoration-none" style="display:none; padding: 16px; border: 1px solid var(--hc-border); border-radius: 8px; transition: all 0.2s;">
                                <div class="text-muted small mb-1">Next</div>
                                <div class="font-weight-bold" style="color: var(--hc-active-text); font-size: 16px;"><span id="textNextDoc"></span> <i class="fas fa-chevron-right ml-2"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div> <!-- End tech-doc-body -->
        </div> <!-- End hc-content-area -->
    </div> <!-- End Main Container Wrapper -->

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
                    
                    // Handle Submenu Toggle
                    const submenuId = this.getAttribute('data-submenu');
                    if (submenuId) {
                        const submenu = document.getElementById(submenuId);
                        if (submenu) {
                            if (submenu.style.display === 'none') {
                                submenu.style.display = 'block';
                                this.classList.add('expanded');
                            } else {
                                submenu.style.display = 'none';
                                this.classList.remove('expanded');
                            }
                        }
                    }

                    const targetId = this.getAttribute('data-target');
                    if(targetId) {
                        activateModule(targetId);
                    }
                });
            });

            function activateModule(targetId) {
                // Update Sidebar
                navItems.forEach(nav => nav.classList.remove('active'));
                const activeNav = document.querySelector(`.hc-nav-item[data-target="${targetId}"]`);
                if(activeNav) {
                    activeNav.classList.add('active');
                    
                    // Expand parent submenu if inside one
                    const parentSubmenu = activeNav.closest('.hc-submenu-container');
                    if (parentSubmenu) {
                        parentSubmenu.style.display = 'block';
                        const parentTrigger = document.querySelector(`[data-submenu="${parentSubmenu.id}"]`);
                        if (parentTrigger) {
                            parentTrigger.classList.add('expanded');
                        }
                    }

                    // Auto-scroll sidebar to the active item
                    setTimeout(() => {
                        activeNav.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 50);

                    // Update Title
                    let newTitle = activeNav.textContent.trim();
                    if(targetId === 'module-welcome') newTitle = 'Welcome to Help Center';
                    document.getElementById('hc-main-title').textContent = newTitle;

                    // Update Breadcrumbs
                    let breadcrumbText = 'Home > ' + newTitle;
                    if (parentSubmenu) {
                        const parentTrigger = document.querySelector(`[data-submenu="${parentSubmenu.id}"]`);
                        if (parentTrigger) {
                            breadcrumbText = 'Home > ' + parentTrigger.textContent.trim() + ' > ' + newTitle;
                        }
                    }
                    if(targetId === 'module-welcome') breadcrumbText = 'Home > Welcome';
                    document.getElementById('hc-breadcrumbs').textContent = breadcrumbText;
                }

                // Update Content
                document.querySelectorAll('.hc-doc-section').forEach(sec => sec.classList.remove('active'));
                const targetSec = document.getElementById(targetId);
                if(targetSec) {
                    targetSec.classList.add('active');
                    // Apply the current language to the newly activated section only
                    applyLanguage(targetSec);
                }
                
                // Reset Feedback Widget
                if (window.resetFeedbackWidget) {
                    window.resetFeedbackWidget();
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
                    pagContainer.style.display = 'block';
                    
                    // Only consider items that actually point to a module (have data-target)
                    const validNavs = Array.from(navItems).filter(nav => nav.getAttribute('data-target'));
                    const currentIndex = validNavs.findIndex(nav => nav.getAttribute('data-target') === targetId);

                        if (currentIndex > 0) {
                            const prevNav = validNavs[currentIndex - 1];
                            btnPrev.style.display = 'block';
                            btnPrev.setAttribute('data-target', prevNav.getAttribute('data-target'));
                            textPrev.textContent = prevNav.textContent.trim();
                        } else {
                            btnPrev.style.display = 'none';
                        }

                        if (currentIndex !== -1 && currentIndex < validNavs.length - 1) {
                            const nextNav = validNavs[currentIndex + 1];
                            btnNext.style.display = 'block';
                            btnNext.setAttribute('data-target', nextNav.getAttribute('data-target'));
                            textNext.textContent = nextNav.textContent.trim();
                        } else {
                            btnNext.style.display = 'none';
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

            // Expose globally for inline links
            window.activateHelpModule = activateModule;

            // Algolia-Style Quick Search
            const searchInput = document.getElementById('hc-doc-search');
            const searchResults = document.getElementById('hc-search-results');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    if (query.length < 2) {
                        searchResults.style.display = 'none';
                        return;
                    }
                    
                    let resultsHTML = '';
                    let matchCount = 0;

                    // Search through all modules
                    document.querySelectorAll('.hc-doc-section').forEach(section => {
                        const targetId = section.id;
                        // Find corresponding nav item to get title
                        const navItem = document.querySelector(`.hc-nav-item[data-target="${targetId}"]`);
                        if (!navItem) return;
                        
                        const title = navItem.textContent.trim();
                        // Only search in the visible language blocks
                        const langBlocks = section.querySelectorAll('.lang-' + currentLang);
                        let textContent = '';
                        langBlocks.forEach(b => textContent += b.textContent + ' ');
                        
                        const lowerText = textContent.toLowerCase();
                        const queryIndex = lowerText.indexOf(query);
                        
                        if (queryIndex !== -1 || title.toLowerCase().includes(query)) {
                            matchCount++;
                            // Create excerpt
                            let snippet = '';
                            if (queryIndex !== -1) {
                                const start = Math.max(0, queryIndex - 40);
                                const end = Math.min(textContent.length, queryIndex + 60);
                                snippet = textContent.substring(start, end).replace(/\n/g, ' ').trim();
                                // Highlight query
                                const regex = new RegExp(`(${query})`, 'gi');
                                snippet = snippet.replace(regex, '<span style="background: rgba(234, 179, 8, 0.3); color: #eab308; font-weight: bold;">$1</span>');
                                snippet = '...' + snippet + '...';
                            }
                            
                            resultsHTML += `
                                <div style="padding: 10px 16px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05);" 
                                     onmouseover="this.style.background='var(--hc-hover)'" 
                                     onmouseout="this.style.background='transparent'"
                                     onclick="window.activateHelpModule('${targetId}'); document.getElementById('hc-search-results').style.display='none'; document.getElementById('hc-doc-search').value=''; window.scrollTo(0,0);">
                                    <div style="font-weight: 600; font-size: 13px; color: var(--hc-heading); margin-bottom: 4px;">${title}</div>
                                    <div style="font-size: 11px; color: #64748b; line-height: 1.4;">${snippet}</div>
                                </div>
                            `;
                        }
                    });

                    if (matchCount > 0) {
                        searchResults.innerHTML = resultsHTML;
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div style="padding: 16px; text-align: center; color: #64748b; font-size: 12px;">No results found for "' + query + '"</div>';
                        searchResults.style.display = 'block';
                    }
                });

                // Hide search when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.style.display = 'none';
                    }
                });
            }

            // Language Toggle
            function applyLanguage(section) {
                if (!section) return;
                section.querySelectorAll('.lang-content').forEach(c => c.style.display = 'none');
                section.querySelectorAll('.lang-' + currentLang).forEach(c => c.style.display = 'block');
            }

            const langBtns = document.querySelectorAll('.doc-lang-btn');
            langBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    currentLang = this.getAttribute('data-lang');

                    langBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Only toggle language inside the CURRENTLY ACTIVE section
                    const activeSection = document.querySelector('.hc-doc-section.active');
                    applyLanguage(activeSection);
                });
            });

            // Global Copy function for Code Snippets
            window.copyCode = function(btn, codeId) {
                const codeEl = document.getElementById(codeId);
                if (!codeEl) return;
                
                const text = codeEl.innerText || codeEl.textContent;
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('copied');
                    }, 2000);
                });
            };

            // Feedback Widget Logic
            const feedbackBtns = document.querySelectorAll('.feedback-btn');
            const feedbackThanks = document.getElementById('hc-feedback-thanks');
            feedbackBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    feedbackBtns.forEach(b => b.style.display = 'none');
                    feedbackThanks.style.display = 'block';
                });
            });

            // Make sure Feedback Widget reset is globally available
            window.resetFeedbackWidget = function() {
                if(feedbackBtns && feedbackThanks) {
                    feedbackBtns.forEach(b => b.style.display = 'inline-block');
                    feedbackThanks.style.display = 'none';
                }
            };
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            mermaid.initialize({ startOnLoad: true, theme: 'base', themeVariables: { primaryColor: '#f8fafc', primaryTextColor: '#0f172a', primaryBorderColor: '#cbd5e1', lineColor: '#64748b', secondaryColor: '#f1f5f9', tertiaryColor: '#fff' } });
        });
    </script>
</div>
<!-- /.container-fluid -->
