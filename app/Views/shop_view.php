<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="shop">
    <div class="shop__filter">
        <div class="shop__search">
            <input type="text" class="form-control" id="searchProduct" placeholder="Search product">
        </div>
        <div class="shop__categories">  
            <h2>Categories</h2>
            <a class="<?= $active == "all" ? "active" : '' ?>" href="/shop">
                <h3>All Products</h3>
                <h3><?= $total_products->total_products ?></h3>
            </a>
            <?php foreach($items_per_category as $category): ?>
                <a class="<?= $active == $category->category_id ? "active" : '' ?>" href="/shop/c_<?= strtolower(str_replace(' ', '-', $category->category_name)); ?>">
                    <h3><?= $category->category_name ?></h3>
                    <h3><?= $category->items_per_category ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="shop__filterBy">
            <div class="shop__filterBy--price">
                <h2>Price</h2>
                <div>
                    <input type="text" class="form-control" placeholder="min" id="minPrice">
                    <input type="text" class="form-control" placeholder="max" id="maxPrice">
                </div>
                <small>Range: $<?= $prices->minPrice ?> - $<?= $prices->maxPrice ?></small>
            </div>
            <!-- <div class="shop__filterBy--size">
                <h2>Size</h2>
                <a href="#" class="activeFilter">
                    <h3>Small</h3>
                    <h3>205</h3>
                </a>
                <a href="#">
                    <h3>Medium</h3>
                    <h3>205</h3>
                </a>
                <a href="#">
                    <h3>Big</h3>
                    <h3>205</h3>
                </a>
            </div> -->
            <div class="shop__filterBy--brands">
                <h2>Brands</h2>
                <?php foreach($items_per_brand as $brand): ?>
                    <a class="<?= $active == $brand->brand_id ? "active" : '' ?>" href="/shop/b_<?= strtolower(str_replace(' ', '-', $brand->brand_name)); ?>">
                        <h3><?= $brand->brand_name ?></h3>
                        <h3><?= $brand->items_per_brand ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="shop__filteredItems">
        <div class="shop__filterOptions">
            <h2><span><?= count($products) ?></span> Products Found</h2>
            <div>
                <h2>Sort By:</h2>
                <select name="sortProducts" id="sortProducts" class="form-control">
                    <option value="">Sort by</option>
                    <option value="DESC">Descending</option>
                    <option value="ASC">Ascending</option>
                    <option value="max_price">Price (max)</option>
                    <option value="min_price">Price (min)</option>
                </select>
            </div>
            <div>
                <span class="far fa-th-large"></span>
                <span class="far fa-bars"></span>
            </div>
        </div>
        <div class="shop__filterResults">
            <?php if($no_items_found): ?>
                <h1>No items found!</h1>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <div class="filterDesignOne">
                        <div class="itemImageContainer">
                            <a href="/products/show/<?= $product->product_id ?>">
                                <img src="/assets/product_uploads/<?= $product->image ?>" alt="<?= $product->name ?>">
                            </a>
                            <div class="addToCart">
                                <h1 class="shopAddToCart" data-product-id="<?= $product->product_id ?>">&plus;</h1>
                            </div>
                        </div>
                        <div class="itemDetails">
                            <h2><?= $truncate($product->name, 6) ?></h2>
                            <h3>$<?= $product->price ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="tablePagination">

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        
    });
    const indicator = "<?= $active; ?>";
        // const totalProductRows = "<?= $total_products_row; ?>";
        const indicatorName = "<?= $indicator; ?>";
        let maxPriceSet = '';
        let minPriceSet = '';
        let rowSet = '';

        $(document).on('change', '#sortProducts', function() {
            console.log(1);
            const data = {
                filterBySort: true,
                // sort: sanitizeHtml($(this).val()),
                // maxPriceSet: sanitizeHtml(maxPriceSet),
                // minPriceSet: sanitizeHtml(minPriceSet),
                // rowSet: sanitizeHtml(rowSet),
            }
            const response = ajax(data, '/shop/filter_products');
            response.done(e => {
                refreshToken(e);
                console.log(e);
            });
        
        });


        let responsePrice = null;
        $(document).on('keypress', '#minPrice, #maxPrice', function(evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 45 || charCode > 46)) {
                return false;
            }
            return true;
        })
        $(document).on('keyup', '#minPrice, #maxPrice', function(evt) {
            const minPrice = $('#minPrice').val();
            const maxPrice = $('#maxPrice').val();
            if(minPrice == '' || maxPrice == '') {
                getNumberPages();
            } else {
                const data = {
                    filterByPrice: true,
                    minPrice: sanitizeHtml(minPrice),
                    maxPrice: sanitizeHtml(maxPrice)
                };
                const execute = function() {
                    if(responsePrice) responsePrice.abort();
                    responsePrice = ajax(data, '/shop/filter_products');
                    responsePrice.done(e => {
                        refreshToken(e);
                        maxPriceSet = maxPrice;
                        minPriceSet = minPrice
                        setProductView(e.products.products);
                    }); 
                }
                execute();
            }

        })
        let responseSearch = null;
        $(document).on('keyup', '#searchProduct', function() {
            if($(this).val() === '') {
                getNumberPages();
            } else {
                const data = {
                    filterByName: true,
                    name: sanitizeHtml($(this).val()),
                };
                const execute = function() {
                    if(responseSearch) responseSearch.abort();
                    responseSearch = ajax(data, '/shop/filter_products');
                    responseSearch.done(e => {
                        refreshToken(e);
                        setProductView(e.products.products);
                    }); 
                }
                execute();
            }
        });
        function truncate(text, limit) {
            if (text.split(' ').length > limit) {
                let words = text.split(' ');
                return words.splice(0, limit).join(' ') + '...'
            }
            return text;
        }

        function getNumberPages() {
            const data = {
                page_number: (1 - 1) * 9,
                indicator_id: indicator == "all" ? '' : sanitizeHtml(indicator),
                indicatorName: indicator == "all" ? '' : sanitizeHtml(indicatorName)
            };
            const response = ajax(data, '/shop/filter_products');
            response.done(e => {
                refreshToken(e);
                const totalProductRows = e.products.total_rows;
                setProductView(e.products.products);

                setPagination(totalProductRows, 1, 9);
                
                $(document).on('click', '#paginate', function(e){
                    e.preventDefault();
                    const pageLinkDisabled = $(this).attr('disabled');
                    if(!pageLinkDisabled) {
                        const pageNumber = $(this).attr('page-number') ? $(this).attr('page-number') : 1;
                        setOffset = pageNumber;
                        const data = {
                            page_number: (sanitizeHtml(pageNumber) - 1) * 9,
                            indicator_id: indicator == "all" ? '' : sanitizeHtml(indicator),
                            indicatorName: indicator == "all" ? '' : sanitizeHtml(indicatorName)
                        };
                        setPagination(totalProductRows, parseInt(pageNumber), 9);
                        const response = ajax(data, '/shop/filter_products');
                        response.done(e => {
                            refreshToken(e);
                            rowSet = parseInt(pageNumber);
                            setProductView(e.products.products);

                        });
                    }
                });
            });
        }

        function setProductView(data) {
            let productViewHtml = '';
            for(const product of data) {
                productViewHtml += `
                    <div class="filterDesignOne">
                        <div class="itemImageContainer">
                            <a href="/products/show/${product.product_id}">
                                <img src="/assets/product_uploads/${product.image}" alt="${product.product_id}">
                            </a>
                            <div class="addToCart">
                                <h1 class="shopAddToCart" data-product-id="${product.product_id}">&plus;</h1>
                            </div>
                        </div>
                        <div class="itemDetails">
                            <h2>${truncate(product.name, 6)}</h2>
                            <h3>$${product.price}</h3>
                        </div>
                    </div>
                `
            }
            $('.shop__filterResults').html(productViewHtml);
        }

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
                        $('#productQuantity').html(1);
                    }
                    if(e.error) {
                        alertMessage(e.error, 'alertDanger');
                    }
                }
            });
        });
        getNumberPages();
</script>
<?=$this->endSection()?>