<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="loginRegister">
        <form action="/user/process" method="POST">
            <div class="loginRegister__heading">
                <a href="/register">
                    <h5>Register Customer</h5>
                    <div class="break  break2"></div>
                </a>
                <a href="/login">
                    <h5>Login Customer</h5>
                    <div class="break"></div>
                </a>
            </div>
            <div class="loginRegister__body">
                <h3>Create an account</h3>
                <div class="break1"></div>
                <div class="loginRegister__input">
                    <label for="">Email Address <span>*</span></label>
                    <input type="text" name="email" class="form-control <?= session()->has('register_error_email') ? "error" : ""; ?>" placeholder="Please enter your email">
                    <p>We will send messages to the above email address. Please ensure the email address is accessible and up-to-date.</p>
                    <div class="invalid-feedback"><?= @session()->get('register_error_email'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Password <span>*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Please enter your password">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Confirm Password <span>*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Please confirm your password">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">First Name <span>*</span> <small>-what should we call you?</small></label>
                    <input type="text" name="first_name" class="form-control" placeholder="Please enter your first name">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Last Name <span>*</span> <small>-what should we call you?</small></label>
                    <input type="text" name="last_name" class="form-control" placeholder="Please enter your last name">
                    <div class="invalid-feedback"></div>
                </div>
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                <input type="submit" name="register" value="SIGN UP" class="btn hover">
            </div>
        </form>
    </div>
<?=$this->endSection()?>