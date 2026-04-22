<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashout External Merchant</h4>
            <p class="dt-page-subtitle">Centralized configuration for all merchant cashout channels</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-success add-btn" data-toggle="modal" data-target="#feeModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Mapping
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search merchant or channel...">
            </div>
            <div class="dt-toolbar-filters">
                <button type="button" class="btn-dt-chip-action btn-dt-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i>
                </button>
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="feeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="feeModalTitle">Add External Merchant Mapping</h6>
                        <small class="mh-subtitle" id="feeModalSubtitle">Link a merchant to a cashout channel</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm" method="post" action="<?= base_url('admin/cashout/external/add'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id" id="mapping_id">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-none bg-white p-3 rounded-4 mb-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-user-tie mr-2"></i> MERCHANT SELECTION
                                </h6>
                                <select class="form-control border-1 select2" id="ref_merchantId" required name="ref_merchantId">
                                    <option value="" selected disabled>Select merchant</option>
                                    <?php foreach ($merchants as $m): ?>
                                        <option value="<?= $m->id ?>"><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-network-wired mr-2"></i> CHANNEL CONFIG
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Channel Group</label>
                                    <select class="form-control border-1 select2" id="c_cashoutChannelGroup" required name="c_cashoutChannelGroup">
                                        <option value="" selected disabled>Select group</option>
                                        <?php foreach ($channel_groups as $chg): ?>
                                            <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">External ID Default</label>
                                    <select class="form-control border-1 select2" id="c_externalIdDefault" required name="c_externalIdDefault">
                                        <option value="" selected disabled>Select external ID</option>
                                        <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                            <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">Specific Channel ID</label>
                                    <select class="form-control border-1 select2" id="ref_cashoutChannelId" required name="ref_cashoutChannelId" disabled>
                                        <option value="" disabled selected>Select channel ID</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-calculator mr-2"></i> FEE STRUCTURE
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Fee Type</label>
                                        <select class="form-control border-1 select2" id="c_feeType" required name="c_feeType">
                                            <option value="Fixed">Fixed</option>
                                            <option value="Percetange">Percentage</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Fixed Fee (IDR)</label>
                                        <input type="number" class="form-control border-1 fw-bold text-primary" required id="c_fee" name="c_fee">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Percentage Fee (%)</label>
                                        <input type="number" step="any" class="form-control border-1 fw-bold text-success" required id="c_feePercetange" name="c_feePercetange">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-none bg-white p-3 rounded-4 mt-4">
                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-shield-alt mr-2"></i> LIMITS & STATUS
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Min</label>
                                <input type="number" class="form-control border-1" id="c_amountMin" required name="c_amountMin">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Max</label>
                                <input type="number" class="form-control border-1" id="c_amountMax" required name="c_amountMax">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Status</label>
                                <select class="form-control border-1 select2" id="c_status" required name="c_status">
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end rounded-bottom-4 px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">SAVE CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <style>
            .dt-card { overflow: visible !important; }
            .select2-container--default .select2-selection--single {
                height: 38px !important;
                border: 1px solid #d1d3e2 !important;
                border-radius: 6px !important;
                display: flex !important;
                align-items: center !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }
            .form-control {
                height: 38px !important;
                border-radius: 6px !important;
            }
            .card.h-100 { height: auto !important; }
            .g-4, .gy-4 { --bs-gutter-y: 1.5rem; --bs-gutter-x: 1.5rem; }
            /* Support for legacy Bootstrap 4 if g-x classes aren't available */
            .modal-body .row > [class*='col-'] { margin-bottom: 15px; }
            .modal-body .card { padding: 20px !important; }
        </style>
        <script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    $('#feeModal').on('shown.bs.modal', function() {
        var $modal = $(this);
        $modal.find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({ dropdownParent: $modal, width: '100%' });
        });
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
                        <button class="btn btn-light btn-sm rounded-circle p-2" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <li><button class="dropdown-item edit-btn" data-toggle="modal" data-target="#feeModal" data-row='${JSON.stringify(row)}'><i class="fas fa-edit text-primary mr-2"></i> Edit</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('admin/cashout/external/delete') ?>/${row.id}" onclick="return confirm('Delete this configuration?')"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ], { order: [[1, 'asc']] });

    function updateChannelIds() {
        let group = $('#c_cashoutChannelGroup').val();
        let external = $('#c_externalIdDefault').val();
        if (group && external) {
            $.post('<?= base_url("admin/getCashoutChannelGroups") ?>', { 
                c_cashoutChannelGroup: group, 
                c_externalIdDefault: external,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            }, function(data) {
                const options = JSON.parse(data);
                const $channelId = $('#ref_cashoutChannelId');
                let currentVal = $channelId.val();
                $channelId.empty().append('<option disabled selected>Select channel ID</option>');
                if (options.length > 0) {
                    options.forEach(item => $channelId.append(`<option value="${item.id}">${item.id}</option>`));
                    $channelId.prop('disabled', false);
                    if (currentVal) $channelId.val(currentVal).trigger('change');
                } else {
                    $channelId.append('<option disabled>No channels found</option>').prop('disabled', true);
                }
            });
        }
    }

    $('#c_cashoutChannelGroup, #c_externalIdDefault').change(updateChannelIds);

    $(document).on('click', '.edit-btn', function() {
        const row = $(this).data('row');
        $('#feeModalTitle').html('Edit External Mapping');
        $('#feeForm').attr('action', "<?= base_url('admin/cashout/external/update'); ?>");
        $('#mapping_id').val(row.id);
        
        $('#c_cashoutChannelGroup, #c_externalIdDefault').off('change', updateChannelIds);
        
        $('#ref_merchantId').val(row.ref_merchantId).trigger('change');
        $('#c_cashoutChannelGroup').val(row.c_cashoutChannelGroup).trigger('change');
        $('#c_externalIdDefault').val(row.c_externalIdDefault).trigger('change');
        
        $('#ref_cashoutChannelId').empty().append(`<option value="${row.ref_cashoutChannelId}" selected>${row.ref_cashoutChannelId}</option>`).prop('disabled', false);

        $('#c_cashoutChannelGroup, #c_externalIdDefault').on('change', updateChannelIds);

        $('#c_feeType').val(row.c_feeType).trigger('change');
        $('#c_fee').val(row.c_fee);
        $('#c_feePercetange').val(row.c_feePercetange);
        $('#c_amountMin').val(row.c_amountMin);
        $('#c_amountMax').val(row.c_amountMax);
        $('#c_status').val(row.c_status).trigger('change');
    });

    $('.add-btn').click(function() {
        $('#feeModalTitle').html('Add External Mapping');
        $('#feeForm').attr('action', "<?= base_url('admin/cashout/external/add'); ?>");
        $('#feeForm')[0].reset();
        $('.select2').val('').trigger('change');
        $('#ref_cashoutChannelId').prop('disabled', true).empty();
    });

    $('#dt-search').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
