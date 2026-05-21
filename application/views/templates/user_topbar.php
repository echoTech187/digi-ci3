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

                // 3. URL Override: If segment match in breadcrumb_url_replace, use custom URL
                $finalUrl = base_url($current_url);
                if (isset($breadcrumb_url_replace) && isset($breadcrumb_url_replace[$segment])) {
                    $finalUrl = base_url($breadcrumb_url_replace[$segment]);
                }

                $breadcrumb[] = [
                    'name' => $displayName,
                    'url' => $finalUrl
                ];
            }
            ?>
            <nav aria-label="breadcrumb" class="d-none d-lg-block">
                <ol class="dt-breadcrumb ">
                    <li class="dt-breadcrumb-item"><a href="<?= base_url('dashboard') ?>" title="Home"><i class="fas fa-home" style="font-size:12px;"></i></a></li>
                    <?php foreach ($breadcrumb as $index => $item): ?>
                        <li class="dt-breadcrumb-separator"><i class="fas fa-chevron-right"></i></li>
                        <li class="dt-breadcrumb-item">
                            <?php if ($index === count($breadcrumb) - 1): ?>
                                <span><?= $item['name'] ?></span>
                            <?php else: ?>
                                <a href="<?= $item['url'] ?>"><?= $item['name'] ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            
            <!-- Global Search -->
            <div class="d-none d-lg-block ml-4 flex-grow-1" style="max-width: 450px;">
                <div class="premium-search-container">
                    <?php
                        $segment2 = $this->uri->segment(2);
                        $active_term = '';
                        
                        if ($segment2 == 'qris') {
                            $active_term = $this->session->userdata('last_search_qris') ?: ($this->session->userdata('search_transactionid_ht') ?: ($this->session->userdata('search_rrn') ?: $this->session->userdata('search_invoice_no')));
                        } elseif ($segment2 == 'virtual_account') {
                            $active_term = $this->session->userdata('search_va_transid') ?: ($this->session->userdata('search_va_number') ?: $this->session->userdata('search_invoice_no'));
                        } elseif ($segment2 == 'ewallet') {
                            $active_term = $this->session->userdata('search_transid_ewallet') ?: $this->session->userdata('search_invoice_no');
                        } elseif ($segment2 == 'bi_fast') {
                            $active_term = $this->session->userdata('search_transid_bifast') ?: $this->session->userdata('search_invoice_no');
                        } elseif ($segment2 == 'history') {
                            $active_term = $this->session->userdata('search_invoice_ppob');
                        } elseif ($segment2 == 'qris_dynamic') {
                            $active_term = $this->session->userdata('search_transid_qd');
                        } elseif ($segment2 == 'ewallet_dynamic') {
                            $active_term = $this->session->userdata('search_transid_qd');
                        } elseif ($segment2 == 'qris_recurring') {
                            $active_term = $this->session->userdata('search_transid_qr');
                        } elseif ($segment2 == 'Va_dynamic') {
                            $active_term = $this->session->userdata('search_merchant_trxid');
                        } elseif ($segment2 == 'VA_recurring') {
                            $active_term = $this->session->userdata('search_transid_var');
                        }
                        
                        $topbar_placeholder = $active_term ?: "Search transactions (ID, VA, RRN, Inv)...";
                    ?>
                    <input type="text" class="form-control premium-search-input" placeholder="<?= htmlspecialchars($topbar_placeholder); ?>" id="globalSearchInput" autocomplete="off">
                    <i class="fas fa-search search-icon" id="globalSearchIcon"></i>
                    <i class="fas fa-spinner fa-spin search-loader" id="globalSearchLoader"></i>
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

                <div class="topbar-divider d-none d-sm-block" style="height: 24px; border-left: 1px solid rgba(0,0,0,0.08);"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow ">
                    <a class="nav-link dropdown-toggle navbar-user-info pr-0 rounded-circle" href="#" id="userDropdown" role="button" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        
                        <img class="navbar-avatar" src="<?= base_url('assets/img/profile/default.jpg') ?>">
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 animated--grow-in mt-3 p-0 overflow-hidden" aria-labelledby="userDropdown" style="border-radius: 20px; width: 280px; background: var(--bg-glass); backdrop-filter: blur(10px);">
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
                                    <span class="badge badge-primary-soft text-primary font-weight-bold px-2 py-1" style="font-size: 10px;">Admin</span>
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

                            <a class="dropdown-item px-3 rounded-lg" href="<?= base_url('user/changePassword'); ?>">
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
                let searchTimeout;

                async function performSearch(query) {
                    if (query.length < 2) {
                        resultsDropdown.style.display = 'none';
                        return;
                    }

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(async () => {
                        const loader = document.getElementById('globalSearchLoader');
                        const icon = document.getElementById('globalSearchIcon');
                        
                        if (loader && icon) {
                            loader.style.display = 'block';
                            icon.style.display = 'none';
                        }

                        try {
                            const response = await fetch(`<?= base_url('dashboard/global-search'); ?>?q=${encodeURIComponent(query)}`);
                            const results = await response.json();

                            // Display Results
                            if (results.length > 0) {
                                resultsDropdown.innerHTML = results.map(res => `
                                    <div class="search-result-item" onclick="window.location.href='${res.url}'">
                                        <div class="result-icon"><i class="${res.icon}"></i></div>
                                        <div class="result-info">
                                            <div class="result-title">${res.title}</div>
                                            <div class="result-category">${res.category}</div>
                                        </div>
                                    </div>
                                `).join('');
                                resultsDropdown.style.display = 'block';
                            } else {
                                resultsDropdown.innerHTML = '<div class="p-3 text-center text-muted small"><i class="fas fa-search mb-2 d-block"></i>No matching results found</div>';
                                resultsDropdown.style.display = 'block';
                            }
                        } catch (error) {
                            console.error('Search error:', error);
                        } finally {
                            if (loader && icon) {
                                loader.style.display = 'none';
                                icon.style.display = 'block';
                            }
                        }
                    }, 300);
                }

                if (searchInput) {
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
                        toggle.checked = (data.status === 'Active'); 
                    })
                    .catch(error => {
                        console.error('Error fetching maintenance status:', error);
                    });

                    toggle.addEventListener('change', function () {
                        const status = toggle.checked ? 'Active' : 'Not Active';
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
                                        Are you sure you want to set Maintenance Mode to <strong>"${status}"</strong>?<br>
                                        <span style="font-size:0.82rem; opacity:0.7;">${isEnabling ? 'This will disable all merchant API access.' : 'This will re-enable all merchant API access.'}</span>
                                    </p>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: `<i class="fas fa-check mr-1"></i> Yes, set to ${status}`,
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
                }
            });
            </script>

        </nav>
        <!-- End of Topbar -->



