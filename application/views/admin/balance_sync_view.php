<!-- Begin Page Content -->
<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Balance Synchronization Report</h4>
            <p class="dt-page-subtitle">Comparing actual ledger balances against system-recorded totals for audit consistency.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.location.reload()" class="btn-dt-action btn-dt-secondary">
                <i class="fas fa-sync mr-1"></i> Re-Sync
            </button>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table dt-table mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th>MERCHANT INFO</th>
                            <th class="text-right">TOTAL (ACTUAL)</th>
                            <th class="text-right">TOTAL (SYSTEM)</th>
                            <th class="text-right">HOLD (ACTUAL)</th>
                            <th class="text-right">HOLD (SYSTEM)</th>
                            <th class="text-right">AVAILABLE (ACTUAL)</th>
                            <th class="text-right">AVAILABLE (SYSTEM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sync_results as $row): ?>
                            <?php 
                                $rowClass = '';
                                $statusIcon = '';
                                if ($row['balance_system'] != $row['balance_actual'] && $row['hold_system'] == $row['hold_actual']) {
                                    $rowClass = 'table-danger';
                                    $statusIcon = '<i class="fas fa-exclamation-triangle text-danger mr-1"></i>';
                                } elseif ($row['balance_system'] != $row['balance_actual'] || $row['hold_system'] != $row['hold_actual']) {
                                    $rowClass = 'table-warning';
                                    $statusIcon = '<i class="fas fa-exclamation-circle text-warning mr-1"></i>';
                                }
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="text-center"><?= $row['no'] ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-dark"><?= htmlspecialchars($row['name']) ?></span>
                                        <code class="small text-primary"><?= $row['id'] ?></code>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($row['balance_actual']) ?></td>
                                <td class="text-right">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="font-weight-bold <?= $row['balance_system'] != $row['balance_actual'] ? 'text-danger' : 'text-dark' ?>">
                                            <?= $statusIcon ?> Rp <?= number_format($row['balance_system']) ?>
                                        </span>
                                        <?php if ($row['updated_total']): ?>
                                            <span class="badge badge-success mt-1" style="font-size: 10px;">SYNCED</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($row['hold_actual']) ?></td>
                                <td class="text-right">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="font-weight-bold <?= $row['hold_system'] != $row['hold_actual'] ? 'text-danger' : 'text-dark' ?>">
                                            Rp <?= number_format($row['hold_system']) ?>
                                        </span>
                                        <?php if ($row['updated_hold']): ?>
                                            <span class="badge badge-success mt-1" style="font-size: 10px;">SYNCED</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <?php 
                                    $availableActual = $row['balance_actual'] - $row['hold_actual'];
                                    $availableSystem = $row['balance_system'] - $row['hold_system'];
                                ?>
                                <td class="text-right font-weight-bold text-primary">Rp <?= number_format($availableActual) ?></td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($availableSystem) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>