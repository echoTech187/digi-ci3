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
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-ban text-primary mr-2"></i> Daily Limits</div>
                <p class="drawer-card-text">Ensure disbursement volumes fit within per-transaction and cumulative daily limits.</p>
            </div>
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

            <?php 
            $errorMsg = $this->session->flashdata('error') ?: $this->session->flashdata('error_message');
            if ($errorMsg) : 
            ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= trim(str_replace(["\r", "\n"], '', $errorMsg)); ?>',
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
            <?php
                $download_url = base_url('finance/bi-fast/download') 
                    . "?search_date_bifast=" . ($this->session->userdata('search_date_bifast') ?: '')
                    . "&search_date_bifast_to=" . ($this->session->userdata('search_date_bifast_to') ?: '')
                    . "&search_name_bifast=" . ($this->session->userdata('search_name_bifast') ?: '')
                    . "&search_status_transaction_bifast=" . ($this->session->userdata('search_status_transaction_bifast') ?: '');
            ?>
            
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card dt-card border-0 shadow-sm">

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

            // Badge count for More Filters (Exclude those moved to global search)
            $extra_active = 0;
            if ($merchant_val)  $extra_active++;
            if ($status_val)    $extra_active++;
            if ($channel_val)   $extra_active++;
        ?>

        <!-- ── Toolbar ── -->
        <form id="bifast_form" method="post" action="<?= base_url('finance/bi-fast'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="bifastGlobalSearch" class="dt-search-input" placeholder="<?= $transid_val ?: 'Search by Trans ID, Invoice, or Account No...'; ?>" value="<?= $transid_val; ?>">
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
                                <a href="<?= base_url('finance/bi-fast/reset'); ?>" class="dt-more-clear">Clear All</a>
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
                                    <select name="search_status_transaction_bifast" class="dt-more-select bifast-select2">
                                        <option value="">All Statuses</option>
                                        <?php foreach(['Pending', 'Process', 'Success', 'Failed', 'Init', 'Timeout'] as $st): ?>
                                            <option value="<?= $st; ?>" <?= ($status_val == $st) ? 'selected' : ''; ?>><?= $st; ?></option>
                                        <?php endforeach; ?>
                                    </select>
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table dt-table mb-0" id="bifastTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Merchant</th>
                            <th>Date Request</th>
                            <th>Merchant Trans ID</th>
                            <th>Invoice No</th>
                            <th>Channel</th>
                            <th>Account No</th>
                            <th>Beneficiary Name</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Information</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
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
                <!-- Guide Banner -->
                <div class="d-flex align-items-start pb-4" id="detail-guide-banner">
                    <div class="d-flex align-items-start p-3 w-100" style="background:rgba(78,115,223,0.06);border:1px solid rgba(78,115,223,0.12);border-radius:12px;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;"><i class="fas fa-paper-plane" style="font-size:13px;"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size:12px;color:var(--text-dark);">BI-FAST Detail Guide</h6>
                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">Review disbursement transaction details including amount, channel routing, beneficiary information, and real-time status from the BI-FAST network.</p>
                        </div>
                    </div>
                </div>


                <!-- ── Section 1: Transaction Info ── -->
                <div class="px-4 pt-4 pb-3">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:6px; height:18px; background: var(--primary); border-radius: 3px;"></div>
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
                            <div style="display:flex; align-items:center; background:var(--bg-body); border:1.5px solid var(--border-color); border-radius:8px; padding:8px 12px;">
                                <i class="fas fa-hashtag text-muted " style="font-size:11px;"></i>
                                <input type="text" id="TransactionIdExternal1" class="border-0 bg-transparent p-0 text-gray-900 font-weight-bold" style="font-size:12.5px; outline:none; width:100%;" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Transaction ID 2</label>
                            <div style="display:flex; align-items:center; background:var(--bg-body); border:1.5px solid var(--border-color); border-radius:8px; padding:8px 12px;">
                                <i class="fas fa-hashtag text-muted " style="font-size:11px;"></i>
                                <input type="text" id="TransactionIdExternal2" class="border-0 bg-transparent p-0 text-gray-900 font-weight-bold" style="font-size:12.5px; outline:none; width:100%;" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mx-4 my-0" style="border-color:#f0f2f8;">

                <!-- ── Section 2: Request ── -->
                <div class="px-4 pt-3 pb-3">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:6px; height:18px; background:#1cc88a; border-radius:3px;"></div>
                        <span class="font-weight-bold text-dark" style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Request</span>
                    </div>

                    <!-- Request DateTime -->
                    <div class="mb-3">
                        <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-clock mr-1 mr-2"></i> Date Time</label>
                        <div style="display:flex; align-items:center; background:var(--success-soft); border:1.5px solid var(--success); border-radius:8px; padding:8px 12px; opacity: 0.85;">
                            <i class="fas fa-calendar-check " style="color:var(--success); font-size:12px;"></i>
                            <input type="text" id="RequestDatetime" class="border-0 bg-transparent p-0 font-weight-bold" style="font-size:12.5px; outline:none; width:100%; color:var(--success);" readonly>
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
                        <div style="width:6px; height:18px; background:#f6c23e; border-radius:3px;"></div>
                        <span class="font-weight-bold text-dark" style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Response</span>
                    </div>

                    <!-- Response DateTime -->
                    <div class="mb-3">
                        <label class="d-block text-muted mb-1" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="fas fa-clock mr-1 mr-2"></i> Date Time</label>
                        <div style="display:flex; align-items:center; background:var(--warning-soft); border:1.5px solid var(--warning); border-radius:8px; padding:8px 12px; opacity: 0.85;">
                            <i class="fas fa-calendar-check " style="color:var(--warning); font-size:12px;"></i>
                            <input type="text" id="ResponseDatetime" class="border-0 bg-transparent p-0 font-weight-bold" style="font-size:12.5px; outline:none; width:100%; color:var(--warning);" readonly>
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
            <div class="modal-footer border-0 px-4 py-3" style="background:var(--bg-card); border-top:1px solid var(--border-color) !important;">
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
        var table = initServerDataTable("#bifastTable", "<?= base_url('finance/bi-fast') ?>", [
            {data: 'no', orderable: false},
            {
                data: 'name_merchant',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    return ' [' + row.ref_merchantId + '] - ' + data;
                }
            },
            {data: 'c_datetime',className: 'text-nowrap', render: function(data){
                return moment(data).format('DD-MM-YYYY HH:mm:ss');
            }},
            {data: 'c_merchantTransactionId',className: 'text-nowrap'},
            {data: 'c_invoiceNo',className: 'text-nowrap'},
            {data: 'ref_cashoutChannelId',className: 'text-nowrap'},
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
                else if(data == 'Process' || data == 'Pending') badge = 'badge-primary';
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
                "search": "<?= $transid_val ?>"
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
        $('.bifast-select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
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

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
