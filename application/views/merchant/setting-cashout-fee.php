<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashout Fee Settings</h4>
            <p class="dt-page-subtitle">Configure disbursement channel fees for <strong><?= $merchant_name ?></strong></p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <a href="<?= base_url('helpcenter#cashout-fee-settings') ?>" target="_blank" class="btn-dt-action btn-dt-action-primary toggle-guide-btn" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> Instructions Guide
            </a>
            <button type="button" class="btn-dt-action btn-dt-action-success add-btn" data-toggle="modal" data-target="#feeModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Channel
            </button>
        </div>
    </div>

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
            <div class="dt-toolbar-filters d-flex align-items-center" style="gap: 10px;">
                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper mb-0">
                    <button type="button" id="feeMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="feeFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="feeMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="feeMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Channel Group -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-layer-group mr-1 mr-2"></i> Channel Group</label>
                                <select id="filter_channel_group" class="dt-more-select filter-select">
                                    <option value="">All Groups</option>
                                    <?php foreach ($channel_groups as $cg): ?>
                                        <option value="<?= $cg->c_channelGroup ?>"><?= $cg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            

                            <!-- Provider -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> Provider / External Default</label>
                                <select id="filter_provider" class="dt-more-select filter-select">
                                    <option value="">All Providers</option>
                                    <?php foreach ($channel_external_id_defaults as $prd): ?>
                                        <option value="<?= $prd->c_externalIdDefault ?>"><?= $prd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Channel ID -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-hashtag mr-1 mr-2"></i> Channel ID</label>
                                <select id="filter_channel_id" class="dt-more-select filter-select">
                                    <option value="">All Channel IDs</option>
                                    <?php foreach ($channel_ids as $cid): ?>
                                        <option value="<?= $cid->id ?>"><?= $cid->id ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Status -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                <select id="filter_status" class="dt-more-select filter-select">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="dt-more-panel-footer">
                            <button type="button" id="feeMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="feeMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
                    </div>
                </div>

                <div class="dt-filter-group mb-0">
                    <button type="button" class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#bulkModal">
                        <i class="fas fa-layer-group mr-1 mr-2"></i> Bulk Add
                    </button>
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
                <tbody id="cashoutTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
        <div id="dt-footer-container"></div>
    </div>
    <script>
        $(document).ready(function() {
            $('#toggleGuideBtn').on('click', function() {
                $('#instructionDrawer, #instructionOverlay').addClass('open');
                $('body').css('overflow', 'hidden');
            });

            $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
                $('#instructionDrawer, #instructionOverlay').removeClass('open');
                $('body').css('overflow', '');
            });
        });
    </script>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="feeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-up"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="feeModalLabel">Add Cashout Fee Setting</h6>
                        <small class="mh-subtitle" id="feeModalSubtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm" method="post" action="<?= base_url('merchant/setting-cashout-fee/create'); ?>">
                <input type="hidden" name="ref_merchantId" id="ref_merchantId" value="<?= $merchant_id ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0 bg-dark-subtle" >
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Configuration Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Follow the instructions below to configure disbursement channels, fee structures, and transaction limits accurately.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Channel Selection</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Select a Channel Group and External ID Default to populate and enable Specific Channel ID options.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-calculator text-primary mr-2"></i> 2. Fee Structure</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Choose Fixed for a flat rate, Percentage for a percentage deduction, or Both to apply both charges.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Limits & Status</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Set the minimum and maximum allowable transaction boundaries and set operational status.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mt-3 d-flex align-items-center">
                                <i class="fas fa-lightbulb text-warning fa-2x mr-3"></i>
                                <span class="text-muted" style="font-size: 11px; line-height: 1.4;">Need help? Contact system administrator for advanced routing configurations.</span>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
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
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
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
                                                <div class="input-group">
                                                    <span class="input-group-text border-1 small">Rp</span>
                                                    <input type="text" class="input-rupiah form-control border-1 rounded-left-0 fw-bold text-primary" required id="c_fee" name="c_fee">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted">Percentage Fee (%)</label>
                                                <div class="input-group">
                                                    <input type="text" class="input-percentage form-control border-1 rounded-right-0 fw-bold text-success" required id="c_feePercetange" name="c_feePercetange">
                                                    <span class="input-group-text border-1 rounded-left-0 small">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-none bg-white p-0 rounded-4 mt-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> LIMITS & STATUS
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Min</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1 rounded-left-0" id="c_amountMin" required name="c_amountMin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Max</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1 rounded-left-0" id="c_amountMax" required name="c_amountMax">
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
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">SAVE CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="bulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-up"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="bulkModalLabel">Bulk Add Cashout Fees</h6>
                        <small class="mh-subtitle" >Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('merchant/setting-cashout-fee/bulk-create/' . $merchant_id); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0 bg-dark-subtle" >
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-bullhorn fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-0" style="font-size: 15px;">Bulk Settings Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Apply uniform fee configurations across multiple channels simultaneously with duplicate protection.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-bullseye text-success mr-2"></i> 1. Target Scope</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Configurations apply uniformly to ALL channels in the selected Group and External ID Default.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-shield-alt text-success mr-2"></i> 2. Duplicate Protection</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Channels with existing fee configurations will be automatically skipped to prevent overwriting.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-coins text-success mr-2"></i> 3. Fee & Limits</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Configure uniform fee types, flat rates, percentage deductions, and transaction limits.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mt-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-success fa-2x mr-3"></i>
                                <span class="text-muted" style="font-size: 11px; line-height: 1.4;">Batch operations are processed securely within a single database transaction.</span>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-success fw-bold mb-3"><i class="fas fa-bullseye mr-2"></i> TARGET GROUPS</h6>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Cashout Channel Group</label>
                                            <select class="form-control border-1 select2" id="bulk_c_cashoutChannelGroup" required name="c_cashoutChannelGroup">
                                                <option value="" selected disabled>Select group</option>
                                                <?php foreach ($channel_groups as $chg): ?>
                                                    <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted">External ID Default</label>
                                            <select class="form-control border-1 select2" id="bulk_c_externalIdDefault" required name="c_externalIdDefault">
                                                <option value="" selected disabled>Select external ID</option>
                                                <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                                    <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-success fw-bold mb-3"><i class="fas fa-coins mr-2"></i> FEE SETTINGS</h6>
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
                                                    <input type="text" class="input-rupiah form-control border-1" required name="c_fee">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold text-muted">Fee Percentage (%)</label>
                                                <div class="input-group">
                                                    <input type="text" class="input-percentage form-control border-1" required name="c_feePercetange">
                                                    <span class="input-group-text border-1 small">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-none bg-white p-0 rounded-4 mt-4">
                                <h6 class="text-success fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> LIMITS & STATUS
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Min</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1" required name="c_amountMin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Max</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1" required name="c_amountMax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Status</label>
                                        <select class="form-control border-1 select2" required name="c_status">
                                            <option value="Active">Active</option>
                                            <option value="Not Active">Not Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">APPLY BULK SETTINGS</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    var table = initServerDataTable("#cashoutTable", window.location.href, [
                { "data": "no", "className": "ps-4 text-muted small" },
                { 
                    "data": "c_cashoutChannelGroup",
                    "render": function(data, type, row) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${data}</span>
                                <span class="text-muted small">ID: <code class="text-primary">${row.ref_cashoutChannelId}</code></span>
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
                                <button class="btn btn-sm rounded-circle shadow-none p-2" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg p-2">
                                    <li>
                                        <button class="dropdown-item rounded-2 py-2 edit-btn" 
                                                data-toggle="modal" data-target="#feeModal"
                                                data-id="${row.id}"
                                                data-group="${row.c_cashoutChannelGroup}"
                                                data-channelid="${row.ref_cashoutChannelId}"
                                                data-externalid="${row.c_externalIdDefault}"
                                                data-feetype="${row.c_feeType}"
                                                data-fee="${row.c_fee}"
                                                data-feepercentage="${row.c_feePercetange}"
                                                data-min="${row.c_amountMin}"
                                                data-max="${row.c_amountMax}"
                                                data-status="${row.c_status}">
                                            <i class="fas fa-edit text-primary mr-2"></i> Edit Setting
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item rounded-2 py-2 text-danger delete-fee-btn" data-href="<?= base_url('merchant/setting-cashout-fee/delete/'. $merchant_id) ?>/${row.id}">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ], {
            ajax: {
                url: window.location.href,
                type: "POST",
                data: function (d) {
                    var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                    var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                    if (csrfName && csrfHash) {
                        d[csrfName] = csrfHash;
                    }
                    d.channel_group = $('#filter_channel_group').val();
                    d.channel_id = $('#filter_channel_id').val();
                    d.provider = $('#filter_provider').val();
                    d.status = $('#filter_status').val();
                }
            },
            order: [[1, 'asc']],
            language: {
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No settings found.</div>'
            }
        });

    function fetchOptions(group, external_id, updateProvider) {
        const csrfName = $('meta[name="csrf-token-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        const tokenObj = {};
        if (csrfName) tokenObj[csrfName] = csrfHash;

        if (!group) return;

        if (updateProvider) {
            $('#c_externalIdDefault').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
        }
        $('#ref_cashoutChannelId').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

        $.ajax({
            url: "<?= base_url('external/cashout/get-filter-options') ?>",
            type: "POST",
            data: Object.assign({ group: group, external_id: external_id }, tokenObj),
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let providerOptions = '<option value="" selected disabled>Select external ID</option>';
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#c_externalIdDefault').html(providerOptions).prop('disabled', false).trigger('change.select2');
                }

                let channelOptions = '<option value="" selected disabled>Select channel ID</option>';
                data.channels.forEach(function(item) {
                    channelOptions += `<option value="${item}">${item}</option>`;
                });
                $('#ref_cashoutChannelId').html(channelOptions).prop('disabled', false).trigger('change.select2');
            },
            error: function() {
                if (updateProvider) $('#c_externalIdDefault').prop('disabled', false).html('<option value="" selected disabled>Select external ID</option>').trigger('change.select2');
                $('#ref_cashoutChannelId').prop('disabled', false).html('<option value="" selected disabled>Select channel ID</option>').trigger('change.select2');
            }
        });
    }

    function onGroupChange() {
        const group = $(this).val();
        $('#c_externalIdDefault').val('').trigger('change.select2');
        $('#ref_cashoutChannelId').val('').trigger('change.select2');
        fetchOptions(group, '', true);
    }

    function onExternalChange() {
        const group = $('#c_cashoutChannelGroup').val();
        const external_id = $(this).val();
        $('#ref_cashoutChannelId').val('').trigger('change.select2');
        fetchOptions(group, external_id, false);
    }

    $('#c_cashoutChannelGroup').on('change', onGroupChange);
    $('#c_externalIdDefault').on('change', onExternalChange);

    // Edit Button Handler
    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#feeModalTitle').html('Edit Cashout Fee Configuration');
        $('#feeModalSubtitle').text('Update and modify existing channel fee configuration');
        $('#feeForm').attr('action', `<?= base_url('merchant/setting-cashout-fee/edit/' . $merchant_id) ?>/${d.id}`);
        
        // Prevent trigger during population
        $('#c_cashoutChannelGroup').off('change', onGroupChange);
        $('#c_externalIdDefault').off('change', onExternalChange);
        
        $('#c_cashoutChannelGroup').empty().append(`<option value="${d.group}" selected>${d.group}</option>`).trigger('change');
        $('#c_externalIdDefault').empty().append(`<option value="${d.externalid}" selected>${d.externalid}</option>`).trigger('change');
        
        // Manual populate for channel ID
        const $channelId = $('#ref_cashoutChannelId');
        $channelId.empty().append(`<option value="${d.channelid}" selected>${d.channelid}</option>`).prop('disabled', false);

        // Re-enable trigger
        $('#c_cashoutChannelGroup').on('change', onGroupChange);
        $('#c_externalIdDefault').on('change', onExternalChange);

        $('#c_feeType').val(d.feetype).trigger('change');
        $('#c_fee').val(Math.floor(parseFloat(d.fee)));
        $('#c_feePercetange').val(d.feepercentage);
        $('#c_amountMin').val(Math.floor(parseFloat(d.min)));
        $('#c_amountMax').val(Math.floor(parseFloat(d.max)));
        $('#c_status').val(d.status).trigger('change');
    });

    $('.add-btn').click(function() {
        $('#feeModalTitle').html('Add New Cashout Fee');
        $('#feeModalSubtitle').text('Create and register new data record');
        $('#feeForm').attr('action', `<?= base_url('merchant/setting-cashout-fee/create'); ?>`);
        $('#feeForm')[0].reset();
        
        // Reset dropdowns manually
        $('#c_cashoutChannelGroup, #c_externalIdDefault, #c_feeType, #c_status').val('').trigger('change');
        $('#ref_cashoutChannelId').prop('disabled', true).empty().append('<option disabled selected>Select channel ID</option>');
    });

    $(document).on('click', '.delete-fee-btn', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        Swal.fire({
            title: 'Delete Fee Setting',
            text: 'Are you sure you want to delete this cashout fee configuration? This action cannot be undone.',
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

    // Global search
    $('#dt-search').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // ── More Filters dropdown ──
    var $moreBtn   = $('#feeMoreFiltersBtn');
    var $morePanel = $('#feeMoreFiltersPanel');
    var $moreClose = $('#feeMoreFiltersClose');
    var $moreApply = $('#feeMoreApply');
    var $moreClear = $('#feeMoreClear');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        var isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $moreClose.on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        }
    });

    $('#feeMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
        $(this).select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('body'),
            minimumResultsForSearch: 0
        });
    });

    function updateFilterBadge() {
        let count = 0;
        $('.filter-select').each(function() {
            if ($(this).val()) count++;
        });
        const $badge = $('#feeFilterBadge');
        if (count > 0) {
            $badge.text(count).show();
            $moreBtn.addClass('dt-more-filters-active');
        } else {
            $badge.hide();
            $moreBtn.removeClass('dt-more-filters-active');
        }
    }

    $moreApply.on('click', function() {
        updateFilterBadge();
        table.ajax.reload(null, false);
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $moreClear.on('click', function() {
        $('.filter-select').val('').trigger('change.select2');
        fetchFilterOptions('', '', true);
        updateFilterBadge();
        table.ajax.reload(null, false);
    });

    // Cascading logic for Advanced Filters
    $('#filter_channel_group').on('change', function() {
        const group = $(this).val();
        $('#filter_provider').val('').trigger('change.select2');
        $('#filter_channel_id').val('').trigger('change.select2');
        fetchFilterOptions(group, '', true);
    });

    $('#filter_provider').on('change', function() {
        const group = $('#filter_channel_group').val();
        const external_id = $(this).val();
        $('#filter_channel_id').val('').trigger('change.select2');
        fetchFilterOptions(group, external_id, false);
    });

    function fetchFilterOptions(group, external_id, updateProvider) {
        const csrfName = $('meta[name="csrf-token-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        const tokenObj = {};
        if (csrfName) tokenObj[csrfName] = csrfHash;

        if (updateProvider) {
            $('#filter_provider').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
        }
        $('#filter_channel_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

        $.ajax({
            url: "<?= base_url('external/cashout/get-filter-options') ?>",
            type: "POST",
            data: Object.assign({ group: group, external_id: external_id }, tokenObj),
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let providerOptions = '<option value="">All Providers</option>';
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#filter_provider').html(providerOptions).prop('disabled', false).trigger('change.select2');
                }

                let channelOptions = '<option value="">All Channel IDs</option>';
                data.channels.forEach(function(item) {
                    channelOptions += `<option value="${item}">${item}</option>`;
                });
                $('#filter_channel_id').html(channelOptions).prop('disabled', false).trigger('change.select2');
            },
            error: function() {
                if (updateProvider) $('#filter_provider').prop('disabled', false).html('<option value="">All Providers</option>').trigger('change.select2');
                $('#filter_channel_id').prop('disabled', false).html('<option value="">All Channel IDs</option>').trigger('change.select2');
            }
        });
    }

    // Bulk modal dynamic logic
    $('#bulk_c_cashoutChannelGroup').on('change', function() {
        const group = $(this).val();
        const csrfName = $('meta[name="csrf-token-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        const tokenVal = $('input[name="' + csrfName + '"]').val() || csrfHash;
        
        $('#bulk_c_externalIdDefault').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

        $.ajax({
            url: "<?= base_url('channel/get-master-filter-options') ?>",
            type: "POST",
            data: { type: 'cashout', group: group, [csrfName]: tokenVal },
            dataType: "json",
            success: function(data) {
                let providerOptions = '<option value="" selected disabled>Select external ID</option>';
                data.providers.forEach(function(item) {
                    providerOptions += `<option value="${item}">${item}</option>`;
                });
                $('#bulk_c_externalIdDefault').html(providerOptions).prop('disabled', false).trigger('change.select2');
            },
            error: function() {
                $('#bulk_c_externalIdDefault').prop('disabled', false).html('<option value="" selected disabled>Select external ID</option>').trigger('change.select2');
            }
        });
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
