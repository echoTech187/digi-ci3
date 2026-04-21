<!-- Begin Page Content -->
<div >
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Transaction Details</h4>
            <p class="dt-page-subtitle">Detailed audit trail and technical parameters for the BI-FAST disbursement.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary  border-0 py-3 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title">BI-FAST DISBURSEMENT INFO</h6>
            <small class="mh-subtitle">Lihat detail informasi lengkap</small>
        </div>
    </div>
    
</div>
        <div class="card-body p-4">
            <?php
                // echo "<pre>";
                // print_r($bifast_data); // or var_dump($data);
                // echo "</pre>";
            ?>         
            <div class="row">
                <?php foreach ($bifast_data as $data): ?>
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
                                    <small class="text-muted d-block">Request Timestamp</small>
                                    <span class="text-dark"><?= $data['c_datetime']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-info mb-1">MERCHANT DETAILS</label>
                            <div class="p-3 rounded-lg border-left-info">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Merchant Name</small>
                                    <span class="font-weight-bold text-dark"><?= $data['name_merchant']; ?></span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Merchant ID</small>
                                    <code class="text-info"><?= $data['ref_merchantId']; ?></code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="dt-more-label text-success mb-1">FINANCIAL SUMMARY</label>
                            <div class="p-3 rounded-lg border-left-success">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Transaction Amount</small>
                                        <span class="font-weight-bold text-dark">Rp <?= number_format($data['c_amount'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-6 mb-2 text-right">
                                        <small class="text-muted d-block">Platform Fee</small>
                                        <span class="text-danger">Rp <?= number_format($data['c_fee'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Net Transfer</small>
                                        <span class="font-weight-bold text-success">Rp <?= number_format($data['c_amountTransfer'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted d-block">Total Debit</small>
                                        <span class="font-weight-bold text-dark">Rp <?= number_format($data['c_amountDebit'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top">
                                    <small class="text-muted d-block">Fee Method</small>
                                    <span class="badge badge-info-soft text-info px-2"><?= $data['c_methodFee']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="dt-more-label text-warning mb-1">BENEFICIARY & CHANNEL</label>
                            <div class="p-3 rounded-lg border-left-warning">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Account Holder</small>
                                    <span class="font-weight-bold text-dark"><?= $data['c_beneficiaryAccountName']; ?></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Account Number</small>
                                    <span class="text-dark"><?= $data['c_accountNo']; ?></span>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Channel ID</small>
                                        <span class="badge badge-dark px-2"><?= $data['ref_cashoutChannelId']; ?></span>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted d-block">Transfer Status</small>
                                        <?php 
                                            $status_cls = (strtolower($data['c_status']) == 'success') ? 'success' : 'warning';
                                        ?>
                                        <span class="badge badge-<?= $status_cls; ?> px-2"><?= strtoupper($data['c_status']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                         <div class="p-3 bg-white border rounded-lg">
                            <small class="dt-more-label text-muted d-block mb-1">Transfer Note / Remark</small>
                            <p class="mb-0 text-dark italic">"<?= $data['c_transferNote'] ?: 'No note provided'; ?>"</p>
                         </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>

</div>
<!-- /.container-fluid -->




