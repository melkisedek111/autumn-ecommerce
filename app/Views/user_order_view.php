<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="order">
        <h1>My Orders</h1>
        <div class="order__lists">
            <?php if(@$order_details != null): ?>
                <?php foreach($order_details as $order): ?>
                <div class="order__lists--list">
                    <div class="orderHeader">
                        <div>
                            <h3>Order: #<?= $order->order_id; ?> </h3>
                            <h5>Placed on <?= $order->order_created; ?></h5>
                        </div>
                        <h3>Total: $<?= $order->total_amount; ?></h3>
                    </div>
                    <?php foreach($order_product_lists[$order->order_id] as $product): ?>
                        <div class="orderDetais">
                            <img src="/assets/product_uploads/<?= $product->image ?>" alt="<?= $product->product_name; ?>">
                            <h2><?= $product->product_name; ?></h2>
                            <h3>Quantity: <span><?= $product->quantity; ?></span></h3>
                            <h3 class="orderStatus <?= $set_status($order->order_status); ?>"><?= $order->order_status; ?></h3>
                            <h3>$<?= $product->product_price; ?></h3>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
<script>
    $(document).ready(function() {
        
    });
</script>
<?=$this->endSection()?>