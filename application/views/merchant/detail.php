<div class="glass-container position-relative">
    <div class="glass-aurora-1"></div>
    <div class="glass-aurora-2"></div>
    <div class="glass-aurora-3"></div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Merchant Detail Dashboard Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This dashboard provides a comprehensive 360-degree overview of the selected merchant's account details, financials, and OpenAPI credentials.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Balance Summary</div>
                <p class="drawer-card-text">Shows Total Balance, Hold Balance (escrowed/reserved), and Available Balance (Total minus Hold) for instant payout operations.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-chart-line text-primary mr-2"></i> Audit Overview</div>
                <p class="drawer-card-text">Monitor transaction volume trends, conversion success rates, and total charged fees. Includes a real-time recent activity stream of transactions across all payment channels.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-id-card text-primary mr-2"></i> Merchant Information</div>
                <p class="drawer-card-text">View the merchant's profile, contact details, status, and OpenAPI settings such as API callback URLs and IP address restrict options.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-shopping-cart text-primary mr-2"></i> Transaction History & Mutation</div>
                <p class="drawer-card-text">Browse the history table to trace individual payments, or inspect the Mutation Log to audit balance movements with precise debits and credits.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-users text-primary mr-2"></i> Sub Accounts</div>
                <p class="drawer-card-text">Manage child sub-merchants underneath this parent account. Sub-accounts inherit the fee configurations and limits defined by the parent.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header (Ultra-Modern Fintech Header) ── -->
    <div class="dt-page-header mb-3 mb-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center w-100 gap-2">
            <div class="d-flex align-items-center min-w-0 mr-auto w-100 w-md-auto">
                <a href="<?= base_url('merchant/manage') ?>" class="btn-dt-action btn-dt-action-outline-secondary border-0 mr-2 text-decoration-none d-inline-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 36px; height: 36px; border-radius: 10px;" title="Back to Merchant List">
                    <i class="fas fa-arrow-left" style="font-size: 13px;"></i>
                </a>
                <div class="min-w-0 flex-grow-1">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <h4 class="dt-page-title mb-0 text-truncate font-weight-bold" style="font-size: 1.15rem; letter-spacing: -0.3px;">
                            <?= $merchant['c_name']; ?>
                        </h4>
                        <span class="badge badge-primary-soft text-primary font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 10.5px; background: rgba(99, 102, 241, 0.12);">#<?= $merchant['id']; ?></span>
                    </div>
                    <p class="dt-page-subtitle mb-0 text-muted d-none d-sm-block" style="font-size: 12px; line-height: 1.35;">Merchant Dashboard & Transaction Analytics</p>
                </div>
            </div>
            
            <!-- Modern Integrated Action Toolbar -->
            <div class="action-toolbar-mobile p-1 rounded-lg d-flex align-items-center gap-2 w-100 w-md-auto mt-2 mt-md-0 ml-md-auto" style="background: rgba(241, 245, 249, 0.85); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 12px;">
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none font-weight-bold flex-fill flex-md-grow-0 text-dark" id="toggleGuideBtn" style="height: 34px; border-radius: 9px; font-size: 12px;">
                    <i class="fas fa-book-open text-primary mr-1"></i> Guide
                </button>
                <a href="<?= base_url('merchant/manage/edit/' . $merchant['id']); ?>" class="btn btn-sm btn-primary border-0 shadow-sm font-weight-bold action-confirm-link text-decoration-none flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center" data-title="Edit Merchant Profile?" data-text="You will be redirected to the merchant configuration page. Do you wish to continue?" data-confirm-btn="Yes, Edit Merchant" data-icon="info" style="height: 34px; border-radius: 9px; font-size: 12px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <div class="dropdown flex-fill flex-md-grow-0">
                    <button class="btn btn-sm btn-light border-0 shadow-none dropdown-toggle font-weight-bold w-100 text-dark d-inline-flex align-items-center justify-content-center" type="button" data-toggle="dropdown" aria-expanded="false" style="height: 34px; border-radius: 9px; font-size: 12px;">
                        <i class="fas fa-ellipsis-v mr-1 text-muted"></i> Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right shadow-lg border-0 py-2" style="border-radius: 12px; font-size: 12.5px;">
                        <li>
                            <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/balance/credit?merchant_id=' . $merchant['id']) ?>" data-title="Add Credit Balance?" data-text="You are about to initiate a credit balance adjustment for <?= $merchant['c_name']; ?>. Proceed?" data-confirm-btn="Yes, Add Credit" data-icon="question">
                                <i class="fas fa-plus-circle text-success mr-2"></i> Add Credit Balance
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/balance/debit?merchant_id=' . $merchant['id']) ?>" data-title="Deduct Debit Balance?" data-text="You are about to initiate a debit balance deduction for <?= $merchant['c_name']; ?>. Proceed?" data-confirm-btn="Yes, Deduct Balance" data-icon="warning">
                                <i class="fas fa-minus-circle text-danger mr-2"></i> Deduct Debit Balance
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/setting-cashin-fee/' . $merchant['id']) ?>" data-title="Cashin Fee Settings?" data-text="Configure inbound transaction fee structures for this merchant. Proceed?" data-confirm-btn="Yes, Configure Fees" data-icon="info">
                                <i class="fas fa-cog text-info mr-2"></i> Cashin Fee Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/setting-cashout-fee/' . $merchant['id']) ?>" data-title="Cashout Fee Settings?" data-text="Configure outbound disbursement fee structures for this merchant. Proceed?" data-confirm-btn="Yes, Configure Fees" data-icon="info">
                                <i class="fas fa-cog text-warning mr-2"></i> Cashout Fee Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Script -->
    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= $this->session->flashdata('success'); ?>',
                    icon: 'success',
                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                    icon: 'error',
                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                    buttonsStyling: false
                });
            <?php endif; ?>

            // Premium Confirmation Popup for Action Links
            $('.action-confirm-link').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const title = $(this).data('title') || 'Confirm Action';
                const text = $(this).data('text') || 'Are you sure you want to proceed with this action?';
                const confirmBtn = $(this).data('confirm-btn') || 'Yes, Proceed';
                const icon = $(this).data('icon') || 'warning';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check mr-2"></i> ' + confirmBtn,
                    cancelButtonText: '<i class="fas fa-times mr-2"></i> Cancel',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm mr-2',
                        cancelButton: 'swal2-premium-cancel'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>

    <!-- ── KPI Summary Cards (Modern Fintech Card Design with Left Gradient Accent Borders) ── -->
    <div class="row mx-n1 mb-3 mb-md-4">
        <!-- Total Balance -->
        <div class="col-12 col-md-4 px-1 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 14px; background: #ffffff; border-left: 4px solid #6366f1 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 10.5px; letter-spacing: 0.5px;">Total Balance</span>
                        <div class="avatar-xs text-primary rounded-circle flex-shrink-0" style="background-color: rgba(99, 102, 241, 0.1); width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-wallet" style="font-size: 12.5px;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-1 responsive-h3" style="font-size: 1.25rem;">Rp <?= number_format($merchant['c_balanceTotal'], 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0" style="font-size: 11px;"><i class="fas fa-shield-alt text-success mr-1"></i> Real-time ledger balance</p>
                </div>
            </div>
        </div>

        <!-- Hold Balance -->
        <div class="col-12 col-md-4 px-1 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 14px; background: #ffffff; border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 10.5px; letter-spacing: 0.5px;">Hold Balance</span>
                        <div class="avatar-xs text-warning rounded-circle flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.1); width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-lock" style="font-size: 12.5px;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold text-warning mb-1 responsive-h3" style="font-size: 1.25rem;">Rp <?= number_format($merchant['c_balanceHold'], 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0" style="font-size: 11px;"><i class="fas fa-exclamation-circle text-warning mr-1"></i> Reserved for pending payouts</p>
                </div>
            </div>
        </div>

        <!-- Available Balance -->
        <div class="col-12 col-md-4 px-1">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 14px; background: #ffffff; border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 10.5px; letter-spacing: 0.5px;">Available Balance</span>
                        <div class="avatar-xs text-success rounded-circle flex-shrink-0" style="background-color: rgba(16, 185, 129, 0.1); width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="font-size: 12.5px;"></i>
                        </div>
                    </div>
                    <?php $available = $merchant['c_balanceTotal'] - $merchant['c_balanceHold']; ?>
                    <h3 class="font-weight-bold text-success mb-1 responsive-h3" style="font-size: 1.25rem;">Rp <?= number_format($available, 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0" style="font-size: 11px;"><i class="fas fa-bolt text-success mr-1"></i> Instantly spendable funds</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Ultra-Modern Segmented Capsule Tab Bar ── -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 16px; background: #ffffff;">
        <div class="card-header bg-white border-bottom p-2 p-md-3">
            <div class="dt-nav-tabs-scroll" style="background: rgba(241, 245, 249, 0.85); padding: 5px; border-radius: 14px; border: 1px solid rgba(226, 232, 240, 0.8);">
                <ul class="nav nav-pills dt-nav-tabs border-0 flex-nowrap gap-1 mb-0" id="merchantDetailTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active font-weight-bold py-2 px-3 border-0" id="overview-tab" data-toggle="tab" href="#nav-overview" role="tab" aria-controls="nav-overview" aria-selected="true" style="font-size: 12px; border-radius: 10px;">
                            <i class="fas fa-chart-line mr-1 text-primary"></i> Audit Overview
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-2 px-3 border-0" id="profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="font-size: 12px; border-radius: 10px;">
                            <i class="fas fa-id-card mr-1 text-primary"></i> Information
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-2 px-3 border-0" id="history-tab" data-toggle="tab" href="#nav-history" role="tab" aria-controls="nav-history" aria-selected="false" style="font-size: 12px; border-radius: 10px;">
                            <i class="fas fa-shopping-cart mr-1 text-info"></i> History
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-2 px-3 border-0" id="mutation-tab" data-toggle="tab" href="#nav-mutation" role="tab" aria-controls="nav-mutation" aria-selected="false" style="font-size: 12px; border-radius: 10px;">
                            <i class="fas fa-exchange-alt mr-1 text-warning"></i> Mutation Log
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-2 px-3 border-0" id="submerchant-tab" data-toggle="tab" href="#nav-submerchant" role="tab" aria-controls="nav-submerchant" aria-selected="false" style="font-size: 12px; border-radius: 10px;">
                            <i class="fas fa-users mr-1 text-success"></i> Sub Accounts
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="merchantDetailTabContent">
                
                <!-- ── TAB 0: TRANSACTION OVERVIEW ── -->
                <div class="tab-pane fade show active p-2 p-md-4" id="nav-overview" role="tabpanel" aria-labelledby="overview-tab">
                    
                    <!-- Date Filter & Audit Actions Bar -->
                    <div class="card border-0 mb-3 shadow-sm" style="border-radius: 12px; background: rgba(248, 250, 252, 0.95); border: 1px solid rgba(226, 232, 240, 0.8);">
                        <div class="card-body p-2 p-sm-3">
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="text-dark font-weight-bold small"><i class="fas fa-filter text-primary mr-1"></i> Audit Range:</span>
                                    <div class="d-flex align-items-center gap-1 flex-fill">
                                        <input type="date" id="overview-start-date" class="form-control form-control-sm border-0 shadow-sm" value="<?= date('Y-m-d', strtotime('-30 days')) ?>" style="border-radius: 8px; font-size: 11px; height: 34px; background: #ffffff;">
                                        <span class="text-muted small px-1">to</span>
                                        <input type="date" id="overview-end-date" class="form-control form-control-sm border-0 shadow-sm" value="<?= date('Y-m-d') ?>" style="border-radius: 8px; font-size: 11px; height: 34px; background: #ffffff;">
                                    </div>
                                </div>
                                <button id="btn-overview-filter" class="btn btn-primary font-weight-bold px-3 d-inline-flex align-items-center justify-content-center shadow-sm mt-2 mt-sm-0" style="border-radius: 8px; font-size: 12px; height: 34px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none;">
                                    <i class="fas fa-sync mr-1"></i> Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Metric Cards Grid (Standard Bootstrap 4 Integer Utility Classes) -->
                    <div class="row mx-n1 mb-3 mb-md-4">
                        <div class="col-6 col-lg-3 px-1 mb-2 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-uppercase small font-weight-bold text-muted text-truncate mr-1" style="font-size: 9.5px; letter-spacing: 0.3px;">Total Tx</span>
                                    <div class="avatar-xs rounded-circle text-primary flex-shrink-0" style="background: rgba(99, 102, 241, 0.1); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-shopping-cart" style="font-size: 10px;"></i>
                                    </div>
                                </div>
                                <h4 id="stat-total-cnt" class="font-weight-bold text-dark mb-0 responsive-h3" style="font-size: 1.05rem;">0</h4>
                                <p class="small mb-0 text-muted text-truncate" style="font-size: 9.5px;">All channels</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 px-1 mb-2 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-uppercase small font-weight-bold text-muted text-truncate mr-1" style="font-size: 9.5px; letter-spacing: 0.3px;">Success Vol</span>
                                    <div class="avatar-xs rounded-circle text-success flex-shrink-0" style="background: rgba(16, 185, 129, 0.1); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    </div>
                                </div>
                                <h4 id="stat-success-amt" class="font-weight-bold text-success mb-0 responsive-h3" style="font-size: 1.05rem;">Rp 0</h4>
                                <p class="small mb-0 text-muted text-truncate" style="font-size: 9.5px;"><span id="stat-success-cnt">0</span> successful</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 px-1 mb-2 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-uppercase small font-weight-bold text-muted text-truncate mr-1" style="font-size: 9.5px; letter-spacing: 0.3px;">Total Fees</span>
                                    <div class="avatar-xs rounded-circle text-info flex-shrink-0" style="background: rgba(6, 182, 212, 0.1); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-percent" style="font-size: 10px;"></i>
                                    </div>
                                </div>
                                <h4 id="stat-total-fee" class="font-weight-bold text-dark mb-0 responsive-h3" style="font-size: 1.05rem;">Rp 0</h4>
                                <p class="small mb-0 text-muted text-truncate" style="font-size: 9.5px;">MDR & Admin</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 px-1 mb-2 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-uppercase small font-weight-bold text-muted text-truncate mr-1" style="font-size: 9.5px; letter-spacing: 0.3px;">Conversion</span>
                                    <div class="avatar-xs rounded-circle text-warning flex-shrink-0" style="background: rgba(245, 158, 11, 0.1); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-percentage" style="font-size: 10px;"></i>
                                    </div>
                                </div>
                                <h4 id="stat-success-rate" class="font-weight-bold text-warning mb-0 responsive-h3" style="font-size: 1.05rem;">0%</h4>
                                <p class="small mb-0 text-muted text-truncate" style="font-size: 9.5px;">Success ratio</p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12 mb-4">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-chart-area text-primary mr-1"></i> Transaction Volume Trend (SUCCESS)</h6>
                                </div>
                                <div class="card-body p-3 p-md-4">
                                    <div class="chart-area" style="position: relative; height: 300px;">
                                        <canvas id="overviewTrendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-chart-pie text-info mr-1"></i> Payment Method Breakdown</h6>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-center">
                                    <div class="chart-pie mb-3" style="position: relative; height: 210px;">
                                        <canvas id="overviewBreakdownChart"></canvas>
                                    </div>
                                    <div id="breakdown-legend" class="text-center small mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-chart-pie text-warning mr-1"></i> Status Distribution</h6>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-center">
                                    <div class="chart-pie mb-3" style="position: relative; height: 210px;">
                                        <canvas id="overviewStatusChart"></canvas>
                                    </div>
                                    <div id="status-legend" class="text-center small mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-history text-primary mr-1"></i> Recent Activity</h6>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-start">
                                    <div id="recent-activity-list" class="recent-activity-timeline">
                                        <!-- Rendered dynamically by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Chart & Merchant & Sub-Account Transactions (Fully Responsive: Cards on Mobile, Table on Desktop) -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-users text-primary mr-1"></i> Merchant & Sub-Account Transaction Overview</h6>
                                </div>
                                <div class="card-body p-2 p-md-0">
                                    <!-- Mobile List Cards View (< 768px) -->
                                    <div class="d-block d-md-none" id="submerchantAuditMobileCards">
                                        <!-- Dynamically generated in JS -->
                                    </div>

                                    <!-- Desktop Table View (>= 768px) -->
                                    <div class="d-none d-md-block">
                                        <div class="table-responsive" style="max-height: 310px; overflow-y: auto;">
                                            <table class="table mb-0" id="submerchantAuditTable" style="min-width: 580px;">
                                                <thead class="bg-light" style="position: sticky; top: 0; z-index: 10;">
                                                    <tr>
                                                        <th>Account Name / Email</th>
                                                        <th class="text-center">Total Tx</th>
                                                        <th class="text-center">Success Tx</th>
                                                        <th class="text-center">Success Rate</th>
                                                        <th class="text-right">Success Amount (Volume)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Will be filled by JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Table Breakdown (Fully Responsive: Cards on Mobile, Table on Desktop) -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8);">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-dark" style="font-size: 13.5px;"><i class="fas fa-table text-success mr-1"></i> Channel Audit Breakdown</h6>
                        </div>
                        <div class="card-body p-2 p-md-0">
                            <!-- Mobile List Cards View (< 768px) -->
                            <div class="d-block d-md-none" id="channelAuditMobileCards">
                                <!-- Dynamically generated in JS -->
                            </div>

                            <!-- Desktop Table View (>= 768px) -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="channelAuditTable" style="min-width: 580px;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Payment Method</th>
                                                <th class="text-center">Total Tx</th>
                                                <th class="text-center">Success Tx</th>
                                                <th class="text-right">Total Amount</th>
                                                <th class="text-right">Total Fee</th>
                                                <th class="text-right">Success Amount (Net)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Will be filled by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB 1: MERCHANT INFORMATION ── -->
                <div class="tab-pane fade p-3 p-md-4" id="nav-profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <!-- Kolom Kiri: Core Profile -->
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="card border-0 shadow-sm h-100 dt-form-card" style="border-radius: 16px;">
                                <div class="card-header bg-transparent border-bottom p-3 p-md-4 d-flex align-items-center">
                                    <div class="avatar-sm bg-primary-soft text-primary rounded-circle p-2 mr-3" style="background-color: rgba(99, 102, 241, 0.12); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user-tie fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Core Profile</h6>
                                        <p class="text-muted small mb-0" style="font-size: 12px;">Basic information and account legalities</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building text-primary mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Merchant Name</span>
                                        </div>
                                        <span class="font-weight-bold text-dark text-right small"><?= $merchant['c_name']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-info mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Official Email</span>
                                        </div>
                                        <span class="font-weight-bold text-dark text-right small text-truncate ml-2" style="max-width: 220px;"><?= $merchant['c_email']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone-alt text-success mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Phone Number</span>
                                        </div>
                                        <span class="font-weight-bold text-dark text-right small"><?= $merchant['c_phoneNumber'] ?: '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-check text-warning mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Registration Date</span>
                                        </div>
                                        <span class="font-weight-bold text-dark text-right small"><?= $merchant['c_dateCreated'] ? date('d M Y, H:i', strtotime($merchant['c_dateCreated'])) : '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group text-purple mr-3" style="width: 18px; color: #b833ff;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Merchant Level</span>
                                        </div>
                                        <span class="badge badge-pill badge-info-soft text-info px-3 py-1 font-weight-bold" style="font-size: 11px;">Level <?= $merchant['c_merchantLevel']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-success mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Account Status</span>
                                        </div>
                                        <?php $st = $merchant['c_status']; $cls = ($st=='Active')?'success':'secondary'; ?>
                                        <span class="badge badge-pill badge-<?= $cls; ?>-soft text-<?= $cls; ?> px-3 py-1 font-weight-bold" style="font-size: 11px;"><?= $st; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: OpenAPI Config -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100 dt-form-card" style="border-radius: 16px;">
                                <div class="card-header bg-transparent border-bottom p-3 p-md-4 d-flex align-items-center">
                                    <div class="avatar-sm bg-purple-soft text-purple rounded-circle p-2 mr-3" style="background-color: rgba(184, 51, 255, 0.12); color: #b833ff; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-network-wired fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">OpenAPI & Integration Config</h6>
                                        <p class="text-muted small mb-0" style="font-size: 12px;">Technical parameters, callbacks, and credentials</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-plug text-success mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">OpenAPI Status</span>
                                        </div>
                                        <?php $ost = $merchant['c_openapiStatus']; $ocls = ($ost=='Active')?'success':'danger'; ?>
                                        <span class="badge badge-pill badge-<?= $ocls; ?>-soft text-<?= $ocls; ?> px-3 py-1 font-weight-bold" style="font-size: 11px;"><?= $ost; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-link text-info mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">VA Callback URL</span>
                                        </div>
                                        <div class="px-2 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 200px; background: rgba(0, 0, 0, 0.04); font-size: 11px;" title="<?= $merchant['c_openapiUrlCallbackVa']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackVa'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-qrcode text-warning mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">QRIS Callback URL</span>
                                        </div>
                                        <div class="px-2 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 200px; background: rgba(0, 0, 0, 0.04); font-size: 11px;" title="<?= $merchant['c_openapiUrlCallbackQrisMpm']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackQrisMpm'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-wallet text-primary mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">E-Wallet Callback</span>
                                        </div>
                                        <div class="px-2 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 200px; background: rgba(0, 0, 0, 0.04); font-size: 11px;" title="<?= $merchant['c_openapiUrlCallbackEwallet']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackEwallet'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-id-badge text-purple mr-3" style="width: 18px; color: #b833ff;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">GVConnect ID</span>
                                        </div>
                                        <span class="font-weight-bold text-dark text-right small"><?= $merchant['c_gvconnectBusinessId'] ?: '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 px-md-4">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-shield-alt text-danger mr-3" style="width: 18px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">IP Allowlist</span>
                                        </div>
                                        <div class="px-2 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 200px; background: rgba(0, 0, 0, 0.04); font-size: 11px;" title="<?= $merchant['c_openapiIPAllow']; ?>">
                                            <?= $merchant['c_openapiIPAllow'] ?: '<em class="text-muted">Any IP Allowed</em>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB 2: TRANSACTION HISTORY ── -->
                <div class="tab-pane fade p-3 p-md-4" id="nav-history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailHistoryTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Merchant</th>
                                    <th>Date Time</th>
                                    <th>Product ID</th>
                                    <th>Invoice No</th>
                                    <th>Customer No</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- ── TAB 3: MUTATION LOG ── -->
                <div class="tab-pane fade p-3 p-md-4" id="nav-mutation" role="tabpanel" aria-labelledby="mutation-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailMutationTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Date Time</th>
                                    <th>Position</th>
                                    <th>Channel</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- ── TAB 4: SUB ACCOUNTS ── -->
                <div class="tab-pane fade p-3 p-md-4" id="nav-submerchant" role="tabpanel" aria-labelledby="submerchant-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailSubmerchantTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Submerchant Name</th>
                                    <th>Email Address</th>
                                    <th>Status</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const merchantId = "<?= $merchant['id'] ?>";
    let historyTableInit = false;
    let mutationTableInit = false;
    let submerchantTableInit = false;

    let trendChart = null;
    let breakdownChart = null;
    let statusChart = null;

    function loadOverview() {
        const startDate = $('#overview-start-date').val();
        const endDate = $('#overview-end-date').val();

        $('#stat-total-cnt').text('...');
        $('#stat-success-amt').text('...');
        $('#stat-total-fee').text('...');
        $('#stat-success-rate').text('...');

        $.ajax({
            url: "<?= base_url('merchant/manage/overview-ajax/') ?>" + merchantId,
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const summary = response.summary;
                    const channels = response.channels;
                    const trend = response.trend;

                    $('#stat-total-cnt').text(number_format(summary.total_cnt));
                    $('#stat-success-amt').text('Rp ' + number_format(summary.success_amt, 0, ',', '.'));
                    $('#stat-success-cnt').text(number_format(summary.success_cnt));
                    $('#stat-total-fee').text('Rp ' + number_format(summary.total_fee, 0, ',', '.'));
                    
                    const successRate = summary.total_cnt > 0 ? ((summary.success_cnt / summary.total_cnt) * 100).toFixed(1) : 0;
                    $('#stat-success-rate').text(successRate + '%');

                    // Render Channel Audit Breakdown (Desktop Table & Mobile Cards)
                    let tableBody = '';
                    let mobileCardsBody = '';
                    const channelNames = ['PPOB', 'VA', 'QRIS', 'EWallet', 'BiFast'];

                    channelNames.forEach(function(chan) {
                        const data = channels[chan];
                        const netAmount = data.success_amt;
                        const rate = data.cnt > 0 ? ((data.success_cnt / data.cnt) * 100).toFixed(1) : 0;
                        
                        // Desktop Table Row
                        tableBody += `
                            <tr>
                                <td class="py-3">
                                    <div class="font-weight-bold text-dark">${chan}</div>
                                </td>
                                <td class="text-center font-weight-bold text-gray-800 py-3">${number_format(data.cnt)}</td>
                                <td class="text-center py-3">
                                    <span class="badge badge-pill badge-success-soft text-success px-2 py-1">${number_format(data.success_cnt)}</span>
                                    <small class="text-muted d-block mt-1">${rate}% Success</small>
                                </td>
                                <td class="text-right text-gray-600 py-3">Rp ${number_format(data.amt, 0, ',', '.')}</td>
                                <td class="text-right text-danger py-3">Rp ${number_format(data.fee, 0, ',', '.')}</td>
                                <td class="text-right font-weight-bold text-success py-3">Rp ${number_format(netAmount, 0, ',', '.')}</td>
                            </tr>
                        `;

                        // Mobile List Card
                        mobileCardsBody += `
                            <div class="card border-0 shadow-sm mb-2 p-3" style="border-radius: 12px; background: rgba(248, 250, 252, 0.95); border: 1px solid rgba(226, 232, 240, 0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 11px;">${chan}</span>
                                    <span class="badge badge-pill badge-success-soft text-success px-2 py-0.5" style="font-size: 10.5px;">${rate}% Success</span>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4 border-right px-1">
                                        <small class="text-muted text-uppercase d-block" style="font-size: 9px;">Total Tx</small>
                                        <span class="font-weight-bold text-dark" style="font-size: 12px;">${number_format(data.cnt)}</span>
                                    </div>
                                    <div class="col-4 border-right px-1">
                                        <small class="text-muted text-uppercase d-block" style="font-size: 9px;">Success Tx</small>
                                        <span class="font-weight-bold text-success" style="font-size: 12px;">${number_format(data.success_cnt)}</span>
                                    </div>
                                    <div class="col-4 px-1">
                                        <small class="text-muted text-uppercase d-block" style="font-size: 9px;">Total Fee</small>
                                        <span class="font-weight-bold text-danger" style="font-size: 11px;">Rp ${number_format(data.fee, 0, ',', '.')}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <small class="text-muted font-weight-bold" style="font-size: 10px;">SUCCESS NET VOLUME</small>
                                    <span class="font-weight-bold text-success" style="font-size: 12.5px;">Rp ${number_format(netAmount, 0, ',', '.')}</span>
                                </div>
                            </div>
                        `;
                    });
                    $('#channelAuditTable tbody').html(tableBody);
                    $('#channelAuditMobileCards').html(mobileCardsBody);

                    // Render Submerchant Overview (Desktop Table & Mobile Cards)
                    let subBody = '';
                    let subMobileCardsBody = '';

                    if (response.sub_merchants && response.sub_merchants.length > 0) {
                        response.sub_merchants.forEach(function(sub) {
                            const rate = sub.total_cnt > 0 ? ((sub.success_cnt / sub.total_cnt) * 100).toFixed(1) : 0;
                            
                            // Desktop Table Row
                            subBody += `
                                <tr>
                                    <td class="py-3">
                                        <div class="font-weight-bold text-dark">${sub.sub_merchant_name}</div>
                                        <small class="text-muted">${sub.sub_merchant_email} (ID: ${sub.sub_merchant_id})</small>
                                    </td>
                                    <td class="text-center font-weight-bold text-gray-800 py-3">${number_format(sub.total_cnt)}</td>
                                    <td class="text-center py-3">
                                        <span class="badge badge-pill badge-success-soft text-success px-2 py-1">${number_format(sub.success_cnt)}</span>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="font-weight-bold text-dark">${rate}%</div>
                                    </td>
                                    <td class="text-right font-weight-bold text-success py-3">Rp ${number_format(sub.success_amt, 0, ',', '.')}</td>
                                </tr>
                            `;

                            // Mobile List Card
                            subMobileCardsBody += `
                                <div class="card border-0 shadow-sm mb-2 p-3" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="font-size: 12.5px; max-width: 220px;">${sub.sub_merchant_name}</h6>
                                            <small class="text-muted text-truncate d-block" style="font-size: 10.5px; max-width: 220px;">${sub.sub_merchant_email} (ID: ${sub.sub_merchant_id})</small>
                                        </div>
                                        <span class="badge badge-pill badge-primary-soft text-primary font-weight-bold px-2 py-0.5" style="font-size: 10px;">${rate}% Rate</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">TX: <strong>${number_format(sub.total_cnt)}</strong> | SUCCESS: <strong class="text-success">${number_format(sub.success_cnt)}</strong></small>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted d-block" style="font-size: 9.5px;">TOTAL VOLUME</small>
                                            <span class="font-weight-bold text-success" style="font-size: 12px;">Rp ${number_format(sub.success_amt, 0, ',', '.')}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        subBody = `
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> No sub-merchants found or active under this account.
                                </td>
                            </tr>
                        `;
                        subMobileCardsBody = `
                            <div class="text-center py-4 text-muted border rounded" style="background: rgba(248, 250, 252, 0.5); border-radius: 12px;">
                                <i class="fas fa-info-circle mr-1"></i> No sub-merchants found under this account.
                            </div>
                        `;
                    }
                    $('#submerchantAuditTable tbody').html(subBody);
                    $('#submerchantAuditMobileCards').html(subMobileCardsBody);

                    if (trendChart) {
                        trendChart.destroy();
                    }
                    const ctxTrend = document.getElementById('overviewTrendChart').getContext('2d');
                    
                    const gradTotal = ctxTrend.createLinearGradient(0, 0, 0, 300);
                    gradTotal.addColorStop(0, 'rgba(78, 115, 223, 0.4)');
                    gradTotal.addColorStop(1, 'rgba(78, 115, 223, 0)');

                    trendChart = new Chart(ctxTrend, {
                        type: 'line',
                        data: {
                            labels: trend.labels.map(d => moment(d).format('DD MMM')),
                            datasets: [
                                {
                                    label: 'Total Volume',
                                    data: trend.datasets.total,
                                    borderColor: '#4e73df',
                                    backgroundColor: gradTotal,
                                    fill: true,
                                    tension: 0.3,
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Volume: Rp ' + number_format(context.raw, 0, ',', '.');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1e6) return 'Rp ' + (value / 1e6) + 'M';
                                            if (value >= 1e3) return 'Rp ' + (value / 1e3) + 'K';
                                            return 'Rp ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    if (breakdownChart) {
                        breakdownChart.destroy();
                    }
                    const ctxPie = document.getElementById('overviewBreakdownChart').getContext('2d');
                    
                    const breakdownData = channelNames.map(chan => channels[chan].success_amt);
                    const hasData = breakdownData.some(val => val > 0);

                    if (!hasData) {
                        breakdownChart = new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: ['No Data'],
                                datasets: [{
                                    data: [1],
                                    backgroundColor: ['#eaecf0'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { enabled: false } }
                            }
                        });
                        $('#breakdown-legend').html('<span class="text-muted">No successful transactions in this period</span>');
                    } else {
                        breakdownChart = new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: channelNames,
                                datasets: [{
                                    data: breakdownData,
                                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#b833ff'],
                                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });

                        let legendHtml = '<div class="row text-center mt-2">';
                        const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#b833ff'];
                        channelNames.forEach((name, idx) => {
                            const val = breakdownData[idx];
                            const pct = summary.success_amt > 0 ? ((val / summary.success_amt) * 100).toFixed(1) : 0;
                            legendHtml += `
                                <div class="col-6 mb-2 text-left">
                                    <span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${colors[idx]}"></span>
                                    <strong class="text-dark">${name}</strong>: ${pct}%
                                </div>
                            `;
                        });
                        legendHtml += '</div>';
                        $('#breakdown-legend').html(legendHtml);
                    }

                    // Render Status Distribution Chart
                    if (statusChart) {
                        statusChart.destroy();
                    }
                    const ctxStatus = document.getElementById('overviewStatusChart').getContext('2d');
                    
                    const statusColors = {
                        'Success': '#1cc88a',
                        'Pending': '#f6c23e',
                        'Process': '#36b9cc',
                        'Failed': '#e74a3b',
                        'Timeout': '#858796',
                        'Cancel': '#5a5c69',
                        'Init': '#4e73df'
                    };

                    const allStatuses = ['Success', 'Pending', 'Process', 'Failed', 'Timeout', 'Cancel', 'Init'];
                    const statusLabels = [];
                    const statusData = [];
                    const bgColors = [];

                    allStatuses.forEach(function(st) {
                        statusLabels.push(st);
                        const val = (response.statuses && response.statuses[st]) ? parseInt(response.statuses[st]) : 0;
                        statusData.push(val);
                        bgColors.push(statusColors[st]);
                    });

                    const totalStatusCount = statusData.reduce((a, b) => a + b, 0);

                    if (totalStatusCount === 0) {
                        statusChart = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: ['No Data'],
                                datasets: [{
                                    data: [1],
                                    backgroundColor: ['#eaecf0'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: { legend: { display: false }, tooltip: { enabled: false } }
                            }
                        });
                    } else {
                        statusChart = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: statusLabels,
                                datasets: [{
                                    data: statusData,
                                    backgroundColor: bgColors,
                                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Always generate and display the full legend
                    let statusLegendHtml = '<div class="row text-center mt-2">';
                    statusLabels.forEach((name, idx) => {
                        const val = statusData[idx];
                        const pct = totalStatusCount > 0 ? ((val / totalStatusCount) * 100).toFixed(1) : 0;
                        statusLegendHtml += `
                            <div class="col-6 mb-2 text-left">
                                <span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${bgColors[idx]}"></span>
                                <strong class="text-dark">${name}</strong>: ${val} (${pct}%)
                            </div>
                        `;
                    });
                    statusLegendHtml += '</div>';
                    $('#status-legend').html(statusLegendHtml);

                    // Render Recent Activity Timeline
                    let activityHtml = '';
                    if (response.recent_activity && response.recent_activity.length > 0) {
                        response.recent_activity.forEach(function(act) {
                            const isSuccess = act.c_status === 'SUCCESS' || act.c_status === 'Success';
                            const isFailed = act.c_status === 'FAILED' || act.c_status === 'Failed';
                            const isPending = act.c_status === 'PENDING' || act.c_status === 'Pending' || act.c_status === 'Process';
                            let statusClass = 'pending';
                            if (isSuccess) statusClass = 'success';
                            if (isFailed) statusClass = 'failed';

                            const channelClass = act.channel ? act.channel.toLowerCase() : '';
                            const formattedTime = moment(act.c_datetime).format('DD MMM, HH:mm');
                            const formattedAmount = 'Rp ' + number_format(act.c_amount, 0, ',', '.');
                            
                            activityHtml += `
                                <div class="timeline-item ${statusClass}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge-channel ${channelClass}">${act.channel || 'TXT'}</span>
                                            <span class="timeline-time">${formattedTime}</span>
                                        </div>
                                        <div class="timeline-body">
                                            ${formattedAmount}
                                        </div>
                                        <div class="timeline-footer">
                                            <span class="text-truncate mr-2" style="max-width: 140px;" title="${act.merchant_name || ''}">${act.merchant_name || 'Main Account'}</span>
                                            <span class="font-weight-bold text-gray-600">${act.c_invoiceNo || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        activityHtml = `
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-2x mb-3 text-gray-300"></i>
                                <p class="mb-0 small">No recent activity found in this period</p>
                            </div>
                        `;
                    }
                    $('#recent-activity-list').html(activityHtml);

                } else {
                    Swal.fire('Error', response.message || 'Failed to load audit overview data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch overview data from the server.', 'error');
            }
        });
    }

    // Load initial overview
    loadOverview();

    // Bind filter button click
    $('#btn-overview-filter').on('click', function() {
        loadOverview();
    });
    $('#overview-start-date, #overview-end-date').on('change', function() {
        loadOverview();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");

        if (target === "#nav-overview") {
            loadOverview();
        }

        // Init History Table
        if (target === "#nav-history" && !historyTableInit) {
            historyTableInit = true;
            initServerDataTable("#detailHistoryTable", "<?= base_url('merchant/manage/history-ajax/') ?>" + merchantId, [
                { data: 'no' },
                { data: 'name_merchant' },
                { data: 'c_datetime', render: function(data){ return moment(data).format('DD-MM-YYYY HH:mm:ss'); } },
                { data: 'ref_cashoutChannelId' },
                { data: 'c_invoiceNo' },
                { data: 'c_phone' },
                { data: 'c_amount', render: function(data){ return 'Rp ' + number_format(data, 0, ',', '.'); } },
                { data: 'c_status', render: function(data){
                    let badgeClass = 'badge-secondary';
                    if(data === 'SUCCESS') badgeClass = 'badge-success';
                    if(data === 'FAILED') badgeClass = 'badge-danger';
                    if(data === 'PENDING') badgeClass = 'badge-warning';
                    return '<span class="badge badge-pill ' + badgeClass + '">' + data + '</span>';
                }}
            ]);
        }

        // Init Mutation Table
        if (target === "#nav-mutation" && !mutationTableInit) {
            mutationTableInit = true;
            initServerDataTable("#detailMutationTable", "<?= base_url('merchant/manage/mutation-ajax/') ?>" + merchantId, [
                { data: 'no', orderable: false, className: 'text-center' },
                { data: 'c_datetime', render: function(data){ return '<i class="far fa-clock mr-1 text-muted"></i>' + (data ? data : '-'); } },
                { data: 'c_position_raw', render: function(data) {
                    if (!data) return '-';
                    const d = data.toLowerCase();
                    const cls = (d === 'credit') ? 'success' : 'danger';
                    return '<span class="badge badge-pill badge-' + cls + '-soft text-' + cls + ' px-3 py-1">' + data + '</span>';
                }},
                { data: 'channelName', render: function(data) { return '<span class="badge badge-light border px-2 py-1 text-uppercase small">' + (data || '-') + '</span>'; } },
                { data: 'description', className: 'small text-muted' },
                { data: 'c_amount_raw', className: 'text-right font-weight-bold', render: function(data, type, row){
                    const isCredit = (row.c_position_raw || '').toLowerCase() === 'credit';
                    const colorCls = isCredit ? 'text-success' : 'text-danger';
                    return '<span class="' + colorCls + '">Rp ' + number_format(data, 0, ',', '.') + '</span>';
                }},
                { data: 'c_balance_raw', className: 'text-right font-weight-bold text-dark', render: function(data){ return 'Rp ' + number_format(data, 0, ',', '.'); } }
            ]);
        }

        // Init Submerchant Table
        if (target === "#nav-submerchant" && !submerchantTableInit) {
            submerchantTableInit = true;
            initServerDataTable("#detailSubmerchantTable", "<?= base_url('merchant/manage/submerchant-ajax/') ?>" + merchantId, [
                { data: 'no', orderable: false },
                { data: 'c_name', className: 'font-weight-bold text-gray-800', render: function(data, type, row) {
                    return '<div>' + data + '</div><small class="text-muted">ID: ' + row.id + '</small>';
                }},
                { data: 'c_email' },
                { data: 'c_status', className: 'text-center', render: function(data) {
                    const status_class = (data === 'Active') ? 'success' : 'secondary';
                    return '<span class="badge badge-' + status_class + '">' + data + '</span>';
                }},
                { data: 'id', className: 'text-center', orderable: false, render: function(data) {
                    const baseUrl = "<?= base_url() ?>";
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                                <li><a class="dropdown-item" href="${baseUrl}merchant/sub-account/${data}"><i class="fas fa-users mr-2 text-success"></i>Sub Accounts</a></li>
                                <li><a class="dropdown-item" href="${baseUrl}finance/mutation/${data}"><i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations</a></li>
                            </ul>
                        </div>
                    `;
                }}
            ]);
        }
    });

    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', '');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
