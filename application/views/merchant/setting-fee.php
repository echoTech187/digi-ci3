<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashin Fee Settings</h4>
            <p class="dt-page-subtitle">Configure payment channel fees for <strong><?= $merchant_name ?></strong></p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-success add-btn" data-toggle="modal" data-target="#feeModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Channel
            </button>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- Alerts -->
    <!-- Alerts Standardized to Swal2 Premium -->
    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= $this->session->flashdata('success'); ?>',
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

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search setting...">
            </div>

            <!-- RIGHT: Actions -->
            <div class="dt-toolbar-filters">
                <div class="dt-filter-group">
                    <label class="dt-filter-label">&nbsp;</label>
                    <div class="d-flex" style="gap:6px;">
                        <button type="button" class="btn-dt-chip-action btn-dt-primary" data-toggle="modal" data-target="#bulkModal">
                            <i class="fas fa-layer-group mr-1 mr-2"></i> Bulk Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="cashinTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="40" class="ps-4">No</th>
                        <th>Channel Config</th>
                        <th>Fee Details</th>
                        <th class="text-center">Interval</th>
                        <th class="text-right">Limits</th>
                        <th class="text-center">Status</th>
                        <th width="80" class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody id="cashinTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
        <div id="dt-footer-container"></div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="feeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-down"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="feeModalTitle">Add Cashin Fee Setting</h6>
                        <small class="mh-subtitle" id="feeModalSubtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm" method="post" action="<?= base_url('admin/createSettingCashinFee/' . $merchant_id); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-network-wired  mr-2"></i> CHANNEL CONFIG
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Channel Group</label>
                                    <select class="form-control border-1 select2" id="c_cashinChannelGroup" required name="c_cashinChannelGroup">
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
                                    <select class="form-control border-1 select2" id="ref_cashinChannelId" required name="ref_cashinChannelId" disabled>
                                        <option value="" disabled selected>Select channel ID</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-calculator  mr-2"></i> FEE STRUCTURE
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Fee Type</label>
                                        <select class="form-control border-1 select2" id="c_feeType" required name="c_feeType">
                                            <option value="Fixed">Fixed</option>
                                            <option value="Percetange">Percentage</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Settlement (Days)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control border-1" id="c_settlementInterval" required name="c_settlementInterval">
                                            <span class="input-group-text border-1 small">Days</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Fixed Fee (IDR)</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="number" class="form-control border-1 fw-bold text-primary" required id="c_fee" name="c_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Percentage Fee (%)</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control border-1 fw-bold text-success" required id="c_feePercetange" name="c_feePercetange">
                                            <span class="input-group-text border-1 small">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-none bg-white p-3 rounded-4 mt-4">
                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-shield-alt  mr-2"></i> LIMITS & STATUS
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Min</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1 small">Rp</span>
                                    <input type="number" class="form-control border-1" id="c_amountMin" required name="c_amountMin">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Max</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1 small">Rp</span>
                                    <input type="number" class="form-control border-1" id="c_amountMax" required name="c_amountMax">
                                </div>
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
                    <button type="submit" class="btn-dt-apply px-4 ">SAVE CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Modal -->
