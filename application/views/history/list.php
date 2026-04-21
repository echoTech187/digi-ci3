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
<div>

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
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="moreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('admin/resetHistory'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Single Date -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Transaction Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_purchase" class="dt-chip-input" value="<?= $search_date_purchase_value; ?>">
                                    </div>
                                </div>

                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
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
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
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
        ]);

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
        // Global search with Debounce
        $('#historyGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // Select2 for the toolbar
        $('.history-select2').select2({
            width: '100%'
        });
    });
</script>


