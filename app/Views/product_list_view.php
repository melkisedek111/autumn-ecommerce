<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="adminProduct">
        <h1>Orders</h1>
        <div class="adminProduct__productTable">
            <div class="adminProduct__productTable--search">
                <input type="text" class="form-control" placeholder="Search product here">
                <div>
                    <buttton class="btn hover" id="addNewProductBtn">Add new product</buttton>
                </div>
            </div>
            <div class="adminProduct__productTable--tableContainer">
                <table class="tbl">
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
                    <tbody>
                        <tr>
                            <td><img src="https://images.pexels.com/photos/322207/pexels-photo-322207.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt=""></td>
                            <td>20</td>
                            <td>9/6/2014</td>
                            <td>1000</td>
                            <td>20</td>
                            <td>
                                <div class="productTableAction">
                                        <button class="btn hover editProduct" ><span class="far fa-edit"></span></button>
                                        <button class="btn hover-danger"><span class="far fa-trash"></span></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="https://images.pexels.com/photos/157675/fashion-men-s-individuality-black-and-white-157675.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt=""></td>
                            <td>10</td>
                            <td>9/6/2014</td>   
                            <td>2000</td>
                            <td>150</td>
                            <td>
                                <div class="productTableAction">
                                    <button class="btn hover editProduct"><span class="far fa-edit"></span></button>
                                        <button class="btn hover-danger"><span class="far fa-trash"></span></button>
                                </div>
                                </td>
                        </tr>
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
                                    <?php if($category['category_id'] == session()->get('product_value_category_id')): ?>
                                        <option value="<?= $category['category_id'] ?>" selected>"<?= $category['category_name'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="">Select Category</option>
                        </select>
                        <div class="invalid-feedback"><?= @session()->get('product_error_category_id'); ?></div>
                        <div class="categoryOptions selectOptions" tabindex="-1">
                            <?php foreach($categories as $category):?>
                                <div class="selectOption">
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
                                    <?php if($brand['brand_id'] == session()->get('product_value_brand_id')): ?>
                                        <option value="<?= $brand['brand_id'] ?>" selected>"<?= $brand['brand_name'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="">Select Brand</option>
                        </select>
                        <div class="invalid-feedback"><?= @session()->get('product_error_brand_id'); ?></div>
                        <div class="brandOptions selectOptions" tabindex="-1">
                            <?php foreach($brands as $brand):?>
                                <div class="selectOption">
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
            const imageContainer = [];
            let setMainImageIndex = '';


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
                $('#imageLists').html('');
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
                                addProductFormData.append('imageFiles[]', fileInput.files[imageFile]);
                                imageContainer.push(fileInput.files[imageFile]);
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
                                                <input type="checkbox" class="setMainImage" data-count-image="${imageFile}">
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
                /**
                 * Set the index of the main images
                 * then append setMainImageIndex to the form data
                 */
                setMainImageIndex = index;
                addProductFormData.append('setMainImageIndex', setMainImageIndex);
                alertMessage('Main image has been set', 'alertSuccess');
                console.log(setMainImageIndex);
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
                    console.log(setMainImageIndex);
                }
                $(this).parent().remove();
                imageContainer.splice(index, 1);
                addProductFormData.delete('imageFiles[]');
                imageContainer.forEach((data) => {
                    addProductFormData.append('imageFiles[]', data);
                });
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
                    if($(this).val() == '') {
                        validationError($(this), $(this).attr('data-required-message'), errorInput);
                    } else if (!imageContainer.length && $(this).attr('name') == 'image[]') {
                        validationError($(this), $(this).attr('data-required-message'), errorInput);
                    } else if (setMainImageIndex == '' && $(this).attr('name') == 'image[]') {
                        validationError($(this), 'Please set a main image', errorInput);
                    } else {
                        if($(this).attr('name') !== 'image[]') {
                            addProductFormData.append($(this).attr('name'), sanitizeHtml($(this).val()));
                        }
                        revertField($(this));
                    }
                });
                if(!errorImage.length && !errorInput.length && setMainImageIndex !== '') {
                    addProductFormData.append(tokenName, tokenValue);
                    const response = ajax(addProductFormData, '/admin/add_product_process');
                    response.done(e => {
                        tokenName = e.token.name;
                        tokenValue = e.token.value;
                        if(e.data.success) {
                            $('#imageLists').html('');
                            $('.formInput').each(function() {
                                $(this).val('');
                            });
                            $('.closeModal').click();
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

            $(document).on('change', '.addNewCategoryBrand', function() {
                const name = $(this).val().trim();
                const dataName = $(this).attr('data-name');
                if(name) {
                    if(name.length > 3) {
                        const trimedName = name.charAt(0).toUpperCase() + name.slice(1);
                        const response = ajax({[dataName]: sanitizeHtml(name)}, '/admin/add_category_brand');
                        response.done(e => {
                            token[e.token.name] = e.token.value; // --> refreshin csrf token to make another http request or ajax request
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
                                    if(e.data.category) {
                                        $('.categoryOptions').prepend(
                                            `<div class="selectOption">
                                                <h3 class="selectValue" data-select-value="${e.data.category.category_name}" data-name="category" data-id="${e.data.category.category_id}">${e.data.category.category_name}</h3>
                                                <div>
                                                    <span class="far fa-edit editSelect" data-name="category" data-id="${e.data.category.category_id}"></span>
                                                    <span class="far fa-trash-alt deleteCategory" data-name="category" data-id="${e.data.category.category_id}"></span>
                                                </div>
                                            </div>`
                                        );
                                    }
                                    if(e.data.brand) {
                                        $('.brandOptions').prepend(
                                            `<div class="selectOption">
                                                <h3 class="selectValue" data-select-value="${e.data.brand.brand_name}" data-name="brand" data-id="${e.data.brand.brand_id}">${e.data.brand.brand_name}</h3>
                                                <div>
                                                    <span class="far fa-edit editSelect" data-name="brand" data-id="${e.data.brand.brand_id}"></span>
                                                    <span class="far fa-trash-alt deleteBrand" data-name="brand" data-id="${e.data.brand.brand_id}"></span>
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
            
            $(document).on('click', '.deleteCategory, .deleteBrand', function() {
                const id = $(this).attr('data-id');
                const indicator = $(this).attr('data-name');
                let messageHead = '';
                if(indicator == 'category') {
                    messageHead = 'DELETE CATEGORY';
                } else if (indicator == 'brand') {
                    messageHead = 'DELETE BRAND';
                }
                deleteModal(messageHead, id, indicator);
            });
            $(document).on('click', '#deleteModalBtn', function() {
                const value = $(this).attr('data-delete-modal-id');
                const indicator = $(this).attr('data-delete-indicator');
                const response = ajax({[`${indicator}_id`]: value, indicator: indicator}, '/admin/delete_process');
                response.done(e => {
                    console.log(e);
                })

            });

            
            $(document).on('focusout', '.inputEditSelect', function() {
                $(this).parent().parent().children().each(function(index, element){
                    if($(element).find('.inputEditSelect').length > 0) {
                        if($(element).children('.inputEditSelect').val().trim()) {
                            const name = $(element).children('.inputEditSelect').attr('data-name');
                            const value = $(element).children('.inputEditSelect').val();
                            const id = $(element).children('.inputEditSelect').attr('data-id');
                            if($(element).children('.inputEditSelect').val() != $(element).children('.inputEditSelect').attr('data-select-value')) {
                                $(element).prepend(`
                                <div class="loadingSelectOption">
                                        <img src="loading.svg" alt="">
                                    </div>
                                `);
                                const response = ajax({[`${name}_name`]: sanitizeHtml(value), [`${name}_id`]: sanitizeHtml(id)}, '/admin/update_process');
                                response.done(e => {
                                    token[e.token.name] = e.token.value; // --> refreshin csrf token to make another http request or ajax request
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
                
                $('.adminProduct__modal').show(function(){
                    $('body').addClass("modalOpen");
                });
                
            }); 
            $(document).on('click', '.closeModal', function() {
                $('.adminProduct__modal').hide(function(){
                    $('body').removeClass("modalOpen");
                });
                
            }); 
            $(document).on('change', '#image', function() {

            });
            $(document).on('click', '#addNewProductBtn', function() {
                // $('name').val();
                // $('description').val();
                // $('price').val();
                // $('categories').val();
                // $('brands').val();
                $('.adminProduct__modal').show(function(){
                    $('body').addClass("modalOpen");
                });
            });
        });
    </script>
<?=$this->endSection()?>