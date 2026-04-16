<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Define and manage administrative roles and their corresponding system permissions.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-primary shadow-sm" data-toggle="modal" data-target="#newRoleModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add New Role
            </button>
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
                                            <a href="<?= base_url('admin/roleaccess/') . $r['id']; ?>" class="btn btn-sm btn-dt-action-primary shadow-sm mr-1">
                                                <i class="fas fa-key fa-sm mr-1 mr-2"></i> Access
                                            </a>
                                            <a href="<?= base_url('admin/deleteRole/') . $r['id']; ?>" class="btn btn-sm btn-dt-secondary border shadow-sm text-danger" onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="fas fa-trash fa-sm mr-1 mr-2"></i> Delete
                                            </a>
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
<div class="modal fade" id="newRoleModal" tabindex="-1" role="dialog" aria-labelledby="newRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title"  id="newRoleModalLabel">ADD NEW ROLE</h6>
            <small class="mh-subtitle" >Create and register new data record</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
            <form action="<?= base_url('admin/role'); ?>" method="post">
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="dt-more-label mb-2">Role Name</label>
                        <input type="text" class="dt-more-input" id="role" name="role" placeholder="e.g. Finance Admin" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn-dt-action btn-dt-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-action btn-dt-action-primary shadow-sm px-4">
                        <i class="fas fa-save mr-1 mr-2"></i> Add Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



