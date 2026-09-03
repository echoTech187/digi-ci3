<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-lg-6 col-md-8">
        <div class="border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-5 text-center">
                
                <!-- Success Icon -->
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-soft text-success rounded-circle" style="width: 80px; height: 80px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                </div>

                <!-- Titles -->
                <h3 class="font-weight-bold text-dark mb-2">Merchant is Published!</h3>
                <p class="text-muted mb-4" style="font-size: 15px;">The merchant credentials have been securely generated.</p>

                <!-- Security Warning -->
                <div class="alert mb-4 text-left d-flex align-items-start" style="background-color: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 12px; color: #856404; font-size: 14px;">
                    <i class="fas fa-exclamation-triangle mt-1 mr-3 text-warning"></i>
                    <div>
                        <strong>Important Security Notice:</strong><br>
                        This secret link and QR code are displayed <b>only once</b> and cannot be recovered. If you refresh or leave this page, you will be redirected to the list for your own security. Please copy the link before proceeding.
                    </div>
                </div>

                <!-- Input Group for URL -->
                <div class="text-left mb-4">
                    <label class="font-weight-bold text-gray-700 small">Share this link:</label>
                    <div class="input-group">
                        <input type="text" id="secretUrlInput" class="form-control bg-light" value="<?= htmlspecialchars($secretUrl); ?>" readonly style="font-size: 14px; border-radius: 8px 0 0 8px; border-right: 0;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="copySecretBtn" style="border-radius: 0 8px 8px 0; background: white; border-color: #ced4da;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                <!-- QR Code Section -->
                <div class="mb-3">
                    <span class="text-muted small text-uppercase letter-spacing-1 bg-white px-2 position-relative" style="top: -24px;">Scan to open in app</span>
                </div>

                <div class="d-flex justify-content-center mb-3" style="margin-top: -10px;">
                    <div class="p-2 border" style="border-radius: 16px; border-color: #e3e6f0!important; display: inline-block;">
                        <!-- Using api.qrserver.com to generate the QR Code instantly -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($secretUrl); ?>" alt="QR Code" style="border-radius: 8px;">
                    </div>
                </div>

                <p class="text-muted small mx-auto" style="max-width: 300px;">Scan this code with your phone to securely view the merchant payload.</p>

                <div class="mt-5">
                    <a href="<?= base_url('merchant/manage'); ?>" class="btn btn-primary px-5 py-2 font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Merchant List
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#copySecretBtn').on('click', function() {
            var copyText = document.getElementById("secretUrlInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            
            var $icon = $(this).find('i');
            var originalHtml = $(this).html();
            
            $(this).html('<i class="fas fa-check text-success"></i> Copied!');
            
            setTimeout(() => {
                $(this).html(originalHtml);
            }, 2000);
            
            // SweetAlert Optional notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'URL copied to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'swal2-premium-popup'
                    }
                });
            }
        });
    });
</script>
