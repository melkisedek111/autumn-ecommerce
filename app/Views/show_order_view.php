<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="adminProductOrder">
        <h1>Order Summary: ID #<?= $order_details->id; ?></h1> 
        <div class="adminProductOrder__details">
            <div class="adminProductOrder__details--shippingInfo">
                <h2>Shipping Information</h2>
                <div>
                    <div class="nameHolder">
                        <div class="textHolder">
                            <small>FIRST NAME</small>
                            <h3><?= $address_details->first_name; ?></h3>
                        </div>
                        <div class="textHolder" >
                            <small>LAST NAME</small>
                            <h3><?= $address_details->last_name; ?></h3>
                        </div>
                    </div>
                    <div class="textHolder">
                        <small>EMAIL ADDRESS</small>
                        <h3><?= $address_details->email; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>PHONE NUMBER</small>
                        <h3><?= $address_details->contact; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>ADDRESS</small>
                        <h3><?= $address_details->address; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>CITY</small>
                        <h3><?= $address_details->city; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>STATE</small>
                        <h3><?= $address_details->province; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>ZIPCODE</small>
                        <h3><?= $address_details->zipcode; ?></h3>
                    </div>
                </div>
            </div>
            <div class="adminProductOrder__details--billingInfo">
                <h2>Billing Information</h2>
                <div>
                    <div class="nameHolder">
                        <div class="textHolder">
                            <small>FIRST NAME</small>
                            <h3><?= $order_details->first_name; ?></h3>
                        </div>
                        <div class="textHolder" >
                            <small>LAST NAME</small>
                            <h3><?= $order_details->last_name; ?></h3>
                        </div>
                    </div>
                    <div class="textHolder">
                        <small>PHONE NUMBER</small>
                        <h3><?= $order_details->contact; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>ADDRESS</small>
                        <h3><?= $order_details->address; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>CITY</small>
                        <h3><?= $order_details->city; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>STATE</small>
                        <h3><?= $order_details->province; ?></h3>
                    </div>
                    <div class="textHolder">
                        <small>ZIPCODE</small>
                        <h3><?= $order_details->zipcode; ?></h3>
                    </div>
                </div>
            </div>
            <div class="adminProductOrder__details--itemSummary">
                <h2>Order Items</h2>
                <div>
                    <?php foreach($order_products_details as $item): ?>
                        <div>
                            <img src="/assets/product_uploads/<?= $item->image; ?>" alt="<?= $item->product_name; ?>">
                            <div>
                                <h3><?= $truncate($item->product_name, 6); ?></h3>
                                <small>Brand: <span><?= $item->brand_name; ?></span></small>
                                <small>Category: <span><?= $item->category_name; ?></span></small>
                            </div>
                            <div>
                                <h3>$<?= $item->product_price; ?></h3>
                                <small>Quantity: <span><?= $item->quantity; ?></span></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <h1>Total: $<?= $order_details->total_amount; ?></h1>
                <h3 class="<?= $order_status; ?>">Status: <span><?= $order_details->order_status; ?></span></h3>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {});

    </script>
<?=$this->endSection()?>