<div >
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Ensure your account security by updating your password regularly.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <!-- Alerts Standardized to Swal2 Premium -->
            <script>
                $(document).ready(function() {
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

            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-4">
                    <form action="<?= base_url('user/changePassword'); ?>" method="post">
                        <div class="form-group mb-4">
                            <label class="dt-more-label mb-2" for="currentPassword">Current Password</label>
                            <input type="password" name="currentPassword" id="currentPassword" class="form-control dt-card border-0 shadow-none px-3" style="background: var(--dt-bg-subtle); height: 45px;" placeholder="Enter current password">
                            <?= form_error('currentPassword', '<small class="text-danger pl-2 mt-1 d-block">', '</small>'); ?>
                        </div>

                        <div class="form-group mb-4">
                            <label class="dt-more-label mb-2" for="newPassword">New Password</label>
                            <input type="password" name="newPassword" id="newPassword" class="form-control dt-card border-0 shadow-none px-3" style="background: var(--dt-bg-subtle); height: 45px;" placeholder="Minimum 6 characters">
                            <?= form_error('newPassword', '<small class="text-danger pl-2 mt-1 d-block">', '</small>'); ?>
                        </div>

                        <div class="form-group mb-4">
                            <label class="dt-more-label mb-2" for="repeatPassword">Repeat Password</label>
                            <input type="password" name="repeatPassword" id="repeatPassword" class="form-control dt-card border-0 shadow-none px-3" style="background: var(--dt-bg-subtle); height: 45px;" placeholder="Repeat new password">
                            <?= form_error('repeatPassword', '<small class="text-danger pl-2 mt-1 d-block">', '</small>'); ?>
                        </div>

                        <button type="submit" class="btn-dt-action btn-dt-action-primary shadow-sm px-4 mt-2 w-100 py-2">
                            <i class="fas fa-key mr-2"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm dt-card bg-light">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-shield-alt mr-2"></i> Password Requirements</h6>
                    <ul class="text-muted small pl-3 mb-0">
                        <li class="mb-2">Must be at least 6 characters long.</li>
                        <li class="mb-2">Should contain a mix of letters and numbers for better security.</li>
                        <li class="mb-2">Cannot be the same as your current password.</li>
                        <li>Avoid using common words or personal information.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
