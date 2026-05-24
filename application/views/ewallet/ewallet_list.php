<!-- Begin Page Content -->
<div >
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> E-Wallet Transactions Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Monitor inbound payments from popular digital wallets (OVO, DANA, LinkAja, ShopeePay, Gopay).</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Channel Routing</div>
                <p class="drawer-card-text">Differentiate traffic flowing through various e-wallet providers easily.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sync text-primary mr-2"></i> Real-Time Querying</div>
                <p class="drawer-card-text">Get immediate verification of wallet balance changes directly from the acquirer interface.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-bell text-primary mr-2"></i> Re-notify Merchants</div>
                <p class="drawer-card-text">Manually fire failed webhooks to ensure systems remain synchronized.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-check-double text-primary mr-2"></i> Settlement Validation</div>
                <p class="drawer-card-text">Confirm status shifts from pending/processing to settled/failed.</p>
            </div>
        </div>
    </div>


    <!-- Page Header -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">E-Wallet Transactions</h4>
            <p class="dt-page-subtitle">Manage and monitor all e-wallet payment activity.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
            <?php
                $download_url = base_url('finance/e-wallet/download')
                    . "?search_date_ewallet=" . ($this->session->userdata('search_date_ewallet') ?: '')
                    . "&search_date_to_ewallet=" . ($this->session->userdata('search_date_ewallet_to') ?: '')
                    . "&search_date_ewallet_settlement=" . ($this->session->userdata('search_date_ewallet_settlement') ?: '')
                    . "&search_name_ewallet=" . ($this->session->userdata('search_name_ewallet') ?: '');
            ?>
            
        </div>
    </div>
    <!-- Main Data Card -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <?php
            $search_date_ewallet_value            = $this->session->userdata('search_date_ewallet') ?: '';
            $search_date_ewallet_to_value         = $this->session->userdata('search_date_ewallet_to') ?: '';
            $search_date_ewallet_settlement_value = $this->session->userdata('search_date_ewallet_settlement') ?: '';
            $search_name_ewallet_value            = $this->session->userdata('search_name_ewallet') ?: '';
            $search_invoice_no_value              = $this->session->userdata('search_invoice_no') ?: '';
            $search_transid_ewallet_value         = $this->session->userdata('search_transid_ewallet') ?: '';

            // Count active extra filters for badge (excludes invoice_no which is now in global search)
            $extra_active = 0;
            if ($search_date_ewallet_value || $search_date_ewallet_to_value)  $extra_active++;
            if ($search_name_ewallet_value)            $extra_active++;
            if ($search_date_ewallet_settlement_value) $extra_active++;
            // search_invoice_no is now in the global search input, excluded from advanced badge
        ?>
        <form id="ewallet_form" method="post" action="<?= base_url('finance/e-wallet'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_ewallet_search = $search_invoice_no_value ?: $search_transid_ewallet_value; ?>
                    <input type="text" id="ewalletGlobalSearch" class="dt-search-input" placeholder="<?= $active_ewallet_search ?: 'Search by Invoice or Trans ID...'; ?>" value="<?= $active_ewallet_search; ?>">
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
                                <a href="<?= base_url('finance/e-wallet/reset'); ?>" class="dt-more-clear">Clear All</a>
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

                                <!-- Invoice No removed from here as it is handled by Global Search -->
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
                        <th>Merchant Info</th>
                        <th>Sub-Merchant Info</th>
                        <th>Merchant Trans ID</th>
                        <th>Invoice No</th>
                        <th>Type</th>
                        <th>Channel</th>
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
        var table = initServerDataTable("#ewalletTable", "<?= base_url('finance/e-wallet') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {
                data: 'name_merchant',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return ' [' + row.ref_merchantId + '] - ' + data;
                }
            },
            {
                data: 'name_submerchant',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return ' [' + row.ref_subMerchantId + '] - ' + data;
                }
            },
            {data: 'Merchant_Transaction_Id',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'c_type',className: 'text-nowrap'},
            {data: 'ref_cashinChannelId',className: 'text-nowrap'},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_mdr',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_fee',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {
                data: 'c_datetimeSettlement',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return (row.c_isSettlementRealtime == 1) ? 'Realtime' : (data || '-');
                }
            },
            {
                data: 'id', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var baseUrl = "<?= base_url() ?>";
                    var detailLink = baseUrl + 'finance/e-wallet/detail/' + data;
                    var resendLink = baseUrl + 'finance/e-wallet/notification/resend/' + data + '/' + row.ref_merchantId;
                    
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg">
                                <li><a href="${detailLink}" class="dropdown-item"><i class="fas fa-eye text-primary mr-2"></i> Detail</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item resend-notif-btn" data-href="${resendLink}"><i class="fas fa-paper-plane text-warning mr-2"></i> Resend</button></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ], {
            "order": [[1, 'desc']],
            "search": {
                "search": "<?= $search_invoice_no_value ?: $search_transid_ewallet_value ?>"
            }
        });

        // Global search with Debounce (Sync with hidden form field)
        $('#ewalletGlobalSearch').on('input', debounce(function() {
            const val = this.value;
            // Option 1: Trigger DataTable search directly
            table.search(val).draw();
            
            // Option 2: If we want it to persist across reloads via the 'invoice' parameter,
            // we could update the session, but DataTable's built-in search doesn't do that by default.
            // For consistency with how the let it be.
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

        $(document).on('click', '.resend-notif-btn', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            Swal.fire({
                title: 'Resend Notification',
                text: 'Are you sure you want to resend this notification?',
                icon: 'question',
                showCancelButton: true,
                customClass: {
                    popup: 'swal2-premium-popup',
                    confirmButton: 'swal2-premium-confirm',
                    cancelButton: 'swal2-premium-cancel'
                },
                buttonsStyling: false,
                confirmButtonText: 'Yes, resend!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // Select2 init for More panel merchant select
        $('.select2-more').select2({
            width: '100%',
            dropdownAutoWidth: true,
            
            minimumResultsForSearch: 5
        });
    });
</script>




<script>
$(document).ready(function() {
    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
