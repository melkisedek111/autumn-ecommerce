<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="shop">
    <div class="shop__filter">
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
                    <input type="text" class="form-control" placeholder="min">
                    <input type="text" class="form-control" placeholder="max">
                </div>
                <small>Range: $50 - $100</small>
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
                <select name="" id="" class="form-control">
                    <option value="">Descending</option>
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
                            <a href="">
                                <img src="/assets/product_uploads/<?= $product->image ?>" alt="<?= $product->name ?>">
                            </a>
                            <div class="addToCart">
                                <h1 class="shopAddToCart">&plus;</h1>
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
            <a href="">First Page</a>
            <a href="">Previous Page</a>
            <a href="">1</a>
            <a href="">2</a>
            <a href="">3</a>
            <a href="">4</a>
            <a href="">Next Page</a>
            <a href="">Last Page</a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const indicator = "<?= $active; ?>";
        // const totalProductRows = "<?= $total_products_row; ?>";
        const indicatorName = "<?= $indicator; ?>";


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
                        const response = ajax(data, '/admin/filter_products');
                        response.done(e => {
                            refreshToken(e);
                            setTableData(e.products);
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
                            <a href="">
                                <img src="/assets/product_uploads/${product.image}" alt="${product.product_id}">
                            </a>
                            <div class="addToCart">
                                <h1 class="shopAddToCart" data-id="${product.product_id}">&plus;</h1>
                            </div>
                        </div>
                        <div class="itemDetails">
                            <h2>${truncate(product.name, 6)}</h2>
                            <h3>${product.price}</h3>
                        </div>
                    </div>
                `
            }
            $('.shop__filterResults').html(productViewHtml);
        }
        getNumberPages();
    });
</script>
<?=$this->endSection()?>