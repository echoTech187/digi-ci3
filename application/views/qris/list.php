<?php
// Session filter values
$search_date_qris_value            = $this->session->userdata('search_date_qris') ?: '';
$search_date_qris_to_value         = $this->session->userdata('search_date_qris_to') ?: '';
$search_date_qris_settlement_value = $this->session->userdata('search_date_qris_settlement') ?: '';
$search_name_qris_value            = $this->session->userdata('search_name_qris') ?: '';
$search_transactionid_ht_value     = $this->session->userdata('search_transactionid_ht') ?: '';
$search_rrn_value                  = $this->session->userdata('search_rrn') ?: '';

// Badge count for More Filters
$extra_active = 0;
if ($search_name_qris_value)            $extra_active++;
if ($search_date_qris_settlement_value) $extra_active++;
if ($search_transactionid_ht_value)     $extra_active++;
if ($search_rrn_value)                  $extra_active++;

$download_url = base_url('admin/download_qris')
    . '?search_date_qris='            . $search_date_qris_value
    . '&search_date_qris_to='         . $search_date_qris_to_value
    . '&search_date_qris_settlement=' . $search_date_qris_settlement_value
    . '&search_name_qris='            . $search_name_qris_value;
?>

<!-- ── Page Header ── -->
<div class="container-fluid pb-4">

    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">QRIS Transactions</h4>
            <p class="dt-page-subtitle">Manage and monitor all QRIS payment activity.</p>
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
                <div class="dt-summary-sub"><i class="fas fa-qrcode mr-1"></i>QRIS transactions</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-list-ul"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL AMOUNT</div>
                <div class="dt-summary-value">Rp <?= number_format($total_trx, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-money-bill-wave mr-1"></i>Volume processed</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-yellow">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL FEE</div>
                <div class="dt-summary-value">Rp <?= number_format($total_fee, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-percent mr-1"></i>Service fee</div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-receipt"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-red">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL PROFIT</div>
                <div class="dt-summary-value">Rp <?= number_format($total_fee - $total_fee_ext, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-chart-line mr-1"></i>Net earnings</div>
            </div>
            <div class="dt-summary-icon dt-icon-red">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>

    </div>


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="qris_form" method="post" action="<?= base_url('admin/qris'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="qrisGlobalSearch" class="dt-search-input" placeholder="Search by any parameter...">
                </div>

                <!-- RIGHT: Primary chips + More Filters trigger -->
                <div class="dt-toolbar-filters">

                    

                    <!-- More Filters trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="qrisMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="qrisMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced filters</span>
                                <a href="<?= base_url('admin/resetqris'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1"></i>Payment Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_qris" class="dt-chip-input" value="<?= $search_date_qris_value; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_qris_to" class="dt-chip-input" value="<?= $search_date_qris_to_value; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-user-tie mr-1"></i>Merchant</label>
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
                                    <label class="dt-more-label"><i class="fas fa-check-circle mr-1"></i>Settlement Date</label>
                                    <input type="date" name="search_date_qris_settlement" class="dt-more-input" value="<?= $search_date_qris_settlement_value; ?>">
                                </div>

                                <!-- Transaction ID -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-hashtag mr-1"></i>Transaction ID</label>
                                    <input type="text" name="search_transactionid_ht" class="dt-more-input" placeholder="TXN-..." value="<?= $search_transactionid_ht_value; ?>">
                                </div>

                                <!-- RRN -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-barcode mr-1"></i>RRN</label>
                                    <input type="text" name="search_rrn" class="dt-more-input" placeholder="RRN..." value="<?= $search_rrn_value; ?>">
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1"></i> APPLY FILTER
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
                        <th>Invoice No</th>
                        <th>Trans ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>MDR</th>
                        <th>Fee</th>
                        <th>RRN</th>
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
        var table = initServerDataTable("#qrisTable", "<?= base_url('admin/qris') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetime', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'merchant_info'},
            {data: 'submerchant_info'},
            {data: 'c_invoiceNo'},
            {data: 'Merchant_Transaction_Id'},
            {data: 'c_type'},
            {data: 'c_amount', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_mdr', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_fee', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_issuerRrn'},
            {data: 'c_isSettlementRealtime'},
            {data: 'c_datetimeSettlement'},
            
            {data: 'action', orderable: false, searchable: false}
        ], {
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
                "infoFiltered": "",
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No transactions found.</div>'
            },
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

        // Global search
        $('#qrisGlobalSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

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

        // Select2 inside panel
        $('.qris-select2').select2({
            width: '100%',
            dropdownParent: $morePanel,
            minimumResultsForSearch: 5
        });
    });
</script>