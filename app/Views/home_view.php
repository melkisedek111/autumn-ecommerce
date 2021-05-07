<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="main">
        <img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="main__background" class="main__image">
        <div class="main__title">
            <h1>AUTUMN</h1>
            <h5>Fashion, Bags Shoes & More</h5>
        </div>
    </div>
    <div class="category">
        <a href="">
            <div class="category__container">
                <img src="https://images.pexels.com/photos/1152077/pexels-photo-1152077.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                <div>
                    <h2>Bags & Backpacks</h2>
                </div>
            </div>
        </a>
        <a href="">
            <div class="category__container">
                <img src="https://images.pexels.com/photos/4869695/pexels-photo-4869695.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                <div>
                    <h2>Ladies Dresses</h2>
                </div>
            </div>
        </a>
        <a href="">
            <div class="category__container">
            <img src="https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
            <div>
                <h2>Shoes & Sneakers</h2>
            </div>
            </div>
        </a>
        <!-- <div class="category__items">
            <a href="#">
                <img src="https://images.pexels.com/photos/1152077/pexels-photo-1152077.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                <h1>Bags & Backpacks</h1>
            </a>
            <a href="">
                <img src="https://images.pexels.com/photos/4869695/pexels-photo-4869695.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                <h1>Ladies Dresses</h1>
            </a>
            <a href="">
                <img src="https://images.pexels.com/photos/965981/pexels-photo-965981.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                <h1>Accessories</h1>
            </a>
        </div>
        <div class="category__items">
            <a href="">
                <img src="https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                <h1>Shoes</h1>
            </a>
            <a href="">
                <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="">
                <h1>Eye Glass Collections</h1>
            </a>
        </div> -->
    </div>
    <div class="divider">
        <h2>Latest Items</h2>
        <div></div>
    </div>
    <div class="latestItems">
        <?php foreach($latest_product as $product): ?>
            <div class="latestItems__container">
                <div class="imageContainer">
                    <a href="">
                        <img src="/assets/product_uploads/<?= $product->image; ?>" alt="<?= $product->name; ?>">
                    </a>
                </div>
                <div class="itemDetails">
                    <div class="itemTitle">
                        <h1 class="itemName"><?= $truncate($product->name, 3); ?></h1>
                        <h4 class="itemCategory"><?= $product->category_name; ?></h4>
                    </div>
                    <div class="itemPrice">
                        <h3>$<?= $product->price; ?></h3>
                        <?php if(session()->has('user')): ?>
                            <span class="far fa-shopping-bag shopAddToCart" data-product-id="<?= $product->product_id ?>"></span>
                        <?php else: ?>
                            <a href="/login" class="far fa-shopping-bag"></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.shopAddToCart', function() {
                const productId = $(this).attr('data-product-id');
                const data = {
                    product_id: sanitizeHtml(productId),
                    quantity: 1
                }
                const response = ajax(data, '/cart/add_to_cart_process');
                response.done(e => {
                    refreshToken(e);
                    if(e.internalValidationError) {
                        alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                    } else {
                        if(e.cart) {
                            update_cart_items(e.cart);
                            alertAddCart(e.cart, productId);
                        }
                        if(e.error) {
                            alertMessage(e.error, 'alertDanger');
                        }
                    }
                });
            });
        });
    </script>
<?=$this->endSection()?>