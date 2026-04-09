<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Configure menu permissions for the <strong><?= $role['role']; ?></strong> role.</p>
        </div>
    </div>

    <?= $this->session->flashdata('message'); ?>

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

</div>
<!-- End of Main Content -->
