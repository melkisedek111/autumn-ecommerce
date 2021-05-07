<?php

namespace App\Models;

use CodeIgniter\Model;

class CheckoutsModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        date_default_timezone_set('Asia/Manila');
    }

    protected function sanitizing(array $array): array
    {
        return array_map(function ($post) {
            if (is_numeric($post)) {
                return filter_var($post, FILTER_SANITIZE_NUMBER_INT);
            } else {
                return filter_var($post, FILTER_SANITIZE_STRING);
            }
        }, $array);
    }

    public function insert_user_transaction($transaction): array {
        $transaction_builder = $this->db->table('transactions');
        $data = [
            'payment_id' => $transaction['payment_id'],
            'user_id' => $transaction['user_id'],
            'amount_paid' => $transaction['amount_paid'],
            'card_brand' => $transaction['card_brand'],
            'payment_type' => $transaction['payment_type'],
            'created_at' => $transaction['created_at'],
        ];
        $transaction_builder->insert($data);
        return $this->db->affectedRows() ? ['transaction_id' => $this->db->insertID()] : [];
    }

    public function insert_user_order(array $posts, string $transation_id, string $user_id, string $totalCartItems, string $totalAmount): array {
        $sanitized_post = $this->sanitizing($posts);
        $data = [
            'user_id' => $user_id,
            'transaction_id' => $transation_id,
            'quantity' => $totalCartItems,
            'total_amount' => $totalAmount,
            'order_status' => 'Order in process',
            'first_name' => $sanitized_post['first_name'],
            'last_name' => $sanitized_post['last_name'],
            'address' => $sanitized_post['address'],
            'contact' => $sanitized_post['contact'],
            'city' => $sanitized_post['city'],
            'province' => $sanitized_post['province'],
            'zipcode' => $sanitized_post['zipcode'],
        ];
        $order_builder = $this->db->table('orders');
        $order_builder->insert($data);
        return $this->db->affectedRows() ? ['order_id' => $this->db->insertID()] : [];
    }


    public function insert_order_products(array $cartItems, string $order_id): bool {
        $order_products_builder = $this->db->table('order_products');
        $data = [];
        foreach($cartItems as $item) {
            $data[] = [
                'order_id' => $order_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product_price,
                'total_amount' => $item->product_price * $item->quantity
            ];
        }
        $order_products_builder->insertBatch($data);
        return $this->db->affectedRows() ? true : false;
    }


    public function get_user_order() {
        $order_builder = $this->db->table('orders');
        $order_builder->select("JSON_ARRAYAGG(JSON_OBJECT('id', id, 'transaction_id', transaction_id, 'quantity', quantity, 'total_amount', total_amount, 'order_status', order_status, 'first_name', first_name, 'last_name', last_name, 'address', address, 'contact', contact, 'city', city, 'province', province, 'zipcode', zipcode, 'created_at', created_at)) AS JSON_ORDER");
        $orders = $order_builder->get();
        return $orders->getRow();
    }

    public function update_oders_status(array $posts): bool {
        $sanitized_post = $this->sanitizing($posts);
        $order_builder = $this->db->table('orders');
        $order_builder->set('order_status', $sanitized_post['set_value']);
        $order_builder->where('id', $sanitized_post['order_id']);
        $order_builder->update();
        return $this->db->affectedRows() ? true : false;
    }


    public function get_user_order_by_id(array $posts): array {
        $sanitized_post = $this->sanitizing($posts);
        $order_builder = $this->db->table('orders');
        $order_builder->select('*');
        $order_builder->where('id', $sanitized_post['order_id']);
        $order = $order_builder->get();
        $order_details = $order->getRow();
        if($order_details == null) {
            return [];
        }
        $address_builder = $this->db->table('addresses');
        $address_builder->select('address, contact, city, province, zipcode, first_name, last_name, email');
        $address_builder->join('users', 'users.id = addresses.user_id');
        $address_builder->where('user_id', $order_details->user_id);
        $address = $address_builder->get();
        $address_details = $address->getRow();

        $order_products = $this->db->table('order_products');
        $order_products->select('products.name as product_name, order_products.price as product_price, total_amount, category_name, brand_name, quantity, image');
        $order_products->join('products', 'products.id = order_products.product_id', 'left');
        $order_products->join('categories', 'categories.id = products.category_id', 'left');
        $order_products->join('brands', 'brands.id = products.brand_id', 'left');
        $order_products->join('product_images', 'product_images.product_id = products.id', 'left');
        $order_products->where('product_images.status', 1);
        $order_products->where('order_products.order_id', $sanitized_post['order_id']);
        $order_products = $order_products->get();
        $order_products_details = $order_products->getResult();
        
        return ['order_details' => $order_details, 'address_details' => $address_details, 'order_products_details' => $order_products_details];
    }




    







}
