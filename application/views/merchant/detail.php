<div class="glass-container position-relative">
    <div class="glass-aurora-1"></div>
    <div class="glass-aurora-2"></div>
    <div class="glass-aurora-3"></div>

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

                <!-- ── TAB 1: MERCHANT INFORMATION (ULTRA-PREMIUM OVERHAUL) ── -->
                <div class="tab-pane fade p-4" id="nav-profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <!-- Kolom Kiri: Core Profile -->
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 20px; box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); overflow: hidden;">
                                <div class="card-header bg-transparent border-bottom p-4 d-flex align-items-center" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                                    <div class="avatar-sm bg-primary-soft text-primary rounded-circle p-2 mr-3" style="background-color: rgba(78, 115, 223, 0.15); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user-tie fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-white mb-0">Core Profile</h6>
                                        <p class="text-muted small mb-0">Basic information and account legalities</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building text-primary mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Merchant Name</span>
                                        </div>
                                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_name']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-info mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Official Email</span>
                                        </div>
                                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_email']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone-alt text-success mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Phone Number</span>
                                        </div>
                                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_phoneNumber'] ?: '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-check text-warning mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Registration Date</span>
                                        </div>
                                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_dateCreated'] ? date('d M Y, H:i', strtotime($merchant['c_dateCreated'])) : '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group text-purple mr-3" style="width: 20px; color: #b833ff;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Merchant Level</span>
                                        </div>
                                        <span class="badge badge-pill badge-info-soft text-info px-3 py-1 font-weight-bold">Level <?= $merchant['c_merchantLevel']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-success mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Account Status</span>
                                        </div>
                                        <?php $st = $merchant['c_status']; $cls = ($st=='Active')?'success':'secondary'; ?>
                                        <span class="badge badge-pill badge-<?= $cls; ?>-soft text-<?= $cls; ?> px-3 py-1 font-weight-bold"><?= $st; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: OpenAPI Config -->
                        <div class="col-lg-6">
                            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 20px; box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); overflow: hidden;">
                                <div class="card-header bg-transparent border-bottom p-4 d-flex align-items-center" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                                    <div class="avatar-sm bg-purple-soft text-purple rounded-circle p-2 mr-3" style="background-color: rgba(184, 51, 255, 0.15); color: #b833ff; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-network-wired fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-white mb-0">OpenAPI & Integration Config</h6>
                                        <p class="text-muted small mb-0">Technical parameters, callbacks, and gateway credentials</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-plug text-success mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">OpenAPI Status</span>
                                        </div>
                                        <?php $ost = $merchant['c_openapiStatus']; $ocls = ($ost=='Active')?'success':'danger'; ?>
                                        <span class="badge badge-pill badge-<?= $ocls; ?>-soft text-<?= $ocls; ?> px-3 py-1 font-weight-bold"><?= $ost; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-link text-info mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">VA Callback URL</span>
                                        </div>
                                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #36b9cc;" title="<?= $merchant['c_openapiUrlCallbackVa']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackVa'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-qrcode text-warning mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">QRIS Callback URL</span>
                                        </div>
                                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #f6c23e;" title="<?= $merchant['c_openapiUrlCallbackQrisMpm']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackQrisMpm'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-wallet text-primary mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">E-Wallet Callback</span>
                                        </div>
                                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #4e73df;" title="<?= $merchant['c_openapiUrlCallbackEwallet']; ?>">
                                            <?= $merchant['c_openapiUrlCallbackEwallet'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-id-badge text-purple mr-3" style="width: 20px; color: #b833ff;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">GVConnect ID</span>
                                        </div>
                                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_gvconnectBusinessId'] ?: '-'; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-3 px-4">
                                        <div class="d-flex align-items-center mr-2">
                                            <i class="fas fa-shield-alt text-danger mr-3" style="width: 20px;"></i>
                                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">IP Allowlist</span>
                                        </div>
                                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #e74a3b;" title="<?= $merchant['c_openapiIPAllow']; ?>">
                                            <?= $merchant['c_openapiIPAllow'] ?: '<em class="text-muted">Any IP Allowed</em>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB 2: TRANSACTION HISTORY ── -->
                <div class="tab-pane fade p-4" id="nav-history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailHistoryTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Merchant</th>
                                    <th>Date Time</th>
                                    <th>Product ID</th>
                                    <th>Invoice No</th>
                                    <th>Customer No</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- ── TAB 3: MUTATION LOG ── -->
                <div class="tab-pane fade p-4" id="nav-mutation" role="tabpanel" aria-labelledby="mutation-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailMutationTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Date Time</th>
                                    <th>Position</th>
                                    <th>Channel</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- ── TAB 4: SUB ACCOUNTS ── -->
                <div class="tab-pane fade p-4" id="nav-submerchant" role="tabpanel" aria-labelledby="submerchant-tab">
                    <div class="table-responsive">
                        <table class="table dt-table mb-0" id="detailSubmerchantTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Submerchant Name</th>
                                    <th>Email Address</th>
                                    <th>Status</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const merchantId = "<?= $merchant['id'] ?>";
    let historyTableInit = false;
    let mutationTableInit = false;
    let submerchantTableInit = false;

    let trendChart = null;
    let breakdownChart = null;
    let statusChart = null;

    function loadOverview() {
        const startDate = $('#overview-start-date').val();
        const endDate = $('#overview-end-date').val();

        $('#stat-total-cnt').text('...');
        $('#stat-success-amt').text('...');
        $('#stat-total-fee').text('...');
        $('#stat-success-rate').text('...');

        $.ajax({
            url: "<?= base_url('merchant/manage/overview-ajax/') ?>" + merchantId,
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const summary = response.summary;
                    const channels = response.channels;
                    const trend = response.trend;

                    $('#stat-total-cnt').text(number_format(summary.total_cnt));
                    $('#stat-success-amt').text('Rp ' + number_format(summary.success_amt, 0, ',', '.'));
                    $('#stat-success-cnt').text(number_format(summary.success_cnt));
                    $('#stat-total-fee').text('Rp ' + number_format(summary.total_fee, 0, ',', '.'));
                    
                    const successRate = summary.total_cnt > 0 ? ((summary.success_cnt / summary.total_cnt) * 100).toFixed(1) : 0;
                    $('#stat-success-rate').text(successRate + '%');

                    let tableBody = '';
                    const channelNames = ['PPOB', 'VA', 'QRIS', 'EWallet', 'BiFast'];
                    channelNames.forEach(function(chan) {
                        const data = channels[chan];
                        const netAmount = data.success_amt;
                        const rate = data.cnt > 0 ? ((data.success_cnt / data.cnt) * 100).toFixed(1) : 0;
                        tableBody += `
                            <tr>
                                <td class="py-3">
                                    <div class="font-weight-bold text-dark">${chan}</div>
                                </td>
                                <td class="text-center font-weight-bold text-gray-800 py-3">${number_format(data.cnt)}</td>
                                <td class="text-center py-3">
                                    <span class="badge badge-pill badge-success-soft text-success px-2 py-1">${number_format(data.success_cnt)}</span>
                                    <small class="text-muted d-block mt-1">${rate}% Success</small>
                                </td>
                                <td class="text-right text-gray-600 py-3">Rp ${number_format(data.amt, 0, ',', '.')}</td>
                                <td class="text-right text-danger py-3">Rp ${number_format(data.fee, 0, ',', '.')}</td>
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
                                    <td class="text-center py-3">
                                        <span class="badge badge-pill badge-success-soft text-success px-2 py-1">${number_format(sub.success_cnt)}</span>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="font-weight-bold text-dark">${rate}%</div>
                                    </td>
                                    <td class="text-right font-weight-bold text-success py-3">Rp ${number_format(sub.success_amt, 0, ',', '.')}</td>
                                </tr>
                            `;
                        });
                    } else {
                        subBody = `
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> No sub-merchants found or active under this account.
                                </td>
                            </tr>
                        `;
                    }
                    $('#submerchantAuditTable tbody').html(subBody);

                    if (trendChart) {
                        trendChart.destroy();
                    }
                    const ctxTrend = document.getElementById('overviewTrendChart').getContext('2d');
                    
                    const gradTotal = ctxTrend.createLinearGradient(0, 0, 0, 300);
                    gradTotal.addColorStop(0, 'rgba(78, 115, 223, 0.4)');
                    gradTotal.addColorStop(1, 'rgba(78, 115, 223, 0)');

                    trendChart = new Chart(ctxTrend, {
                        type: 'line',
                        data: {
                            labels: trend.labels.map(d => moment(d).format('DD MMM')),
                            datasets: [
                                {
                                    label: 'Total Volume',
                                    data: trend.datasets.total,
                                    borderColor: '#4e73df',
                                    backgroundColor: gradTotal,
                                    fill: true,
                                    tension: 0.3,
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Volume: Rp ' + number_format(context.raw, 0, ',', '.');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1e6) return 'Rp ' + (value / 1e6) + 'M';
                                            if (value >= 1e3) return 'Rp ' + (value / 1e3) + 'K';
                                            return 'Rp ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    if (breakdownChart) {
                        breakdownChart.destroy();
                    }
                    const ctxPie = document.getElementById('overviewBreakdownChart').getContext('2d');
                    
                    const breakdownData = channelNames.map(chan => channels[chan].success_amt);
                    const hasData = breakdownData.some(val => val > 0);

                    if (!hasData) {
                        breakdownChart = new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: ['No Data'],
                                datasets: [{
                                    data: [1],
                                    backgroundColor: ['#eaecf0'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { enabled: false } }
                            }
                        });
                        $('#breakdown-legend').html('<span class="text-muted">No successful transactions in this period</span>');
                    } else {
                        breakdownChart = new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: channelNames,
                                datasets: [{
                                    data: breakdownData,
                                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#b833ff'],
                                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });

                        let legendHtml = '<div class="row text-center mt-2">';
                        const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#b833ff'];
                        channelNames.forEach((name, idx) => {
                            const val = breakdownData[idx];
                            const pct = summary.success_amt > 0 ? ((val / summary.success_amt) * 100).toFixed(1) : 0;
                            legendHtml += `
                                <div class="col-6 mb-2 text-left">
                                    <span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${colors[idx]}"></span>
                                    <strong class="text-dark">${name}</strong>: ${pct}%
                                </div>
                            `;
                        });
                        legendHtml += '</div>';
                        $('#breakdown-legend').html(legendHtml);
                    }

                    // Render Status Distribution Chart
                    if (statusChart) {
                        statusChart.destroy();
                    }
                    const ctxStatus = document.getElementById('overviewStatusChart').getContext('2d');
                    
                    const statusColors = {
                        'Success': '#1cc88a',
                        'Pending': '#f6c23e',
                        'Process': '#36b9cc',
                        'Failed': '#e74a3b',
                        'Timeout': '#858796',
                        'Cancel': '#5a5c69',
                        'Init': '#4e73df'
                    };

                    const allStatuses = ['Success', 'Pending', 'Process', 'Failed', 'Timeout', 'Cancel', 'Init'];
                    const statusLabels = [];
                    const statusData = [];
                    const bgColors = [];

                    allStatuses.forEach(function(st) {
                        statusLabels.push(st);
                        const val = (response.statuses && response.statuses[st]) ? parseInt(response.statuses[st]) : 0;
                        statusData.push(val);
                        bgColors.push(statusColors[st]);
                    });

                    const totalStatusCount = statusData.reduce((a, b) => a + b, 0);

                    if (totalStatusCount === 0) {
                        statusChart = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: ['No Data'],
                                datasets: [{
                                    data: [1],
                                    backgroundColor: ['#eaecf0'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: { legend: { display: false }, tooltip: { enabled: false } }
                            }
                        });
                    } else {
                        statusChart = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: statusLabels,
                                datasets: [{
                                    data: statusData,
                                    backgroundColor: bgColors,
                                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Always generate and display the full legend
                    let statusLegendHtml = '<div class="row text-center mt-2">';
                    statusLabels.forEach((name, idx) => {
                        const val = statusData[idx];
                        const pct = totalStatusCount > 0 ? ((val / totalStatusCount) * 100).toFixed(1) : 0;
                        statusLegendHtml += `
                            <div class="col-6 mb-2 text-left">
                                <span class="d-inline-block mr-1" style="width:10px; height:10px; border-radius:50%; background:${bgColors[idx]}"></span>
                                <strong class="text-dark">${name}</strong>: ${val} (${pct}%)
                            </div>
                        `;
                    });
                    statusLegendHtml += '</div>';
                    $('#status-legend').html(statusLegendHtml);

                    // Render Recent Activity Timeline
                    let activityHtml = '';
                    if (response.recent_activity && response.recent_activity.length > 0) {
                        response.recent_activity.forEach(function(act) {
                            const isSuccess = act.c_status === 'SUCCESS' || act.c_status === 'Success';
                            const isFailed = act.c_status === 'FAILED' || act.c_status === 'Failed';
                            const isPending = act.c_status === 'PENDING' || act.c_status === 'Pending' || act.c_status === 'Process';
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
                                        <div class="timeline-body">
                                            ${formattedAmount}
                                        </div>
                                        <div class="timeline-footer">
                                            <span class="text-truncate mr-2" style="max-width: 140px;" title="${act.merchant_name || ''}">${act.merchant_name || 'Main Account'}</span>
                                            <span class="font-weight-bold text-gray-600">${act.c_invoiceNo || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        activityHtml = `
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-2x mb-3 text-gray-300"></i>
                                <p class="mb-0 small">No recent activity found in this period</p>
                            </div>
                        `;
                    }
                    $('#recent-activity-list').html(activityHtml);

                } else {
                    Swal.fire('Error', response.message || 'Failed to load audit overview data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch overview data from the server.', 'error');
            }
        });
    }

    // Load initial overview
    loadOverview();

    // Bind filter button click
    $('#btn-overview-filter').on('click', function() {
        loadOverview();
    });
    $('#overview-start-date, #overview-end-date').on('change', function() {
        loadOverview();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");

        if (target === "#nav-overview") {
            loadOverview();
        }

        // Init History Table
        if (target === "#nav-history" && !historyTableInit) {
            historyTableInit = true;
            initServerDataTable("#detailHistoryTable", "<?= base_url('merchant/manage/history-ajax/') ?>" + merchantId, [
                { data: 'no' },
                { data: 'name_merchant' },
                { data: 'c_datetime', render: function(data){ return moment(data).format('DD-MM-YYYY HH:mm:ss'); } },
                { data: 'ref_cashoutChannelId' },
                { data: 'c_invoiceNo' },
                { data: 'c_phone' },
                { data: 'c_amount', render: function(data){ return 'Rp ' + number_format(data, 0, ',', '.'); } },
                { data: 'c_status', render: function(data){
                    let badgeClass = 'badge-secondary';
                    if(data === 'SUCCESS') badgeClass = 'badge-success';
                    if(data === 'FAILED') badgeClass = 'badge-danger';
                    if(data === 'PENDING') badgeClass = 'badge-warning';
                    return '<span class="badge badge-pill ' + badgeClass + '">' + data + '</span>';
                }}
            ]);
        }

        // Init Mutation Table
        if (target === "#nav-mutation" && !mutationTableInit) {
            mutationTableInit = true;
            initServerDataTable("#detailMutationTable", "<?= base_url('merchant/manage/mutation-ajax/') ?>" + merchantId, [
                { data: 'no', orderable: false, className: 'text-center' },
                { data: 'c_datetime', render: function(data){ return '<i class="far fa-clock mr-1 text-muted"></i>' + (data ? data : '-'); } },
                { data: 'c_position_raw', render: function(data) {
                    if (!data) return '-';
                    const d = data.toLowerCase();
                    const cls = (d === 'credit') ? 'success' : 'danger';
                    return '<span class="badge badge-pill badge-' + cls + '-soft text-' + cls + ' px-3 py-1">' + data + '</span>';
                }},
                { data: 'channelName', render: function(data) { return '<span class="badge badge-light border px-2 py-1 text-uppercase small">' + (data || '-') + '</span>'; } },
                { data: 'description', className: 'small text-muted' },
                { data: 'c_amount_raw', className: 'text-right font-weight-bold', render: function(data, type, row){
                    const isCredit = (row.c_position_raw || '').toLowerCase() === 'credit';
                    const colorCls = isCredit ? 'text-success' : 'text-danger';
                    return '<span class="' + colorCls + '">Rp ' + number_format(data, 0, ',', '.') + '</span>';
                }},
                { data: 'c_balance_raw', className: 'text-right font-weight-bold text-dark', render: function(data){ return 'Rp ' + number_format(data, 0, ',', '.'); } }
            ]);
        }

        // Init Submerchant Table
        if (target === "#nav-submerchant" && !submerchantTableInit) {
            submerchantTableInit = true;
            initServerDataTable("#detailSubmerchantTable", "<?= base_url('merchant/manage/submerchant-ajax/') ?>" + merchantId, [
                { data: 'no', orderable: false },
                { data: 'c_name', className: 'font-weight-bold text-gray-800', render: function(data, type, row) {
                    return '<div>' + data + '</div><small class="text-muted">ID: ' + row.id + '</small>';
                }},
                { data: 'c_email' },
                { data: 'c_status', className: 'text-center', render: function(data) {
                    const status_class = (data === 'Active') ? 'success' : 'secondary';
                    return '<span class="badge badge-' + status_class + '">' + data + '</span>';
                }},
                { data: 'id', className: 'text-center', orderable: false, render: function(data) {
                    const baseUrl = "<?= base_url() ?>";
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                                <li><a class="dropdown-item" href="${baseUrl}merchant/sub-account/${data}"><i class="fas fa-users mr-2 text-success"></i>Sub Accounts</a></li>
                                <li><a class="dropdown-item" href="${baseUrl}finance/mutation/${data}"><i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations</a></li>
                            </ul>
                        </div>
                    `;
                }}
            ]);
        }
    });

    // Custom styling for tabs to look premium
    $('.dt-nav-tabs .nav-link').on('click', function() {
        $('.dt-nav-tabs .nav-link').removeClass('border-bottom border-primary text-primary').css('border-bottom-width', '0');
        $(this).addClass('border-bottom border-primary text-primary').css({
            'border-bottom-width': '3px',
            'border-bottom-style': 'solid'
        });
    });
    // Set initial active tab style
    $('.dt-nav-tabs .nav-link.active').addClass('border-bottom border-primary text-primary').css({
        'border-bottom-width': '3px',
        'border-bottom-style': 'solid'
    });

    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
