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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    const table = initServerDataTable('#balanceLogTable', '<?= base_url("admin/balance_log") ?>', [
        {data: 'no', orderable: false, className: 'text-center'},
        {data: 'created_at', render: function(data){
            return '<span class="font-weight-bold">' + moment(data).format('DD-MM-YYYY HH:mm:ss') + '</span>';
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
        dom: 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
        order: [[1, 'desc']],
        language: {
            "info": "Showing _START_ – _END_ of _TOTAL_ entries",
            "infoEmpty": "No results to show",
            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No logs found.</div>'
        },
        drawCallback: function(settings) {
            var api    = this.api();
            var info   = api.page.info();
            var $pager = $(api.table().container()).find('.dt-footer-pager');

            var currPage   = info.page + 1;
            var totalPages = info.pages || 1;

            $pager.html(
                '<button class="dt-nav-btn dt-prev-btn" ' + (info.page === 0 ? 'disabled' : '') + '>' +
                    '<i class="fas fa-chevron-left mr-2"></i> PREVIOUS' +
                '</button>' +
                '<span class="dt-page-counter">' +
                    '<strong>' + currPage + '</strong> of <strong>' + totalPages + '</strong>' +
                '</span>' +
                '<button class="dt-nav-btn dt-next-btn" ' + (info.page >= totalPages - 1 ? 'disabled' : '') + '>' +
                    'NEXT <i class="fas fa-chevron-right"></i>' +
                '</button>'
            );

            $pager.find('.dt-prev-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('previous').draw('page'); }
            });
            $pager.find('.dt-next-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('next').draw('page'); }
            });
        }
    });

    // Global Search Binding
    // Optimized search with debounce
    $('#dt-global-search').on('keyup', debounce(function() {
        table.search(this.value).draw();
    }, 400));
});
</script>


