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
            $excluded_urls = ['admin', 'admin/analytics'];
            
            foreach ($menus as $m) : 
                // Collect all clickable links for this menu branch
                $links = [];
                
                // Case 1: The parent menu itself has a URL
                if (!empty($m['url'])) {
                    $links[] = $m;
                }
                
                // Case 2: Process Submenus
                if (!empty($m['sub_menus'])) {
                    foreach ($m['sub_menus'] as $sm) {
                        if (!empty($sm['url'])) {
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

<style>
/* Welcome Page Premium Styles */
.welcome-container {
    max-width: 900px;
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Avatar Styling */
.welcome-avatar-wrap {
    position: relative;
    display: inline-block;
}
.welcome-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid white;
    position: relative;
    z-index: 2;
}
.avatar-ring {
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    border: 2px dashed #4e73df;
    border-radius: 50%;
    animation: rotate 20s linear infinite;
    opacity: 0.3;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Quick Access Cards */
.quick-access-card {
    background: white;
    border-radius: 20px;
    min-height: 140px;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.quick-access-card:hover {
    transform: translateY(-8px);
    background: #4e73df;
    box-shadow: 0 20px 25px -5px rgba(78, 115, 223, 0.4) !important;
}

.card-icon-circle {
    width: 50px;
    height: 50px;
    background: #f8f9fc;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4e73df;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.quick-access-card:hover .card-icon-circle {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.card-label {
    color: #4a5568;
    font-size: 13px;
    text-align: center;
    transition: all 0.3s ease;
}

.quick-access-card:hover .card-label {
    color: white;
}

.card-hover-hint {
    position: absolute;
    bottom: -20px;
    font-size: 9px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease;
    opacity: 0;
}

.quick-access-card:hover .card-hover-hint {
    bottom: 12px;
    opacity: 1;
}

/* Background Accents */
.status-indicator-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.status-indicator-dot.online { background: #10b981; box-shadow: 0 0 10px #10b981; }

.welcome-subtitle {
    font-size: 15px;
    color: #718096;
}

/* Dark Mode Support Adjustment */
.bg-primary-soft { background-color: rgba(78, 115, 223, 0.1); }
</style>
