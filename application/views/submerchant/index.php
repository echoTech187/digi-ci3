<?php
$id = $this->uri->segment(3);
?>

<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Sub Accounts Management</h4>
            <p class="dt-page-subtitle">Managing sub accounts for <strong><?= $merchant[0]->c_name ?></strong></p>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle "></i> <?= $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle "></i> <?= $this->session->flashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search submerchants...">
            </div>

            <!-- RIGHT: Actions -->
            <div class="dt-toolbar-filters">
                <div class="dt-filter-group">
                    <label class="dt-filter-label">&nbsp;</label>
                    <button type="button" class="btn-dt-chip-action btn-dt-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt mr-1 mr-2"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="submerchantTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Submerchant Name</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Pagination/Info handled via JS container in footer -->
         <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Server-side DataTable
    var table = initServerDataTable("#submerchantTable", "<?= base_url('admin/submerchant/'.$id) ?>", [
        { data: 'no', orderable: false },
        { data: 'c_name', className: 'font-weight-bold text-gray-800' },
        { data: 'c_email' },
        { data: 'c_status', className: 'text-center' },
        { data: 'action', className: 'text-center', orderable: false }
    ], {
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "infoFiltered": "",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No subaccounts found.</div>'
            },
            "dom": 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
            "drawCallback": function(settings) {
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

    // Global search with Debounce
    $('#dt-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));
});
</script>
