<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Update your personal account information and profile picture.</p>
        </div>
    </div>

    <!-- ── Profile Edit Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <div class="card-body p-4">
            <!-- Content -->
            <div class="row">
                <div class="col-lg-8">
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
                                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-1 mr-2"></i> Recommended size: 512x512px. Format: JPG, PNG.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn-dt-action btn-dt-action-primary shadow-sm px-4">
                                <i class="fas fa-save  mr-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->




