<?=$this->extend("master_view")?>

<?=$this->section("content")?>
<div class="shop">
        <div class="shop__filter">
            <div class="shop__categories">  
                <h2>Categories</h2>
                <a href="#">
                    <h3>All Products</h3>
                    <h3>205</h3>
                </a>
                <a href="#">
                    <h3>Bags & Backpacks</h3>
                    <h3>205</h3>
                </a>
                <a href="#">
                    <h3>Shoes & Sneakers</h3>
                    <h3>10</h3>
                </a>
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
                <div class="shop__filterBy--size">
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
                </div>
                <div class="shop__filterBy--brands">
                    <h2>Brands</h2>
                    <a href="#">
                        <h3>The Northe Face</h3>
                        <h3>205</h3>
                    </a>
                    <a href="#">
                        <h3>Sandugo</h3>
                        <h3>205</h3>
                    </a>
                    <a href="#">
                        <h3>Chanel</h3>
                        <h3>205</h3>
                    </a>
                </div>
            </div>
        </div>
        <div class="shop__filteredItems">
            <div class="shop__filterOptions">
                <h2><span>205</span> Products Found</h2>
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
                <div class="filterDesignOne">
                    <div class="itemImageContainer">
                        <a href="">
                            <img src="https://images.pexels.com/photos/157675/fashion-men-s-individuality-black-and-white-157675.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                        </a>
                        <div class="addToCart">
                            <h1>&plus;</h1>
                        </div>
                    </div>
                    <div class="itemDetails">
                        <h2>Dark Cloak Embroid Design</h2>
                        <h3>$249.59</h3>
                    </div>
                </div>
                <div class="filterDesignOne">
                    <div class="itemImageContainer">
                        <a href="">
                            <img src="https://images.pexels.com/photos/3850557/pexels-photo-3850557.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                        </a>
                        <div class="addToCart">
                            <h1>&plus;</h1>
                        </div>
                    </div>
                    <div class="itemDetails">
                        <h2>Lactote Designer Bag</h2>
                        <h3>$49.99</h3>
                    </div>
                </div>
                <div class="filterDesignOne">
                    <div class="itemImageContainer">
                        <a href="">
                            <img src="https://images.pexels.com/photos/2529157/pexels-photo-2529157.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
                        </a>
                        <div class="addToCart">
                            <h1>&plus;</h1>
                        </div>
                    </div>
                    <div class="itemDetails">
                        <h2>Nike Jordan Air Mac Edition</h2>
                        <h3>$49.99</h3>
                    </div>
                </div>
                <div class="filterDesignOne">
                    <div class="itemImageContainer">
                        <a href="">
                            <img src="https://images.pexels.com/photos/4066290/pexels-photo-4066290.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                        </a>
                        <div class="addToCart">
                            <h1>&plus;</h1>
                        </div>
                    </div>
                    <div class="itemDetails">
                        <h2>Levi Jeans</h2>
                        <h3>$49.99</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?=$this->endSection()?>