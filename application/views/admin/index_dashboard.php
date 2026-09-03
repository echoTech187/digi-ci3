<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header mb-4">
        <div>
            <div class="d-flex align-items-center mb-1">
                <h4 class="dt-page-title mb-0 mr-3"><?= $title; ?> Analytics</h4>
                <div class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                    <i class="fas fa-shield-check mr-1"></i> LOGICAL INTEGRITY VERIFIED
                </div>
            </div>
            <p class="dt-page-subtitle text-muted">Holistic overview of the ecosystem's real-time performance and financial health.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="text-right mr-3 d-none d-md-block">
                <small class="text-muted d-block font-weight-bold" style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">Cloud Sync Active</small>
                <small class="text-primary font-weight-bold" style="font-size: 11px;">Last updated: <span id="stat_last_synced">Loading...</span></small>
            </div>
            <div class="d-flex align-items-center gap-2 px-3 py-2 btn-dt-chip-action" style="border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid var(--border-color); cursor: default;">
                <span class="position-relative d-flex">
                    <span id="maintenance_ping" class="position-absolute h-full w-full rounded-full bg-secondary opacity-75" style="width: 8px; height: 8px;"></span>
                    <span id="maintenance_dot" class="relative rounded-full bg-secondary" style="width: 8px; height: 8px;"></span>
                </span>
                <span class="font-weight-bold" style="color: var(--gray-700); letter-spacing: 0.5px; font-size: 12px;">System: <span id="maintenance_label"><span class="skeleton-box" style="width: 60px;"></span></span></span>
            </div>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn">
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer (Partial) ── -->
    <?php $this->load->view('admin/partials/dashboard_drawer'); ?>

    <!-- ── KPI Summary Grid (Glassmorphism Transformation) ── -->
    <div class="row mb-4 gr-3">
        <!-- Today's Volume -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; border-radius: 20px; overflow: hidden; position: relative; z-index: 1; transform: translateZ(0);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="dash-kpi-icon-wrap" style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                        <div class="text-right">
                             <span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.2); font-size: 10px;">TODAY</span>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Net Volume</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap; gap: 10px;"><span>Rp</span><span id="stat_total_volume"><span class="skeleton-box" style="width: 120px;"></span></span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-arrow-up mr-1" style="font-size: 8px;"></i> <span id="stat_total_qty"><span class="skeleton-box" style="width: 30px;"></span></span>&nbsp;Settled 
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>

        <!-- QRIS Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 20px; overflow: hidden; position: relative; z-index: 1; transform: translateZ(0);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="dash-kpi-icon-wrap" style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-qrcode fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">QRIS Performance</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap; gap: 10px;"><span>Rp</span><span id="stat_qris_amount"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-check-circle mr-1" style="font-size: 8px;"></i> <span id="stat_qris_qty"><span class="skeleton-box" style="width: 30px;"></span></span>&nbsp;Successful
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
        </div>

        <!-- Disbursement Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 20px; overflow: hidden; position: relative; z-index: 1; transform: translateZ(0);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="dash-kpi-icon-wrap" style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-paper-plane fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Disbursement Out</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap; gap: 10px;"><span>Rp</span><span id="stat_disburse_amount"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-paper-plane mr-1" style="font-size: 8px;"></i> <span id="stat_disburse_qty"><span class="skeleton-box" style="width: 30px;"></span></span>&nbsp;Transferred
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>

        <!-- Total Merchant Partner Count -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border-radius: 20px; overflow: hidden; position: relative; z-index: 1; transform: translateZ(0);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="dash-kpi-icon-wrap" style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users-crown fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Registered Merchants</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap;"><span id="stat_merchant_count"><span class="skeleton-box" style="width: 60px;"></span></span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-shield mr-1" style="font-size: 8px;"></i> Partner Ecosystem
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-users-crown"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts Section ── -->
    <div class="row">
        <!-- Area Chart (Interactive Monthly Ecosystem Movement) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm dt-card mb-4" style="border-radius: 20px;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-transparent border-0 px-4 pt-4">
                    <div>
                        <h6 class="m-0 font-weight-bold text-dark" style="font-size: 16px;">Financial Flow (Yearly Snapshot)</h6>
                        <small class="text-muted">Direct comparison between QRIS collections & outbound disbursements</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="d-flex align-items-center font-weight-bold text-muted small"><span style="width: 10px; height: 10px; border-radius: 3px; background: #6366f1; display: inline-block; margin-right: 6px;"></span> QRIS</span>
                        <span class="d-flex align-items-center font-weight-bold text-muted small"><span style="width: 10px; height: 10px; border-radius: 3px; background: #f59e0b; display: inline-block; margin-right: 6px;"></span> Disburse</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-area" style="height: 330px;">
                        <canvas id="mainAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart (Channel Ratio Allocation) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm dt-card mb-4" style="border-radius: 20px;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-transparent border-0 px-4 pt-4">
                    <div>
                        <h6 class="m-0 font-weight-bold text-dark" style="font-size: 16px;">Channel Distribution</h6>
                        <small class="text-muted">Today's transaction channel breakdown</small>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-pie pt-2 pb-2" style="height: 250px; position: relative;">
                        <canvas id="channelDonutChart"></canvas>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <span class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Today</span>
                            <h4 class="font-weight-bold text-dark mb-0" id="stat_total_qty_donut">--</h4>
                            <small class="text-muted" style="font-size: 10px;">Transactions</small>
                        </div>
                    </div>
                    <div class="mt-4 text-center small text-muted d-flex justify-content-around">
                        <span><i class="fas fa-circle mr-1" style="color: #6366f1;"></i> QRIS</span>
                        <span><i class="fas fa-circle mr-1" style="color: #10b981;"></i> VA</span>
                        <span><i class="fas fa-circle mr-1" style="color: #3b82f6;"></i> E-Wallet</span>
                        <span><i class="fas fa-circle mr-1" style="color: #f59e0b;"></i> Disburse</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Include Dashboard Scripts (Asset) ── -->
<script>
    window.dashboardMonthlyLabels = <?= json_encode($monthly_overview['labels']); ?>;
</script>
<script src="<?= base_url('assets/js/dashboard.js'); ?>"></script>
