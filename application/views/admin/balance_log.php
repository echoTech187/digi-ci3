<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700"><?= $title; ?></h4>
            <p class="dt-page-subtitle text-muted">Monitoring and auditing merchant balance configurations and hold adjustments.</p>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4 d-none">
        <!-- Total Logs -->
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL LOGS</div>
                <div class="dt-summary-value"><?= number_format($total_logs, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-history mr-1 mr-2"></i> Total adjustment records</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>

        <!-- Active Merchants -->
        <div class="dt-summary-card dt-summary-green">
            <div class="dt-summary-body">
                <div class="dt-summary-label">ACTIVE MERCHANTS</div>
                <div class="dt-summary-value"><?= number_format($total_merchants, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-users-cog mr-1 mr-2"></i> Unique merchants adjusted</div>
            </div>
            <div class="dt-summary-icon dt-icon-green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <!-- Total Settled -->
        <div class="dt-summary-card dt-summary-orange">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL SETTLED</div>
                <div class="dt-summary-value">Rp <?= number_format($total_settled, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-wallet mr-1 mr-2"></i> Cumulative balance added</div>
            </div>
            <div class="dt-summary-icon dt-icon-orange">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Avg. Adjustment -->
        <div class="dt-summary-card dt-summary-purple">
            <div class="dt-summary-body">
                <div class="dt-summary-label">AVG. ADJUSTMENT</div>
                <div class="dt-summary-value">Rp <?= number_format($avg_settled, 0, ',', '.'); ?></div>
                <div class="dt-summary-sub"><i class="fas fa-chart-line mr-1 mr-2"></i> Average per transaction</div>
            </div>
            <div class="dt-summary-icon dt-icon-purple">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <div class="dt-toolbar-left">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search logs...">
                </div>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="balanceLogTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>DATE</th>
                        <th>MERCHANT ID</th>
                        <th>MERCHANT NAME</th>
                        <th class="text-right">BALANCE SETTLED</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── External Scripts ── -->
<script>
$(document).ready(function() {
    const table = initServerDataTable('#balanceLogTable', '<?= base_url("admin/balance_log") ?>', [
        {data: 'no', orderable: false, className: 'text-center'},
        {data: 'created_at', render: function(data){
            return '<span class="font-weight-bold">' + (data ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-') + '</span>';
        }},
        {data: 'merchant_id', render: function(data){
            return '<span class="badge badge-light text-dark border px-2 py-1">' + data + '</span>';
        }},
        {data: 'merchant_name', className: 'font-weight-bold text-dark'},
        {data: 'add_to_available', render: function(data){
            var val = typeof data === 'string' ? data.replace(/[^0-9.-]+/g,"") : data;
            return '<span class="font-weight-bold text-dark">Rp ' + Number(val).toLocaleString('id-ID') + '</span>';
        }, className: 'text-right'}
    ], {
        order: [[1, 'desc']]
    });

    // Global Search Binding with debounce
    $('#dt-global-search').on('keyup', debounce(function() {
        table.search(this.value).draw();
    }, 400));
});
</script>


