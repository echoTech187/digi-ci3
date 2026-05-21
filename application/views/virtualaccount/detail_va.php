<!-- Begin Page Content -->
<div >
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> VA Transaction Details Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Detailed technical trace and status data of a specific Virtual Account transaction.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-terminal text-primary mr-2"></i> Gateway Payload</div>
                <p class="drawer-card-text">Check exact JSON callback payloads received from payment partners.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-history text-primary mr-2"></i> Status Log</div>
                <p class="drawer-card-text">Review transition timeline from created to paid.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-percent text-primary mr-2"></i> Fee Splitting</div>
                <p class="drawer-card-text">View the breakdown of merchant share vs admin fee share for the payment.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="dt-page-title mb-1">Transaction Details</h4>
            <p class="dt-page-subtitle mb-0">Granular technical and financial data for the Virtual Account payment transaction.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary  border-0 py-3 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-star"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title">VIRTUAL ACCOUNT TRANSACTION INFO</h6>
            <small class="mh-subtitle">Lihat detail informasi lengkap</small>
        </div>
    </div>
    
</div>
        <div class="card-body">
            <!-- <?php
                // echo "<pre>";
                // print_r($va_data); // or var_dump($data);
                // echo "</pre>";
            ?>         -->

            <div class="row">
                <?php foreach ($va_data as $data): ?>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="dt-more-label text-primary mb-1">REFERENCE INFO</label>
                            <div class="p-3 rounded-lg border-left-primary">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Invoice Number</small>
                                    <span class="font-weight-bold text-dark"><?= $data['c_invoiceNo']; ?></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Merchant Transaction ID</small>
                                    <code class="text-primary"><?= $data['Merchant_Transaction_Id']; ?></code>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Request Timestamp</small>
                                    <span class="text-dark"><?= $data['c_datetime']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-info mb-1">MERCHANT & SUB-MERCHANT</label>
                            <div class="p-3 rounded-lg border-left-info">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Merchant</small>
                                        <span class="font-weight-bold text-dark"><?= $data['name_merchant']; ?></span>
                                        <code class="small text-muted">[<?= $data['id_merchant']; ?>]</code>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Sub-Merchant</small>
                                        <span class="font-weight-bold text-dark"><?= $data['name_submerchant']; ?></span>
                                        <code class="small text-muted">[<?= $data['id_submerchant']; ?>]</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="dt-more-label text-success mb-1">FINANCIAL & VA DETAILS</label>
                            <div class="p-3 rounded-lg border-left-success">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">VA Number</small>
                                        <span class="font-weight-bold text-primary" style="font-size: 1.1rem;"><?= $data['c_vaNumber']; ?></span>
                                    </div>
                                    <div class="col-6 mb-2 text-right">
                                        <small class="text-muted d-block">Tx Type</small>
                                        <span class="badge badge-info-soft text-info px-2"><?= strtoupper($data['c_type']); ?></span>
                                    </div>
                                    <div class="col-6 mt-2 pt-2 border-top">
                                        <small class="text-muted d-block">Transaction Amount</small>
                                        <span class="font-weight-bold text-dark">Rp <?= number_format($data['c_amount'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-6 mt-2 pt-2 border-top text-right">
                                        <small class="text-muted d-block">Platform Fee</small>
                                        <span class="text-danger">Rp <?= number_format($data['c_fee'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-warning mb-1">PAYMENT & SETTLEMENT</label>
                            <div class="p-3 rounded-lg border-left-warning">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Payment Date</small>
                                        <span class="text-dark"><?= $data['c_datetimePayment'] ?: '-'; ?></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Settlement Status</small>
                                        <span class="text-dark">
                                            <?= ($data['c_isSettlementRealtime'] == 1) ? '<span class="badge badge-success-soft text-success">REALTIME</span>' : ($data['c_datetimeSettlement'] ?: '-'); ?>
                                        </span>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <small class="text-muted d-block">Channel ID</small>
                                        <span class="badge badge-dark px-2"><?= $data['ref_cashinChannelId']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-0">
                            <label class="dt-more-label text-muted mb-1 uppercase small">External Provider Data</label>
                            <div class="p-3 bg-white border rounded-lg">
                                <div class="row">
                                    <div class="col-md-2 mb-2 mb-md-0 border-right">
                                        <small class="text-muted d-block">External ID</small>
                                        <span class="text-dark font-weight-bold"><?= $data['ref_cashinExternalId']; ?></span>
                                    </div>
                                    <div class="col-md-2 mb-2 mb-md-0 border-right">
                                        <small class="text-muted d-block">Ext. Settlement</small>
                                        <span class="text-dark"><?= ($data['c_isSettlementRealtimeExternal'] == 1) ? 'Realtime' : ($data['c_datetimeSettlementExternal'] ?: '-'); ?></span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <small class="text-muted d-block">Ext. Fee</small>
                                        <span class="text-danger">Rp <?= number_format($data['c_feeExternal'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>

</div>
<!-- /.container-fluid -->




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
