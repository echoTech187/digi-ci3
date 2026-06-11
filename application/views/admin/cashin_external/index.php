<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashin External Merchant</h4>
            <p class="dt-page-subtitle">Centralized configuration for all merchant cashin channels</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#globalUpdateModal">
                <i class="fas fa-globe mr-1 mr-2"></i> Edit Mapping
            </button>
            <a href="<?= base_url('external/cashin/create'); ?>" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Mapping
            </a>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Cashin Mapping Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This dashboard allows you to manage external cashin (deposit) channel configurations for all merchants.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-globe text-primary mr-2"></i> Edit Mapping (Bulk Update)</div>
                <p class="drawer-card-text">Update the cashin provider for multiple merchants at once. Choose to update a channel group globally or for a specific merchant.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sliders-h text-primary mr-2"></i> Filtering & Search</div>
                <p class="drawer-card-text">Quickly locate configuration rows using global search, or narrow them down by Channel Group, Channel ID, Provider, or Status.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-percent text-primary mr-2"></i> Fee Structures</div>
                <p class="drawer-card-text">View active fee types (Fixed, Percentage, or Both) as well as the exact fee value applied to transactions.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Settlement & Limits</div>
                <p class="drawer-card-text">Check the settlement interval (in days) and the minimum/maximum transactional limits configured for each mapping.</p>
            </div>
        </div>
    </div>


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search merchant or channel..." value="<?= htmlspecialchars($this->session->userdata('search_external_cashin')); ?>">
            </div>

            <!-- RIGHT: Filters & Actions -->
            <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="cashinMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="cashinFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="cashinMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="cashinMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Merchant -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                <select id="filter_merchant" class="dt-more-select filter-select">
                                    <option value="">All Merchants</option>
                                    <?php foreach ($merchants as $m): ?>
                                        <option value="<?= $m->id ?>"><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

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
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> External ID Default</label>
                                <select id="filter_provider" class="dt-more-select filter-select">
                                    <option value="">All External IDs</option>
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
                            <button type="button" id="cashinMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="cashinMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
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
                        <th>Merchant</th>
                        <th>Channel Config</th>
                        <th>Fee Details</th>
                        <th class="text-center">Interval</th>
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
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="globalUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
            <form id="globalUpdateForm" action="<?= base_url('external/cashin/bulk-update'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="d-flex align-items-start p-3 mb-4" style="background:rgba(78,115,223,0.08);border:1px solid rgba(78,115,223,0.15);border-radius:12px;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:36px;height:36px;"><i class="fas fa-globe"></i></div>
                        <div>
                            <h6 class="fw-bold text-primary mb-1" style="font-size:13px;">Global Cash In Update Guide</h6>
                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">This operation switches the active cashin provider for <strong>all merchants at once</strong>. Choose between group-based or individual channel updates. Changes take effect immediately after confirmation.</p>
                        </div>
                    </div>
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
                            <option value="">Select merchant</option>
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
                                    <option value="">Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">External ID Default (Optional)</label>
                                <select class="form-control select2" name="current_externalId" id="global_current_external" disabled>
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="current_cashinChannelId" id="global_current_channel" disabled>
                                    <option value="">All Channel IDs</option>
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
                                    <option value="">Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New External ID Default (Optional)</label>
                                <select class="form-control select2" name="new_externalId" id="global_new_external" disabled>
                                    <option value="">Don't Update (Keep Original)</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="new_cashinChannelId" id="global_new_channel" disabled>
                                    <option value="">Don't Update (Keep Original)</option>
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


