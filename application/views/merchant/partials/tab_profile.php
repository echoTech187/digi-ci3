<!-- ── TAB 1: MERCHANT INFORMATION (ULTRA-PREMIUM OVERHAUL) ── -->
<div class="tab-pane fade p-4" id="nav-profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
        <!-- Kolom Kiri: Core Profile -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 20px; box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); overflow: hidden;">
                <div class="card-header bg-transparent border-bottom p-4 d-flex align-items-center" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                    <div class="avatar-sm bg-primary-soft text-primary rounded-circle p-2 mr-3" style="background-color: rgba(78, 115, 223, 0.15); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold text-white mb-0">Core Profile</h6>
                        <p class="text-muted small mb-0">Basic information and account legalities</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building text-primary mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Merchant Name</span>
                        </div>
                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_name']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope text-info mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Official Email</span>
                        </div>
                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_email']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone-alt text-success mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Phone Number</span>
                        </div>
                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_phoneNumber'] ?: '-'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-warning mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Registration Date</span>
                        </div>
                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_dateCreated'] ? date('d M Y, H:i', strtotime($merchant['c_dateCreated'])) : '-'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-layer-group text-purple mr-3" style="width: 20px; color: #b833ff;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Merchant Level</span>
                        </div>
                        <span class="badge badge-pill badge-info-soft text-info px-3 py-1 font-weight-bold">Level <?= $merchant['c_merchantLevel']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-success mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Account Status</span>
                        </div>
                        <?php $st = $merchant['c_status']; $cls = ($st=='Active')?'success':'secondary'; ?>
                        <span class="badge badge-pill badge-<?= $cls; ?>-soft text-<?= $cls; ?> px-3 py-1 font-weight-bold"><?= $st; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: OpenAPI Config -->
        <div class="col-lg-6">
            <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 20px; box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); overflow: hidden;">
                <div class="card-header bg-transparent border-bottom p-4 d-flex align-items-center" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                    <div class="avatar-sm bg-purple-soft text-purple rounded-circle p-2 mr-3" style="background-color: rgba(184, 51, 255, 0.15); color: #b833ff; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-network-wired fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold text-white mb-0">OpenAPI & Integration Config</h6>
                        <p class="text-muted small mb-0">Technical parameters, callbacks, and gateway credentials</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-plug text-success mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">OpenAPI Status</span>
                        </div>
                        <?php $ost = $merchant['c_openapiStatus']; $ocls = ($ost=='Active')?'success':'danger'; ?>
                        <span class="badge badge-pill badge-<?= $ocls; ?>-soft text-<?= $ocls; ?> px-3 py-1 font-weight-bold"><?= $ost; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center mr-2">
                            <i class="fas fa-link text-info mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">VA Callback URL</span>
                        </div>
                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #36b9cc;" title="<?= $merchant['c_openapiUrlCallbackVa']; ?>">
                            <?= $merchant['c_openapiUrlCallbackVa'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center mr-2">
                            <i class="fas fa-qrcode text-warning mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">QRIS Callback URL</span>
                        </div>
                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #f6c23e;" title="<?= $merchant['c_openapiUrlCallbackQrisMpm']; ?>">
                            <?= $merchant['c_openapiUrlCallbackQrisMpm'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center mr-2">
                            <i class="fas fa-wallet text-primary mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">E-Wallet Callback</span>
                        </div>
                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #4e73df;" title="<?= $merchant['c_openapiUrlCallbackEwallet']; ?>">
                            <?= $merchant['c_openapiUrlCallbackEwallet'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center mr-2">
                            <i class="fas fa-exchange-alt text-success mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">Transfer Callback</span>
                        </div>
                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #20c997;" title="<?= $merchant['c_openapiUrlCallbackTransfer']; ?>">
                            <?= $merchant['c_openapiUrlCallbackTransfer'] ?: '<em class="text-muted">Not Configured</em>'; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.05) !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-id-badge text-purple mr-3" style="width: 20px; color: #b833ff;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">GVConnect ID</span>
                        </div>
                        <span class="font-weight-bold text-white text-right"><?= $merchant['c_gvconnectBusinessId'] ?: '-'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex align-items-center mr-2">
                            <i class="fas fa-shield-alt text-danger mr-3" style="width: 20px;"></i>
                            <span class="text-muted font-weight-bold small text-uppercase" style="letter-spacing: 0.5px;">IP Allowlist</span>
                        </div>
                        <div class="px-3 py-1 rounded text-truncate font-family-monospace small border" style="max-width: 260px; background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1) !important; color: #e74a3b;" title="<?= $merchant['c_openapiIPAllow']; ?>">
                            <?= $merchant['c_openapiIPAllow'] ?: '<em class="text-muted">Any IP Allowed</em>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
