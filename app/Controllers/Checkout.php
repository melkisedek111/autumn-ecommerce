<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\CartsModel;
use App\Models\CheckoutsModel;
use App\Models\UsersModel;

class Checkout extends BaseController
{
    protected $session;
    protected $requests;
    protected $CartsModel;
    protected $UsersModel;
    protected $CheckoutsModel;
    protected $token;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $utilities;
    public function __construct()
    {
        $this->CartsModel = new CartsModel;
        $this->UsersModel = new UsersModel;
        $this->CheckoutsModel = new CheckoutsModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->utilities = new Utilities;

        $this->rules =  [
            'first_name' => 'required|min_length[3]|max_length[150]|alpha_space',
            'last_name' => 'required|min_length[3]|max_length[150]|alpha_space',
            'address' => 'required',
            'contact' => 'required|numeric',
            'city' => 'required|alpha_space',
            'province' => 'required|alpha_space',
            'zipcode' => 'required|numeric',
        ];
        $this->messages = [
            'first_name' => [
                'required' => 'First name is required',
                'min_length' => 'First name must be at least 3 characters',
                'max_length' => 'First name is too long!',
                'alpha' => 'First name should be letter'
            ],
            'last_name' => [
                'required' => 'Last name is required',
                'min_length' => 'Last name must be at least 3 characters',
                'max_length' => 'Last name is too long!',
                'alpha' => 'Last name should be letter'
            ],
            'address' => [
                'required' => 'Address is required',
            ],
            'contact' => [
                'required' => 'Contact is required',
                'numeric' => 'Contact should be numeric',
            ],
            'city' => [
                'required' => 'City is required',
                'alpha' => 'City should be letter'
            ],
            'province' => [
                'required' => 'Province is required',
                'alpha' => 'Province should be letter'
            ],
            'zipcode' => [
                'required' => 'Zipcode is required',
                'numeric' => 'Zipcode should be numeric',
            ]
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
        $user_shipping_address = $this->UsersModel->get_user_address(['user_id' => $user->user_id]);
        $cart_items = $this->CartsModel->get_user_cart(['user_id' => $user->user_id]);
        return view('checkout_view',['cart_items' => $cart_items, 'user_shipping_address' => $user_shipping_address]);
    }

    public function checkout_process() {

        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "checkout")) {
                return redirect()->to('/checkout');
            } else {
                $stripe = new \Stripe\StripeClient('sk_test_51Guub0ESprxiEf1pK5XHsf41dMIqdRW9wmJQR7Cqeogc4UiSb47QR4XrSdIIek3UyiowaQmYceCJGvC51bjjsuSO00eUaQlx5b');
                $user = $this->session->get('user');
                $cart_items = $this->CartsModel->get_user_cart(['user_id' => $user->user_id]);
                $customer = $stripe->customers->create([
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    "source" => $this->requests->getPost('stripeToken')
                ]);
                $totalAmount = array_reduce($cart_items, function($accu, $curr) {
                    $accu += $curr->product_price * $curr->quantity;
                    return $accu;
                }); 
                $charge = $stripe->charges->create(array(
                    "amount" => round($totalAmount, 2) * 100,
                    "currency" => "USD",
                    "description" => "Payment for the ".$totalAmount. " items from your cart",
                    "customer" => $customer->id
                ));
                $transaction = $this->CheckoutsModel->insert_user_transaction([
                    'payment_id' => $charge->id,
                    'user_id' => $user->user_id,
                    'amount_paid' => $totalAmount,
                    'card_brand' => $charge->source->brand,
                    'payment_type' => $charge->payment_method_details->type,
                    'created_at' => $charge->created,
                ]);

                if($transaction) {
                    $totalItem = array_reduce($cart_items, function($carry, $item){
                        $carry += $item->quantity;
                        return $carry;
                    });
                    $order = $this->CheckoutsModel->insert_user_order($this->requests->getPost(), $transaction['transaction_id'], $user->user_id, $totalItem, $charge->amount_captured);
                    $order_products = $this->CheckoutsModel->insert_order_products($cart_items, $order['order_id']);
                    if($order_products) {
                        $this->CartsModel->remove_all_items_from_cart($cart_items, $user->user_id);
                    }
                    if($charge->paid) {
                        $this->session->setFlashdata('paid', $charge->paid);
                        $this->session->setFlashdata('transaction_id', $charge->id);
                        $this->session->setFlashdata('amount_captured', $charge->amount_captured);
                        $this->session->setFlashdata('description', $charge->description);
                        $this->session->setFlashdata('created', $charge->created);
                        $this->session->setFlashdata('brand', $charge->source->brand);
                        $this->session->setFlashdata('type', $charge->payment_method_details->type);
                        $this->session->setFlashdata('id', $charge->id);
                        $this->session->setFlashdata('email', $user->email);
                        $this->session->setFlashdata('full_name', $this->requests->getPost('first_name') . ' ' . $this->requests->getPost('last_name'));
                        $this->session->setFlashdata('address', $this->requests->getPost('address'));
                        $this->session->setFlashdata('city', $this->requests->getPost('city'));
                        $this->session->setFlashdata('province', $this->requests->getPost('province'));
                        $this->session->setFlashdata('zipcode', $this->requests->getPost('zipcode'));
                        $this->session->setFlashdata('checkout_success', true);
                        return redirect()->to('/success');
                    }
                }
            }
        }
    }

    public function success() {
        if($this->session->has('paid') && $this->session->has('checkout_success')) {
            $payment_data = [
                'paid' => $this->session->get('paid'),
                'transaction_id' => $this->session->get('transaction_id'),
                'amount_captured' => $this->session->get('amount_captured'),
                'description' => $this->session->get('description'),
                'created' => $this->session->get('created'),
                'brand' => $this->session->get('brand'),
                'type' => $this->session->get('type'),
                'id' => $this->session->get('id'),
                'email' => $this->session->get('email'),
                'full_name' => $this->session->get('full_name'),
                'address' => $this->session->get('address'),
                'city' => $this->session->get('city'),
                'province' => $this->session->get('province'),
                'zipcode' => $this->session->get('zipcode'),
                'checkout_success' => $this->session->get('checkout_success'),
            ];
            return view('checkout_success_view', ['payment_data' => $payment_data]);
        } else {
            return redirect()->to('/cart');
        }
        // $payment_data = [
        //     'paid' => '',
        //     'transaction_id' => '',
        //     'amount_captured' => '',
        //     'description' => '',
        //     'created' => '',
        //     'brand' => '',
        //     'type' => '',
        //     'id' => '',
        //     'email' => '',
        //     'full_name' => '',
        // ];
        // return view('checkout_success_view', ['payment_data' => $payment_data]);
    }
    // public function get_checkout() {
    //     $this->session->setFlashdata('checkout', true);
    //     return redirect()->to('/checkout');
    // }

 

    



}
