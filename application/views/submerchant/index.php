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

    <!-- ── KPI Summary Cards ── -->
    

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
                <!-- Add any additional filters here if needed in the future -->
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

        <!-- Pagination/Info handled via JS -->
        <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="subMerchantModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content glass-card border-0">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-store"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title" id="subMerchantModalLabel">Add New Submerchant</h6>
            <small class="mh-subtitle" >Create and register new data record</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
            <form id="submerchant_form" method="post" action="<?= base_url('admin/registersubMerchant'); ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="ref_merchantId" value="<?= $id ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-title mb-3 text-primary-custom font-weight-bold small uppercase">BASIC INFORMATION</div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Merchant Name</label>
                                <input type="text" class="form-control" required name="c_name" id="c_name" placeholder="Enter name">
                            </div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Merchant Email</label>
                                <input type="email" class="form-control" required name="c_email" id="c_email" placeholder="email@example.com">
                            </div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Status</label>
                                <select class="form-control" name="c_status" id="c_status">
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Blocked">Blocked</option>
                                    <option value="Freeze">Freeze</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="section-title mb-3 text-primary-custom font-weight-bold small uppercase">GVCONNECT INTEGRATION</div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Business ID</label>
                                <input type="text" class="form-control" required name="c_gvconnectBusinessId" id="c_gvconnectBusinessId" placeholder="GV-XXXX">
                            </div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Business Name</label>
                                <input type="text" class="form-control" required name="c_gvconnectBusinessName" id="c_gvconnectBusinessName" placeholder="GV Business Name">
                            </div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Connect Key (Optional)</label>
                                <input type="text" class="form-control" name="c_gvconnectGVConnectKey" id="c_gvconnectGVConnectKey" placeholder="Secret Key">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="section-title mb-3 text-primary-custom font-weight-bold small uppercase text-center">STATIC CHANNELS (OPTIONAL)</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">QRIS Static Raw</label>
                            <input type="text" class="form-control form-control-sm" name="c_gvconnectStaticQrisRaw" id="c_gvconnectStaticQrisRaw">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">VA BNI Static</label>
                            <input type="text" class="form-control form-control-sm" name="c_gvconnectStaticVaBni" id="c_gvconnectStaticVaBni">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">VA BCA Static</label>
                            <input type="text" class="form-control form-control-sm" name="c_gvconnectStaticVaBca" id="c_gvconnectStaticVaBca">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">VA CIMB Static</label>
                            <input type="text" class="form-control form-control-sm" name="c_gvconnectStaticVaCimb" id="c_gvconnectStaticVaCimb">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">VA Permata Static</label>
                            <input type="text" class="form-control form-control-sm" name="c_gvconnectStaticVaPermata" id="c_gvconnectStaticVaPermata">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-center">
                    <button type="button" class="btn-dt-action btn-dt-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-dt-action btn-dt-action-primary px-5 shadow-sm">Save Submerchant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Modal Switch: Add vs Edit
    $(document).on('click', '.add-sub-btn', function() {
        $('#modalLabel').text('Add New Submerchant');
        $('#submerchant_form').attr('action', '<?= base_url('admin/registersubMerchant') ?>');
        $('#submerchant_form')[0].reset();
        $('#c_status').val('Active');
    });

    $(document).on('click', '.edit-sub-btn', function() {
        const d = $(this).data();
        $('#modalLabel').text('Edit Submerchant: ' + d.name);
        $('#submerchant_form').attr('action', '<?= base_url('admin/edit_submerchant/') ?>' + d.id);
        
        $('#c_name').val(d.name);
        $('#c_email').val(d.email);
        $('#c_status').val(d.status);
        $('#c_gvconnectBusinessId').val(d.businessid);
        $('#c_gvconnectBusinessName').val(d.businessname);
        $('#c_gvconnectGVConnectKey').val(d.key);
        $('#c_gvconnectStaticQrisRaw').val(d.qris);
        $('#c_gvconnectStaticVaBni').val(d.bni);
        $('#c_gvconnectStaticVaBca').val(d.bca);
        $('#c_gvconnectStaticVaCimb').val(d.cimb);
        $('#c_gvconnectStaticVaPermata').val(d.permata);
    });

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
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No transactions found.</div>'
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

    // Global search
    $('#dt-search').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>


