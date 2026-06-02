<!-- Begin Page Content -->
<div >
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Define and manage administrative roles and their corresponding system permissions.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-success shadow-sm" data-toggle="modal" data-target="#newRoleModal" >
                <i class="fas fa-plus mr-2"></i> Add New Role
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Security Roles Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page allows administrators to define, create, and manage back-office security roles and their granular access profiles.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-user-tag text-primary mr-2"></i> Security Roles</div>
                <p class="drawer-card-text">Establish role classifications (e.g. Administrator, Supervisor, Finance Admin) to map to organizational duties.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-key text-primary mr-2"></i> Permission Management</div>
                <p class="drawer-card-text">Click the Actions dropdown next to a role and select 'Access Rights' to toggle specific dashboard menu accesses.</p>
            </div>
        </div>
    </div>

    <?= form_error('role', '<div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fas fa-exclamation-circle "></i>', '</div>'); ?>
    <?= $this->session->flashdata('message'); ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- ── Roles Table Card ── -->
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="dataTable" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 70px;">NO</th>
                                    <th>ROLE NAME</th>
                                    <th class="text-center" style="width: 250px;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($role as $r) : ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted"><?= $i++ ?></td>
                                        <td class="font-weight-bold text-dark"><?= $r['role']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm rounded-circle shadow-none p-2" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg p-2" style="border-radius: 12px; min-width: 160px;">
                                                    <li>
                                                        <a class="dropdown-item rounded-2 py-2" href="<?= base_url('access-control/roles/access/') . $r['id']; ?>">
                                                            <i class="fas fa-key text-primary mr-2"></i> Access Rights
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item rounded-2 py-2 text-danger delete-role-btn" data-href="<?= base_url('access-control/roles/delete/') . $r['id']; ?>" data-role="<?= $r['role']; ?>">
                                                            <i class="fas fa-trash mr-2"></i> Delete Role
                                                        </button>
                                                    </li>
                                                </ul>
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


<!-- Modal Input -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="newRoleModal" tabindex="-1" role="dialog" aria-labelledby="newRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="newRoleModalLabel">ADD NEW ROLE</h6>
                        <small class="mh-subtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('access-control/roles'); ?>" method="post">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user-shield fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-0" style="font-size: 15px;">Role Security Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Establish role parameters for fine-grained system access controls.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-tag text-warning mr-2"></i> 1. Role Name</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Enter a logical title mapping to organizational tasks (e.g., Finance Admin).</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-key text-info mr-2"></i> 2. Authorization</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">After creating the role, assign specific system endpoints inside the role Access Rights matrix.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="form-group mb-0">
                                <label class="dt-more-label mb-2">Role Name</label>
                                <input type="text" class="dt-more-input" id="role" name="role" placeholder="e.g. Finance Admin" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-action btn-dt-action-success shadow-sm px-4">
                        <i class="fas fa-save mr-2"></i> Add Role
                    </button>
                </div>
            </form>
        </div>
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

    $('.delete-role-btn').on('click', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        var roleName = $(this).data('role');
        Swal.fire({
            title: 'Delete Role',
            text: 'Are you sure you want to delete the role "' + roleName + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            customClass: {
                popup: 'swal2-premium-popup',
                confirmButton: 'swal2-premium-confirm',
                cancelButton: 'swal2-premium-cancel'
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>



