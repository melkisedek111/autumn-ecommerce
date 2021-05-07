<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
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

    public function check_email(array $email) {
        $sanitized_posts = $this->sanitizing($email);
        $builder = $this->db->table('users');
        $builder->select('email');
        $query = $builder->getWhere(['email' => $sanitized_posts['email']]);
        return $query->getResult();
    }

    public function insert_user(array $posts):bool
    {
        // Prepare the Query
        unset($posts['register']);
        unset($posts['confirm_password']);
        $sanitized_posts = $this->sanitizing($posts);
        $salt = bin2hex(openssl_random_pseudo_bytes(22)); // hashing for password
        $encrypted_password = md5($sanitized_posts['password']. '' .$salt);
        $pQuery = $this->db->prepare(function ($db) {
            return $db->table('users')
              ->insert([
                   'email' => '1',
                   'encrypted_password' => '2',
                   'first_name' => '3',
                   'last_name' => '4',
                   'salt' => '5',
              ]);
        });
        // Run the Query
        $result = $pQuery->execute($sanitized_posts['email'], $encrypted_password, $sanitized_posts['first_name'], $sanitized_posts['last_name'], $salt);
        return empty($result->error) ? true : false;
    }

    public function login_user(array $posts) {
        unset($posts['login']);
        $sanitized_posts = $this->sanitizing($posts);
        $builder = $this->db->table('users');
        $builder->select('users.id as user_id, first_name, last_name, email, encrypted_password, salt, image, user_type');
        $query = $builder->getWhere(['email' => $sanitized_posts['email']]);
        $user = $query->getRow();
        $login_encrypted_password = md5("{$sanitized_posts['password']}{$user->salt}");
        if($login_encrypted_password === $user->encrypted_password) {
            return $user;
        } else {
            return [];
        }
    }

    public function set_user_address(array $posts, string $user_id) {
        unset($posts['set_address']);
        $sanitized_posts = $this->sanitizing($posts);
        $sanitized_posts['isShipping'] = 1;
        $sanitized_posts['isBilling'] = 0;
        $sanitized_posts['user_id'] = $user_id;
        $address_builder = $this->db->table('addresses');
        $address_builder->insert($sanitized_posts);
        return $this->db->affectedRows();
    }

    public function check_user_address(array $posts) {
        $sanitized_posts = $this->sanitizing($posts);
        $address_builder = $this->db->table('addresses');
        $address_builder->select('COUNT(*) as address_count');
        $address_builder->where('user_id', $sanitized_posts['user_id']);
        $address_builder->where('isShipping', 1);
        $address_count = $address_builder->get();
        return $address_count->getRow();
    }

    public function get_user_address(array $posts) {
        $sanitized_posts = $this->sanitizing($posts);
        $address_builder = $this->db->table('addresses');
        $address_builder->select('*');
        $address_builder->where('user_id', $sanitized_posts['user_id']);
        $address_builder->where('isShipping', 1);
        $address_count = $address_builder->get();
        return $address_count->getRow();
    }

    public function get_user_orders(array $posts): array {
        $sanitized_posts = $this->sanitizing($posts);
        $order_builder = $this->db->table('orders');
        $order_builder->select('orders.id as order_id, quantity, total_amount, order_status, orders.created_at as order_created');
        $order_builder->where('user_id', $sanitized_posts['user_id']);
        $orders = $order_builder->get();
        $order_details = $orders->getResult();
        if(count($order_details)) {
            return [];
        }
        $order_product_lists = [];
        foreach($order_details as $order) {
            $order_products = $this->db->table('order_products');
            $order_products->select('products.name as product_name, order_products.price as product_price, total_amount, category_name, brand_name, quantity, image');
            $order_products->join('products', 'products.id = order_products.product_id', 'left');
            $order_products->join('categories', 'categories.id = products.category_id', 'left');
            $order_products->join('brands', 'brands.id = products.brand_id', 'left');
            $order_products->join('product_images', 'product_images.product_id = products.id', 'left');
            $order_products->where('product_images.status', 1);
            $order_products->where('order_products.order_id', $order->order_id);
            $order_products = $order_products->get();
            $order_product_lists[$order->order_id] = $order_products->getResult();
        }
        return ['order_details' => $order_details, 'order_product_lists' => $order_product_lists];
    }

}
