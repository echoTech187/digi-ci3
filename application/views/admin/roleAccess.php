<!-- Begin Page Content -->
<div >

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Configure menu permissions for the <strong><?= $role['role']; ?></strong> role.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Role Access Rights Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This matrix interface allows administrators to toggle high-level module permission sets for the selected security role.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-toggle-on text-primary mr-2"></i> Interactive Switch</div>
                <p class="drawer-card-text">Toggle switches in the 'Grant Access' column. Checked items grant access to the corresponding menu module, while unchecked items revoke it.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-save text-primary mr-2"></i> Real-time Persist</div>
                <p class="drawer-card-text">Changes made via toggles are processed asynchronously and saved immediately to database permission records.</p>
            </div>
        </div>
    </div>

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

    <div class="row">
        <div class="col-lg-8">
            <!-- ── Permissions Card ── -->
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <table class="table dt-table mb-0" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">NO</th>
                                <th>MENU MODULE</th>
                                <th class="text-center" style="width: 150px;">GRANT ACCESS</th>
                            </tr>
                        </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($menu as $m) : ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="font-weight-bold text-dark"><?= $m['menu']; ?></td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch">
                                                <input class="custom-control-input form-check-input" type="checkbox" id="check-<?= $m['id']; ?>" <?= check_access($role['id'], $m['id']); ?> data-role="<?= $role['id']; ?>" data-menu="<?= $m['id']; ?>">
                                                <label class="custom-control-label" for="check-<?= $m['id']; ?>" style="cursor: pointer;"></label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- /.container-fluid -->

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
});
</script>

