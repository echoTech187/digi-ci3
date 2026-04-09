<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title" style="font-size: 1.75rem; font-weight: 800; color: #1a202c; letter-spacing: -0.025em;"><?= $title; ?></h4>
            <p class="dt-page-subtitle" style="color: #718096; font-size: 1rem;">Holistic overview of the ecosystem's real-time performance and financial health.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 btn-dt-chip-action" style="border-radius: 12px;">
                <span class="position-relative d-flex">
                    <span class="animate-ping position-absolute h-full w-full rounded-full bg-<?= ($maintenance_status == 'Active' ? 'success' : 'danger'); ?> opacity-75" style="width: 8px; height: 8px;"></span>
                    <span class="relative rounded-full bg-<?= ($maintenance_status == 'Active' ? 'success' : 'danger'); ?>" style="width: 8px; height: 8px;"></span>
                </span>
                <span class="font-weight-bold" style="color: #4a5568; letter-spacing: 0.5px;">System: <?= ($maintenance_status == 'Active' ? 'Online' : 'Maintenance'); ?></span>
            </div>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="dt-summary-row mb-4">
        <!-- Today's Volume -->
        <div class="dt-summary-card dt-summary-blue border-0 shadow-sm" style="border-left: 5px solid var(--primary) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">Total Volume (Today)</div>
                <div class="dt-summary-value text-primary-custom" style="font-size: 1.5rem;">Rp <?= number_format($today_stats['total_volume'], 0, ',', '.'); ?></div>
                <div class="dt-summary-sub">
                    <i class="fas fa-chart-line text-success mr-1"></i> <span class="font-weight-bold text-gray-800"><?= number_format($today_stats['total_qty'], 0, ',', '.'); ?></span> settled transactions
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <!-- QRIS Today -->
        <div class="dt-summary-card dt-summary-green border-0 shadow-sm" style="border-left: 5px solid var(--success) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">QRIS Performance</div>
                <div class="dt-summary-value text-success" style="font-size: 1.5rem;">Rp <?= number_format($today_stats['qris']['amount'], 0, ',', '.'); ?></div>
                <div class="dt-summary-sub">
                    <i class="fas fa-qrcode mr-1"></i> <span class="font-weight-bold text-gray-800"><?= number_format($today_stats['qris']['qty'], 0, ',', '.'); ?></span> scan successful
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-qrcode"></i>
            </div>
        </div>

        <!-- Disbursement Today -->
        <div class="dt-summary-card dt-summary-yellow border-0 shadow-sm" style="border-left: 5px solid var(--warning) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">BI-FAST Disburse</div>
                <div class="dt-summary-value text-warning" style="font-size: 1.5rem;">Rp <?= number_format($today_stats['disburse']['amount'], 0, ',', '.'); ?></div>
                <div class="dt-summary-sub">
                    <i class="fas fa-paper-plane mr-1"></i> <span class="font-weight-bold text-gray-800"><?= number_format($today_stats['disburse']['qty'], 0, ',', '.'); ?></span> processed
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-paper-plane"></i>
            </div>
        </div>

        <!-- Active Merchants -->
        <div class="dt-summary-card dt-summary-red border-0 shadow-sm" style="border-left: 5px solid var(--danger) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">Ecosystem Health</div>
                <div class="dt-summary-value text-danger" style="font-size: 1.5rem;"><?= number_format($merchant_count, 0, ',', '.'); ?> Partners</div>
                <div class="dt-summary-sub">
                    <i class="fas fa-shield-alt mr-1"></i> Active merchant accounts
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-red">
                <i class="fas fa-store"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Area Chart: Transaction Trends -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;">Transaction Trends</h6>
                        <p class="m-0 text-muted small">Yearly comparison across primary payment channels</p>
                    </div>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="mainAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart: Channel Mix -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h6 class="m-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;">Channel Mix (Today)</h6>
                    <p class="m-0 text-muted small">Distribution of transaction volume</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-pie pt-4 pb-2" style="height: 260px;">
                        <canvas id="channelDonutChart"></canvas>
                    </div>
                    <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--primary);"></span>
                            <span class="small font-weight-bold text-gray-600">QRIS</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></span>
                            <span class="small font-weight-bold text-gray-600">VA</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--info);"></span>
                            <span class="small font-weight-bold text-gray-600">E-Wallet</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--warning);"></span>
                            <span class="small font-weight-bold text-gray-600">Disburse</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Ecosystem Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm dt-card" style="border-radius: 20px;">
                <div class="dt-toolbar border-0 d-flex align-items-center justify-content-between" style="padding: 24px;">
                    <div class="d-flex align-items-center gap-3">
                        <h6 class="m-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;">Ecosystem Real-time Activity</h6>
                        <div class="d-flex align-items-center gap-2 bg-success-soft text-success px-3 py-1 rounded-pill">
                            <div style="width: 6px; height: 6px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px var(--success-soft);" class="pulse"></div>
                            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Live feed</span>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/report'); ?>" class="btn btn-light btn-sm font-weight-bold px-3 py-2" style="border-radius: 10px; font-size: 11px; color: var(--primary);">
                        VIEW FULL LOGS <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="recentActivityTable" class="table mb-0" style="width:100%">
                            <thead>
                                <tr style="background: #f9fafb;">
                                    <th class="border-0 px-4 py-3" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; width: 180px;">TIMESTAMP</th>
                                    <th class="border-0 px-4 py-3" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">PARTNER NAME</th>
                                    <th class="border-0 px-4 py-3" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">TRANS. TYPE</th>
                                    <th class="border-0 px-4 py-3 text-right" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">AMOUNT</th>
                                    <th class="border-0 px-4 py-3 text-center" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loaded via Server-side DataTables -->
                            </tbody>
                        </table>
                    </div>
                    <div class="py-4 text-center">
                        <p class="m-0 text-muted small">Updated every 30 seconds &bull; Real-time ecosystem integrity monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Styles */
