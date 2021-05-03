<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\ProductsModel;

class Admin extends BaseController
{
    protected $session;
    protected $requests;
    protected $ProductsModel;
    protected $token;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $utilities;
    public function __construct()
    {
        $this->ProductsModel = new ProductsModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->utilities = new Utilities;

        $this->rules =  [
                'name' => 'required|min_length[3]|max_length[150]',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric',
                'product_id' => 'required|numeric',
                'description' => 'required',
                'price' => 'required|decimal',
                'category_name' => 'required|min_length[2]|max_length[250]',
                'brand_name' => 'required|min_length[2]|max_length[250]',
                
        ];
        $this->messages = [
            'name' => [
                'required' => 'Product is required!',
                'min_length' => 'Product name should at least 3 characters',
                'max_length' => 'Product name should be maximum 150 characters'
            ],
            'category_id' => [
                'required' => 'Category is required!',
                'numeric' => 'Category should be numeric',
            ],
            'brand_id' => [
                'required' => 'Brand is required!',
                'numeric' => 'Brand should be numeric',
            ],
            'product_id' => [
                'required' => 'Product is required!',
                'numeric' => 'Product should be numeric',
            ],
            'description' => [
                'required' => 'Description is required!',
            ],
            'price' => [
                'required' => 'Price is required!',
                'decimal' => 'Price should a decimal',
            ],
            'image' => [
                'uploaded' => 'Product image is required',
                'mime_in' => 'Invalid file image type',
                'max_size' => 'Image max files size is 2MB'
            ],
            'category_name' => [
                'required' => 'Category is required!',
                'min_length' => 'Category name should at least 2 characters',
                'max_length' => 'Category name should be maximum 250 characters'
            ],
            'brand_name' => [
                'required' => 'Category is required!',
                'min_length' => 'Category name should at least 2 characters',
                'max_length' => 'Category name should be maximum 250 characters'
            ],
        ];
        if ($this->requests->getPost()) {
            $this->rulesAndMessages = $this->utilities->getRules($this->rules, $this->messages, $this->requests->getPost());
        }
    }

    protected function validatePost(array $rules, array $messages, array $post, string $proxy = ''): bool
    {
        $isFormValid = $this->validate($rules, $messages);
        foreach ($this->validator->getErrors() as $name => $errors) {
            $this->session->setFlashdata($proxy.'_error_'.$name, $errors);
        }
        if (count($this->validator->getErrors())) {
            foreach ($post as $name => $_) {
                $this->session->setFlashdata($proxy.'_value_'.$name, $post[$name]);
            }
        }
        return $isFormValid;
    }


    public function index()
    {
        if (!$this->utilities->isUserLogin('admin')) {
            return redirect()->to('/admin');
        }
        return view('main_view');
    }
    
    public function products()
    {
        if (!$this->utilities->isUserLogin('admin')) {
            return redirect()->to('/admin');
        }
        $categories = $this->ProductsModel->get_categories_brand('tbl_categories', 'category_id, category_name', 'created_at DESC');
        $brands = $this->ProductsModel->get_categories_brand('tbl_brands', 'brand_id, brand_name', 'created_at DESC');
        $products = $this->ProductsModel->get_products();

        return view('product_list_view', ['categories' => $categories, 'brands' => $brands, 'products' => $products]);
    }

