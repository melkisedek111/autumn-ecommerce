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
                'description' => 'required',
                'price' => 'required|decimal',

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
                'numeric' => 'Category should be numeric',
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
            ]
        ];
        if ($this->requests->getPost()) {
            $this->rulesAndMessages = $this->utilities->getRules($this->rules, $this->messages, $this->requests->getPost());

        }
    }

    protected function validatePost(array $rules, array $messages, array $post, string $proxy): bool
    {
        $imageRules = [
            'imageFiles' => [
                'uploaded[imageFiles]',
                'mime_in[imageFiles,image/jpg,image/jpeg,image/png]',
                'max_size[imageFiles,2024]',
            ]
        ];
        $imageMessage = [
            'imageFiles' => [
                'uploaded' => 'Product image is required',
                'mime_in' => 'Invalid file image type',
                'max_size' => 'Image max files size is 2MB'
            ]
        ];

        $isFormValid = $this->validate(array_merge($rules, $imageRules), array_merge($messages, $imageMessage));
        foreach ($this->validator->getErrors() as $name => $errors) {
            $this->session->setFlashdata($proxy.'_error_'.$name, $errors);
        }
        foreach ($post as $name => $_) {
            $this->session->setFlashdata($proxy.'_value_'.$name, $post[$name]);
        }
        return $isFormValid;
    }

    protected function validatePostImage(string $proxy): bool
    {
        $imageValid = $this->validate([
            'imageFiles' => [
                'uploaded[imageFiles]',
                'mime_in[imageFiles,image/jpg,image/jpeg,image/png]',
                'max_size[imageFiles,2024]',
            ]
            ],[
                'imageFiles' => [
                    'uploaded' => 'Product image is required',
                    'mime_in' => 'Invalid file image type',
                    'max_size' => 'Image max files size is 2MB'
                ]
            ]);
        foreach ($this->validator->getErrors() as $name => $errors) {
            $this->session->setFlashdata($proxy.'_error_'.$name, $errors);
        }
        return $imageValid;
    }

    public function index()
    {
        if(!$this->utilities->isUserLogin('admin')) return redirect()->to('/admin');
        return view('main_view');
    }  
    
    public function products() {
        if(!$this->utilities->isUserLogin('admin')) return redirect()->to('/admin');
        $categories = [
            ['category_id' => 1, 'category_name' => 'Bags & Backpacks'],
            ['category_id' => 2, 'category_name' => 'Pands & Jeans'],
            ['category_id' => 3, 'category_name' => 'Pendants & Necklace'],
            ['category_id' => 4, 'category_name' => 'T Shirts'],
        ];
        $brands = [
            ['brand_id' => 1, 'brand_name' => 'The North Face'],
            ['brand_id' => 2, 'brand_name' => 'The Chanel'],
            ['brand_id' => 3, 'brand_name' => 'Altarago'],
            ['brand_id' => 4, 'brand_name' => 'Johny Doe'],
        ];
        return view('product_list_view', ['categories' => $categories, 'brands' => $brands]);
    }

    public function add_product_process() {
        // echo '<pre>';
        // echo WRITEPATH;
        // echo PHP_EOL;
        // var_dump($this->requests->getFiles('productImages'));
        // // $file = $this->requests->getFileMultiple('productImages')[0];
        // // $newName = $file->getRandomName();
        // // var_dump($file->move(ROOTPATH.'public/assets/product_uploads', $newName));
        // exit;
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token


        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "product")) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $imageContainer = [];
                    $imageFiles = $this->requests->getFiles();
                    // var_dump($imageFiles);
                    foreach($imageFiles['imageFiles'] as $key => $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            // Get file name and extension
                            $newName = $file->getRandomName();
                            $file->move(ROOTPATH.'public/assets/product_uploads', $newName);
                            if($this->requests->getPost('setMainImageIndex') == $key) {
                                $imageContainer[] = ['image' => $newName, 'status' => 1];
                            } else {
                                $imageContainer[] = ['image' => $newName, 'status' => 0];
                            }
                         }else{
                            // Response
                            $data['success'] = 2;
                            $data['message'] = 'File not uploaded.'; 
                         }
                    }
                    var_dump($imageContainer);
                    // $data['data'] = $this->requests->getPost();
                    // $data['productImages'] = $this->requests->getFiles('productImages');
                    // echo json_encode($data);
                    // var_dump($this->requests->getFiles('productImages'));
                    exit;
                }
            }
            
        }

    }
}
