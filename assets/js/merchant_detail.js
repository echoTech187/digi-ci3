/**
 * Merchant Detail Interactive Dashboard & Tab Analytics
 */
$(document).ready(function() {
    'use strict';

    const merchantId = window.CURRENT_MERCHANT_ID || '';
    let historyTableInit = false;
    let mutationTableInit = false;
    let submerchantTableInit = false;

    let trendChart = null;
    let breakdownChart = null;
    let statusChart = null;

    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

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

    // Overview Metric & Chart Loader
    function loadOverview() {
        const startDate = $('#overview-start-date').val();
        const endDate = $('#overview-end-date').val();
        const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

        $('#stat-total-cnt, #stat-success-amt, #stat-total-fee, #stat-success-rate').html('<i class="fas fa-spinner fa-spin fa-xs"></i>');

        $.ajax({
            url: window.BASE_URL + "merchant/manage/audit-overview-ajax/" + merchantId,
            type: "POST",
            data: { start_date: startDate, end_date: endDate, [csrfName]: csrfHash },
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    const summary = response.summary;
                    const channels = response.channels;
                    const trend = response.trend;

                    $('#stat-total-cnt').text(number_format(summary.total_cnt));
                    $('#stat-success-cnt').text(number_format(summary.success_cnt));
                    $('#stat-success-amt').text('Rp ' + number_format(summary.success_amt, 0, ',', '.'));
                    $('#stat-total-fee').text('Rp ' + number_format(summary.total_fee, 0, ',', '.'));
                    $('#stat-success-rate').text(summary.success_rate + '%');

                    let tableBody = '';
                    const channelNames = Object.keys(channels);
                    channelNames.forEach(function(chan) {
                        const data = channels[chan];
                        const netAmount = data.success_amt - data.total_fee;
                        tableBody += `
                            <tr>
                                <td class="font-weight-bold text-dark py-3"><span class="badge badge-light border px-2 py-1 mr-2">${chan}</span></td>
                                <td class="text-center font-weight-bold text-gray-800 py-3">${number_format(data.total_cnt)}</td>
                                <td class="text-center text-success font-weight-bold py-3">${number_format(data.success_cnt)}</td>
                                <td class="text-right py-3">Rp ${number_format(data.total_amt, 0, ',', '.')}</td>
                                <td class="text-right text-danger py-3">Rp ${number_format(data.total_fee, 0, ',', '.')}</td>
                                <td class="text-right font-weight-bold text-success py-3">Rp ${number_format(netAmount, 0, ',', '.')}</td>
                            </tr>
                        `;
                    });
                    $('#channelAuditTable tbody').html(tableBody);

                    let subBody = '';
                    if (response.sub_merchants && response.sub_merchants.length > 0) {
                        response.sub_merchants.forEach(function(sub) {
                            const rate = sub.total_cnt > 0 ? ((sub.success_cnt / sub.total_cnt) * 100).toFixed(1) : 0;
                            subBody += `
                                <tr>
                                    <td class="py-3">
                                        <div class="font-weight-bold text-dark">${sub.sub_merchant_name}</div>
                                        <small class="text-muted">${sub.sub_merchant_email} (ID: ${sub.sub_merchant_id})</small>
                                    </td>
                                    <td class="text-center font-weight-bold text-gray-800 py-3">${number_format(sub.total_cnt)}</td>
                                    <td class="text-center py-3"><span class="badge badge-pill badge-success-soft text-success px-2 py-1">${number_format(sub.success_cnt)}</span></td>
                                    <td class="text-center py-3"><div class="font-weight-bold text-dark">${rate}%</div></td>
                                    <td class="text-right font-weight-bold text-success py-3">Rp ${number_format(sub.success_amt, 0, ',', '.')}</td>
                                </tr>
                            `;
                        });
                    } else {
                        subBody = `<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-info-circle mr-1"></i> No sub-merchants found or active under this account.</td></tr>`;
                    }
                    $('#submerchantAuditTable tbody').html(subBody);

                    renderTrendChart(trend);
                    renderBreakdownChart(channels, channelNames, summary);
                    renderStatusChart(response.statuses);
                    renderRecentActivity(response.recent_activity);
                } else {
                    Swal.fire('Error', response.message || 'Failed to load audit overview data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch overview data from the server.', 'error');
            }
        });
    }

    function renderTrendChart(trend) {
        if (trendChart) trendChart.destroy();
        const ctxTrend = document.getElementById('overviewTrendChart');
        if (!ctxTrend) return;
        const ctx = ctxTrend.getContext('2d');
        const gradTotal = ctx.createLinearGradient(0, 0, 0, 300);
        gradTotal.addColorStop(0, 'rgba(78, 115, 223, 0.4)');
        gradTotal.addColorStop(1, 'rgba(78, 115, 223, 0)');

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trend.labels.map(d => moment(d).format('DD MMM')),
                datasets: [{
                    label: 'Total Volume',
                    data: trend.datasets.total,
                    borderColor: '#4e73df',
                    backgroundColor: gradTotal,
                    fill: true,
                    tension: 0.3,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: c => 'Volume: Rp ' + number_format(c.raw, 0, ',', '.') } }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: val => (val >= 1e6 ? 'Rp ' + (val / 1e6) + 'M' : (val >= 1e3 ? 'Rp ' + (val / 1e3) + 'K' : 'Rp ' + val))
                        }
                    }
                }
            }
        });
    }

    function renderBreakdownChart(channels, channelNames, summary) {
        if (breakdownChart) breakdownChart.destroy();
        const ctxPie = document.getElementById('overviewBreakdownChart');
        if (!ctxPie) return;
        const ctx = ctxPie.getContext('2d');
        const breakdownData = channelNames.map(chan => channels[chan].success_amt);
        const hasData = breakdownData.some(val => val > 0);

        if (!hasData) {
            breakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#eaecf0'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
            $('#breakdown-legend').html('<span class="text-muted">No successful transactions in this period</span>');
        } else {
            const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#b833ff'];
            breakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: channelNames, datasets: [{ data: breakdownData, backgroundColor: colors }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
            });

            let legendHtml = '<div class="row text-center mt-2">';
            channelNames.forEach((name, idx) => {
                const val = breakdownData[idx];
                const pct = summary.success_amt > 0 ? ((val / summary.success_amt) * 100).toFixed(1) : 0;
                legendHtml += `<div class="col-6 mb-2 text-left"><span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${colors[idx]}"></span><strong class="text-dark">${name}</strong>: ${pct}%</div>`;
            });
            legendHtml += '</div>';
            $('#breakdown-legend').html(legendHtml);
        }
    }

    function renderStatusChart(statuses) {
        if (statusChart) statusChart.destroy();
        const ctxStatus = document.getElementById('overviewStatusChart');
        if (!ctxStatus) return;
        const ctx = ctxStatus.getContext('2d');

        const statusColors = {
            'Success': '#1cc88a', 'Pending': '#f6c23e', 'Process': '#36b9cc',
            'Failed': '#e74a3b', 'Timeout': '#858796', 'Cancel': '#5a5c69', 'Init': '#4e73df'
        };
        const allStatuses = ['Success', 'Pending', 'Process', 'Failed', 'Timeout', 'Cancel', 'Init'];
        const statusLabels = [];
        const statusData = [];
        const bgColors = [];

        allStatuses.forEach(function(st) {
            statusLabels.push(st);
            const val = (statuses && statuses[st]) ? parseInt(statuses[st]) : 0;
            statusData.push(val);
            bgColors.push(statusColors[st]);
        });

        const totalStatusCount = statusData.reduce((a, b) => a + b, 0);

        if (totalStatusCount === 0) {
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#eaecf0'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
        } else {
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: bgColors }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
            });
        }

        let statusLegendHtml = '<div class="row text-center mt-2">';
        statusLabels.forEach((name, idx) => {
            const val = statusData[idx];
            const pct = totalStatusCount > 0 ? ((val / totalStatusCount) * 100).toFixed(1) : 0;
            statusLegendHtml += `<div class="col-6 mb-2 text-left"><span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${bgColors[idx]}"></span><strong class="text-dark">${name}</strong>: ${val} (${pct}%)</div>`;
        });
        statusLegendHtml += '</div>';
        $('#status-legend').html(statusLegendHtml);
    }

    function renderRecentActivity(recentActivity) {
        let activityHtml = '';
        if (recentActivity && recentActivity.length > 0) {
            recentActivity.forEach(function(act) {
                const isSuccess = act.c_status === 'SUCCESS' || act.c_status === 'Success';
                const isFailed = act.c_status === 'FAILED' || act.c_status === 'Failed';
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
                            <div class="timeline-body">${formattedAmount}</div>
                            <div class="timeline-footer">
                                <span class="text-truncate mr-2" style="max-width: 140px;" title="${act.merchant_name || ''}">${act.merchant_name || 'Main Account'}</span>
                                <span class="font-weight-bold text-gray-600">${act.c_invoiceNo || '-'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            activityHtml = `<div class="text-center py-5 text-muted"><i class="fas fa-history fa-2x mb-3 text-gray-300"></i><p class="mb-0 small">No recent activity found in this period</p></div>`;
        }
        $('#recent-activity-list').html(activityHtml);
    }

    // Load initial overview
    loadOverview();

    $('#btn-overview-filter, #overview-start-date, #overview-end-date').on('click change', function() {
        loadOverview();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        if (target === "#nav-overview") loadOverview();

        if (target === "#nav-history" && !historyTableInit) {
            historyTableInit = true;
            initServerDataTable("#detailHistoryTable", window.BASE_URL + 'merchant/manage/history-ajax/' + merchantId, [
                { data: 'no' },
                { data: 'name_merchant' },
                { data: 'c_datetime', render: data => moment(data).format('DD-MM-YYYY HH:mm:ss') },
                { data: 'ref_cashoutChannelId' },
                { data: 'c_invoiceNo' },
                { data: 'c_phone' },
                { data: 'c_amount', render: data => 'Rp ' + number_format(data, 0, ',', '.') },
                { data: 'c_status', render: data => {
                    let badgeClass = (data === 'SUCCESS') ? 'badge-success' : ((data === 'FAILED') ? 'badge-danger' : 'badge-warning');
                    return '<span class="badge badge-pill ' + badgeClass + '">' + data + '</span>';
                }}
            ]);
        }

        if (target === "#nav-mutation" && !mutationTableInit) {
            mutationTableInit = true;
            initServerDataTable("#detailMutationTable", window.BASE_URL + 'merchant/manage/mutation-ajax/' + merchantId, [
                { data: 'no', orderable: false, className: 'text-center' },
                { data: 'c_datetime', render: data => '<i class="far fa-clock mr-1 text-muted"></i>' + (data || '-') },
                { data: 'c_position_raw', render: data => {
                    if (!data) return '-';
                    const cls = (data.toLowerCase() === 'credit') ? 'success' : 'danger';
                    return '<span class="badge badge-pill badge-' + cls + '-soft text-' + cls + ' px-3 py-1">' + data + '</span>';
                }},
                { data: 'channelName', render: data => '<span class="badge badge-light border px-2 py-1 text-uppercase small">' + (data || '-') + '</span>' },
                { data: 'description', className: 'small text-muted' },
                { data: 'c_amount_raw', className: 'text-right font-weight-bold', render: (data, type, row) => {
                    const isCredit = (row.c_position_raw || '').toLowerCase() === 'credit';
                    return '<span class="' + (isCredit ? 'text-success' : 'text-danger') + '">Rp ' + number_format(data, 0, ',', '.') + '</span>';
                }},
                { data: 'c_balance_raw', className: 'text-right font-weight-bold text-dark', render: data => 'Rp ' + number_format(data, 0, ',', '.') }
            ]);
        }

        if (target === "#nav-submerchant" && !submerchantTableInit) {
            submerchantTableInit = true;
            initServerDataTable("#detailSubmerchantTable", window.BASE_URL + 'merchant/manage/submerchant-ajax/' + merchantId, [
                { data: 'no', orderable: false },
                { data: 'c_name', className: 'font-weight-bold text-gray-800', render: (data, type, row) => '<div>' + data + '</div><small class="text-muted">ID: ' + row.id + '</small>' },
                { data: 'c_email' },
                { data: 'c_status', className: 'text-center', render: data => '<span class="badge badge-' + ((data === 'Active') ? 'success' : 'secondary') + '">' + data + '</span>' },
                { data: 'id', className: 'text-center', orderable: false, render: data => `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                            <li><a class="dropdown-item" href="${window.BASE_URL}merchant/sub-account/${data}"><i class="fas fa-users mr-2 text-success"></i>Sub Accounts</a></li>
                            <li><a class="dropdown-item" href="${window.BASE_URL}finance/mutation/${data}"><i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations</a></li>
                        </ul>
                    </div>
                `}
            ]);
        }
    });

    $('.dt-nav-tabs .nav-link').on('click', function() {
        $('.dt-nav-tabs .nav-link').removeClass('border-bottom border-primary text-primary').css('border-bottom-width', '0');
        $(this).addClass('border-bottom border-primary text-primary').css({ 'border-bottom-width': '3px', 'border-bottom-style': 'solid' });
    });
    $('.dt-nav-tabs .nav-link.active').addClass('border-bottom border-primary text-primary').css({ 'border-bottom-width': '3px', 'border-bottom-style': 'solid' });
});
