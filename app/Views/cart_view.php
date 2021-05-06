<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="cart">
            <div>
                <h1>Here is What you Getting!</h1>
            </div>
            <div class="cart__table">
                <h2>You have <span id="cartTotalItem"><?= array_reduce($cart_items, function($carry, $item){
                    $carry += $item->quantity;
                    return $carry;
                }); ?></span> items in your bag:</h2>
                <?php if(@session()->has('alert')): ?>
                    <div class="alertFixed <?= @session()->get('class'); ?>">
                        <h3><?= @session()->get('head'); ?></h3>
                        <p><?= @session()->get('message'); ?></p>
                    </div>
                <?php endif; ?>
               <div class="cart__table--container">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $items): ?>
                                <tr>
                                <td>
                                    <img src="/assets/product_uploads/<?= $items->image ?>" alt="">
                                </td>
                                <td>
                                    <h2><?= $items->product_name ?></h2>
                                    <h4>Category: <span><?= $items->category_name; ?></span></h4>
                                    <h4>Brand: <span><?= $items->brand_name; ?></span></h4>
                                </td>
                                <td>
                                    <h2>$<?= $items->price; ?></h2>
                                </td>
                                <td>
                                    <div>
                                        <span class="addRemoveCartProcess" data-process="remove" data-product-id="<?= $items->product_id; ?>" data-id="<?= $items->cart_id; ?>">&minus;</span>
                                        <h2 id="cartTotalQuantity_<?= $items->cart_id; ?>"><?= $items->quantity; ?></h2>
                                        <span class="addRemoveCartProcess" data-process="add" data-product-id="<?= $items->product_id; ?>" data-id="<?= $items->cart_id; ?>">&plus;</span>
                                    </div>
                                </td>
                                <td>
                                    <h2 id="cartProductTotal_<?= $items->cart_id; ?>">$<?= $items->price * $items->quantity; ?></h2>
                                </td>
                                <td>
                                    <form action="/cart/delete_cart_item" method="POST">
                                        <div>
                                            <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                                            <input type="hidden" name="cart_id" value="<?= $items->cart_id; ?>">
                                            <input type="submit" name="delete_item" class="btn hover" value="Remove">
                                        </div>                            
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
               </div>
              <div class="misc">
                <a href="" class="btn hover-invert">Go to Checkout</a>
              </div>
            </div>
        </div>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.addRemoveCartProcess', function() {
                const cart_id = $(this).attr('data-id');
                const product_id = $(this).attr('data-product-id');
                const process = $(this).attr('data-process');
                const data = {
                    cart_id: sanitizeHtml(cart_id),
                    product_id: sanitizeHtml(product_id),
                    process: sanitizeHtml(process),
                };
                const response = ajax(data, '/cart/add_remove_item_process');
                response.done(e => {
                    refreshToken(e);
                    const itemToUpdate = e.cart.filter(data => data.cart_id == cart_id)[0];
                    $(`#cartProductTotal_${cart_id}`).html('$'+itemToUpdate.product_price * itemToUpdate.quantity);
                    $(`#cartTotalQuantity_${cart_id}`).html(itemToUpdate.quantity);
                    $(`#cartTotalItem`).html(e.cart.reduce((acc, curr) => acc + parseInt(curr.quantity), 0));
                    alertMessage(`Item has been ${process} (1)`, 'alertSuccess');
                    // alertAddCart(e.cart, productId);
                })
            });
        }); 
    </script>
<?=$this->endSection()?>