<?=$this->extend("master_view")?>
<?=$this->section("content")?>
    <div class="checkoutSuccess">
    <div class="order_success">
        <h1>Payment successfull!</h1>
        <h1><span class="fa fa-check-circle"></span></h1>
        <div class="order_info">
            <div>
                <h5>Payment Type</h5>
                <h5><span><?= strtoupper(@$payment_data['type']) ?></span></h5>
            </div>
            <div>
                <h5>Card Type</h5>
                <h5><span><?= strtoupper(@$payment_data['brand']) ?></span></h5>
            </div>
            <div>
                <h5>Email</h5>
                <h5><span><?= strtoupper(@$payment_data['email']) ?></span></h5>
            </div>
            <div>
                <h5>Full Name</h5>
                <h5><span><?= strtoupper(@$payment_data['full_name']) ?></span></h5>
            </div>
            <div>
                <h5>Address</h5>
                <h5><span><?= strtoupper(@$payment_data['address']) ?></span></h5>
            </div>
            <div>
                <h5>City</h5>
                <h5><span><?= strtoupper(@$payment_data['city']) ?></span></h5>
            </div>
            <div>
                <h5>Province</h5>
                <h5><span><?= strtoupper(@$payment_data['province']) ?></span></h5>
            </div>
            <div>
                <h5>Zipcode</h5>
                <h5><span><?= strtoupper(@$payment_data['zipcode']) ?></span></h5>
            </div>
            <div>
                <h5>Created</h5>
                <h5><span><?= date("F j, Y, g:i a", @$payment_data['created']); ?></span></h5>
            </div>
        </div>
        <div class="order_amount">
            <div>
                <h3>Amount</h3>
                <h3><span>$<?= @$payment_data['amount_captured']; ?></span></h3>
            </div>
            <div>
                <h3>Transaction ID</h3>
                <h3><span><?= strtoupper(@$payment_data['transaction_id']) ?></span></h3>
            </div>
            <div>
                <a href="">print</a>
                <a href="/shop">Go shopping</a>
            </div>
        </div>
    </div>
    </div>
   
    <script>
        $(document).ready(function() {});

    </script>
<?=$this->endSection()?>