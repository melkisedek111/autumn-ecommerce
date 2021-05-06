<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\ProductsModel;
use App\Models\ShopModel;

class Shop extends BaseController
{
    protected $session;
    protected $requests;
    protected $ProductsModel;
    protected $ShopModel;
    protected $token;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $utilities;
    public function __construct()
    {
        $this->ProductsModel = new ProductsModel;
        $this->ShopModel = new ShopModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->utilities = new Utilities;

        $this->rules =  [
                'page_number' => 'required|numeric',
                'indicator_id' => 'numeric',
        ];
        $this->messages = [
            'page_number' => [
                'required' => 'Page number is required!',
                'numeric' => 'Product should be numeric',
            ],
            'indicator_id' => [
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


    public function index(string $filter_keyword = '')
    {
        // if (!$this->utilities->isUserLogin('admin')) {
        //     return redirect()->to('/admin');
        // };
        $keyword = substr($filter_keyword, 2);
        $check_indicator = chop($filter_keyword, $keyword);

        if($check_indicator == 'c_') {
            $set_indicator = 'category';
        } elseif($check_indicator == 'b_') {
            $set_indicator = 'brand';
        } else {
            $set_indicator = '';
        }

        $searchCategoryBrand = [];
        $products = $this->ShopModel->get_items([], '');
        $items_per_category = $this->ShopModel->get_items_per_categories_brands('categories', 'category');
        foreach ($items_per_category['item_per_category'] as $category) {
            $convert_ampersand = str_replace('&amp;', '&', $category->category_name);
            $convert_spaces = str_replace(' ', '-', $convert_ampersand);
            $searchCategoryBrand[strtolower($convert_spaces)] = $category->category_id;
        }
        $items_per_brand = $this->ShopModel->get_items_per_categories_brands('brands', 'brand');
        foreach ($items_per_brand['item_per_brand'] as $brand) {
            $convert_ampersand = str_replace('&amp;', '&', $brand->brand_name);
            $convert_spaces = str_replace(' ', '-', $convert_ampersand);
            $searchCategoryBrand[strtolower($convert_spaces)] = $brand->brand_id;
        }


        if($filter_keyword !== '') {
            if (@$searchCategoryBrand[$keyword]) {
                $products = $this->ShopModel->get_items(['id' => $searchCategoryBrand[$keyword]], $set_indicator);
            } elseif(!isset($searchCategoryBrand[$keyword])) {
                $no_items_found = true;
            }
        }

         
        return view('shop_view', 
            [
            'items_per_category' => $items_per_category['item_per_category'], 
            'items_per_brand' => $items_per_brand['item_per_brand'], 
            'total_products' => $items_per_category['total_products'], 
            'products' => $products['products'], 
            'indicator' => $set_indicator,
            'total_products_row' => $products['total_rows'],
            'active' => @$searchCategoryBrand[$keyword] ? $searchCategoryBrand[$keyword] : 'all', 
            'no_items_found' => @$no_items_found,
            'truncate' => function ($text, $limit) {
            if (str_word_count($text, 0) > $limit) {
                    $words = str_word_count($text, 2);
                    $pos   = array_keys($words);
                    $text  = substr($text, 0, $pos[$limit]) . '...';
                }
                return $text;
            }
        ]);
    }

    public function filter_products() {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                $data['products'] = $this->ShopModel->get_items(['id' => $this->requests->getPost('indicator_id')], $this->requests->getPost('indicatorName'), $this->requests->getPost('page_number'));
                echo json_encode($data);
            }
        }
    }
}
