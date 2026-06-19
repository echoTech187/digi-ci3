<style>
    /* ── Secret Published Page — Dark Mode Aware ── */
    .sp-expiry-badge {
        display: flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        background: var(--gray-100);
        border: 1px solid var(--border-color);
        color: var(--gray-700);
        transition: background 0.3s, border 0.3s, color 0.3s;
    }
    .sp-expiry-badge.countdown-ok {
        background: rgba(28, 200, 138, 0.1);
        border-color: rgba(28, 200, 138, 0.3);
        color: var(--success);
    }
    .sp-expiry-badge.countdown-warn {
        background: rgba(246, 194, 62, 0.12);
        border-color: rgba(246, 194, 62, 0.35);
        color: var(--warning);
    }
    .sp-expiry-badge.countdown-expired {
        background: rgba(231, 74, 59, 0.1);
        border-color: rgba(231, 74, 59, 0.3);
        color: var(--danger);
    }

    /* Security alert — dark mode aware */
    .sp-security-alert {
        background: rgba(246, 194, 62, 0.1);
        border: 1px solid rgba(246, 194, 62, 0.35);
        border-radius: 12px;
        color: var(--warning);
        font-size: 14px;
    }
    [data-theme="dark"] .sp-security-alert {
        background: rgba(246, 194, 62, 0.08);
        border-color: rgba(246, 194, 62, 0.25);
        color: #f6c23e;
    }
    .sp-security-alert strong,
    .sp-security-alert b {
        color: inherit;
    }

    /* Copy button — dark mode aware */
    .sp-copy-btn {
        border-radius: 0 8px 8px 0 !important;
        background: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        color: var(--gray-600) !important;
        transition: background 0.2s, color 0.2s;
    }
    .sp-copy-btn:hover {
        background: var(--gray-100) !important;
        color: var(--gray-900) !important;
    }

    /* HR — dark mode aware */
    .sp-divider {
        border-top: 1px dashed var(--border-color);
    }

    /* QR border */
    .sp-qr-wrapper {
        border-radius: 16px;
        border: 1px solid var(--border-color) !important;
        display: inline-block;
        padding: 8px;
        background: var(--bg-card);
    }
</style>

<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-lg-6 col-md-8">
        <div class="border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-5 text-center">
                
                <!-- Success Icon -->
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(28, 200, 138, 0.12);">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                </div>

                <!-- Titles -->
                <h3 class="font-weight-bold text-dark mb-2">Merchant is Published!</h3>
                <p class="text-muted mb-4" style="font-size: 15px;">The merchant credentials have been securely generated.</p>

                <!-- Security Warning -->
                <div class="sp-security-alert alert mb-4 text-left d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle mt-1 mr-3"></i>
                    <div>
                        <strong>Important Security Notice:</strong><br>
                        This secret link and QR code are displayed <b>only once</b> and cannot be recovered. If you refresh or leave this page, you will be redirected to the list for your own security. Please copy the link before proceeding.
                    </div>
                </div>

                <!-- Expiry Info -->
                <div class="d-flex align-items-center justify-content-center mb-4" style="gap: 10px; flex-wrap: wrap;">
                    <div class="sp-expiry-badge">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>Expired: <strong><?= date('d M Y, H:i', $secretExpiresAt); ?> WIB</strong></span>
                    </div>
                    <div class="sp-expiry-badge countdown-ok" id="countdownBadge">
                        <i class="fas fa-hourglass-half mr-2" id="countdownIcon"></i>
                        <span>Sisa: <strong id="countdownTimer">--:--:--</strong></span>
                    </div>
                </div>

                <!-- Input Group for URL -->
                <div class="text-left mb-4">
                    <label class="font-weight-bold text-gray-700 small">Share this link:</label>
                    <div class="input-group">
                        <input type="text" id="secretUrlInput" class="form-control" value="<?= htmlspecialchars($secretUrl); ?>" readonly style="font-size: 14px; border-radius: 8px 0 0 8px; border-right: 0;">
                        <div class="input-group-append">
                            <button class="btn sp-copy-btn" type="button" id="copySecretBtn">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="sp-divider my-4">

                <!-- QR Code Section -->
                <div class="mb-3">
                    <span class="text-muted small text-uppercase letter-spacing-1 px-2 position-relative" style="top: -24px; background: var(--bg-card);">Scan to open in app</span>
                </div>

                <div class="d-flex justify-content-center mb-3" style="margin-top: -10px;">
                    <div class="sp-qr-wrapper">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($secretUrl); ?>" alt="QR Code" style="border-radius: 8px; display: block;">
                    </div>
                </div>

                <p class="text-muted small mx-auto" style="max-width: 300px;">Scan this code with your phone to securely view the merchant payload.</p>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // ── Copy URL Button ──
        $('#copySecretBtn').on('click', function() {
            var copyText = document.getElementById("secretUrlInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            var originalHtml = $(this).html();
            $(this).html('<i class="fas fa-check"></i> Copied!');
            $(this).css('color', 'var(--success)');
            setTimeout(() => {
                $(this).html(originalHtml);
                $(this).css('color', '');
            }, 2000);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'URL copied to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'swal2-premium-popup' }
                });
            }
        });

        // ── Countdown Timer ──
        var expiresAt = <?= (int)$secretExpiresAt; ?> * 1000; // convert to ms
        var badge     = document.getElementById('countdownBadge');
        var icon      = document.getElementById('countdownIcon');
        var timer     = document.getElementById('countdownTimer');

        function updateCountdown() {
            var remaining = expiresAt - Date.now();

            if (remaining <= 0) {
                timer.textContent = 'EXPIRED';
                badge.className = 'sp-expiry-badge countdown-expired';
                icon.className  = 'fas fa-times-circle mr-2';
                clearInterval(countdownInterval);
                return;
            }

            var hours   = Math.floor(remaining / 3600000);
            var minutes = Math.floor((remaining % 3600000) / 60000);
            var seconds = Math.floor((remaining % 60000) / 1000);

            timer.textContent = String(hours).padStart(2, '0') + ':' +
                                String(minutes).padStart(2, '0') + ':' +
                                String(seconds).padStart(2, '0');

            // Warna badge berdasarkan sisa waktu
            if (remaining < 3600000) {
                // < 1 jam → merah
                badge.className = 'sp-expiry-badge countdown-expired';
                icon.className  = 'fas fa-hourglass-end mr-2';
            } else if (remaining < 21600000) {
                // < 6 jam → kuning
                badge.className = 'sp-expiry-badge countdown-warn';
                icon.className  = 'fas fa-hourglass-half mr-2';
            } else {
                // > 6 jam → hijau
                badge.className = 'sp-expiry-badge countdown-ok';
                icon.className  = 'fas fa-hourglass-half mr-2';
            }
        }

        updateCountdown();
        var countdownInterval = setInterval(updateCountdown, 1000);
    });
</script>
