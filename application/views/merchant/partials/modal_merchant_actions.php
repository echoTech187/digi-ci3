<!-- Modal: Credit Balance -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="creditBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="creditBalanceModalLabel">Credit Merchant Balance</h6>
                        <small class="mh-subtitle">Process and top-up merchant credit balance</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                    <!-- Left Column: Instructions Guide -->
                    <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px;">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size: 14px;">Credit Guide</h6>
                            </div>
                            <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for manual balance top-ups:</p>
                            
                            <div class="d-flex flex-column gap-3">
                                <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                    <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-check-circle text-success mr-2"></i> 1. Accurate Funding</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Select the appropriate Cash-In Channel to ensure the ledger aligns with bank mutations.</p>
                                </div>
                                <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                    <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-file-invoice text-info mr-2"></i> 2. Precise Auditing</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Manual transactions require clear descriptions. Explain why you are crediting the merchant's balance.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: The Form -->
                    <div class="col-lg-7 p-4 bg-light mb-0">
                        <form id="creditBalanceForm">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                <input type="text" class="form-control border-0 py-2 bg-dark text-white fw-bold" readonly required id="merchantName" style="border-color: rgba(255,255,255,0.1);">
                                <input type="hidden" id="merchantId" name="merchantId">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Channel ID</label>
                                <select id="creditChannelId" name="channelId" class="form-select border-1 py-1" style="font-size: 13px;" required>
                                    <option value="">Select Channel</option>
                                    <?php foreach ($cashin_channels as $cashin_channel): ?>
                                        <option value="<?php echo $cashin_channel->id; ?>"><?php echo $cashin_channel->id; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Description</label>
                                <input type="text" class="form-control border-1 py-2" id="creditDescription" name="description" placeholder="e.g. Manual top-up">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Amount (IDR)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1">Rp</span>
                                    <input type="text" class="form-control border-1 py-2 fw-bold text-success" id="amountCredit" name="amount" oninput="formatNumber(this)" placeholder="0" required>
                                </div>
                                <input type="hidden" id="rawAmountCredit" name="rawAmountCredit">
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                <button type="submit" id="btnConfirmCredit" class="btn-dt-apply px-4 no-loader">
                                    <i class="fas fa-check mr-2"></i> CONFIRM CREDIT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Debit Balance -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="debitBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="debitBalanceModalLabel">Debit Merchant Balance</h6>
                        <small class="mh-subtitle">Process and modify merchant debit balance</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                    <!-- Left Column: Instructions Guide -->
                    <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px; background-color: rgba(220, 53, 69, 0.2) !important;">
                                    <i class="fas fa-book-open text-danger"></i>
                                </div>
                                <h6 class="fw-bold text-danger mb-0" style="font-size: 14px;">Debit Guide</h6>
                            </div>
                            <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for manual balance deductions:</p>
                            
                            <div class="d-flex flex-column gap-3">
                                <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                    <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-info-circle text-warning mr-2"></i> 1. Balance Availability</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Ensure the merchant's available balance is sufficient to cover the debit adjustment.</p>
                                </div>
                                <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                    <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-file-contract text-info mr-2"></i> 2. Audit Trail</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Enter a detailed explanation for the balance deduction.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: The Form -->
                    <div class="col-lg-7 p-4 bg-light mb-0">
                        <form id="debitBalanceForm">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Merchant Name</label>
                                <input type="text" class="form-control border-0 py-2 bg-dark text-white fw-bold" required readonly id="merchantNameDebit" style="border-color: rgba(255,255,255,0.1);">
                                <input type="hidden" id="merchantIdDebit" name="merchantIdDebit">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Channel ID</label>
                                <select id="debitChannelId" name="channelId" class="form-select border-1 py-1" style="font-size: 13px;" required>
                                    <option value="">Select Channel</option>
                                    <?php foreach ($cashout_channels as $cashout_channel): ?>
                                        <option value="<?php echo $cashout_channel->id; ?>"><?php echo $cashout_channel->id; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Description</label>
                                <input type="text" class="form-control border-1 py-2" id="debitDescription" name="description" placeholder="e.g. Administrative deduction">
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Amount (IDR)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-1">Rp</span>
                                    <input type="text" class="form-control border-1 py-2 fw-bold text-danger" id="amountDebit" name="amount" oninput="formatNumber(this)" placeholder="0" required>
                                </div>
                                <input type="hidden" id="rawAmountDebit" name="rawAmountDebit">
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                <button type="submit" id="btnConfirmDebit" class="btn-dt-apply px-4 no-loader">
                                    <i class="fas fa-check mr-2"></i> CONFIRM DEBIT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delegate Access -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="delegateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="delegateModalLabel">Delegate Permission Ceiling</h6>
                        <small class="mh-subtitle">Manage maximum hierarchy permissions</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 bg-light">
                <form id="delegateForm" class="mb-0 w-100">
                    <input type="hidden" id="delegateMerchantId" name="merchantId">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-5 p-4 d-flex flex-column justify-content-between mb-0">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 14px;">Delegation Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Guide for managing merchant permission ceilings:</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-shield-alt text-warning mr-2"></i> 1. Permission Ceiling</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">You can only delegate permissions that you personally possess.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-sitemap text-info mr-2"></i> 2. Inheritance &amp; Flow</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Granting a permission allows the merchant to use and further delegate.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 p-4 bg-light mb-0">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <div class="avatar-sm bg-warning-soft text-warning rounded-circle p-2 me-3" style="background-color: rgba(255, 193, 7, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-store text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" id="delegateMerchantName">Merchant Name</h6>
                                    <small class="text-muted">Setting maximum permissions for this merchant and its hierarchy.</small>
                                </div>
                            </div>

                            <div id="permissionsList" style="max-height: 400px; overflow-y: auto;" class="p-2 border rounded bg-white">
                                <div class="text-center py-5" id="permissionsLoader">
                                    <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>
                                    <p class="mt-2 text-muted">Fetching permissions...</p>
                                </div>
                            </div>

                            <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-end">
                                <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn-dt-apply px-4 no-loader" id="btnSaveDelegation">
                                    <i class="fas fa-save mr-2"></i> SAVE CHANGES
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
