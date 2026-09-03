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
                
                $displayName = ucwords(str_replace(['_', '-'], ' ', $segment));
                
                if (isset($breadcrumb_replace) && isset($breadcrumb_replace[$segment])) {
                    $displayName = $breadcrumb_replace[$segment];
                } elseif (is_numeric($segment)) {
                    $displayName = '#' . $segment;
                }

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
                <ol class="dt-breadcrumb">
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
                        $topbar_merchant_id = (isset($merchant) && isset($merchant['id'])) ? $merchant['id'] : (($this->uri->segment(1) == 'merchant' && is_numeric($this->uri->segment(4))) ? $this->uri->segment(4) : '');
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

                <div class="topbar-divider d-none d-sm-block" style="height: 24px; border-left: 1px solid rgba(0,0,0,0.08);"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle navbar-user-info pr-0 rounded-circle" href="#" id="userDropdown" role="button" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        <img class="navbar-avatar" src="<?= base_url('assets/img/profile/default.jpg') ?>">
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 animated--grow-in mt-3 p-0 overflow-hidden" aria-labelledby="userDropdown" style="border-radius: 20px; width: 280px; backdrop-filter: blur(10px);">
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

        </nav>
        <!-- End of Topbar -->

<script>
    window.SEARCH_MERCHANT_ID = "<?= $topbar_merchant_id; ?>";
</script>
<script src="<?= base_url('assets/js/user_topbar.js'); ?>"></script>
