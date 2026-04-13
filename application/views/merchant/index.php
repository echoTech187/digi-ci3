<div class="container-fluid pb-4">

    <!-- Page Header -->
    <div class="dt-page-header">
        <div>
            <h1 class="dt-page-title">Merchant Management</h1>
            <p class="dt-page-subtitle">View and manage all registered merchants and their balances.</p>
        </div>
    </div>

    <!-- Summary Cards (Refined for Symmetry) -->
    <div class="dt-summary-row mb-4">
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL MERCHANTS</div>
                <div class="dt-summary-value"><?= isset($total_merchants) ? number_format($total_merchants, 0, ',', '.') : 0 ?></div>
                <div class="dt-summary-sub"><i class="fas fa-users mr-1"></i>Registered accounts</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-store"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL BALANCE</div>
                <div class="dt-summary-value">Rp <?= number_format($total_balance, 0, ',', '.') ?></div>
                <div class="dt-summary-sub"><i class="fas fa-wallet mr-1"></i>Combined funds</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-university"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-orange">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL HOLD</div>
                <div class="dt-summary-value">Rp <?= number_format($total_hold, 0, ',', '.') ?></div>
                <div class="dt-summary-sub"><i class="fas fa-lock mr-1"></i>Funds on hold</div>
            </div>
            <div class="dt-summary-icon dt-icon-orange">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-yellow">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL AVAILABLE</div>
                <div class="dt-summary-value">Rp <?= number_format($total_available, 0, ',', '.') ?></div>
                <div class="dt-summary-sub"><i class="fas fa-check-circle mr-1"></i>Ready for payout</div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Data Table Card -->
    <div class="card dt-card border-0 shadow-sm">
        <!-- Toolbar -->
        <form id="merchant_search_form" method="post" action="<?= base_url('admin/merchant'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="merchantGlobalSearch" class="dt-search-input" placeholder="Search by name, ID, or email..." value="<?= $this->session->userdata('search_merchant'); ?>">
                </div>

                <!-- RIGHT: Filters & Actions -->
                <div class="dt-toolbar-filters">
                    <div class="dt-filter-group">
                        <label class="dt-filter-label">&nbsp;</label>
                        <div class="d-flex" style="gap:6px;">
                            <a href="<?= base_url('admin/resetMerchant'); ?>" class="btn-dt-chip-action btn-dt-secondary">
                                <i class="fas fa-undo mr-1"></i>
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn-dt-action btn-dt-action-success border-0" data-toggle="modal" data-target="#registerMerchabntModal">
                        <i class="fas fa-plus"></i> <span class="d-none d-md-block">Add Merchant</span>
                    </button>
                </div>
            </div>
        </form>

        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="merchantTable" class="table dt-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Merchant ID</th>
                            <th>Merchant Info</th>
                            <th>Balance Summary</th>
                            <th>Status Details</th>
                            <th class="text-center pe-4" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Modal: Credit Balance -->
            <div class="modal fade" id="creditBalanceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-plus-circle me-2"></i>Credit Merchant Balance</h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="post" action="<?php echo base_url('admin/createCreditBalance'); ?>">
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                    <input type="text" class="form-control border-0 py-2 fw-bold" readonly id="merchantName">
                                    <input type="hidden" id="merchantId" name="merchantId">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Channel ID</label>
                                    <select name="channelId" class="form-select border-1 py-2" required>
                                        <option value="">Select Channel</option>
                                        <?php foreach ($cashin_channels as $cashin_channel): ?>
                                            <option value="<?php echo $cashin_channel->id; ?>"><?php echo $cashin_channel->id; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Description</label>
                                    <input type="text" class="form-control border-1 py-2" id="description" name="description" placeholder="e.g. Manual top-up by admin" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Amount (IDR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-1">Rp</span>
                                        <input type="text" class="form-control border-1 py-2 fw-bold text-success" id="amountCredit" name="amount" oninput="formatNumber(this)" placeholder="0">
                                    </div>
                                    <input type="hidden" id="rawAmountCredit" name="rawAmountCredit">
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0 mt-4">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2" onclick="javascript: return confirm('Are you sure the credit amount is correct?')">
                                        <i class="fas fa-check mr-2"></i> CONFIRM CREDIT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Debit Balance -->
            <div class="modal fade" id="debitBalanceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-minus-circle me-2"></i>Debit Merchant Balance</h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="post" action="<?php echo base_url('admin/createDebitBalance'); ?>">
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                    <input type="text" class="form-control border-0 py-2 fw-bold" readonly id="merchantNameDebit">
                                    <input type="hidden" id="merchantIdDebit" name="merchantIdDebit">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Channel ID</label>
                                    <select name="channelId" class="form-select border-1 py-2" required>
                                        <option value="">Select Channel</option>
                                        <?php foreach ($cashout_channels as $cashout_channel): ?>
                                            <option value="<?php echo $cashout_channel->id; ?>"><?php echo $cashout_channel->id; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Description</label>
                                    <input type="text" class="form-control border-1 py-2" id="description" name="description" placeholder="e.g. Administrative deduction" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Amount (IDR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-1">Rp</span>
                                        <input type="text" class="form-control border-1 py-2 fw-bold text-danger" id="amountDebit" name="amount" oninput="formatNumber(this)" placeholder="0">
                                    </div>
                                    <input type="hidden" id="rawAmountDebit" name="rawAmountDebit">
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0 mt-4">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2" onclick="javascript: return confirm('Are you sure the debit amount is correct?')">
                                        <i class="fas fa-check mr-2"></i> CONFIRM DEBIT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Register Merchant -->
            <div class="modal fade" id="registerMerchabntModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-plus me-2"></i>Register New Merchant</h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <?php if (validation_errors()): ?>
                                <div class="alert alert-danger border-0 shadow-sm mb-4"><?= validation_errors(); ?></div>
                            <?php endif; ?>
                            
                            <form method="post" action="<?php echo base_url('admin/registerMerchant'); ?>" class="row g-3">
                                <div class="col-md-12">
                                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Account Information</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Merchant Name</label>
                                    <input type="text" class="form-control border-1" required name="c_name" placeholder="ABC Store">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Merchant Email</label>
                                    <input type="email" class="form-control border-1" required name="c_email" placeholder="owner@abc.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Merchant Phone</label>
                                    <input type="text" class="form-control border-1" name="c_phoneNumber" placeholder="08123xxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">GVConnect Business ID</label>
                                    <input type="text" class="form-control border-1" name="c_gvconnectBusinessId" placeholder="24090200001">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Password</label>
                                    <input type="password" class="form-control border-1" required name="c_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Confirm Password</label>
                                    <input type="password" class="form-control border-1" required name="c_confirmPassword">
                                </div>

                                <div class="col-md-12 mt-4">
                                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">OpenAPI Configuration</h6>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Whitelist IP (semicolon separated)</label>
                                    <input type="text" class="form-control border-1" name="c_openapiIPAllow" placeholder="1.2.3.4; 5.6.7.8">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Callback QRIS MPM</label>
                                    <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackQrisMpm" placeholder="https://api.your.com/callback">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Callback E-wallet</label>
                                    <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackEwallet" placeholder="https://api.your.com/callback">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Callback VA</label>
                                    <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackVa" placeholder="https://api.your.com/callback">
                                </div>
                                
                                <div class="col-md-12 mt-4">
                                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Service Permissions</h6>
                                    
                                    <div class="row pt-2">
                                        <!-- VA Services -->
                                        <div class="col-md-4 border-end">
                                            <div class="d-flex align-items-center gap-1 mb-3 h6">
                                                <div class="bg-primary-soft text-primary rounded-pill px-2 py-1 me-2" style="background-color: rgba(13, 110, 253, 0.1); font-size: 10px;">
                                                    <i class="fas fa-university"></i>
                                                </div>
                                                <span class="fw-bold small text-dark mt-1 text-uppercase">Virtual Account</span>
                                            </div>
                                            <?php
                                            $va_checkboxes = [
                                                'c_openapiChannelVaDynamicCreate' => 'VA Dynamic Create',
                                                'c_openapiChannelVaDynamicQuery' => 'VA Dynamic Query',
                                                'c_openapiChannelVaDynamicCancel' => 'VA Dynamic Cancel',
                                                'c_openapiChannelVaRecurringCreate' => 'VA Recurring Create',
                                                'c_openapiChannelVaRecurringCancel' => 'VA Recurring Cancel'
                                            ];
                                            foreach ($va_checkboxes as $key => $label) {
                                                echo '<div class="mb-3">';
                                                echo '  <div class="form-check form-switch">';
                                                echo '    <input class="form-check-input" type="checkbox" name="' . $key . '" id="' . $key . '" value="1">';
                                                echo '    <label class="form-check-label small fw-bold text-muted" for="' . $key . '">' . $label . '</label>';
                                                echo '  </div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>

                                        <!-- QRIS & E-Wallet -->
                                        <div class="col-md-4 border-end">
                                            <div class="d-flex align-items-center gap-1 mb-3 h6">
                                                <div class="bg-success-soft text-success rounded-pill px-2 py-1 me-2" style="background-color: rgba(25, 135, 84, 0.1); font-size: 10px;">
                                                    <i class="fas fa-qrcode"></i>
                                                </div>
                                                <span class="fw-bold small text-dark mt-1 text-uppercase">QRIS & E-Wallet</span>
                                            </div>
                                            <?php
                                            $qris_checkboxes = [
                                                'c_openapiChannelQrisMpmDynamicCreate' => 'QRIS MPM Create',
                                                'c_openapiChannelQrisMpmDynamicQuery' => 'QRIS MPM Query',
                                                'c_openapiChannelQrisMpmDynamicCancel' => 'QRIS MPM Cancel',
                                                'c_openapiChannelEwalletDynamicCreate' => 'E-wallet Create',
                                                'c_openapiChannelEwalletDynamicQuery' => 'E-wallet Query',
                                                'c_openapiChannelEwalletDynamicCancel' => 'E-wallet Cancel'
                                            ];
                                            foreach ($qris_checkboxes as $key => $label) {
                                                echo '<div class="mb-3">';
                                                echo '  <div class="form-check form-switch">';
                                                echo '    <input class="form-check-input" type="checkbox" name="' . $key . '" id="' . $key . '" value="1">';
                                                echo '    <label class="form-check-label small fw-bold text-muted" for="' . $key . '">' . $label . '</label>';
                                                echo '  </div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>

                                        <!-- Transfer Services -->
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-1 mb-3 h6">
                                                <div class="bg-warning-soft text-warning rounded-pill px-2 py-1 me-2" style="background-color: rgba(255, 193, 7, 0.1); font-size: 10px;">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </div>
                                                <span class="fw-bold small text-dark mt-1 text-uppercase">Transfer</span>
                                            </div>
                                            <?php
                                            $transfer_checkboxes = [
                                                'c_openapiChannelTransferToBifast' => 'BI-FAST Transfer',
                                                'c_openapiChannelTransferToRealtimeOnline' => 'Realtime Online Transfer',
                                                'c_allowTransferFromDashboard' => 'Allow Dashboard Transfer'
                                            ];
                                            foreach ($transfer_checkboxes as $key => $label) {
                                                echo '<div class="mb-3">';
                                                echo '  <div class="form-check form-switch">';
                                                echo '    <input class="form-check-input" type="checkbox" name="' . $key . '" id="' . $key . '" value="1">';
                                                echo '    <label class="form-check-label small fw-bold text-muted" for="' . $key . '">' . $label . '</label>';
                                                echo '  </div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer border-0 px-0 pb-0 mt-4 w-100 justify-content-end">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2">
                                        <i class="fas fa-save mr-2"></i> REGISTER MERCHANT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Delegate Access -->
            <div class="modal fade" id="delegateModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-key me-2"></i>Delegate Permission Ceiling</h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-warning-soft text-warning rounded-circle p-2 me-3" style="background-color: rgba(255, 193, 7, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" id="delegateMerchantName">Merchant Name</h6>
                                        <small class="text-muted">Setting maximum permissions for this merchant and its hierarchy.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <form id="delegateForm">
                                <input type="hidden" id="delegateMerchantId" name="merchantId">
                                <div id="permissionsList" style="max-height: 400px; overflow-y: auto;" class="p-4">
                                    <!-- Loading State -->
                                    <div class="text-center py-5" id="permissionsLoader">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Fetching permissions...</p>
                                    </div>
                                </div>
                                
                                <div class="modal-footer border-0 px-4 py-3 border-top">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2" id="btnSaveDelegation">
                                        <i class="fas fa-save mr-2"></i> SAVE CHANGES
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DataTables & Scripts -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
            <style>
                /* Prevent table clipping for dropdowns */
                .dt-card { overflow: visible !important; }
                .card-body { overflow: visible !important; }
                .table-responsive { overflow: visible !important; }
                
                @media (max-width: 991.98px) {
                    .table-responsive {
                        overflow-x: auto !important;
                        -webkit-overflow-scrolling: touch;
                    }
                }

                /* Delegation Modal Responsive */
                @media (max-width: 575.98px) {
                    #permissionsList .table thead {
                        display: none;
                    }
                    #permissionsList .table tbody tr {
                        display: block;
                        padding: 1rem 0.5rem;
                        border-bottom: 1px solid #edf2f9;
                    }
                    #permissionsList .table td {
                        display: block;
                        width: 100% !important;
                        padding: 0.25rem 0 !important;
                        border: none !important;
                    }
                    #permissionsList .table td:last-child {
                        margin-top: 0.75rem;
                    }
                    #permissionsList .btn-group {
                        margin-top: 5px;
                    }
                    #permissionsList .btn-group .btn {
                        padding: 0.5rem;
                        font-size: 11px;
                    }
                }
            </style>
            <script>
                $(document).ready(function() {
                    var ajaxUrl = "<?= base_url('admin/merchant') ?>";
                    var columns = [
                        { "data": "no", "orderable": false, "className": "ps-4 text-muted small" },
                        { "data": "id" },
                        { "data": "info","className": "text-left" },
                        { "data": "balance", "orderable": false },
                        { "data": "status", "orderable": false },
                        { "data": "action", "orderable": false, "className": "text-center pe-4" }
                    ];

                    var table = initServerDataTable('#merchantTable', ajaxUrl, columns, {
                        "language": {
                            "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
                            "info": "Showing _START_ – _END_ of _TOTAL_ results",
                            "infoEmpty": "No results to show",
                            "infoFiltered": "",
                            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No transactions found.</div>'
                        },
                        "dom": 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
                        "responsive": false,
                        "autoWidth": false,
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

                    // External search mapping
                    $('#merchantGlobalSearch').on('keyup', function() {
                        table.search(this.value).draw();
                    });
                });

                function detail(id, name) {
                    document.getElementById('merchantId').value = id;
                    document.getElementById('merchantName').value = name;
                }
                function detaildebit(id, name) {
                    document.getElementById('merchantIdDebit').value = id;
                    document.getElementById('merchantNameDebit').value = name;
                }

                function formatNumber(input) {
                    let rawValue = input.value.replace(/[^0-9]/g, ''); 
                    if (input.id === "amountCredit") {
                        document.getElementById('rawAmountCredit').value = rawValue;
                    } else if (input.id === "amountDebit") {
                        document.getElementById('rawAmountDebit').value = rawValue;
                    }
                    
                    if (rawValue) {
                        input.value = parseInt(rawValue).toLocaleString('id-ID');
                    } else {
                        input.value = '';
                    }
                }

                function openDelegateModal(id, name) {
                    $('#delegateMerchantId').val(id);
                    $('#delegateMerchantName').text(name);
                    $('#permissionsList').html('<div class="text-center py-5" id="permissionsLoader"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Fetching permissions...</p></div>');
                    
                    $.ajax({
                        url: '<?= base_url('admin/fetchMerchantPermissions/') ?>' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                let html = '<div class="table-responsive"><table class="table mb-0 align-middle">';
                                html += '<thead><tr><th style="width: 60%">Permission Name</th><th class="text-center">Action</th></tr></thead><tbody>';
                                
                                response.data.forEach(function(perm) {
                                    html += `<tr>
                                        <td>
                                            <div class="fw-bold text-dark">${perm.label}</div>
                                            <div class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">${perm.name}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="permissions[${perm.id}]" id="grant_${perm.id}" value="Grant" ${perm.status.toLowerCase() === 'grant' ? 'checked' : ''} autocomplete="off">
                                                <label class="btn btn-outline-success btn-sm font-weight-bold" for="grant_${perm.id}">GRANT</label>
                                                
                                                <input type="radio" class="btn-check" name="permissions[${perm.id}]" id="deny_${perm.id}" value="Deny" ${perm.status.toLowerCase() === 'deny' ? 'checked' : ''} autocomplete="off">
                                                <label class="btn btn-outline-danger btn-sm font-weight-bold" for="deny_${perm.id}">DENY</label>
                                            </div>
                                        </td>
                                    </tr>`;
                                });
                                
                                html += '</tbody></table></div>';
                                $('#permissionsList').html(html);
                            } else {
                                $('#permissionsList').html('<div class="alert alert-danger m-3">Failed to load permissions.</div>');
                            }
                        },
                        error: function() {
                            $('#permissionsList').html('<div class="alert alert-danger m-3">An error occurred while fetching data.</div>');
                        }
                    });
                }

                $('#delegateForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#delegateMerchantId').val();
                    const $btn = $('#btnSaveDelegation');
                    const originalText = $btn.text();
                    
                    const csrfName = $('meta[name="csrf-token-name"]').attr('content');
                    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                    const formData = $(this).serializeArray();
                    formData.push({name: csrfName, value: csrfHash});
                    
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>SAVING...');
                    
                    $.ajax({
                        url: '<?= base_url('admin/saveDelegation/') ?>' + id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#delegateModal').modal('hide');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An internal server error occurred.'
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).text(originalText);
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

