<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="address">
        <div class="address__info">
                <div class="">
                    <h1>Shipping Information</h1>
                    <form action="/user/set_address_process" method="POST" id="setAddressForm">
                    <div>
                        <div  class="form-group">
                            <label for="">Address</label>
                            <input type="text" name="address" class="form-control <?= session()->has('set_address_error_address') ? "error" : ""; ?>" placeholder="Enter your address" value="<?= @session()->get('set_address_value_address'); ?>" data-required-message="Address is required">
                            <div class="invalid-feedback"><?= @session()->get('set_address_error_address'); ?></div>
                        </div>
                        <div  class="form-group">
                            <label for="">Contact Number</label>
                            <input type="text" name="contact" class="form-control <?= session()->has('set_address_error_contact') ? "error" : ""; ?>" placeholder="Enter your contact number e.g 09XXXXXXXXX" value="<?= @session()->get('set_address_value_contact'); ?>" data-required-message="Contact number is required">
                            <div class="invalid-feedback"><?= @session()->get('set_address_error_contact'); ?></div>
                        </div>
                        <div  class="form-group">
                            <label for="">City</label>
                            <input type="text" name="city" class="form-control <?= session()->has('set_address_error_city') ? "error" : ""; ?>" placeholder="Enter your city" value="<?= @session()->get('set_address_value_city'); ?>" data-required-message="City is required">
                            <div class="invalid-feedback"><?= @session()->get('set_address_error_city'); ?></div>
                        </div>
                        <div  class="form-group">
                            <label for="">Province</label>
                            <input type="text" name="province" class="form-control <?= session()->has('set_address_error_province') ? "error" : ""; ?>" placeholder="Enter your state" value="<?= @session()->get('set_address_value_province'); ?>" data-required-message="Province is required">
                            <div class="invalid-feedback"><?= @session()->get('set_address_error_province'); ?></div>
                        </div>
                        <div  class="form-group">
                            <label for="">Zipcode</label>
                            <input type="text" name="zipcode" class="form-control <?= session()->has('set_address_error_zipcode') ? "error" : ""; ?>" placeholder="Enter your zipcode" value="<?= @session()->get('set_address_value_zipcode'); ?>" data-required-message="Zipcode is required">
                            <div class="invalid-feedback"><?= @session()->get('set_address_error_zipcode'); ?></div>
                        </div>
                    </div>
                    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                    <input type="submit" name="set_address" class="btn hover" value="Submit">
                    </form>
                </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            
            $(document).on('submit', '#setAddressForm', function(e) {
                let error = [];
                const data = {}
                e.preventDefault();
                $('.form-control').each((idx, element) => {
                    if($(element).val() == '') {
                        validationError($(element), $(element).attr('data-required-message'), error);
                    } else {
                        data[$(element).attr('name')] =$(element).val();
                        revertField($(element));
                    }
                });
                data['set_address'] = true;
                if(!error.length) {
                    loading();
                    const response = ajax(data, '/user/set_address_process');
                    response.done(e => {
                        if(e.internalValidationError) {
                            alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                        } else {
                            if(e.isAddressSet) {
                                alertMessage('Your shipping information has been set', 'alertSuccess');
                                setTimeout(() => {
                                    window.location.href = "/";
                                }, 2000);
                            }
                        }
                    });
                }
            });
        });
    </script>
<?=$this->endSection()?>