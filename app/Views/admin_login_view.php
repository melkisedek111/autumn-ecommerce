<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="loginRegister">
        <form action="/user/process" method="POST" id="loginForm">
            <div class="loginRegister__body">
                <?php if(@session()->has('alert')): ?>
                    <div class="alertFixed <?= @session()->get('class'); ?>">
                        <h3><?= @session()->get('head'); ?></h3>
                        <p><?= @session()->get('message'); ?></p>
                    </div>
                <?php endif; ?>
                <h3>Admin Login Page</h3>
                <div class="break1"></div>
                <div class="loginRegister__input">
                    <label for="">Email Address</label>
                    <input type="text" name="email" id="email" class="form-control <?= session()->has('login_error_email') ? "error" : ""; ?>" placeholder="Please enter your email" value="<?= @session()->get('login_value_email'); ?>" data-validate-required-message="Email is empty">
                    <div class="invalid-feedback"><?= @session()->get('login_error_email'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?= session()->has('login_error_password') ? "error" : ""; ?>" placeholder="Please enter your password" value="<?= @session()->get('login_value_password'); ?>" data-validate-required-message="Password is empty">
                    <div class="invalid-feedback"><?= @session()->get('login_error_password'); ?></div>
                </div>
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                <input type="submit" name="login" value="LOG IN" class="btn hover">
            </div>
        </form>
    </div>
    <script>
        
    </script>
<?=$this->endSection()?>