<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700">External Balance Log</h4>
            <p class="dt-page-subtitle text-muted">Daily snapshots and comparisons of balances across external providers.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> External Balance Log Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page provides daily snapshots comparing total ledger balances across external payment providers.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-history text-primary mr-2"></i> Daily Snapshots</div>
                <p class="drawer-card-text">Compare actual recorded totals between Gidi, Paylabs, GV, and Paydgn for date-based verification.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search-dollar text-primary mr-2"></i> Discrepancy Audits</div>
                <p class="drawer-card-text">Quickly inspect any balance gaps to perform manual matching or trigger re-syncing actions if necessary.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sort-amount-down text-primary mr-2"></i> Default Ordering</div>
                <p class="drawer-card-text">By default, log records are listed in descending order starting from the most recent snapshot date.</p>
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

        <div class="px-4 pb-4">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= $this->session->flashdata('error'); ?>
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="balanceTable" class="table table-hover dt-standard" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Snapshot Date</th>
                            <th class="text-right">Gidi</th>
                            <th class="text-right">Paylabs</th>
                            <th class="text-right">GV</th>
                            <th class="text-right">Paydgn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($balance_external_logs as $index => $log): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $index + 1 ?></td>
                                <td class="font-weight-bold text-dark"><?= $log->c_datetimeCreated; ?></td>
                                <td class="text-right font-weight-bold text-primary">
                                     <?= $log->gidi !== null ? 'Rp ' . number_format((float)$log->gidi, 2, ',', '.') : '-'; ?>
                                </td>
                                <td class="text-right">
                                    <?= $log->paylabs !== null ? 'Rp ' . number_format((float)$log->paylabs, 2, ',', '.') : '-'; ?>
                                </td>
                                <td class="text-right text-success">
                                    <?= $log->gv !== null ? 'Rp ' . number_format((float)$log->gv, 2, ',', '.') : '-'; ?>
                                </td>
                                <td class="text-right text-info">
                                    <?= $log->paydgn !== null ? 'Rp ' . number_format((float)$log->paydgn, 2, ',', '.') : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Instructions Guide drawer handlers
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer').addClass('open');
        $('#instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden'); // Lock background scroll
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer').removeClass('open');
        $('#instructionOverlay').removeClass('open');
        $('body').css('overflow', ''); // Unlock scroll
    });

    const table = $('#balanceTable').DataTable({
        dom: 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
        pageLength: 25,
        order: [[1, 'desc']],
        columnDefs: [
            {
                targets: [2, 3, 4, 5],
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract number from Rp currency string for sorting
                        return parseFloat(data.replace(/[^0-9,-]+/g,"").replace(',', '.')) || 0;
                    }
                    return data;
                }
            }
        ],
        language: {
            "info": "Showing _START_ – _END_ of _TOTAL_ logs",
            "infoEmpty": "No logs recorded",
            "zeroRecords": '<div class="text-center py-5 text-muted"><i class="fas fa-history fa-3x mb-3 opacity-25"></i><br>No matching logs found</div>'
        },
        drawCallback: function(settings) {
            var api = this.api();
            var info = api.page.info();
            var $pager = $(api.table().container()).find('.dt-footer-pager');
            var currPage = info.page + 1;
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

            $pager.find('.dt-prev-btn').click(function() { if (info.page > 0) api.page('previous').draw('page'); });
            $pager.find('.dt-next-btn').click(function() { if (info.page < info.pages - 1) api.page('next').draw('page'); });
        }
    });

    $('#dt-global-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));
});
</script>
