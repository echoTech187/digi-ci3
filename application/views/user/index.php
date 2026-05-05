<!-- Begin Page Content -->
<div >

    <!-- Page Heading -->

    <div class="card shadow border-left-info">
        <div class="card-header">
            <h1 class="h3 text-dark"><?= $title; ?></h1>
        </div>
        <div class="card-body">
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
