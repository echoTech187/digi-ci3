<!-- Begin Page Content -->
<div >
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> QRIS Transaction Details Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Granular technical details of a QRIS dynamic transaction.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-store text-primary mr-2"></i> Acquirer Info</div>
                <p class="drawer-card-text">Bank ID, Merchant ID (MID), and Terminal ID (TID) from the QRIS acquirer.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-qrcode text-primary mr-2"></i> Image & Code</div>
                <p class="drawer-card-text">Review raw string data used to build the QR code image payload.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-stamp text-primary mr-2"></i> Settlement Stamp</div>
                <p class="drawer-card-text">Check settlement timings and transaction fee deductions.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="dt-page-title mb-1">Transaction Details</h4>
            <p class="dt-page-subtitle mb-0">Granular technical and financial data for the QRIS payment transaction.</p>
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
                    <h6 class="mh-title">QRIS TRANSACTION INFO</h6>
                    <small class="mh-subtitle">Lihat detail informasi lengkap</small>
                </div>
            </div>
            
        </div>
        <div class="card-body">
            <!-- <?php
                // echo "<pre>";
                // print_r($qris_data); // or var_dump($data);
                // echo "</pre>";
            ?>         -->

            <div class="row">
                <?php foreach ($qris_data as $data): ?>
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
                                    <small class="text-muted d-block">Request Timestamp</small>
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
                                        <small class="text-muted d-block">MDR (Merchant)</small>
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
                                        <span class="text-dark"><?= $data['c_datetimePayment'] ?: '-'; ?></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Settlement Status</small>
                                        <span class="text-dark">
                                            <?= ($data['c_isSettlementRealtime'] == 1) ? '<span class="badge badge-success-soft text-success">REALTIME</span>' : ($data['c_datetimeSettlement'] ?: '-'); ?>
                                        </span>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <small class="text-muted d-block">Channel Type</small>
                                        <span class="badge badge-info-soft text-info px-2"><?= strtoupper($data['c_type']); ?></span>
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
                                    <div class="col-md-3 mb-2 mb-md-0 border-right">
                                        <small class="text-muted d-block">Issuer RRN</small>
                                        <span class="text-dark font-weight-bold"><?= $data['c_issuerRrn'] ?: '-'; ?></span>
                                    </div>
                                    <div class="col-md-3 mb-2 mb-md-0 border-right">
                                        <small class="text-muted d-block">Ext. Settlement</small>
                                        <span class="text-dark"><?= ($data['c_isSettlementRealtimeExternal'] == 1) ? 'Realtime' : ($data['c_datetimeSettlementExternal'] ?: '-'); ?></span>
                                    </div>
                                    <div class="col-md-2 mb-2 mb-md-0 border-right text-right">
                                        <small class="text-muted d-block">Ext. MDR</small>
                                        <span class="text-danger">Rp <?= number_format($data['c_mdrExternal'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <small class="text-muted d-block">Ext. Fee</small>
                                        <span class="text-danger">Rp <?= number_format($data['c_feeExternal'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <?php if (!empty($create_log)): ?>
                    <div class="col-12 mt-4">
                        <div class="mb-0">
                            <div class="d-flex align-items-start p-3 w-100 mb-3" style="background:rgba(78,115,223,0.06);border:1px solid rgba(78,115,223,0.12);border-radius:12px;">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;"><i class="fas fa-qrcode" style="font-size:13px;"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="font-size:12px;color:var(--text-dark);">QRIS Generation Log (Create)</h6>
                                    <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">Inspect QRIS dynamic payment details including QR generation data, merchant info, settlement routing, and external channel responses.</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="mb-3">
                                    <div class="small text-uppercase font-weight-bold text-muted mb-1">Provider</div>
                                    <div class="h6 font-weight-bold text-light mb-0 text-uppercase"><?= $data['ref_cashinExternalId']; ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 1</div>
                                        <div class="d-flex align-items-start">
                                            <div class="h6 font-weight-bold mb-0 text-break mr-2" style="word-break: break-all;"><?= isset($create_log['TransactionIdExternal1']) && $create_log['TransactionIdExternal1'] ? $create_log['TransactionIdExternal1'] : '-'; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 2</div>
                                        <div class="d-flex align-items-start">
                                            <div class="h6 font-weight-bold mb-0 text-break mr-2" style="word-break: break-all;"><?= isset($create_log['TransactionIdExternal2']) && $create_log['TransactionIdExternal2'] ? $create_log['TransactionIdExternal2'] : '-'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-pills mb-3" id="createLogTab" role="tablist" style="gap: 10px;">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-pill px-4 font-weight-bold" id="create-request-tab" data-toggle="pill" href="#create-request" style="font-size: 11px; border: 1px solid #4e73df;">REQUEST</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-pill px-4 font-weight-bold text-muted" id="create-response-tab" data-toggle="pill" href="#create-response" style="font-size: 11px; border: 1px solid #ddd;">RESPONSE</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="createLogTabContent">
                                <div class="tab-pane fade show active" id="create-request">
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-primary mb-1">Request Datetime</label>
                                            <div class="text-dark small"><?= $create_log['RequestDatetime'] ?? '-'; ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                            <?php $reqH = is_array($create_log['RequestHeader']) ? json_encode($create_log['RequestHeader'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $create_log['RequestHeader']; ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($reqH ?: ''); ?></pre>
                                        </div>
                                        <div>
                                            <label class="small font-weight-bold text-primary mb-1">Payload</label>
                                            <?php $reqB = is_array($create_log['RequestBody']) ? json_encode($create_log['RequestBody'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $create_log['RequestBody']; ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 250px; overflow-y: auto;"><?= htmlspecialchars($reqB ?: ''); ?></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="create-response">
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-primary mb-1">Response Datetime</label>
                                            <div class="text-dark small"><?= $create_log['ResponseDatetime'] ?? '-'; ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                            <?php $resH = is_array($create_log['ResponseHeader']) ? json_encode($create_log['ResponseHeader'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $create_log['ResponseHeader']; ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($resH ?: ''); ?></pre>
                                        </div>
                                        <div>
                                            <label class="small font-weight-bold text-primary mb-1">Body</label>
                                            <?php $resB = is_array($create_log['ResponseBody']) ? json_encode($create_log['ResponseBody'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $create_log['ResponseBody']; ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 250px; overflow-y: auto;"><?= htmlspecialchars($resB ?: ''); ?></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($external_log)): ?>
                    <div class="col-12 mt-4">
                        <div class="mb-0">
                            <div class="d-flex align-items-start p-3 w-100 mb-3" style="background:rgba(231,74,59,0.06);border:1px solid rgba(231,74,59,0.12);border-radius:12px;">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;"><i class="fas fa-satellite-dish" style="font-size:13px;"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="font-size:12px;color:var(--text-dark);">External Webhook / Payment Callback Log</h6>
                                    <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">Inspect raw webhook payloads received from the external provider when this payment was settled.</p>
                                </div>
                            </div>

                            <ul class="nav nav-pills mb-3" id="extLogTab" role="tablist" style="gap: 10px;">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-pill px-4 font-weight-bold" id="ext-request-tab" data-toggle="pill" href="#ext-request" style="font-size: 11px; border: 1px solid #e74a3b;">REQUEST (INCOMING)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-pill px-4 font-weight-bold text-muted" id="ext-response-tab" data-toggle="pill" href="#ext-response" style="font-size: 11px; border: 1px solid #ddd;">RESPONSE (OUTGOING)</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="extLogTabContent">
                                <div class="tab-pane fade show active" id="ext-request">
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-danger mb-1">Request Datetime</label>
                                            <div class="text-dark small"><?= $external_log['c_datetimeRequest'] ?? '-'; ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-danger mb-1">Headers</label>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($external_log['c_requestHeader'] ?? ''); ?></pre>
                                        </div>
                                        <div>
                                            <label class="small font-weight-bold text-danger mb-1">Payload Body</label>
                                            <?php
                                                $reqBody = $external_log['c_requestBody'] ?? '';
                                                $reqJson = json_decode($reqBody, true);
                                                $reqFormatted = $reqJson ? json_encode($reqJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $reqBody;
                                            ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 250px; overflow-y: auto;"><?= htmlspecialchars($reqFormatted); ?></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="ext-response">
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-danger mb-1">Response Datetime</label>
                                            <div class="text-dark small"><?= $external_log['c_datetimeResponse'] ?? '-'; ?></div>
                                        </div>
                                        <?php if (isset($external_log['c_responseHeader'])): ?>
                                        <div class="mb-3">
                                            <label class="small font-weight-bold text-danger mb-1">Headers</label>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($external_log['c_responseHeader'] ?? ''); ?></pre>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <label class="small font-weight-bold text-danger mb-1">Response Body</label>
                                            <?php
                                                $resBody = $external_log['c_responseBody'] ?? '';
                                                $resJson = json_decode($resBody, true);
                                                $resFormatted = $resJson ? json_encode($resJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $resBody;
                                            ?>
                                            <pre class="p-3 rounded small border-0 mb-0" style="max-height: 250px; overflow-y: auto;"><?= htmlspecialchars($resFormatted); ?></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

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
