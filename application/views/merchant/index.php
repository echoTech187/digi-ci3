<div>

    <!-- Page Header -->
    <div class="dt-page-header">
        <div>
            <h1 class="dt-page-title">Merchant Management</h1>
            <p class="dt-page-subtitle">View and manage all registered merchants and their balances.</p>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
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
                    <button type="button" class="btn-dt-action btn-dt-action-success border-0" data-toggle="modal" data-target="#registerMerchantModal">
                        <i class="fas fa-plus mr-2"></i> <span class="d-none d-md-block">Add Merchant</span>
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
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header modal-header-primary border-0 mh-premium">
                        <div class="d-flex align-items-center">
                            <div class="mh-icon-badge">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="mh-title-wrap">
                                <h6 class="mh-title" id="creditBalanceModalLabel">Credit Merchant Balance</h6>
                                <small class="mh-subtitle">Modify and update existing information</small>
                            </div>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="creditBalanceForm">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                <input type="text" class="form-control border-0 py-2 fw-bold" readonly id="merchantName">
                                <input type="hidden" id="merchantId" name="merchantId">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Channel ID</label>
                                <select id="creditChannelId" name="channelId" class="form-select border-1 py-1" style="font-size: 13px;" required>
                                    <option value="">Select Channel</option>
                                    <?php foreach ($cashin_channels as $cashin_channel): ?>
                                        <option value="<?php echo $cashin_channel->id; ?>"><?php echo $cashin_channel->id; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Description</label>
                                <input type="text" class="form-control border-1 py-2" id="creditDescription" name="description" placeholder="e.g. Manual top-up" required>
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
                                <button type="submit" id="btnConfirmCredit" class="btn-dt-apply px-4">
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
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header modal-header-primary border-0 mh-premium">
                        <div class="d-flex align-items-center">
                            <div class="mh-icon-badge">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="mh-title-wrap">
                                <h6 class="mh-title" id="debitBalanceModalLabel">Debit Merchant Balance</h6>
                                <small class="mh-subtitle">Process and modify merchant debit balance</small>
                            </div>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="debitBalanceForm">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                <input type="text" class="form-control border-0 py-2 fw-bold" readonly id="merchantNameDebit">
                                <input type="hidden" id="merchantIdDebit" name="merchantIdDebit">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Channel ID</label>
                                <select id="debitChannelId" name="channelId" class="form-select border-1 py-1" style="font-size: 13px;" required>
                                    <option value="">Select Channel</option>
                                    <?php foreach ($cashout_channels as $cashout_channel): ?>
                                        <option value="<?php echo $cashout_channel->id; ?>"><?php echo $cashout_channel->id; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Description</label>
                                <input type="text" class="form-control border-1 py-2" id="debitDescription" name="description" placeholder="e.g. Administrative deduction" required>
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
                                <button type="submit" id="btnConfirmDebit" class="btn-dt-apply px-4">
                                    <i class="fas fa-check mr-2"></i> CONFIRM DEBIT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Register Merchant -->
        <div class="modal fade" id="registerMerchantModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header modal-header-primary border-0 mh-premium">
                        <div class="d-flex align-items-center">
                            <div class="mh-icon-badge">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="mh-title-wrap">
                                <h6 class="mh-title" id="registerMerchantModalLabel">Register New Merchant</h6>
                                <small class="mh-subtitle">Create and configure a new merchant account</small>
                            </div>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4"><?= validation_errors(); ?></div>
                            <script>
                                $(document).ready(function() {
                                    $('#registerMerchantModal').modal('show');
                                });
                            </script>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo base_url('admin/registerMerchant'); ?>" class="row g-3">
                            <div class="col-md-12">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Account Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Merchant Name</label>
                                <input type="text" class="form-control border-1" required name="c_name" placeholder="ABC Store">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Merchant Email</label>
                                <input type="email" class="form-control border-1" required name="c_email" placeholder="owner@abc.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Merchant Phone</label>
                                <input type="text" class="form-control border-1" name="c_phoneNumber" placeholder="08123xxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">GVConnect Business ID</label>
                                <input type="text" class="form-control border-1" name="c_gvconnectBusinessId" placeholder="24090200001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" class="form-control border-1" required name="c_password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirm Password</label>
                                <input type="password" class="form-control border-1" required name="c_confirmPassword">
                            </div>

                            <div class="col-md-12 mt-4">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">OpenAPI Configuration</h6>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label small fw-bold text-muted">Whitelist IP (semicolon separated)</label>
                                <input type="text" class="form-control border-1" name="c_openapiIPAllow" placeholder="1.2.3.4; 5.6.7.8">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Callback QRIS MPM</label>
                                <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackQrisMpm" placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Callback E-wallet</label>
                                <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackEwallet" placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Callback VA</label>
                                <input type="text" class="form-control border-1" required name="c_openapiUrlCallbackVa" placeholder="https://api.your.com/callback">
                            </div>

                            <div class="col-md-12 mt-4">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Service Permissions</h6>
                                <div class="row pt-2">
                                    <div class="col-md-4 border-end">
                                        <div class="d-flex align-items-center gap-1 mb-3 h6">
                                            <div class="bg-primary-soft text-primary rounded-pill px-2 py-1" style="background-color: rgba(13, 110, 253, 0.1); font-size: 10px;">
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
                                            echo '<div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="'.$key.'" value="1"><label class="form-check-label small fw-bold text-muted">'.$label.'</label></div></div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4 border-end">
                                        <div class="d-flex align-items-center gap-1 mb-3 h6">
                                            <div class="bg-success-soft text-success rounded-pill px-2 py-1" style="background-color: rgba(25, 135, 84, 0.1); font-size: 10px;">
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
                                            echo '<div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="'.$key.'" value="1"><label class="form-check-label small fw-bold text-muted">'.$label.'</label></div></div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-1 mb-3 h6">
                                            <div class="bg-warning-soft text-warning rounded-pill px-2 py-1" style="background-color: rgba(255, 193, 7, 0.1); font-size: 10px;">
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
                                            echo '<div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="'.$key.'" value="1"><label class="form-check-label small fw-bold text-muted">'.$label.'</label></div></div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0 mt-4 w-100 justify-content-end">
                                <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn-dt-apply px-4">
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
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header modal-header-primary border-0 mh-premium">
                        <div class="d-flex align-items-center">
                            <div class="mh-icon-badge">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="mh-title-wrap">
                                <h6 class="mh-title" id="delegateModalLabel">Delegate Permission Ceiling</h6>
                                <small class="mh-subtitle">Manage maximum hierarchy permissions</small>
                            </div>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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
                                <div class="text-center py-5" id="permissionsLoader">
                                    <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>
                                    <p class="mt-2 text-muted">Fetching permissions...</p>
                                </div>
                            </div>
                            <div class="modal-footer border-0 px-4 py-3 border-top">
                                <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn-dt-apply px-4" id="btnSaveDelegation">
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
        <script>
            $(document).ready(function() {
                var ajaxUrl = "<?= base_url('admin/merchant') ?>";
                var columns = [
                    { "data": "no", "orderable": false, "className": "ps-4 text-muted small" },
                    { 
                        "data": "id",
                        "render": function(data, type, row) {
                            return '<span class="fw-bold text-dark">#' + data + '</span>';
                        }
                    },
                    { 
                        "data": "c_name",
                        "className": "text-left",
                        "render": function(data, type, row) {
                            return '<div class="d-flex flex-column">' +
                                   '    <span class="fw-bold text-dark">' + data + '</span>' +
                                   '    <span class="text-muted small">' + row.c_email + '</span>' +
                                   '</div>';
                        }
                    },
                    { 
                        "data": "c_balanceTotal",
                        "orderable": false,
                        "render": function(data, type, row) {
                            var total = parseFloat(data);
                            var hold = parseFloat(row.c_balanceHold);
                            var available = total - hold;
                            return '<div class="d-flex flex-column" style="min-width: 150px;">' +
                                   '    <div class="d-flex justify-content-between small mb-1">' +
                                   '        <span class="text-muted">Total:</span>' +
                                   '        <span class="fw-bold text-dark">Rp ' + number_format(total, 0, ',', '.') + '</span>' +
                                   '    </div>' +
                                   '    <div class="d-flex justify-content-between small mb-1">' +
                                   '        <span class="text-muted">Hold:</span>' +
                                   '        <span class="text-warning fw-bold">Rp ' + number_format(hold, 0, ',', '.') + '</span>' +
                                   '    </div>' +
                                   '    <div class="d-flex justify-content-between small border-top pt-1 mt-1">' +
                                   '        <span class="text-muted">Available:</span>' +
                                   '        <span class="text-success fw-bold">Rp ' + number_format(available, 0, ',', '.') + '</span>' +
                                   '    </div>' +
                                   '</div>';
                        }
                    },
                    { 
                        "data": "c_status",
                        "orderable": false,
                        "render": function(data, type, row) {
                            var status_class = (data == 'Active') ? 'bg-success' : 'bg-secondary';
                            var openapi_class = (row.c_openapiStatus == 'Active') ? 'text-success' : 'text-muted';
                            return '<div class="d-flex flex-column">' +
                                   '    <span class="mb-2 badge ' + status_class + '-soft text-' + status_class.replace('-soft', '').replace('bg-', '') + ' rounded-pill px-3 py-1" style="width: fit-content;">' +
                                   '        ' + data +
                                   '    </span>' +
                                   '    <span class="small ' + openapi_class + ' d-flex align-items-center gap-1">' +
                                   '        <i class="fas fa-plug me-1"></i>OpenAPI: ' + row.c_openapiStatus +
                                   '    </span>' +
                                   '</div>';
                        }
                    },
                    { 
                        "data": "action",
                        "orderable": false,
                        "className": "text-center pe-4",
                        "render": function(data, type, row) {
                            var baseUrl = "<?= base_url() ?>";
                            var actionHtml = '<div class="dropdown">' +
                                '    <button class="btn btn-white btn-sm rounded shadow-none dropdown-toggle border px-3" type="button" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">' +
                                '        <i class="fas fa-ellipsis-v text-muted mr-2"></i>Actions' +
                                '    </button>' +
                                '    <div class="dropdown-menu dropdown-menu-right border-0 shadow-lg p-2" style="min-width: 200px;">' +
                                '        <a class="dropdown-item rounded-2 py-2" href="' + baseUrl + 'admin/editMerchant/' + row.id + '">' +
                                '            <i class="fas fa-edit mr-2 text-info" style="width: 20px;"></i>Edit Merchant' +
                                '        </a>' +
                                '        <a class="dropdown-item rounded-2 py-2" href="' + baseUrl + 'admin/mutation/' + row.id + '">' +
                                '            <i class="fas fa-exchange-alt mr-2 text-primary" style="width: 20px;"></i>Mutation Log' +
                                '        </a>' +
                                '        <a class="dropdown-item rounded-2 py-2" href="' + baseUrl + 'admin/submerchant/' + row.id + '">' +
                                '            <i class="fas fa-users mr-2 text-success" style="width: 20px;"></i>Sub Accounts' +
                                '        </a>';

                            if (row.c_merchantLevel == 0) {
                                actionHtml += '<button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#delegateModal" onClick="openDelegateModal(' + row.id + ', \'' + row.c_name.replace(/'/g, "\\'") + '\')">' +
                                    '    <i class="fas fa-key mr-2 text-warning" style="width: 20px;"></i>Delegate' +
                                    '</button>';
                            }

                            if (row.hasBalancePermission) {
                                actionHtml += '<div class="dropdown-divider"></div>' +
                                    '<button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#creditBalanceModal" onClick="detail(' + row.id + ', \'' + row.c_name.replace(/'/g, "\\'") + '\')">' +
                                    '    <i class="fas fa-plus-circle mr-2 text-success" style="width: 20px;"></i>Credit Balance' +
                                    '</button>' +
                                    '<button class="dropdown-item rounded-2 py-2 border-0 bg-transparent w-100 text-left" data-toggle="modal" data-target="#debitBalanceModal" onClick="detaildebit(' + row.id + ', \'' + row.c_name.replace(/'/g, "\\'") + '\')">' +
                                    '    <i class="fas fa-minus-circle mr-2 text-danger" style="width: 20px;"></i>Debit Balance' +
                                    '</button>';
                            }

                            actionHtml += '<div class="dropdown-divider"></div>' +
                                '        <a class="dropdown-item rounded-2 py-2" href="' + baseUrl + 'admin/settingcashinfee/' + row.id + '">' +
                                '            <i class="fas fa-cog mr-2 text-secondary" style="width: 20px;"></i>Cashin Fee Settings' +
                                '        </a>' +
                                '        <a class="dropdown-item rounded-2 py-2" href="' + baseUrl + 'admin/settingcashoutfee/' + row.id + '">' +
                                '            <i class="fas fa-cog mr-2 text-secondary" style="width: 20px;"></i>Cashout Fee Settings' +
                                '        </a>' +
                                '    </div>' +
                                '</div>';
                            return actionHtml;
                        }
                    }
                ];
                var table = initServerDataTable('#merchantTable', ajaxUrl, columns);
                window.merchantTableInstance = table; // Expose untuk digunakan oleh AJAX credit/debit refresh

                table.on('xhr', function(e, settings, json) {
                    if (json && json.redirect) {
                        window.location = json.redirect;
                    }
                });

                // Global search with Debounce
                $('#merchantGlobalSearch').on('input', debounce(function() {
                    table.search(this.value).draw();
                }, 400));
            });

            function detail(id, name) {
                document.getElementById('merchantId').value = id;
                document.getElementById('merchantName').value = name;
                // Re-init Select2 dengan dropdownParent agar tidak terpotong modal (#12)
                setTimeout(function() {
                    $('#creditChannelId').select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        dropdownParent: $('#creditBalanceModal')
                    });
                }, 300);
            }
            function detaildebit(id, name) {
                document.getElementById('merchantIdDebit').value = id;
                document.getElementById('merchantNameDebit').value = name;
                // Re-init Select2 dengan dropdownParent agar tidak terpotong modal (#12)
                setTimeout(function() {
                    $('#debitChannelId').select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        dropdownParent: $('#debitBalanceModal')
                    });
                }, 300);
            }

            function formatNumber(input) {
                let rawValue = input.value.replace(/[^0-9]/g, ''); 
                if (input.id === "amountCredit") document.getElementById('rawAmountCredit').value = rawValue;
                else if (input.id === "amountDebit") document.getElementById('rawAmountDebit').value = rawValue;
                input.value = rawValue ? parseInt(rawValue).toLocaleString('id-ID') : '';
            }

            function openDelegateModal(id, name) {
                $('#delegateMerchantId').val(id);
                $('#delegateMerchantName').text(name);
                $('#permissionsList').html('<div class="text-center py-5"><div class="spinner-border text-info" role="status"></div><p class="mt-2 text-muted">Fetching permissions...</p></div>');
                
                $.ajax({
                    url: '<?= base_url('admin/fetchMerchantPermissions/') ?>' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            let html = '<table class="table mb-0"><thead><tr><th>Permission Name</th><th class="text-center">Action</th></tr></thead><tbody>';
                            response.data.forEach(function(perm) {
                                const isGrant = perm.status.toLowerCase() === 'grant';
                                const isDeny = perm.status.toLowerCase() === 'deny';
                                
                                html += `
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">${perm.label}</div>
                                        <div class="text-muted small">${perm.name}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="delegation-toggle-group">
                                            <label class="btn btn-sm delegation-btn ${isGrant ? 'btn-success' : 'btn-link text-success'}">
                                                <input type="radio" name="permissions[${perm.id}]" value="Grant" ${isGrant ? 'checked' : ''} style="display:none;"> GRANT
                                            </label>
                                            <label class="btn btn-sm delegation-btn ${isDeny ? 'btn-danger' : 'btn-link text-danger'}">
                                                <input type="radio" name="permissions[${perm.id}]" value="Deny" ${isDeny ? 'checked' : ''} style="display:none;"> DENY
                                            </label>
                                        </div>
                                    </td>
                                </tr>`;
                            });
                            $('#permissionsList').html(html + '</tbody></table>');
                        } else {
                            $('#permissionsList').html('<div class="alert alert-danger m-3">Failed to load permissions: ' + response.message + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#permissionsList').html('<div class="alert alert-danger m-3">Connection error. Please check your network.</div>');
                    }
                });
            }

            // Manual Event Delegation for Radio Buttons in Modal (Original UI Style)
            $(document).on('click', '.delegation-btn', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                const $label = $(this);
                const $input = $label.find('input');
                const $parentGroup = $label.closest('.delegation-toggle-group');
                const val = $input.val();

                // Reset all buttons in group to link style
                $parentGroup.find('.delegation-btn').each(function() {
                    const $l = $(this);
                    const $i = $l.find('input');
                    if ($i.val() === 'Grant') {
                        $l.removeClass('btn-success active').addClass('btn-link text-success');
                    } else {
                        $l.removeClass('btn-danger active').addClass('btn-link text-danger');
                    }
                });

                // Set clicked button to solid style
                if (val === 'Grant') {
                    $label.removeClass('btn-link text-success').addClass('btn-success active');
                } else {
                    $label.removeClass('btn-link text-danger').addClass('btn-danger active');
                }

                // Data update
                $input.prop('checked', true);
                $input.trigger('change');
            });

            // #7: Confirm dialog sebelum save delegation (aksi kritis)
            $('#delegateForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#delegateMerchantId').val();
                const merchantName = $('#delegateMerchantName').text();
                const $form = $(this);

                Swal.fire({
                    icon: 'warning',
                    title: 'Simpan Perubahan?',
                    html: 'Permission untuk <strong>' + merchantName + '</strong> akan langsung berlaku setelah disimpan.',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    const $btn = $('#btnSaveDelegation');
                    const originalBtnHtml = $btn.html();

                    // Loading State
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> SAVING...');

                    $.ajax({
                        url: '<?= base_url('admin/saveDelegation/') ?>' + id,
                        type: 'POST',
                        data: $form.serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') { 
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'Tersimpan!', 
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }); 
                                $('#delegateModal').modal('hide'); 
                            } else {
                                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalBtnHtml);
                        }
                    });
                });
            });

            // #2: Credit Balance form — AJAX agar tidak full page reload
            $('#creditBalanceForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#btnConfirmCredit');
                var originalHtml = $btn.html();
                var rawAmount = $('#rawAmountCredit').val();
                if (!rawAmount || parseInt(rawAmount) <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Masukkan jumlah amount yang valid.' });
                    return;
                }
                $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
                $.ajax({
                    url: '<?= base_url('admin/createCreditBalance') ?>',
                    type: 'POST',
                    data: {
                        merchantId: $('#merchantId').val(),
                        channelId: $('#creditChannelId').val(),
                        description: $('#creditDescription').val(),
                        amount: rawAmount,
                        rawAmountCredit: rawAmount
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Credit balance berhasil ditambahkan.', timer: 2500, showConfirmButton: false });
                            $('#creditBalanceModal').modal('hide');
                            $('#creditBalanceForm')[0].reset();
                            if (window.merchantTableInstance) window.merchantTableInstance.draw('page');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: (response && response.message) ? response.message : 'Terjadi kesalahan.' });
                        }
                    },
                    error: function(xhr) {
                        // Fallback: jika server tidak mengembalikan JSON, submit form biasa
                        if (xhr.status !== 200) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
                        } else {
                            window.location.href = '<?= base_url('admin/createCreditBalance') ?>';
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // #2: Debit Balance form — AJAX agar tidak full page reload
            $('#debitBalanceForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#btnConfirmDebit');
                var originalHtml = $btn.html();
                var rawAmount = $('#rawAmountDebit').val();
                if (!rawAmount || parseInt(rawAmount) <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Masukkan jumlah amount yang valid.' });
                    return;
                }
                $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
                $.ajax({
                    url: '<?= base_url('admin/createDebitBalance') ?>',
                    type: 'POST',
                    data: {
                        merchantIdDebit: $('#merchantIdDebit').val(),
                        channelId: $('#debitChannelId').val(),
                        description: $('#debitDescription').val(),
                        amount: rawAmount,
                        rawAmountDebit: rawAmount
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Debit balance berhasil diproses.', timer: 2500, showConfirmButton: false });
                            $('#debitBalanceModal').modal('hide');
                            $('#debitBalanceForm')[0].reset();
                            if (window.merchantTableInstance) window.merchantTableInstance.draw('page');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: (response && response.message) ? response.message : 'Terjadi kesalahan.' });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status !== 200) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
                        } else {
                            window.location.href = '<?= base_url('admin/createDebitBalance') ?>';
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
        </script>
    </div>
</div>
