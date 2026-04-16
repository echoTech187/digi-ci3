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
                    <li class="dt-breadcrumb-item"><a href="<?= base_url('admin') ?>" title="Home"><i class="fas fa-home" style="font-size:12px;"></i></a></li>
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
                    <a class="nav-link dropdown-toggle navbar-user-info pr-0 rounded-circle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class=" d-none d-lg-inline"><?= $user['c_name']; ?></span>
                        <img class="navbar-avatar" src="<?= base_url('assets/img/profile/default.jpg') ?>">
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 animated--grow-in mt-2" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 200px;">
                        <div class="px-4 py-3 border-bottom d-lg-none">
                            <p class="text-xs font-weight-bold text-uppercase text-muted mb-1">Signed in as</p>
                            <p class="font-weight-bold text-dark mb-0"><?= $user['c_name']; ?></p>
                        </div>

                        <?php if ($user['role_id'] != 4): ?>
                            <div class="dropdown-item py-2 px-4 border-bottom">
                                <div class="d-flex align-items-center justify-content-between" style="gap:10px">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-network-wired fa-sm fa-fw mr-3 text-gray-400"></i>
                                        <span class="text-xs font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Maintenance Mode</span>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch-premium p-0 m-0" style="min-height: auto;">
                                        <input type="checkbox" class="custom-control-input" id="toggleMaintenanceButton">
                                        <label class="custom-control-label" for="toggleMaintenanceButton" style="padding: 0; min-height: 20px; font-size: 0 !important; cursor: pointer;">&nbsp;</label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <a class="dropdown-item py-2 px-4" href="<?= base_url('user/changePassword'); ?>">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-lock fa-sm fa-fw mr-3 text-gray-400"></i>
                                <span>Change Password</span>
                            </div>
                        </a>
                        <div class="dropdown-divider my-0"></div>
                        <a class="dropdown-item py-2 px-4 text-danger" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-3"></i>
                                <span>Logout</span>
                            </div>
                        </a>
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

                // Maintenance toggle logic
                const toggle = document.getElementById('toggleMaintenanceButton');
                if (toggle) {
                    fetch("<?= base_url('admin/getMaintenanceStatus') ?>")
                    .then(response => response.json())
                    .then(data => {
                        toggle.checked = (data.status === 'Not Active'); 
                    })
                    .catch(error => {
                        console.error('Error fetching maintenance status:', error);
                    });

                    toggle.addEventListener('change', function () {
                        const status = toggle.checked ? 'Not Active' : 'Active';

                        // Show confirmation before proceeding
                        const confirmMessage = `Are you sure you want to set OpenAPI status to "${status}"?`;
                        if (!confirm(confirmMessage)) {
                            toggle.checked = !toggle.checked; // Revert toggle if cancelled
                            return;
                        }

                        // Proceed with AJAX request if confirmed
                        fetch("<?= base_url('admin/toggleOpenApiStatus') ?>", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: status })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("An error occurred while updating OpenAPI status.");
                        });
                    });
                }
            });
            </script>

        </nav>
        <!-- End of Topbar -->



