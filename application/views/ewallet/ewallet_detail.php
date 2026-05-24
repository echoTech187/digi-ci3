<!-- Begin Page Content -->
<div >
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> E-Wallet Transaction Details Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Granular logs and metadata for an E-Wallet payment.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-mobile-alt text-primary mr-2"></i> Wallet Metadata</div>
                <p class="drawer-card-text">Mobile phone number used, provider reference ID, and product type.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-envelope-open-text text-primary mr-2"></i> Callback Responses</div>
                <p class="drawer-card-text">Success response payloads captured during transaction processing.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-history text-primary mr-2"></i> Audit Timeline</div>
                <p class="drawer-card-text">Retrace webhook triggers and transaction success milestones.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="dt-page-title mb-1">Transaction Details</h4>
            <p class="dt-page-subtitle mb-0">Complete data breakdown for the E-Wallet transaction, including merchant and settlement info.</p>
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
            <h6 class="mh-title">E-WALLET TRANSACTION INFO</h6>
            <small class="mh-subtitle">Lihat detail informasi lengkap</small>
        </div>
    </div>
    
</div>
        <div class="card-body">
            <!-- <?php
                // echo "<pre>";
                // print_r($ewallet_data); // or var_dump($data);
                // echo "</pre>";
            ?>         -->

            <div class="row">
                <?php foreach ($ewallet_data as $data): ?>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="dt-more-label text-primary mb-3">REFERENCE INFO</label>
                            <div class="p-3 rounded-lg border-left-primary">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Invoice Number</small>
                                    <span class="font-weight-bold text-dark"><?= $data['c_invoiceNo']; ?></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Merchant Transaction ID</small>
                                    <code class="text-primary"><?= $data['c_merchantTransactionId']; ?></code>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Transaction Timestamp</small>
                                    <span class="text-dark"><?= $data['c_datetime']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-info mb-3">MERCHANT & SUB-MERCHANT</label>
                            <div class="p-3 rounded-lg border-left-info">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Merchant</small>
                                        <span class="font-weight-bold text-dark"><?= $data['name_merchant']; ?></span>
                                        <code class="small text-muted">[<?= $data['ref_merchantId']; ?>]</code>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Sub-Merchant</small>
                                        <span class="font-weight-bold text-dark"><?= $data['name_submerchant']; ?></span>
                                        <code class="small text-muted">[<?= $data['ref_subMerchantId']; ?>]</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="dt-more-label text-success mb-3">FINANCIAL DETAILS</label>
                            <div class="p-3 rounded-lg border-left-success">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Transaction Amount</small>
                                        <span class="font-weight-bold text-dark">Rp <?= number_format($data['c_amount'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-6 mb-2 text-right">
                                        <small class="text-muted d-block">MDR (Fee)</small>
                                        <span class="text-danger"><?= $data['c_mdr']; ?></span>
                                    </div>
                                    <div class="col-12 mt-2 pt-2 border-top">
                                        <small class="text-muted d-block">Net Fee to Platform</small>
                                        <span class="font-weight-bold text-success">Rp <?= number_format($data['c_fee'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-warning mb-3">PAYMENT & SETTLEMENT</label>
                            <div class="p-3 rounded-lg border-left-warning">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Payment Date</small>
                                        <span class="text-dark"><?= $data['c_datetimePayment']; ?></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Settlement Date</small>
                                        <span class="text-dark">
                                            <?= ($data['c_isSettlementRealtime'] == 1) ? '<span class="badge badge-success-soft text-success">REALTIME</span>' : ($data['c_datetimeSettlement'] ?: '-'); ?>
                                        </span>
                                    </div>
                                    <div class="col-6 mt-1">
                                        <small class="text-muted d-block">Channel ID</small>
                                        <span class="badge badge-dark px-2"><?= $data['ref_cashinChannelId']; ?></span>
                                    </div>
                                    <div class="col-6 mt-1 text-right">
                                        <small class="text-muted d-block">Tx Type</small>
                                        <span class="badge badge-info-soft text-info px-2"><?= strtoupper($data['c_type']); ?></span>
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
