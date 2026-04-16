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
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="bifastMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced filters</span>
                                <a href="<?= base_url('admin/resetbi_fast'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_date_bifast" class="dt-chip-input" value="<?= $date_from_val; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_bifast_to" class="dt-chip-input" value="<?= $date_to_val; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Merchant -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
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
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Transaction Status</label>
                                    <select name="search_status_transaction_bifast" class="dt-more-select">
                                        <option value="">All Statuses</option>
                                        <?php foreach(['Pending', 'Process', 'Success', 'Failed', 'Init', 'Timeout'] as $st): ?>
                                            <option value="<?= $st; ?>" <?= ($status_val == $st) ? 'selected' : ''; ?>><?= $st; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Trans ID -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-hashtag mr-1 mr-2"></i> Merchant Trans ID</label>
                                    <input type="text" name="search_transid_bifast" class="dt-more-input" placeholder="TRX-..." value="<?= $transid_val; ?>">
                                </div>

                                <!-- Channel -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-network-wired mr-1 mr-2"></i> External Channel</label>
                                    <select name="search_channel_bifast" class="dt-more-select">
                                        <option value="">All Channels</option>
                                        <?php foreach ($channels as $ch): ?>
                                            <option value="<?= $ch->c_cashoutExternalId; ?>" <?= ($ch->c_cashoutExternalId == $channel_val) ? 'selected' : ''; ?>><?= $ch->c_cashoutExternalId; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- External Reff -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-fingerprint mr-1 mr-2"></i> External Reff ID</label>
                                    <input type="text" name="search_external_reff_id" class="dt-more-input" placeholder="REF-..." value="<?= $external_val; ?>">
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <!-- Header -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title" id="detailBiFastChannelExternalModalLabel">Detail Disbursement</h6>
            <small class="mh-subtitle" id="detailBiFastSubtitle">Channel External</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

            <div class="modal-body p-0">

                <!-- ── Section 1: Transaction Info ── -->
                <div class="px-4 pt-4 pb-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="" style="width:6px; height:18px; background: var(--primary); border-radius: 3px;"></div>
                        <span class="font-weight-bold text-dark" style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Transaction Information</span>
                    </div>

                    <!-- Channel Badge -->
                    <div class="mb-3">
                        <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Channel External</label>
                        <div style="display:inline-flex; align-items:center; background:var(--primary-soft,#eef2ff); border:1.5px solid var(--primary,#4e73df); border-radius:8px; padding:7px 14px; min-width:160px;">
                            <i class="fas fa-network-wired " style="color:var(--primary,#4e73df); font-size:13px;"></i>
                            <span id="cashoutExternalId" class="font-weight-bold" style="font-size:13px; color:var(--primary,#4e73df);">—</span>
                        </div>
                    </div>

                    <!-- Transaction IDs -->
                    <div class="row" style="gap:0;">
                        <div class="col-md-6 mb-3">
                            <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Transaction ID 1</label>
                            <div style="display:flex; align-items:center; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px; padding:8px 12px;">
                                <i class="fas fa-hashtag text-muted " style="font-size:11px;"></i>
                                <input type="text" id="TransactionIdExternal1" class="border-0 bg-transparent p-0 text-dark font-weight-bold" style="font-size:12.5px; outline:none; width:100%;" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Transaction ID 2</label>
                            <div style="display:flex; align-items:center; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px; padding:8px 12px;">
                                <i class="fas fa-hashtag text-muted " style="font-size:11px;"></i>
                                <input type="text" id="TransactionIdExternal2" class="border-0 bg-transparent p-0 text-dark font-weight-bold" style="font-size:12.5px; outline:none; width:100%;" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mx-4 my-0" style="border-color:#f0f2f8;">

                <!-- ── Section 2: Request ── -->
                <div class="px-4 pt-3 pb-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="" style="width:6px; height:18px; background:#1cc88a; border-radius:3px;"></div>
                        <span class="font-weight-bold text-dark" style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Request</span>
                    </div>

                    <!-- Request DateTime -->
                    <div class="mb-3">
                        <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-clock mr-1 mr-2"></i> Date Time</label>
                        <div style="display:flex; align-items:center; background:#f0fff8; border:1.5px solid #b2f5e0; border-radius:8px; padding:8px 12px;">
                            <i class="fas fa-calendar-check " style="color:#1cc88a; font-size:12px;"></i>
                            <input type="text" id="RequestDatetime" class="border-0 bg-transparent p-0 font-weight-bold" style="font-size:12.5px; outline:none; width:100%; color:#0d7a52;" readonly>
                        </div>
                    </div>

                    <!-- Request Header -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="text-muted m-0" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-code mr-1 mr-2"></i> Header</label>
                            <button type="button" class="btn-copy-json" data-target="RequestHeaderCode" style="background:none; border:none; color:#1cc88a; font-size:11px; font-weight:600; cursor:pointer; padding:0;">
                                <i class="fas fa-copy mr-1 mr-2"></i> Copy
                            </button>
                        </div>
                        <div style="background:#1a1d2e; border-radius:10px; overflow:hidden;">
                            <div style="padding:6px 14px; background:#12152a; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#ff5f57; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#febc2e; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#28c840; display:inline-block;"></span>
                                <span class=" text-muted" style="font-size:10px;">request-header.json</span>
                            </div>
                            <pre id="RequestHeaderCode" style="margin:0; padding:12px 16px; font-size:11.5px; color:#a8d8a8; font-family:'Courier New',monospace; max-height:100px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"></pre>
                        </div>
                    </div>

                    <!-- Request Body -->
                    <div class="mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="text-muted m-0" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-file-code mr-1 mr-2"></i> Body</label>
                            <button type="button" class="btn-copy-json" data-target="RequestBodyCode" style="background:none; border:none; color:#1cc88a; font-size:11px; font-weight:600; cursor:pointer; padding:0;">
                                <i class="fas fa-copy mr-1 mr-2"></i> Copy
                            </button>
                        </div>
                        <div style="background:#1a1d2e; border-radius:10px; overflow:hidden;">
                            <div style="padding:6px 14px; background:#12152a; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#ff5f57; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#febc2e; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#28c840; display:inline-block;"></span>
                                <span class=" text-muted" style="font-size:10px;">request-body.json</span>
                            </div>
                            <pre id="RequestBodyCode" style="margin:0; padding:12px 16px; font-size:11.5px; color:#a8d8a8; font-family:'Courier New',monospace; max-height:160px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"></pre>
                        </div>
                    </div>
                </div>

                <hr class="mx-4 my-0" style="border-color:#f0f2f8;">

                <!-- ── Section 3: Response ── -->
                <div class="px-4 pt-3 pb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="" style="width:6px; height:18px; background:#f6c23e; border-radius:3px;"></div>
                        <span class="font-weight-bold text-dark" style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Response</span>
                    </div>

                    <!-- Response DateTime -->
                    <div class="mb-3">
                        <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-clock mr-1 mr-2"></i> Date Time</label>
                        <div style="display:flex; align-items:center; background:#fffbf0; border:1.5px solid #fde68a; border-radius:8px; padding:8px 12px;">
                            <i class="fas fa-calendar-check " style="color:#f6c23e; font-size:12px;"></i>
                            <input type="text" id="ResponseDatetime" class="border-0 bg-transparent p-0 font-weight-bold" style="font-size:12.5px; outline:none; width:100%; color:#92701a;" readonly>
                        </div>
                    </div>

                    <!-- Response Header -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="text-muted m-0" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-code mr-1 mr-2"></i> Header</label>
                            <button type="button" class="btn-copy-json" data-target="ResponseHeaderCode" style="background:none; border:none; color:#f6c23e; font-size:11px; font-weight:600; cursor:pointer; padding:0;">
                                <i class="fas fa-copy mr-1 mr-2"></i> Copy
                            </button>
                        </div>
                        <div style="background:#1a1d2e; border-radius:10px; overflow:hidden;">
                            <div style="padding:6px 14px; background:#12152a; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#ff5f57; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#febc2e; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#28c840; display:inline-block;"></span>
                                <span class=" text-muted" style="font-size:10px;">response-header.json</span>
                            </div>
                            <pre id="ResponseHeaderCode" style="margin:0; padding:12px 16px; font-size:11.5px; color:#fde68a; font-family:'Courier New',monospace; max-height:100px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"></pre>
                        </div>
                    </div>

                    <!-- Response Body -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="text-muted m-0" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-file-code mr-1 mr-2"></i> Body</label>
                            <button type="button" class="btn-copy-json" data-target="ResponseBodyCode" style="background:none; border:none; color:#f6c23e; font-size:11px; font-weight:600; cursor:pointer; padding:0;">
                                <i class="fas fa-copy mr-1 mr-2"></i> Copy
                            </button>
                        </div>
                        <div style="background:#1a1d2e; border-radius:10px; overflow:hidden;">
                            <div style="padding:6px 14px; background:#12152a; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#ff5f57; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#febc2e; display:inline-block;"></span>
                                <span style="width:8px; height:8px; border-radius:50%; background:#28c840; display:inline-block;"></span>
                                <span class=" text-muted" style="font-size:10px;">response-body.json</span>
                            </div>
                            <pre id="ResponseBodyCode" style="margin:0; padding:12px 16px; font-size:11.5px; color:#fde68a; font-family:'Courier New',monospace; max-height:160px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"></pre>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 px-4 py-3" style="background:#f8fafc; border-top:1px solid #edf2f9 !important;">
                <button type="button" class="btn-dt-cancel" data-dismiss="modal">
                    <i class="fas fa-times mr-1 mr-2"></i> Close
                </button>
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
                "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No transactions found.</div>'
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
                        '<i class="fas fa-chevron-left mr-2"></i> PREVIOUS' +
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

            $('#detailBiFastChannelExternalModalLabel').text('Detail Disbursement');
            $('#detailBiFastSubtitle').text(merchantTransactionId);
            
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
                    alert('Failed to load transaction details.');
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



