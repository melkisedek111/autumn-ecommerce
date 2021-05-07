<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="product">
            <div class="product__breadcrumb">
                <a href="/shop">Shop</a> <span> / </span>
                <h4 href=""><?= $products['details']->category_name; ?></h4> <span> / </span>
                <h4 href=""><?= $products['details']->name; ?></h4>
            </div>
            <div class="product__view">
                <div class="product__view--imageList">
                    <div>
                        <?php foreach($products['images'] as $image): ?>
                            <img src="/assets/product_uploads/<?= $image->image; ?>" alt="<?= $image->image; ?>" class="productImage">
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="product__view--mainImage">
                    <img src="/assets/product_uploads/<?= $products['details']->image ?>" alt="<?= $products['details']->name; ?>" id="productMainImage">.attr('src', )
                </div>
                <div class="product__view--details">
                    <small>Autumn</small>
                    <h1><?= $products['details']->name; ?></h1>
                    <h2>$<?= $products['details']->price; ?></h2>
                    <h3>Available: <span><?= $products['details']->stock_quantity; ?></span> in stock</h3>
                    <p><?= $products['details']->description; ?></p>
                    <div>
                        <div class="product__view--setQuantity">
                            <span id="deductQuantity" data-set-quantity="deduct">&minus;</span>
                            <p id="productQuantity">1</p>
                            <span id="addQuantity" data-set-quantity="add">&plus;</span>
                        </div>
                        <?php if(session()->has('user')): ?>
                            <button class="btn hover" id="addToCart" data-product-id="<?= $products['details']->product_id; ?>">Add To Cart</button>
                        <?php else: ?>
                            <a href="/login" class="btn hover">Add To Cart</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="product__reviews">
                <h1>3 reviews for <?= $products['details']->name; ?></h1>
                <div class="product__reviews--lists">
                    <div>
                        <div class="reviewUserImage">
                            <img src="https://cdn0.iconfinder.com/data/icons/user-pictures/100/unknown2-512.png" alt="">
                        </div>
                        <div class="reviewDetails">
                            <h3>Daniel Vandaft - <span>January 7, 2020</span></h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates porro explicabo maiores laboriosam sapiente modi consectetur, numquam similique praesentium quidem voluptatem facere veniam. Quod fuga blanditiis molestiae quibusdam omnis. Quos? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab illum dolores ullam, sit ea excepturi pariatur voluptates iure amet rem minus tenetur quidem expedita? Animi incidunt voluptate ducimus numquam deleniti?</p>
                        </div>
                    </div>
                    <div>
                        <div class="reviewUserImage">
                            <img src="https://images.pexels.com/photos/4344614/pexels-photo-4344614.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                        </div>
                        <div class="reviewDetails">
                            <h3>Daniel Vandaft - <span>January 7, 2020</span></h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates porro explicabo maiores laboriosam sapiente modi consectetur, numquam similique praesentium quidem voluptatem facere veniam. Quod fuga blanditiis molestiae quibusdam omnis. Quos? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab illum dolores ullam, sit ea excepturi pariatur voluptates iure amet rem minus tenetur quidem expedita? Animi incidunt voluptate ducimus numquam deleniti?</p>
                        </div>
                    </div>
                    <div>
                        <div class="reviewUserImage">
                            <img src="https://images.pexels.com/photos/2434165/pexels-photo-2434165.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                        </div>
                        <div class="reviewDetails">
                            <h3>Daniel Vandaft - <span>January 7, 2020</span></h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates porro explicabo maiores laboriosam sapiente modi consectetur, numquam similique praesentium quidem voluptatem facere veniam. Quod fuga blanditiis molestiae quibusdam omnis. Quos? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab illum dolores ullam, sit ea excepturi pariatur voluptates iure amet rem minus tenetur quidem expedita? Animi incidunt voluptate ducimus numquam deleniti?</p>
                        </div>
                    </div>
                </div>
                <div class="product__reviews--write">
                    <?php if(session()->has('user')): ?>
                        <div class="writeReview">
                            <div>
                                <h1>Write your review:</h1>
                                <form action="">
                                    <textarea name="" id="" cols="30" rows="50" class="form-control textArea-control" placeholder="Write your review here!"></textarea>
                                    <input type="text" class="btn hover" value="Submit Review">
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="noUserReview">
                            <h1>Want to review this Product?</h1>
                            <div>
                                <div>
                                    <h4>have already an account?</h4>
                                    <a href="" class="btn hover">Login</a>
                                </div>
                                <div>
                                    <h4>do not have an account?</h4>
                                    <a href="" class="btn hover-invert">Register</a>
                                </div>
                            </div>
                    </div>
                    <?php endif; ?>
                 

                </div>
            </div>
        </div>
<script>
    $(document).ready(function() {
        let quantity = $('#productQuantity').html();
        $(document).on('click', '#deductQuantity, #addQuantity', function() {
            const set = $(this).attr('data-set-quantity');
            if(set == 'deduct') {
                if(quantity > 1) {
                    quantity--
                    $('#productQuantity').html(quantity)
                }
            } else if (set == 'add') {
                quantity++
                $('#productQuantity').html(quantity)
            }(quantity);
        });
        $(document).on('click', '#addToCart', function() {
            const productId = $(this).attr('data-product-id');
            const data = {
                product_id: sanitizeHtml(productId),
                quantity: sanitizeHtml(quantity)
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
                        $('#productQuantity').html(1);
                    }
                    if(e.error) {
                        alertMessage(e.error, 'alertDanger');
                    }
                }
            });
        });
        $(document).on('click', '.productImage', function() {
            const productSrc = $(this).attr('src');
            $('#productMainImage').attr('src', productSrc);
        });
    });
</script>
<?=$this->endSection()?>