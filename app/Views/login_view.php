<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="loginRegister">
        <form action="/ecommerce/signin" method="POST">
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
                    <input type="text" name="email" class="form-control" placeholder="Please enter your email">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="loginRegister__input">
                    <label for="">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Please enter your password">
                    <div class="invalid-feedback"></div>
                </div>
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                <input type="submit" name="login" value="LOG IN" class="btn hover">
            </div>
        </form>
    </div>

<?=$this->endSection()?>