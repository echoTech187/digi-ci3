<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Transaction Details</h4>
            <p class="dt-page-subtitle">Complete data breakdown for the E-Wallet transaction, including merchant and settlement info.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <div class="modal-header modal-header-primary border-0 py-3">
            <h6 class="modal-title font-weight-bold text-white uppercase" style="letter-spacing:1px;">
                <i class="fas fa-wallet mr-2"></i> E-WALLET TRANSACTION INFO
            </h6>
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
                            <label class="dt-more-label text-primary mb-1">REFERENCE INFO</label>
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
                            <label class="dt-more-label text-info mb-1">MERCHANT & SUB-MERCHANT</label>
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
                            <label class="dt-more-label text-success mb-1">FINANCIAL DETAILS</label>
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
                            <label class="dt-more-label text-warning mb-1">PAYMENT & SETTLEMENT</label>
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