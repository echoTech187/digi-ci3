<!-- Begin Page Content -->
<div >

    <!-- Page Heading -->

    <div class="card shadow border-left-info">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 text-dark mb-0"><?= $title; ?></h1>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>

        <!-- ── Toggleable Page Instructional Drawer ── -->
        <div class="drawer-overlay" id="instructionOverlay"></div>
        <div class="drawer-right" id="instructionDrawer">
            <div class="drawer-header">
                <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> My Profile Guide</h6>
                <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
            </div>
            <div class="drawer-body">
                <p class="drawer-desc">This profile screen shows your user credentials, avatar, and system access role level.</p>
                
                <div class="drawer-card">
                    <div class="drawer-card-title"><i class="fas fa-user text-primary mr-2"></i> Profile Details</div>
                    <p class="drawer-card-text">View your login identity, registered full name, profile image, and the date your back-office account was created.</p>
                </div>
                
                <div class="drawer-card">
                    <div class="drawer-card-title"><i class="fas fa-shield-alt text-primary mr-2"></i> Security Clearance</div>
                    <p class="drawer-card-text">Access roles and clearances are assigned by supervisors under administrative roles. Contact the administrator to update permissions.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Alerts Standardized to Swal2 Premium -->
            <script>
                $(document).ready(function() {
                    // Instructions Guide drawer handlers
                    $('#toggleGuideBtn').on('click', function() {
                        $('#instructionDrawer').addClass('open');
                        $('#instructionOverlay').addClass('open');
                        $('body').css('overflow', 'hidden'); // Lock background scroll
                    });

                    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
                        $('#instructionDrawer').removeClass('open');
                        $('#instructionOverlay').removeClass('open');
                        $('body').css('overflow', ''); // Unlock scroll
                    });
                    <?php 
                    $successMsg = $this->session->flashdata('success') ?: $this->session->flashdata('message');
                    if ($successMsg) : 
                    ?>
                        Swal.fire({
                            title: 'Success!',
                            text: '<?= strip_tags($successMsg); ?>',
                            icon: 'success',
                            customClass: {
                                popup: 'swal2-premium-popup',
                                confirmButton: 'swal2-premium-confirm'
                            },
                            buttonsStyling: false
                        });
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')) : ?>
                        Swal.fire({
                            title: 'Error!',
                            html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                            icon: 'error',
                            customClass: {
                                popup: 'swal2-premium-popup',
                                confirmButton: 'swal2-premium-confirm'
                            },
                            buttonsStyling: false
                        });
                    <?php endif; ?>
                });
            </script>
            <div class="card mb-3" style="max-width: 540px;">
                <div class="row no-gutters">
                    <div class="col-md-4">
                        <img src="<?= base_url('assets/img/profile/') . (isset($user['image']) && !empty($user['image']) ? $user['image'] : 'default.jpg'); ?>" class="shadow card-img" alt="Profile Image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= isset($user['name']) ? $user['name'] : (isset($user['c_name']) ? $user['c_name'] : 'Administrator'); ?></h5>
                            <p class="card-text"><?= isset($user['email']) ? $user['email'] : (isset($user['c_email']) ? $user['c_email'] : '-'); ?></p>
                            <p class="card-text"><small class="text-muted">Member Since <?= isset($user['date_created']) ? date('d F Y', $user['date_created']) : 'N/A'; ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
