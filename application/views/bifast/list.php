<!-- Begin Page Content -->
<div>
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> BI-FAST Transactions Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Track outbound real-time bank transfers and disbursements through the BI-FAST network.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-university text-primary mr-2"></i> Destination Details</div>
                <p class="drawer-card-text">Shows recipient bank, account number, account holder name, and transfer amount.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-exchange-alt text-primary mr-2"></i> Status Flow</div>
                <p class="drawer-card-text">Track state transitions from Pending/In Process to Success, Failed, or Reversed.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search-plus text-primary mr-2"></i> Re-inquiry</div>
                <p class="drawer-card-text">Perform status checks directly against the bank gateway to resolve hanging transactions.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">BI-FAST Transactions</h4>
            <p class="dt-page-subtitle">Monitor and manage all disbursement activities through BI-FAST.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card dt-card border-0 shadow-sm">
        <form id="bifast_form" method="post" action="<?= base_url('finance/bi-fast'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_bifast_search = $this->session->userdata('last_dt_search_bifast') ?: ''; ?>
                    <input type="text" id="bifastGlobalSearch" class="dt-search-input" placeholder="Search by Trans ID, Invoice, Account No, or Beneficiary Name..." value="<?= $active_bifast_search; ?>">
                </div>

                <div class="dt-toolbar-filters">
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="bifastMoreFiltersBtn" class="dt-more-filters-btn">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <div class="dt-more-panel" id="bifastMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <button type="button" class="close" id="bifastMoreFiltersClose">&times;</button>
                            </div>

                            <div class="dt-more-panel-body">
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar mr-1 mr-2"></i> Date Range</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="search_bifast_date1" class="form-control form-control-sm" value="<?= $this->session->userdata('search_bifast_date1'); ?>">
                                        <input type="date" name="search_bifast_date2" class="form-control form-control-sm" value="<?= $this->session->userdata('search_bifast_date2'); ?>">
                                    </div>
                                </div>
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_bifast_name" class="bifast-select2 form-control">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $m): ?>
                                            <option value="<?= $m->id ?>" <?= $this->session->userdata('search_bifast_name') == $m->id ? 'selected' : ''; ?>><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                    <select name="search_bifast_status" class="bifast-select2 form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Success" <?= $this->session->userdata('search_bifast_status') == 'Success' ? 'selected' : ''; ?>>Success</option>
                                        <option value="Pending" <?= $this->session->userdata('search_bifast_status') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Failed" <?= $this->session->userdata('search_bifast_status') == 'Failed' ? 'selected' : ''; ?>>Failed</option>
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

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="bifastTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>DATETIME</th>
                        <th>MERCHANT</th>
                        <th>TRANS ID</th>
                        <th>INVOICE</th>
                        <th>PROVIDER</th>
                        <th>CHANNEL</th>
                        <th>ACCOUNT NO</th>
                        <th>BENEFICIARY</th>
                        <th>AMOUNT</th>
                        <th>FEE</th>
                        <th>STATUS</th>
                        <th>RESPONSE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('bifast/partials/modal_detail_external'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/bifast_list.js'); ?>"></script>
