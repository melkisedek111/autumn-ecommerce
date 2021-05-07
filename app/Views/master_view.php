<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.svg">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="stylesheet" href="/css/main.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sanitize-html/1.27.5/sanitize-html.min.js" integrity="sha512-1WdDeZGPykoWawFKD3NGJfZM+4hq2+OxUF8ZJrrqFBNU3J+Q5Tgvn+XwHNt8HaVs1MRFFlAgtOgyJr6/mqN/xw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
  
    <title>Autumn | <?= @$pageTitle; ?></title>
</head>
<body>
<script>
    let token = {<?= csrf_token(); ?>: "<?= csrf_hash(); ?>"};
    let tokenName = "<?= csrf_token(); ?>";
    let tokenValue = "<?= csrf_hash(); ?>";

    function refreshToken(e) {
        token[e.token.name] = e.token.value; // --> refreshin csrf token to make another http request or ajax request
        tokenName = e.token.name;
        tokenValue = e.token.value;
    }
    function ajax(ajaxData, url_root){
        if(ajaxData instanceof FormData) {
            return $.ajax({
                url: `${url_root}`,
                method: "POST",
                data: ajaxData,
                contentType: false,
                processData: false,
                dataType: "json",
                mode: "cors",
                header: {
                    "Content-Type": "application/json",
                    Accept: "application/json"
                }
            });
        } else {
            const data = {
                ...token,
                ...ajaxData
            };
            return $.ajax({
                url: `${url_root}`,
                method: "POST",
                data: data,
                dataType: "json",
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            });
        }            
    }
</script>
<div class="loadingContainer">

