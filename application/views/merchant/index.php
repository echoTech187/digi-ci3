<div>

    <!-- Page Header -->
    <div class="dt-page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="dt-page-title">Merchant Management</h1>
            <p class="dt-page-subtitle">View and manage all registered merchants and their balances.</p>
        </div>
        <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
            <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
        </button>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Merchant Management Overview</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Welcome to the core Merchant Management portal. Here you can oversee all registered merchants, track their API statuses, and manage their fund balances with absolute precision.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Global Search</div>
                <p class="drawer-card-text">Find any merchant instantly by their Name, Merchant ID, Business ID, or Email Address using the fast search bar.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-filter text-primary mr-2"></i> Advanced Filters</div>
                <p class="drawer-card-text">Narrow down the list by Account Status (Active, Blocked, Frozen), OpenAPI Status, and Registration Date range.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Balance Controls</div>
                <p class="drawer-card-text">Authorized admins can manually Credit (Add) or Debit (Deduct) a merchant's balance directly from the actions menu.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-key text-primary mr-2"></i> Delegation & Security</div>
                <p class="drawer-card-text">Configure a merchant's maximum hierarchy permissions, ensuring sub-accounts never exceed their parent's access ceilings.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sliders-h text-primary mr-2"></i> Deep Configuration</div>
                <p class="drawer-card-text">Use the Action (⋮) menu to jump to a merchant's specific Mutation Logs, Sub-Accounts, or Cashin/Cashout Fee Settings.</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card dt-card border-0 shadow-sm">
        <!-- Toolbar -->
        <form id="merchant_search_form" method="post" action="<?= base_url('merchant/manage'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="merchantGlobalSearch" class="dt-search-input" placeholder="Search by name, ID, email, or Business ID..." value="<?= $this->session->userdata('search_merchant'); ?>">
                </div>

                <!-- RIGHT: Filters & Actions -->
                <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <button type="button" id="merchantMoreFiltersBtn" class="dt-more-filters-btn <?= (!empty($this->session->userdata('search_merchant_status')) || !empty($this->session->userdata('search_merchant_openapi_status')) || !empty($this->session->userdata('search_merchant_date_from')) || !empty($this->session->userdata('search_merchant_date_to'))) ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php 
                                $extra_active = 0;
                                if (!empty($this->session->userdata('search_merchant_status'))) $extra_active++;
                                if (!empty($this->session->userdata('search_merchant_openapi_status'))) $extra_active++;
                                if (!empty($this->session->userdata('search_merchant_date_from')) || !empty($this->session->userdata('search_merchant_date_to'))) $extra_active++;
                                if ($extra_active > 0): 
                            ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="merchantMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('merchant/manage/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Registration Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Registration Date</label>
                                    <div class="premium-picker">
                                        <input type="date" name="search_merchant_date_from" class="dt-chip-input" value="<?= $this->session->userdata('search_merchant_date_from'); ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_merchant_date_to" class="dt-chip-input" value="<?= $this->session->userdata('search_merchant_date_to'); ?>" title="Date To">
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Account Status</label>
                                    <select name="search_merchant_status" class="dt-more-select">
                                        <option value="">All Account Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_merchant_status') == 'Pending') ? 'selected' : ''; ?>>Pending Approval</option>
                                        <option value="Active" <?= ($this->session->userdata('search_merchant_status') == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Blocked" <?= ($this->session->userdata('search_merchant_status') == 'Blocked') ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= ($this->session->userdata('search_merchant_status') == 'Freeze') ? 'selected' : ''; ?>>Frozen</option>
                                    </select>
                                </div>
                                
                                <!-- OpenAPI Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-plug mr-1 mr-2"></i> OpenAPI Status</label>
                                    <select name="search_merchant_openapi_status" class="dt-more-select">
                                        <option value="">All OpenAPI Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_merchant_openapi_status') == 'Pending') ? 'selected' : ''; ?>>Pending Approval</option>
                                        <option value="Active" <?= ($this->session->userdata('search_merchant_openapi_status') == 'Active') ? 'selected' : ''; ?>>Active Access</option>
                                        <option value="Not Active" <?= ($this->session->userdata('search_merchant_openapi_status') == 'Not Active') ? 'selected' : ''; ?>>Deactivated</option>
                                        <option value="Blocked" <?= ($this->session->userdata('search_merchant_openapi_status') == 'Blocked') ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= ($this->session->userdata('search_merchant_openapi_status') == 'Freeze') ? 'selected' : ''; ?>>Account Frozen</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="merchantMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="<?= base_url('merchant/manage/add'); ?>" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center" >
                        <i class="fas fa-plus mr-2"></i> <span class="d-none d-md-block">Add Merchant</span>
                    </a>
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
                            <th>Registration Date</th>
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
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="creditBalanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
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
                    <div class="modal-body p-0">
                        <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                            <!-- Left Column: Instructions Guide -->
                            <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0" style="background:#202328;color:#fff;border-right:1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px; background-color: rgba(40, 167, 69, 0.2) !important;">
                                            <i class="fas fa-book-open text-success"></i>
                                        </div>
                                        <h6 class="fw-bold text-success mb-0" style="font-size: 14px;">Credit Guide</h6>
                                    </div>
                                    <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for manual balance adjustments:</p>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-user-check text-warning mr-2"></i> 1. Merchant Identity</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Verify the merchant's business name and ID carefully before executing any balance adjustment.</p>
                                        </div>
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-file-invoice text-info mr-2"></i> 2. Precise Auditing</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Manual transactions require clear descriptions. Explain why you are crediting the merchant's balance.</p>
                                        </div>
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-bolt text-success mr-2"></i> 3. Instant Execution</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Credits are applied to the active wallet instantly. This operation is recorded and cannot be automatically undone.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: The Form -->
                            <div class="col-lg-7 p-4 bg-light mb-0">
                                <form id="creditBalanceForm">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                        <input type="text" class="form-control border-0 py-2 bg-dark text-white fw-bold" readonly id="merchantName" style="border-color: rgba(255,255,255,0.1);">
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
                                    <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                        <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                        <button type="submit" id="btnConfirmCredit" class="btn-dt-apply px-4 no-loader">
                                            <i class="fas fa-check mr-2"></i> CONFIRM CREDIT
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Debit Balance -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="debitBalanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
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
                    <div class="modal-body p-0">
                        <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                            <!-- Left Column: Instructions Guide -->
                            <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0" style="background:#202328;color:#fff;border-right:1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px; background-color: rgba(220, 53, 69, 0.2) !important;">
                                            <i class="fas fa-book-open text-danger"></i>
                                        </div>
                                        <h6 class="fw-bold text-danger mb-0" style="font-size: 14px;">Debit Guide</h6>
                                    </div>
                                    <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for manual balance deductions:</p>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-info-circle text-warning mr-2"></i> 1. Balance Availability</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Ensure the merchant's available balance is sufficient to cover the debit adjustment to avoid a negative balance.</p>
                                        </div>
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-file-contract text-info mr-2"></i> 2. Audit Trail</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Enter a detailed explanation for the balance deduction (e.g. system correction, penalty, fee recovery).</p>
                                        </div>
                                        <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                            <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-minus-square text-danger mr-2"></i> 3. Immediate Effect</h6>
                                            <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Funds are deducted from the merchant's balance in real-time. This action is permanently logged and audited.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: The Form -->
                            <div class="col-lg-7 p-4 bg-light mb-0">
                                <form id="debitBalanceForm">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                        <input type="text" class="form-control border-0 py-2 bg-dark text-white fw-bold" readonly id="merchantNameDebit" style="border-color: rgba(255,255,255,0.1);">
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
                                    <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                        <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                        <button type="submit" id="btnConfirmDebit" class="btn-dt-apply px-4 no-loader">
                                            <i class="fas fa-check mr-2"></i> CONFIRM DEBIT
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal: Delegate Access -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="delegateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
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
                    <div class="modal-body p-0 bg-light">
                        <form id="delegateForm" class="mb-0 w-100">
                            <input type="hidden" id="delegateMerchantId" name="merchantId">
                            <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                                <!-- Left Column: Instructions Guide -->
                                <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0" style="background:#202328;color:#fff;border-right:1px solid rgba(255,255,255,0.05);">
                                    <div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px;">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                            <h6 class="fw-bold text-primary mb-0" style="font-size: 14px;">Delegation Guide</h6>
                                        </div>
                                        <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for managing merchant permission ceilings:</p>
                                        
                                        <div class="d-flex flex-column gap-3">
                                            <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-shield-alt text-warning mr-2"></i> 1. Permission Ceiling</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">You can only delegate permissions that you personally possess. You cannot grant access beyond your clearance.</p>
                                            </div>
                                            <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-sitemap text-info mr-2"></i> 2. Inheritance &amp; Flow</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Granting a permission allows the merchant to use the feature and further delegate it down their hierarchy.</p>
                                            </div>
                                            <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-sliders-h text-success mr-2"></i> 3. Granular Controls</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Toggle between Grant (Allow) and Deny (Block) to override default role permissions instantly.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Permissions List -->
                                <div class="col-lg-7 p-4 bg-light mb-0">
                                    <div class="d-flex align-items-center gap-2 mb-4">
                                        <div class="avatar-sm bg-warning-soft text-warning rounded-circle p-2 me-3" style="background-color: rgba(255, 193, 7, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-store text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark" id="delegateMerchantName">Merchant Name</h6>
                                            <small class="text-muted">Setting maximum permissions for this merchant and its hierarchy.</small>
                                        </div>
                                    </div>

                                    <div id="permissionsList" style="max-height: 400px; overflow-y: auto;" class="p-2 border rounded bg-white">
                                        <div class="text-center py-5" id="permissionsLoader">
                                            <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>
                                            <p class="mt-2 text-muted">Fetching permissions...</p>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                        <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                        <button type="submit" class="btn-dt-apply px-4 no-loader" id="btnSaveDelegation">
                                            <i class="fas fa-save mr-2"></i> SAVE CHANGES
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTables & Scripts -->
        <script>
            $(document).ready(function() {
                var ajaxUrl = "<?= base_url('merchant/manage') ?>";
                var columns = [
                    { "data": "no", "orderable": false, "className": "ps-4 text-muted small" },
                    { 
                        "data": "id",
                        "className": "text-left text-nowrap",
                        "render": function(data, type, row) {
                            return '<span class="fw-bold text-dark">#' + data + '</span>';
                        }
                    },
                    { 
                        "data": "c_name",
                        "className": "text-left text-nowrap",
                        "render": function(data, type, row) {
                            return '<div class="d-flex flex-column">' +
                                   '    <a href="<?= base_url('merchant/manage/detail/') ?>' + row.id + '" class="fw-bold text-primary text-decoration-none">' + data + '</a>' +
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
                        "data": "c_dateCreated", 
                        "orderable": true,
                        "className": "text-center text-nowrap",
                        "render": function(data, type, row) {
                            if (!data) return '-';
                            var d = new Date(data);
                            if (isNaN(d)) return data;
                            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            var day = ('0' + d.getDate()).slice(-2);
                            var month = months[d.getMonth()];
                            var year = d.getFullYear();
                            return '<span class="fw-bold text-dark">' + day + ' ' + month + ' ' + year + '</span>';
                        }
                    },
                    { 
                        "data": "c_status",
                        "orderable": false,
                        "render": function(data, type, row) {
                            var status_bg = 'bg-secondary-soft';
                            var status_text = 'text-secondary';
                            if (data == 'Active') {
                                status_bg = 'bg-success-soft';
                                status_text = 'text-success';
                            } else if (data == 'Pending') {
                                status_bg = 'bg-warning-soft';
                                status_text = 'text-warning';
                            } else if (data == 'Blocked') {
                                status_bg = 'bg-danger-soft';
                                status_text = 'text-danger';
                            } else if (data == 'Freeze') {
                                status_bg = 'bg-info-soft';
                                status_text = 'text-info';
                            }

                            var openapi_class = 'text-muted';
                            if (row.c_openapiStatus == 'Active') {
                                openapi_class = 'text-success';
                            } else if (row.c_openapiStatus == 'Pending') {
                                openapi_class = 'text-warning';
                            } else if (row.c_openapiStatus == 'Blocked') {
                                openapi_class = 'text-danger';
                            } else if (row.c_openapiStatus == 'Freeze') {
                                openapi_class = 'text-info';
                            }

                            return '<div class="d-flex flex-column">' +
                                   '    <span class="mb-2 badge ' + status_bg + ' ' + status_text + ' rounded-pill px-3 py-1" style="width: fit-content;">' +
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
                            var baseUrl = "<?= base_url(); ?>"; 
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport"><i class="fas fa-ellipsis-v"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="dropdown-item" href="${baseUrl}merchant/manage/detail/${row.id}"><i class="fas fa-eye text-primary"></i> Detail Merchant</a></li>
                                        <li><a class="dropdown-item" href="${baseUrl}merchant/manage/edit/${row.id}"><i class="fas fa-edit text-info"></i> Edit Merchant</a></li>
                                        <li><a class="dropdown-item" href="${baseUrl}finance/mutation/${row.id}"><i class="fas fa-exchange-alt text-primary"></i> Mutation Log</a></li>
                                        <li><a class="dropdown-item" href="${baseUrl}merchant/sub-account/${row.id}"><i class="fas fa-users text-success"></i> Sub Accounts</a></li>
                                        ${row.c_merchantLevel == 0 ? `<li><button class="dropdown-item" data-toggle="modal" data-target="#delegateModal" onClick="openDelegateModal(${row.id}, '${row.c_name.replace(/'/g, "\\'")}')"><i class="fas fa-key text-warning"></i> Delegate</button></li>` : ''}
                                        ${row.hasBalancePermission ? `
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button class="dropdown-item" data-toggle="modal" data-target="#creditBalanceModal" onClick="detail(${row.id}, '${row.c_name.replace(/'/g, "\\'")}')"><i class="fas fa-plus-circle text-success"></i> Credit Balance</button></li>
                                            <li><button class="dropdown-item" data-toggle="modal" data-target="#debitBalanceModal" onClick="detaildebit(${row.id}, '${row.c_name.replace(/'/g, "\\'")}')"><i class="fas fa-minus-circle text-danger"></i> Debit Balance</button></li>
                                        ` : ''}
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="${baseUrl}merchant/setting-cashin-fee/${row.id}"><i class="fas fa-cog text-secondary"></i> Cashin Fee</a></li>
                                        <li><a class="dropdown-item" href="${baseUrl}merchant/setting-cashout-fee/${row.id}"><i class="fas fa-cog text-secondary"></i> Cashout Fee</a></li>
                                    </ul>
                                </div>
                            `;
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

                // Drawer Logic
                $('#toggleGuideBtn').on('click', function() {
                    $('#instructionDrawer, #instructionOverlay').addClass('open');
                    $('body').css('overflow', 'hidden');
                });

                $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
                    $('#instructionDrawer, #instructionOverlay').removeClass('open');
                    $('body').css('overflow', '');
                });

                // ── More Filters dropdown ──
                var $moreBtn   = $('#merchantMoreFiltersBtn');
                var $morePanel = $('#merchantMoreFiltersPanel');
                var $moreClose = $('#merchantMoreFiltersClose');

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

                // Select2 for ALL selects inside the More Filters panel
                $('#merchantMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
                    $(this).select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        dropdownParent: $('body'),
                        minimumResultsForSearch: 0
                    });
                });

                // Global search with Debounce
                $('#merchantGlobalSearch').on('input', debounce(function() {
                    table.search(this.value).draw();
                }, 400));

                // ── Premium Date Range Picker — Registration Date filter ──
                (function() {
                    var fromSession = '<?= $this->session->userdata('search_merchant_date_from'); ?>';
                    var toSession   = '<?= $this->session->userdata('search_merchant_date_to'); ?>';

                    // Use session values or default to empty
                    var defaultStart = fromSession ? fromSession : '';
                    var defaultEnd   = toSession   ? toSession   : '';

                    // Write initial values into hidden inputs
                    $('#search_merchant_date_from').val(defaultStart);
                    $('#search_merchant_date_to').val(defaultEnd);

                    new PremiumDateRangePicker('#merchantRegDateTrigger', {
                        startInput:  '#search_merchant_date_from',
                        endInput:    '#search_merchant_date_to',
                        displayText: '#merchant-reg-date-display'
                    });
                })();
            });

            function detail(id, name) {
                document.getElementById('merchantId').value = id;
                document.getElementById('merchantName').value = name;
                // Re-init Select2 dengan dropdownParent agar tidak terpotong modal (#12)
                setTimeout(function() {
                    $('#creditChannelId').select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        dropdownParent: $('#creditChannelId').parent()
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
                        dropdownParent: $('#debitChannelId').parent()
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
                    url: '<?= base_url('merchant/permissions/') ?>' + id,
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

                // Data update: Explicitly uncheck all radio buttons in this group first to prevent duplicate serialization
                $parentGroup.find('input[type="radio"]').prop('checked', false).removeAttr('checked');
                // Then check the clicked one
                $input.prop('checked', true).attr('checked', 'checked');
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
                    title: 'Save Permission Changes?',
                    html: 'Maximum hierarchy permissions for <strong class="text-primary">' + merchantName + '</strong> will be updated immediately. Do you want to proceed?',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Yes, Save Changes',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm mr-2',
                        cancelButton: 'swal2-premium-cancel'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    const $btn = $('#btnSaveDelegation');
                    const originalBtnHtml = $btn.html();

                    // Loading State
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> SAVING...');

                    $.ajax({
                        url: '<?= base_url('merchant/permissions/') ?>'+id+'/save',
                        type: 'POST',
                        data: $form.serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') { 
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'Saved Successfully!', 
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    customClass: { popup: 'swal2-premium-popup' }
                                }); 
                                $('#delegateModal').modal('hide'); 
                            } else {
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: 'Failed', 
                                    text: response.message,
                                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                    buttonsStyling: false
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Connection Error', 
                                text: 'Unable to connect to the server. Please try again.',
                                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                buttonsStyling: false
                            });
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
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Invalid Amount', 
                        text: 'Please enter a valid credit amount greater than zero.',
                        customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                        buttonsStyling: false
                    });
                    return;
                }
                $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
                $.ajax({
                    url: '<?= base_url('merchant/balance/credit') ?>',
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
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Success!', 
                                text: response.message || 'Credit balance has been successfully added.', 
                                timer: 2500, 
                                showConfirmButton: false,
                                customClass: { popup: 'swal2-premium-popup' }
                            });
                            $('#creditBalanceModal').modal('hide');
                            $('#creditBalanceForm')[0].reset();
                            if (window.merchantTableInstance) window.merchantTableInstance.draw('page');
                        } else {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Failed', 
                                text: (response && response.message) ? response.message : 'An error occurred while processing credit balance.',
                                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status !== 200) {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Connection Error', 
                                text: 'Unable to connect to the server. Please try again.',
                                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                buttonsStyling: false
                            });
                        } else {
                            window.location.href = '<?= base_url('merchant/balance/credit') ?>';
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
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Invalid Amount', 
                        text: 'Please enter a valid debit amount greater than zero.',
                        customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                        buttonsStyling: false
                    });
                    return;
                }
                $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
                $.ajax({
                    url: '<?= base_url('merchant/balance/debit') ?>',
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
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Success!', 
                                text: response.message || 'Debit balance deduction has been successfully processed.', 
                                timer: 2500, 
                                showConfirmButton: false,
                                customClass: { popup: 'swal2-premium-popup' }
                            });
                            $('#debitBalanceModal').modal('hide');
                            $('#debitBalanceForm')[0].reset();
                            if (window.merchantTableInstance) window.merchantTableInstance.draw('page');
                        } else {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Failed', 
                                text: (response && response.message) ? response.message : 'An error occurred while processing debit balance.',
                                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status !== 200) {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Connection Error', 
                                text: 'Unable to connect to the server. Please try again.',
                                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                buttonsStyling: false
                            });
                        } else {
                            window.location.href = '<?= base_url('merchant/balance/debit') ?>';
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
