// Dashboard Interactive Charts & Real-time Metrics
$(document).ready(function() {
    // Instruction Drawer Trigger
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    // Helper: Number Formatter
    function formatNumber(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    var mainAreaChart = null;
    var ctxLine = document.getElementById("mainAreaChart");
    if (ctxLine) {
        mainAreaChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: (window.dashboardMonthlyLabels || []),
                datasets: [
                    {
                        label: "QRIS Incoming",
                        lineTension: 0.4,
                        backgroundColor: "rgba(99, 102, 241, 0.05)",
                        borderColor: "#6366f1",
                        pointRadius: 3,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#6366f1",
                        pointBorderWidth: 2,
                        borderWidth: 3.5
                    },
                    {
                        label: "BI-FAST Disbursed",
                        lineTension: 0.4,
                        backgroundColor: "rgba(245, 158, 11, 0.05)",
                        borderColor: "#f59e0b",
                        pointRadius: 3,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#f59e0b",
                        pointBorderWidth: 2,
                        borderWidth: 3.5
                    }
                ],
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
                                return 'Rp ' + formatNumber(value); 
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
                    yPadding: 15,
                    displayColors: true,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return ' ' + data.datasets[tooltipItem.datasetIndex].label + ': Rp ' + formatNumber(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }

    var channelDonutChart = null;
    var ctxDonut = document.getElementById("channelDonutChart");
    if (ctxDonut) {
        channelDonutChart = new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ["QRIS", "VA", "E-Wallet", "Disburse"],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: ['#6366f1', '#10b981', '#3b82f6', '#f59e0b'],
                    hoverBackgroundColor: ['#4f46e5', '#059669', '#2563eb', '#d97706'],
                    hoverBorderColor: "#fff",
                    borderWidth: 6,
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 84,
                legend: { display: false },
                tooltips: {
                    backgroundColor: "rgba(255, 255, 255, 1)",
                    bodyFontColor: "#1e293b",
                    bodyFontSize: 13,
                    borderColor: "#e2e8f0",
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.labels[tooltipItem.index];
                            var value = data.datasets[0].data[tooltipItem.index];
                            return ' ' + label + ': ' + formatNumber(value) + ' transactions';
                        }
                    }
                },
            },
        });
    }

    function loadMetadata() {
        var url = window.dashboardMetadataUrl || (window.BASE_URL + 'dashboard/metadata/json');
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(resp) {
                $("#stat_merchant_count").text(formatNumber(resp.merchant_count));
                
                var is_active = (resp.maintenance_status === 'Active');
                $("#maintenance_label").text(is_active ? 'Online' : 'Maintenance');
                
                $("#maintenance_dot").removeClass('bg-secondary bg-success bg-danger')
                    .addClass(is_active ? 'bg-success' : 'bg-danger');
                
                $("#maintenance_ping").removeClass('bg-secondary bg-success bg-danger')
                    .addClass(is_active ? 'bg-success' : 'bg-danger');
                
                if (is_active) $("#maintenance_ping").addClass('animate-ping');
                else $("#maintenance_ping").removeClass('animate-ping');
            }
        });
    }

    function loadTodayStats() {
        var url = window.dashboardTodayStatsUrl || (window.BASE_URL + 'dashboard/today-stats/json');
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(resp) {
                $("#stat_last_synced").text(resp.last_synced);
                $("#stat_total_volume").text(formatNumber(Math.round(resp.today_stats.total_volume)));
                $("#stat_total_qty").text(formatNumber(resp.today_stats.total_qty));
                $("#stat_total_qty_donut").text(formatNumber(resp.today_stats.total_qty));
                
                $("#stat_qris_amount").text(formatNumber(Math.round(resp.today_stats.qris.amount)));
                $("#stat_qris_qty").text(formatNumber(resp.today_stats.qris.qty));
                
                $("#stat_disburse_amount").text(formatNumber(Math.round(resp.today_stats.disburse.amount)));
                $("#stat_disburse_qty").text(formatNumber(resp.today_stats.disburse.qty));

                if (channelDonutChart) {
                    channelDonutChart.data.datasets[0].data = [
                        resp.today_stats.qris.qty,
                        resp.today_stats.va.qty,
                        resp.today_stats.ewallet.qty,
                        resp.today_stats.disburse.qty
                    ];
                    channelDonutChart.update();
                }
            }
        });
    }

    function loadMonthlyStats() {
        var url = window.dashboardMonthlyStatsUrl || (window.BASE_URL + 'dashboard/monthly-stats/json');
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(resp) {
                if (mainAreaChart) {
                    mainAreaChart.data.datasets[0].data = resp.monthly_overview.qris;
                    mainAreaChart.data.datasets[1].data = resp.monthly_overview.disburse;
                    mainAreaChart.update();
                }
            }
        });
    }

    loadMetadata();
    loadTodayStats();
    loadMonthlyStats();
});
