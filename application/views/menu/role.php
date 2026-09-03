<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage system roles and configure their menu access permissions.</p>
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
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Menu Role Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page displays all registered security roles within the system. You can configure the dashboard menu permissions for each role.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-user-tag text-primary mr-2"></i> Menu Role Setup</div>
                <p class="drawer-card-text">View role classifications (e.g. Administrator, Member). Each card represents a distinct access group.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-cog text-primary mr-2"></i> Configuration Action</div>
                <p class="drawer-card-text">Click the <strong>Configure</strong> button on any card to view and manage specific parent and submenu access parameters for that role.</p>
            </div>
        </div>
    </div>

    <!-- ── Alert Messages ── -->
    <?php if ($this->session->flashdata('message')) : ?>
        <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn mb-4">
            <i class="fas fa-check-circle "></i> <?= $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>

    <!-- ── Role Cards ── -->
    <div class="row">
        <?php $i = 1; foreach ($role as $r): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; transition: box-shadow 0.2s;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="mr-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:52px; height:52px; background: linear-gradient(135deg, #4e73df22, #4e73df44); flex-shrink:0;">
                        <i class="fas fa-user-shield text-primary" style="font-size:20px;"></i>
                    </div>
                    <div class="flex-grow-1 mr-3">
                        <div class="small text-muted mb-1" style="text-transform:uppercase; letter-spacing:0.08em; font-size:10px; font-weight:600;">Role #<?= $i ?></div>
                        <div class="font-weight-bold text-dark" style="font-size:15px;"><?= $r->role_name; ?></div>
                    </div>
                    <a href="<?= base_url('access-control/roles/access/') . $r->id; ?>"
                       class="btn-dt-chip-action btn-dt-secondary flex-shrink-0"
                       style="white-space:nowrap;">
                        <i class="fas fa-cog mr-1 mr-2"></i> Configure
                    </a>
                </div>
            </div>
        </div>
        <?php $i++; endforeach; ?>

        <?php if (empty($role)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-user-shield fa-3x mb-3 d-block"></i>
                    <p>No roles found.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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



