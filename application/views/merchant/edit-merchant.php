<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage merchant account details, callback configurations and system status</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Merchant Configuration Form</h6>
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

                    <form class="ajax-form" action="<?= base_url('merchant/manage/update/' . $merchant['id']); ?>" data-redirect="<?= base_url('merchant/manage'); ?>" method="post" autocomplete="off">
                        
                        <!-- Account Information -->
                        <div class="section-title mb-4 mt-0 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-user-circle mr-2"></i> Account Information
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Name</label>
                                <input type="text" name="c_name" class="form-control" 
                                       value="<?= set_value('c_name', isset($merchant['c_name']) ? $merchant['c_name'] : ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Email</label>
                                <input type="email" name="c_email" class="form-control" 
                                       value="<?= set_value('c_email', isset($merchant['c_email']) ? $merchant['c_email'] : ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Merchant Phone</label>
                                <input type="text" name="c_phoneNumber" class="input-phone form-control" 
                                       value="<?= set_value('c_phoneNumber', isset($merchant['c_phoneNumber']) ? $merchant['c_phoneNumber'] : ''); ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">GVConnect Business ID</label>
                                <input type="text" name="c_gvconnectBusinessId" class="form-control" 
                                       value="<?= set_value('c_gvconnectBusinessId', isset($merchant['c_gvconnectBusinessId']) ? $merchant['c_gvconnectBusinessId'] : ''); ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Password (Leave blank to keep current)</label>
                                <input type="password" name="c_password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Confirm Password</label>
                                <input type="password" name="c_confirmPassword" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- OpenAPI Configuration -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-key mr-2"></i> OpenAPI Configuration
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">Whitelist IP (semicolon separated)</label>
                            <input type="text" name="c_openapiIPAllow" class="form-control" 
                                   value="<?= set_value('c_openapiIPAllow', isset($merchant['c_openapiIPAllow']) ? $merchant['c_openapiIPAllow'] : ''); ?>"
                                   placeholder="1.2.3.4; 5.6.7.8">
                            <small class="text-muted">Restrict API access to these IP addresses</small>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback QRIS MPM</label>
                                <input type="text" name="c_openapiUrlCallbackQrisMpm" class="form-control" 
                                       value="<?= set_value('c_openapiUrlCallbackQrisMpm', isset($merchant['c_openapiUrlCallbackQrisMpm']) ? $merchant['c_openapiUrlCallbackQrisMpm'] : ''); ?>"
                                       placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback E-wallet</label>
                                <input type="text" name="c_openapiUrlCallbackEwallet" class="form-control" 
                                       value="<?= set_value('c_openapiUrlCallbackEwallet', isset($merchant['c_openapiUrlCallbackEwallet']) ? $merchant['c_openapiUrlCallbackEwallet'] : ''); ?>"
                                       placeholder="https://api.your.com/callback">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Callback VA</label>
                                <input type="text" name="c_openapiUrlCallbackVa" class="form-control" 
                                       value="<?= set_value('c_openapiUrlCallbackVa', isset($merchant['c_openapiUrlCallbackVa']) ? $merchant['c_openapiUrlCallbackVa'] : ''); ?>"
                                       placeholder="https://api.your.com/callback">
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
                                    $checked = (isset($merchant[$key]) && $merchant[$key] == '1') ? 'checked' : '';
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1" '.$checked.'><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
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
                                    $checked = (isset($merchant[$key]) && $merchant[$key] == '1') ? 'checked' : '';
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1" '.$checked.'><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
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
                                    $checked = (isset($merchant[$key]) && $merchant[$key] == '1') ? 'checked' : '';
                                    echo '<div class="mb-3"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1" '.$checked.'><label class="custom-control-label small fw-bold text-muted" for="'.$key.'">'.$label.'</label></div></div>';
                                }
                                ?>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- System Status -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-cog mr-2"></i> System Status
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label class="font-weight-bold text-gray-700 small">Merchant Account Status</label>
                                <select name="c_status" class="form-control custom-select">
                                    <option value="Pending" <?= (isset($merchant['c_status']) && $merchant['c_status'] == 'Pending') ? 'selected' : ''; ?>>🕒 Pending Approval</option>
                                    <option value="Active" <?= (isset($merchant['c_status']) && $merchant['c_status'] == 'Active') ? 'selected' : ''; ?>>✅ Active</option>
                                    <option value="Blocked" <?= (isset($merchant['c_status']) && $merchant['c_status'] == 'Blocked') ? 'selected' : ''; ?>>🚫 Blocked</option>
                                    <option value="Freeze" <?= (isset($merchant['c_status']) && $merchant['c_status'] == 'Freeze') ? 'selected' : ''; ?>>❄️ Frozen</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-gray-700 small">OpenAPI Access Status</label>
                                <select name="c_openapiStatus" class="form-control custom-select">
                                    <option value="Pending" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Pending') ? 'selected' : ''; ?>>🕒 Pending Approval</option>
                                    <option value="Active" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Active') ? 'selected' : ''; ?>>✅ Active Access</option>
                                    <option value="Not Active" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Not Active') ? 'selected' : ''; ?>>❌ Deactivated</option>
                                    <option value="Blocked" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Blocked') ? 'selected' : ''; ?>>🚫 Blocked</option>
                                    <option value="Freeze" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Freeze') ? 'selected' : ''; ?>>❄️ Account Frozen</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= base_url('merchant/manage'); ?>" class="btn btn-light px-4 py-2 mr-3 font-weight-bold small text-uppercase">Cancel</a>
                            <button type="submit" class="btn-dt-action btn-dt-action-success px-5 py-2">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Merchant Profile Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-white text-primary rounded-circle mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-store"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">Merchant Profile</h5>
                    </div>
                    <div class="mb-3">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Business Name</div>
                        <div class="h6 mb-0 font-weight-bold"><?= $merchant['c_name']; ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Account Email</div>
                        <div class="mb-0" style="word-break: break-all; font-size: 13px;"><?= $merchant['c_email']; ?></div>
                    </div>
                    <div>
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Merchant ID</div>
                        <div class="mb-0 font-weight-bold">#<?= $merchant['id']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Instructions Guide Card -->
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book-open mr-2"></i> Instructions Guide</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="p-3 mb-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-plug text-primary mr-2"></i> Integration Tips</div>
                        <p class="text-muted small mb-0">Use <strong>HTTPS</strong> for all callbacks. Implement signature verification to validate incoming webhooks.</p>
                    </div>
                    <div class="p-3 mb-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-server text-primary mr-2"></i> Server Requirements</div>
                        <p class="text-muted small mb-0">Ensure your server responds with a <code>200 OK</code> status promptly on every callback hit.</p>
                    </div>
                    <div class="p-3" >
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-shield-alt text-primary mr-2"></i> Access &amp; Status</div>
                        <p class="text-muted small mb-0">Account suspensions take effect immediately. Set OpenAPI Status to "Active" only after integration testing is done.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
