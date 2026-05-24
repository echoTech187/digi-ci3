<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Create and configure a new merchant account with granular permissions</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Merchant Registration Form</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <!-- Alerts Standardized to Swal2 Premium -->
                    <script>
                        $(document).ready(function() {
                            <?php if ($this->session->flashdata('success')) : ?>
                                Swal.fire({
                                    title: 'Success!',
                                    text: '<?= $this->session->flashdata('success'); ?>',
                                    icon: 'success',
                                    customClass: {
                                        popup: 'swal2-premium-popup',
                                        confirmButton: 'swal2-premium-confirm'
                                    },
                                    buttonsStyling: false
                                });
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')) : ?>
                                Swal.fire({
                                    title: 'Error!',
                                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                                    icon: 'error',
                                    customClass: {
                                        popup: 'swal2-premium-popup',
                                        confirmButton: 'swal2-premium-confirm'
                                    },
                                    buttonsStyling: false
                                });
                            <?php endif; ?>
                        });
                    </script>

                    <form class="ajax-form" action="<?= base_url('merchant/manage/register'); ?>" data-redirect="<?= base_url('merchant/manage'); ?>" method="post">
                        
                        <!-- Account Information -->
                        <div class="section-title mb-4 mt-0 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-user-circle mr-2"></i> Account Information
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Name</label>
                                <input type="text" name="c_name" class="form-control" placeholder="ABC Store" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Email</label>
                                <input type="email" name="c_email" class="form-control" placeholder="owner@abc.com" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Phone</label>
                                <input type="text" name="c_phoneNumber" class="input-phone form-control" placeholder="08123xxx">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">GVConnect Business ID</label>
                                <input type="text" name="c_gvconnectBusinessId" class="form-control" placeholder="24090200001">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Password</label>
                                <input type="password" name="c_password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Confirm Password</label>
                                <input type="password" name="c_confirmPassword" class="form-control" required>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- OpenAPI Configuration -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-key mr-2"></i> OpenAPI Configuration
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">Whitelist IP (semicolon separated)</label>
                            <input type="text" name="c_openapiIPAllow" class="form-control" placeholder="1.2.3.4; 5.6.7.8">
                            <small class="text-muted">Restrict API access to these IP addresses</small>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback QRIS MPM</label>
                                <input type="text" name="c_openapiUrlCallbackQrisMpm" class="form-control" placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback E-wallet</label>
                                <input type="text" name="c_openapiUrlCallbackEwallet" class="form-control" placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback VA</label>
                                <input type="text" name="c_openapiUrlCallbackVa" class="form-control" placeholder="https://api.your.com/callback">
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- Service Permissions -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-shield-alt mr-2"></i> Service Permissions
                        </div>
                        <div class="row pt-2">
                            <div class="col-md-4 border-right">
                                <div class="d-flex align-items-center gap-1 mb-3 h6">
                                    <div class="bg-primary-soft text-primary rounded-pill px-2 py-1 mr-2" style="background-color: rgba(13, 110, 253, 0.1); font-size: 10px;">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <span class="fw-bold small text-dark mt-1 text-uppercase">Virtual Account</span>
                                </div>
                                <?php
                                $va_checkboxes = [
                                    'c_openapiChannelVaDynamicCreate' => 'VA Dynamic Create',
                                    'c_openapiChannelVaDynamicQuery' => 'VA Dynamic Query',
                                    'c_openapiChannelVaDynamicCancel' => 'VA Dynamic Cancel',
                                    'c_openapiChannelVaRecurringCreate' => 'VA Recurring Create',
                                    'c_openapiChannelVaRecurringCancel' => 'VA Recurring Cancel'
                                ];
                                foreach ($va_checkboxes as $key => $label) {
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
                                }
                                ?>
                            </div>
                            <div class="col-md-4 border-right">
                                <div class="d-flex align-items-center gap-1 mb-3 h6">
                                    <div class="bg-success-soft text-success rounded-pill px-2 py-1 mr-2" style="background-color: rgba(25, 135, 84, 0.1); font-size: 10px;">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <span class="fw-bold small text-dark mt-1 text-uppercase">QRIS & E-Wallet</span>
                                </div>
                                <?php
                                $qris_checkboxes = [
                                    'c_openapiChannelQrisMpmDynamicCreate' => 'QRIS MPM Create',
                                    'c_openapiChannelQrisMpmDynamicQuery' => 'QRIS MPM Query',
                                    'c_openapiChannelQrisMpmDynamicCancel' => 'QRIS MPM Cancel',
                                    'c_openapiChannelEwalletDynamicCreate' => 'E-wallet Create',
                                    'c_openapiChannelEwalletDynamicQuery' => 'E-wallet Query',
                                    'c_openapiChannelEwalletDynamicCancel' => 'E-wallet Cancel'
                                ];
                                foreach ($qris_checkboxes as $key => $label) {
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
                                }
                                ?>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-1 mb-3 h6">
                                    <div class="bg-warning-soft text-warning rounded-pill px-2 py-1 mr-2" style="background-color: rgba(255, 193, 7, 0.1); font-size: 10px;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <span class="fw-bold small text-dark mt-1 text-uppercase">Transfer</span>
                                </div>
                                <?php
                                $transfer_checkboxes = [
                                    'c_openapiChannelTransferToBifast' => 'BI-FAST Transfer',
                                    'c_openapiChannelTransferToRealtimeOnline' => 'Realtime Online Transfer',
                                    'c_allowTransferFromDashboard' => 'Allow Dashboard Transfer'
                                ];
                                foreach ($transfer_checkboxes as $key => $label) {
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <a href="<?= base_url('merchant/manage'); ?>" class="btn btn-light px-4 py-2 mr-3 font-weight-bold small text-uppercase">Cancel</a>
                            <button type="submit" class="btn-dt-action btn-dt-action-success px-5 py-2">
                                <i class="fas fa-save mr-2"></i> Register Merchant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm dt-card h-100">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book-open mr-2"></i> Instructions Guide</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="text-muted small mb-4">Fill out the form to create a new merchant. All account credentials will be generated and emailed to the merchant upon activation.</p>
                    
                    <div class="p-3 mb-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-user-circle text-primary mr-2"></i> Account Information</div>
                        <p class="text-muted small mb-0">Passwords must match and be at least 6 characters. The GVConnect Business ID is optional but recommended.</p>
                    </div>
                    
                    <div class="p-3 mb-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-key text-primary mr-2"></i> OpenAPI Configuration</div>
                        <p class="text-muted small mb-0">Whitelist IP ensures only authorized servers can call the API. Separate multiple IPs with a semicolon (;).</p>
                    </div>
                    
                    <div class="p-3 mb-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-shield-alt text-primary mr-2"></i> Service Permissions</div>
                        <p class="text-muted small mb-0">Toggle the specific channel permissions this merchant is allowed to use. These can be modified later in the edit page.</p>
                    </div>
                    
                    <div class="p-3" style="background-color: rgba(25, 135, 84, 0.1); border: 1px dashed rgba(25, 135, 84, 0.5); border-radius: 8px;">
                        <div class="font-weight-bold mb-1 text-success" style="font-size: 13px;"><i class="fas fa-info-circle mr-2"></i> Status</div>
                        <h5 class="mb-0 font-weight-bold text-success">New Registration</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
