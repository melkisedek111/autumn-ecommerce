<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.svg">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="stylesheet" href="/css/main.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script>
        const token = {<?= csrf_token(); ?>: "<?= csrf_hash(); ?>"};
        function ajax(ajaxData, url_root){
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
    </script>
    <title>Autumn | <?= @$pageTitle; ?></title>
</head>
<body>
<div class="loadingContainer">

</div>
<div class="alertContainer">
        <div class="alertProduct">
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
        </div>
    </div>
    <div class="nav">
        <a href="" class="nav__icon">AUTUMN</a>
        <div class="nav__navLinks">
            <a href="/index.html">Home</a>
            <a href="/shop.html">Shop</a>
            <a href="/collections">Collections</a>
        </div>
        <div class="nav__userLinks">
            <a href=""><i class="far fa-user"></i></a>
            <div class="linkCartContainer">
                <a href="" class="myCart"><i class="far fa-shopping-bag"></i><span class="nav__cartCount">10</span></a>
                <div class="cartContainerOutside">
                    <div class="cartContainerInside">
                        <h2>Items in Bag: <span>25</span></h2>
                        <div>
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
                            </div>
                            <div>
                                <img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="">
                                <div>
                                    <h3>Dark Cloak Embroid Design</h3>
                                    <p><span>$49.99</span> X <span>6</span></p>
                                </div>
                            </div>
                        </div>
                        <a href="">Go to checkout</a>
                    </div>
                </div>
            </div>
            
            <a href="">Login</a>
 
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
 
        function alertMessage(message, classes) {
            $('.alertContainer').append(`
                <div class="alertMessage ${classes}">
                    <h4>${message}</h4>
                </div>
            `).delay(3000).fadeOut(1000).queue(function() { $(this).remove(); });;
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
                $('body').removeClass("modalOpen");
            }, 3000);
        }
        // loading();
        // unsetLoading();
    </script>
</body>
</html>