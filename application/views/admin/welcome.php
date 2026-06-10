<!-- Begin Page Content -->
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 80vh;">

    <div class="welcome-container text-center">
        <!-- Profile Pulse Avatar -->
        <div class="welcome-avatar-wrap mb-4">
            <div class="avatar-ring"></div>
            <img src="<?= base_url('assets/img/profile/default.jpg'); ?>" alt="Profile" class="welcome-avatar shadow-lg">
        </div>

        <!-- Greeting Block -->
        <div class="greeting-block mb-3">
            <h5 class="text-primary font-weight-bold mb-1" style="letter-spacing: 2px; text-transform: uppercase; font-size: 0.8rem;">
                <?= $greeting; ?>
            </h5>
            <h1 class="display-4 font-weight-bold text-gray-900 mb-0" style="letter-spacing: -2px;">
                Welcome back, <?= explode(' ', $user['c_name'])[0]; ?>!
            </h1>
        </div>

        <p class="text-muted mb-5 welcome-subtitle" style="max-width: 500px; margin: 0 auto; line-height: 1.6;">
            Your workspace is successfully synchronized. We've organized your authorized tools below to help you get started quickly today.
        </p>

        <!-- Quick Access Grid -->
        <div class="row justify-content-center g-3 mt-4 px-lg-5">
            <?php 
            $count = 0;
            $excluded_urls = ['admin', 'dashboard', 'dashboard/analytics'];
            
            foreach ($menus as $m) : 
                // Collect all clickable links for this menu branch
                $links = [];
                
                // Case 1: The parent menu itself has a URL
                if (!empty($m['url']) && $m['url'] !== '#') {
                    $links[] = $m;
                }
                
                // Case 2: Process Submenus
                if (!empty($m['sub_menus'])) {
                    foreach ($m['sub_menus'] as $sm) {
                        if (!empty($sm['url']) && $sm['url'] !== '#') {
                            $links[] = $sm;
                        }
                    }
                }

                foreach ($links as $link) :
                    // Strict Exclusion Filter for Dashboard/Analytics
                    if (in_array(trim($link['url'], '/'), $excluded_urls)) continue;
                    $count++;
            ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <a href="<?= base_url($link['url']); ?>" class="quick-access-card shadow-sm border-0 d-flex flex-column align-items-center justify-content-center p-4">
                    <div class="card-icon-circle mb-3">
                        <i class="<?= $link['icon'] ?: 'fas fa-th-large'; ?>"></i>
                    </div>
                    <span class="card-label font-weight-bold" style="line-height: 1.2;"><?= $link['title']; ?></span>
                    <div class="card-hover-hint">Open Page <i class="fas fa-arrow-right ml-1"></i></div>
                </a>
            </div>
            <?php endforeach; ?>
            <?php endforeach; ?>

            <?php if ($count == 0) : ?>
                <div class="col-12 text-center py-5">
                    <div class="p-4 bg-white shadow-sm rounded-lg border">
                        <i class="fas fa-user-shield fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted m-0">Your permission profile is active, but no operational modules are currently mapped. Please contact your system administrator.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer Note -->
        <div class="mt-5 pt-4">
            <div class="d-inline-flex align-items-center bg-white shadow-sm px-4 py-2 rounded-pill border" style="font-size: 11px;">
                <span class="status-indicator-dot online mr-2"></span>
                <span class="text-muted font-weight-bold uppercase" style="letter-spacing: 1px;">Session Secure & Encrypted</span>
            </div>
        </div>
    </div>

</div>

