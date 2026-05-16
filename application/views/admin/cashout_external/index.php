<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashout External Merchant</h4>
            <p class="dt-page-subtitle">Centralized configuration for all merchant cashout channels</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary" data-toggle="modal" data-target="#globalUpdateModal">
                <i class="fas fa-globe mr-1 mr-2"></i> Edit Mapping
            </button>
            <a href="<?= base_url('admin/cashout/external/add_view'); ?>" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Mapping
            </a>
        </div>
    </div>


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search merchant or channel...">
            </div>

        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="cashoutTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="40" class="ps-4">No</th>
                        <th>Merchant</th>
                        <th>Channel Config</th>
                        <th>Fee Details</th>
                        <th class="text-right">Limits</th>
                        <th class="text-center">Status</th>
                        <th width="80" class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="dt-footer-container"></div>
    </div>
</div>

<!-- Edit Mapping Modal -->
<div class="modal fade" id="globalUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 mh-premium" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title">Global Channel Update</h6>
                        <small class="mh-subtitle">Switch providers for all merchants at once</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="globalUpdateForm" action="<?= base_url('admin/cashout/external/bulk_update'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info border-0 shadow-sm small mb-4">
                        <i class="fas fa-info-circle mr-2"></i> Configure the criteria for the bulk channel update.
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted mb-3">Update Scope</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="update-choice-card w-100 mb-0" for="updateByGroup">
                                    <input type="radio" id="updateByGroup" name="update_type" value="group" checked class="d-none">
                                    <div class="choice-content p-3 border rounded-3 position-relative h-100">
                                        <div class="choice-icon mb-2">
                                            <i class="fas fa-globe text-primary fa-lg"></i>
                                        </div>
                                        <div class="choice-text">
                                            <div class="fw-bold small mb-1">Edit Mapping</div>
                                            <div class="smaller text-muted">Update channels for all merchants in a group</div>
                                        </div>
                                        <div class="choice-check position-absolute" style="top: 10px; right: 10px;">
                                            <div class="check-circle"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="update-choice-card w-100 mb-0" for="updateByMerchant">
                                    <input type="radio" id="updateByMerchant" name="update_type" value="merchant" class="d-none">
                                    <div class="choice-content p-3 border rounded-3 position-relative h-100">
                                        <div class="choice-icon mb-2">
                                            <i class="fas fa-store text-primary fa-lg"></i>
                                        </div>
                                        <div class="choice-text">
                                            <div class="fw-bold small mb-1">Specific Merchant</div>
                                            <div class="smaller text-muted">Update channels for one specific merchant only</div>
                                        </div>
                                        <div class="choice-check position-absolute" style="top: 10px; right: 10px;">
                                            <div class="check-circle"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="merchantSelectGroup" class="mb-3" style="display: none;">
                        <label class="form-label small fw-bold text-muted">Select Merchant</label>
                        <select class="form-control select2" name="ref_merchantId" id="global_merchant">
                            <option value="" selected disabled>Select merchant</option>
                            <?php foreach ($merchants as $m): ?>
                                <option value="<?= $m->id ?>"><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-light text-muted px-2 py-1 mr-2">1</span>
                                <span class="small fw-bold text-muted">CURRENT CONFIGURATION (FILTER)</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">Channel Group</label>
                                <select class="form-control select2" name="current_group" id="global_current_group" required>
                                    <option value="" selected disabled>Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">External Provider (Optional)</label>
                                <select class="form-control select2" name="current_externalId" id="global_current_external">
                                    <option value="" selected>All External Providers</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="current_cashoutChannelId" id="global_current_channel">
                                    <option value="" selected>All Channel IDs</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr class="my-4 border-dashed">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary-soft text-primary px-2 py-1 mr-2">2</span>
                                <span class="small fw-bold text-primary">NEW CONFIGURATION (TARGET)</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Channel Group</label>
                                <select class="form-control select2" name="new_group" id="global_new_group" required>
                                    <option value="" selected disabled>Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New External Provider (Optional)</label>
                                <select class="form-control select2" name="new_externalId" id="global_new_external">
                                    <option value="" selected>Don't Update (Keep Original)</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="new_cashoutChannelId" id="global_new_channel">
                                    <option value="" selected>Don't Update (Keep Original)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">UPDATE ALL MERCHANTS</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    $('#feeModal, #globalUpdateModal').on('shown.bs.modal', function() {
        var $modal = $(this);
        $modal.find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({ 
                dropdownParent: $(this).parent(),
                width: '100%' 
            });
        });
    });

    $('#globalUpdateModal').on('hidden.bs.modal', function() {
        $(this).find('form').each(function() {
            this.reset();
        });
        $(this).find('.select2').val('').trigger('change');
    });

    var table = initServerDataTable("#cashoutTable", "<?= base_url('admin/cashout/external/ajax_list') ?>", [
        { "data": "no", "className": "ps-4 text-muted small" },
        { 
            "data": "merchant_name",
            "render": function(data, type, row) {
                return `<div class="fw-bold text-dark">${data}</div><small class="text-muted">ID: ${row.ref_merchantId}</small>`;
            }
        },
        { 
            "data": "c_cashoutChannelGroup",
            "render": function(data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">${data}</span>
                        <span class="text-muted small">Channel: <code class="text-primary">${row.ref_cashoutChannelId}</code></span>
                        <span class="text-muted smaller">Provider: ${row.c_externalIdDefault}</span>
                    </div>
                `;
            }
        },
        {
            "data": "c_feeType",
            "render": function(data, type, row) {
                const fee = new Intl.NumberFormat('id-ID').format(row.c_fee);
                return `
                    <div class="d-flex flex-column">
                        <div class="d-flex justify-content-between small"><span class="text-muted">Type:</span><span class="badge badge-light text-dark px-2 py-0">${data}</span></div>
                        <div class="d-flex justify-content-between fw-bold mt-1"><span class="text-muted small">Fixed:</span><span class="text-primary">Rp ${fee}</span></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Percentage:</span><span class="text-success fw-bold">${row.c_feePercetange}%</span></div>
                    </div>
                `;
            }
        },
        {
            "data": "c_amountMin",
            "className": "text-right",
            "render": function(data, type, row) {
                const min = new Intl.NumberFormat('id-ID').format(data);
                const max = new Intl.NumberFormat('id-ID').format(row.c_amountMax);
                return `<div class="d-flex flex-column align-items-end"><small class="text-muted">Min: Rp ${min}</small><small class="text-muted">Max: Rp ${max}</small></div>`;
            }
        },
        {
            "data": "c_status",
            "className": "text-center",
            "render": function(data) {
                const style = data === 'Active' ? 'success' : 'secondary';
                return `<span class="badge bg-${style}-soft text-${style} rounded-pill px-3 py-1">${data}</span>`;
            }
        },
        {
            "data": null,
            "className": "text-center pe-4",
            "orderable": false,
            "render": function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg">
                            <li><a class="dropdown-item" href="<?= base_url('admin/cashout/external/edit_view') ?>/${row.id}"><i class="fas fa-edit text-primary mr-2"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger delete-btn" href="javascript:void(0)" data-href="<?= base_url('admin/cashout/external/delete') ?>/${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ], { order: [[1, 'asc']] });


    // Reset modal on close
    $('#globalUpdateModal').on('hidden.bs.modal', function () {
        const $form = $('#globalUpdateForm');
        $form[0].reset();
        $form.find('.select2').val(null).trigger('change');
        $('#updateByGroup').prop('checked', true).trigger('change');
        $('#merchantSelectGroup').hide();
        restoreBtn($form);
    });

    $('#globalUpdateForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const $btn = $(form).find('button[type="submit"]');

        const updateType = $('input[name="update_type"]:checked').val();
        const merchant = $('#global_merchant').val();
        const curGroup = $('#global_current_group').val();
        const curExt = $('#global_current_external').val();
        const curChan = $('#global_current_channel').val();
        const newGroup = $('#global_new_group').val();
        const newExt = $('#global_new_external').val();
        const newChan = $('#global_new_channel').val();

        // Validation: Group is always required
        if (!curGroup || !newGroup) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Current Group and New Group are required!',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', actions: 'swal2-premium-actions' },
                buttonsStyling: false
            });
            return false;
        }

        if (updateType === 'merchant' && !merchant) {
            Swal.fire({
                icon: 'error',
                title: 'Merchant Required',
                text: 'Please select a merchant for this update type!',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', actions: 'swal2-premium-actions' },
                buttonsStyling: false
            });
            return false;
        }

        if (curGroup === newGroup && !newExt && !newChan) {
            Swal.fire({
                icon: 'info',
                title: 'No Changes',
                text: 'The new configuration is identical to the current one.',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', actions: 'swal2-premium-actions' },
                buttonsStyling: false
            });
            return false;
        }

        Swal.fire({
            title: 'Confirm Bulk Update?',
            text: "This will affect all matching channel mappings!",
            icon: 'warning',
            showCancelButton: true,
            customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', cancelButton: 'swal2-premium-cancel', actions: 'swal2-premium-actions' },
            buttonsStyling: false,
            confirmButtonText: 'Yes, update all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> UPDATING...');
                form.submit();
            }
        });
    });

    // Flash Data Notifications
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $this->session->flashdata('success'); ?>',
            customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', actions: 'swal2-premium-actions' },
            buttonsStyling: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= $this->session->flashdata('error'); ?>',
            customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', actions: 'swal2-premium-actions' },
            buttonsStyling: false
        });
    <?php endif; ?>

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const url = $(this).data('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "This configuration will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            customClass: {
                popup: 'swal2-premium-popup',
                confirmButton: 'swal2-premium-confirm',
                cancelButton: 'swal2-premium-cancel',
                actions: 'swal2-premium-actions'
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    $('#dt-search').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
