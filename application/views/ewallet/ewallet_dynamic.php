<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">E-Wallet Dynamic Transactions</h4>
            <p class="dt-page-subtitle">Track and analyze dynamic e-wallet payment activities in real-time.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> E-Wallet Dynamic Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This ledger displays dynamically generated E-Wallet transactions and payment activities in real-time.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-mobile-alt text-primary mr-2"></i> E-Wallet Transactions</div>
                <p class="drawer-card-text">Monitor real-time dynamic wallet payments including OVO, DANA, LinkAja, and other active e-wallet channels.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-filter text-primary mr-2"></i> Filters & Statuses</div>
                <p class="drawer-card-text">Filter transactions by date range, merchant, or statuses (Created, Paid, Pending, Failed, Expired).</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-vial text-primary mr-2"></i> Simulation & Actions</div>
                <p class="drawer-card-text">Use the Action column to trigger or test dynamic channel callbacks directly in sandbox environments.</p>
            </div>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <?php
            // Badge count for More Filters
            $extra_active = 0;
            if ($this->session->userdata('search_date_qd') || $this->session->userdata('search_date_qd_to')) $extra_active++;
            if ($this->session->userdata('search_name_qd')) $extra_active++;
            if ($this->session->userdata('search_status_transaction_qd')) $extra_active++;
        ?>

        <!-- ── Toolbar ── -->
        <form id="ewallet_dynamic_form" method="post" action="<?= base_url('e-wallet/dynamic'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_ed_search = $this->session->userdata('search_transid_qd'); ?>
                    <input type="text" id="ewalletDynamicGlobalSearch" class="dt-search-input" placeholder="<?= $active_ed_search ?: 'Search Transaction, Merchant, or ID...'; ?>" value="<?= $active_ed_search; ?>">
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
                                <a href="<?= base_url('e-wallet/dynamic/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_qd" class="dt-chip-input" value="<?= $this->session->userdata('search_date_qd'); ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_qd_to" class="dt-chip-input" value="<?= $this->session->userdata('search_date_qd_to'); ?>" title="Date To">
                                    </div>
                                </div>

                                <!-- Primary: Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <div class="dt-filter-chip">
                                        <select name="search_name_qd" class="dt-chip-select ewallet-dynamic-select2">
                                            <option value="">All Merchants</option>
                                            <?php foreach ($merchants as $m): ?>
                                                <option value="<?= $m->id; ?>" <?= ($this->session->userdata('search_name_qd') == $m->id) ? 'selected' : ''; ?>>
                                                    [<?= $m->id; ?>] <?= $m->c_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status Transaction</label>
                                    <select name="search_status_transaction_qd" class="dt-more-select ewallet-dynamic-select2 no-search">
                                        <option value="">All Statuses</option>
                                        <option value="Pending"  <?= ($this->session->userdata('search_status_transaction_qd') == 'Pending')  ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Created"  <?= ($this->session->userdata('search_status_transaction_qd') == 'Created')  ? 'selected' : ''; ?>>Created</option>
                                        <option value="Paid"     <?= ($this->session->userdata('search_status_transaction_qd') == 'Paid')     ? 'selected' : ''; ?>>Paid</option>
                                        <option value="Failed"   <?= ($this->session->userdata('search_status_transaction_qd') == 'Failed')   ? 'selected' : ''; ?>>Failed</option>
                                        <option value="Expired"  <?= ($this->session->userdata('search_status_transaction_qd') == 'Expired')  ? 'selected' : ''; ?>>Expired</option>
                                        <option value="Cancel"   <?= ($this->session->userdata('search_status_transaction_qd') == 'Cancel')   ? 'selected' : ''; ?>>Cancel</option>
                                    </select>
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

                   

                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="ewalletDynamicTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>DATE REQUEST</th>
                        <th>SUB MERCHANT INFO</th>
                        <th>CHANNEL ID</th>
                        <th>MERCHANT TRANS ID</th>
                        <th>EXTERNAL ID</th>
                        <th>AMOUNT</th>
                        <th>EXPIRED DATE</th>
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
                <!-- Guide Banner -->
                <div class="d-flex align-items-start pb-4" id="detail-guide-banner">
                    <div class="d-flex align-items-start p-3 w-100" style="background:rgba(78,115,223,0.06);border:1px solid rgba(78,115,223,0.12);border-radius:12px;">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;"><i class="fas fa-mobile-alt" style="font-size:13px;"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size:12px;color:var(--text-dark);">E-Wallet Dynamic Detail</h6>
                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">View dynamic e-wallet transaction details including payment amount, merchant routing, and channel external response data.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-3">
                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Provider</div>
                        <div class="h6 font-weight-bold text-dark mb-0" id="cashinExternalId"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 1</div>
                            <div class="d-flex align-items-start">
                                <div class="h6 font-weight-bold text-dark mb-0 text-break mr-2" style="word-break: break-all;" id="TransactionIdExternal1"></div>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 flex-shrink-0 copy-ref-btn" data-target="#TransactionIdExternal1" title="Copy ID" style="line-height: 1;">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 2</div>
                            <div class="d-flex align-items-start">
                                <div class="h6 font-weight-bold text-dark mb-0 text-break mr-2" style="word-break: break-all;" id="TransactionIdExternal2"></div>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 flex-shrink-0 copy-ref-btn" data-target="#TransactionIdExternal2" title="Copy ID" style="line-height: 1;">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        // Drawer Logic
        $('#toggleGuideBtn').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').addClass('open');
            $('body').css('overflow', 'hidden');
        });

        $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').removeClass('open');
            $('body').css('overflow', '');
        });

        // Init Server-Side DataTable
        var table = initServerDataTable("#ewalletDynamicTable", "<?= base_url('e-wallet/dynamic') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetimeRequest',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'name_submerchant',className: 'text-nowrap'},
            {data: 'ref_cashinChannelId',className: 'text-nowrap'},
            {data: 'c_merchantTransactionId', className: 'text-dark font-weight-bold text-nowrap'},
            {data: 'ref_cashinExternalId', className: 'text-nowrap', render: function(data, type, row) {
                return data ? data : '-';
            }},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
                return '<span class="font-weight-bold text-dark">Rp ' + Number(val).toLocaleString('id-ID') + '</span>';
            }},
            {data: 'c_datetimeExpired',className: 'text-nowrap', render: function(data){
                return data ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-';
            }},
            {
                data: 'c_status',
                className: 'text-center',
                render: function(data, type, row) {
                    var status_class = 'secondary';
                    var s = (data || '').toUpperCase();
                    if (s == 'PAID' || s == 'SUCCESS')        status_class = 'success';
                    else if (s == 'EXPIRED' || s == 'FAILED') status_class = 'danger';
                    else if (s == 'PENDING' || s == 'CREATED') status_class = 'warning';
                    else if (s == 'CANCEL')                    status_class = 'secondary';
                    
                    var badge = '<span class="badge badge-' + status_class + ' px-2 py-1">' + data.toUpperCase() + '</span>';
                    
                    if ((s == 'PAID' || s == 'SUCCESS') && row.ref_cashinExternalId) {
                        return '<a href="javascript:void(0)" class="detailEwalletDynamicChannelExternalAjax text-decoration-none" ' +
                               'data-merchanttransactionid="' + row.c_merchantTransactionId + '" ' +
                               'data-ref_cashinexternalid="' + row.ref_cashinExternalId + '" ' +
                               'data-ref_cashinexternallogewalletidcreate="' + row.ref_cashinExternalLogEwalletIdCreate + '">' +
                               badge + '</a>';
                    }
                    return badge;
                }
            }
        ]);

        // Global search with Debounce
        $('#ewalletDynamicGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // Trigger initial search if value exists (Deep Linking)
        var initSearch = $('#ewalletDynamicGlobalSearch').val();
        if (initSearch) {
            table.search(initSearch).draw();
        }

        // Select2 inside toolbar
        $('.ewallet-dynamic-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('.dt-toolbar'),
            minimumResultsForSearch: 5
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

        // Prevent panel close when clicking inside
        $morePanel.on('click', function(e) {
            e.stopPropagation();
        });

        // Modal AJAX Detail
        $(document).on('click', '.detailEwalletDynamicChannelExternalAjax', function(e) {
            e.preventDefault();
            var merchantTransactionId = $(this).data('merchanttransactionid');
            var ref_cashinExternalId = $(this).data('ref_cashinexternalid'); 
            var ref_cashinExternalLogEwalletIdCreate = $(this).data('ref_cashinexternallogewalletidcreate'); 
            // Handle potential variations in data attribute naming by jQuery
            if (!ref_cashinExternalLogEwalletIdCreate) {
                ref_cashinExternalLogEwalletIdCreate = $(this).attr('data-ref_cashinExternalLogEwalletIdCreate');
            }

            $('#detailQrisDynamicChannelExternalModalLabel').html('External Log - ' + merchantTransactionId);
            
            // Reset values to Loading/placeholder
            $('#cashinExternalId, #TransactionIdExternal1, #TransactionIdExternal2, #RequestDatetime, #ResponseDatetime').text('...');
            $('#RequestHeader, #RequestBody, #ResponseHeader, #ResponseBody').text('Loading...');

            $.ajax({
                url: "<?php echo base_url('e-wallet/dynamic/channel/external'); ?>",
                method: "POST",
                data: {
                    ref_cashinExternalId: ref_cashinExternalId,
                    ref_cashinExternalLogEwalletIdCreate: ref_cashinExternalLogEwalletIdCreate,
                    "<?= $this->security->get_csrf_token_name(); ?>": "<?= $this->security->get_csrf_hash(); ?>"
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


