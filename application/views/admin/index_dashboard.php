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
            </div>
    </div>

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
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">BI-FAST Disbursement</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap; gap: 10px;"><span>Rp</span><span id="stat_disburse_amount"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-bolt mr-1" style="font-size: 8px;"></i> <span id="stat_disburse_qty"><span class="skeleton-box" style="width: 30px;"></span></span>&nbsp;Processed
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>

        <!-- Active Merchants -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 20px; overflow: hidden; position: relative; z-index: 1; transform: translateZ(0);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="dash-kpi-icon-wrap" style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-store fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Ecosystem Partners</h6>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; letter-spacing: -0.5px; white-space: nowrap; gap: 10px;"><span id="stat_merchant_count"><span class="skeleton-box" style="width: 80px;"></span></span> <span>Accounts</span></h3>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="px-2 py-1 rounded-pill d-flex align-items-center" style="background: rgba(255,255,255,0.15); font-size: 10px;">
                            <i class="fas fa-user-check mr-1" style="font-size: 8px;"></i> All Active
                        </div>
                    </div>
                </div>
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; font-size: 100px;">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts Section ── -->
    <div class="row mb-5 d-flex align-items-stretch">
        <!-- Area Chart: Transaction Trends -->
        <div class="col-12 col-xl-8 mb-4 d-flex flex-column">
            <div class="card border-0 shadow-sm dt-card h-100 d-flex flex-column" style="border-radius: 24px; background: white; min-height: 520px;">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-gray-900" style="font-size: 1rem;">Transaction Trends</h6>
                        <p class="m-0 text-muted small mt-1">Yearly volume distribution across core payment channels</p>
                    </div>
                    <div class="d-flex gap-2 pr-2">
                        <div class="d-flex align-items-center small font-weight-bold mr-3" style="color: #6366f1;">
                            <span class="mr-1" style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1; display: inline-block;"></span> QRIS
                        </div>
                        <div class="d-flex align-items-center small font-weight-bold" style="color: #f59e0b;">
                            <span class="mr-1" style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span> Disburse
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 flex-grow-1">
                    <div class="chart-area" style="height: 380px;">
                        <canvas id="mainAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart: Channel Mix -->
        <div class="col-12 col-xl-4 mb-4 d-flex flex-column">
            <div class="card border-0 shadow-sm dt-card h-100 d-flex flex-column" style="border-radius: 24px; background: white; min-height: 520px;">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0">
                    <h6 class="font-weight-bold text-gray-900" style="font-size: 1rem;">Channel Mix (Today)</h6>
                    <p class="m-0 text-muted small mt-1">Share of successful transaction qty</p>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column justify-content-center flex-grow-1">
                    <div class="chart-pie mb-4" style="height: 280px; position: relative;">
                        <canvas id="channelDonutChart"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <span class="d-block text-muted" style="font-size: 10px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">Total Qty</span>
                            <span class="h4 font-weight-bolder text-dark mb-0" id="stat_total_qty_donut">...</span>
                        </div>
                    </div>
                    <div class="grid d-flex flex-wrap justify-content-center gap-3">
                        <div class="small p-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="background: var(--gray-100); border-color: var(--border-color) !important;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1;"></span>
                            <span class="font-weight-bold text-gray-600">QRIS</span>
                        </div>
                        <div class="small p-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="background: var(--gray-100); border-color: var(--border-color) !important;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                            <span class="font-weight-bold text-gray-600">VA</span>
                        </div>
                        <div class="small p-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="background: var(--gray-100); border-color: var(--border-color) !important;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span>
                            <span class="font-weight-bold text-gray-600">E-Wallet</span>
                        </div>
                        <div class="small p-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="background: var(--gray-100); border-color: var(--border-color) !important;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                            <span class="font-weight-bold text-gray-600">Disburse</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recent Ecosystem Activity ── -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm dt-card" style="border-radius: 24px; overflow: hidden; background: white;">
                <div class="dt-toolbar border-0 d-flex align-items-center justify-content-between" style="padding: 28px;">
                    <div class="d-flex align-items-center gap-3">
                        <h6 class="font-weight-bold text-gray-900" style="font-size: 1.1rem;">Ecosystem Real-time Activity</h6>
                        <div class="d-flex align-items-center gap-2 bg-success-soft text-success px-3 py-1 rounded-pill">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #059669; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);" class="pulse"></div>
                            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">LIVE FEED</span>
                        </div>
                    </div>
                    <a href="<?= base_url('report/download'); ?>" class="btn btn-primary btn-sm font-weight-bold px-4 py-2" style="border-radius: 12px; font-size: 11px;">
                        EXPLORE RECORDS <i class="fas fa-external-link-alt ml-2" style="font-size: 10px;"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="recentActivityTable" class="table mb-0" style="width:100%">
                            <thead>
                                <tr style="background: var(--bg-body);">
                                    <th class="border-0 px-4 py-4 text-muted" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; width: 220px;">TIMESTAMP</th>
                                    <th class="border-0 px-4 py-4 text-muted" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">PARTNER NAME</th>
                                    <th class="border-0 px-4 py-4 text-muted" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">CHANNEL</th>
                                    <th class="border-0 px-4 py-4 text-muted text-right" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">AMOUNT</th>
                                    <th class="border-0 px-4 py-4 text-muted text-center" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loaded via Server-side DataTables -->
                            </tbody>
                        </table>
                    </div>
                    <div class="py-5 text-center border-top" style="border-color: var(--border-color) !important;">
                        <p class="m-0 text-muted small"><i class="fas fa-sync-alt fa-spin mr-2"></i> Auto-refresh active every 30s &bull; Guaranteed data integrity from 4 core payment clusters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('assets/'); ?>vendor/chart.js/Chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        </script>
