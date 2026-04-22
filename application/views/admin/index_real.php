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
                <a href="<?= base_url('admin/analytics?period=yesterday'); ?>" class="btn btn-sm <?= ($current_period == 'yesterday' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Yesterday</a>
                <a href="<?= base_url('admin/analytics?period=last_7_days'); ?>" class="btn btn-sm <?= ($current_period == 'last_7_days' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Last 7 Days</a>
                <a href="<?= base_url('admin/analytics?period=last_month'); ?>" class="btn btn-sm <?= ($current_period == 'last_month' ? 'btn-primary' : 'btn-link text-muted'); ?> px-4 py-2 font-weight-bold shadow-none" style="border-radius: 10px; font-size: 11px; text-decoration: none;">Last Month</a>
            </div>
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

    $qris_current = $current_stats['qris']['amount'];
    $qris_prev    = $prev_stats['qris']['amount'];

    $disburse_current = $current_stats['disburse']['amount'];
    $disburse_prev    = $prev_stats['disburse']['amount'];

    $va_current = $current_stats['va']['amount'];
    $va_prev    = $prev_stats['va']['amount'];

    $profit_current = ($current_stats['qris']['fee'] - $current_stats['qris']['fee_external']) + 
                      ($current_stats['disburse']['fee'] - $current_stats['disburse']['fee_external']) + 
                      ($current_stats['va']['fee'] - $current_stats['va']['fee_external']);

    $profit_prev = ($prev_stats['qris']['fee'] - $prev_stats['qris']['fee_external']) + 
                   ($prev_stats['disburse']['fee'] - $prev_stats['disburse']['fee_external']) + 
                   ($prev_stats['va']['fee'] - $prev_stats['va']['fee_external']);
    ?>

    <!-- ── Growth KPI Cards (Glassmorphism Transform) ── -->
    <div class="row mb-5 g-4">
        
        <!-- QRIS Growth -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>QRIS GROWTH</span>
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3" style="font-size: 1.4rem;">Rp <?= number_format($qris_current, 0, ',', '.') ?></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <?= render_growth_badge($qris_current, $qris_prev) ?>
                        <span class="small" style="opacity: 0.8;">vs <?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profitability Metric -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>PLATFORM YIELD</span>
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3" style="font-size: 1.4rem;">Rp <?= number_format($profit_current, 0, ',', '.') ?></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <?= render_growth_badge($profit_current, $profit_prev) ?>
                        <span class="small" style="opacity: 0.8;">net vs <?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disburse Efficiency -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>DISBURSE VOL.</span>
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3" style="font-size: 1.4rem;">Rp <?= number_format($disburse_current, 0, ',', '.') ?></h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <?= render_growth_badge($disburse_current, $disburse_prev) ?>
                        <span class="small" style="opacity: 0.8;">vs <?= $comparison_label ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm dash-kpi-card h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small font-weight-bold uppercase" style="letter-spacing: 1px;">
                        <span>CHANNEL STABILITY</span>
                        <i class="fas fa-vial"></i>
                    </div>
                    <h3 class="font-weight-bold mb-3" style="font-size: 1.4rem;"><?= $success_rate; ?>%</h3>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1" style="background: rgba(255,255,255,0.2); font-size: 10px; color: white;">
                             <i class="fas <?= ($success_rate >= 95 ? 'fa-check' : 'fa-exclamation-triangle'); ?> mr-1"></i> <?= ($success_rate >= 95 ? 'HIGH' : 'NORMAL'); ?>
                        </span>
                        <span class="small" style="opacity: 0.8;">integrity score</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Analysis Depth Sections ── -->
    <div class="row g-4 mb-5">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 24px;">
                <div class="card-header border-0 bg-transparent py-4 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-gray-900" style="font-size: 1rem;">Yield Analysis Trend</h6>
                        <p class="m-0 text-muted small mt-1">Growth progression in total platform volume over selected period</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-area" style="height: 380px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 24px;">
                <div class="card-header border-0 bg-transparent py-4 px-4">
                    <h6 class="m-0 font-weight-bold text-gray-900" style="font-size: 1rem;">Net Contribution Mix</h6>
                    <p class="m-0 text-muted small mt-1">Direct profit distribution (Fee - Cost)</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-pie pt-4 mb-4" style="height: 280px; position: relative;">
                        <canvas id="channelPieChart"></canvas>
                        <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <span class="d-block text-muted" style="font-size: 10px; text-transform: uppercase; font-weight: 800;">Yield</span>
                            <span class="h4 font-weight-bolder text-dark mb-0">Rp<?= number_format($profit_current/1000000, 1) ?>M</span>
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
                                <div class="col-md-6 mb-4">
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
                                <div class="col-md-6 mb-4">
                                    <div class="p-4 h-100 d-flex flex-column" style="background: rgba(255,255,255,0.05); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-emerald rounded-lg d-flex align-items-center justify-content-center mr-3" style="width: 38px; height: 38px; min-width: 38px; background: #10b981;">
                                                <i class="fas fa-rocket"></i>
                                            </div>
                                            <h6 class="font-weight-bold m-0">Expansion Opportunity</h6>
                                        </div>
                                        <p class="small m-0 text-white-50" style="line-height: 1.6;">QRIS yield accounts for <?= $profit_current > 0 ? round(($qris_current / $profit_current) * 100, 1) : 0 ?>% of net income. Accelerate onboarding for street-level retail partners.</p>
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

    // Profit Contribution Mix (Enhanced Donut)
    var ctxDonut = document.getElementById("channelPieChart");
    var channelPieChart = new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ["QRIS", "Disburse", "VA"],
        datasets: [{
          data: [
            <?= ($current_stats['qris']['fee'] - $current_stats['qris']['fee_external']); ?>, 
            <?= ($current_stats['disburse']['fee'] - $current_stats['disburse']['fee_external']); ?>, 
            <?= ($current_stats['va']['fee'] - $current_stats['va']['fee_external']); ?>
          ],
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
    var chartLabels = <?= json_encode($chart_data['labels']); ?>;
    var chartValues = <?= json_encode($chart_data['values']); ?>;
    var periodLabel = '<?= ($current_period == "yesterday" ? "Hourly" : "Daily"); ?>';

    var ctxArea = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctxArea, {
      type: 'line',
      data: {
        labels: chartLabels,
        datasets: [{
          label: periodLabel + " Volume",
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
          data: chartValues,
        }],
      },
      options: {
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
          yAxes: [{
            gridLines: { color: "rgba(241, 245, 249, 1)", zeroLineColor: "rgba(241, 245, 249, 1)", drawBorder: false },
            ticks: {
              callback: function(value) { return 'Rp' + number_format(value); },
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

</script>
