<!-- ── TAB 0: TRANSACTION OVERVIEW (PREMIUM INTERACTIVE AUDIT) ── -->
<div class="tab-pane fade show active p-4" id="nav-overview" role="tabpanel" aria-labelledby="overview-tab">
    
    <!-- Date Filter & Audit Actions -->
    <div class="card border-0 mb-4 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="text-dark font-weight-bold mr-2"><i class="fas fa-filter text-primary mr-1"></i> Audit Date Range:</span>
                <div class="premium-picker">
                    <input type="date" id="overview-start-date" class="form-control form-control-sm d-inline-block w-auto mr-2" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                    <span class="mr-2">to</span>
                    <input type="date" id="overview-end-date" class="form-control form-control-sm d-inline-block w-auto mr-2" value="<?= date('Y-m-d') ?>">
                </div>
                <button id="btn-overview-filter" class="btn btn-sm btn-primary font-weight-bold px-3">
                    <i class="fas fa-sync mr-1"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 glass-metric-card glass-metric-primary h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase small font-weight-bold">Total Transactions</span>
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <h3 id="stat-total-cnt" class="font-weight-bold mb-1 responsive-h3">0</h3>
                <p class="small mb-0">All payment channels</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 glass-metric-card glass-metric-success h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase small font-weight-bold">Successful Volume</span>
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <h3 id="stat-success-amt" class="font-weight-bold mb-1 responsive-h3">Rp 0</h3>
                <p class="small mb-0"><span id="stat-success-cnt">0</span> successful txs</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 glass-metric-card glass-metric-info h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase small font-weight-bold">Total Fees Charged</span>
                    <i class="fas fa-percent fa-lg"></i>
                </div>
                <h3 id="stat-total-fee" class="font-weight-bold mb-1 responsive-h3">Rp 0</h3>
                <p class="small mb-0">MDR & Admin fee costs</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 glass-metric-card glass-metric-warning h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase small font-weight-bold">Conversion Rate</span>
                    <i class="fas fa-percentage fa-lg"></i>
                </div>
                <h3 id="stat-success-rate" class="font-weight-bold mb-1 responsive-h3">0%</h3>
                <p class="small mb-0">Success vs Total ratio</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-area text-primary mr-1"></i> Transaction Volume Trend (SUCCESS)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="position: relative; height: 320px;">
                        <canvas id="overviewTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-pie text-info mr-1"></i> Payment Method Breakdown</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="chart-pie mb-4" style="position: relative; height: 220px;">
                        <canvas id="overviewBreakdownChart"></canvas>
                    </div>
                    <div id="breakdown-legend" class="text-center small mt-2"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-pie text-warning mr-1"></i> Transaction Status Distribution</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="chart-pie mb-3" style="position: relative; height: 220px;">
                        <canvas id="overviewStatusChart"></canvas>
                    </div>
                    <div id="status-legend" class="text-center small mt-2"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-history text-primary mr-1"></i> Recent Activity</h6>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-start">
                    <div id="recent-activity-list" class="recent-activity-timeline">
                        <!-- Rendered dynamically by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Chart & Merchant & Sub-Account Transactions -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-users text-primary mr-1"></i> Merchant & Sub-Account Transaction Overview</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 310px; overflow-y: auto;">
                        <table class="table mb-0" id="submerchantAuditTable">
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

    <!-- Audit Table Breakdown -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-table text-success mr-1"></i> Channel Audit Breakdown</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="channelAuditTable">
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
