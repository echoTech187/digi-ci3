<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    
</style>

<section class="login-wrapper">
    <div class="login-card">
        <!-- Left Side -->
        <div class="login-left">
            <div class="login-animation"></div>
            <div class="login-left-content brand-logo">
                <img src="<?= base_url('public/image/icon-white--300.png'); ?>" alt="Digi Logo" class="img-fluid" style="width: 42px; height: 42px;z-index: 99;">
                <span class="m-0 font-weight-bold h3">Admin</span>
            </div>
            
            <div class="login-left-content mt-auto d-none">
                <div class="quote-text">
                    "Sistem lengkap yang<br>saya dan tim butuhkan."
                </div>
                <div class="quote-author">Karen Yue</div>
                <div class="quote-title">Direktur Teknologi Pemasaran Digital</div>
            </div>
        </div>
        
        <!-- Right Side -->
        <div class="login-right">
            
            

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
                <div class="form-group mb-0">
                    <div class="g-recaptcha" data-sitekey="6LcohZQsAAAAABZs36_69j5-9aKaLdewFK05foHx"></div>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>

            
            
        </div>
    </div>
</section>
