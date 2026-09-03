<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashin Fee Settings</h4>
            <p class="dt-page-subtitle">Configure payment channel fees for <strong><?= $merchant_name ?></strong></p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary toggle-guide-btn" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> Instructions Guide
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-success add-btn" data-toggle="modal" data-target="#feeModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Channel
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-info" data-toggle="modal" data-target="#bulkModal">
                <i class="fas fa-layer-group mr-1 mr-2"></i> Bulk Add
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Cashin Management Overview</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Configure deposit gateway fees for <strong><?= $merchant_name ?></strong>.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Live Search</div>
                <p class="drawer-card-text">Filter table records by channel name, external ID, or fee type.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-plus-circle text-primary mr-2"></i> Add Channel</div>
                <p class="drawer-card-text">Configure a single specific payment channel with dedicated fee structures.</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search setting...">
            </div>

            <div class="dt-toolbar-filters d-flex align-items-center" style="gap: 10px;">
                <div class="dt-filter-group dt-more-filters-wrapper mb-0">
                    <button type="button" id="feeMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="feeFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <div class="dt-more-panel" id="feeMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="feeMoreClear" class="dt-more-clear">Clear All</a>
                        </div>
                        <div class="dt-more-panel-body">
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-layer-group mr-1 mr-2"></i> Channel Group</label>
                                <select id="filter_channel_group" class="dt-more-select filter-select">
                                    <option value="">All Groups</option>
                                    <?php foreach ($channel_groups as $cg): ?>
                                        <option value="<?= $cg->c_channelGroup ?>"><?= $cg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> Provider</label>
                                <select id="filter_provider" class="dt-more-select filter-select">
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $prd): ?>
                                        <option value="<?= $prd->c_externalIdDefault ?>"><?= $prd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
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
                            <button type="button" class="btn-dt-action btn-dt-action-primary w-100" id="feeMoreApply">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="cashinFeeTable" style="width: 100%;">
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
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('merchant/partials/modal_setting_cashin_fee'); ?>

<!-- ── Include JavaScript Assets ── -->
<script>
    window.CURRENT_MERCHANT_ID = "<?= $merchant_id ?>";
</script>
<script src="<?= base_url('assets/js/merchant_setting_cashin_fee.js'); ?>"></script>
