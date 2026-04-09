<?php
// Session values
$search_date_var_value        = $this->session->userdata('search_date_var') ?: '';
$search_date_var_to_value     = $this->session->userdata('search_date_var_to') ?: '';
$search_name_var_value        = $this->session->userdata('search_name_var') ?: '';
$search_submerchant_var_value = $this->session->userdata('search_submerchant_var') ?: '';
$download_url = base_url('admin/download_VA_recurring') // Assuming this exists or follows pattern
    . '?search_date_var='            . $search_date_var_value
    . '&search_date_var_to='         . $search_date_var_to_value
    . '&search_name_var='            . $search_name_var_value;
?>
<!-- ── Page Header ── -->
<div class="container-fluid pb-4">
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Recurring VA Transactions</h4>
            <p class="dt-page-subtitle">Manage and track automated recurring Virtual Account payments.</p>
        </div>
    </div>
    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4">
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL QUANTITY</div>
                <div class="dt-summary-value"><?= number_format($qty, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-repeat mr-1"></i>Recurring payments</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL AMOUNT</div>
                <div class="dt-summary-value text-success">Rp <?= number_format($total_trx, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-wallet mr-1"></i>Processed volume</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        <!-- Placeholders for symmetry -->
        <div class="dt-summary-card dt-summary-yellow" style="opacity: 0.6;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">ACTIVE SCHEDULES</div>
                <div class="dt-summary-value text-warning">-</div>
                <div class="dt-summary-sub">Coming soon</div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="dt-summary-card dt-summary-red" style="opacity: 0.6;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">FAILED REQUESTS</div>
                <div class="dt-summary-value text-danger">-</div>
                <div class="dt-summary-sub">Coming soon</div>
            </div>
            <div class="dt-summary-icon dt-icon-red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
         <!-- ── Toolbar ── -->
        <?php
            // Badge count for More Filters
            $extra_active = 0;
            if ($this->session->userdata('search_name_vad'))      $extra_active++;
            if ($this->session->userdata('search_va_number'))    $extra_active++;
            if ($this->session->userdata('search_merchant_trxid')) $extra_active++;
        ?>
        <form id="varecurring_form" method="post" action="<?= base_url('admin/VA_recurring'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="vaRecurringGlobalSearch" class="dt-search-input" placeholder="Search by Channel, Merchant, or ID...">
                </div>
                <!-- RIGHT: Primary chips (Fewer than 4, so no More Filters button) -->
                <div class="dt-toolbar-filters">
                    <!-- RIGHT: Filters -->
                <div class="dt-toolbar-filters">
                    
                    
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="vadynamicMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>
                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="vadynamicMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced Filters</span>
                                <a href="<?= base_url('admin/resetVa_dynamic'); ?>" class="dt-more-clear">Clear All</a>
                            </div>
                            
                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1"></i>Request Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_var" class="dt-chip-input" value="<?= $search_date_var_value; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_var_to" class="dt-chip-input" value="<?= $search_date_var_to_value; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Primary: Merchant -->
                                <div class="dt-more-field" style="min-width: 200px;">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1"></i>Merchant</label>
                                    <select name="search_name_var" class="dt-chip-select select2">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $merchant): ?>
                                            <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $search_name_var_value) ? 'selected' : ''; ?>>
                                                [<?= $merchant->id; ?>] <?= $merchant->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1"></i> APPLY FILTER
                                </button>
                                <button type="button" id="vadynamicMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>
        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="varecurringTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date Time Request</th>
                        <th>Merchant</th>
                        <th>Sub Merchant</th>
                        <th>Merchant Trans ID</th>
                        <th>Channel ID</th>
                        <th>External ID</th>
                        <th>VA Number</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div><!-- /.dt-card -->
