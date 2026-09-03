<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700">Merchant Supervisor</h4>
            <p class="dt-page-subtitle text-muted">Manage and organize merchant supervisors and their assigned merchants.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary toggle-guide-btn" id="toggleGuideBtn" style="background-color: #6f42c1; border-color: #6f42c1; color: #fff;">
                <i class="fas fa-book-open mr-2"></i> Instructions Guide
            </button>
            <button type="button" class="btn-dt-action btn-dt-action-success border-0 d-flex align-items-center shadow-sm" data-toggle="modal" data-target="#addMerchantSpv">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Supervisor
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Supervisor Management Overview</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Oversee and configure merchant supervisors, credentials, and merchant linkages.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Live Search</div>
                <p class="drawer-card-text">Filter supervisor records by name, username, or email.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-plus-circle text-primary mr-2"></i> Add Supervisor</div>
                <p class="drawer-card-text">Create new supervisor credentials and select supervised merchants.</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <form id="spv_search_form" method="post" action="<?= base_url('merchant/supervisor'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="dt-toolbar-left flex-grow-1" style="min-width: 280px;">
                    <div class="dt-search-wrapper">
                        <i class="fas fa-search dt-search-icon"></i>
                        <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search by name, username, and email..." value="<?= $this->session->userdata('search_spv'); ?>">
                    </div>
                </div>
                <div class="dt-toolbar-right d-flex align-items-center gap-2">
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <?php 
                            $extra_active = 0;
                            if (!empty($this->session->userdata('search_spv_status'))) $extra_active++;
                            if (!empty($this->session->userdata('search_spv_date_from')) || !empty($this->session->userdata('search_spv_date_to'))) $extra_active++;
                        ?>
                        <button type="button" id="spvMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <div class="dt-more-panel" id="spvMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('merchant/supervisor/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Registration Date</label>
                                    <div class="premium-picker">
                                        <input type="date" name="search_spv_date_from" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_date_from'); ?>">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_spv_date_to" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_date_to'); ?>">
                                    </div>
                                </div>

                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Account Status</label>
                                    <select name="search_spv_status" class="dt-more-select">
                                        <option value="">All Account Statuses</option>
                                        <option value="Active" <?= $this->session->userdata('search_spv_status') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Pending" <?= $this->session->userdata('search_spv_status') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Blocked" <?= $this->session->userdata('search_spv_status') == 'Blocked' ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= $this->session->userdata('search_spv_status') == 'Freeze' ? 'selected' : ''; ?>>Freeze</option>
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

        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="merchantSpvTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="40" class="text-center">No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th class="text-center">Registered</th>
                        <th width="60" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('merchantspv/partials/modal_merchantspv'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/merchantspv_index.js'); ?>"></script>
