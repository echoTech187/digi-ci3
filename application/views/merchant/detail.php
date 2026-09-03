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
            <p class="drawer-desc">Comprehensive 360-degree overview of the selected merchant's account details, financials, and OpenAPI credentials.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Balance Summary</div>
                <p class="drawer-card-text">Shows Total Balance, Hold Balance, and Available Balance.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-chart-line text-primary mr-2"></i> Audit Overview</div>
                <p class="drawer-card-text">Monitor transaction volume trends, conversion rates, and recent activity stream.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('merchant/manage') ?>" class="btn btn-sm btn-light border shadow-sm mr-3" title="Back to Merchant List">
                    <i class="fas fa-arrow-left text-dark"></i>
                </a>
                <div>
                    <h4 class="dt-page-title mb-1"><?= $merchant['c_name']; ?> <span class="text-muted small">#<?= $merchant['id']; ?></span></h4>
                    <p class="dt-page-subtitle mb-0">Comprehensive Merchant Dashboard & Transaction Analytics</p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
            <a href="<?= base_url('merchant/manage/edit/' . $merchant['id']); ?>" class="btn-dt-apply btn-dt-action-primary shadow-sm mr-2 action-confirm-link" data-title="Edit Merchant Profile?" data-text="You will be redirected to the merchant configuration page. Proceed?" data-confirm-btn="Yes, Edit Merchant" data-icon="info">
                <i class="fas fa-edit mr-2"></i> Edit Merchant
            </a>
            <div class="dropdown">
                <button class="btn btn-light border shadow-sm dropdown-toggle py-2 px-3 fw-bold" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v mr-2"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                    <li>
                        <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/balance/credit?merchant_id=' . $merchant['id']) ?>" data-title="Add Credit Balance?" data-text="Initiate a credit balance adjustment for <?= $merchant['c_name']; ?>. Proceed?" data-confirm-btn="Yes, Add Credit" data-icon="question">
                            <i class="fas fa-plus-circle text-success mr-2"></i> Add Credit Balance
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/balance/debit?merchant_id=' . $merchant['id']) ?>" data-title="Deduct Debit Balance?" data-text="Initiate a debit balance deduction for <?= $merchant['c_name']; ?>. Proceed?" data-confirm-btn="Yes, Deduct Balance" data-icon="warning">
                            <i class="fas fa-minus-circle text-danger mr-2"></i> Deduct Debit Balance
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/setting-cashin-fee/' . $merchant['id']) ?>" data-title="Cashin Fee Settings?" data-text="Configure inbound transaction fee structures. Proceed?" data-confirm-btn="Yes, Configure Fees" data-icon="info">
                            <i class="fas fa-cog text-info mr-2"></i> Cashin Fee Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item action-confirm-link" href="<?= base_url('merchant/setting-cashout-fee/' . $merchant['id']) ?>" data-title="Cashout Fee Settings?" data-text="Configure outbound disbursement fee structures. Proceed?" data-confirm-btn="Yes, Configure Fees" data-icon="info">
                            <i class="fas fa-cog text-warning mr-2"></i> Cashout Fee Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="row mb-4">
        <!-- Total Balance -->
        <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dt-card h-100 overflow-hidden" style="border-radius: 16px;">
                <div class="card p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase font-weight-bold text-muted small">Total Balance</span>
                        <div class="avatar-sm bg-primary-soft text-primary rounded-circle p-2" style="background-color: rgba(78, 115, 223, 0.1); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-1">Rp <?= number_format($merchant['c_balanceTotal'], 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0 mt-2"><i class="fas fa-shield-alt text-success mr-1"></i> Real-time ledger balance</p>
                </div>
            </div>
        </div>

        <!-- Hold Balance -->
        <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dt-card h-100 overflow-hidden" style="border-radius: 16px;">
                <div class="card p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase font-weight-bold text-muted small">Hold Balance</span>
                        <div class="avatar-sm bg-warning-soft text-warning rounded-circle p-2" style="background-color: rgba(255, 193, 7, 0.1); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-lock fa-lg"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold text-warning mb-1">Rp <?= number_format($merchant['c_balanceHold'], 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0 mt-2"><i class="fas fa-exclamation-circle text-warning mr-1"></i> Reserved for pending transactions</p>
                </div>
            </div>
        </div>

        <!-- Available Balance -->
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm dt-card h-100 overflow-hidden" style="border-radius: 16px;">
                <div class="card p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase font-weight-bold text-muted small">Available Balance</span>
                        <div class="avatar-sm bg-success-soft text-success rounded-circle p-2" style="background-color: rgba(40, 167, 69, 0.1); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                    <?php $available = $merchant['c_balanceTotal'] - $merchant['c_balanceHold']; ?>
                    <h3 class="font-weight-bold text-success mb-1">Rp <?= number_format($available, 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0 mt-2"><i class="fas fa-bolt text-success mr-1"></i> Instantly spendable funds</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Premium Navigation Tabs ── -->
    <div class="card border-0 shadow-sm dt-card mb-4 overflow-hidden" style="border-radius: 16px;">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs dt-nav-tabs border-0 px-4 pt-3" id="merchantDetailTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active font-weight-bold py-3 px-4 border-0" id="overview-tab" data-toggle="tab" href="#nav-overview" role="tab">
                        <i class="fas fa-chart-line mr-2" style="color: #b833ff;"></i> Audit Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link font-weight-bold py-3 px-4 border-0" id="profile-tab" data-toggle="tab" href="#nav-profile" role="tab">
                        <i class="fas fa-id-card mr-2 text-primary"></i> Merchant Information
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link font-weight-bold py-3 px-4 border-0" id="history-tab" data-toggle="tab" href="#nav-history" role="tab">
                        <i class="fas fa-shopping-cart mr-2 text-info"></i> Transaction History
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link font-weight-bold py-3 px-4 border-0" id="mutation-tab" data-toggle="tab" href="#nav-mutation" role="tab">
                        <i class="fas fa-exchange-alt mr-2 text-warning"></i> Mutation Log
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link font-weight-bold py-3 px-4 border-0" id="submerchant-tab" data-toggle="tab" href="#nav-submerchant" role="tab">
                        <i class="fas fa-users mr-2 text-success"></i> Sub Accounts
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="merchantDetailTabContent">
                <?php $this->load->view('merchant/partials/tab_overview'); ?>
                <?php $this->load->view('merchant/partials/tab_profile'); ?>
                <?php $this->load->view('merchant/partials/tab_tables'); ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.CURRENT_MERCHANT_ID = "<?= $merchant['id'] ?>";
</script>
<script src="<?= base_url('assets/js/merchant_detail.js'); ?>"></script>