<script>
$(document).ready(function() {
    // Drawer Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    $('#feeModal, #globalUpdateModal').on('shown.bs.modal', function() {
        var $modal = $(this);
        $modal.find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({ 
                dropdownParent: $('body'),
                width: '100%',
                minimumResultsForSearch: 0
            });
        });
    });

    $('#globalUpdateModal').on('hidden.bs.modal', function() {
        $(this).find('form').each(function() {
            this.reset();
        });
        $(this).find('.select2').val('').trigger('change');
    });

    var table = initServerDataTable("#cashinTable", "<?= base_url('external/cashin/list') ?>", [
        { "data": "no", "className": "ps-4 text-muted small" },
        { 
            "data": "merchant_name",
            "render": function(data, type, row) {
                return `<div class="fw-bold text-dark">${data}</div><small class="text-muted">ID: ${row.ref_merchantId}</small>`;
            }
        },
        { 
            "data": "c_cashinChannelGroup",
            "render": function(data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">${data}</span>
                        <span class="text-muted small">Channel: <code class="text-primary">${row.ref_cashinChannelId}</code></span>
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
                            <li><a class="dropdown-item" href="<?= base_url('external/cashin/edit') ?>/${row.id}"><i class="fas fa-edit text-primary mr-2"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger delete-btn" href="javascript:void(0)" data-href="<?= base_url('external/cashin/delete') ?>/${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ], {
        ajax: {
            url: "<?= base_url('external/cashin/list') ?>",
            type: "POST",
            data: function (d) {
                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                if (csrfName && csrfHash) {
                    d[csrfName] = csrfHash;
                }
                d.merchant_id = $('#filter_merchant').val();
                d.channel_group = $('#filter_channel_group').val();
                d.channel_id = $('#filter_channel_id').val();
                d.provider = $('#filter_provider').val();
                d.status = $('#filter_status').val();
                d.search_channel = $('#dt-search').val() || '';
            }
        }
    });

    const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    let origGroups = '';
    let origExternals = '';

    $(document).ready(function() {
        origGroups = $('#global_current_group').html();
        origExternals = $('#global_current_external').html();
    });

    // Update Scope Radio Toggle
    $('input[name="update_type"]').on('change', function() {
        if (this.value === 'merchant') {
            $('#merchantSelectGroup').slideDown();
        } else {
            $('#merchantSelectGroup').slideUp();
            $('#global_merchant').val('').trigger('change.select2');
            
            // Restore ALL options for Group and External ID
            if (origGroups) {
                $('#global_current_group').html(origGroups).val('').trigger('change.select2');
                $('#global_current_external').html(origExternals).val('').trigger('change.select2');
            }
        }
        
        // Always reset other selects when switching tabs
        $('#global_current_channel').val('').trigger('change.select2');
        $('#global_new_group').val('').trigger('change.select2');
        $('#global_new_external').val('').trigger('change.select2');
        $('#global_new_channel').val('').trigger('change.select2');
    });

    $('#global_merchant').on('change', function() {
        const merchantId = $(this).val();
        if (!merchantId) return;
        const tokenVal = $('input[name="' + csrfName + '"]').val() || csrfHash;

        $.ajax({
            url: "<?= base_url('external/cashin/get-merchant-mappings') ?>",
            type: "POST",
            data: { 
                merchant_id: merchantId,
                [csrfName]: tokenVal 
            },
            dataType: "json",
            success: function(data) {
                // Update current group
                let groupOptions = '<option value="">Select group</option>';
                data.groups.forEach(function(item) {
                    groupOptions += `<option value="${item}">${item}</option>`;
                });
                $('#global_current_group').html(groupOptions).trigger('change');

                // Update current external
                let extOptions = '<option value="" selected>All External IDs</option>';
                data.providers.forEach(function(item) {
                    extOptions += `<option value="${item}">${item}</option>`;
                });
                $('#global_current_external').html(extOptions).trigger('change');
            }
        });
    });

    // Dynamic population for Current Configuration
    $('#global_current_group').on('change', function() {
        const group = $(this).val();
        if (!group) {
            $('#global_current_external').val('').prop('disabled', true).trigger('change.select2');
            $('#global_current_channel').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#global_current_external').val('').trigger('change.select2');
        $('#global_current_channel').val('').trigger('change.select2');
        fetchCurrentConfigOptions(group, '', true);
    });

    $('#global_current_external').on('change', function() {
        const group = $('#global_current_group').val();
        const external_id = $(this).val();
        if (!external_id) {
            $('#global_current_channel').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#global_current_channel').val('').trigger('change.select2');
        fetchCurrentConfigOptions(group, external_id, false);
    });

    function fetchCurrentConfigOptions(group, external_id, updateProvider) {
        const tokenVal = $('input[name="' + csrfName + '"]').val() || csrfHash;

        if (!group) return;

        if (updateProvider) {
            $('#global_current_external').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
            $('#global_current_channel').prop('disabled', true).html('<option value="">All Channel IDs</option>').trigger('change.select2');
        } else {
            $('#global_current_channel').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
        }

        $.ajax({
            url: "<?= base_url('external/cashin/get-filter-options') ?>",
            type: "POST",
            data: { group: group, external_id: external_id, [csrfName]: tokenVal },
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let providerOptions = '<option value="">All External IDs</option>';
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#global_current_external').html(providerOptions).prop('disabled', false).trigger('change.select2');
                } else {
                    let channelOptions = '<option value="">All Channel IDs</option>';
                    data.channels.forEach(function(item) {
                        channelOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#global_current_channel').html(channelOptions).prop('disabled', false).trigger('change.select2');
                }
            },
            error: function() {
                if (updateProvider) {
                    $('#global_current_external').prop('disabled', false).html('<option value="">All External IDs</option>').trigger('change.select2');
                } else {
                    $('#global_current_channel').prop('disabled', false).html('<option value="">All Channel IDs</option>').trigger('change.select2');
                }
            }
        });
    }

    // Dynamic population for New Configuration
    $('#global_new_group').on('change', function() {
        const group = $(this).val();
        if (!group) {
            $('#global_new_external').val('').prop('disabled', true).trigger('change.select2');
            $('#global_new_channel').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#global_new_external').val('').trigger('change.select2');
        $('#global_new_channel').val('').trigger('change.select2');
        fetchNewConfigOptions(group, '', true);
    });

    $('#global_new_external').on('change', function() {
        const group = $('#global_new_group').val();
        const external_id = $(this).val();
        if (!external_id) {
            $('#global_new_channel').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#global_new_channel').val('').trigger('change.select2');
        fetchNewConfigOptions(group, external_id, false);
    });

    function fetchNewConfigOptions(group, external_id, updateProvider) {
        const tokenVal = $('input[name="' + csrfName + '"]').val() || csrfHash;

        if (!group) return;

        if (updateProvider) {
            $('#global_new_external').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
            $('#global_new_channel').prop('disabled', true).html('<option value="">Don\'t Update (Keep Original)</option>').trigger('change.select2');
        } else {
            $('#global_new_channel').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
        }

        $.ajax({
            url: "<?= base_url('external/cashin/get-filter-options') ?>",
            type: "POST",
            data: { group: group, external_id: external_id, [csrfName]: tokenVal },
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let providerOptions = '<option value="">Don\'t Update (Keep Original)</option>';
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#global_new_external').html(providerOptions).prop('disabled', false).trigger('change.select2');
                } else {
                    let channelOptions = '<option value="">Don\'t Update (Keep Original)</option>';
                    data.channels.forEach(function(item) {
                        channelOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#global_new_channel').html(channelOptions).prop('disabled', false).trigger('change.select2');
                }
            },
            error: function() {
                if (updateProvider) {
                    $('#global_new_external').prop('disabled', false).html('<option value="">Don\'t Update (Keep Original)</option>').trigger('change.select2');
                } else {
                    $('#global_new_channel').prop('disabled', false).html('<option value="">Don\'t Update (Keep Original)</option>').trigger('change.select2');
                }
            }
        });
    }

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

    $('#dt-search').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // ── More Filters dropdown ──
    var $moreBtn   = $('#cashinMoreFiltersBtn');
    var $morePanel = $('#cashinMoreFiltersPanel');
    var $moreClose = $('#cashinMoreFiltersClose');
    var $moreApply = $('#cashinMoreApply');
    var $moreClear = $('#cashinMoreClear');

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

    // Select2 for ALL selects inside the More Filters panel
    $('#cashinMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
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
        const $badge = $('#cashinFilterBadge');
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
        const tokenVal = $('input[name="' + csrfName + '"]').val() || csrfHash;
        
        if (updateProvider) {
            $('#filter_provider').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
        }
        $('#filter_channel_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

        $.ajax({
            url: "<?= base_url('external/cashin/get-filter-options') ?>",
            type: "POST",
            data: { group: group, external_id: external_id, [csrfName]: tokenVal },
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let providerOptions = '<option value="">All External IDs</option>';
                    const currentProvider = $('#filter_provider').val();
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#filter_provider').html(providerOptions).prop('disabled', false).trigger('change.select2');
                }

                let channelOptions = '<option value="">All Channel IDs</option>';
                const currentChannel = $('#filter_channel_id').val();
                data.channels.forEach(function(item) {
                    channelOptions += `<option value="${item}">${item}</option>`;
                });
                $('#filter_channel_id').html(channelOptions).prop('disabled', false).trigger('change.select2');
            },
            error: function() {
                if (updateProvider) $('#filter_provider').prop('disabled', false).html('<option value="">All External IDs</option>').trigger('change.select2');
                $('#filter_channel_id').prop('disabled', false).html('<option value="">All Channel IDs</option>').trigger('change.select2');
            }
        });
    }
});
</script>
