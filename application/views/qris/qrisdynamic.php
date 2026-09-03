<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">QRIS Dynamic Transactions</h4>
            <p class="dt-page-subtitle">Monitor and manage all dynamic QRIS activities in real-time.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> QRIS Dynamic Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Monitor dynamically generated QRIS transactions in real-time.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-qrcode text-primary mr-2"></i> Real-Time Monitor</div>
                <p class="drawer-card-text">Monitor generated QR codes and check transaction status.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-code text-primary mr-2"></i> External Log Inspector</div>
                <p class="drawer-card-text">Inspect external JSON logs from provider APIs.</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <?php
            $extra_active = 0;
            if ($this->session->userdata('search_qrisdynamic_date1') || $this->session->userdata('search_qrisdynamic_date2')) $extra_active++;
            if ($this->session->userdata('search_qrisdynamic_name')) $extra_active++;
            if ($this->session->userdata('search_qrisdynamic_status')) $extra_active++;
            if ($this->session->userdata('search_qrisdynamic_reff')) $extra_active++;
            if ($this->session->userdata('search_qrisdynamic_channel')) $extra_active++;
            if ($this->session->userdata('search_qrisdynamic_external')) $extra_active++;
        ?>

        <form id="qris_dynamic_form" method="post" action="<?= base_url('qris/dynamic'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_qd_search = $this->session->userdata('last_dt_search_qrisdynamic'); ?>
                    <input type="text" id="qrisDynamicGlobalSearch" class="dt-search-input" placeholder="Search by Merchant, ID, or Reference..." value="<?= htmlspecialchars($active_qd_search); ?>">
                </div>

                <div class="dt-toolbar-filters">
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <button type="button" id="qrisMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <div class="dt-more-panel" id="qrisMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('qris/dynamic/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Date Range</label>
                                    <div class="premium-picker">
                                        <input type="date" name="search_date_transaction1_qd" class="dt-chip-input" value="<?= $this->session->userdata('search_qrisdynamic_date1'); ?>">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_transaction2_qd" class="dt-chip-input" value="<?= $this->session->userdata('search_qrisdynamic_date2'); ?>">
                                    </div>
                                </div>

                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_name_merchant_qd" class="dt-more-select">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $m): ?>
                                            <option value="<?= $m->c_name; ?>" <?= ($this->session->userdata('search_qrisdynamic_name') == $m->c_name) ? 'selected' : ''; ?>><?= $m->c_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                    <select name="search_status_transaction_qd" class="dt-more-select">
                                        <option value="">All Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_qrisdynamic_status') == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Paid" <?= ($this->session->userdata('search_qrisdynamic_status') == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                        <option value="Failed" <?= ($this->session->userdata('search_qrisdynamic_status') == 'Failed') ? 'selected' : ''; ?>>Failed</option>
                                        <option value="Expired" <?= ($this->session->userdata('search_qrisdynamic_status') == 'Expired') ? 'selected' : ''; ?>>Expired</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="qrisMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">CANCEL</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table dt-table mb-0" id="qrisDynamicTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>DATE REQUEST</th>
                        <th>MERCHANT INFO</th>
                        <th>SUB-MERCHANT INFO</th>
                        <th>Merchant Trans ID</th>
                        <th>Channel</th>
                        <th>External ID</th>
                        <th>Amount</th>
                        <th>EXPIRED</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('qris/partials/modal_qrisdynamic'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/qrisdynamic_list.js'); ?>"></script>