.pulse {
    animation: pulse-animation 2s infinite;
}
@keyframes pulse-animation {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(28, 200, 138, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(28, 200, 138, 0); }
}

#recentActivityTable tbody tr {
    transition: background 0.2s;
}
#recentActivityTable tbody tr:hover {
    background-color: #f8fafc !important;
}
#recentActivityTable td {
    padding: 16px 24px;
    font-size: 14px;
    color: #4a5568;
    border-bottom: 1px solid #f1f5f9;
}
</style>

<script src="<?= base_url('assets/'); ?>vendor/chart.js/Chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Enhanced DataTable Init
        initServerDataTable("#recentActivityTable", "<?= base_url('admin/recent_mutations_json') ?>", [
            {data: 'date'},
            {data: 'merchant'},
            {data: 'type'},
            {data: 'amount', className:"text-right font-weight-bold text-gray-800"},
            {data: 'status', className: 'text-center'}
        ], {
            "pageLength": 10,
            "lengthChange": false,
            "searching": false,
            "info": false,
            "ordering": false
        });
    });

    // Global Chart.js Overrides for Premium Finish
    Chart.defaults.global.defaultFontFamily = "'Inter', -apple-system, system-ui, sans-serif";
    Chart.defaults.global.defaultFontColor = '#94a3b8';
    Chart.defaults.global.defaultFontSize = 11;

    function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Area Chart: Polished for FinTech Aesthetic
    var ctxArea = document.getElementById("mainAreaChart");
    var mainAreaChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: "QRIS",
                    borderColor: "#664EFF", // --primary
                    backgroundColor: "rgba(102, 78, 255, 0.08)",
                    data: <?= json_encode($monthly_overview['qris']); ?>,
                    lineTension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 2,
                    borderWidth: 3
                },
                {
                    label: "Disburse",
                    borderColor: "#f6c23e", // --warning
                    backgroundColor: "rgba(246, 194, 62, 0.08)",
                    data: <?= json_encode($monthly_overview['disburse']); ?>,
                    lineTension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 2,
                    borderWidth: 3
                }
            ],
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    gridLines: { color: "#f1f5f9", zeroLineColor: "#f1f5f9", drawBorder: false },
                    ticks: {
                        callback: function(value) { return 'Rp' + number_format(value); },
                        maxTicksLimit: 7,
                        padding: 15
                    }
                }],
                xAxes: [{
                    gridLines: { display: false },
                    ticks: { padding: 15 }
                }]
            },
            tooltips: {
                backgroundColor: "rgba(255, 255, 255, 0.95)",
                titleFontColor: "#1e293b",
                bodyFontColor: "#64748b",
                borderColor: "#e2e8f0",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: true,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        return data.datasets[tooltipItem.datasetIndex].label + ': Rp ' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    // Donut Chart: Ultra-Modern Clean Look
    var ctxDonut = document.getElementById("channelDonutChart");
    var channelDonutChart = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ["QRIS", "VA", "E-Wallet", "Disburse"],
            datasets: [{
                data: [
                    <?= $today_stats['qris']['qty']; ?>,
                    <?= $today_stats['va']['qty']; ?>,
                    <?= $today_stats['ewallet']['qty']; ?>,
                    <?= $today_stats['disburse']['qty']; ?>
                ],
                backgroundColor: ['#664EFF', '#1cc88a', '#36b9cc', '#f6c23e'],
                hoverBackgroundColor: ['#553ce6', '#17a673', '#2c9faf', '#dda20a'],
                hoverBorderColor: "rgba(255, 255, 255, 1)",
                borderWidth: 5,
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 82,
            legend: { display: false },
            tooltips: {
                backgroundColor: "rgba(255, 255, 255, 0.95)",
                bodyFontColor: "#1e293b",
                borderColor: "#e2e8f0",
                borderWidth: 1,
                xPadding: 12,
                yPadding: 12,
                displayColors: false,
                caretPadding: 10,
            },
        },
    });
</script>