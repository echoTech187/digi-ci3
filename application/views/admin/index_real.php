<!-- Begin Page Content -->
<div>

    <!-- ── Strategic Header ── -->
    <div class="dt-page-header mb-4">
        <div>
            <div class="d-flex align-items-center mb-1">
                <h4 class="dt-page-title mb-0 mr-3">Strategic Analytics & Insights</h4>
                <div class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                    <i class="fas fa-brain mr-1"></i> BI-ENGINE ACTIVE
                </div>
            </div>
            <p class="dt-page-subtitle text-muted">
                Data Coverage: <span class="badge bg-white shadow-sm text-dark px-3 py-1 font-weight-bold" style="border-radius: 8px; font-size: 11px;"><?= $date_range_label; ?></span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Period Selector -->
            <div class="d-flex p-1 gap-1" style="border-radius: 14px;">
                <a href="<?= base_url('dashboard/analytics?period=yesterday'); ?>" class="btn btn-sm <?= ($current_period == 'yesterday' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Yesterday</a>
                <a href="<?= base_url('dashboard/analytics?period=last_7_days'); ?>" class="btn btn-sm <?= ($current_period == 'last_7_days' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Last 7 Days</a>
                <a href="<?= base_url('dashboard/analytics?period=last_month'); ?>" class="btn btn-sm <?= ($current_period == 'last_month' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Last Month</a>
            </div>
            <button id="toggleGuideBtn" class="btn btn-sm btn-outline-primary px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px;">
                <i class="fas fa-info-circle mr-1"></i> Instructions Guide
            </button>
        </div>
    </div>

    <?php 
    // Growth Calculation Helper
    if (!function_exists('get_growth')) {
        function get_growth($current, $prev) {
            if ($prev <= 0) return ($current > 0) ? 100 : 0;
            return round((($current - $prev) / $prev) * 100, 1);
        }
    }
    
    if (!function_exists('render_growth_badge')) {
        function render_growth_badge($current, $prev) {
            $growth = get_growth($current, $prev);
            $icon = ($growth >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';
            $class = ($growth >= 0) ? 'emerald' : 'rose';
            $prefix = ($growth >= 0) ? '+' : '';
            
            return '<span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.2); font-size: 10px; color: white;">
                        <i class="fas '.$icon.' text-white mr-1" style="opacity:0.8;"></i> '.$prefix.$growth.'%
                    </span>';
        }
    }
    ?>

    <!-- ── Growth KPI Cards (Glassmorphism Transform) ── -->
    <div class="row mb-5">
        
        <!-- QRIS Growth -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dash-kpi-card h-100 w-100" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>QRIS GROWTH</span>
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; white-space: nowrap; gap: 8px;"><span style="margin-right: 2px;">Rp</span><span id="stat_qris_current"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2" id="qris_growth_container">
                        <span class="skeleton-box" style="width: 60px;"></span>
                        <span class="small" style="opacity: 0.8; white-space: nowrap;">vs&nbsp;<?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profitability Metric -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dash-kpi-card h-100 w-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>PLATFORM YIELD</span>
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; white-space: nowrap; gap: 8px;"><span style="margin-right: 2px;">Rp</span><span id="stat_profit_current"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2" id="profit_growth_container">
                        <span class="skeleton-box" style="width: 60px;"></span>
                        <span class="small" style="opacity: 0.8; white-space: nowrap;">net vs&nbsp;<?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disburse Efficiency -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dash-kpi-card h-100 w-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>DISBURSE VOL.</span>
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline" style="font-size: 1.4rem; white-space: nowrap; gap: 12px;"><span style="margin-right: 2px;">Rp</span><span id="stat_disburse_current"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2" id="disburse_growth_container">
                        <span class="skeleton-box" style="width: 60px;"></span>
                        <span class="small" style="opacity: 0.8; white-space: nowrap;">vs&nbsp;<?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dash-kpi-card h-100 w-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>CHANNEL STABILITY</span>
                        <i class="fas fa-vial"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3" style="font-size: 1.2rem;"><span id="stat_success_rate"><span class="skeleton-box" style="width: 60px;"></span></span>%</h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <span id="success_rate_badge"><span class="skeleton-box" style="width: 80px;"></span></span>
                        <span class="small" style="opacity: 0.8; white-space: nowrap;">integrity score</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── System Health & DLQ Monitoring ── -->
    <div class="d-flex align-items-center mb-3 mt-5">
        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
            <i class="fas fa-shield-alt text-primary mr-2"></i> System Health & Operations
        </h6>
        <div class="ml-4 flex-grow-1" style="height: 1px; background: #e5e7eb;"></div>
        <a href="<?= base_url('notifications') ?>" class="btn btn-sm btn-light shadow-sm ml-4 font-weight-bold" style="border-radius: 8px; font-size: 11px;">
            Manage DLQ <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <div class="row mb-5">
        <!-- Unresolved DLQ -->
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dt-card h-100 w-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-muted small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>UNRESOLVED DLQ</span>
                        <div class="dash-kpi-icon-wrap" style="background: rgba(239,68,68,0.1); border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline text-gray-900" style="font-size: 1.6rem; white-space: nowrap; gap: 8px;"><span id="stat_dlq_unresolved"><span class="skeleton-box" style="width: 80px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(239,68,68,0.1); font-size: 10px; color: #ef4444;">
                            <i class="fas fa-calendar-day mr-1"></i> <span id="stat_dlq_today">...</span> Today
                        </span>
                        <span class="small text-muted" style="white-space: nowrap;">Errors captured</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Outage Merchant -->
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dt-card h-100 w-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-muted small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>TOP OUTAGE SOURCE</span>
                        <div class="dash-kpi-icon-wrap" style="background: rgba(245,158,11,0.1); border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-server" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-3 text-gray-900" style="font-size: 1.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><span id="stat_dlq_merchant"><span class="skeleton-box" style="width: 150px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(245,158,11,0.1); font-size: 10px; color: #f59e0b;">
                            Highest Contributor
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Error Occurred -->
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm dt-card h-100 w-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-muted small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>LATEST INCIDENT</span>
                        <div class="dash-kpi-icon-wrap" style="background: rgba(59,130,246,0.1); border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="color: #3b82f6;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-3 d-flex align-items-baseline text-gray-900" style="font-size: 1.3rem; white-space: nowrap; gap: 8px;"><span id="stat_dlq_time"><span class="skeleton-box" style="width: 100px;"></span></span></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(59,130,246,0.1); font-size: 10px; color: #3b82f6;">
                            Timestamp Logged
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Analysis Depth Sections ── -->
    <div class="row mb-5 d-flex align-items-stretch">

        <!-- Area Chart -->
        <div class="col-12 col-xl-8 d-flex flex-column">
            <div class="card border-0 shadow-sm dt-card h-100 d-flex flex-column" style="border-radius: 24px; min-height: 520px;">
                <div class="card-header border-0 bg-transparent py-4 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-gray-900" style="font-size: 1rem;">Yield Analysis Trend</h6>
                        <p class="m-0 text-muted small mt-1">Growth progression in total platform volume over selected period</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 flex-grow-1">
                    <div class="chart-area" style="height: 380px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-12 col-xl-4 d-flex flex-column">
            <div class="card border-0 shadow-sm dt-card h-100 d-flex flex-column" style="border-radius: 24px; min-height: 520px;">
                <div class="card-header border-0 bg-transparent py-4 px-4">
                    <h6 class="font-weight-bold text-gray-900" style="font-size: 1rem;">Net Contribution Mix</h6>
                    <p class="m-0 text-muted small mt-1">Direct profit distribution (Fee - Cost)</p>
                </div>
                <div class="card-body px-4 pb-4 flex-grow-1">
                    <div class="chart-pie pt-4 mb-4" style="height: 280px; position: relative;">
                        <canvas id="channelPieChart"></canvas>
                        <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <span class="d-block text-muted" style="font-size: 10px; text-transform: uppercase; font-weight: 800;">Yield</span>
                            <span id="stat_profit_short" class="h4 font-weight-bolder text-dark mb-0"><span class="skeleton-box" style="width: 40px;"></span></span>
                        </div>
                    </div>
                    <div class="mt-auto d-flex flex-wrap justify-content-center gap-3">
                        <div class="d-flex align-items-center gap-2 small font-weight-bold" style="color: #6366f1;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1;"></span> QRIS
                        </div>
                        <div class="d-flex align-items-center gap-2 small font-weight-bold" style="color: #10b981;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Disburse
                        </div>
                        <div class="d-flex align-items-center gap-2 small font-weight-bold" style="color: #3b82f6;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span> VA
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Strategic Recommendations Card (Modern Makeover) ── -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg text-white" style="border-radius: 28px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); overflow: hidden; position: relative;">
                <div class="card-body p-5 position-relative" style="z-index: 2;">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="badge bg-primary px-3 py-2 mb-4 rounded-pill font-weight-bold" style="font-size: 10px; letter-spacing: 1px;">INTELLIGENCE REPORT</div>
                            <h2 class="font-weight-bold mb-3" style="letter-spacing: -1px;">Strategic Growth Directives</h2>
                            <p class="mb-5 text-white-50" style="line-height: 1.8; font-size: 15px; max-width: 600px;">Our BI engine has analyzed the data for the selected period. These actionable insights are designed to maximize ecosystem efficiency and partner profitability.</p>
                            
                            <div class="row g-4">
                                <div class="col-12 col-xl-6 mb-4">
                                    <div class="p-4 h-100 d-flex flex-column" style="background: rgba(255,255,255,0.05); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary rounded-lg d-flex align-items-center justify-content-center mr-3" style="width: 38px; height: 38px; min-width: 38px;">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                            <h6 class="font-weight-bold m-0">Optimize Peak Performance</h6>
                                        </div>
                                        <p class="small m-0 text-white-50" style="line-height: 1.6;">Volume peaks between 7PM - 10PM. Schedule intense background operations after 1AM to ensure zero-latency periods during high traffic.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6 mb-4">
                                    <div class="p-4 h-100 d-flex flex-column" style="background: rgba(255,255,255,0.05); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-emerald rounded-lg d-flex align-items-center justify-content-center mr-3" style="width: 38px; height: 38px; min-width: 38px; background: #10b981;">
                                                <i class="fas fa-rocket"></i>
                                            </div>
                                            <h6 class="font-weight-bold m-0">Expansion Opportunity</h6>
                                        </div>
                                        <p class="small m-0 text-white-50" style="line-height: 1.6;">QRIS yield accounts for <span id="stat_qris_contribution"><span class="skeleton-box" style="width: 30px;"></span></span>% of net income. Accelerate onboarding for street-level retail partners.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-center pr-5">
                            <div class="pulse-ring d-inline-block">
                                <img src="https://cdn-icons-png.flaticon.com/512/2822/2822709.png" style="width: 220px; filter: brightness(0) invert(1) opacity(0.15);" alt="BI Icon">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Glass accents -->
                <div style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: #6366f1; border-radius: 50%; filter: blur(120px); opacity: 0.15;"></div>
                <div style="position: absolute; bottom: -50px; left: -50px; width: 250px; height: 250px; background: #10b981; border-radius: 50%; filter: blur(120px); opacity: 0.15;"></div>
            </div>
        </div>
    </div>

    <!-- Instructions Guide Drawer -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h5 class="drawer-title">
                <i class="fas fa-info-circle mr-2"></i> Strategic Analytics Guide
            </h5>
            <button class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">
                This dashboard displays platform-wide revenue, profit growth indicators, channel contributions, and BI insights.
            </p>
            <div class="drawer-card">
                <div class="drawer-card-title">
                    <i class="fas fa-chart-line text-indigo mr-2"></i> Revenue & Growth
                </div>
                <p class="drawer-card-text">
                    KPI blocks compute current period volume against the corresponding prior period (e.g. yesterday vs same day last week).
                </p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title">
                    <i class="fas fa-hand-holding-usd text-success mr-2"></i> Platform Yield
                </div>
                <p class="drawer-card-text">
                    Net yield represents total transactions fees gathered minus underlying external provider cost fees.
                </p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title">
                    <i class="fas fa-brain text-warning mr-2"></i> Growth Directives
                </div>
                <p class="drawer-card-text">
                    BI insights highlight network-load-free windows and volume distribution to maximize merchant settlement rates.
                </p>
            </div>
        </div>
    </div>

