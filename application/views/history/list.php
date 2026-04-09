<?php
// Session filter values
$search_date_purchase_value = $this->session->userdata('search_date_purchase') ?: '';
$search_merchant_purchase_value = $this->session->userdata('search_merchant_purchase') ?: '';

// Badge count for More Filters (Merchant only for now)
$extra_active = 0;
if ($search_merchant_purchase_value) $extra_active++;

$download_url = base_url('admin/download_history') 
    . "?search_date_purchase=" . $search_date_purchase_value 
    . "&search_merchant_purchase=" . $search_merchant_purchase_value;
?>

<!-- ── Page Header ── -->
<div class="container-fluid pb-4">

    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Purchase Transactions</h4>
            <p class="dt-page-subtitle">Complete history transactions and other purchase activities.</p>
        </div>
        
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4">

        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL QUANTITY</div>
                <div class="dt-summary-value"><?= number_format($qty, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-plus mr-1"></i>Purchase count</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-list-ul"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL VOLUME</div>
                <div class="dt-summary-value">Rp <?= number_format($total_trx, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-coins mr-1"></i>Transaction value</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Placeholder/Derived analytics to maintain 4-card layout symmetry -->
        <div class="dt-summary-card dt-summary-yellow">
            <div class="dt-summary-body">
                <div class="dt-summary-label">AVG. TICKET</div>
                <div class="dt-summary-value">Rp <?= $qty > 0 ? number_format($total_trx / $qty, 0, ',', '.') : '0'; ?></div>
                <div class="dt-summary-sub"><i class="fas fa-chart-line mr-1"></i>Value per purchase</div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-calculator"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-red">
            <div class="dt-summary-body">
                <div class="dt-summary-label">SYSTEM UPTIME</div>
                <div class="dt-summary-value">100%</div>
                <div class="dt-summary-sub"><i class="fas fa-check-circle mr-1"></i>Purchase Gateway</div>
            </div>
            <div class="dt-summary-icon dt-icon-red">
                <i class="fas fa-server"></i>
            </div>
        </div>

    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
         
        <form id="historyForm" method="post" action="<?= base_url('admin/history'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="historyGlobalSearch" class="dt-search-input" placeholder="Search by Invoice, Phone, or ID...">
                </div>
                <!-- RIGHT: Filters -->
                <div class="dt-toolbar-filters">
                    
                    

                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="btnToggleFilters" class="dt-more-filters-btn btn-sm <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="moreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced Filters</span>
                                <a href="<?= base_url('admin/resetewallet_dynamic'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Single Date -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1"></i>Transaction Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_purchase" class="dt-chip-input" value="<?= $search_date_purchase_value; ?>">
                                    </div>
                                </div>

                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1"></i>Merchant</label>
                                    <div class="dt-filter-chip" style="min-width: 180px;">
                                        <select name="search_merchant_purchase" class="dt-chip-input history-select2">
                                            <option value="">All Merchants</option>
                                            <?php foreach ($merchants as $merchant): ?>
                                                <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $search_merchant_purchase_value) ? 'selected' : ''; ?>>
                                                    <?= $merchant->c_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1"></i> APPLY FILTER
                                </button>
                                <button type="button" id="btnCloseFilters" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="<?= $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success">
                        <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                    </a>
                   
                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="historyTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Merchant</th>
                        <th>Date Time</th>
                        <th>ID Produk</th>
                        <th>Invoice No</th>
                        <th>No Pelanggan</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div><!-- /.dt-card -->
</div>
<script type="text/javascript">
    $(document).ready(function() {
        // Init Server-Side DataTable
        var table = initServerDataTable("#historyTable", "<?= base_url('admin/history') ?>", [
            {data: 'no'},
            {data: 'name_merchant'},
            {data: 'c_datetime', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'ref_cashoutChannelId'},
            {data: 'c_invoiceNo'},
            {data: 'c_phone'},
            {data: 'c_amount', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_status', render: function(data){
                var badgeClass = 'badge-secondary';
                if(data == 'SUCCESS') badgeClass = 'badge-success';
                if(data == 'FAILED') badgeClass = 'badge-danger';
                if(data == 'PENDING') badgeClass = 'badge-warning';
                return '<span class="badge badge-pill ' + badgeClass + '">' + data + '</span>';
            }}
        ], {
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

        // ── More Filters dropdown ──
        var $moreBtn   = $('#btnToggleFilters');
        var $morePanel = $('#moreFiltersPanel');
        var $moreClose = $('#btnCloseFilters');

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

        // Close panel when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
                $morePanel.removeClass('dt-panel-open');
                $moreBtn.removeClass('dt-open');
            }
        });

        // Global search
        $('#historyGlobalSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Select2 for the toolbar
        $('.history-select2').select2({
            width: '100%'
        });
    });
</script>