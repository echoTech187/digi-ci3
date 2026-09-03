<!-- Begin Page Content -->
<div >

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Update your personal account information and profile picture.</p>
        </div>
    </div>

    <div class="row">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- ── Profile Edit Card ── -->
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-4">
                    <?= form_open_multipart('user/edit'); ?>
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="email" id="email" value="<?= $user['email']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Full Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" id="name" value="<?= $user['name']; ?>">
                            <?= form_error('name', '<small class="text-danger pl-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            Picture
                        </div>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-3">
                                    <img src="<?= base_url('assets/img/profile/') . $user['image']; ?>" class="img-thumbnail dt-card shadow-sm" style="border-radius: 12px; border: 2px solid var(--dt-card-border);" alt="<?= $user['image']; ?>">
                                </div>
                                <div class="col-sm-9">
                                    <div class="custom-file dt-card" style="border-radius: 8px; overflow: hidden;">
                                        <input type="file" class="custom-file-input" id="image" name="image">
                                        <label class="custom-file-label border-0" for="image">Choose new profile picture...</label>
                                    </div>
                                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-2"></i> Recommended size: 512x512px. Format: JPG, PNG.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn-dt-action btn-dt-action-success shadow-sm px-4">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Instructions Guide -->
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book-open mr-2"></i> Instructions Guide</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="text-muted small mb-4">Manage your personal profile and account credentials.</p>
                    <div class="p-3 mb-3" style="background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.05); border-radius:12px;">
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-envelope text-primary mr-2"></i> Read-Only Email</div>
                        <p class="text-muted small mb-0">Your email address is used for authentication and account security. It cannot be changed directly for safety reasons.</p>
                    </div>
                    <div class="p-3 mb-3" style="background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.05); border-radius:12px;">
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-user text-primary mr-2"></i> Full Name</div>
                        <p class="text-muted small mb-0">Ensure your name is spelled correctly and matches official documentation to prevent issues with identity verification.</p>
                    </div>
                    <div class="p-3" style="background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.05); border-radius:12px;">
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-image text-primary mr-2"></i> Profile Picture</div>
                        <p class="text-muted small mb-0">Upload a professional JPG or PNG image. For best results, use a square image (recommended size 512x512px).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->




