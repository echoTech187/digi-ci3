<!-- Add/Edit Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="feeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-up"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="feeModalTitle">Add Cashout Fee Setting</h6>
                        <small class="mh-subtitle" id="feeModalSubtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm" method="post" action="<?= base_url('merchant/setting-cashout-fee/create'); ?>">
                <input type="hidden" name="ref_merchantId" id="ref_merchantId" value="<?= $merchant_id ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0 bg-dark-subtle">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Configuration Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Configure payout channels, fee structures, and disbursement boundaries accurately.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-network-wired text-primary mr-2"></i> 1. Channel Selection</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Select a Channel Group and External ID Default to populate Specific Channel IDs.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-calculator text-primary mr-2"></i> 2. Fee Structure</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Choose Fixed, Percentage, or Both for disbursement fee deductions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-network-wired mr-2"></i> CHANNEL CONFIG
                                        </h6>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Channel Group</label>
                                            <select class="form-control border-1 select2" id="c_cashoutChannelGroup" required name="c_cashoutChannelGroup">
                                                <option value="" selected disabled>Select group</option>
                                                <?php foreach ($channel_groups as $chg): ?>
                                                    <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">External ID Default</label>
                                            <select class="form-control border-1 select2" id="c_externalIdDefault" required name="c_externalIdDefault">
                                                <option value="" selected disabled>Select external ID</option>
                                                <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                                    <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted">Specific Channel ID</label>
                                            <select class="form-control border-1 select2" id="ref_cashoutChannelId" required name="ref_cashoutChannelId" disabled>
                                                <option value="" disabled selected>Select channel ID</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-calculator mr-2"></i> FEE STRUCTURE
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Fee Type</label>
                                                <select class="form-control border-1 select2" id="c_feeType" required name="c_feeType">
                                                    <option value="Fixed">Fixed</option>
                                                    <option value="Percetange">Percentage</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Settlement (Days)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control border-1 rounded-right-0 input-rupiah" id="c_settlementInterval" required name="c_settlementInterval">
                                                    <span class="input-group-text border-1 small rounded-left-0">Days</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted">Fixed Fee (IDR)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text border-1 small">Rp</span>
                                                    <input type="text" class="input-rupiah form-control border-1 rounded-left-0 fw-bold text-primary" required id="c_fee" name="c_fee">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted">Percentage Fee (%)</label>
                                                <div class="input-group">
                                                    <input type="text" class="input-percentage form-control border-1 rounded-right-0 fw-bold text-success" required id="c_feePercetange" name="c_feePercetange">
                                                    <span class="input-group-text border-1 rounded-left-0 small">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-none bg-white p-0 rounded-4 mt-4">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> LIMITS & STATUS
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Min</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1 rounded-left-0" id="c_amountMin" required name="c_amountMin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Max</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1 rounded-left-0" id="c_amountMax" required name="c_amountMax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Status</label>
                                        <select class="form-control border-1 select2" id="c_status" required name="c_status">
                                            <option value="Active">Active</option>
                                            <option value="Not Active">Not Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">SAVE CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="bulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-arrow-circle-up"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="bulkModalLabel">Bulk Add Cashout Fees</h6>
                        <small class="mh-subtitle">Apply uniform fee rates across channel group</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('merchant/setting-cashout-fee/bulk-create/' . $merchant_id); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0 bg-dark-subtle">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-bullhorn fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-0" style="font-size: 15px;">Bulk Settings Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Apply uniform fee configurations across multiple channels simultaneously with duplicate protection.</p>
                            </div>
                        </div>
                        
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-success fw-bold mb-3"><i class="fas fa-bullseye mr-2"></i> TARGET GROUPS</h6>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Cashout Channel Group</label>
                                            <select class="form-control border-1 select2" id="bulk_c_cashoutChannelGroup" required name="c_cashoutChannelGroup">
                                                <option value="" selected disabled>Select group</option>
                                                <?php foreach ($channel_groups as $chg): ?>
                                                    <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted">External ID Default</label>
                                            <select class="form-control border-1 select2" id="bulk_c_externalIdDefault" required name="c_externalIdDefault">
                                                <option value="" selected disabled>Select external ID</option>
                                                <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                                    <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-none bg-white p-0 rounded-4">
                                        <h6 class="text-success fw-bold mb-3"><i class="fas fa-coins mr-2"></i> FEE SETTINGS</h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-muted">Fee Type</label>
                                                <select class="form-control border-1 select2" required name="c_feeType">
                                                    <option value="Fixed">Fixed</option>
                                                    <option value="Percetange">Percentage</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-muted">Fixed Fee</label>
                                                <div class="input-group">
                                                    <span class="input-group-text border-1 small">Rp</span>
                                                    <input type="text" class="input-rupiah form-control border-1" required name="c_fee">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold text-muted">Fee Percentage (%)</label>
                                                <div class="input-group">
                                                    <input type="text" class="input-percentage form-control border-1" required name="c_feePercetange">
                                                    <span class="input-group-text border-1 small">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-none bg-white p-0 rounded-4 mt-4">
                                <h6 class="text-success fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> LIMITS & STATUS
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Min</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1" required name="c_amountMin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Amount Max</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-1 small">Rp</span>
                                            <input type="text" class="input-rupiah form-control border-1" required name="c_amountMax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Status</label>
                                        <select class="form-control border-1 select2" required name="c_status">
                                            <option value="Active">Active</option>
                                            <option value="Not Active">Not Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">APPLY BULK CONFIGURATION</button>
                </div>
            </form>
        </div>
    </div>
</div>
