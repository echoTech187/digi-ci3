<?php
// Session filter values
$search_date_qris_value            = $this->session->userdata('search_date_qris') ?: '';
$search_date_qris_to_value         = $this->session->userdata('search_date_qris_to') ?: '';
$search_date_qris_settlement_value = $this->session->userdata('search_date_qris_settlement') ?: '';
$search_name_qris_value            = $this->session->userdata('search_name_qris') ?: '';
$search_transactionid_ht_value     = $this->session->userdata('search_transactionid_ht') ?: '';
$search_rrn_value                  = $this->session->userdata('search_rrn') ?: '';
$search_invoice_no_value          = $this->session->userdata('search_invoice_no') ?: '';

// Badge count for More Filters (excludes transaction ID and RRN which are now in global search)
$extra_active = 0;
if ($search_name_qris_value)            $extra_active++;
if ($search_date_qris_settlement_value) $extra_active++;
// search_invoice_no, search_transactionid_ht, search_rrn are in global search

$download_url = base_url('finance/qris/download')
    . '?search_date_qris='            . $search_date_qris_value
    . '&search_date_qris_to='         . $search_date_qris_to_value
    . '&search_date_qris_settlement=' . $search_date_qris_settlement_value
    . '&search_name_qris='            . $search_name_qris_value;
?>

<!-- ── Page Header ── -->
<div>
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> QRIS Transactions Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Track and audit payments received via QRIS MPM (Merchant Presented Mode) dynamic codes.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-qrcode text-primary mr-2"></i> Dynamic Generation</div>
                <p class="drawer-card-text">Dynamic QR codes are generated for each transaction with a pre-configured expiry time.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-key text-primary mr-2"></i> Retrieval & RRN</div>
                <p class="drawer-card-text">Use the Reference Retrieval Number (RRN) from the acquiring bank to trace credit settlements.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-bell text-primary mr-2"></i> Resend Callbacks</div>
                <p class="drawer-card-text">Push status updates back to the merchant's callback URL in case of transport timeouts.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sliders-h text-primary mr-2"></i> Advanced Querying</div>
                <p class="drawer-card-text">Filter by specific merchant names, RRNs, invoice numbers, or payment status.</p>
            </div>
        </div>
    </div>


    <div class="dt-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="dt-page-title mb-1">QRIS Transactions</h4>
            <p class="dt-page-subtitle mb-0">Manage and monitor all QRIS payment activity.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- Alerts Standardized to Swal2 Premium -->
    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= $this->session->flashdata('success'); ?>',
                    icon: 'success',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>
        });
    </script>

    <!-- ── KPI Summary Cards ── -->
    


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="qris_form" method="post" action="<?= base_url('finance/qris'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_qris_search = $search_transactionid_ht_value ?: ($search_rrn_value ?: $search_invoice_no_value); ?>
                    <input type="text" id="qrisGlobalSearch" class="dt-search-input" placeholder="<?= $active_qris_search ?: 'Search by Invoice, Trans ID, or RRN...'; ?>" value="<?= $active_qris_search; ?>">
                </div>

                <!-- RIGHT: Primary chips + More Filters trigger -->
                <div class="dt-toolbar-filters">

                    

                    <!-- More Filters trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="qrisMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="qrisMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced filters</span>
                                <a href="<?= base_url('finance/qris/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Payment Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_qris" class="dt-chip-input" value="<?= $search_date_qris_value; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_qris_to" class="dt-chip-input" value="<?= $search_date_qris_to_value; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-user-tie mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_name_qris" class="dt-more-select qris-select2 text-primary-custom fw-bold">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $merchant): ?>
                                            <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $search_name_qris_value) ? 'selected' : ''; ?>>
                                                [<?= $merchant->id; ?>] <?= $merchant->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Settlement Date -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-check-circle mr-1 mr-2"></i> Settlement Date</label>
                                    <input type="date" name="search_date_qris_settlement" class="dt-more-input" value="<?= $search_date_qris_settlement_value; ?>">
                                </div>

                                <!-- Transaction ID & RRN moved to Global Search -->
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="qrisMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div><!-- /dt-more-panel -->
                    </div>

                    <!-- Always-visible Filter & Reset -->
                    <a href="<?= $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success ">
                        <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                    </a>    
                    
                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="qrisTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date Payment</th>
                        <th>Merchant</th>
                        <th>Sub Merchant</th>
                        <th>Trans ID</th>
                        <th>RRN</th>
                        <th>Invoice No</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>MDR</th>
                        <th>Fee</th>
                        <th>Realtime?</th>
                        <th>Date Settlement</th>
                        <th>Action</th>
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
        var table = initServerDataTable("#qrisTable", "<?= base_url('finance/qris') ?>", [
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
            {data: 'c_issuerRrn',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'c_type',className: 'text-nowrap'},
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
                data: 'c_isSettlementRealtime',
                className: 'text-nowrap text-center',
                render: function(data) {
                    return (data == 1) ? 'Yes' : 'No';
                }
            },
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
                    var detailLink = baseUrl + 'finance/qris/detail/' + data;
                    var resendLink = baseUrl + 'qris/notification/resend/' + data + '/' + row.ref_merchantId;
                    
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
                "search": "<?= $search_transactionid_ht_value ?: ($search_rrn_value ?: $search_invoice_no_value) ?>"
            }
        });

        table.on('xhr', function(e, settings, json) {
            if (json && json.redirect) {
                window.location = json.redirect;
            }
        });

        // Global search with Debounce
        $('#qrisGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // ── More Filters dropdown ──
        var $moreBtn   = $('#qrisMoreFiltersBtn');
        var $morePanel = $('#qrisMoreFiltersPanel');
        var $moreClose = $('#qrisMoreFiltersClose');

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

        // Select2 inside panel
        $('.qris-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $morePanel,
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
