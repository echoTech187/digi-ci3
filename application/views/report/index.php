<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Download and manage generated transaction report files.</p>
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
                <a href="<?= base_url('admin/resetdownload'); ?>" class="btn-dt-chip-action btn-dt-action-primary border-0">
                    <i class="fas fa-undo mr-1 mr-2"></i> Reset Filter
                </a>
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
        var table = initServerDataTable('#reportTable', "<?= base_url('admin/report') ?>", [
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
                        return '<a href="' + baseUrl + 'admin/download?filename=' + encodeURIComponent(data) + '" class="text-primary font-weight-bold">' + data + '</a>';
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
                "url": "<?= base_url('admin/report') ?>",
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


