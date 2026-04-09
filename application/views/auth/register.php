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
                    "Join our network and<br>start growing today."
                </div>
            </div>
        </div>
        
        <!-- Right Side -->
        <div class="login-right">
            <h1 class="login-title">Create Account</h1>
            <p class="login-subtitle">Fill in the details below to create your administrator account.</p>
            
            <form class="user" method="post" action="<?= base_url('auth/register'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="form-group-custom">
                    <label class="form-label-custom">Full Name</label>
                    <input type="text" class="form-control-custom" id="name" name="name" placeholder="Enter your full name" value="<?= set_value('name'); ?>" autofocus required>
                    <?= form_error('name', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Email Address</label>
                    <input type="email" class="form-control-custom" id="email" name="email" placeholder="example@email.com" value="<?= set_value('email'); ?>" required>
                    <?= form_error('email', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group-custom">
                            <label class="form-label-custom">Password</label>
                            <input type="password" class="form-control-custom" id="password1" name="password1" placeholder="••••••••" required>
                            <?= form_error('password1', '<small class="text-danger mt-1 d-block font-weight-bold">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group-custom">
                            <label class="form-label-custom">Repeat Password</label>
                            <input type="password" class="form-control-custom" id="password2" name="password2" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-login mt-3">
                    Register Now
                </button>
            </form>

            <div class="text-center mt-4 btm-links">
                Already have an account? 
                <a href="<?= base_url('auth'); ?>" class="text-purple font-weight-bold" style="text-decoration: none;">
                    Login here
                </a>
            </div>
        </div>
    </div>
</section>
