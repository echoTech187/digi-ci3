<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Header -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">E-Wallet Transactions</h4>
            <p class="dt-page-subtitle">Manage and monitor all e-wallet payment activity.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php
                $download_url = base_url('admin/download_ewallet')
                    . "?search_date_ewallet=" . ($this->session->userdata('search_date_ewallet') ?: '')
                    . "&search_date_to_ewallet=" . ($this->session->userdata('search_date_ewallet_to') ?: '')
                    . "&search_date_ewallet_settlement=" . ($this->session->userdata('search_date_ewallet_settlement') ?: '')
                    . "&search_name_ewallet=" . ($this->session->userdata('search_name_ewallet') ?: '');
            ?>
            
        </div>
    </div>

    <!-- KPI Summary Cards -->
    

    <!-- Main Data Card -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <?php
            $search_date_ewallet_value            = $this->session->userdata('search_date_ewallet') ?: '';
            $search_date_ewallet_to_value         = $this->session->userdata('search_date_ewallet_to') ?: '';
            $search_date_ewallet_settlement_value = $this->session->userdata('search_date_ewallet_settlement') ?: '';
            $search_name_ewallet_value            = $this->session->userdata('search_name_ewallet') ?: '';
            $search_invoice_no_value              = $this->session->userdata('search_invoice_no') ?: '';

            // Count active extra filters for badge
            $extra_active = 0;
            if ($search_name_ewallet_value)            $extra_active++;
            if ($search_date_ewallet_settlement_value) $extra_active++;
            if ($search_invoice_no_value)              $extra_active++;
        ?>
        <form id="ewallet_form" method="post" action="<?= base_url('admin/ewallet'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="ewalletGlobalSearch" class="dt-search-input" placeholder="Search by Trans ID...">
                </div>

                <!-- RIGHT: Primary chips + More Filters trigger -->
                <div class="dt-toolbar-filters">

                    

                    <!-- More Filters trigger button -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="moreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown panel -->
                        <div class="dt-more-panel" id="moreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced filters</span>
                                <a href="<?= base_url('admin/resetewallet'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Payment Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_ewallet" class="dt-chip-input" value="<?= $search_date_ewallet_value; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_ewallet_to" class="dt-chip-input" value="<?= $search_date_ewallet_to_value; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-user-tie mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_name_ewallet" class="dt-more-select select2-more">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $merchant): ?>
                                            <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $search_name_ewallet_value) ? 'selected' : ''; ?>>
                                                [<?= $merchant->id; ?>] <?= $merchant->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Settlement Date -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-check-circle mr-1 mr-2"></i> Settlement Date</label>
                                    <input type="date" name="search_date_ewallet_settlement" class="dt-more-input" value="<?= $search_date_ewallet_settlement_value; ?>">
                                </div>

                                <!-- Invoice No -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-file-invoice mr-1 mr-2"></i> Invoice No</label>
                                    <input type="text" name="search_invoice_no" class="dt-more-input" placeholder="INV-..." value="<?= $search_invoice_no_value; ?>">
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="moreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div><!-- /dt-more-panel -->
                    </div>

                    <!-- Apply & Reset (always visible) -->
                    <a href="<?= $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success ">
                        <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                    </a>
                    

                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="ewalletTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date Time</th>
                        <th>Sub Merchant Info</th>
                        <th>Invoice No</th>
                        <th>Type</th>
                        <th>Channel</th>
                        <th>Merchant Trans ID</th>
                        <th>Amount</th>
                        <th>MDR</th>
                        <th>Fee</th>
                        <th>Settlement Info</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div><!-- /container-fluid -->
<script>
    $(document).ready(function() {
        // Select2 for merchant chips
        $('.dt-chip-select').select2({
            width: 'auto',
            dropdownAutoWidth: true,
            minimumResultsForSearch: 5
        });

        // Init Server-Side DataTable
        var table = initServerDataTable("#ewalletTable", "<?= base_url('admin/ewallet') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'submerchant_info',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'c_type',className: 'text-nowrap'},
            {data: 'ref_cashinChannelId',className: 'text-nowrap'},
            {data: 'Merchant_Transaction_Id',className: 'text-nowrap'},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_mdr',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_fee',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'settlement_info',className: 'text-nowrap'},
            {data: 'action', orderable: false, searchable: false}
        ], {
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "infoFiltered": "",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No transactions found.</div>'
            },
            "order": [[1, 'desc']],
            "dom": 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
            "drawCallback": function(settings) {
                var api    = this.api();
                var info   = api.page.info();
                var $pager = $(api.table().container()).find('.dt-footer-pager');

                var currPage  = info.page + 1;       // 1-based
                var totalPages = info.pages || 1;

                $pager.html(
                    '<button class="dt-nav-btn dt-prev-btn" ' + (info.page === 0 ? 'disabled' : '') + '>'+
                        '<i class="fas fa-chevron-left mr-2"></i> PREVIOUS'+
                    '</button>'+
                    '<span class="dt-page-counter">'+
                        '<strong>' + currPage + '</strong> of <strong>' + totalPages + '</strong>'+
                    '</span>'+
                    '<button class="dt-nav-btn dt-next-btn" ' + (info.page >= totalPages - 1 ? 'disabled' : '') + '>'+
                        'NEXT <i class="fas fa-chevron-right"></i>'+
                    '</button>'
                );

                // Prev / Next click handlers
                $pager.find('.dt-prev-btn').off('click').on('click', function() {
                    if (!$(this).prop('disabled')) { api.page('previous').draw('page'); }
                });
                $pager.find('.dt-next-btn').off('click').on('click', function() {
                    if (!$(this).prop('disabled')) { api.page('next').draw('page'); }
                });
            }
        });

        // Global search with Debounce
        $('#ewalletGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // ── More Filters dropdown toggle ──
        var $moreBtn   = $('#moreFiltersBtn');
        var $morePanel = $('#moreFiltersPanel');
        var $moreClose = $('#moreFiltersClose');

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

        // Close when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
                $morePanel.removeClass('dt-panel-open');
                $moreBtn.removeClass('dt-open');
            }
        });

        // Select2 init for More panel merchant select
        $('.select2-more').select2({
            width: '100%',
            dropdownParent: $morePanel,
            minimumResultsForSearch: 5
        });
    });
</script>



