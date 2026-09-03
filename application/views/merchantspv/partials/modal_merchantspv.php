<!-- Modal: Add Merchant SPV -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addMerchantSpv" tabindex="-1" role="dialog" aria-labelledby="addMerchantSpvLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addMerchantSpvLabel">Register Merchant Supervisor</h6>
                        <small class="mh-subtitle">Create supervisor profile and assign merchants</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('merchant/supervisor/register'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light text-dark">
                    <div class="d-flex g-0 w-100 flex-wrap flex-lg-nowrap">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Supervisor Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5; color: #a0a5b0 !important;">Register and configure a merchant supervisor profile accurately.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-user text-primary mr-2"></i> 1. Account Profile</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Enter the full name, username, and email for this supervisor.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-link text-primary mr-2"></i> 2. Merchant Linkage</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Select one or more merchants from the searchable list.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">SPV Name <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required name="c_name" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required name="c_username" placeholder="username123">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="dt-more-input" required name="c_email" placeholder="email@example.com">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="dt-more-input" required name="c_password" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="dt-more-input" required name="c_confirmPassword" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Assigned Merchants</label>
                                <select class="form-control select2-merchant" id="c_merchant_spv" name="c_merchant_spv[]" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2 d-block">Account Status <span class="text-danger">*</span></label>
                                <div class="d-flex" style="gap:20px;">
                                    <?php 
                                        $statuses = ['Active', 'Pending', 'Blocked', 'Freeze'];
                                        foreach($statuses as $st):
                                    ?>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="c_status" value="<?= $st ?>" id="status_<?= $st ?>" <?= $st == 'Active' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" style="font-size:13px; font-weight:500;" for="status_<?= $st ?>"><?= $st ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">
                        <i class="fas fa-save mr-2"></i> Register Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Merchant SPV -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="editMerchantSpv" tabindex="-1" role="dialog" aria-labelledby="editMerchantSpvLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header modal-header-primary border-0 mh-premium" style="background: linear-gradient(135deg, #f39c12 0%, #d35400 100%);">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editMerchantSpvLabel">Edit Merchant Supervisor</h6>
                        <small class="mh-subtitle">Modify supervisor profile and assign merchants</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMerchantSpvForm" method="post" action="">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light text-dark">
                    <div class="d-flex g-0 w-100 flex-wrap flex-lg-nowrap">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px; background-color: #d35400 !important;">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-warning mb-0" style="font-size: 15px; color: #f39c12 !important;">Update Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5; color: #a0a5b0 !important;">Modify the supervisor profile and reassign merchants.</p>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">SPV Name <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required id="edit_c_name" name="c_name" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required id="edit_c_username" name="c_username" placeholder="username123">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="dt-more-input" required id="edit_c_email" name="c_email" placeholder="email@example.com">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Password <span class="text-muted font-weight-normal">(Leave blank to keep current)</span></label>
                                    <input type="password" class="dt-more-input" id="edit_c_password" name="c_password" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Confirm Password</label>
                                    <input type="password" class="dt-more-input" id="edit_c_confirmPassword" name="c_confirmPassword" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Assigned Merchants</label>
                                <select class="form-control select2-merchant" id="edit_c_merchant_spv" name="c_merchant_spv[]" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2 d-block">Account Status <span class="text-danger">*</span></label>
                                <div class="d-flex" style="gap:20px;">
                                    <?php 
                                        $statuses = ['Active', 'Pending', 'Blocked', 'Freeze'];
                                        foreach($statuses as $st):
                                    ?>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="c_status" value="<?= $st ?>" id="edit_status_<?= $st ?>">
                                        <label class="custom-control-label" style="font-size:13px; font-weight:500;" for="edit_status_<?= $st ?>"><?= $st ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4" style="background: linear-gradient(135deg, #f39c12 0%, #d35400 100%); border-color: #d35400;">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
