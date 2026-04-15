<!-- Begin Page Content -->
<div class="container-fluid pb-4">

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">BI-FAST Transactions</h4>
            <p class="dt-page-subtitle">Monitor and manage all disbursement activities through BI-FAST.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php
                $download_url = base_url('admin/download_bi_fast') 
                    . "?search_date_bifast=" . ($this->session->userdata('search_date_bifast') ?: '')
                    . "&search_date_bifast_to=" . ($this->session->userdata('search_date_bifast_to') ?: '')
                    . "&search_name_bifast=" . ($this->session->userdata('search_name_bifast') ?: '')
                    . "&search_status_transaction_bifast=" . ($this->session->userdata('search_status_transaction_bifast') ?: '');
            ?>
            
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <?php
            // Session values already loaded above in the file, but let's re-ensure clean local vars
            $date_from_val = $this->session->userdata('search_date_bifast') ?: '';
            $date_to_val   = $this->session->userdata('search_date_bifast_to') ?: '';
            $merchant_val  = $this->session->userdata('search_name_bifast') ?: '';
            $status_val    = $this->session->userdata('search_status_transaction_bifast') ?: '';
            $transid_val   = $this->session->userdata('search_transid_bifast') ?: '';
            $channel_val   = $this->session->userdata('search_channel_bifast') ?: '';
            $external_val  = $this->session->userdata('search_external_reff_id') ?: '';

            // Badge count for More Filters (Anything except Date Range)
            $extra_active = 0;
            if ($merchant_val)  $extra_active++;
            if ($status_val)    $extra_active++;
            if ($transid_val)   $extra_active++;
            if ($channel_val)   $extra_active++;
            if ($external_val)  $extra_active++;
        ?>

        <!-- ── Toolbar ── -->
        <form id="bifast_form" method="post" action="<?= base_url('admin/bi_fast'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="bifastGlobalSearch" class="dt-search-input" placeholder="Search by any parameter...">
                </div>

                <!-- RIGHT: Primary chips + More Filters trigger -->
                <div class="dt-toolbar-filters">

                    

                    <!-- More Filters trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="bifastMoreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="bifastMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced filters</span>
                                <a href="<?= base_url('admin/resetbi_fast'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1"></i>Period</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_bifast" class="dt-chip-input" value="<?= $date_from_val; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_bifast_to" class="dt-chip-input" value="<?= $date_to_val; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1"></i>Merchant</label>
                                    <select name="search_name_bifast" class="dt-more-select bifast-select2">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $merchant): ?>
                                            <option value="<?= $merchant->id; ?>" <?= ($merchant->id == $merchant_val) ? 'selected' : ''; ?>>
                                                [<?= $merchant->id; ?>] <?= $merchant->c_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1"></i>Transaction Status</label>
                                    <select name="search_status_transaction_bifast" class="dt-more-select">
                                        <option value="">All Statuses</option>
                                        <?php foreach(['Pending', 'Process', 'Success', 'Failed', 'Init', 'Timeout'] as $st): ?>
                                            <option value="<?= $st; ?>" <?= ($status_val == $st) ? 'selected' : ''; ?>><?= $st; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Trans ID -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-hashtag mr-1"></i>Merchant Trans ID</label>
                                    <input type="text" name="search_transid_bifast" class="dt-more-input" placeholder="TRX-..." value="<?= $transid_val; ?>">
                                </div>

                                <!-- Channel -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-network-wired mr-1"></i>External Channel</label>
                                    <select name="search_channel_bifast" class="dt-more-select">
                                        <option value="">All Channels</option>
                                        <?php foreach ($channels as $ch): ?>
                                            <option value="<?= $ch->c_cashoutExternalId; ?>" <?= ($ch->c_cashoutExternalId == $channel_val) ? 'selected' : ''; ?>><?= $ch->c_cashoutExternalId; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- External Reff -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-fingerprint mr-1"></i>External Reff ID</label>
                                    <input type="text" name="search_external_reff_id" class="dt-more-input" placeholder="REF-..." value="<?= $external_val; ?>">
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1"></i> APPLY FILTER
                                </button>
                                <button type="button" id="bifastMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div><!-- /dt-more-panel -->
                    </div>

                    <!-- Always-visible Actions -->
                    <a href="<?= $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success ">
                        <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                    </a>    
                    

                </div><!-- /.dt-toolbar-filters -->
            </div><!-- /.dt-toolbar -->
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="bifastTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Merchant</th>
                        <th>Date Request</th>
                        <th>Invoice No</th>
                        <th>Merchant Trans ID</th>
                        <th>Channel</th>
                        <th>Account No</th>
                        <th>Beneficiary Name</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Information</th>
                        <th style="min-width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailBiFastChannelExternalModal" tabindex="-1" aria-labelledby="detailBiFastChannelExternalModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-primary border-0 py-3">
                <h5 class="modal-title font-weight-bold text-white" id="detailBiFastChannelExternalModalLabel"><i class="fas fa-info-circle mr-2"></i>Detail Disbursement - Channel External</h5>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body shadow-sm">
                <div class="table-responsive">
                    <table class="table dt-table table-borderless small mb-0">
                        <tbody>
                            <tr>
                                <td width="30%" class="font-weight-bold align-middle">Channel External</td>
                                <td><input type="text" id="cashoutExternalId" class="dt-more-input bg-light" readonly></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Transaction ID 1</td>
                                <td><input type="text" id="TransactionIdExternal1" class="dt-more-input bg-light" readonly></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Transaction ID 2</td>
                                <td><input type="text" id="TransactionIdExternal2" class="dt-more-input bg-light" readonly></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Request Date Time</td>
                                <td><input type="text" id="RequestDatetime" class="dt-more-input bg-light" readonly></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Request Header</td>
                                <td><textarea id="RequestHeader" class="dt-more-input bg-light" rows="2" readonly></textarea></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Request Body</td>
                                <td><textarea id="RequestBody" class="dt-more-input bg-light" rows="4" readonly></textarea></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Response Date Time</td>
                                <td><input type="text" id="ResponseDatetime" class="dt-more-input bg-light" readonly></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Response Header</td>
                                <td><textarea id="ResponseHeader" class="dt-more-input bg-light" rows="2" readonly></textarea></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold align-middle">Response Body</td>
                                <td><textarea id="ResponseBody" class="dt-more-input bg-light" rows="4" readonly></textarea></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Init Server-Side DataTable
        var table = initServerDataTable("#bifastTable", "<?= base_url('admin/bi_fast') ?>", [
            {data: 'no', orderable: false},
            {data: 'merchant_info',className: 'text-nowrap'},
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'c_merchantTransactionId',className: 'text-nowrap'},
            {data: 'ref_cashoutChannelId',className: 'text-nowrap'},
            {data: 'c_accountNo',className: 'text-nowrap'},
            {data: 'c_beneficiaryAccountName',className: 'text-nowrap'},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            }},
            {data: 'c_fee',className: 'text-nowrap', render: function(data){
                var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            }},
            {data: 'c_status',className: 'text-nowrap', render: function(data) {
                var badge = 'badge-secondary';
                if(data == 'Success') badge = 'badge-success';
                else if(data == 'Failed') badge = 'badge-danger';
                else if(data == 'Process' || data == 'Pending') badge = 'badge-primary';
                return '<span class="badge badge-pill ' + badge + '">' + data + '</span>';
            }},
            {data: 'parsedResponse',className: 'text-nowrap'},
            {data: 'action', orderable: false, searchable: false}
        ], {
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                "info": "Showing _START_ – _END_ of _TOTAL_ results",
                "infoEmpty": "No results to show",
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
        $('#bifastGlobalSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // ── More Filters dropdown ──
        var $moreBtn   = $('#bifastMoreFiltersBtn');
        var $morePanel = $('#bifastMoreFiltersPanel');
        var $moreClose = $('#bifastMoreFiltersClose');

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
        $('.bifast-select2').select2({
            width: '100%',
            dropdownParent: $morePanel,
            minimumResultsForSearch: 5
        });

        // Detail Modal Ajax
        $(document).on('click', '.btn-info-request', function(e) {
            e.preventDefault();
            var merchantTransactionId = $(this).data('merchanttransactionid');
            var ref_cashoutExternalId = $(this).data('ref_cashoutexternalid'); 
            var ref_cashoutExternalLogBifastId = $(this).data('ref_cashoutexternallogbifastid'); 

            $('#detailBiFastChannelExternalModalLabel').text('Detail Disbursement - ' + merchantTransactionId);
            
            $.ajax({
                url: "<?php echo base_url('admin/getDetailBiFastChannelExternal'); ?>",
                method: "POST",
                data: {
                    ref_cashoutExternalId: ref_cashoutExternalId,
                    ref_cashoutExternalLogBifastId: ref_cashoutExternalLogBifastId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
                },
                dataType: "json",
                success: function(response) {
                    $('#cashoutExternalId').val(ref_cashoutExternalId);
                    $('#TransactionIdExternal1').val(response.TransactionIdExternal1);
                    $('#TransactionIdExternal2').val(response.TransactionIdExternal2);
                    $('#RequestDatetime').val(response.RequestDatetime);
                    $('#RequestHeader').val(JSON.stringify(response.RequestHeader, null, 2));
                    $('#RequestBody').val(JSON.stringify(response.RequestBody, null, 2));
                    $('#ResponseDatetime').val(response.ResponseDatetime);
                    $('#ResponseHeader').val(JSON.stringify(response.ResponseHeader, null, 2));
                    $('#ResponseBody').val(JSON.stringify(response.ResponseBody, null, 2));
                    $('#detailBiFastChannelExternalModal').modal('show');
                },
                error: function() {
                    alert('Failed to load transaction details.');
                }
            });
        });
    });
</script>
<!-- /.container-fluid -->
