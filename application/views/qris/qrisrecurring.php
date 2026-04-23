<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">QRIS Recurring Transactions</h4>
            <p class="dt-page-subtitle">Monitor and manage all recurring QRIS activities in real-time.</p>
        </div>
    </div>
    <!-- ── KPI Summary Cards ── -->
    
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
        <form id="qris_recurring_form" method="post" action="<?= base_url('admin/qris_recurring'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="qrisRecurringGlobalSearch" class="dt-search-input" placeholder="Search by Merchant, ID, or Reference...">
                </div>
                
                <!-- RIGHT: Filters -->
                <div class="dt-toolbar-filters">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="vadynamicMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>
                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="vadynamicMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('admin/qris_recurring'); ?>" class="dt-more-clear">Clear All</a>
                            </div>
                            
                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_qr" class="dt-chip-input" value="<?= $this->session->userdata('search_date_qr'); ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_qr_to" class="dt-chip-input" value="<?= $this->session->userdata('search_date_qr_to'); ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Primary: Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <div class="dt-filter-chip">
                                        <select name="search_name_qr" class="dt-chip-select qris-recurring-select2">
                                            <option value="">All Merchants</option>
                                            <?php foreach ($merchants as $m): ?>
                                                <option value="<?= $m->id; ?>" <?= ($this->session->userdata('search_name_qr') == $m->id) ? 'selected' : ''; ?>>
                                                    [<?= $m->id; ?>] <?= $m->c_name; ?>
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
            <table class="table dt-table mb-0" id="qrisRecurringTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>DATE REQUEST</th>
                        <th>MERCHANT</th>
                        <th>SUB MERCHANT</th>
                        <th>MERCHANT TRANS ID</th>
                        <th>EXTERNAL ID</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div><!-- /.dt-card -->
</div>
<!-- /.container-fluid -->
<!-- Details Modal -->
<div class="modal fade" id="detailQrisDynamicChannelExternalModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title"  id="detailQrisDynamicChannelExternalModalLabel">External Log Details</h6>
            <small class="mh-subtitle" >View comprehensive information details</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
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
                                <pre class="p-3 rounded small border-0 mb-0" id="RequestHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Payload</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="RequestBody" style="max-height: 250px; overflow-y: auto;"></pre>
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
                                <pre class="p-3 rounded small border-0 mb-0" id="ResponseHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Body</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="ResponseBody" style="max-height: 250px; overflow-y: auto;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn-dt-action btn-dt-secondary px-4" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Init Server-Side DataTable
        var table = initServerDataTable("#qrisRecurringTable", "<?= base_url('admin/qris_recurring') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetimeRequest',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'name_merchant',className: 'text-nowrap'},
            {
                data: 'name_submerchant',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return row.ref_subMerchantId ? ' [' + row.ref_subMerchantId + '] ' + data : '-';
                }
            },
            {data: 'c_merchantTransactionId', className: 'text-dark font-weight-bold text-nowrap'},
            {
                data: 'ref_cashinExternalId',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    if (data && row.ref_cashinExternalLogQrisMpmIdCreate) {
                        return '<a data-toggle="modal" href="#" ' +
                            'data-target="#detailQrisDynamicChannelExternalModal" ' +
                            'data-merchantTransactionId="' + row.c_merchantTransactionId + '" ' +
                            'data-ref_cashinExternalId="' + data + '" ' +
                            'data-ref_cashinExternalLogQrisMpmIdCreate="' + row.ref_cashinExternalLogQrisMpmIdCreate + '" ' +
                            'class="detailQrisDynamicChannelExternalAjax">' + data + '</a>';
                    }
                    return data || '-';
                }
            },
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                return '<span class="font-weight-bold text-dark">Rp ' + number_format(data, 0, ',', '.') + '</span>';
            }},
            {
                data: 'c_status',
                render: function(data) {
                    var status_class = 'secondary';
                    var s = (data || '').toUpperCase();
                    if (['PAID', 'SUCCESS'].indexOf(s) !== -1) status_class = 'success';
                    else if (['FAILED', 'EXPIRED'].indexOf(s) !== -1) status_class = 'danger';
                    else if (['PENDING', 'CREATED'].indexOf(s) !== -1) status_class = 'warning';
                    return '<span class="badge badge-' + status_class + '">' + data + '</span>';
                }
            }
        ]);
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
        // Global search with Debounce
        $('#qrisRecurringGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));
        // Select2 inside toolbar
        $('.qris-recurring-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('.dt-toolbar'),
            minimumResultsForSearch: 5
        });
        // Detail AJAX
        $(document).on('click', '.detailQrisDynamicChannelExternalAjax', function(e) {
            e.preventDefault();
            var merchantTransactionId = $(this).data('merchanttransactionid');
            var ref_cashinExternalId = $(this).data('ref_cashinexternalid'); 
            var ref_cashinExternalLogQrisMpmIdCreate = $(this).data('ref_cashinextallogqrismpmidcreate') || $(this).data('ref_cashinexternallogqrismpmidcreate'); 
            // Handle potential variations in data attribute naming by jQuery
            if (!ref_cashinExternalLogQrisMpmIdCreate) {
                ref_cashinExternalLogQrisMpmIdCreate = $(this).attr('data-ref_cashinExternalLogQrisMpmIdCreate');
            }
            $('#detailQrisDynamicChannelExternalModalLabel').html('External Log - ' + merchantTransactionId);
            
            // Reset values to Loading/placeholder
            $('#cashinExternalId, #TransactionIdExternal1, #TransactionIdExternal2, #RequestDatetime, #ResponseDatetime').text('...');
            $('#RequestHeader, #RequestBody, #ResponseHeader, #ResponseBody').text('Loading...');
            $.ajax({
                url: "<?php echo base_url('admin/getDetailQrisRecurringChannelExternal'); ?>",
                method: "POST",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    ref_cashinExternalId: ref_cashinExternalId,
                    ref_cashinExternalLogQrisMpmIdCreate: ref_cashinExternalLogQrisMpmIdCreate
                },
                dataType: "json",
                beforeSend: function() {
                    $('#detailQrisDynamicChannelExternalModal').modal('show');
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