<div class="modal fade" id="bulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-down"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="bulkModalLabel">Bulk Add Cashin Fees</h6>
                        <small class="mh-subtitle" id="feeModalSubtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('admin/bulkCreateSettingCashinFee/' . $merchant_id); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="alert bg-success-soft text-dark border-0 small mb-4 py-3 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 text-success fa-lg"></i>
                        <span>Apply these settings to <strong>ALL channels</strong> within the selected Group and External ID Default. Existing configurations for duplicates will be skipped.</span>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-success fw-bold mb-3"><i class="fas fa-bullseye  mr-2"></i> TARGET GROUPS</h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Cashin Channel Group</label>
                                    <select class="form-control border-1 select2" required name="c_cashinChannelGroup">
                                        <option value="" selected disabled>Select group</option>
                                        <?php foreach ($channel_groups as $chg): ?>
                                            <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">External ID Default</label>
                                    <select class="form-control border-1 select2" required name="c_externalIdDefault">
                                        <option value="" selected disabled>Select external ID</option>
                                        <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                            <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-success fw-bold mb-3"><i class="fas fa-coins  mr-2"></i> FEE SETTINGS</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Fee Type</label>
                                        <select class="form-control border-1 select2" required name="c_feeType">
                                            <option value="Fixed">Fixed</option>
                                            <option value="Percetange">Percentage</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Fixed Fee</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="number" class="form-control border-1" required name="c_fee">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Fee Percentage (%)</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control border-1" required name="c_feePercetange">
                                            <span class="input-group-text border-1 small">%</span>
                                        </div>
                                    </div>
                                    <div class="col-12 text-muted smaller">
                                        *Limits and settlement will use default values for bulk operations.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end rounded-bottom-4 px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4 ">APPLY BULK SETTINGS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for modal with dropdownParent to fix focus/render issues
    $('#feeModal, #bulkModal').on('shown.bs.modal', function() {
        var $modal = $(this);
        $modal.find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                dropdownParent: $modal,
                dropdownAutoWidth: true,
                width: '100%'
            });
        });
    });

    var table = initServerDataTable("#cashinTable", window.location.href, [
                { "data": "no", "className": "ps-4 text-muted small" },
                { 
                    "data": "c_cashinChannelGroup",
                    "render": function(data, type, row) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${data}</span>
                                <span class="text-muted small">ID: <code class="text-primary">${row.ref_cashinChannelId}</code></span>
                                <span class="text-muted smaller">Ext: ${row.c_externalIdDefault}</span>
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
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Type:</span>
                                    <span class="badge badge-light text-dark-soft text-dark px-2 py-0" style="font-size: 10px;">${data}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold mt-1">
                                    <span class="text-muted small">Fixed:</span>
                                    <span class="text-primary">Rp ${fee}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Percentage:</span>
                                    <span class="text-success fw-bold">${row.c_feePercetange}%</span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "c_settlementInterval",
                    "className": "text-center",
                    "render": function(data) {
                        return `<span class="badge bg-info-soft text-info rounded-pill px-3 py-1">${data} Days</span>`;
                    }
                },
                {
                    "data": "c_amountMin",
                    "className": "text-right",
                    "render": function(data, type, row) {
                        const min = new Intl.NumberFormat('id-ID').format(data);
                        const max = new Intl.NumberFormat('id-ID').format(row.c_amountMax);
                        return `
                            <div class="d-flex flex-column align-items-end">
                                <span class="text-muted small">Min: <span class="text-dark fw-bold">Rp ${min}</span></span>
                                <span class="text-muted small">Max: <span class="text-dark fw-bold">Rp ${max}</span></span>
                            </div>
                        `;
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
                                <button class="btn btn-light btn-sm rounded-circle shadow-none p-2" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <button class="dropdown-item rounded-2 py-2 edit-btn" 
                                                data-toggle="modal" data-target="#feeModal"
                                                data-id="${row.id}"
                                                data-group="${row.c_cashinChannelGroup}"
                                                data-channelid="${row.ref_cashinChannelId}"
                                                data-externalid="${row.c_externalIdDefault}"
                                                data-feetype="${row.c_feeType}"
                                                data-fee="${row.c_fee}"
                                                data-feepercentage="${row.c_feePercetange}"
                                                data-settlement="${row.c_settlementInterval}"
                                                data-min="${row.c_amountMin}"
                                                data-max="${row.c_amountMax}"
                                                data-status="${row.c_status}">
                                            <i class="fas fa-edit text-primary mr-2"></i> Edit Setting
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item rounded-2 py-2 text-danger" href="<?= base_url('admin/deleteSettingCashinFee/'. $merchant_id) ?>/${row.id}" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ], {
            order: [[1, 'asc']],
            language: {
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No settings found.</div>'
            }
        });

    function updateChannelIds() {
        let group = $('#c_cashinChannelGroup').val();
        let external = $('#c_externalIdDefault').val();

        if (group && external) {
            let csrfName = $('meta[name="csrf-token-name"]').attr('content');
            let csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
            
            let postData = {
                c_cashinChannelGroup: group,
                c_externalIdDefault: external
            };
            if (csrfName) postData[csrfName] = csrfHash;

            $.post('<?= base_url("admin/getCashinChannelGroups") ?>', postData, function(data) {
                const options = JSON.parse(data);
                const $channelId = $('#ref_cashinChannelId');
                
                let currentVal = $channelId.val();
                
                $channelId.empty().append('<option disabled selected>Select channel ID</option>');
                if (options.length > 0) {
                    options.forEach(item => $channelId.append(`<option value="${item.id}">${item.id}</option>`));
                    $channelId.prop('disabled', false);
                    if (currentVal) $channelId.val(currentVal).trigger('change');
                } else {
                    $channelId.append('<option disabled>No channels found</option>').prop('disabled', true);
                }
            }).fail(() => {
                console.error('Failed to load channel IDs');
            });
        }
    }

    $('#c_cashinChannelGroup, #c_externalIdDefault').change(updateChannelIds);

    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#feeModalTitle').html('Edit Cashin Fee Configuration');
        $('#feeModalSubtitle').text('Update and modify existing channel fee configuration');
        $('#feeForm').attr('action', `<?= base_url('admin/editSettingCashinFee/' . $merchant_id) ?>/${d.id}`);
        
        $('#c_cashinChannelGroup, #c_externalIdDefault').off('change', updateChannelIds);
        
        $('#c_cashinChannelGroup').val(d.group).trigger('change');
        $('#c_externalIdDefault').val(d.externalid).trigger('change');
        
        const $channelId = $('#ref_cashinChannelId');
        $channelId.empty().append(`<option value="${d.channelid}" selected>${d.channelid}</option>`).prop('disabled', false);

        $('#c_cashinChannelGroup, #c_externalIdDefault').on('change', updateChannelIds);

        $('#c_feeType').val(d.feetype).trigger('change');
        $('#c_fee').val(d.fee);
        $('#c_feePercetange').val(d.feepercentage);
        $('#c_settlementInterval').val(d.settlement);
        $('#c_amountMin').val(d.min);
        $('#c_amountMax').val(d.max);
        $('#c_status').val(d.status).trigger('change');
    });

    $('.add-btn').click(function() {
        $('#feeModalTitle').html('Add New Cashin Fee');
        $('#feeModalSubtitle').text('Create and register new data record');
        $('#feeForm').attr('action', `<?= base_url('admin/createSettingCashinFee/' . $merchant_id) ?>`);
        $('#feeForm')[0].reset();
        
        $('#c_cashinChannelGroup, #c_externalIdDefault, #c_feeType, #c_status').val('').trigger('change');
        $('#ref_cashinChannelId').prop('disabled', true).empty().append('<option disabled selected>Select channel ID</option>');
    });

    // Global search
    $('#dt-search').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>



