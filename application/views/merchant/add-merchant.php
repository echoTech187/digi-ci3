<div>
    <!-- ── Unified Page Header with Title and Subtitle ── -->
    <div class="dt-page-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center min-w-0 mr-2">
                <div>
                    <h4 class="dt-page-title mb-0 text-truncate" style="font-size: 1.2rem; font-weight: 800; letter-spacing: -0.3px;"><?= $title; ?></h4>
                    <p class="dt-page-subtitle mb-0 text-muted" style="font-size: 12.5px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">Create a new merchant account with custom callback URLs and channel permissions</p>
                </div>
            </div>
            <!-- Step Badge: Only visible on Mobile (< 768px) -->
            <span class="badge badge-subtle-primary px-2.5 py-1.5 font-weight-bold flex-shrink-0 d-md-none" id="wizardStepBadge" style="border-radius: 8px; font-size: 11px;">Step 1 of 3</span>
        </div>
    </div>

    <div class="row">
        <!-- ── Main Form Column (Full width 12 cols on Mobile, Tablet & Notebook < 1200px, 8 cols on Large Desktop >= 1200px) ── -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card border-0 shadow-sm dt-form-card">
                <form class="ajax-form" id="merchantRegisterForm" action="<?= base_url('merchant/manage/register'); ?>" data-redirect="<?= base_url('merchant/manage'); ?>" method="post" autocomplete="off">
                    <div class="card-body p-3.5 p-md-4">
                        <!-- Progress Bar: Only visible on Mobile (< 768px) -->
                        <div class="wizard-mini-progress d-flex align-items-center justify-content-between gap-2 mt-1 mb-4 d-md-none">
                            <div class="mini-progress-step active flex-fill" data-step="1" title="Step 1: Account"></div>
                            <div class="mini-progress-step flex-fill" data-step="2" title="Step 2: OpenAPI"></div>
                            <div class="mini-progress-step flex-fill" data-step="3" title="Step 3: Permissions"></div>
                        </div>

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
                            
                        <!-- SECTION 1: Account Credentials -->
                        <div class="wizard-step-pane" data-step="1">
                            <div class="section-title-clean mb-4 d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                <div class="d-flex align-items-center">
                                    <span class="section-icon-circle mr-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); font-size: 12px;">
                                        <i class="fas fa-user-circle"></i>
                                    </span>
                                    <span class="font-weight-bold text-dark text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Account Credentials</span>
                                </div>
                                <span class="badge badge-light text-muted font-weight-normal px-2.5 py-1" style="font-size: 10.5px; border-radius: 6px;">Required</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Merchant Name <span class="text-danger">*</span></label>
                                    <div class="form-input-group">
                                        <i class="fas fa-store form-input-icon"></i>
                                        <input type="text" name="c_name" class="form-control form-control-prep" placeholder="e.g. ABC Store" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Merchant Email <span class="text-danger">*</span></label>
                                    <div class="form-input-group">
                                        <i class="fas fa-envelope form-input-icon"></i>
                                        <input type="email" name="c_email" class="form-control form-control-prep" placeholder="owner@abc.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Merchant Phone</label>
                                    <div class="form-input-group">
                                        <i class="fas fa-phone-alt form-input-icon"></i>
                                        <input type="text" name="c_phoneNumber" class="input-phone form-control form-control-prep" placeholder="08123xxx">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">GVConnect Business ID</label>
                                    <div class="form-input-group">
                                        <i class="fas fa-id-card form-input-icon"></i>
                                        <input type="text" name="c_gvconnectBusinessId" class="form-control form-control-prep" placeholder="24090200001">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Password <span class="text-danger">*</span></label>
                                    <div class="form-input-group">
                                        <i class="fas fa-lock form-input-icon"></i>
                                        <input type="password" name="c_password" class="form-control form-control-prep" autocomplete="new-password" placeholder="••••••••" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="form-input-group">
                                        <i class="fas fa-check-double form-input-icon"></i>
                                        <input type="password" name="c_confirmPassword" class="form-control form-control-prep" autocomplete="new-password" placeholder="••••••••" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Intermediate Next Button for Step 1 -->
                            <div class="form-action-footer d-flex d-md-none justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                                <a href="<?= base_url('merchant/manage'); ?>" class="btn btn-light px-4 py-2.5 font-weight-bold text-uppercase" style="border-radius: 12px; font-size: 13px;">Cancel</a>
                                <button type="button" class="btn btn-btn-gradient-primary px-4 py-2.5 font-weight-bold text-uppercase d-flex align-items-center justify-content-center shadow-sm btn-next-step" data-next="2" style="border-radius: 12px; font-size: 13px;">
                                    Next: OpenAPI Config <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- SECTION 2: Webhooks & IP Whitelist -->
                        <div class="wizard-step-pane mt-md-5" data-step="2">
                            <div class="section-title-clean mb-4 d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                <div class="d-flex align-items-center">
                                    <span class="section-icon-circle mr-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); font-size: 12px;">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <span class="font-weight-bold text-dark text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Webhooks & IP Whitelist</span>
                                </div>
                                <span class="badge badge-light text-muted font-weight-normal px-2.5 py-1" style="font-size: 10.5px; border-radius: 6px;">Security</span>
                            </div>

                            <div class="form-group mb-3.5">
                                <label class="font-weight-bold text-gray-700 small mb-1.5">Whitelist IP (semicolon separated)</label>
                                <div class="form-input-group">
                                    <i class="fas fa-network-wired form-input-icon"></i>
                                    <input type="text" name="c_openapiIPAllow" class="form-control form-control-prep" placeholder="1.2.3.4; 5.6.7.8">
                                </div>
                                <small class="text-muted mt-1.5 d-block" style="font-size: 11.5px;"><i class="fas fa-shield-alt mr-1 text-primary"></i> Restrict API access to these IP addresses</small>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Callback QRIS MPM</label>
                                    <div class="form-input-group">
                                        <i class="fas fa-link form-input-icon"></i>
                                        <input type="text" name="c_openapiUrlCallbackQrisMpm" class="form-control form-control-prep" placeholder="https://api.your.com/callback">
                                    </div>
                                </div>
                                <div class="col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Callback E-wallet</label>
                                    <div class="form-input-group">
                                        <i class="fas fa-link form-input-icon"></i>
                                        <input type="text" name="c_openapiUrlCallbackEwallet" class="form-control form-control-prep" placeholder="https://api.your.com/callback">
                                    </div>
                                </div>
                                <div class="col-12 mb-3.5">
                                    <label class="font-weight-bold text-gray-700 small mb-1.5">Callback VA</label>
                                    <div class="form-input-group">
                                        <i class="fas fa-link form-input-icon"></i>
                                        <input type="text" name="c_openapiUrlCallbackVa" class="form-control form-control-prep" placeholder="https://api.your.com/callback">
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Intermediate Next Button for Step 2 -->
                            <div class="form-action-footer d-flex d-md-none justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                                <button type="button" class="btn btn-light px-4 py-2.5 font-weight-bold text-uppercase btn-prev-step" data-prev="1" style="border-radius: 12px; font-size: 13px;">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-btn-gradient-primary px-4 py-2.5 font-weight-bold text-uppercase d-flex align-items-center justify-content-center shadow-sm btn-next-step" data-next="3" style="border-radius: 12px; font-size: 13px;">
                                    Next: Permissions <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- SECTION 3: Channel Access Toggles -->
                        <div class="wizard-step-pane mt-md-5" data-step="3">
                            <div class="section-title-clean mb-4 d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                <div class="d-flex align-items-center">
                                    <span class="section-icon-circle mr-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); font-size: 12px;">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <span class="font-weight-bold text-dark text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Channel Access Toggles</span>
                                </div>
                                <span class="badge badge-light text-muted font-weight-normal px-2.5 py-1" style="font-size: 10.5px; border-radius: 6px;">Channels</span>
                            </div>

                            <div class="row">
                                <!-- Virtual Account Permissions -->
                                <div class="col-12 mb-3.5">
                                    <div class="permission-card p-3.5 rounded-16 border">
                                        <div class="d-flex align-items-center gap-1 mb-3 pb-2 border-bottom">
                                            <div class="bg-primary-soft text-primary rounded-pill px-2.5 py-1 mr-2 d-inline-flex align-items-center" style="background-color: rgba(99, 102, 241, 0.12); font-size: 11px;">
                                                <i class="fas fa-university mr-1.5"></i>
                                                <span class="font-weight-bold text-uppercase">Virtual Account</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php
                                            $va_checkboxes = [
                                                'c_openapiChannelVaDynamicCreate' => 'VA Dynamic Create',
                                                'c_openapiChannelVaDynamicQuery' => 'VA Dynamic Query',
                                                'c_openapiChannelVaDynamicCancel' => 'VA Dynamic Cancel',
                                                'c_openapiChannelVaRecurringCreate' => 'VA Recurring Create',
                                                'c_openapiChannelVaRecurringCancel' => 'VA Recurring Cancel'
                                            ];
                                            foreach ($va_checkboxes as $key => $label) {
                                                echo '<div class="col-md-6 col-12 mb-2.5"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small font-weight-bold text-gray-700" for="'.$key.'">'.$label.'</label></div></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- QRIS & E-Wallet Permissions -->
                                <div class="col-12 mb-3.5">
                                    <div class="permission-card p-3.5 rounded-16 border">
                                        <div class="d-flex align-items-center gap-1 mb-3 pb-2 border-bottom">
                                            <div class="bg-success-soft text-success rounded-pill px-2.5 py-1 mr-2 d-inline-flex align-items-center" style="background-color: rgba(16, 185, 129, 0.12); font-size: 11px;">
                                                <i class="fas fa-qrcode mr-1.5"></i>
                                                <span class="font-weight-bold text-uppercase">QRIS & E-Wallet</span>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                                echo '<div class="col-md-6 col-12 mb-2.5"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small font-weight-bold text-gray-700" for="'.$key.'">'.$label.'</label></div></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transfer Permissions -->
                                <div class="col-12 mb-3.5">
                                    <div class="permission-card p-3.5 rounded-16 border">
                                        <div class="d-flex align-items-center gap-1 mb-3 pb-2 border-bottom">
                                            <div class="bg-warning-soft text-warning rounded-pill px-2.5 py-1 mr-2 d-inline-flex align-items-center" style="background-color: rgba(245, 158, 11, 0.12); font-size: 11px;">
                                                <i class="fas fa-exchange-alt mr-1.5"></i>
                                                <span class="font-weight-bold text-uppercase">Transfer</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php
                                            $transfer_checkboxes = [
                                                'c_openapiChannelTransferToBifast' => 'BI-FAST Transfer',
                                                'c_openapiChannelTransferToRealtimeOnline' => 'Realtime Online Transfer',
                                                'c_allowTransferFromDashboard' => 'Allow Dashboard Transfer'
                                            ];
                                            foreach ($transfer_checkboxes as $key => $label) {
                                                echo '<div class="col-md-6 col-12 mb-2.5"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="'.$key.'" name="'.$key.'" value="1"><label class="custom-control-label small font-weight-bold text-gray-700" for="'.$key.'">'.$label.'</label></div></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Intermediate Next Button for Step 3 -->
                            <div class="form-action-footer d-flex d-md-none justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                                <button type="button" class="btn btn-light px-4 py-2.5 font-weight-bold text-uppercase btn-prev-step" data-prev="2" style="border-radius: 12px; font-size: 13px;">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="submit" class="btn btn-btn-gradient-primary px-4 py-2.5 font-weight-bold text-uppercase d-flex align-items-center justify-content-center shadow-sm" style="border-radius: 12px; font-size: 13px;">
                                    <i class="fas fa-check-circle mr-2"></i> Register Merchant
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tablet & Desktop Dedicated Card Footer Bar (Visible ONLY on Tablet >= 768px & Desktop) -->
                    <div class="card-footer px-4 py-3.5 border-top d-none d-md-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(248, 250, 252, 0.75);">
                        <div class="d-none d-sm-flex align-items-center text-muted small" style="font-size: 12px;">
                            <i class="fas fa-shield-alt text-primary mr-2" style="font-size: 13px;"></i>
                            <span>All configuration data is encrypted and saved securely</span>
                        </div>
                        <div class="d-flex align-items-center ml-auto gap-2">
                            <a href="<?= base_url('merchant/manage'); ?>" class="btn btn-light px-4 py-2.5 font-weight-bold text-uppercase mr-2" style="border-radius: 12px; font-size: 13px;">Cancel</a>
                            <button type="submit" class="btn btn-btn-gradient-primary px-4.5 py-2.5 font-weight-bold text-uppercase d-flex align-items-center justify-content-center shadow-sm" style="border-radius: 12px; font-size: 13px;">
                                <i class="fas fa-check-circle mr-2"></i> Register Merchant
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Instructions Guide Column (Standard position on Large Desktop Monitors >= 1200px) ── -->
        <div class="col-xl-4 col-12 mb-4 d-none d-xl-block">
            <div class="card border-0 shadow-sm dt-form-card">
                <div class="card-header py-3.5 px-4 border-0 d-flex align-items-center justify-content-between" style="background: transparent; border-bottom: 1px solid var(--border-color, rgba(226, 232, 240, 0.6)) !important;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                            <i class="fas fa-book-open" style="font-size: 15px;"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-dark" style="font-size: 14.5px;">Setup Guide</h6>
                    </div>
                    <span class="badge badge-subtle-primary px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 10.5px;">Quick Info</span>
                </div>
                <div class="card-body p-3.5">
                    <p class="text-muted small mb-3.5" style="line-height: 1.5; font-size: 12px;">Follow these guidelines to complete the merchant setup smoothly.</p>
                    
                    <div class="p-3 mb-2 guide-box rounded-12 border" style="background: rgba(248, 250, 252, 0.6);">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-primary rounded-circle mr-2 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">1</span>
                            <span class="font-weight-bold text-dark" style="font-size: 12.5px;">Account Credentials</span>
                        </div>
                        <p class="text-muted small mb-0 pl-4" style="font-size: 11.5px; line-height: 1.45;">Passwords must match and be at least 6 characters. Business ID is optional.</p>
                    </div>
                    
                    <div class="p-3 mb-2 guide-box rounded-12 border" style="background: rgba(248, 250, 252, 0.6);">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-primary rounded-circle mr-2 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">2</span>
                            <span class="font-weight-bold text-dark" style="font-size: 12.5px;">OpenAPI & Webhooks</span>
                        </div>
                        <p class="text-muted small mb-0 pl-4" style="font-size: 11.5px; line-height: 1.45;">Configure IP whitelist and HTTPS callback URLs for real-time notifications.</p>
                    </div>
                    
                    <div class="p-3 mb-3 guide-box rounded-12 border" style="background: rgba(248, 250, 252, 0.6);">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-primary rounded-circle mr-2 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">3</span>
                            <span class="font-weight-bold text-dark" style="font-size: 12.5px;">Channel Permissions</span>
                        </div>
                        <p class="text-muted small mb-0 pl-4" style="font-size: 11.5px; line-height: 1.45;">Toggle payment channels (VA, QRIS, E-Wallet, BI-FAST) allowed for this merchant.</p>
                    </div>
                    
                    <div class="p-3 guide-status-box" style="background-color: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.35); border-radius: 12px;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="font-weight-bold text-success small" style="font-size: 11.5px;"><i class="fas fa-info-circle mr-1.5"></i> Mode</span>
                            <span class="d-inline-block" style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);"></span>
                        </div>
                        <h6 class="mb-0 font-weight-bold text-success" style="font-size: 13.5px;">Merchant Registration Mode</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Wizard Interactive Controller JS (Mobile Only) -->
<script>
$(document).ready(function() {
    function isMobileView() {
        return window.innerWidth < 768;
    }

    function goToStep(step) {
        if (!isMobileView()) return; // On Tablet & Desktop, all panes are displayed together

        $('.wizard-step-pane').hide();
        $('.wizard-step-pane[data-step="' + step + '"]').fadeIn(220);
        
        $('.mini-progress-step').removeClass('active completed');
        $('.mini-progress-step').each(function() {
            var progressStep = parseInt($(this).data('step'));
            if (progressStep === step) {
                $(this).addClass('active');
            } else if (progressStep < step) {
                $(this).addClass('completed');
            }
        });
        
        $('#wizardStepBadge').text('Step ' + step + ' of 3');
        $('html, body').animate({ scrollTop: $('.dt-form-card').offset().top - 80 }, 200);
    }

    // Initialize layout on page load and window resize
    function initLayout() {
        if (isMobileView()) {
            goToStep(1);
        } else {
            $('.wizard-step-pane').show();
        }
    }

    initLayout();
    $(window).on('resize', initLayout);

    $(document).on('click', '.btn-next-step', function() {
        var currentPane = $(this).closest('.wizard-step-pane');
        var isValid = true;
        
        // Validate required inputs in current step
        currentPane.find('input[required]').each(function() {
            if (!this.checkValidity()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Field Wajib Diisi',
                text: 'Harap isi semua kolom yang bertanda bintang (*) sebelum melanjutkan.',
                icon: 'warning',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                buttonsStyling: false
            });
            return;
        }

        var nextStep = parseInt($(this).data('next'));
        goToStep(nextStep);
    });

    $(document).on('click', '.btn-prev-step', function() {
        var prevStep = parseInt($(this).data('prev'));
        goToStep(prevStep);
    });

    $(document).on('click', '.mini-progress-step.completed', function() {
        var step = parseInt($(this).data('step'));
        goToStep(step);
    });
});
</script>
