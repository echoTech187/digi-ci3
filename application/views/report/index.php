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
        <form id="reportDownloadForm" method="post" action="<?= base_url('report/download'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar py-3 px-4">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="reportGlobalSearch" class="dt-search-input" placeholder="Search by filename, remark...">
                </div>

                <!-- RIGHT: Filter -->
                <div class="dt-toolbar-filters">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <?php 
                        $extra_active = 0;
                        if ($search_type) $extra_active++;
                        if ($search_date || $search_date_to) $extra_active++;
                        if ($search_status) $extra_active++;
                        ?>
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" class="btn-dt-chip-action dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>" id="dt-more-filters-btn">
                            <i class="fas fa-sliders-h"></i> <span class="d-none d-md-block">Filter</span>
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>
                        
                        <!-- The Slide-Down Panel -->
                        <div class="dt-more-panel" id="dt-more-filters-panel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advance Filter</span>
                                <a href="<?= base_url('report/download/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Type -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-tags mr-1 mr-2"></i> Type</label>
                                    <select name="search_type" class="dt-more-select select2-report text-primary-custom fw-bold">
                                        <option value="">All Types</option>
                                        <option value="BI FAST" <?= ($search_type === 'BI FAST') ? 'selected' : ''; ?>>BI FAST</option>
                                        <option value="QRIS" <?= ($search_type === 'QRIS') ? 'selected' : ''; ?>>QRIS</option>
                                        <option value="PPOB" <?= ($search_type === 'PPOB') ? 'selected' : ''; ?>>PPOB</option>
                                        <option value="EWALLET" <?= ($search_type === 'EWALLET') ? 'selected' : ''; ?>>EWALLET</option>
                                        <option value="Virtual Account" <?= ($search_type === 'Virtual Account') ? 'selected' : ''; ?>>VIRTUAL ACCOUNT</option>
                                        <option value="Mutation" <?= ($search_type === 'Mutation') ? 'selected' : ''; ?>>MUTATION</option>
                                    </select>
                                </div>
                                <!-- Primary: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                    <div class="premium-picker">
                                        <input type="date" name="search_date" class="dt-chip-input" value="<?= $search_date; ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_to" class="dt-chip-input" value="<?= $search_date_to; ?>" title="Date To">
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                    <select name="search_status" class="dt-more-select select2-report text-primary-custom fw-bold">
                                        <option value="">All Status</option>
                                        <option value="Success" <?= ($search_status === 'Success') ? 'selected' : ''; ?>>Success</option>
                                        <option value="Pending" <?= ($search_status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Failed" <?= ($search_status === 'Failed') ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="dt-more-panel-footer">
                                <button type="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" class="btn-dt-cancel btn-dt-secondary" id="dt-panel-close">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
            "order": [[1, 'desc']]
        });

        // Global search with Debounce
        $('#reportGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // ── More Filters dropdown ──
        var $moreBtn   = $('#dt-more-filters-btn');
        var $morePanel = $('#dt-more-filters-panel');
        var $moreClose = $('#dt-panel-close');

        $moreBtn.on('click', function(e) {
            e.stopPropagation();
            var isOpen = $morePanel.hasClass('dt-panel-open');
            $morePanel.toggleClass('dt-panel-open', !isOpen);
            $moreBtn.toggleClass('dt-open', !isOpen);
        });

        $moreClose.on('click', function() {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
                $morePanel.removeClass('dt-panel-open');
                $moreBtn.removeClass('dt-open');
            }
        });

        // Select2 inside panel
        $('.select2-report').select2({
            width: '100%',
            dropdownAutoWidth: true,
            minimumResultsForSearch: -1
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