</div>


<script src="<?= base_url('assets/'); ?>vendor/chart.js/Chart.min.js"></script>
<script>
    // Global Configuration for Charts
    Chart.defaults.global.defaultFontFamily = "'Inter', -apple-system, system-ui, sans-serif";
    Chart.defaults.global.defaultFontColor = '#64748b';
    Chart.defaults.global.defaultFontSize = 11;

    function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function get_growth(current, prev) {
        if (prev <= 0) return (current > 0) ? 100 : 0;
        return ((current - prev) / prev * 100).toFixed(1);
    }

    function render_growth_badge(current, prev) {
        var growth = get_growth(current, prev);
        var icon = (growth >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';
        var prefix = (growth >= 0) ? '+' : '';
        
        return '<span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.2); font-size: 10px; color: white;">' +
               '<i class="fas ' + icon + ' text-white mr-1" style="opacity:0.8;"></i> ' + prefix + growth + '%' +
               '</span>';
    }

    // Profit Contribution Mix (Enhanced Donut)
    var ctxDonut = document.getElementById("channelPieChart");
    var channelPieChart = new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ["QRIS", "Disburse", "VA"],
        datasets: [{
          data: [0, 0, 0],
          backgroundColor: ['#6366f1', '#10b981', '#3b82f6'],
          hoverBackgroundColor: ['#4f46e5', '#059669', '#2563eb'],
          hoverBorderColor: "#fff",
          borderWidth: 6
        }],
      },
      options: {
        maintainAspectRatio: false,
        cutoutPercentage: 84,
        legend: { display: false },
        tooltips: {
          backgroundColor: "rgba(255, 255, 255, 1)",
          titleFontColor: "#1e293b",
          bodyFontColor: "#475569",
          borderColor: "#e2e8f0",
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, data) {
                var value = data.datasets[0].data[tooltipItem.index];
                return ' ' + data.labels[tooltipItem.index] + ' Profit: Rp ' + number_format(value);
            }
          }
        }
      },
    });

    // Area Chart: Yield Trends
    var ctxArea = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctxArea, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          label: "Volume",
          lineTension: 0.4,
          backgroundColor: "rgba(99, 102, 241, 0.05)",
          borderColor: "#6366f1",
          borderWidth: 3.5,
          pointRadius: 4,
          pointBackgroundColor: "#fff",
          pointBorderColor: "#6366f1",
          pointBorderWidth: 2,
          pointHoverRadius: 6,
          pointHoverBackgroundColor: "#6366f1",
          pointHoverBorderColor: "#fff",
          pointHitRadius: 50,
          data: [],
        }],
      },
      options: {
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
          yAxes: [{
            gridLines: { color: "rgba(241, 245, 249, 1)", zeroLineColor: "rgba(241, 245, 249, 1)", drawBorder: false },
            ticks: {
              callback: function(value) { 
                  if (value >= 1e12) return 'Rp ' + (value / 1e12).toFixed(1) + ' T';
                  if (value >= 1e9) return 'Rp ' + (value / 1e9).toFixed(1) + ' B';
                  if (value >= 1e6) return 'Rp ' + (value / 1e6).toFixed(1) + ' M';
                  if (value >= 1e3) return 'Rp ' + (value / 1e3).toFixed(1) + ' K';
                  return 'Rp ' + number_format(value); 
              },
              maxTicksLimit: 7,
              padding: 15,
              fontStyle: '600'
            }
          }],
          xAxes: [{
            gridLines: { display: false },
            ticks: { padding: 15, fontStyle: '600' }
          }]
        },
        tooltips: {
          backgroundColor: "rgba(255, 255, 255, 1)",
          titleFontColor: "#1e293b",
          titleFontSize: 14,
          bodyFontColor: "#475569",
          bodyFontSize: 13,
          borderColor: "#e2e8f0",
          borderWidth: 1,
          xPadding: 15,
          yPadding: 20,
          displayColors: true,
          mode: 'index',
          intersect: false,
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              return ' Volume Feed: Rp ' + number_format(tooltipItem.yLabel);
            }
          }
        }
      }
    });

    function loadAnalyticsData() {
        $.ajax({
            url: "<?= base_url('dashboard/analytics-data/json?period=' . $current_period); ?>",
            type: "GET",
            dataType: "json",
            success: function(resp) {
                var c = resp.current_stats;
                var p = resp.prev_stats;
                
                // Update KPI Cards
                $("#stat_qris_current").text(number_format(Math.round(c.qris.amount)));
                $("#qris_growth_container").html(render_growth_badge(c.qris.amount, p.qris.amount) + 
                    '<span class="small" style="opacity: 0.8; white-space: nowrap;">vs&nbsp;' + resp.comparison_label + '</span>');
                
                $("#stat_disburse_current").text(number_format(Math.round(c.disburse.amount)));
                $("#disburse_growth_container").html(render_growth_badge(c.disburse.amount, p.disburse.amount) + 
                    '<span class="small" style="opacity: 0.8; white-space: nowrap;">vs&nbsp;' + resp.comparison_label + '</span>');
                
                var profit_c = (c.qris.fee - c.qris.fee_external) + (c.disburse.fee - c.disburse.fee_external) + (c.va.fee - c.va.fee_external);
                var profit_p = (p.qris.fee - p.qris.fee_external) + (p.disburse.fee - p.disburse.fee_external) + (p.va.fee - p.va.fee_external);
                
                $("#stat_profit_current").text(number_format(Math.round(profit_c)));
                $("#profit_growth_container").html(render_growth_badge(profit_c, profit_p) + 
                    '<span class="small" style="opacity: 0.8; white-space: nowrap;">net vs&nbsp;' + resp.comparison_label + '</span>');
                
                $("#stat_profit_short").text('Rp' + (profit_c / 1000000).toFixed(1) + 'M');

                $("#stat_success_rate").text(resp.success_rate);
                var sr_icon = (resp.success_rate >= 95 ? 'fa-check' : 'fa-exclamation-triangle');
                var sr_text = (resp.success_rate >= 95 ? 'HIGH' : 'NORMAL');
                $("#success_rate_badge").html('<span class="badge rounded-pill px-3 py-1" style="background: rgba(255,255,255,0.2); font-size: 10px; color: white;">' +
                    '<i class="fas ' + sr_icon + ' mr-1"></i> ' + sr_text + '</span>');

                var qris_contribution = profit_c > 0 ? (c.qris.amount / profit_c * 100).toFixed(1) : 0;
                $("#stat_qris_contribution").text(qris_contribution);

                // Update Donut Chart
                channelPieChart.data.datasets[0].data = [
                    (c.qris.fee - c.qris.fee_external),
                    (c.disburse.fee - c.disburse.fee_external),
                    (c.va.fee - c.va.fee_external)
                ];
                channelPieChart.update();

                // Update Area Chart
                myLineChart.data.labels = resp.chart_data.labels;
                myLineChart.data.datasets[0].data = resp.chart_data.values;
                myLineChart.data.datasets[0].label = (resp.comparison_label === 'prev. day' ? 'Hourly' : 'Daily') + " Volume";
                myLineChart.update();
            }
        });
    }

    function loadDlqHealth() {
        $.ajax({
            url: "<?= base_url('dashboard/dlq-health/json'); ?>",
            type: "GET",
            dataType: "json",
            success: function(resp) {
                var unresolved = parseInt(resp.total_unresolved);
                $("#stat_dlq_unresolved").text(number_format(unresolved));
                $("#stat_dlq_today").text(number_format(resp.today_errors));
                $("#stat_dlq_merchant").text(resp.top_merchant);
                $("#stat_dlq_time").text(resp.last_error_time);
                
                if (unresolved == 0) {
                    $("#stat_dlq_unresolved").closest('.dash-kpi-card').css('background', 'linear-gradient(135deg, #059669 0%, #047857 100%)');
                    $("#stat_dlq_unresolved").closest('.dash-kpi-card').find('i.fa-exclamation-triangle').removeClass('fa-exclamation-triangle text-danger').addClass('fa-check-circle text-white');
                }
            }
        });
    }

    $(document).ready(function() {
        loadAnalyticsData();
        loadDlqHealth();

        // Instructions Guide Drawer Toggle logic
        $('#toggleGuideBtn').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').addClass('open');
            $('body').addClass('drawer-open');
        });

        $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').removeClass('open');
            $('body').removeClass('drawer-open');
        });
    });
</script>

</script>
