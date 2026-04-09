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

                $breadcrumb[] = [
                    'name' => $displayName,
                    'url' => base_url($current_url)
                ];
            }
            ?>
            <nav aria-label="breadcrumb" class="d-none d-lg-block">
                <ol class="dt-breadcrumb ml-2">
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

                <div class="topbar-divider d-none d-sm-block" style="height: 24px; border-left: 1px solid rgba(0,0,0,0.08);"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow ml-2">
                    <a class="nav-link dropdown-toggle navbar-user-info pr-0 rounded-circle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline"><?= $user['c_name']; ?></span>
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
                const toggle = document.getElementById('toggleMaintenanceButton');

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
            });
            </script>

        </nav>
        <!-- End of Topbar -->
