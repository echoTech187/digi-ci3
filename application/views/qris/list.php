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
<div>

    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">QRIS Transactions</h4>
            <p class="dt-page-subtitle">Manage and monitor all QRIS payment activity.</p>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <!-- ── KPI Summary Cards ── -->
    


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="qris_form" method="post" action="<?= base_url('admin/qris'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="qrisGlobalSearch" class="dt-search-input" placeholder="Search by Trans ID, or RRN...">
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
                                <a href="<?= base_url('admin/resetqris'); ?>" class="dt-more-clear">Clear All</a>
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

                                <!-- Transaction ID -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-hashtag mr-1 mr-2"></i> Transaction ID</label>
                                    <input type="text" name="search_transactionid_ht" class="dt-more-input" placeholder="TXN-..." value="<?= $search_transactionid_ht_value; ?>">
                                </div>

                                <!-- RRN -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-barcode mr-1 mr-2"></i> RRN</label>
                                    <input type="text" name="search_rrn" class="dt-more-input" placeholder="RRN..." value="<?= $search_rrn_value; ?>">
                                </div>
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
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'Merchant_Transaction_Id',className: 'text-nowrap'},
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
            {data: 'c_issuerRrn',className: 'text-nowrap'},
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
                    var detailLink = baseUrl + 'admin/qris_detail/' + data;
                    var resendLink = baseUrl + 'admin/SendnotifikasiQRIS/' + data + '/' + row.ref_merchantId;
                    
                    return '<a href="' + detailLink + '" class="btn btn-action-detail"><i class="fas fa-eye mr-2"></i>Detail</a> ' +
                           '<a onclick="javascript: return confirm(\'Are you sure, want to resend notification again ??\')" href="' + resendLink + '" class="btn btn-action-resend"><i class="fas fa-paper-plane mr-2"></i>Resend</a>';
                }
            }
        ], {
            "order": [[1, 'desc']]
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

        // Select2 inside panel
        $('.qris-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $morePanel,
            minimumResultsForSearch: 5
        });
    });
</script>


