<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="checkout">
            <div class="checkout__bag">
                <h1>Shopping Bag Summary</h1>
                <div class="tableContainer">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <img src="/assets/product_uploads/<?= $item->image; ?>" alt="<?= $item->product_name; ?>">
                                    </td>
                                    <td>
                                        <h3><?= $item->product_name; ?></h3>
                                    </td>
                                    <td>
                                        <h3>$<?= $item->product_price; ?></h3>
                                    </td>
                                    <td>
                                        <h3><?= $item->quantity; ?></h3>
                                    </td>
                                    <td>
                                        <h3>$<?= $item->quantity * $item->product_price; ?></h3>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                           
                        </tbody>
                    </table>
                </div>
                <div class="totalDetails">
                    <h1>Total: <span>$
                        <?= array_reduce($cart_items, function($accu, $curr) {
                                $accu += $curr->product_price * $curr->quantity;
                                return $accu;
                            });     
                        ?></span></h1>
                </div>
            </div>
            <div class="checkout__info">
                <h1>Payment Info</h1>
                <form action="/checkout/checkout_process" method="POST" id="payment-form">
                <div class="checkout__info--details">
                    <div class="shippingInfo">
                        <h2>Shipping Information</h2>
                        <div>
                            <h3 id="first_name">First Name: <span><?= session()->get('user')->first_name; ?></span></h3>
                            <h3 id="last_name">Last Name: <span><?= session()->get('user')->last_name; ?></span></h3>
                            <h3 id="address">Address: <span><?= $user_shipping_address->address ?></span></h3>
                            <h3 id="contact">Contact: <span><?= $user_shipping_address->contact ?></span></h3>
                            <h3 id="city">City: <span><?= $user_shipping_address->city ?></span></h3>
                            <h3 id="province">Province: <span><?= $user_shipping_address->province ?></span></h3>
                            <h3 id="zipcode">Zipcode: <span><?= $user_shipping_address->zipcode ?></span></h3>
                        </div>
                    </div>
                    <div class="billingInfo">
                        <h2>Billing Information</h2>
                       <div class="shippingCheckbox">
                            <input type="checkbox" name="useShippingInfoAsBilling" id="useShippingInfoAsBilling" class="checkmark">
                            <label for="useShippingInfo">Same as shipping</label>
                       </div>
                        <div>
                            <div>
                                <label for="">First Name</label>
                                <input type="text" name="first_name" class="form-input <?= session()->has('checkout_error_first_name') ? "error" : ""; ?>" placeholder="Enter your first name" value="<?= @session()->get('checkout_value_first_name'); ?>">
                                <input type="hidden" >
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_first_name'); ?></div>
                            </div>
                            <div>
                                <label for="">Last Name</label>
                                <input type="text" name="last_name" class="form-input <?= session()->has('checkout_error_last_name') ? "error" : ""; ?>" placeholder="Enter your last name" value="<?= @session()->get('checkout_value_last_name'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_last_name'); ?></div>

                            </div>
                            <div>
                                <label for="">Address</label>
                                <input type="text" name="address" class="form-input <?= session()->has('checkout_error_address') ? "error" : ""; ?>" placeholder="Enter your address" value="<?= @session()->get('checkout_value_address'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_address'); ?></div>
                            </div>
                            <div>
                                <label for="">Contact</label>
                                <input type="text" name="contact" class="form-input <?= session()->has('checkout_error_contact') ? "error" : ""; ?>" placeholder="Enter your contact" value="<?= @session()->get('checkout_value_contact'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_contact'); ?></div>
                            </div>
                            <div>
                                <label for="">City</label>
                                <input type="text" name="city" class="form-input <?= session()->has('checkout_error_city') ? "error" : ""; ?>" placeholder="Enter your city" value="<?= @session()->get('checkout_value_city'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_city'); ?></div>

                            </div>
                            <div>
                                <label for="">Province</label>
                                <input type="text" name="province" class="form-input <?= session()->has('checkout_error_province') ? "error" : ""; ?>" placeholder="Enter your state" value="<?= @session()->get('checkout_value_province'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_province'); ?></div>

                            </div>
                            <div>
                                <label for="">Zipcode</label>
                                <input type="text" name="zipcode" class="form-input <?= session()->has('checkout_error_zipcode') ? "error" : ""; ?>" placeholder="Enter your zipcode" value="<?= @session()->get('checkout_value_zipcode'); ?>">
                                <div class="invalid-feedback"><?= @session()->get('checkout_error_zipcode'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="cardInfo">
                        <h2>Card Information</h2>
                        <div>
                            <div>
                                <label for="">Card Number</label>
                                <div id="card-number"><!--Stripe.js injects the Card Element--></div>
                                <div id="card-number-errors"></div>
                            </div>
                            <div>
                                <label for="">Card CVC</label>
                                <div id="card-cvc"><!--Stripe.js injects the Card Element--></div>
                                <div id="card-cvc-errors"></div>
                            </div>
                            <div>
                                <label for="">Card Expiration</label>
                                <div id="card-exp"><!--Stripe.js injects the Card Element--></div>
                                <div id="card-exp-errors"></div>
                            </div>
                            <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                            <input type="submit" class="btn hover" value="CHECKOUT" <?= count($cart_items) ? '' : 'disabled'; ?>>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script>
            $(document).ready(function() {
                
                $(document).on('click', '#useShippingInfoAsBilling', function() {
                    const ifShippingInfoChecked = $('#useShippingInfoAsBilling:checkbox:checked').length;
                    if(ifShippingInfoChecked) {
                        $('.form-input').each(function() {
                            const name = $(this).attr('name');
                            const value = $(`#${name} span`).html();
                            $(this).val(value);
                            $(this).attr('readonly', true);
                        });
                    } else {
                        $('.form-input').each(function() {
                            $(this).val('');
                            $(this).attr('readonly', false);
                        });
                    }
                });

                var stripe = Stripe('pk_test_51Guub0ESprxiEf1pGomNfB1hTwrbZDQfMlQyNztkIrTuJHx2O6fF11UrgG080nd9jZY4PppmkBuOKkcNWe4GBlSI00zvGQdzYN');

                // Create an instance of Elements
                var elements = stripe.elements();

                // Custom styling can be passed to options when creating an Element.
                // (Note that this demo uses a wider set of styles than the guide below.)


                // Style button with BS
                // document.querySelector('#checkout').classList = 'btn btn-primary btn-block mt-4';

                // Create an instance of the card Element
                var cardNumber = elements.create('cardNumber', {
                    classes:{
                        base: "base"
                    }
                });
                var cardCvc = elements.create('cardCvc', {
                    classes:{
                        base: "base"
                    }
                });
                var cardExpiry = elements.create('cardExpiry', {
                    classes:{
                        base: "base"
                    }
                });
                // Add an instance of the card Element into the `card-element` <div>
                cardNumber.mount('#card-number');
                cardCvc.mount('#card-cvc');
                cardExpiry.mount('#card-exp');

                // Handle real-time validation errors from the card Element.
                cardNumber.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-number-errors');
                    displayError.style.color = 'red';
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
                cardCvc.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-cvc-errors');
                    displayError.style.color = 'red';
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                    });
                    cardExpiry.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-exp-errors');
                    displayError.style.color = 'red';

                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });

                // Handle form submission
                var form = document.getElementById('payment-form');
                form.addEventListener('submit', function(event) {

                    event.preventDefault();
                    stripe.createToken(cardNumber).then(function(result) {
                        if (result.error) {
                        // Inform the user if there was an error
                        var errorElement = document.getElementById('card-number-errors');
                        errorElement.textContent = result.error.message;
                        } else {
                        // Send the token to your server
                        stripeTokenHandler(result.token);
                        }
                    });
                });

                function stripeTokenHandler(token) {
                    // Insert the token ID into the form so it gets submitted to the server
                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);

                    // Submit the form
                    form.submit();
                }
            });

    </script>
<?=$this->endSection()?>