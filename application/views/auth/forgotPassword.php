<section class="login-wrapper">
    <div class="login-card">
        <!-- Left Side -->
        <div class="login-left">
            <div class="login-animation"></div>
            <div class="login-left-content brand-logo">
                <img src="<?= base_url('public/image/icon-white--300.png'); ?>" alt="Digi Logo" class="img-fluid" style="width: 42px; height: 42px;z-index: 99;">
                <span class="m-0 font-weight-bold h3">Admin</span>
            </div>
            
            <div class="login-left-content mt-auto">
                <div class="quote-text">
                    "Secure and reliable<br>account recovery."
                </div>
            </div>
        </div>
        
        <!-- Right Side -->
        <div class="login-right">
            <h1 class="login-title">Forgot Password?</h1>
            <p class="login-subtitle">No worries! Enter your email address and we'll send you a link to reset your password.</p>
            
            <div class="text-center">
                <?= $this->session->flashdata('message'); ?>
            </div>

            <form class="user" method="post" action="<?= base_url('auth/forgot-password'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="form-group-custom">
                    <label class="form-label-custom">Email Address</label>
                    <input type="email" class="form-control-custom" id="email" name="email" placeholder="example@email.com" value="<?= set_value('email'); ?>" autofocus required>
                    <?= form_error('email', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                </div>

                <button type="submit" class="btn-login mt-4">
                    Send Reset Link
                </button>
            </form>

            <div class="text-center mt-4 btm-links">
                <a href="<?= base_url('auth'); ?>" class="text-purple font-weight-bold" style="text-decoration: none;">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</section>