    public function add_product_process()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "product")) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    if ($this->requests->getPost('isProductUpdate')) {
                        $imageContainer = [];
                        if($this->requests->getFiles()) {
                            $imageContainer = $this->upload_images($this->requests->getFiles());
                        }
                        $imageToBeDeleted = [];
                        if($this->requests->getPost('imageToBeDeleted')) {
                            $imageToBeDeleted = json_decode(stripslashes($this->requests->getPost('imageToBeDeleted')));
                            foreach($imageToBeDeleted as $image) {
                                unlink(ROOTPATH."public/assets/product_uploads/{$image->imageName}");
                            }
                            $data['imageToBeDeleted'] = $imageToBeDeleted;
                        }
                        $success = $this->ProductsModel->update_product($this->requests->getPost(), $imageContainer, $imageToBeDeleted);
                        $data['data'] = [
                            'product' => $success[0],
                            'isProductUpdated' => true,
                            'error' => $success == 'error' ? true : false,
                            'dump' => $this->requests->getPost()
                        ];
                        echo json_encode($data);
                    } else {
                        $imageContainer = $this->upload_images($this->requests->getFiles());
                        $success = $this->ProductsModel->add_product($this->requests->getPost(), $imageContainer);
                        $data['data'] = [
                            'isProductAdded' => $success ? true : false,
                            'error' => $success == 'error' ? true : false,
                            'product' => $success[0]
                        ];
                        $data['imagefiles'] = $this->requests->getFiles();
                        $data['productImages'] = $imageContainer;
                        echo json_encode($data);
                    }
                }
            }
        }
    }

    protected function upload_images(array $images): array
    {
        $imageContainer = [];
        $imageFiles = $images;

        foreach ($imageFiles['imageFiles'] as $key => $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                // Get file name and extension
                $newName = $file->getRandomName();
                $file->move(ROOTPATH.'public/assets/product_uploads', $newName);
                if ($this->requests->getPost('setMainImageIndex') == $key && $this->requests->getPost('setMainImageIndex') != null) {
                    $imageContainer[] = ['image' => $newName, 'status' => 1];
                } else {
                    $imageContainer[] = ['image' => $newName, 'status' => 0];
                }

            } else {
                $data['message'] = 'File not uploaded.';
            }
        }
        return $imageContainer;
    }

    public function update_process()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost())) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    if ($this->requests->getPost('category_name')) {
                        $success = $this->ProductsModel->update_category_brand('tbl_categories', $this->requests->getPost(), 'category');
                        $data['data'] = [
                            'exists' => @$success['category_exists'] ? 'Category name already exists' : false,
                            'updated' => count($success) ? 'Category has been updated' : false,
                            'error' => $success == 'error' ? true : false,
                            'category' => $success
                        ];
                        echo json_encode($data);
                    }
                    if ($this->requests->getPost('brand_name')) {
                        $success = $this->ProductsModel->update_category_brand('tbl_brands', $this->requests->getPost(), 'brand');
                        $data['data'] = [
                            'exists' => @$success['brand_exists'] ? 'Brand name already exists' : false,
                            'updated' => count($success) ? 'Brand has been updated' : false,
                            'error' => $success == 'error' ? true : false,
                            'brand' => $success
                        ];
                        echo json_encode($data);
                    }
                }
            }
        }
    }
    public function add_category_brand()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost())) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    if ($this->requests->getPost('category_name')) {
                        $success = $this->ProductsModel->add_category($this->requests->getPost());
                        $data['data'] = [
                            'exists' => @$success['category_exists'] ? 'Category name already exists' : false,
                            'added' => count($success) ? 'Category has been added' : false,
                            'error' => $success == 'error' ? true : false,
                            'category' => @$success[0]
                        ];
                        echo json_encode($data);
                    }
                    if ($this->requests->getPost('brand_name')) {
                        $success = $this->ProductsModel->add_brand($this->requests->getPost());
                        $data['data'] = [
                            'exists' => @$success['brand_exists'] ? 'Brand name already exists' : false,
                            'added' => count($success) ? 'Brand has been added' : false,
                            'error' => $success == 'error' ? true : false,
                            'brand' => @$success[0]
                        ];
                        echo json_encode($data);
                    }
                }
            }
        }
    }
    public function delete_process()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost())) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $indicator = $this->requests->getPost('indicator');
                    $table = $indicator == 'category' ? 'tbl_categories' : ($indicator == 'brand' ? 'tbl_brands' : '');
                    $success = $this->ProductsModel->delete_category_brand($table, $this->requests->getPost(), $indicator);
                    $data['data'] = [
                        'deleted' => $success ? ucwords($indicator) . " has been deleted" : false,
                        'error' => !$success ? true : false,
                        'id' => $success  ? $this->requests->getPost("{$indicator}_id") : false,
                        'indicator' => $indicator
                    ];
                    echo json_encode($data);
                }
            }
        }
    }

    public function get_product()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost())) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $data['data'] = $this->ProductsModel->get_product($this->requests->getPost());
                    echo json_encode($data);
                }
            }
        }
    }

    public function get_total_row_products()
    {
    }
}
