<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="adminProduct">
        <h1>Orders</h1>
        <div class="adminProduct__productTable">
            <div class="adminProduct__productTable--search">
                <input type="text" id="searchProduct" class="form-control" placeholder="Search product here">
                <div>
                    <buttton class="btn hover" id="addNewProductBtn">Add new product</buttton>
                </div>
            </div>
            <div class="adminProduct__productTable--tableContainer">
                <table class="tbl" id="productTable">
                    <thead>
                        <tr>
                            <th width="25%">Picture</th>
                            <th width="10%">ID</th>
                            <th width="25%">Name</th>
                            <th width="10%">Inventory Count</th>
                            <th width="10%">Quantity Sold</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="productsTbody">
                        <!-- <?php foreach($products as $product): ?>
                            <tr id="product_<?= $product->product_id ?>">
                                <td><img src="/assets/product_uploads/<?= $product->image ?>" alt="<?= $product->name ?>"></td>
                                <td><?= $product->product_id ?></td>
                                <td><?= $product->name ?></td>   
                                <td><?= $product->stock_quantity ? $product->stock_quantity : 0 ?></td>
                                <td><?= $product->stock_sold ? $product->stock_sold : 0 ?></td>
                                <td>
                                    <div class="productTableAction">
                                        <button class="btn hover editProduct" data-id="<?= $product->product_id ?>"><span class="far fa-edit"></span></button>
                                        <button class="btn hover-danger deleteProduct" data-id="<?= $product->product_id ?>" data-name="product"><span class="far fa-trash"></span></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?> -->
                    </tbody>
                </table>
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
        <div class="adminProduct__modal" tabindex="-1">
            <div class="adminProduct__modal--form">
                <h1 id="formModalHeading">Add new product</h1>
                <form action="/admin/add_product_process" id="productForm" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control formInput <?= session()->has('product_error_name') ? "error" : ""; ?>" id="name" placeholder="product name" value="<?= @session()->get('product_value_name'); ?>" data-required-message="Product name is required!">
                        <div class="invalid-feedback"><?= @session()->get('product_error_name'); ?></div>
                    </div>
                    <div>
                        <label for="description">Description</label>
                        <textarea name="description" cols="30" rows="10" id="description"  class="form-control textArea-control-2 formInput <?= session()->has('product_error_description') ? "error" : ""; ?>" placeholder="product description" data-required-message="Product description is required!"><?= @session()->get('product_value_description'); ?></textarea>
                        <div class="invalid-feedback"><?= @session()->get('product_error_description'); ?></div>
                    </div>
                    <div>
                        <label for="price">Price</label>
                        <input type="text" name="price" class="form-control formInput <?= session()->has('product_error_price') ? "error" : ""; ?>" id="price"  placeholder="product price" value="<?= @session()->get('product_value_price'); ?>" data-required-message="Product price is required!">
                        <div class="invalid-feedback"><?= @session()->get('product_error_price'); ?></div>
                    </div>
                    <div class="modifiedSelect">
                        <label for="categories">Categories</label>
                        <select name="category_id" id="categories"  class="form-control parentSelect formInput <?= session()->has('product_error_category_id') ? "error" : ""; ?>" data-required-message="Product category is required!">
                            <?php if(session()->has('product_value_category_id')): ?>
                                <?php foreach($categories as $category):?>
                                    <?php if($category->category_id == session()->get('product_value_category_id')): ?>
                                        <option value="<?= $category->category_id ?>" selected>"<?= $category->category_name ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="">Select Category</option>
                        </select>
                        <div class="invalid-feedback"><?= @session()->get('product_error_category_id'); ?></div>
                        <div class="categoryOptions selectOptions" tabindex="-1">
                            <?php foreach($categories as $category):?>
                                <div class="selectOption" data-name="category" data-id="<?= $category->category_id; ?>">
                                    <h3 class="selectValue" data-select-value="<?= $category->category_name; ?>" data-name="category" data-id="<?= $category->category_id; ?>"><?= $category->category_name; ?></h3>
                                    <div class="selectOptionButtons">
                                        <span class="far fa-edit editSelect" data-name="category" data-select-value="<?= $category->category_name; ?>" data-id="<?= $category->category_id; ?>"></span>
                                        <span class="far fa-trash-alt deleteCategory" data-name="category" data-id="<?= $category->category_id; ?>"></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label for="addNewCategory">or add new Category</label>
                        <input type="text" id="addNewCategory" class="form-control addNewCategoryBrand" placeholder="add new product category" data-name="category_name">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="modifiedSelect">
                        <label for="brand">Brands</label>
                        <select name="brand_id" id="brand" class="form-control formInput <?= session()->has('product_error_brand_id') ? "error" : ""; ?> parentSelect" data-required-message="Product brand is required!">
                            <?php if(session()->has('product_value_brand_id')): ?>
                                <?php foreach($brands as $brand):?>
                                    <?php if($brand->brand_id == session()->get('product_value_brand_id')): ?>
                                        <option value="<?= $brand->brand_id ?>" selected>"<?= $brand->brand_name ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="">Select Brand</option>
                        </select>
                        <div class="invalid-feedback"><?= @session()->get('product_error_brand_id'); ?></div>
                        <div class="brandOptions selectOptions" tabindex="-1">
                            <?php foreach($brands as $brand):?>
                                <div class="selectOption" data-name="brand" data-id="<?= $brand->brand_id; ?>">
                                    <h3 class="selectValue" data-select-value="<?= $brand->brand_name; ?>" data-name="brand" data-id="<?= $brand->brand_id; ?>"><?= $brand->brand_name; ?></h3>
                                    <div class="selectOptionButtons">
                                        <span class="far fa-edit editSelect" data-select-value="<?= $brand->brand_name; ?>" data-name="brand" data-id="<?= $brand->brand_id; ?>"></span>
                                        <span class="far fa-trash-alt deleteBrand" data-name="brand" data-id="<?= $brand->brand_id; ?>"></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label for="name">or add new Brand</label>
                        <input type="text" class="form-control addNewCategoryBrand" id="addNewBrand" placeholder="add new product brand" data-name="brand_name">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div>
                       
                        <label for="image">Upload new image</label>
                        <input type="file" id="image" name="image[]" class="form-control formInput <?= session()->has('product_error_image') ? "error" : ""; ?>" multiple data-required-message="Product image is required!">
                        <div class="invalid-feedback"><?= @session()->get('product_error_image'); ?></div>
                    </div>
                    <div class="productImageList">
                        <h2>Product Images <small class="imageContainerError invalid-feedback"></small></h2>
                        <div id="imageLists">
            
                        </div>
                    </div>
                    <div class="formButtons">
                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                        <button type="button" class="btn hover-danger closeModal">Cancel</button>
                        <a href="" class="btn hover-success">Preview</a>
                        <input type="submit" id="addProduct" value="Add" class="btn hover">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>

        $(document).ready(function(){
            <?php if(session()->has('product_error_name') || session()->has('product_error_description') || session()->has('product_error_price') || session()->has('product_error_category_id') || session()->has('product_error_brand_id') || session()->has('product_error_image')): ?>
                $('.adminProduct__modal').slideDown(function(){
                    $('body').addClass("modalOpen");
                });
            <?php endif; ?>


            const errorImage = [];
            const errorInput = [];
            const addProductFormData = new FormData();
            // const updateProductFormData = new FormData();
            const imageContainer = [];
            const updateImageToAddContainer = [];
            const imageDeletedContainer = [];
            let setMainImageIndex = '';
            let isProductUpdate = false;
            let imageProductCount = 0;
            let product_rows = 0;
            /**
             * PRICE INPUT is only number will be input when keypress
             */
            $(document).on('keypress', '#price', function(evt) {
                evt = evt ? evt : window.event;
                var charCode = evt.which ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 45 || charCode > 46)) {
                    return false;
                }
                return true;
            });


            /**
             * 
             */
            $(document).on('change', '#image', function() {
                errorImage.length = 0;
                if(!isProductUpdate) {
                    $('#imageLists').html('');
                }
                const fileInput = document.getElementById('image');
                for(const imageFile in fileInput.files) {
                    if(imageFile !== 'length' && imageFile !== 'item') {
                        /**
                         * VALIDATION FOR IMAGE
                         */
                        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                        const preview = document.getElementById('imagePreview');
                        if(!allowedExtensions.exec(fileInput.files[imageFile].name)){
                            /**
                             * if the image does not any of the allowedExtensions will throw alert
                             */
                            alertMessage('Please upload file having extensions .jpeg/.jpg/.png/.gif only.', 'alertDanger');
                            errorImage.push(true);
                        }else if (fileInput.files[imageFile].size > 2072000) {
                            /**
                             * if the imaage file size is greater than 2MB, then it will throw a alert
                             */
                            alertMessage('The File is Too Big!, Please Upload the file less than 2MB only.', 'alertDanger');
                            errorImage.push(true);
                        } else {
                            if (fileInput.files[imageFile]) {
                                /**
                                 * remove error messages when validation is success
                                 * append the images to a form data
                                 */
                                revertField($(this));
                                // addProductFormData.append('imageFiles[]', fileInput.files[imageFile]);
                                imageContainer.push(fileInput.files[imageFile]);
                                // if(isProductUpdate) {
                                //     updateProductFormData.append('imageFiles[]', fileInput.files[imageFile]);
                                //     updateImageToAddContainer.push(fileInput.files[imageFile]);
                                // }
                                /**
                                 * create a new instance of a FileReader();
                                 * once the reader ready, then onload the images and append to the div with a id #imageList to show the selected images
                                 */
                                let reader = new FileReader();
                                reader.onload = function(e) {
                                    $('#imageLists').prepend(`
                                        <div class="selectedImage">
                                            <img src="${e.target.result}" alt="">
                                            <h3>${fileInput.files[imageFile].name}</h3>
                                            <span class="deleteImageProduct far fa-trash-alt" data-image-index="${imageFile}"></span>
                                            <div>
                                                <input type="checkbox" class="setMainImage" data-image-set="new" data-count-image="${imageFile}">
                                                <label for="">Main</label>
                                            </div>
                                        </div>
                                    `)
                                };
                                reader.readAsDataURL(fileInput.files[imageFile]);
                            }
                        }
                    }
                }
            });

            /**
             * Checking the checkbox will set the main image product
             */
            $(document).on('click', '.setMainImage', function() {
                const index = $(this).attr('data-count-image');


                /**
                 * Loop all the instance of selected images with checkboxes, if the selected images is not checked then the other selected images will be unchecked and the selected image will be check and set
                 */
                $('.setMainImage').each((idx, element) => {
                    if(element.getAttribute('data-count-image') !== index) {
                        element.checked = false;
                    } else {
                        element.checked = true;
                    }
                });
                if(isProductUpdate && !$(this).attr('data-image-set')) {
                    setMainImageIndex = $(this).attr('data-count-image');
                    addProductFormData.append('previousImageSetMain', setMainImageIndex);   
                } else {
                    /**
                     * Set the index of the main images
                     * then append setMainImageIndex to the form data
                     */
                    setMainImageIndex = index;
                    addProductFormData.append('setMainImageIndex', setMainImageIndex);
                }
                alertMessage('Main image has been set', 'alertSuccess');
            })

            /**
             * Deleting image from container
             * Before removing the image from the container imagelists, it will check first if the images has been set as main image, if checked then the setMainImageIndex and the data setMainImageIndex apppended from the form data will be deleted
             * Get the index of the image from the image container
             * Remove the image from the container base on the index
             * delete the imageFiles[] from the form data to reset the imageFiles[]
             * loop the the imageContainer and append each loop to the imageFiles[] to get new set imageFiles that to be submitted
             */
            $(document).on('click', '.deleteImageProduct', function() {
                const index = $(this).attr('data-image-index');
                if($(this).parent().find('div').find('input:checked').length > 0) {
                    setMainImageIndex = '';
                    addProductFormData.delete('setMainImageIndex');
                }
                $(this).parent().remove();
                imageContainer.splice(index, 1);
                // addProductFormData.delete('imageFiles[]');
                // imageContainer.forEach((data) => {
                //     addProductFormData.append('imageFiles[]', data);
                // });
                alertMessage('Image has been removed', 'alertDanger');
            });

             /**
             * Submitting the product form
             * every submitt will reset the errorInput
             * loop through all elements with a class of .formInput then start the validation
             * if the inputs passed the validation then it will sanitize it and revert fields using revertFields functions
             */
            $(document).on('submit', '#productForm', function(e) {
                e.preventDefault();
                errorInput.length = 0;
                $('.formInput').each(function() {
                    if($(this).val() == '' && $(this).attr('name') != 'image[]') {
                        validationError($(this), $(this).attr('data-required-message'), errorInput);
                    } else if (setMainImageIndex == '' && $(this).attr('name') == 'image[]') {
                        validationError($(this), 'Please set a main image', errorInput);
                    } else if (!imageContainer.length && $(this).attr('name') == 'image[]' && !isProductUpdate) {
                        validationError($(this), $(this).attr('data-required-message'), errorInput);
                    } else {
                        if($(this).attr('name') !== 'image[]') {
                            addProductFormData.append($(this).attr('name'), sanitizeHtml($(this).val()));
                        }
                        revertField($(this));
                    }
                });
                if(!errorImage.length && !errorInput.length && setMainImageIndex !== '') {
                    loading();
                    imageContainer.forEach((data) => {
                        addProductFormData.append('imageFiles[]', data);
                    });
                    if(isProductUpdate) {
                        addProductFormData.append('imageToBeDeleted', JSON.stringify(imageDeletedContainer));
                    }
                    addProductFormData.append(tokenName, tokenValue);
                    const response = ajax(addProductFormData, '/admin/add_product_process');
                    response.done(e => {
                        /**
                         * Refreshing the token
                         */
                        refreshToken(e);
                        if(e.data.isProductAdded) {
                            $('#imageLists').html('');
                            $('.formInput').each(function() {
                                $(this).val('');
                            });
                            /**
                             * When adding product is successful then prepend to table tbody
                             */
                            $('#productsTbody').prepend(`
                                <tr id="product_${e.data.product.product_id}">
                                    <td><img src="/assets/product_uploads/${e.data.product.image}" alt="${e.data.name}"></td>
                                    <td>${e.data.product.product_id}</td>
                                    <td>${e.data.product.name}</td>   
                                    <td>${e.data.product.stock_quantity ? e.data.product.stock_quantity : 0}</td>
                                    <td>${e.data.product.stock_sold ? e.data.product.stock_sold : 0}</td>
                                    <td>
                                        <div class="productTableAction">
                                            <button class="btn hover editProduct" data-id="${e.data.product.product_id}"><span class="far fa-edit"></span></button>
                                                <button class="btn hover-danger deleteProduct" data-id="${e.data.product.product_id}" data-name="product"><span class="far fa-trash"></span></button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                            $('.closeModal').click();
                            unsetLoading();
                            alertMessage('Product has been added', 'alertSuccess'); // --> message if there are somethings wrong in validations
                        }
                        if(e.data.isProductUpdated) {
                            console.log(e);
                            $(`#product_${e.data.product.product_id}`).replaceWith(`
                                <td><img src="/assets/product_uploads/${e.data.product.image}" alt="${e.data.name}"></td>
                                <td>${e.data.product.product_id}</td>
                                <td>${e.data.product.name}</td>   
                                <td>${e.data.product.stock_quantity ? e.data.product.stock_quantity : 0}</td>
                                <td>${e.data.product.stock_sold ? e.data.product.stock_sold : 0}</td>
                                <td>
                                    <div class="productTableAction">
                                        <button class="btn hover editProduct" data-id="${e.data.product.product_id}"><span class="far fa-edit"></span></button>
                                            <button class="btn hover-danger deleteProduct" data-id="${e.data.product.product_id}" data-name="product"><span class="far fa-trash"></span></button>
                                    </div>
                                </td>
                            `);
                            $('.closeModal').click();
                            unsetLoading();
                            alertMessage('Product has been updated', 'alertSuccess');
                            return false;
                        }
                        if(e.data.error) {
                            $('.closeModal').click();
                            unsetLoading();
                            alertMessage('Somethine went wrong', 'alertDanger');
                            return false;
                        }
                    })
                }
            });


            /**
             * Dynamic select tag
             * By clicking the selectOption element
             * it will get the attribute of data-select-value, data-id and data-name
             * those attribute will be use as data for the <option></option> tag for the select
             */
            $(document).on('click', '.selectOption', function() {
                if($(this).find('.selectValue').length) {
                    const value = $(this).find('.selectValue').attr('data-select-value');
                    const id = $(this).find('.selectValue').attr('data-id');
                    const dataName = $(this).find('.selectValue').attr('data-name');
                    $(this).parent().parent().find('select').html(`
                            <option value="${id}" selected>${value}</option>
                        `);
                }
            });

            /**
             * Edit options value from select tag
             * By clicking the selectOption element
             * it will get the attribute of data-select-value, data-id
             * then it will remove the edit button for the option tag then replace with cancel button
             * 
             */
            $(document).on('click', '.editSelect', function() {
                const id = $(this).attr('data-id');
                const name = $(this).attr('data-select-value');
                const indicator = $(this).attr('data-name');
                $(this).parent().parent().find('.selectValue').remove();
                if(!$(this).parent().parent().find('input').length) {
                    $(this).parent().parent().prepend(`
                        <input class="form-control inputEditSelect" value="${name}" data-select-value="${name}" data-id="${id}" data-name="${indicator}"/>
                    `); 
                    $(this).parent().parent().find('.inputEditSelect').focus();
                    $(this).parent().prepend(`
                        <span class="far fa-pen-square cancelEditSelect" data-select-value="${name}" data-name="${indicator}" data-id="${id}"></span>
                    `); 
                }
                $(this).remove();
                $('.cancelEditSelect').each((index, element) => {
                    if(element.getAttribute('data-id') !== id) {
                        $(element).click();
                    }
                });
            });


             /**
             * cancel the edit options value from select tag
             * By clicking the selectOption element
             * it will get the attribute of data-select-value, data-id
             * then it will remove the cancel button for the option tag then replace with edit button
             * 
             */
            $(document).on('click', '.cancelEditSelect', function() {
                const id = $(this).attr('data-id');
                const name = $(this).attr('data-select-value');
                const indicator = $(this).attr('data-name');
                $(this).parent().parent().find('.inputEditSelect').remove();
                if(!$(this).parent().parent().find('h3').length) {
                    $(this).parent().parent().prepend(`
                        <h3 class="selectValue" data-select-value="${name}" data-name="${indicator}" data-id="${id}">${name}</h3>
                    `); 
                    $(this).parent().prepend(`
                        <span class="far fa-edit editSelect" data-select-value="${name}" data-name="${indicator}" data-id="${id}"></span>
                    `); 
                }
                $(this).remove();
            });

            /**
             * Event for adding category and brand
             * trimming the white spaces on the input
             * get the attributes
             * check if the input data is less than 2 then throw alert
             * if not then add the category of brand
             */
            
            $(document).on('change', '.addNewCategoryBrand', function() {
                const name = $(this).val().trim();
                const dataName = $(this).attr('data-name');
                if(name) {
                    if(name.length > 2) {
                        const trimedName = name.charAt(0).toUpperCase() + name.slice(1);
                        const response = ajax({[dataName]: sanitizeHtml(name)}, '/admin/add_category_brand');
                        response.done(e => {
                            refreshToken(e);

                            if(e.internalValidationError) {
                                alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                            } else {
                                if(e.data.exists) {
                                    validationError($(this), e.data.exists, errorInput);
                                    return false
                                }
                                if(e.data.added) {
                                    $(this).val('');
                                    revertField($(this));
                                    alertMessage(e.data.added, 'alertSuccess');
                                    /**
                                     * prepend the newly added category or brand in select options like
                                     */
                                    if(e.data.category) {
                                        $('.categoryOptions').prepend(
                                            `<div class="selectOption" data-name="category" data-id="${e.data.category.id}">
                                                <h3 class="selectValue" data-select-value="${e.data.category.category_name}" data-name="category" data-id="${e.data.category.id}">${e.data.category.category_name}</h3>
                                                <div class="selectOptionButtons">
                                                    <span class="far fa-edit editSelect" data-name="category" data-id="${e.data.category.id}"></span>
                                                    <span class="far fa-trash-alt deleteCategory" data-name="category" data-id="${e.data.category.id}"></span>
                                                </div>
                                            </div>`
                                        );
                                    }
                                    if(e.data.brand) {
                                        $('.brandOptions').prepend(
                                            `<div class="selectOption" data-name="brand" data-id="${e.data.brand.id}">
                                                <h3 class="selectValue" data-select-value="${e.data.brand.brand_name}" data-name="brand" data-id="${e.data.brand.id}">${e.data.brand.brand_name}</h3>
                                                <div class="selectOptionButtons">
                                                    <span class="far fa-edit editSelect" data-name="brand" data-id="${e.data.brand.id}"></span>
                                                    <span class="far fa-trash-alt deleteBrand" data-name="brand" data-id="${e.data.brand.id}"></span>
                                                </div>
                                            </div>`
                                        );
                                    }
                                    return false;
                                }
                                if(e.data.error) {
                                    $(this).val('');
                                    alertMessage('Somethine went wrong', 'alertDanger');
                                    return false;
                                }
                            }
                        });
                    } else {
                        validationError($(this), "Category name should be at least 3 characters", errorInput);
                        return false
                    }
                }
            })

            /**
             * Deteleting category or brand modal
             * get the required attributes
             * indicotor on which is needed to be deleted
             * then show modal
             */

 
            $(document).on('click', '.deleteCategory, .deleteBrand, .deleteProduct', function() {
                const id = $(this).attr('data-id');
                const indicator = $(this).attr('data-name');
                let messageHead = '';
                if(indicator == 'category') {
                    messageHead = 'DELETE CATEGORY';
                } else if (indicator == 'brand') {
                    messageHead = 'DELETE BRAND';
                } else if (indicator == 'product') {
                    messageHead = 'DELETE PRODUCT';
                }
                deleteModal(messageHead, id, indicator);
            });
            
            /**
             * deleteModalBtn - button from the modal, since modal is executed once, the operation is global whenever deleteModalBtn is being called then do some stuff on the event e.g  $(document).on('click', '#deleteModalBtn', function() { do something here });
             * get the required attributes
             * 
             */
            $(document).on('click', '#deleteModalBtn', function() {
                loading();
                const value = $(this).attr('data-delete-modal-id');
                const indicator = $(this).attr('data-delete-indicator');
                if(indicator == 'category' || indicator == 'brand') {
                    const response = ajax({[`${indicator}_id`]: sanitizeHtml(value), indicator: sanitizeHtml(indicator)}, '/admin/delete_process');
                    response.done(e => {
                        refreshToken(e);

                        if(e.internalValidationError) {
                            alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                        } else {
                            if(e.data.deleted) {
                                /**
                                 * if deleted is success
                                 * then delete the selected option from the options
                                 */
                                setTimeout(() => {
                                    $('.selectOption').each(function(index, element) {
                                        const name = $(element).attr('data-name');
                                        const id = $(element).attr('data-id');
                                        if(name == e.data.indicator && id == e.data.id) {
                                            $(element).parent().parent().find('select').val('');
                                            $(element).remove();
                                        };
                                    });
                                    removeModalDelete();
                                    unsetLoading();
                                    alertMessage(e.data.deleted, 'alertDanger');
                                    return false;
                                }, 1500);
                            }
                            if(e.data.error) {
                                alertMessage('Somethine went wrong', 'alertDanger');
                                return false;
                            }
                        }
                    });
                } else if (indicator == 'product') {
                    const response = ajax({'product_id': sanitizeHtml(value), indicator: sanitizeHtml(indicator)}, '/admin/delete_product_process');
                    response.done(e => {
                        refreshToken(e);

                        if(e.internalValidationError) {
                            alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                        } else {
                            if(e.data.isProductDeleted) {
                                $(`#product_${e.data.product_id}`).remove();
                                removeModalDelete();
                                unsetLoading();
                                alertMessage(e.data.productDeleteMessage, 'alertSuccess');
                            }
                            if(e.data.error) {
                                alertMessage('Somethine went wrong', 'alertDanger');
                                return false;
                            }
                        }
                    });
                }
            });

            /**
             * when the edit option has been focusout do something
             * check if there is a class inputEditSelect then check if the lenght of the input field
             * trim the input
             * get the required attributes
             * if the value of the input is not equal to the data-select-value then do nothing
             * if the value of the input is not equal to the data-select-value then do something
             */
            $(document).on('focusout', '.inputEditSelect', function() {
                $(this).parent().parent().children().each(function(index, element){
                    if($(element).find('.inputEditSelect').length > 0) {
                        if($(element).children('.inputEditSelect').val().trim()) {
                            const name = $(element).children('.inputEditSelect').attr('data-name');
                            const value = $(element).children('.inputEditSelect').val();
                            const id = $(element).children('.inputEditSelect').attr('data-id');

                            if($(element).children('.inputEditSelect').val() != $(element).children('.inputEditSelect').attr('data-select-value')) {
                                /**
                                 * prepend loading indicator
                                 */
                                $(element).prepend(`
                                <div class="loadingSelectOption">
                                        <img src="loading.svg" alt="">
                                    </div>
                                `);
                                const response = ajax({[`${name}_name`]: sanitizeHtml(value), [`${name}_id`]: sanitizeHtml(id)}, '/admin/update_process');
                                response.done(e => {
                                    refreshToken(e);

                                    setTimeout(() => {
                                        $('.loadingSelectOption').remove();
                                        if(e.internalValidationError) {
                                            alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                                        } else {
                                            if(e.data.exists) {
                                                validationError($(element).parent(), e.data.exists, errorInput);
                                                return false
                                            }
                                            if(e.data.updated) {
                                                revertField($(element).parent());
                                                alertMessage(e.data.updated, 'alertSuccess');
                                                /**
                                                 * if updating category or brand success then prepend to their respective select tags 
                                                 */
                                                if(e.data.category) {
                                                    $(element).children('.inputEditSelect').attr('data-select-value', e.data.category.category_name);
                                                    $(element).children('.selectOptionButtons').find('.cancelEditSelect').attr('data-select-value', e.data.category.category_name);
                                                    $(element).children('.selectOptionButtons').find('.cancelEditSelect').click();
                                                }
                                                if(e.data.brand) {
                                                    $(element).children('.inputEditSelect').attr('data-select-value', e.data.brand.brand_name);
                                                    $(element).children('.selectOptionButtons').find('.cancelEditSelect').attr('data-select-value', e.data.brand.brand_name);
                                                    $(element).children('.selectOptionButtons').find('.cancelEditSelect').click();
                                                }
                                                return false;
                                            }
                                            if(e.data.error) {
                                                $(this).val('');
                                                alertMessage('Somethine went wrong', 'alertDanger');
                                                return false;
                                            }
                                        }
                                    }, 2000);
                                });
                            } else {
                                $(element).children('.inputEditSelect').attr('data-select-value', value);
                                $(element).children('.selectOptionButtons').find('.cancelEditSelect').attr('data-select-value', value);
                                $(element).children('.selectOptionButtons').find('.cancelEditSelect').click();
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.editProduct', function() {
                resetFormData();
                $('#addProduct').val('Update');
                $('.formInput').each(function() {
                    revertField($(this));
                });
                const id = $(this).attr('data-id');
                $('#formModalHeading').html(`Edit product - ID ${id}`);
                const response = ajax({product_id: sanitizeHtml(id)}, '/admin/get_product');
                response.done(e => {
                    refreshToken(e);
                    isProductUpdate = true;
                    imageProductCount = e.data.images.length;
                    addProductFormData.append('product_id', e.data.details.product_id);
                    addProductFormData.append('isProductUpdate', isProductUpdate);
                    $('#imageLists').html('');
                    $('#name').val(e.data.details.name);
                    $('#description').val(e.data.details.description);
                    $('#price').val(e.data.details.price);
                    $('#categories').prepend(`
                        <option value="${e.data.details.category_id}" selected>${e.data.details.category_name}</option>
                    `);
                    $('#brand').prepend(`
                        <option value="${e.data.details.brand_id}" selected>${e.data.details.brand_name}</option>
                    `);
                    for(const image of e.data.images) {
                        if(image.status == 1) {
                            setMainImageIndex = image.image_id;
                            addProductFormData.append('previousImageSetMain', image.image_id);    
                        }
                        $('#imageLists').prepend(
                            `<div class="selectedImage">
                                <img src="/assets/product_uploads/${image.image}" alt="">
                                <h3>${image.image}</h3>
                                <span class="deleteImageOnUpdate far fa-trash-alt" data-image-name="${image.image}" data-image-index="${image.image_id}"></span>
                                <div>
                                    <input type="checkbox" class="setMainImage" data-count-image="${image.image_id}" ${image.status == 1 ? "checked": ""}>
                                    <label for="">Main</label>
                                </div>
                            </div>`
                        );
                    }
                });
                $('.adminProduct__modal').show(function(){
                    $('body').addClass("modalOpen");
                });
            }); 

            $(document).on('click', '.undoDeleteImage', function() {
                const id = $(this).attr('data-image-index');
                for(const imageToUndo in imageDeletedContainer) {
                    if(imageDeletedContainer[imageToUndo].id == id) {
                        $(this).parent().find('h3').removeClass('unlisted');
                        $(this).parent().find('div').find('input').attr('disabled', false);
                        $(this).parent().find('h3').after(`<span class="deleteImageOnUpdate far fa-trash-alt" data-image-name="${imageDeletedContainer[imageToUndo].image}" data-image-index="${imageDeletedContainer[imageToUndo].image_id}"></span>`);
                        $(this).remove();
                        imageDeletedContainer.splice(imageToUndo, 1);
                    }
                }
                imageProductCount++;
                alertMessage('The has been undo', 'alertSuccess');
                return false;
            });
            $(document).on('click', '.deleteImageOnUpdate', function() {
                if($(this).parent().find('div').find('input:checked').length > 0) {
                    setMainImageIndex = '';
                }
                if(imageProductCount != 1) {
                    const id = $(this).attr('data-image-index');
                    const image = $(this).attr('data-image-name');
                    $(this).parent().find('h3').addClass('unlisted');
                    $(this).parent().find('div').find('input').prop('checked', false).attr('disabled', true);
                    $(this).parent().find('h3').after(`<span class="undoDeleteImage colorSuccess far fa-undo" data-image-name="${image}" data-image-index="${id}"></span>`);
                    imageDeletedContainer.push({imageName: image, id: id});
                    imageProductCount--;
                    $(this).remove();
                    alertMessage('This image will be deleted!', 'alertDanger');
                    return false;
                } else {
                    alertMessage('There should be at least 1 image, this cannot be deleted', 'alertDanger');
                    return false
                }
            });

            $(document).on('keyup', '#searchProduct', function() {
                var value = $(this).val().toLowerCase();
                $("#productTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            })

            $(document).on('click', '.closeModal', function() {
                $('.adminProduct__modal').hide(function(){
                    $('body').removeClass("modalOpen");
                });
            }); 
            $(document).on('click', '#addNewProductBtn', function() {
                resetFormData();
                $('#formModalHeading').html('Add new product');
                $('#addProduct').val('Add');
                $('.adminProduct__modal').show(function(){
                    $('body').addClass("modalOpen");
                });
            });

            function getNumberPages() {
                const data = {
                    page_number: (1 - 1) * 5 
                };
                const response = ajax(data, '/admin/filter_products');
                response.done(e => {
                    refreshToken(e);
                    const totalProductRows = e.total_rows.product_rows;
                    setTableData(e.products);
                    setPagination(totalProductRows);
                    
                    $(document).on('click', '#paginate', function(e){
                        e.preventDefault();
                        const pageLinkDisabled = $(this).attr('disabled');
                        if(!pageLinkDisabled) {
                            const pageNumber = $(this).attr('page-number') ? $(this).attr('page-number') : 1;
                            setOffset = pageNumber;
                            const data = {
                                page_number: (sanitizeHtml(pageNumber) - 1) * 5,
                            };
                            setPagination(totalProductRows, parseInt(pageNumber));
                            const response = ajax(data, '/admin/filter_products');
                            response.done(e => {
                                refreshToken(e);
                                setTableData(e.products);
                            });
                        }
                    });
                });
            }

            

            function setTableData(data) {
                let tbodyTable = ''
                for(const product of data) {
                    tbodyTable += `<tr id="product_${product.product_id}">
                            <td><img src="/assets/product_uploads/${product.image}" alt="${product.name}"></td>
                            <td>${product.product_id}</td>
                            <td>${product.name}</td>   
                            <td>${product.stock_quantity ? product.stock_quantity : 0}</td>
                            <td>${product.stock_sold ? product.stock_sold : 0}</td>
                            <td>
                                <div class="productTableAction">
                                    <button class="btn hover editProduct" data-id="${product.product_id}"><span class="far fa-edit"></span></button>
                                        <button class="btn hover-danger deleteProduct" data-id="${product.product_id}" data-name="product"><span class="far fa-trash"></span></button>
                                </div>
                            </td>
                        </tr>`;
                }
                $('#productsTbody').html(tbodyTable);
            }

            

            function resetFormData() {
                $('#name').val('');
                $('#description').val('');
                $('#price').val('');
                $('#categories').val('');
                $('#brand').val('');
                $('#image').val('');
                $('#imageLists').html('');
                addProductFormData.delete('setMainImageIndex');
                addProductFormData.delete('isProductUpdate');
                addProductFormData.delete('name');
                addProductFormData.delete('description');
                addProductFormData.delete('price');
                addProductFormData.delete('brand');
                addProductFormData.delete('categories');
                addProductFormData.delete('imageFiles[]');
                addProductFormData.delete('imageFiles');
                errorImage.length = 0;
                errorInput.length = 0;
                imageContainer.length = 0;
                updateImageToAddContainer.length = 0;
                imageDeletedContainer.length = 0;
                setMainImageIndex = '';
                isProductUpdate = false;
                imageProductCount = 0;
                console.log('qweasdasd');
            }

            getNumberPages();   
        });
    </script>
<?=$this->endSection()?>