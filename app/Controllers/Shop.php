<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\ProductsModel;

class Shop extends BaseController
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
                'page_number' => 'required|numeric',
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
            'page_number' => [
                'required' => 'Page number is required!',
                'numeric' => 'Product should be numeric',
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
        // if (!$this->utilities->isUserLogin('admin')) {
        //     return redirect()->to('/admin');
        // }
        return view('shop_view');
    }
}
