
<?php
    // Dynamic User Data Extraction for Profile Block
    $sb_name = $this->session->userdata('c_name');
    if (!$sb_name) {
        $sb_name = $this->session->userdata('name');
        if (!$sb_name) {
            $sb_name = 'Administrator';
        }
    }
    
    $actual_role_id = $this->session->userdata('role');
    if (!$actual_role_id) {
        $actual_role_id = $this->session->userdata('role_id'); 
    }
    $sb_role = ($actual_role_id == 1) ? 'Super Admin' : (($actual_role_id == 2) ? 'Merchant' : 'Supervisor'); 

    // Detect Current URL for Active State Mapping
    $curr_url = strtolower($this->uri->segment(1));
    if ($this->uri->segment(2)) $curr_url .= '/' . strtolower($this->uri->segment(2));
    if ($this->uri->segment(3)) $curr_url .= '/' . strtolower($this->uri->segment(3));

    // Tarik Data Menu secara dinamis melalui RBAC
    $menus = $this->rbac->get_menus_by_role($actual_role_id);
?>

<!-- Sidebar -->
<nav class="sb-sidebar" id="accordionSidebar">

    <!-- Brand -->
    <div class="sb-brand">
        <a href="<?= base_url('/admin'); ?>" class="sb-brand-link">
            <div class="sb-brand-icon">
                <img src="<?= base_url('public/image/icon.png'); ?>" alt="Logo">
            </div>
            <span class="sb-brand-text">GIDI</span>
        </a>
        <!-- Mobile Close Button -->
        <button type="button" class="sb-mobile-close d-md-none" id="closeSidebarMobile" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <div class="sb-nav-wrapper">
        <ul class="sb-nav" id="sb-accordion">

            <?php 
            $current_group = '';
            $is_first_group = true;
            foreach ($menus as $m) :
                $has_submenu = !empty($m['sub_menus']);

                // Check if this menu or its submenus are active
                $is_active = ($curr_url == $m['url']) ? true : false;
                if ($has_submenu) {
                    foreach ($m['sub_menus'] as $sm) {
                        if ($curr_url == $sm['url']) {
                            $is_active = true;
                            break;
                        }
                    }
                }

                // Inject dynamic group headings
                if (!empty($m['group_modules']) && strtolower($m['group_modules']) !== strtolower($current_group)) {
                    $current_group = $m['group_modules'];
                    $mt_class = $is_first_group ? '' : ' mt-2';
                    echo '<li class="sb-group-label' . $mt_class . '"><span>' . strtoupper($current_group) . '</span></li>';
                    $is_first_group = false;
                }
            ?>

            <?php if (!$has_submenu) : ?>
                <li class="sb-nav-item <?= $is_active ? 'active' : '' ?>">
                    <a class="sb-nav-link" href="<?= base_url($m['url']) ?>">
                        <span class="sb-nav-icon"><i class="<?= $m['icon'] ?>"></i></span>
                        <span class="sb-nav-label"><?= $m['title'] ?></span>
                    </a>
                </li>

            <?php else : ?>
                <li class="sb-nav-item sb-has-sub <?= $is_active ? 'active' : '' ?>">
                    <a class="sb-nav-link <?= $is_active ? '' : 'collapsed' ?>"
                       href="#"
                       data-toggle="collapse"
                       data-target="#sbCollapse<?= $m['id'] ?>"
                       aria-expanded="<?= $is_active ? 'true' : 'false' ?>"
                       aria-controls="sbCollapse<?= $m['id'] ?>">
                        <span class="sb-nav-icon"><i class="<?= $m['icon'] ?>"></i></span>
                        <span class="sb-nav-label"><?= $m['title'] ?></span>
                        <span class="sb-nav-arrow"><i class="fas fa-chevron-right"></i></span>
                    </a>

                    <!-- Flyout Submenu for Mini Sidebar Mode -->
                    <div class="sb-flyout">
                        <div class="sb-flyout-title"><?= $m['title'] ?></div>
                        <ul class="sb-flyout-list">
                            <?php foreach ($m['sub_menus'] as $sm) : ?>
                                <li class="sb-flyout-item <?= ($curr_url == $sm['url']) ? 'active' : '' ?>">
                                    <a class="sb-flyout-link" href="<?= base_url($sm['url']) ?>">
                                        <?php if (!empty($sm['icon'])) : ?>
                                            <span class="sb-flyout-icon"><i class="<?= $sm['icon'] ?>"></i></span>
                                        <?php else : ?>
                                            <span class="sb-flyout-dot"></span>
                                        <?php endif; ?>
                                        <span><?= $sm['title'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div id="sbCollapse<?= $m['id'] ?>" class="collapse <?= $is_active ? 'show' : '' ?>" data-parent="#sb-accordion">
                        <ul class="sb-subnav">
                            <?php foreach ($m['sub_menus'] as $sm) : ?>
                                <li class="sb-subnav-item <?= ($curr_url == $sm['url']) ? 'active' : '' ?>">
                                    <a class="sb-subnav-link" href="<?= base_url($sm['url']) ?>">
                                        <?php if (!empty($sm['icon'])) : ?>
                                            <span class="sb-subnav-icon"><i class="<?= $sm['icon'] ?>"></i></span>
                                        <?php else : ?>
                                            <span class="sb-subnav-dot"></span>
                                        <?php endif; ?>
                                        <span><?= $sm['title'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>

            <?php endif; ?>
            <?php endforeach; ?>

            <!-- Logout -->
            <!-- <li class="sb-group-label mt-2"><span>Account</span></li> -->
            <li class="sb-nav-item sb-logout-item">
                <a class="sb-nav-link sb-logout-link" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
                    <span class="sb-nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span class="sb-nav-label">Logout</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- User Profile (Pinned Bottom) -->
    <div class="sb-user-block">
        <img src="<?= base_url('assets/img/profile/default.jpg'); ?>" alt="Profile" class="sb-user-avatar">
        <div class="sb-user-info">
            <div class="sb-user-name"><?= $sb_name; ?></div>
            <div class="sb-user-role"><?= $sb_role; ?></div>
        </div>
        <div class="sb-user-actions">
            <div class="sb-online-dot"></div>
        </div>
    </div>

    <!-- Sidebar Toggle (Desktop) -->
    <div class="sb-toggle-wrap d-none d-md-flex">
        <button class="sb-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

</nav>