<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700">External Balance Log</h4>
            <p class="dt-page-subtitle text-muted">Daily snapshots and comparisons of balances across external providers.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
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
    </script>
