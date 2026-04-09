<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashout Fee Settings</h4>
            <p class="dt-page-subtitle">Configure disbursement channel fees for <strong><?= $merchant_name ?></strong></p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-success add-btn" data-toggle="modal" data-target="#feeModal">
                <i class="fas fa-plus mr-1"></i> Add Channel
            </button>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4">
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL CHANNELS</div>
                <div class="dt-summary-value"><?= number_format($total_channels) ?></div>
                <div class="dt-summary-sub"><i class="fas fa-list-ul mr-1"></i>Configured</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">ACTIVE CHANNELS</div>
                <div class="dt-summary-value"><?= number_format($active_channels) ?></div>
                <div class="dt-summary-sub"><i class="fas fa-check-circle mr-1"></i>Currently enabled</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-toggle-on"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-orange">
            <div class="dt-summary-body">
                <div class="dt-summary-label">INACTIVE CHANNELS</div>
                <div class="dt-summary-value"><?= number_format($inactive_channels) ?></div>
                <div class="dt-summary-sub"><i class="fas fa-times-circle mr-1"></i>Currently disabled</div>
            </div>
            <div class="dt-summary-icon dt-icon-orange">
                <i class="fas fa-toggle-off"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-yellow">
            <div class="dt-summary-body">
                <div class="dt-summary-label">MERCHANT</div>
                <div class="dt-summary-value small" style="font-size: 18px;"><?= $merchant_name ?></div>
                <div class="dt-summary-sub"><i class="fas fa-store mr-1"></i>ID: #<?= $merchant_id ?></div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-id-badge"></i>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                            <i class="fas fa-layer-group mr-1"></i> Bulk Add
                        </button>
                        <button type="button" class="btn-dt-chip-action btn-dt-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="cashoutTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="40" class="ps-4">No</th>
                        <th>Channel Config</th>
                        <th>Fee Details</th>
                        <th class="text-right">Limits</th>
                        <th class="text-center">Status</th>
                        <th width="80" class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cashout_channel_x_merchant as $index => $row): ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= $index + 1 ?></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark"><?= $row->c_cashoutChannelGroup; ?></span>
                                    <span class="text-muted small">ID: <code class="text-primary"><?= $row->ref_cashoutChannelId; ?></code></span>
                                    <span class="text-muted smaller">Ext: <?= $row->c_externalIdDefault; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Type:</span>
                                        <span class="badge badge-light text-dark-soft text-dark px-2 py-0" style="font-size: 10px;"><?= $row->c_feeType; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold mt-1">
                                        <span class="text-muted small">Fixed:</span>
                                        <span class="text-primary">Rp <?= number_format($row->c_fee, 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Percentage:</span>
                                        <span class="text-success fw-bold"><?= $row->c_feePercetange; ?>%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-muted small">Min: <span class="text-dark fw-bold">Rp <?= number_format($row->c_amountMin, 0, ',', '.'); ?></span></span>
                                    <span class="text-muted small">Max: <span class="text-dark fw-bold">Rp <?= number_format($row->c_amountMax, 0, ',', '.'); ?></span></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $row->c_status == 'Active' ? 'success' : 'secondary' ?>-soft text-<?= $row->c_status == 'Active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-1">
                                    <?= $row->c_status; ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-none p-2" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2">
                                        <li>
                                            <button class="dropdown-item rounded-2 py-2 edit-btn" 
                                                    data-toggle="modal" data-target="#feeModal"
                                                    data-id="<?= $row->id ?>"
                                                    data-group="<?= $row->c_cashoutChannelGroup ?>"
                                                    data-channelid="<?= $row->ref_cashoutChannelId ?>"
                                                    data-externalid="<?= $row->c_externalIdDefault ?>"
                                                    data-feetype="<?= $row->c_feeType ?>"
                                                    data-fee="<?= $row->c_fee ?>"
                                                    data-feepercentage="<?= $row->c_feePercetange ?>"
                                                    data-min="<?= $row->c_amountMin ?>"
                                                    data-max="<?= $row->c_amountMax ?>"
                                                    data-status="<?= $row->c_status ?>">
                                                <i class="fas fa-edit me-2 text-primary"></i> Edit Setting
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item rounded-2 py-2 text-danger" href="<?= base_url('admin/deleteSettingCashoutFee/'. $merchant_id) . '/' . $row->id; ?>" onclick="return confirm('Are you sure you want to delete this configuration?')">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
            <div class="modal-header modal-header-primary border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-white" id="exampleModalLabel"><i class="fas fa-cog me-2"></i>Add Cashout Fee Setting</h5>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm" method="post" action="<?= base_url('admin/createSettingCashoutFee/' . $merchant_id); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-network-wired me-2"></i> CHANNEL CONFIG
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Channel Group</label>
                                    <select class="form-select border-1" id="c_cashoutChannelGroup" required name="c_cashoutChannelGroup">
                                        <option value="" selected disabled>Select group</option>
                                        <?php foreach ($channel_groups as $chg): ?>
                                            <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">External ID Default</label>
                                    <select class="form-select border-1" id="c_externalIdDefault" required name="c_externalIdDefault">
                                        <option value="" selected disabled>Select external ID</option>
                                        <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                            <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">Specific Channel ID</label>
                                    <select class="form-select border-1" id="ref_cashoutChannelId" required name="ref_cashoutChannelId" disabled>
                                        <option value="" disabled selected>Select channel ID</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-calculator me-2"></i> FEE STRUCTURE
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Fee Type</label>
                                        <select class="form-select border-1" id="c_feeType" required name="c_feeType">
                                            <option value="Fixed">Fixed</option>
                                            <option value="Percetange">Percentage</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Fixed Fee (IDR)</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 bg-light small">Rp</span>
                                            <input type="number" class="form-control border-1 fw-bold text-primary" required id="c_fee" name="c_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Percentage Fee (%)</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control border-1 fw-bold text-success" required id="c_feePercetange" name="c_feePercetange">
                                            <span class="input-group-text border-1 bg-light small">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-none bg-white p-3 rounded-4 mt-4">
                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-shield-alt me-2"></i> LIMITS & STATUS
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Min</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1 bg-light small">Rp</span>
                                    <input type="number" class="form-control border-1" id="c_amountMin" required name="c_amountMin">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Amount Max</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1 bg-light small">Rp</span>
                                    <input type="number" class="form-control border-1" id="c_amountMax" required name="c_amountMax">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Status</label>
                                <select class="form-select border-1" id="c_status" required name="c_status">
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-light rounded-bottom-4 px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4 ml-2">SAVE CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Modal -->
<div class="modal fade" id="bulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-layer-group me-2"></i>Bulk Add Cashout Fees</h5>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('admin/bulkCreateSettingCashoutFee/' . $merchant_id); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="alert bg-success-soft text-dark border-0 small mb-4 py-3 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 text-success fa-lg"></i>
                        <span>Apply these settings to <strong>ALL channels</strong> within the selected Group and External ID Default. Existing configurations for duplicates will be skipped.</span>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-none bg-white p-3 rounded-4">
                                <h6 class="text-success fw-bold mb-3"><i class="fas fa-bullseye me-2"></i>TARGET GROUPS</h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Cashout Channel Group</label>
                                    <select class="form-select border-1" required name="c_cashoutChannelGroup">
                                        <option value="" selected disabled>Select group</option>
                                        <?php foreach ($channel_groups as $chg): ?>
                                            <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">External ID Default</label>
                                    <select class="form-select border-1" required name="c_externalIdDefault">
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
                                <h6 class="text-success fw-bold mb-3"><i class="fas fa-coins me-2"></i>FEE SETTINGS</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Fee Type</label>
                                        <select class="form-select border-1" required name="c_feeType">
                                            <option value="Fixed">Fixed</option>
                                            <option value="Percetange">Percentage</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Fixed Fee</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 bg-light small">Rp</span>
                                            <input type="number" class="form-control border-1" required name="c_fee">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Fee Percentage (%)</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control border-1" required name="c_feePercetange">
                                            <span class="input-group-text border-1 bg-light small">%</span>
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
                <div class="modal-footer border-0 p-3 justify-content-end bg-light rounded-bottom-4 px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4 ml-2">APPLY BULK SETTINGS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#cashoutTable').DataTable({
            "processing": false,
            "serverSide": false,
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "infoFiltered": "",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No settings found.</div>'
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
                        '<i class="fas fa-chevron-left"></i> PREVIOUS' +
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

    // Dynamic Channel ID loading
    function updateChannelIds() {
        let group = $('#c_cashoutChannelGroup').val();
        let external = $('#c_externalIdDefault').val();

        if (group && external) {
            $.post('<?= base_url("admin/getCashoutChannelGroups") ?>', {
                c_cashoutChannelGroup: group,
                c_externalIdDefault: external
            }, function(data) {
                const options = JSON.parse(data);
                const $channelId = $('#ref_cashoutChannelId');
                $channelId.empty().append('<option disabled selected>Select channel ID</option>');
                if (options.length > 0) {
                    options.forEach(item => $channelId.append(`<option value="${item.id}">${item.id}</option>`));
                    $channelId.prop('disabled', false);
                } else {
                    $channelId.append('<option disabled>No channels found</option>').prop('disabled', true);
                }
            }).fail(() => alert('Failed to load channel IDs.'));
        }
    }

    $('#c_cashoutChannelGroup, #c_externalIdDefault').change(updateChannelIds);

    // Edit Button Handler
    // Use event delegation for buttons inside DataTables
    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#exampleModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Cashout Fee Configuration');
        $('#feeForm').attr('action', `<?= base_url('admin/editSettingCashoutFee/' . $merchant_id) ?>/${d.id}`);
        
        $('#c_cashoutChannelGroup').val(d.group);
        $('#c_externalIdDefault').val(d.externalid);
        
        // Manual trigger for channel ID since it depends on AJAX
        const $channelId = $('#ref_cashoutChannelId');
        $channelId.empty().append(`<option value="${d.channelid}" selected>${d.channelid}</option>`).prop('disabled', false);

        $('#c_feeType').val(d.feetype);
        $('#c_fee').val(d.fee);
        $('#c_feePercetange').val(d.feepercentage);
        $('#c_amountMin').val(d.min);
        $('#c_amountMax').val(d.max);
        $('#c_status').val(d.status);
    });

    $('.add-btn').click(function() {
        $('#exampleModalLabel').html('<i class="fas fa-plus me-2"></i>Add New Cashout Fee');
        $('#feeForm').attr('action', `<?= base_url('admin/createSettingCashoutFee/' . $merchant_id) ?>`);
        $('#feeForm')[0].reset();
        $('#ref_cashoutChannelId').prop('disabled', true).empty().append('<option disabled selected>Select channel ID</option>');
    });

    // Global search
    $('#dt-search').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>

</div>
