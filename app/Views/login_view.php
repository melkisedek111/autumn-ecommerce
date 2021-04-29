<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="loginRegister">
        <form action="/user/process" method="POST" id="loginForm">
            <div class="loginRegister__heading">
                <a href="/register">
                    <h5>Register Customer</h5>
                    <div class="break "></div>
                </a>
                <a href="/login">
                    <h5>Login Customer</h5>
                    <div class="break break2"></div>
                </a>
            </div>
            <div class="loginRegister__body">
                <?php if(@session()->has('alert')): ?>
                    <div class="alertFixed <?= @session()->get('class'); ?>">
                        <h3><?= @session()->get('head'); ?></h3>
                        <p><?= @session()->get('message'); ?></p>
                    </div>
                <?php endif; ?>
                <h3>Login to your Account</h3>
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
        $(document).ready(function() {
            $(document).on('submit', '#loginForm', function(e) {
                e.preventDefault();
                const error = [];
                $('.form-control').each(function() {
                    if($(this).val() !== '') {
                        $(this).parent().find('.invalid-feedback').html(''); // --> invalid-feedback element will be empty if input is not empty
                        $(this).removeClass('error');
                    } else {
                        validationError($(this), $(this).attr('data-validate-required-message'), error);
                    }
                });
                if(!error.length) {
                    const data = {
                        email: $('#email').val(),
                        password: $('#password').val(),
                        login: true,
                        ajaxLogin: true,
                    };
                    const response = ajax(data, '/user/process');
                    response.done(e => {
                        loading();
                        token[e.token.name] = e.token.value;
                        if(e.internalValidationError) {
                            alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                        } else {
                            if(e.loginSuccess) {
                                setTimeout(() => {
                                    alertMessage('You will now redirected to home page!', 'alertSuccess');
                                    setTimeout(() => {
                                        window.location.href = "/";
                                    }, 2000);
                                }, 3000);
                            } else {
                                validationError($('#email'), 'Username or password does not matched!', error);
                            }
                        }
                    })
                }
            })
        });
    </script>
<?=$this->endSection()?>