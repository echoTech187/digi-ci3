<!-- Begin Page Content -->
<div class="container-fluid pb-4">

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="<?= base_url('admin/merchant_spv'); ?>" class="btn btn-sm btn-light border rounded-circle " title="Back to Supervisors">
                    <i class="fas fa-arrow-left text-primary"></i>
                </a>
                <h4 class="dt-page-title text-dark fw-700 mb-0">Merchants for Supervisor #<?= $supervisor_id ?></h4>
            </div>
            <p class="dt-page-subtitle text-muted">Viewing all merchants currently assigned to this supervisor.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-primary border shadow-sm px-3 py-2" style="font-size: 13px;">
                <i class="fas fa-store mr-1"></i> <?= count($merchants) ?> Merchants Found
            </span>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <div class="dt-toolbar-left">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search merchants...">
                </div>
            </div>
            <div class="dt-toolbar-right">
                <!-- Additional filters could go here -->
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="supervisorMerchantTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">ID</th>
                        <th>MERCHANT NAME</th>
                        <th>TOTAL BALANCE</th>
                        <th>HOLD BALANCE</th>
                        <th>OPENAPI</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($merchants)): ?>
                        <!-- Table will handle zeroRecords -->
                    <?php else: ?>
                        <?php foreach ($merchants as $m): ?>
                            <tr>
                                <td class="text-center"><span class="badge badge-light text-dark border px-2 py-1 text-dark"><?= $m->id ?></span></td>
                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($m->c_name) ?></td>
                                <td class="font-weight-bold text-primary">Rp <?= number_format($m->c_balanceTotal, 0, ',', '.') ?></td>
                                <td class="text-muted small">Rp <?= number_format($m->c_balanceHold, 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-<?= $m->c_openapistatus === 'Active' ? 'success' : 'secondary' ?>">
                                        <?= $m->c_openapistatus ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $m->c_status === 'Active' ? 'success' : 'danger' ?>">
                                        <?= $m->c_status ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-white border-top-0 p-4 pt-0">
             <!-- Any bottom buttons could go here -->
        </div>
    </div><!-- /.dt-card -->

</div><!-- /.container-fluid -->

<script>
$(document).ready(function() {
    // ── DataTable Initialization ──
    const table = $('#supervisorMerchantTable').DataTable({
        dom: 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
        pageLength: 10,
        order: [[1, 'asc']], // Sort by Merchant Name
        language: {
            "info": "Showing _START_ – _END_ of _TOTAL_ entries",
            "infoEmpty": "No entries to show",
            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No merchants assigned to this supervisor.</div>'
        },
        drawCallback: function(settings) {
            var api    = this.api();
            var info   = api.page.info();
            var $pager = $(api.table().container()).find('.dt-footer-pager');

            var currPage   = info.page + 1;
            var totalPages = info.pages || 1;

            $pager.html(
                '<button class="dt-nav-btn dt-prev-btn" ' + (info.page === 0 ? 'disabled' : '') + '>' +
                    '<i class="fas fa-chevron-left mr-2"></i> PREVIOUS' +
                '</button>' +
                '<span class="dt-page-counter">' +
                    '<strong>' + currPage + '</strong> of <strong>' + totalPages + '</strong>' +
                '</span>' +
                '<button class="dt-nav-btn dt-next-btn" ' + (info.page >= totalPages - 1 ? 'disabled' : '') + '>' +
                    'NEXT <i class="fas fa-chevron-right"></i>' +
                '</button>'
            );

            $pager.find('.dt-prev-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('previous').draw('page'); }
            });
            $pager.find('.dt-next-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('next').draw('page'); }
            });
        }
    });

    // Global Search
    $('#dt-global-search').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>



