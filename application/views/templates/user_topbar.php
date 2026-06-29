<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light navbar-premium static-top">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 text-primary">
                <i class="fa fa-bars"></i>
            </button>

            <!-- ── Breadcrumbs (Laptop+) ── -->
            <?php
            $segments = $this->uri->segment_array();
            $breadcrumb = [];
            $current_url = '';
            foreach ($segments as $segment) {
                $current_url .= '/' . $segment;
                
                // Default display name
                $displayName = ucwords(str_replace(['_', '-'], ' ', $segment));
                
                // 1. Priority: Explicit replacement from Controller
                if (isset($breadcrumb_replace) && isset($breadcrumb_replace[$segment])) {
                    $displayName = $breadcrumb_replace[$segment];
                } 
                // 2. Fallback: If segment is numeric (ID) and no replacement provided, mask it
                elseif (is_numeric($segment)) {
                    $displayName = '#' . $segment;
                }

                // 3. URL Override & Unclickable Fallback
                // Berikut adalah daftar segment yang murni HANYA berupa grup (tanpa halaman index) 
                // atau action method (butuh parameter).
                $unclickable_segments = [
                    // Grup Modul Utama (tidak ada halamannya sendiri)
                    'finance', 'merchant', 'report', 'access-control', 'external', 'channel', 'product', 'health', 'auth', 'games',
                    
                    // Aksi / Method (Pasti error kalau dibuka tanpa parameter / POST)
                    'detail', 'edit', 'update', 'delete', 'add', 'create', 'bulk-create', 'reset', 'download',
                    'notification', 'resend', 'ajax', 'json', 'save', 'get', 'search', 'groups', 'register',
                    'history-ajax', 'mutation-ajax', 'submerchant-ajax', 'overview-ajax', 'bulk-update',
                    'get-channels', 'get-filter-options', 'get-merchant-mappings', 'change-password', 'forgot-password', 'reset-password',
                    'verify', 'blocked', 'toggle-openapi', 'maintenance-status', 'sync-balance', 'recent-mutations', 'today-stats',
                    'monthly-stats', 'metadata', 'analytics-data', 'global-search', 'recent-search', 'channels',
                    'setting-cashin-fee', 'setting-cashout-fee', 'balance', 'credit', 'debit', 'permissions'
                ];
                
                $finalUrl = base_url($current_url);
                if (isset($breadcrumb_url_replace) && isset($breadcrumb_url_replace[$segment])) {
                    $finalUrl = base_url($breadcrumb_url_replace[$segment]);
                } elseif (in_array(strtolower($segment), $unclickable_segments) || is_numeric($segment)) {
                    $finalUrl = 'javascript:void(0)';
                }

                $breadcrumb[] = [
                    'name' => $displayName,
                    'url' => $finalUrl
                ];
            }
            ?>
            <nav aria-label="breadcrumb" class="d-none d-lg-block">
                <ol class="dt-breadcrumb align-items-center">
                    <li class="dt-breadcrumb-item"><a href="<?= base_url('dashboard') ?>" title="Home" style="font-weight: 600;">Home</a></li>
                    <?php
                    $breadcrumbCount = count($breadcrumb);
                    $maxVisible = 1; // Show only the last item normally

                    if ($breadcrumbCount > $maxVisible) {
                        $hiddenItems = array_slice($breadcrumb, 0, $breadcrumbCount - $maxVisible);
                        $visibleItems = array_slice($breadcrumb, $breadcrumbCount - $maxVisible);
                        
                        // Render ellipsis dropdown
                        ?>
                        <li class="dt-breadcrumb-separator"><i class="fas fa-chevron-right" style="font-size: 10px;"></i></li>
                        <li class="dt-breadcrumb-item dropdown">
                            <a href="#" class="text-decoration-none d-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight: 700; font-size: 16px; margin-top: -6px; letter-spacing: 1px;">
                                ... <i class="fas fa-chevron-down ml-1" style="font-size: 10px; margin-top: 6px;"></i>
                            </a>
                            <div class="dropdown-menu shadow-sm border-0 animated--fade-in p-2 mt-2" style="border-radius: 8px;">
                                <?php foreach ($hiddenItems as $hItem): ?>
                                    <a class="dropdown-item rounded py-2 font-weight-bold" href="<?= $hItem['url'] ?>"><?= $hItem['name'] ?></a>
                                <?php endforeach; ?>
                            </div>
                        </li>
                        <?php
                        
                        // Render visible items
                        foreach ($visibleItems as $index => $item):
                            $isLast = ($index === count($visibleItems) - 1);
                            ?>
                            <li class="dt-breadcrumb-separator"><i class="fas fa-chevron-right" style="font-size: 10px;"></i></li>
                            <li class="dt-breadcrumb-item">
                                <?php if ($isLast): ?>
                                    <span class="text-dark font-weight-bold"><?= $item['name'] ?></span>
                                <?php else: ?>
                                    <a href="<?= $item['url'] ?>" style="font-weight: 600;"><?= $item['name'] ?></a>
                                <?php endif; ?>
                            </li>
                            <?php
                        endforeach;

                    } else {
                        // Render normally
                        foreach ($breadcrumb as $index => $item):
                            $isLast = ($index === $breadcrumbCount - 1);
                            ?>
                            <li class="dt-breadcrumb-separator"><i class="fas fa-chevron-right" style="font-size: 10px;"></i></li>
                            <li class="dt-breadcrumb-item">
                                <?php if ($isLast): ?>
                                    <span class="text-dark font-weight-bold"><?= $item['name'] ?></span>
                                <?php else: ?>
                                    <a href="<?= $item['url'] ?>" style="font-weight: 600;"><?= $item['name'] ?></a>
                                <?php endif; ?>
                            </li>
                            <?php
                        endforeach;
                    }
                    ?>
                </ol>
            </nav>
            
            <!-- Global Search -->
            <div class="d-none d-lg-block ml-4 flex-grow-1" style="max-width: 450px;">
                <div class="premium-search-container">
                    <?php
                        $segment1 = $this->uri->segment(1);
                        $segment2 = $this->uri->segment(2);
                        $active_term = '';
                        
                        if ($segment1 == 'finance' && $segment2 == 'qris') {
                            $active_term = $this->session->userdata('last_dt_search_qris');
                        } elseif ($segment1 == 'finance' && $segment2 == 'virtual_account') {
                            $active_term = $this->session->userdata('last_dt_search_va');
                        } elseif ($segment1 == 'finance' && $segment2 == 'ewallet') {
                            $active_term = $this->session->userdata('last_dt_search_ewallet');
                        } elseif ($segment1 == 'finance' && $segment2 == 'bi_fast') {
                            $active_term = $this->session->userdata('last_dt_search_bifast');
                        } elseif ($segment1 == 'finance' && $segment2 == 'history') {
                            $active_term = $this->session->userdata('last_dt_search_history');
                        } elseif ($segment1 == 'finance' && $segment2 == 'mutation') {
                            $active_term = $this->session->userdata('last_dt_search_mutation');
                        } elseif ($segment1 == 'qris' && $segment2 == 'dynamic') {
                            $active_term = $this->session->userdata('last_dt_search_qrisdynamic');
                        } elseif ($segment1 == 'e-wallet' && $segment2 == 'dynamic') {
                            $active_term = $this->session->userdata('last_dt_search_ewalletdynamic');
                        } elseif ($segment1 == 'qris' && $segment2 == 'recurring') {
                            $active_term = $this->session->userdata('last_dt_search_qrisrecurring');
                        } elseif ($segment1 == 'virtual-account' && $segment2 == 'dynamic') {
                            $active_term = $this->session->userdata('last_dt_search_vadynamic');
                        } elseif ($segment1 == 'virtual-account' && $segment2 == 'recurring') {
                            $active_term = $this->session->userdata('last_dt_search_varecurring');
                        } elseif ($segment1 == 'merchant' && $segment2 == 'manage') {
                            $active_term = $this->session->userdata('search_merchant');
                        } elseif ($segment1 == 'access-control' && $segment2 == 'accounts') {
                            $active_term = $this->session->userdata('search_admin');
                        } elseif ($segment1 == 'merchant' && $segment2 == 'supervisor') {
                            $active_term = $this->session->userdata('search_spv');
                        } elseif ($segment1 == 'channel' && $segment2 == 'cashin') {
                            $active_term = $this->session->userdata('search_channel');
                        } elseif ($segment1 == 'channel' && $segment2 == 'cashout') {
                            $active_term = $this->session->userdata('search_channel_out');
                        } elseif ($segment1 == 'external' && $segment2 == 'cashin') {
                            $active_term = $this->session->userdata('search_external_cashin');
                        } elseif ($segment1 == 'external' && $segment2 == 'cashout') {
                            $active_term = $this->session->userdata('search_external_cashout');
                        }
                        
                        $topbar_placeholder = $active_term ?: "Search anything (Merchant, Channel, Admin, or Transaction...)";
                    ?>
                    <input type="text" class="form-control premium-search-input" placeholder="<?= htmlspecialchars($topbar_placeholder); ?>" id="globalSearchInput" autocomplete="off">
                    <i class="fas fa-search search-icon" id="globalSearchIcon"></i>
                    <i class="fas fa-spinner fa-spin search-loader d-none" id="globalSearchLoader"></i>
                    <div class="search-badge">⌘ K</div>
                    <div id="searchResultsDropdown" class="search-results-dropdown"></div>
                </div>
            </div>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto align-items-center">

                <!-- Theme Toggle -->
                <li class="nav-item">
                    <button id="themeToggle" class="btn btn-link rounded-circle text-gray-500" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; outline: none !important; flex-shrink: 0;">
                        <i class="fas fa-moon theme-icon-dark"></i>
                        <i class="fas fa-sun theme-icon-light d-none"></i>
                    </button>
                </li>

                <!-- ── Bell Notification ─────────────────────────── -->
                <!-- <li class="nav-item dropdown no-arrow mx-1" id="notificationNavItem">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                       data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"
                       style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-bell fa-fw text-gray-500"></i> -->
                        <!-- Badge merah: disembunyikan jika 0 -->
                        <!--<span class="badge badge-danger badge-counter" id="notifBadge" style="display:none;">0</span>
                    </a>-->
                    <!-- Dropdown -->
                    <!-- <div class="dropdown-list dropdown-menu dropdown-menu-right shadow-lg border-0 animated--grow-in p-0 overflow-hidden notif-dropdown-menu"
                         aria-labelledby="alertsDropdown"
                         style="border-radius: 16px; width: 360px; max-height: 480px;"> -->

                        <!-- Header -->
                        <!-- <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom"
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-bell mr-2"></i>Notifikasi
                            </h6>
                            <button id="notifMarkAllBtn" class="btn btn-sm btn-link text-white p-0"
                                    style="font-size: 11px; opacity: 0.85; text-decoration: none;" title="Tandai semua sudah dibaca">
                                <i class="fas fa-check-double mr-1"></i>Semua Dibaca
                            </button>
                        </div>-->

                        <!-- List notifikasi (di-render JS) -->
                        <!--<div id="notifList" style="overflow-y: auto; flex: 1;">
                            <div class="text-center py-4 text-muted" id="notifEmpty">
                                <i class="fas fa-bell-slash fa-2x mb-2 d-block opacity-50"></i>
                                <span style="font-size: 13px;">Tidak ada notifikasi</span>
                            </div>
                        </div>
                    </div>
                </li> -->
                <!-- ────────────────────────────────────────────────── -->

                <div class="topbar-divider d-none d-sm-block" style="height: 24px; border-left: 1px solid rgba(0,0,0,0.08);"></div>


                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow ">
                    <a class="nav-link dropdown-toggle navbar-user-info pr-0 rounded-circle" href="#" id="userDropdown" role="button" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        
                        <img class="navbar-avatar" src="<?= base_url('assets/img/profile/default.jpg') ?>">
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 animated--grow-in mt-3 p-0 overflow-hidden" aria-labelledby="userDropdown" style="border-radius: 20px; width: 280px; backdrop-filter: blur(10px);">
                        <!-- Header Section -->
                        <div class="px-4 py-4 border-bottom bg-light-subtle d-flex align-items-center">
                            <div class="mr-3">
                                <div class="avatar-glow-sm">
                                    <img src="<?= base_url('assets/img/profile/default.jpg') ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="font-weight-bold text-dark mb-0 text-truncate"><?= $user['c_name']; ?></h6>
                                <code class="text-muted small text-truncate d-block" style="font-size: 10px;"><?= $user['c_email'] ?? ''; ?></code>
                            </div>
                        </div>

                        <!-- Account Stats Section -->
                        <div class="px-4 py-3 bg-white">
                            <div class="row no-gutters mb-2">
                                <div class="col-6">
                                    <p class="text-xs font-weight-bold text-uppercase text-muted mb-1" style="letter-spacing: 0.5px;">Role ID</p>
                                    <span class="badge badge-light border font-weight-bold px-2 py-1" style="font-size: 10px;">#<?= $user['role_id'] ?? '0'; ?></span>
                                </div>
                                <div class="col-6 text-right">
                                    <p class="text-xs font-weight-bold text-uppercase text-muted mb-1" style="letter-spacing: 0.5px;">Level</p>
                                    <?php 
                                        $topbar_role_name = $this->session->userdata('role_name');
                                        if (!$topbar_role_name && isset($user['role_id'])) {
                                            $role_query = $this->db->get_where('roles', ['id' => $user['role_id']])->row_array();
                                            $topbar_role_name = $role_query ? $role_query['role_name'] : 'ADMIN';
                                        }
                                        $topbar_role_name = $topbar_role_name ? strtoupper($topbar_role_name) : 'ADMIN';
                                    ?>
                                    <span class="badge badge-primary-soft text-primary font-weight-bold px-2 py-1" style="font-size: 10px;">
                                        <?= htmlspecialchars($topbar_role_name); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-xs font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Status</span>
                                <span class="badge badge-pill badge-success px-2 py-0 font-weight-bold" style="font-size: 9px;">ACTIVE</span>
                            </div>
                        </div>

                        <div class="dropdown-divider m-0" style="opacity: 0.05;"></div>
                        
                        <!-- Menu Section -->
                        <div class="p-2">
                            <?php if ($user['role_id'] != 4): ?>
                                <div class="dropdown-item px-3 rounded-lg border-bottom mb-1">
                                    <div class="d-flex align-items-center justify-content-between w-100" style="gap:10px">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-icon-wrap mr-3 bg-info-soft text-info">
                                                <i class="fas fa-network-wired fa-sm"></i>
                                            </div>
                                            <span class="font-weight-bold small">Maintenance Mode</span>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-premium p-0 m-0" style="min-height: auto;">
                                            <input type="checkbox" class="custom-control-input" id="toggleMaintenanceButton">
                                            <label class="custom-control-label" for="toggleMaintenanceButton" style="padding: 0; min-height: 20px; font-size: 0 !important; cursor: pointer;">&nbsp;</label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <a class="dropdown-item px-3 rounded-lg" href="<?= base_url('helpcenter'); ?>" target="_blank">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-icon-wrap mr-3 bg-primary-soft text-primary">
                                        <i class="fas fa-book fa-sm"></i>
                                    </div>
                                    <span class="font-weight-bold small">Help Center / Docs</span>
                                </div>
                            </a>

                            <a class="dropdown-item px-3 rounded-lg" href="<?= base_url('user/change-password'); ?>">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-icon-wrap mr-3 bg-warning-soft text-warning">
                                        <i class="fas fa-lock fa-sm"></i>
                                    </div>
                                    <span class="font-weight-bold small">Change Password</span>
                                </div>
                            </a>
                            
                            <div class="dropdown-divider my-2" style="opacity: 0.05;"></div>
                            
                            <a class="dropdown-item px-3 rounded-lg text-danger" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-icon-wrap mr-3 bg-danger-soft text-danger">
                                        <i class="fas fa-sign-out-alt fa-sm"></i>
                                    </div>
                                    <span class="font-weight-bold small">Logout Account</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const themeToggle = document.getElementById('themeToggle');
                const darkIcon = themeToggle.querySelector('.theme-icon-dark');
                const lightIcon = themeToggle.querySelector('.theme-icon-light');
                const html = document.documentElement;

                // Function to update icon visibility
                function updateThemeIcon(theme) {
                    if (theme === 'dark') {
                        darkIcon.classList.add('d-none');
                        lightIcon.classList.remove('d-none');
                        themeToggle.classList.remove('text-gray-500');
                        themeToggle.classList.add('text-warning');
                    } else {
                        darkIcon.classList.remove('d-none');
                        lightIcon.classList.add('d-none');
                        themeToggle.classList.remove('text-warning');
                        themeToggle.classList.add('text-gray-500');
                    }
                }
            }
            // Initial setup
            const currentTheme = localStorage.getItem('theme') || 'light';
            updateThemeIcon(currentTheme);

            // Handle toggle click
            themeToggle.addEventListener('click', function() {
                let theme = html.getAttribute('data-theme');
                let newTheme = (theme === 'dark') ? 'light' : 'dark';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });

            // Global Search Keyboard Shortcut
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    document.getElementById('globalSearchInput').focus();
                }
            });

            // Global Search Real-time Logic
            const searchInput = document.getElementById('globalSearchInput');
            const resultsDropdown = document.getElementById('searchResultsDropdown');
            const searchIcon = document.getElementById('globalSearchIcon');
            const searchLoader = document.getElementById('globalSearchLoader');
            let searchTimeout;
            let currentAbortController = null;

            async function performSearch(query) {
                if (query.length < 2) {
                    if (currentAbortController) currentAbortController.abort();
                    clearTimeout(searchTimeout);
                    resultsDropdown.style.display = 'none';
                    if (searchLoader) searchLoader.classList.add('d-none');
                    if (searchIcon) searchIcon.classList.remove('d-none');
                    return;
                }

                // Show loader immediately
                if (searchIcon) searchIcon.classList.add('d-none');
                if (searchLoader) searchLoader.classList.remove('d-none');

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(async () => {
                    // Abort any existing request before starting a new one
                    if (currentAbortController) currentAbortController.abort();
                    currentAbortController = new AbortController();
                    const currentSignal = currentAbortController.signal;

                    try {
                        const trimmedQuery = query.trim();
                        const response = await fetch(`<?= base_url('dashboard/global-search'); ?>?q=${encodeURIComponent(trimmedQuery)}`, {
                            signal: currentSignal
                        });
                        const results = await response.json();

                        if (currentSignal.aborted) return;

                        // Display Results
                        if (results.length > 0) {
                            // Group results by category
                            const grouped = {};
                            results.forEach(res => {
                                if (!grouped[res.category]) grouped[res.category] = [];
                                grouped[res.category].push(res);
                            });
                            
                            const categories = Object.keys(grouped);
                            // Check if we have a merchant/sub-account to show Recent Transactions
                            let searchMerchantId = null;
                            let searchMerchantUrl = null;
                            const merchantResult = results.find(res => res.merchant_id);
                            if (merchantResult) {
                                searchMerchantId = merchantResult.merchant_id;
                                searchMerchantUrl = merchantResult.url;
                                categories.unshift('Recent Transactions');
                            }

                            // Default active tab is the first category
                            let activeCategory = categories[0];
                            
                            let recentTransactionsData = null;
                            let isFetchingRecent = false;
                            
                            const renderResults = () => {
                                let html = '<div class="search-tabs-container">';
                                categories.forEach(cat => {
                                    const count = grouped[cat] ? grouped[cat].length : (recentTransactionsData ? recentTransactionsData.length : '?');
                                    const isActive = cat === activeCategory ? 'active' : '';
                                    const countDisplay = cat === 'Recent Transactions' && count === '?' ? '' : ` (${count})`;
                                    html += `<div class="search-tab ${isActive}" data-category="${cat}">${cat}${countDisplay}</div>`;
                                });
                                html += '</div>';
                                
                                html += '<div class="search-results-list" id="searchResultsListWrapper">';
                                
                                if (activeCategory === 'Recent Transactions') {
                                    if (recentTransactionsData) {
                                        if (recentTransactionsData.length > 0) {
                                            recentTransactionsData.forEach(rt => {
                                                let statusBadge = 'badge-rt-pending';
                                                if (rt.status === 'Success' || rt.status === 'Settled') statusBadge = 'badge-rt-success';
                                                if (rt.status === 'Failed' || rt.status === 'Error') statusBadge = 'badge-rt-failed';
                                                
                                                let iconClass = rt.type === 'Cash-In' ? 'fas fa-arrow-down' : 'fas fa-arrow-up';
                                                if (rt.channel.includes('QRIS')) iconClass = 'fas fa-qrcode';
                                                else if (rt.channel.includes('VA')) iconClass = 'fas fa-university';
                                                else if (rt.channel.includes('EWALLET')) iconClass = 'fas fa-wallet';
                                                else if (rt.channel.includes('BIFAST')) iconClass = 'fas fa-paper-plane';

                                                html += `
                                                    <div class="rt-item">
                                                        <div class="rt-icon"><i class="${iconClass}"></i></div>
                                                        <div class="rt-details">
                                                            <span class="rt-title">${rt.channel} <span style="opacity:0.5; font-weight:normal; font-size:0.65rem;">• ${rt.date_formatted}</span></span>
                                                            <span class="rt-subtitle">${rt.transid}</span>
                                                        </div>
                                                        <div class="rt-status">
                                                            <span class="rt-amount">${rt.amount_formatted}</span>
                                                            <span class="badge ${statusBadge} px-2 py-0" style="font-size:0.6rem; border-radius:4px;">${rt.status}</span>
                                                        </div>
                                                    </div>
                                                `;
                                            });
                                            const baseUrl = "<?= base_url(); ?>";
                                            html += `
                                                <div class="p-2 mt-1">
                                                    <a href="${baseUrl}merchant/manage/detail/${searchMerchantId}#nav-history" class="btn btn-sm btn-block text-primary font-weight-bold">
                                                        View All Transactions <i class="fas fa-arrow-right ml-1"></i>
                                                    </a>
                                                </div>
                                            `;
                                        } else {
                                            html += '<div class="p-3 text-center text-muted small"><i class="fas fa-inbox mb-2 d-block"></i>No recent transactions</div>';
                                        }
                                    } else {
                                        html += '<div class="p-4 text-center text-muted small"><i class="fas fa-spinner fa-spin mb-2 d-block text-primary" style="font-size:20px;"></i>Loading transactions...</div>';
                                        
                                        // Fetch the data if not fetching
                                        if (!isFetchingRecent && searchMerchantId) {
                                            isFetchingRecent = true;
                                            fetch(`<?= base_url('dashboard/recent-search'); ?>?merchant_id=${searchMerchantId}`, {
                                                headers: {
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }
                                            })
                                                .then(res => res.json())
                                                .then(data => {
                                                    if (data.status === 'success') {
                                                        recentTransactionsData = data.data;
                                                        if (activeCategory === 'Recent Transactions') renderResults();
                                                    }
                                                })
                                                .catch(err => {
                                                    recentTransactionsData = [];
                                                    if (activeCategory === 'Recent Transactions') renderResults();
                                                });
                                        }
                                    }
                                } else {
                                    if (grouped[activeCategory]) {
                                        grouped[activeCategory].forEach(res => {
                                            html += `
                                                <div class="search-result-item" onclick="window.location.href='${res.url}'">
                                                    <div class="result-icon"><i class="${res.icon}"></i></div>
                                                    <div class="result-info">
                                                        <div class="result-title">${res.title}</div>
                                                        <div class="result-category">${res.category}</div>
                                                    </div>
                                                </div>
                                            `;
                                        });
                                    }
                                }
                                html += '</div>';
                                
                                resultsDropdown.innerHTML = html;
                                
                                // Add event listeners to tabs
                                const tabs = resultsDropdown.querySelectorAll('.search-tab');
                                let isDragging = false;
                                
                                tabs.forEach(tab => {
                                    tab.addEventListener('click', (e) => {
                                        if (isDragging) return;
                                        e.stopPropagation(); // Prevent closing dropdown
                                        activeCategory = tab.getAttribute('data-category');
                                        renderResults();
                                        document.getElementById('globalSearchInput').focus(); // Keep focus
                                    });
                                });
                                
                                // Add mouse drag scrolling for tabs
                                const slider = resultsDropdown.querySelector('.search-tabs-container');
                                let isDown = false;
                                let startX;
                                let scrollLeft;

                                if (slider) {
                                    slider.addEventListener('mousedown', (e) => {
                                        isDown = true;
                                        isDragging = false;
                                        slider.style.cursor = 'grabbing';
                                        startX = e.pageX - slider.offsetLeft;
                                        scrollLeft = slider.scrollLeft;
                                    });
                                    slider.addEventListener('mouseleave', () => {
                                        isDown = false;
                                        slider.style.cursor = 'auto';
                                    });
                                    slider.addEventListener('mouseup', () => {
                                        isDown = false;
                                        slider.style.cursor = 'auto';
                                        setTimeout(() => { isDragging = false; }, 50);
                                    });
                                    slider.addEventListener('mousemove', (e) => {
                                        if (!isDown) return;
                                        e.preventDefault();
                                        isDragging = true;
                                        const x = e.pageX - slider.offsetLeft;
                                        const walk = (x - startX) * 2;
                                        slider.scrollLeft = scrollLeft - walk;
                                    });
                                }
                            };
                            
                            renderResults();
                            resultsDropdown.style.display = 'block';
                        } else {
                            resultsDropdown.innerHTML = '<div class="p-3 text-center text-muted small"><i class="fas fa-search mb-2 d-block"></i>No matching results found</div>';
                            resultsDropdown.style.display = 'block';
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Search error:', error);
                        }
                    } finally {
                        if (!currentSignal.aborted) {
                            if (searchLoader) searchLoader.classList.add('d-none');
                            if (searchIcon) searchIcon.classList.remove('d-none');
                        }
                    }
                }, 300);
            }

            if (searchInput) {
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                    }
                });
                searchInput.addEventListener('input', (e) => performSearch(e.target.value));
                searchInput.addEventListener('focus', (e) => { if(e.target.value.length > 1) resultsDropdown.style.display = 'block'; });
            }

            document.addEventListener('click', (e) => {
                if (searchInput && !searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
                    resultsDropdown.style.display = 'none';
                }
            });

            // Maintenance toggle logic
            const toggle = document.getElementById('toggleMaintenanceButton');
            if (toggle) {
                fetch("<?= base_url('dashboard/maintenance-status') ?>")
                .then(response => response.json())
                .then(data => {
                    // If data.status is 'Active' it means APIs are active, so Maintenance Mode is OFF (unchecked)
                    toggle.checked = (data.status === 'Not Active'); 
                })
                .catch(error => {
                    console.error('Error fetching maintenance status:', error);
                });

                toggle.addEventListener('change', function () {
                    // If toggle is checked (Maintenance Mode ON), we set API status to 'Not Active'
                    const status = toggle.checked ? 'Not Active' : 'Active';
                    const displayStatus = toggle.checked ? 'ON' : 'OFF';
                    const originalState = !toggle.checked;
                    const isEnabling = toggle.checked;

                    // Revert immediately until confirmed
                    toggle.checked = originalState;

                    Swal.fire({
                        html: `
                            <div style="display:flex; flex-direction:column; align-items:center; gap:12px; padding: 8px 0;">
                                <div style="width:52px; height:52px; border-radius:50%; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-network-wired" style="font-size:22px; color:#ef4444;"></i>
                                </div>
                                <h5 style="font-weight:700; margin:0; font-size:1.1rem;">Change Maintenance Mode?</h5>
                                <p style="color:var(--gray-500, #94a3b8); font-size:0.92rem; margin:0; line-height:1.6;">
                                    Are you sure you want to turn Maintenance Mode <strong>${displayStatus}</strong>?<br>
                                    <span style="font-size:0.82rem; opacity:0.7;">${isEnabling ? 'This will disable all merchant API access.' : 'This will re-enable all merchant API access.'}</span>
                                </p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: `<i class="fas fa-check mr-1"></i> Yes, turn ${displayStatus}`,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup:             'swal2-premium-popup',
                            confirmButton:     'swal2-premium-confirm',
                            cancelButton:      'swal2-premium-cancel',
                            actions:           'swal2-premium-actions'
                        },
                        buttonsStyling: false,
                        focusConfirm: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            toggle.checked = !originalState; // Optimistically apply change

                            const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content');
                            const csrfHash = document.querySelector('meta[name="csrf-token-hash"]')?.getAttribute('content');
                            const formData = new FormData();
                            formData.append('status', status);
                            if (csrfName && csrfHash) {
                                formData.append(csrfName, csrfHash);
                            }

                            fetch("<?= base_url('dashboard/toggle-openapi') ?>", {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    html: `
                                        <div style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:8px 0;">
                                            <div style="width:52px; height:52px; border-radius:50%; background:rgba(28,200,138,0.1); display:flex; align-items:center; justify-content:center;">
                                                <i class="fas fa-check-circle" style="font-size:24px; color:#1cc88a;"></i>
                                            </div>
                                            <h5 style="font-weight:700; margin:0;">Updated!</h5>
                                            <p style="color:var(--gray-500,#94a3b8); font-size:0.9rem; margin:0;">${data.message || 'Maintenance Mode has been updated.'}</p>
                                        </div>
                                    `,
                                    showConfirmButton: false,
                                    timer: 2500,
                                    customClass: { popup: 'swal2-premium-popup' },
                                    buttonsStyling: false
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                toggle.checked = originalState; // Revert on error
                                Swal.fire({
                                    html: `
                                        <div style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:8px 0;">
                                            <div style="width:52px; height:52px; border-radius:50%; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center;">
                                                <i class="fas fa-exclamation-triangle" style="font-size:24px; color:#ef4444;"></i>
                                            </div>
                                            <h5 style="font-weight:700; margin:0;">Error</h5>
                                            <p style="color:var(--gray-500,#94a3b8); font-size:0.9rem; margin:0;">An error occurred while updating Maintenance Mode.</p>
                                        </div>
                                    `,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK',
                                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                    buttonsStyling: false
                                });
                            });
                        }
                        // If dismissed/cancelled, toggle stays reverted (originalState)
                    });
                });
                // ── Notification Bell System ─────────────────────────────────
                (function () {
                    const BASE = '<?= base_url() ?>';
                    const badge    = document.getElementById('notifBadge');
                    const listEl   = document.getElementById('notifList');
                    const emptyEl  = document.getElementById('notifEmpty');
                    const markBtn  = document.getElementById('notifMarkAllBtn');
                    const bellDrop = document.getElementById('alertsDropdown');

                    // ── Render satu item notifikasi ──────────────────────────
                    function renderItem(n) {
                        const colorMap = { maintenance: '#0ea5e9', login_new_ip: '#f59e0b', dlq_failed: '#ef4444' };
                        const bgMap    = { maintenance: 'rgba(14,165,233,.08)', login_new_ip: 'rgba(245,158,11,.08)', dlq_failed: 'rgba(239,68,68,.08)' };
                        const color    = colorMap[n.type]  || '#6b7280';
                        const bg       = bgMap[n.type]     || 'rgba(107,114,128,.06)';
                        const unreadDot = n.is_read ? '' : '<span style="width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0;margin-top:4px;"></span>';

                        return `
                        <div class="notif-item d-flex align-items-start px-4 py-3 border-bottom"
                             data-id="${n.id}" style="cursor:pointer; gap:12px; transition:background .15s;
                             background:${n.is_read ? 'transparent' : bg};"
                             onmouseenter="this.style.background='rgba(0,0,0,.04)'"
                             onmouseleave="this.style.background='${n.is_read ? 'transparent' : bg}'"
                             onclick="notifMarkRead(${n.id}, this)">
                            <div style="width:36px;height:36px;border-radius:10px;background:${bg};border:1px solid ${color}22;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="${n.icon}" style="color:${color};font-size:14px;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p class="font-weight-bold mb-0 text-truncate" style="font-size:13px;">${n.title}</p>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;white-space:normal;">${n.message}</p>
                                <span class="text-muted" style="font-size:10px;"><i class="fas fa-clock mr-1"></i>${n.time_ago}</span>
                            </div>
                            ${unreadDot}
                        </div>`;
                    }

                    // ── Update badge ─────────────────────────────────────────
                    function updateBadge(count) {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }

                    // ── Poll: hanya ambil count (ringan, setiap 30 detik) ────
                    function pollCount() {
                        fetch(BASE + 'notifications/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(d => updateBadge(d.unread || 0))
                            .catch(() => {}); // silent fail
                    }

                    // ── Fetch list saat dropdown dibuka ──────────────────────
                    function fetchList() {
                        listEl.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-muted"></i></div>';
                        fetch(BASE + 'notifications/list', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(d => {
                                updateBadge(d.unread || 0);
                                if (!d.items || d.items.length === 0) {
                                    listEl.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-bell-slash fa-2x mb-2 d-block opacity-50"></i><span style="font-size:13px;">Tidak ada notifikasi</span></div>';
                                } else {
                                    listEl.innerHTML = d.items.map(renderItem).join('');
                                }
                            })
                            .catch(() => {
                                listEl.innerHTML = '<div class="text-center py-3 text-danger" style="font-size:12px;"><i class="fas fa-exclamation-circle mr-1"></i>Gagal memuat notifikasi</div>';
                            });
                    }

                    // ── Mark single as read ──────────────────────────────────
                    window.notifMarkRead = function(id, el) {
                        fetch(BASE + 'notifications/read/' + id, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + id
                        }).then(r => r.json()).then(d => {
                            updateBadge(d.unread || 0);
                            // Hapus unread dot & reset background
                            const dot = el.querySelector('span[style*="border-radius:50%"]');
                            if (dot) dot.remove();
                            el.style.background = 'transparent';
                            el.onmouseleave = function() { this.style.background = 'transparent'; };
                        }).catch(() => {});
                    };

                    // ── Mark all as read ─────────────────────────────────────
                    if (markBtn) {
                        markBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            fetch(BASE + 'notifications/read-all', {
                                method: 'POST',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(r => r.json()).then(() => {
                                updateBadge(0);
                                fetchList();
                            }).catch(() => {});
                        });
                    }

                    // ── Buka dropdown → fetch list ────────────────────────────
                    if (bellDrop) {
                        bellDrop.addEventListener('click', function () {
                            setTimeout(fetchList, 50); // setelah dropdown animasi
                        });
                    }

                    // ── Jalankan polling ─────────────────────────────────────
                    // pollCount();                      // langsung saat load
                    // setInterval(pollCount, 30000);    // setiap 30 detik
                })();
                // ─────────────────────────────────────────────────────────────
            });
            </script>

        </nav>
        <!-- End of Topbar -->