</div>
<!-- Details Modal -->
<div class="modal fade" id="detailVaDynamicChannelExternalModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 p-4">
                <h5 class="modal-title text-white font-weight-bold" id="detailVaDynamicChannelExternalModalLabel">
                    <i class="fas fa-info-circle mr-2"></i> External Log Details
                </h5>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Provider</div>
                        <div class="h6 font-weight-bold text-dark mb-0" id="cashinExternalId"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 1</div>
                        <div class="h6 font-weight-bold text-dark mb-0" id="TransactionIdExternal1"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 2</div>
                        <div class="h6 font-weight-bold text-dark mb-0" id="TransactionIdExternal2"></div>
                    </div>
                </div>
                
                <ul class="nav nav-pills mb-3" id="detailTab" role="tablist" style="gap: 10px;">
                    <li class="nav-item">
                        <a class="nav-link active rounded-pill px-4 font-weight-bold" id="request-tab" data-toggle="pill" href="#request" style="font-size: 11px;">REQUEST</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-4 font-weight-bold text-muted" id="response-tab" data-toggle="pill" href="#response" style="font-size: 11px;">RESPONSE</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="detailTabContent">
                    <div class="tab-pane fade show active" id="request">
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Request Datetime</label>
                                <div class="text-dark small" id="RequestDatetime"></div>
                            </div>
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                <pre class="bg-light p-3 rounded small border-0 mb-0" id="RequestHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Payload</label>
                                <pre class="bg-light p-3 rounded small border-0 mb-0" id="RequestBody" style="max-height: 250px; overflow-y: auto;"></pre>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="response">
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Response Datetime</label>
                                <div class="text-dark small" id="ResponseDatetime"></div>
                            </div>
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                <pre class="bg-light p-3 rounded small border-0 mb-0" id="ResponseHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Body</label>
                                <pre class="bg-light p-3 rounded small border-0 mb-0" id="ResponseBody" style="max-height: 250px; overflow-y: auto;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-4 pt-0">
                <button type="button" class="btn-dt-action btn-dt-secondary px-4" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Init Select2
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        // Init Server-Side DataTable
        var table = initServerDataTable("#varecurringTable", "<?= base_url('admin/VA_recurring') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetimeRequest', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'name_merchant'},
            {data: 'name_submerchant'},
            {data: 'c_merchantTransactionId'},
            {data: 'ref_cashinChannelId'},
            {data: 'ref_cashinExternalId'},
            {data: 'c_vaNumber'},
            {data: 'c_amount', render: function(data){
                var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
                return '<span class="font-weight-bold text-dark">Rp ' + Number(val).toLocaleString('id-ID') + '</span>';
            }},
            {data: 'c_status'}
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
        // ── More Filters dropdown ──
        var $moreBtn   = $('#vadynamicMoreFiltersBtn');
        var $morePanel = $('#vadynamicMoreFiltersPanel');
        var $moreClose = $('#vadynamicMoreFiltersClose');
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
        // Global search
        $('#vaRecurringGlobalSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
        // Detail AJAX
        $(document).on('click', '.detailVaDynamicChannelExternalAjax', function(e) {
            e.preventDefault();
            var merchantTransactionId = $(this).data('merchanttransactionid');
            var ref_cashinExternalId = $(this).data('ref_cashinexternalid'); 
            var ref_cashinExternalLogVaIdCreate = $(this).data('ref_cashinexternallogvaidcreate'); 
            // Handle potential variations in data attribute naming by jQuery
            if (!ref_cashinExternalLogVaIdCreate) {
                ref_cashinExternalLogVaIdCreate = $(this).attr('data-ref_cashinExternalLogVaIdCreate');
            }
            $('#detailVaDynamicChannelExternalModalLabel').html('<i class="fas fa-info-circle mr-2"></i> External Log - ' + merchantTransactionId);
            
            // Reset values to Loading/placeholder
            $('#cashinExternalId, #TransactionIdExternal1, #TransactionIdExternal2, #RequestDatetime, #ResponseDatetime').text('...');
            $('#RequestHeader, #RequestBody, #ResponseHeader, #ResponseBody').text('Loading...');
            $.ajax({
                url: "<?php echo base_url('admin/getDetailVaRecurringChannelExternal'); ?>",
                method: "POST",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    ref_cashinExternalId: ref_cashinExternalId,
                    ref_cashinExternalLogVaIdCreate: ref_cashinExternalLogVaIdCreate
                },
                dataType: "json",
                beforeSend: function() {
                    $('#detailVaDynamicChannelExternalModal').modal('show');
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        return;
                    }
                    $('#cashinExternalId').text(ref_cashinExternalId ? ref_cashinExternalId.toUpperCase() : '-');
                    $('#TransactionIdExternal1').text(response.TransactionIdExternal1 || '-');
                    $('#TransactionIdExternal2').text(response.TransactionIdExternal2 || '-');
                    $('#RequestDatetime').text(response.RequestDatetime ? moment(response.RequestDatetime).format('DD MMM YYYY, HH:mm:ss') : '-');
                    $('#RequestHeader').text(response.RequestHeader ? JSON.stringify(response.RequestHeader, null, 4) : '-');
                    $('#RequestBody').text(response.RequestBody ? JSON.stringify(response.RequestBody, null, 4) : '-');
                    $('#ResponseDatetime').text(response.ResponseDatetime ? moment(response.ResponseDatetime).format('DD MMM YYYY, HH:mm:ss') : '-');
                    $('#ResponseHeader').text(response.ResponseHeader ? JSON.stringify(response.ResponseHeader, null, 4) : '-');
                    $('#ResponseBody').text(response.ResponseBody ? JSON.stringify(response.ResponseBody, null, 4) : '-');
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    Swal.fire('Error', 'Failed to fetch transaction details. Please check console for details.', 'error');
                }
            });
        });
    });
</script>