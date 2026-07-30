
<section class="login-wrapper">
    <div class="login-card">
        <!-- Left Side -->
        <div class="login-left">
            <div class="login-animation"></div>
            <div class="login-left-content brand-logo">
                <img src="<?= base_url('public/image/icon-white--300.png'); ?>" alt="Digi Logo" class="img-fluid" style="width: 42px; height: 42px;z-index: 99;">
                <span class="m-0 font-weight-bold h3">Admin</span>
            </div>
            <div class="login-left-content mt-auto text-white">
                <!-- Clock & Version -->
                <div class="d-flex align-items-center mb-4" style="gap: 15px;">
                    <div class="clock-display font-weight-bold" style="font-size: 2.2rem; letter-spacing: 1px; color: #f8fafc; text-shadow: 0 2px 10px rgba(0,0,0,0.3);" id="liveClock">
                        00:00
                    </div>
                    <div style="border-left: 2px solid rgba(255,255,255,0.15); height: 35px;"></div>
                    <div>
                        <div style="font-size: 0.85rem; color: #a5b4fc; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;">Digi Core Admin</div>
                        <div style="font-size: 0.8rem; color: #94a3b8;">v2.1.0 &mdash; &copy; <?= date('Y'); ?></div>
                    </div>
                </div>

                <!-- IT Support -->
                <div class="support-info p-3" style="background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; transition: all 0.3s ease;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-headset mt-1 mr-3" style="color: #818cf8; font-size: 1.4rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
                        <div>
                            <div style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; line-height: 1.4;">Butuh bantuan akses atau lupa kredensial login?</div>
                            <div style="font-size: 0.95rem; font-weight: 600; color: #f8fafc;">
                                <i class="fas fa-envelope mr-1" style="color: #94a3b8; font-size: 0.85rem;"></i> support@gidi.co.id
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side -->
        <div class="login-right" style="position: relative;">
            
            <!-- Theme Toggle -->
            <button type="button" id="authThemeBtn" class="btn" style="position: absolute; top: 20px; right: 20px; background: transparent; border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; z-index: 10;">
                <i class="fas fa-moon"></i>
            </button>
            <div class="login-mobile-logo">
                <img src="<?= base_url('public/image/icon-300.png'); ?>" alt="Logo">
            </div>

            <h1 class="login-title">Welcome back</h1>
            <p class="login-subtitle">Easily monitor and manage all your transactions.</p>
            <div class="text-center">
                <?= $this->session->flashdata('message'); ?>
            </div>
            <form class="user" method="post" action="<?= base_url('auth'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="form-group-custom">
                    <label class="form-label-custom">Email</label>
                    <input type="email" class="form-control-custom" id="email" name="email" placeholder="example@email.com" value="<?= set_value('email'); ?>" autofocus>
                    <?= form_error('email', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                </div>

                <div class="form-group-custom mb-3">
                    <label class="form-label-custom">Password</label>
                    <input type="password" class="form-control-custom" id="password" name="password" placeholder="••••••••••••">
                    <?= form_error('password', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                </div>

                <!-- <div class="d-flex justify-content-between align-items-center mb-4">
                    <div style="font-size: 15px; color: #6B7280; font-weight: 500;">Remember Password</div>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="customSwitch1">
                      <label class="custom-control-label" for="customSwitch1" style="cursor: pointer;"></label>
                    </div>
                </div> -->

                <!-- Recaptcha if active -->
                <div class="form-group mb-0" id="recaptcha-wrapper">
                    <div class="g-recaptcha" id="recaptcha-container"></div>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>
        </div>
    </div>
</section>

<script>
    function updateLiveClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const clockEl = document.getElementById('liveClock');
        if(clockEl) clockEl.textContent = timeString;
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock(); // Initialize immediately

    // Theme Toggle & reCAPTCHA Dynamic Theme Logic
    const themeBtn = document.getElementById('authThemeBtn');
    const themeIcon = themeBtn.querySelector('i');
    const recaptchaSiteKey = "<?= $recaptcha_site_key; ?>";
    
    function updateIcon(theme) {
        if(theme === 'light') {
            themeIcon.className = 'fas fa-sun';
            themeBtn.style.color = '#f59e0b'; // Amber for sun
            themeBtn.style.borderColor = 'rgba(0,0,0,0.1)';
        } else {
            themeIcon.className = 'fas fa-moon';
            themeBtn.style.color = '#94a3b8';
            themeBtn.style.borderColor = 'rgba(255,255,255,0.1)';
        }
    }

    window.renderRecaptcha = function(theme) {
        const wrapper = document.getElementById('recaptcha-wrapper');
        if (!wrapper || typeof grecaptcha === 'undefined' || typeof grecaptcha.render !== 'function') {
            return;
        }
        if (!recaptchaSiteKey) return;

        const currentTheme = theme || document.documentElement.getAttribute('data-theme') || 'dark';
        
        // Re-create container DOM element to clear Google reCAPTCHA internal node registration
        wrapper.innerHTML = '<div class="g-recaptcha" id="recaptcha-container"></div>';

        try {
            grecaptcha.render('recaptcha-container', {
                'sitekey': recaptchaSiteKey,
                'theme': currentTheme
            });
        } catch(e) {
            console.error('reCAPTCHA render error:', e);
        }
    };

    window.onloadRecaptchaCallback = function() {
        window.isRecaptchaLoaded = true;
        if (typeof window.renderRecaptcha === 'function') {
            window.renderRecaptcha();
        }
    };

    if (window.isRecaptchaLoaded && typeof grecaptcha !== 'undefined') {
        window.renderRecaptcha();
    }

    // Init icon based on current theme
    const initialTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    updateIcon(initialTheme);

    themeBtn.addEventListener('click', () => {
        let currentTheme = document.documentElement.getAttribute('data-theme');
        let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);

        if (window.isRecaptchaLoaded && typeof grecaptcha !== 'undefined') {
            window.renderRecaptcha(newTheme);
        }
    });
</script>
