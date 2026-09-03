<!-- Begin Page Content -->
<div>
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> BI-FAST Transactions Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Track outbound real-time bank transfers and disbursements through the BI-FAST network.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-university text-primary mr-2"></i> Destination Details</div>
                <p class="drawer-card-text">Shows recipient bank, account number, account holder name, and transfer amount.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-exchange-alt text-primary mr-2"></i> Status Flow</div>
                <p class="drawer-card-text">Track state transitions from Pending/In Process to Success, Failed, or Reversed.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search-plus text-primary mr-2"></i> Re-inquiry</div>
                <p class="drawer-card-text">Perform status checks directly against the bank gateway to resolve hanging transactions.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">BI-FAST Transactions</h4>
            <p class="dt-page-subtitle">Monitor and manage all disbursement activities through BI-FAST.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card dt-card border-0 shadow-sm">
        <form id="bifast_form" method="post" action="<?= base_url('finance/bi-fast'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_bifast_search = $this->session->userdata('last_dt_search_bifast') ?: ''; ?>
                    <input type="text" id="bifastGlobalSearch" class="dt-search-input" placeholder="Search by Trans ID, Invoice, Account No, or Beneficiary Name..." value="<?= $active_bifast_search; ?>">
                </div>

                <div class="dt-toolbar-filters">
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="bifastMoreFiltersBtn" class="dt-more-filters-btn">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <div class="dt-more-panel" id="bifastMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <button type="button" class="close" id="bifastMoreFiltersClose">&times;</button>
                            </div>

                            <div class="dt-more-panel-body">
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar mr-1 mr-2"></i> Date Range</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="search_bifast_date1" class="form-control form-control-sm" value="<?= $this->session->userdata('search_bifast_date1'); ?>">
                                        <input type="date" name="search_bifast_date2" class="form-control form-control-sm" value="<?= $this->session->userdata('search_bifast_date2'); ?>">
                                    </div>
                                </div>
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                    <select name="search_bifast_name" class="bifast-select2 form-control">
                                        <option value="">All Merchants</option>
                                        <?php foreach ($merchants as $m): ?>
                                            <option value="<?= $m->id ?>" <?= $this->session->userdata('search_bifast_name') == $m->id ? 'selected' : ''; ?>><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                    <select name="search_bifast_status" class="bifast-select2 form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Success" <?= $this->session->userdata('search_bifast_status') == 'Success' ? 'selected' : ''; ?>>Success</option>
                                        <option value="Pending" <?= $this->session->userdata('search_bifast_status') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Failed" <?= $this->session->userdata('search_bifast_status') == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search mr-1"></i> Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="bifastTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>DATETIME</th>
                        <th>MERCHANT</th>
                        <th>TRANS ID</th>
                        <th>INVOICE</th>
                        <th>PROVIDER</th>
                        <th>CHANNEL</th>
                        <th>ACCOUNT NO</th>
                        <th>BENEFICIARY</th>
                        <th>AMOUNT</th>
                        <th>FEE</th>
                        <th>STATUS</th>
                        <th>RESPONSE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Init Server-Side DataTable
        var table = initServerDataTable("#bifastTable", "<?= base_url('finance/bi-fast') ?>", [
            {data: 'no', orderable: false},
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {
                data: 'merchant_name',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return ' [' + row.ref_merchantId + '] - ' + data;
                }
            },
            {data: 'c_merchantTransactionId',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {
                data: 'ref_cashoutExternalId',
                className: 'text-nowrap',
                render: function(data) {
                    if (data) {
                        return '<span class="badge badge-primary">' + data.toUpperCase() + '</span>';
                    }
                    return '-';
                }
            },
            {
                data: 'ref_cashoutChannelId',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    if (row.channel_description) {
                        return '<div class="font-weight-bold text-dark">' + row.channel_description + '</div>' +
                               '<small class="text-muted">' + data + '</small>';
                    }
                    return data;
                }
            },
            {data: 'c_accountNo',className: 'text-nowrap'},
            {data: 'c_beneficiaryAccountName',className: 'text-nowrap'},
            {data: 'c_amount',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_fee',className: 'text-nowrap', render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }},
            {data: 'c_status',className: 'text-nowrap', render: function(data) {
                var badge = 'badge-secondary';
                if(data == 'Success') badge = 'badge-success';
                else if(data == 'Failed') badge = 'badge-danger';
                else if(data == 'Pending') badge = 'badge-primary';
                return '<span class="badge badge-pill ' + badge + '">' + data + '</span>';
            }},
            {
                data: 'c_responseBody',
                className: 'text-nowrap',
                render: function(data) {
                    if (!data) return '-';
                    try {
                        var decoded = typeof data === 'string' ? JSON.parse(data) : data;
                        return decoded.responseMessage || decoded.message || '-';
                    } catch(e) {
                        return '-';
                    }
                }
            },
            {
                data: 'id', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var baseUrl = "<?= base_url() ?>";
                    var detailLink = baseUrl + 'finance/bi-fast/detail/' + data;
                    
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg">
                                <li><a href="${detailLink}" class="dropdown-item"><i class="fas fa-eye text-primary mr-2"></i> Detail</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item btn-info-request" href="#" 
                                    data-merchantTransactionId="${row.c_merchantTransactionId}" 
                                    data-ref_cashoutExternalId="${row.ref_cashoutExternalId}" 
                                    data-ref_cashoutExternalLogBifastId="${row.ref_cashoutExternalLogBifastId}">
                                    <i class="fas fa-info-circle text-info mr-2"></i> Info Request</a></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ], {
            "order": [[2, 'desc']],
            "search": {
                "search": "<?= $this->session->userdata('last_dt_search_bifast') ?: '' ?>"
            }
        });

        // Global search
        $('#bifastGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

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
        $('.bifast-select2').each(function () {
            $(this).select2({
                width: '100%',
                dropdownAutoWidth: true,
                dropdownParent: $('body'),
                minimumResultsForSearch: 5
            });
        });

        // Dynamic Filtering for Internal Channel based on External Channel
        var channelMappings = <?= json_encode($channel_mappings ?? []) ?>;
        var selectedInternalChannel = "<?= $internal_channel_val ?>";
        var allInternalChannels = [
            <?php foreach($internal_channels as $ic): ?>
                {id: "<?= $ic->id ?>", text: "<?= $ic->c_description ?>"},
            <?php endforeach; ?>
        ];

        $('#search_channel_bifast').on('change', function() {
            var selectedExternal = $(this).val();
            var $internalSelect = $('#search_internal_channel_bifast');
            
            // clear options
            $internalSelect.empty();
            $internalSelect.append(new Option("All Channels", "", false, false));
            
            if (selectedExternal) {
                $internalSelect.prop('disabled', false);
                // filter internal channels
                var validInternalIds = channelMappings.filter(function(m) {
                    return m.c_cashoutExternalId == selectedExternal;
                }).map(function(m) {
                    return m.ref_cashoutChannelId;
                });
                
                var validChannels = allInternalChannels.filter(function(c) {
                    return validInternalIds.includes(c.id);
                });
                
                validChannels.forEach(function(c) {
                    var isSelected = (c.id == selectedInternalChannel);
                    $internalSelect.append(new Option(c.text, c.id, false, isSelected));
                });
            } else {
                $internalSelect.prop('disabled', true);
                // show all
                allInternalChannels.forEach(function(c) {
                    var isSelected = (c.id == selectedInternalChannel);
                    $internalSelect.append(new Option(c.text, c.id, false, isSelected));
                });
            }
            // re-init select2
            $internalSelect.trigger('change.select2');
        });
        
        // Trigger on load to set initial state
        $('#search_channel_bifast').trigger('change');

        // Detail Modal Ajax
        $(document).on('click', '.btn-info-request', function(e) {
            e.preventDefault();
            var merchantTransactionId = $(this).data('merchanttransactionid');
            var ref_cashoutExternalId = $(this).data('ref_cashoutexternalid'); 
            var ref_cashoutExternalLogBifastId = $(this).data('ref_cashoutexternallogbifastid'); 

            $('#detailBiFastChannelExternalModalLabel').text('Detail Disbursement');
            $('#detailBiFastSubtitle').text(merchantTransactionId);
            
            $.ajax({
                url: "<?php echo base_url('finance/bi-fast/channel/external'); ?>",
                method: "POST",
                data: {
                    ref_cashoutExternalId: ref_cashoutExternalId,
                    ref_cashoutExternalLogBifastId: ref_cashoutExternalLogBifastId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
                },
                dataType: "json",
                success: function(response) {
                    // Transaction Info
                    $('#cashoutExternalId').text(ref_cashoutExternalId || '—');
                    $('#TransactionIdExternal1').val(response.TransactionIdExternal1 || '—');
                    $('#TransactionIdExternal2').val(response.TransactionIdExternal2 || '—');

                    // Request section
                    $('#RequestDatetime').val(response.RequestDatetime || '—');
                    $('#RequestHeaderCode').text(JSON.stringify(response.RequestHeader, null, 2));
                    $('#RequestBodyCode').text(JSON.stringify(response.RequestBody, null, 2));

                    // Response section
                    $('#ResponseDatetime').val(response.ResponseDatetime || '—');
                    $('#ResponseHeaderCode').text(JSON.stringify(response.ResponseHeader, null, 2));
                    $('#ResponseBodyCode').text(JSON.stringify(response.ResponseBody, null, 2));

                    $('#detailBiFastChannelExternalModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load transaction details.',
                        icon: 'error',
                        customClass: {
                            popup: 'swal2-premium-popup',
                            confirmButton: 'swal2-premium-confirm'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        // Copy-to-clipboard for code blocks
        $(document).on('click', '.btn-copy-json', function() {
            var targetId = $(this).data('target');
            var text = $('#' + targetId).text();
            var $btn = $(this);

            if (navigator.clipboard && text) {
                navigator.clipboard.writeText(text).then(function() {
                    $btn.html('<i class="fas fa-check mr-1 mr-2"></i> Copied!');
                    setTimeout(function() {
                        $btn.html('<i class="fas fa-copy mr-1 mr-2"></i> Copy');
                    }, 1800);
                });
            }
        });
    });
</script>
<!-- /.container-fluid -->

<script>
$(document).ready(function() {
    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/bifast_list.js'); ?>"></script>
