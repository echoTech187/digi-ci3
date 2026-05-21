<!-- Begin Page Content -->
<div>
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Reports Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Access, schedule, and download Excel/CSV reconciliation reports for accounting.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-file-excel text-primary mr-2"></i> Report Generation</div>
                <p class="drawer-card-text">Request asynchronous report builds for high-volume date periods.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-download text-primary mr-2"></i> Download Options</div>
                <p class="drawer-card-text">Secure link retrieval for downloaded file archives.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-archive text-primary mr-2"></i> Archive Retention</div>
                <p class="drawer-card-text">Generated report files are kept for a limited duration before automatic cleanup.</p>
            </div>
        </div>
    </div>


    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Download and manage generated transaction report files.</p>
        
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="reportGlobalSearch" class="dt-search-input" placeholder="Search by type, filename, status...">
            </div>

            <!-- RIGHT: Date Filter -->
            <div class="dt-toolbar-filters">
                <div class="dt-filter-group">
                    <label class="dt-filter-label">Filter Date</label>
                    <input type="date" id="search_date" class="dt-more-input" value="<?= $search_date; ?>" style="min-width:160px;">
                </div>
            </div>
            
        </div>

        <!-- ── Alert Messages ── -->
        <?php if ($this->session->flashdata('success')) : ?>
            <div class="alert alert-success mx-4 mt-3 mb-0 border-0 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-check-circle "></i> <?= $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="reportTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th>DATE TIME</th>
                        <th>TYPE</th>
                        <th>FILE NAME</th>
                        <th>STATUS</th>
                        <th>REMARK</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = initServerDataTable('#reportTable', "<?= base_url('report/download') ?>", [
                {data: 'no', orderable: false, className: 'text-center'},
                {data: 'c_datetime', className: 'font-weight-bold text-dark'},
                {data: 'c_type', render: function(data) {
                    return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase" style="font-size:10px; letter-spacing:0.5px;">' + data + '</span>';
                }},
                {
                    data: 'c_filename',
                    className: 'text-nowrap',
                    render: function(data) {
                        var baseUrl = "<?= base_url() ?>";
                        return '<a href="' + baseUrl + 'report/download/export?filename=' + encodeURIComponent(data) + '" class="text-primary font-weight-bold">' + data + '</a>';
                    }
                },
                {data: 'c_status', render: function(data) {
                    var d = (data || '').toUpperCase();
                    var cls = 'secondary';
                    if (d === 'SUCCESS' || d === 'DONE' || d === 'COMPLETED') cls = 'success';
                    else if (d === 'FAILED' || d === 'ERROR') cls = 'danger';
                    else if (d === 'PENDING' || d === 'PROCESSING') cls = 'warning';
                    return '<span class="badge badge-' + cls + ' px-2 py-1">' + data + '</span>';
                }},
                {data: 'c_remark', className: 'small text-muted'}
            ], {
            "ajax": {
                "url": "<?= base_url('report/download') ?>",
                "type": "POST",
                "data": function(d) {
                    d.search_date = $('#search_date').val();
                    var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                    var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                    if (csrfName && csrfHash) d[csrfName] = csrfHash;
                }
            },
            "order": [[1, 'desc']]
        });

        // Global search
        // Global search with Debounce
        $('#reportGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // Date filter
        $('#search_date').on('change', function() {
            table.ajax.reload();
        });
    });
</script>



<script>
$(document).ready(function() {
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
