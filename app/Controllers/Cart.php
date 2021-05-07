<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\CartsModel;
use App\Models\ProductsModel;

class Cart extends BaseController
{
    protected $session;
    protected $requests;
    protected $CartsModel;
    protected $ShopModel;
    protected $ProductsModel;
    protected $token;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $utilities;
    public function __construct()
    {
        $this->CartsModel = new CartsModel;
        $this->ProductsModel = new ProductsModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->utilities = new Utilities;

        $this->rules =  [
                'cart_id' => 'numeric',
                'quantity' => 'numeric',
        ];
        $this->messages = [
            'cart_id' => [
                'numeric' => 'Cart ID should be numeric',
            ],
            'quantity' => [
                'numeric' => 'Quantity should be numeric',
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
        $user = $this->session->get('user');
        $cart_items = $this->CartsModel->get_user_cart(['user_id' => $user->user_id]);
        return view('cart_view',['cart_items' => $cart_items]);
    }

    public function delete_cart_item() {
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "product")) {
                $this->utilities->alert('alertFixedDanger', 'Internal Error', 'Please reload the page!');
                return redirect()->to('/cart');
            } else {
                $cart_item_deleted = $this->CartsModel->remove_item_from_cart(['cart_id' => $this->requests->getPost('cart_id')]);
                if($cart_item_deleted) {
                    $this->utilities->alert('alertFixedSuccess text-left', 'Cart Item Deleted', 'You have deleted an item in your cart');
                } else {
                    $this->utilities->alert('alertFixedDanger text-left', 'Internal Error', 'Please reload the page!');
                }
                return redirect()->to('/cart');
            }
        }
    }

    public function add_remove_item_process() {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "cart")) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                $user = $this->session->get('user');
                $cart = $this->CartsModel->add_product_to_cart(['product_id' => $this->requests->getPost('product_id'),'cart_id' => $this->requests->getPost('cart_id'), 'user_id' => $user->user_id, 'quantity' => 1], $this->requests->getPost('process'));
                if($cart) {
                    $data['cart'] = $this->CartsModel->get_user_cart_header(['user_id' => $user->user_id]);
                } else {
                    $data['error'] = 'Something went wrong';
                }                
                echo json_encode($data);
            }
        }
    }

    public function cart_on_header()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                $user = $this->session->get('user');
                $data['cart'] = $this->CartsModel->get_user_cart_header(['user_id' => $user->user_id]);
                echo json_encode($data);
            }
        }
    }

    public function add_to_cart_process() {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "product")) {
                $data['internalValidationError'] = true;
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $user = $this->session->get('user');
                    $cart = $this->CartsModel->add_product_to_cart(['product_id' => $this->requests->getPost('product_id'), 'user_id' => $user->user_id, 'quantity' => $this->requests->getPost('quantity')]);
                    $check_product = $this->ProductsModel->check_has_product_stock(['product_id' => $this->requests->getPost('product_id'), 'quantity' => $this->requests->getPost('quantity')]);
                    if($check_product) {
                        if($cart) {
                            $data['cart'] = $this->CartsModel->get_user_cart_header(['user_id' => $user->user_id]);
                        } else {
                            $data['error'] = 'Something went wrong';
                        }
                    } else {
                        $data['no_stock'] = 'Not enough stock, please adjust your quantitiy';
                    }

                    echo json_encode($data);
                }
            }
        }
    }


}
