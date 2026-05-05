<?php
// Session filter values
$search_date_va_value            = $this->session->userdata('search_date_va') ?: '';
$search_date_va_to_value         = $this->session->userdata('search_date_va_to') ?: '';
$search_date_va_settlement_value = $this->session->userdata('search_date_va_settlement') ?: '';
$search_name_va_value            = $this->session->userdata('search_name_va') ?: '';
$search_va_number_value          = $this->session->userdata('search_va_number') ?: '';
$search_va_transid_value         = $this->session->userdata('search_va_transid') ?: '';
$search_invoice_no_value         = $this->session->userdata('search_invoice_no') ?: '';

// Badge count for More Filters (excludes VA number and trans ID which are now in global search)
$extra_active = 0;
if ($search_name_va_value)            $extra_active++;
if ($search_date_va_settlement_value) $extra_active++;
// VA number, trans ID, invoice are in global search

$download_url = base_url('admin/download_VA')
    . '?search_date_va='            . $search_date_va_value
    . '&search_date_va_to='         . $search_date_va_to_value
    . '&search_date_va_settlement=' . $search_date_va_settlement_value
    . '&search_name_va='            . $search_name_va_value;
?>

<!-- ── Page Header ── -->
<div>

    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Virtual Account Transactions</h4>
            <p class="dt-page-subtitle">Track and manage all Virtual Account (VA) payment movements.</p>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <!-- ── KPI Summary Cards ── -->
    


    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="va_form" method="post" action="<?= base_url('admin/virtual_account'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_va_search = $search_va_number_value ?: ($search_va_transid_value ?: $search_invoice_no_value); ?>
                    <input type="text" id="vaGlobalSearch" class="dt-search-input" placeholder="<?= $active_va_search ?: 'Search by VA Number, Invoice, or Trans ID...'; ?>" value="<?= $active_va_search; ?>">
                </div>

                <!-- RIGHT: Primary chips + More Filters trigger -->
                <div class="dt-toolbar-filters">

                    

                    <!-- More Filters trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="vaMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="vaMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced filters</span>
                                <a href="<?= base_url('admin/resetVA'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Payment Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_va" class="dt-chip-input" value="<?= $search_date_va_value; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_va_to" class="dt-chip-input" value="<?= $search_date_va_to_value; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_name_va" class="dt-more-select va-select2">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $merchant): ?>
                                            <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $search_name_va_value) ? 'selected' : ''; ?>>
                                                [<?= $merchant->id; ?>] <?= $merchant->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Settlement Date -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-check mr-1 mr-2"></i> Settlement Date</label>
                                    <input type="date" name="search_date_va_settlement" class="dt-more-input" value="<?= $search_date_va_settlement_value; ?>">
                                </div>

                                <!-- VA Number & Trans ID moved to Global Search -->
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="vaMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div><!-- /dt-more-panel -->
                    </div>
                    
                    <!-- Always-visible Filter & Reset -->
                     <a href="<?= $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success">
                        <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                    </a>
                    
                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="vaTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date Payment</th>
                        <th>Merchant</th>
                        <th>Trans ID</th>
                        <th>VA Number</th>
                        <th>VA Custom ID</th>
                        <th>Invoice No</th>
                        <th>Channel Id</th>
                        <th>Type</th>
                        <th>Amount</th>
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
        var table = initServerDataTable("#vaTable", "<?= base_url('admin/virtual_account') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'merchant_name',className: 'text-nowrap'},
            {data: 'Merchant_Transaction_Id',className: 'text-nowrap'},
            {data: 'c_vaNumber',className: 'text-nowrap'},
            {data: 'c_custom',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'ref_cashinChannelId',className: 'text-nowrap'},
            {data: 'c_type',className: 'text-nowrap'},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
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
                    var detailLink = baseUrl + 'admin/VA_detail/' + data;
                    var resendLink = baseUrl + 'admin/SendnotifikasiVA/' + data + '/' + row.ref_merchantId;
                    
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm text-white rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                <li><a href="${detailLink}" class="dropdown-item"><i class="fas fa-eye text-primary mr-2"></i> Detail</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a onclick="javascript: return confirm('Are you sure, want to resend notification again ??')" href="${resendLink}" class="dropdown-item"><i class="fas fa-paper-plane text-warning mr-2"></i> Resend</a></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ], {
            "order": [[1, 'desc']],
            "search": {
                "search": "<?= $search_va_number_value ?: ($search_va_transid_value ?: $search_invoice_no_value) ?>"
            }
        });

        // Global search with Debounce
        $('#vaGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // ── More Filters dropdown ──
        var $moreBtn   = $('#vaMoreFiltersBtn');
        var $morePanel = $('#vaMoreFiltersPanel');
        var $moreClose = $('#vaMoreFiltersClose');

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
        $('.va-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $morePanel,
            minimumResultsForSearch: 5
        });
    });
</script>



