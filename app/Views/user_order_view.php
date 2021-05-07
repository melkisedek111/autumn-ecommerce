<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="order">
        <h1>My Orders</h1>
        <div class="order__lists">
            <div class="order__lists--list">
                <div class="orderHeader">
                    <div>
                        <h3>Order: 345352337632092</h3>
                        <h5>Placed on 25 Dec 2020 11:34:16</h5>
                    </div>
                    <h3>Total: $785.55</h3>
                </div>
                <div class="orderDetais">
                    <img src="https://images.pexels.com/photos/2529157/pexels-photo-2529157.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                    <h2>Lactote Designer Bag Limited Edition</h2>
                    <h3>Quantity: <span>1</span></h3>
                    <h3 class="orderStatus">delivered</h3>
                    <h3>$45.55</h3>
                </div>
                <div class="orderDetais">
                    <img src="https://images.pexels.com/photos/2529157/pexels-photo-2529157.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                    <h2>Lactote Designer Bag Limited Edition</h2>
                    <h3>Quantity: <span>1</span></h3>
                    <h3 class="orderStatus">delivered</h3>
                    <h3>$45.55</h3>
                </div>
            </div>
            <div class="order__lists--list">
                <div class="orderHeader">
                    <div>
                        <h3>Order: 345352337632092</h3>
                        <h5>Placed on 25 Dec 2020 11:34:16</h5>
                    </div>
                    <h3>Total: $785.55</h3>
                </div>
                <div class="orderDetais">
                    <img src="https://images.pexels.com/photos/2529157/pexels-photo-2529157.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                    <h2>Lactote Designer Bag Limited Edition</h2>
                    <h3>Quantity: <span>1</span></h3>
                    <h3 class="orderStatus">delivered</h3>
                    <h3>$45.55</h3>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function() {
        
    });
</script>
<?=$this->endSection()?>