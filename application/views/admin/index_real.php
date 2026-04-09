<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- ── Strategic Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Strategic Analytics & Insights</h4>
            <p class="dt-page-subtitle">
                Data Coverage: <span class="badge bg-primary-soft text-primary font-weight-bold" style="background: rgba(102, 78, 255, 0.1); padding: 5px 12px; border-radius: 8px;"><?= $date_range_label; ?></span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Period Selector -->
            <div class="d-flex bg-card p-1 border shadow-sm gap-1 dt-period-selector" style="border-radius: 12px; background-color: var(--bg-card); border-color: var(--border-color) !important;">
                <a href="<?= base_url('admin/analytics?period=yesterday'); ?>" class="btn btn-sm <?= ($current_period == 'yesterday' ? 'btn-dt-action-primary active' : 'btn-light'); ?> px-3 py-2 font-weight-bold" style="border-radius: 10px; font-size: 11px; <?= ($current_period == 'yesterday' ? 'background: var(--primary); color: #fff; border: none;' : ''); ?>">Yesterday</a>
                <a href="<?= base_url('admin/analytics?period=last_7_days'); ?>" class="btn btn-sm <?= ($current_period == 'last_7_days' ? 'btn-dt-action-primary active' : 'btn-light'); ?> px-3 py-2 font-weight-bold" style="border-radius: 10px; font-size: 11px; <?= ($current_period == 'last_7_days' ? 'background: var(--primary); color: #fff; border: none;' : ''); ?>">Last 7 Days</a>
                <a href="<?= base_url('admin/analytics?period=last_month'); ?>" class="btn btn-sm <?= ($current_period == 'last_month' ? 'btn-dt-action-primary active' : 'btn-light'); ?> px-3 py-2 font-weight-bold" style="border-radius: 10px; font-size: 11px; <?= ($current_period == 'last_month' ? 'background: var(--primary); color: #fff; border: none;' : ''); ?>">Last Month</a>
            </div>

        </div>
    </div>

    <?php 
    // Growth Calculation Helper
    function get_growth($current, $prev) {
        if ($prev <= 0) return ($current > 0) ? 100 : 0;
        return round((($current - $prev) / $prev) * 100, 1);
    }
    
    function render_growth_badge($current, $prev) {
        $growth = get_growth($current, $prev);
        $icon = ($growth >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';
        $class = ($growth >= 0) ? 'success' : 'danger';
        $prefix = ($growth >= 0) ? '+' : '';
        
        return '<span class="badge bg-'.$class.'-soft text-'.$class.' px-2 py-1 rounded-pill" style="font-size: 10px;">
                    <i class="fas '.$icon.'"></i> '.$prefix.$growth.'%
                </span>';
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

    <!-- ── Growth KPI Cards ── -->
    <div class="dt-summary-row mb-4">
        
        <!-- QRIS Growth -->
        <div class="dt-summary-card dt-summary-blue border-0 shadow-sm" style="border-top: 4px solid var(--primary) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">QRIS Volume Growth</div>
                <div class="dt-summary-value" style="font-size: 1.5rem;">Rp <?= number_format($qris_current, 0, ',', '.') ?></div>
                <div class="dt-summary-sub d-flex align-items-center gap-2 d-block">
                    <?= render_growth_badge($qris_current, $qris_prev) ?>
                    <span style="font-size: 11px; color: #94a3b8;">vs <?= $comparison_label ?></span>
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <!-- Profitability Metric -->
        <div class="dt-summary-card dt-summary-green border-0 shadow-sm" style="border-top: 4px solid var(--success) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">Total Platform Profit</div>
                <div class="dt-summary-value text-success" style="font-size: 1.5rem;">Rp <?= number_format($profit_current, 0, ',', '.') ?></div>
                <div class="dt-summary-sub d-flex align-items-center gap-2 d-block">
                    <?= render_growth_badge($profit_current, $profit_prev) ?>
                    <span style="font-size: 11px; color: #94a3b8;">net yield vs <?= $comparison_label ?></span>
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>

        <!-- Disburse Efficiency -->
        <div class="dt-summary-card dt-summary-yellow border-0 shadow-sm" style="border-top: 4px solid var(--warning) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">Disburse Volume</div>
                <div class="dt-summary-value text-warning" style="font-size: 1.5rem;">Rp <?= number_format($disburse_current, 0, ',', '.') ?></div>
                <div class="dt-summary-sub d-flex align-items-center gap-2 d-block">
                    <?= render_growth_badge($disburse_current, $disburse_prev) ?>
                    <span style="font-size: 11px; color: #94a3b8;">vs <?= $comparison_label ?></span>
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="dt-summary-card <?= ($success_rate >= 95 ? 'dt-summary-red' : 'dt-summary-yellow'); ?> border-0 shadow-sm" style="border-top: 4px solid var(--<?= ($success_rate >= 95 ? 'danger' : 'warning'); ?>) !important;">
            <div class="dt-summary-body">
                <div class="dt-summary-label">Channel Success Rate</div>
                <div class="dt-summary-value text-<?= ($success_rate >= 95 ? 'danger' : 'warning'); ?>" style="font-size: 1.5rem;"><?= $success_rate; ?>%</div>
                <div class="dt-summary-sub d-flex align-items-center gap-2">
                    <span class="badge bg-<?= ($success_rate >= 95 ? 'danger' : 'warning'); ?>-soft text-<?= ($success_rate >= 95 ? 'danger' : 'warning'); ?> px-2 py-1 rounded-pill" style="font-size: 10px;">
                        <i class="fas <?= ($success_rate >= 95 ? 'fa-check' : 'fa-exclamation-circle'); ?>"></i> <?= ($success_rate >= 95 ? 'High' : 'Avg'); ?>
                    </span>
                    <span style="font-size: 11px; color: #94a3b8;">stable across channels</span>
                </div>
            </div>
            <div class="dt-summary-icon dt-icon-<?= ($success_rate >= 95 ? 'red' : 'yellow'); ?>">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

    </div>

    <!-- ── Analysis Depth Sections ── -->
    <div class="row">

        <!-- Area Chart: QRIS Growth Trends -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 20px;">
                <div class="card-header border-0 py-4 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;">Ecosystem Yield Trend (Yearly)</h6>
                        <p class="m-0 text-muted small">Sequential profit analysis for seasonal scaling patterns</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Revenue Distribution -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm dt-card h-100" style="border-radius: 20px;">
                <div class="card-header border-0 py-4 px-4">
                    <h6 class="m-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;">Profit Contribution Mix</h6>
                    <p class="m-0 text-muted small">Net yield distribution across channels (Fee - Ext. Cost)</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-pie pt-4 pb-2" style="height: 260px;">
                        <canvas id="channelPieChart"></canvas>
                    </div>
                    <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--primary);"></span>
                            <span class="small font-weight-bold text-gray-600">QRIS Profit</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></span>
                            <span class="small font-weight-bold text-gray-600">Disburse Profit</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--info);"></span>
                            <span class="small font-weight-bold text-gray-600">VA Profit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Strategic Recommendations Section ── -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg text-white" style="border-radius: 24px; background: linear-gradient(135deg, var(--primary) 0%, #4433ff 100%);">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h4 class="font-weight-bold mb-3" style="letter-spacing: -0.5px;">Strategic Recommendations</h4>
                            <p class="mb-4 opacity-75 mr-lg-5" style="line-height: 1.6;">Based on the analysis of last month's data, we've identified key areas for ecosystem growth and risk mitigation. These insights are derived from current transaction patterns and success rates.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex gap-3">
                                        <div class="bg-white rounded-lg d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; min-width: 42px; color: var(--primary);">
                                            <i class="fas fa-lightbulb"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1">Optimize Peak Volumes</h6>
                                            <p class="small m-0 text-white-50">Volume peaks between 7PM - 10PM. Schedule cache-clearing after midnight to maintain low latency.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex gap-3">
                                        <div class="bg-white rounded-lg d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; min-width: 42px; color: var(--primary);">
                                            <i class="fas fa-rocket"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1">Scale QRIS Partners</h6>
                                            <p class="small m-0 text-white-50">QRIS growth outperformed VA by 15%. Focus merchant onboarding on QRIS-ready retailers.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-right">
                            <img src="https://cdn-icons-png.flaticon.com/512/3094/3094837.png" style="width: 180px; opacity: 0.2; filter: brightness(0) invert(1);" alt="Insights Icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* Analytics Styling */
.chart-area {
    position: relative;
    padding-top: 10px;
}
.bg-success-soft { background-color: #ecfdf5; color: #059669; }
.bg-warning-soft { background-color: #fefce8; color: #ca8a04; }
.bg-danger-soft { background-color: #fef2f2; color: #dc2626; }
.opacity-75 { opacity: 0.75; }
.text-white-50 { color: rgba(255, 255, 255, 0.6); }

.dt-summary-card {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.dt-summary-card:hover {
    transform: translateY(-8px);
}
</style>

<script src="<?= base_url('assets/'); ?>vendor/chart.js/Chart.min.js"></script>
<script>
    // Global Config
    Chart.defaults.global.defaultFontFamily = "'Inter', -apple-system, system-ui, sans-serif";
    Chart.defaults.global.defaultFontColor = '#94a3b8';
    Chart.defaults.global.defaultFontSize = 11;

    function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Profit Contribution Mix Donut Chart
    var ctxDonut = document.getElementById("channelPieChart");
    var channelPieChart = new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ["QRIS Profit", "Disburse Profit", "VA Profit"],
        datasets: [{
          data: [
            <?php echo ($qris_summary_last_month[0]['fee'] - $qris_summary_last_month[0]['fee_external']); ?>, 
            <?php echo ($disburse_summary_last_month[0]['fee'] - $disburse_summary_last_month[0]['fee_external']); ?>, 
            <?php echo ($va_summary_last_month[0]['fee'] - $va_summary_last_month[0]['fee_external']); ?>
          ],
          backgroundColor: ['#664EFF', '#1cc88a', '#36b9cc'],
          hoverBackgroundColor: ['#553ce6', '#17a673', '#2c9faf'],
          hoverBorderColor: "#fff",
          borderWidth: 5
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
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, data) {
                var value = data.datasets[0].data[tooltipItem.index];
                return data.labels[tooltipItem.index] + ': Rp ' + number_format(value);
            }
          }
        }
      },
    });

    // Area Chart: Dynamic Growth Trends
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
          backgroundColor: "rgba(102, 78, 255, 0.08)",
          borderColor: "#664EFF",
          borderWidth: 3,
          pointRadius: 4,
          pointBackgroundColor: "#fff",
          pointBorderColor: "#664EFF",
          pointBorderWidth: 2,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: "#664EFF",
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
          mode: 'index',
          intersect: false,
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              return 'Volume: Rp ' + number_format(tooltipItem.yLabel);
            }
          }
        }
      }
    });

</script>