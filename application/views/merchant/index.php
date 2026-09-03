<div>
    <!-- Page Header -->
    <div class="dt-page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="dt-page-title">Merchant Management</h1>
            <p class="dt-page-subtitle">View and manage all registered merchants and their balances.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('merchant/manage/create'); ?>" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Merchant
            </a>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Merchant Management Overview</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Oversee all registered merchants, track their API statuses, and manage their fund balances with absolute precision.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Global Search</div>
                <p class="drawer-card-text">Find any merchant instantly by Name, ID, Business ID, or Email.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Balance Controls</div>
                <p class="drawer-card-text">Credit (Add) or Debit (Deduct) a merchant's balance directly from the actions menu.</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card dt-card border-0 shadow-sm">
        <form id="merchant_search_form" method="post" action="<?= base_url('merchant/manage'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar">
                <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="merchantGlobalSearch" class="dt-search-input" placeholder="Search by name, ID, email, or Business ID..." value="<?= $this->session->userdata('search_merchant'); ?>">
                </div>

                <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <button type="button" id="merchantMoreFiltersBtn" class="dt-more-filters-btn">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <div class="dt-more-panel" id="merchantMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <button type="button" class="close" id="merchantMoreFiltersClose">&times;</button>
                            </div>
                            <div class="dt-more-panel-body">
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar mr-1 mr-2"></i> Date Range</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="search_merchant_date_from" class="form-control form-control-sm" value="<?= $this->session->userdata('search_merchant_date_from'); ?>">
                                        <input type="date" name="search_merchant_date_to" class="form-control form-control-sm" value="<?= $this->session->userdata('search_merchant_date_to'); ?>">
                                    </div>
                                </div>
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-shield-alt mr-1 mr-2"></i> Account Status</label>
                                    <select name="search_merchant_status" class="form-control form-control-sm">
                                        <option value="">All Statuses</option>
                                        <option value="Active" <?= $this->session->userdata('search_merchant_status') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Pending" <?= $this->session->userdata('search_merchant_status') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Blocked" <?= $this->session->userdata('search_merchant_status') == 'Blocked' ? 'selected' : ''; ?>>Blocked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="dt-more-panel-footer">
                                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search mr-1"></i> Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Main Merchant Table -->
        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="merchantTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4">NO</th>
                        <th>MERCHANT ID</th>
                        <th>NAME / EMAIL</th>
                        <th>BALANCES (TOTAL / HOLD / AVAIL)</th>
                        <th class="text-center">REGISTERED</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">OPENAPI</th>
                        <th class="text-center pe-4" style="width: 80px;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('merchant/partials/modal_merchant_actions'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/merchant_index.js'); ?>"></script>
