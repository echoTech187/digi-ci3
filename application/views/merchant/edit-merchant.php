<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage merchant callback configurations and system status</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-gray-800">Callback & API Configuration</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger border-left-danger shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success border-left-success shadow-sm mb-4">
                            <i class="fas fa-check-circle mr-2"></i> <?= $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/updateMerchant/' . $merchant['id']); ?>" method="post">
                        
                        <div class="section-title mb-4 mt-0 text-primary font-weight-bold small">
                            <i class="fas fa-link mr-2"></i> WEBHOOK CALLBACKS
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">URL Callback QRIS MPM</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0"><i class="fas fa-qrcode"></i></span>
                                </div>
                                <input type="text" name="c_openapiUrlCallbackQrisMpm" class="form-control border-left-0"
                                       placeholder="https://your-server.com/callback/qris"
                                       value="<?= set_value('c_openapiUrlCallbackQrisMpm', isset($merchant['c_openapiUrlCallbackQrisMpm']) ? $merchant['c_openapiUrlCallbackQrisMpm'] : ''); ?>">
                            </div>
                            <small class="text-muted">Notification endpoint for dynamic QRIS payments</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">URL Callback Virtual Account</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0"><i class="fas fa-university"></i></span>
                                </div>
                                <input type="text" name="c_openapiUrlCallbackVa" class="form-control border-left-0"
                                       placeholder="https://your-server.com/callback/va"
                                       value="<?= set_value('c_openapiUrlCallbackVa', isset($merchant['c_openapiUrlCallbackVa']) ? $merchant['c_openapiUrlCallbackVa'] : ''); ?>">
                            </div>
                            <small class="text-muted">Notification endpoint for Virtual Account settlements</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">URL Callback E-Wallet</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0"><i class="fas fa-wallet"></i></span>
                                </div>
                                <input type="text" name="c_openapiUrlCallbackEwallet" class="form-control border-left-0"
                                       placeholder="https://your-server.com/callback/ewallet"
                                       value="<?= set_value('c_openapiUrlCallbackEwallet', isset($merchant['c_openapiUrlCallbackEwallet']) ? $merchant['c_openapiUrlCallbackEwallet'] : ''); ?>">
                            </div>
                            <small class="text-muted">Notification endpoint for e-wallet transactions (OVO, Dana, etc.)</small>
                        </div>

                        <hr class="my-5" style="border-top: 1px dashed #e3e6f0;">

                        <div class="section-title mb-4 text-primary font-weight-bold small">
                            <i class="fas fa-shield-alt mr-2"></i> SYSTEM STATUS
                        </div>

                        <div class="form-group mb-5">
                            <label class="font-weight-bold text-gray-700 small">OpenAPI Access Status</label>
                            <select name="c_openapiStatus" class="form-control custom-select">
                                <option value="Pending" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Pending') ? 'selected' : ''; ?>>🕒 Pending Approval</option>
                                <option value="Active" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Active') ? 'selected' : ''; ?>>✅ Active Access</option>
                                <option value="Not Active" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Not Active') ? 'selected' : ''; ?>>❌ Deactivated</option>
                                <option value="Blocked" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Blocked') ? 'selected' : ''; ?>>🚫 Blocked</option>
                                <option value="Freeze" <?= (isset($merchant['c_openapiStatus']) && $merchant['c_openapiStatus'] == 'Freeze') ? 'selected' : ''; ?>>❄️ Account Frozen</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-dt-action btn-dt-action-primary" style="padding: 12px 40px; font-size: 14px;">
                                <i class="fas fa-save mr-2"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-white text-primary rounded-circle mr-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-store"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">Merchant Profile</h5>
                    </div>
                    
                    <div class="mb-4">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Business Name</div>
                        <div class="h5 mb-0 font-weight-bold"><?= $merchant['c_name']; ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Account Email</div>
                        <div class="mb-0 font-weight-bold" style="word-break: break-all;"><?= $merchant['c_email']; ?></div>
                    </div>
                    
                    <div>
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Registration No</div>
                        <div class="mb-0 font-weight-bold"><?= $merchant['c_registrasiNo'] ?? '—'; ?></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-gray-800 mb-3 d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning mr-2"></i> Integration Tips
                    </h6>
                    <div class="small text-muted mb-3" style="line-height: 1.6;">
                        Optimize your integration by following these best practices for webhook delivery.
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1" style="font-size: 10px;"></i>
                            <span class="small">Use <strong>HTTPS</strong> for all callback endpoints to ensure data security.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1" style="font-size: 10px;"></i>
                            <span class="small">Implement signature verification to validate that incoming webhooks are from us.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1" style="font-size: 10px;"></i>
                            <span class="small">Ensure your server responds with a <code>200 OK</code> status promptly.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.letter-spacing-1 {
    letter-spacing: 1px;
}
.opacity-75 {
    opacity: 0.75;
}
.section-title {
    font-size: 0.75rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.custom-select {
    height: calc(1.5em + 1rem + 2px);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}
</style>
</div>