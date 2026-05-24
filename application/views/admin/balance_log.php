<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700"><?= $title; ?></h4>
            <p class="dt-page-subtitle text-muted">Monitoring and auditing merchant balance configurations and hold adjustments.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Balance Log Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This ledger monitors and audits changes or settlements to merchant balance configurations.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-clipboard-list text-primary mr-2"></i> Audit Trails</div>
                <p class="drawer-card-text">Track the exact timestamp and merchant detail of when adjustments or balance settlements occurred.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Search Filter</div>
                <p class="drawer-card-text">Search adjustment records instantly by entering the Merchant Name, ID, or other keywords into the search box.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-coins text-primary mr-2"></i> Balance Settled</div>
                <p class="drawer-card-text">Displays the exact amount added to the merchant's available balance in Rupiah (IDR) during the transaction or adjustment event.</p>
            </div>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4 d-none">
        <!-- Total Logs -->
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL LOGS</div>
                <div class="dt-summary-value"><?= number_format($total_logs, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-history mr-1 mr-2"></i> Total adjustment records</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>

        <!-- Active Merchants -->
        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">ACTIVE MERCHANTS</div>
                <div class="dt-summary-value"><?= number_format($total_merchants, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-users-cog mr-1 mr-2"></i> Unique merchants adjusted</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <!-- Total Settled -->
        <div class="dt-summary-card dt-summary-orange">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL SETTLED</div>
                <div class="dt-summary-value">Rp <?= number_format($total_settled, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-wallet mr-1 mr-2"></i> Cumulative balance added</div>
            </div>
            <div class="dt-summary-icon dt-icon-orange">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Avg. Adjustment -->
        <div class="dt-summary-card dt-summary-purple">
            <div class="dt-summary-body">
                <div class="dt-summary-label">AVG. ADJUSTMENT</div>
                <div class="dt-summary-value">Rp <?= number_format($avg_settled, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-chart-line mr-1 mr-2"></i> Average per transaction</div>
            </div>
            <div class="dt-summary-icon dt-icon-purple">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <form id="balanceLogForm" method="post" action="<?= base_url('report/balance-log'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar py-3 px-4">
                <div class="dt-toolbar-left">
                    <div class="dt-search-wrapper">
                        <i class="fas fa-search dt-search-icon"></i>
                        <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search by merchant name, amount...">
                    </div>
                </div>
                
                <div class="dt-toolbar-filters">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <?php 
                        $extra_active = 0;
                        if ($search_merchant) $extra_active++;
                        if ($search_date_from || $search_date_to) $extra_active++;
                        ?>
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" class="btn-dt-chip-action dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>" id="dt-more-filters-btn">
                            <i class="fas fa-sliders-h"></i> <span class="d-none d-md-block">Filter</span>
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>
                        
                        <!-- The Slide-Down Panel -->
                        <div class="dt-more-panel" id="dt-more-filters-panel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advance Filter</span>
                                <a href="<?= base_url('report/balance-log/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_merchant_balance_log" class="dt-more-select balance-log-select2 text-primary-custom fw-bold">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $m): ?>
                                            <option value="<?= $m->id; ?>" <?= ($m->id == $search_merchant) ? 'selected' : ''; ?>>
                                                [<?= $m->id; ?>] <?= $m->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_balance_log" class="dt-chip-input" value="<?= $search_date_from; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_balance_log_to" class="dt-chip-input" value="<?= $search_date_to; ?>" title="Date To">
                                    </div>
                                </div>
                            </div>
                            <div class="dt-more-panel-footer">
                                <button type="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" class="btn-dt-cancel btn-dt-secondary" id="dt-panel-close">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="balanceLogTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>DATE</th>
                        <th>MERCHANT ID</th>
                        <th>MERCHANT NAME</th>
                        <th class="text-right">BALANCE SETTLED</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── External Scripts ── -->
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

    const table = initServerDataTable('#balanceLogTable', '<?= base_url("report/balance-log") ?>', [
        {data: 'no', orderable: false, className: 'text-center'},
        {data: 'created_at', render: function(data){
            return '<span class="font-weight-bold">' + (data ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-') + '</span>';
        }},
        {data: 'merchant_id', render: function(data){
            return '<span class="badge badge-light text-dark border px-2 py-1">' + data + '</span>';
        }},
        {data: 'merchant_name', className: 'font-weight-bold text-dark'},
        {data: 'add_to_available', render: function(data){
            var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
            return '<span class="font-weight-bold text-dark">Rp ' + Number(val).toLocaleString('id-ID') + '</span>';
        }, className: 'text-right'}
    ], {
        order: [[1, 'desc']]
    });

    // Global Search Binding with debounce
    $('#dt-global-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));

    // ── More Filters dropdown ──
    var $moreBtn   = $('#dt-more-filters-btn');
    var $morePanel = $('#dt-more-filters-panel');
    var $moreClose = $('#dt-panel-close');

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

    // Select2 inside panel
    $('.balance-log-select2').select2({
        width: '100%',
        dropdownAutoWidth: true,
        minimumResultsForSearch: 5
    });
});
</script>


