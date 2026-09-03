<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage payout channels, external provider links, and transaction fee logic.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center" data-toggle="modal" data-target="#addChanelModal">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Channel
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Cash Out Channels Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Manage and configure outbound payout channels for external bank transfer or provider endpoints.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-arrow-alt-circle-up text-primary mr-2"></i> Disbursement Setup</div>
                <p class="drawer-card-text">Configure outbound routes such as BI-FAST, online bank transfer, and e-wallet transfers.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-server text-primary mr-2"></i> Provider Routing</div>
                <p class="drawer-card-text">Define default external provider codes and identifiers responsible for executing payouts upstream.</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="cashoutGlobalSearch" class="dt-search-input" placeholder="Search by Channel, Provider, or Fee..." value="<?= $this->session->userdata('search_channel_out'); ?>">
            </div>

            <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="cashoutMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="cashoutFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <div class="dt-more-panel" id="cashoutMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="cashoutMoreClear" class="dt-more-clear">Clear All</a>
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
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> External ID Default</label>
                                <select id="filter_external_id" class="dt-more-select filter-select">
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $prd): ?>
                                        <option value="<?= $prd->c_externalIdDefault ?>"><?= $prd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="dt-more-panel-footer">
                            <button type="button" id="cashoutMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm w-100">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="cashoutTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th>CHANNEL ID</th>
                        <th>GROUP</th>
                        <th>DESCRIPTION</th>
                        <th>PROVIDER</th>
                        <th>FEE TYPE</th>
                        <th>FEE (FIXED)</th>
                        <th>FEE (%)</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('chanel/partials/modal_cashout'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/channel_cashout.js'); ?>"></script>