</div>
    <div class="alertContainer">
        <!-- <div class="alertProduct">
            <div class="detailContainer">
                <img src="https://images.pexels.com/photos/157675/fashion-men-s-individuality-black-and-white-157675.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="">
               <div>
                <h3>Dark Cloak Embroid Design</h3>
                <p>Total in bag: <span>5</span></p>
               </div>
            </div>
            <div class="messageContainer">
                <h4>Product has been added!</h4>
            </div>
        </div>
        <div class="alertMessage alertSuccess">
            <h4>Product has been added</h4>
        </div>
        <div class="alertMessage alertDanger">
            <h4>Product has been Deleted</h4>
        </div> -->
    </div>
    <div class="nav">
        <a href="/home" class="nav__icon">AUTUMN</a>
        <div class="nav__navLinks">
            <a href="/home">Home</a>
            <a href="/shop">Shop</a>
            <?php if(session()->has('user')): ?>
                <?php if(session()->get('user')->user_type == 'admin'): ?>
                    <a href="/main">Orders</a>
                    <a href="/products">Products</a>
                    <a href="/collections">Collections</a>
                <?php endif; ?>
            <?php endif; ?>
          
        </div>
        <div class="nav__userLinks">
            <a href=""><i class="far fa-user"></i></a>
            <div class="linkCartContainer">
                <a href="/cart" class="myCart"><i class="far fa-shopping-bag"></i><span class="nav__cartCount">0</span></a>
                <div class="cartContainerOutside">
                    <div class="cartContainerInside">
                        <h2>Items in Bag: <span id="totalItemsInBag">0</span></h2>
                        <div class="cartItemList">
                            <!-- <div>
                                <img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                                <div>
                                    <h3>Dark Cloak Embroid Design</h3>
                                    <p><span>$49.99</span> X <span>6</span></p>
                                </div>
                            </div>
                            <div>
                                <img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                                <div>
                                    <h3>Dark Cloak Embroid Design</h3>
                                    <p><span>$49.99</span> X <span>6</span></p>
                                </div>
                            </div>
                            <div>
                                <img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                                <div>
                                    <h3>Dark Cloak Embroid Design</h3>
                                    <p><span>$49.99</span> X <span>6</span></p>
                                </div>
                            </div> -->
                        </div>
                        <a href="">Go to checkout</a>
                    </div>
                </div>
            </div>
            <?php if(session()->has('user')): ?>

                <div class="userPlaceholder">
                    <div class="imageHolder">
                        <img src="https://cdn0.iconfinder.com/data/icons/user-pictures/100/unknown2-512.png" alt="">
                    </div>
                    <p>Hi!, <?= session()->get('user')->first_name; ?></p>
                </div>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
 
        </div>
    </div>
    <div class="wrapper">
        <?= $this->renderSection('content') ?>   

        <footer>
            <div class="break"></div>
            <div class="details">
                <div>
                    <a href="">Terms & Condition</a>
                    <a href="">Policy</a>
                    <a href="">Map</a>
                </div>
                <a href="" class="icon">AUTUMN</a>
                <div>
                    <h5>Follow Us On Social</h5>
                    <a href=""><i class="fab fa-twitter"></i></a>
                    <a href=""><i class="fab fa-facebook-f"></i></a>
                    <a href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="right_reserve">
                <p>&copy; 2021 <span>Autumn</span>. All Rights Reserved</p>
            </div>
        </footer> 
    </div>
    <script>

        <?php if(session()->has('user')): ?>
            // function initCartHeader(){
            //     const response = ajax({}, '/cart/cart_on_header');
            //     response.done(e => {
            //         refreshToken(e)
            //         update_cart_items(e.cart);
            //     })
            // }     
            // setTimeout(() => {
            //     initCartHeader();
            // }, 1000);
            function update_cart_items(cartItems) {
                let cartHtml = '';
                const totalItemsInBag = cartItems.reduce((accu, curr) => accu + parseInt(curr.quantity), 0);
                $('#totalItemsInBag, .nav__cartCount').html(totalItemsInBag);
                for(const item of cartItems) {
                    cartHtml += `
                        <div>
                            <img src="/assets/product_uploads/${item.image}" alt="${item.product_name}">
                            <div>
                                <h3>${item.product_name}</h3>
                                <p><span>$${item.product_price}</span> X <span>${item.quantity}</span></p>
                            </div>
                        </div>
                    `
                }
                $('.cartItemList').html(cartHtml);
            } 
            function alertAddCart(cartItems, productId) {
                const item = cartItems.filter(item => item.product_id == productId);
                $('.alertContainer').append(`            
                <div class="alertProduct">
                    <div class="detailContainer">
                        <img src="/assets/product_uploads/${item[0].image}" alt="${item[0].product_name}">
                        <div>
                            <h3>${item[0].product_name}</h3>
                            <p>Total in bag: <span>${item[0].quantity}</span></p>
                        </div>
                    </div>
                    <div class="messageContainer">
                        <h4>Product has been added!</h4>
                    </div>
                </div>`);
                $('.alertProduct').delay(3000).fadeOut(1000).queue(function() { $(this).remove(); });
            }
        <?php endif; ?>

 
        function deleteModal(messageHeader, id, indicator) {
            $('body').prepend(`<div class="modal">
                    <div>
                        <h1>${messageHeader}</h1>
                        <h3>Are you sure you want to delete! <span>This action can't be undone</span></h3>
                        <button class="btn hover" id="removeModalDelete">Cancel</button>
                        <button class="btn hover-danger" data-delete-modal-id="${id}" data-delete-indicator="${indicator}" id="deleteModalBtn">Delete</button>
                    </div>
                </div>`);
            $('body').addClass("modalOpen");
        }
        function setPagination(totalRecords, pageNumber = 1, rowOffSet = 5) {
            const pageNumberContainer = document.createElement('div');
            pageNumberContainer.setAttribute('class', 'pageNumbers');
            $('.tablePagination').html(pageNumberContainer);
            const  totalNumberPages = totalRecords % rowOffSet > 0 ? Math.round(totalRecords / rowOffSet) + 1 : Math.round(totalRecords / rowOffSet);
   
            const secondLast = totalNumberPages - 1;
            let offset = (pageNumber - 1) * rowOffSet;
            let nextPage = pageNumber + 1;
            let previousPage = pageNumber - 1;
            let adjacents = 2;
            // $('.pageNumbers').append(`<a id="paginate" href="#" ${pageNumber <= 1 ? 'disabled="1"' : ""} page-number="1">First Page</a>`);
            $('.pageNumbers').append(`<a id="paginate" href="#" page-number="${previousPage}">Previous</a>`);
            if(totalNumberPages <= 10) {
                for(let countPages = 1; countPages <= totalNumberPages; countPages++) {
                    if(countPages == pageNumber) {
                        $('.pageNumbers').append(`<a href="#" class="active" id="paginate">${countPages}</a>`)
                    } else {
                        $('.pageNumbers').append(`<a href="#" page-number="${countPages}" id="paginate">${countPages}</a>`)
                    }
                }
            } else if(totalNumberPages > 10) {
                if(pageNumber <= 4) {
                    for(let countPages = 1; countPages < 8; countPages++) {
                        if(countPages == pageNumber) {
                            $('.pageNumbers').append(`<a href="#" class="active"  id="paginate">${countPages}</a>`);
                        } else {
                            $('.pageNumbers').append(`<a href="#" page-number="${countPages}" id="paginate">${countPages}</a>`);
                        }
                    }
                    $('.pageNumbers').append(`<a href="#">...</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="${secondLast}">${secondLast}</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="${totalNumberPages}">${totalNumberPages}</a>`);
                } else if (pageNumber > 4 && pageNumber < totalNumberPages - 4) {
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="1">1</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="2">2</a>`);
                    $('.pageNumbers').append(`<a href="#">...</a>`);
                    for(let countPages = pageNumber - adjacents; countPages <= pageNumber + adjacents; countPages++) {
                        if(countPages == pageNumber) {
                            $('.pageNumbers').append(`<a href="#" class="active" id="paginate">${countPages}</a>`);
                        } else {
                            $('.pageNumbers').append(`<a href="#" page-number="${countPages}" id="paginate">${countPages}</a>`);
                        }
                    }
                    $('.pageNumbers').append(`<a href="#">...</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="${secondLast}">${secondLast}</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="${totalNumberPages}">${totalNumberPages}</a>`);
                } else {
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="1" >1</a>`);
                    $('.pageNumbers').append(`<a id="paginate" href="#" page-number="2">2</a>`);
                    $('.pageNumbers').append(`<a href="#">...</a>`);

                    for(let countPages = totalNumberPages - 6; countPages <= totalNumberPages; countPages++) {
                        if(countPages == pageNumber) {
                            $('.pageNumbers').append(`<a href="#" class="active" id="paginate">${countPages}</a>`);
                        } else {
                            $('.pageNumbers').append(`<a href="#" page-number="${countPages}" id="paginate">${countPages}</a>`);
                        }
                    }
                }
            } 
            $('.pageNumbers').append(`<a id="paginate" href="#" ${pageNumber >= totalNumberPages ? 'disabled="1"' : ""} ${pageNumber < totalNumberPages ? `page-number="${nextPage}"` : ""}>Next</a>`);
            // $('.pageNumbers').append(`${pageNumber < totalNumberPages ? `<a id="paginate" href="#" page-number="${totalNumberPages}">Last Page</a>` : ""}`);
        }
        function removeModalDelete() {
            $('.modal').remove();
        }
        $(document).on('click', '#removeModalDelete', function() {
            $(this).parent().parent().remove();
            // $('body').removeClass("modalOpen");
        });

       

        function alertMessage(message, classes) {
            $('.alertContainer').append(`<div class="alertMessage ${classes}">
                    <h4>${message}</h4>
                </div>`);
            $('.alertMessage').delay(3000).fadeOut(1000).queue(function() { $(this).remove(); });
        }
        /**
         * validationError function is to show error for each input
         */
        function validationError(element, message, error) {
            $(element).parent().find('.invalid-feedback').html(message);
            $(element).addClass('error');
            error.push(true);
        }
        function revertField(element) {
            $(element).parent().find('.invalid-feedback').html('');
            $(element).removeClass('error');
        }
        // setInterval(() => {
        //         setTimeout(() => {
        //             $('.alertProduct').fadeOut(function(){
        //                 $(this).remove();
        //             });
        //             $('.alertMessage').fadeOut(function(){
        //                 $(this).remove();
        //             });
        //         }, 2000);
        //     }, 3000);
        function loading() {
            $('.loadingContainer').html(
                `
                <div class="loading">
                    <img src="loading.svg" alt="">
                </div>
                `
            );
            $('body').addClass("modalOpen");
        }
        function unsetLoading() {
            setTimeout(() => {
                $('.loadingContainer').html('');
                if(!$('.adminProduct__modal').length) {
                    $('body').removeClass("modalOpen");
                }
            }, 1500);
        }
        // loading();
        // unsetLoading();
    </script>
</body>
</html>