<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="loginRegister">
        <form action="/user/process" method="POST" data-parsley-validate="" id="registerForm">
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
                    <input type="text" name="email" id="email" class="form-control <?= session()->has('register_error_email') ? "error" : ""; ?>" placeholder="Please enter your email" value="<?= @session()->get('register_value_email'); ?>" data-validate-required-message="Email is required">
                    <p>We will send messages to the above email address. Please ensure the email address is accessible and up-to-date.</p>
                    <div class="invalid-feedback"><?= @session()->get('register_error_email'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Password <span>*</span></label>
                    <input type="password" name="password" id="password" class="form-control <?= session()->has('register_error_password') ? "error" : ""; ?>" placeholder="Please enter your password" value="<?= @session()->get('register_value_password'); ?>"  data-validate-required-message="Password is required" >
                    <div class="invalid-feedback"><?= @session()->get('register_error_password'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Confirm Password <span>*</span></label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control <?= session()->has('register_error_confirm_password') ? "error" : ""; ?>" placeholder="Please confirm your password" value="<?= @session()->get('register_value_confirm_password'); ?>"  data-validate-required-message="Confirm password is required" data-parsley-equalto-message="Comfirm password does not matched!">
                    <div class="invalid-feedback"><?= @session()->get('register_error_confirm_password'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">First Name <span>*</span> <small>-what should we call you?</small></label>
                    <input type="text" name="first_name" id="first_name" class="form-control <?= session()->has('register_error_first_name') ? "error" : ""; ?>" placeholder="Please enter your first name" value="<?= @session()->get('register_value_first_name'); ?>"  data-validate-required-message="First name is required">
                    <div class="invalid-feedback"><?= @session()->get('register_error_first_name'); ?></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Last Name <span>*</span> <small></small></label>
                    <input type="text" name="last_name" id="last_name" class="form-control <?= session()->has('register_error_last_name') ? "error" : ""; ?>" placeholder="Please enter your last name" value="<?= @session()->get('register_value_last_name'); ?>"  data-validate-required-message="Last name is required">
                    <div class="invalid-feedback"><?= @session()->get('register_error_last_name'); ?></div>
                </div>
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                <input type="submit" name="register" value="SIGN UP" class="btn hover">
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            function emailIsValid (email) {
                // email validation function
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
            
             /**
             * event submit for register form
             */
            $(document).on('submit', '#registerForm', function(e) { 
                e.preventDefault();
                var letters = /^[a-zA-Z\s]*$/;
                const error = [];
                
                $('.form-control').each(function() {// --> class form-control of each input that has been loop and validate
                    if($(this).val() !== '') { // --> if each input is not empty, then it will validate
                        $(this).parent().find('.invalid-feedback').html(''); // --> invalid-feedback element will be empty if input is not empty
                        $(this).removeClass('error');
                        if($(this).attr('name') == 'email'){
                            if(emailIsValid($(this).val())) { // -->  validation for email
                                const response = ajax({register: true, checkEmail: true, email: $(this).val()}, '/user/process'); // --> ajax request for check email duplication
                                response.done(e => { // --> ajax response
                                    token[e.token.name] = e.token.value; // --> refreshin csrf token to make another http request or ajax request
                                    if(e.internalValidationError) {
                                        alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                                    } else {
                                        if(e.isEmailExists) {
                                        validationError($(this), 'Email already exists!', error);
                                        } else {
                                            if(!error.length) {
                                                loading();
                                                const data = {
                                                    email: $('#email').val(),
                                                    password: $('#password').val(),
                                                    first_name: $('#first_name').val(),
                                                    last_name: $('#last_name').val(),
                                                    register: true,
                                                    ajaxRegister: true,
                                                }
                                                const response = ajax(data, '/user/process');
                                                response.done(e => {
                                                    token[e.token.name] = e.token.value
                                                    if(e.registerSuccess) {
                                                        setTimeout(() => {
                                                            alertMessage('You will now redirected to login page!', 'alertSuccess');
                                                            setTimeout(() => {
                                                                window.location.href = "/login";
                                                            }, 2000);
                                                        }, 3000);
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                            } else {
                                validationError($(this), 'Email is invalid!', error);
                            }
                        }
                        if($(this).attr('name') == 'first_name' || $(this).attr('name') == 'last_name') {
                            if(!$(this).val().match(letters)) {
                                validationError($(this), 'This field should be letters only', error);
                            } else if($(this).val().length < 2) {
                                validationError($(this), 'This field should be at least 2 characters', error);
                            }
                        }
                        if($(this).attr('name') == 'password' || $(this).attr('name') == 'confirm_password') {
                            const noSpaceRegex = /^\S*$/;
                            if($(this).val().length < 8) {
                                validationError($(this), 'This field should be at least 8 characters', error);
                            } else if (!$(this).val().match(noSpaceRegex)) {
                                validationError($(this), 'This field should not contain spaces', error);
                            } else if($(this).attr('name') == 'confirm_password') {
                                if($(this).val() !== $('#password').val()) {
                                    validationError($(this), 'Confirm password does not matched!', error);
                                }
                            }
                        }
                    } else {
                        validationError($(this), $(this).attr('data-validate-required-message'), error);
                    }
                });
             });    
        }); 
    </script>
<?=$this->endSection()?>