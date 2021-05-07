<?php

namespace App\Models;

use CodeIgniter\Model;

class CartsModel extends Model
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
    public function get_user_cart(array $posts): array {
        $sanitized_post = $this->sanitizing($posts);
        $cart_builder = $this->db->table('carts');
        $cart_builder->select('carts.id as cart_id, products.id as product_id, products.name as product_name, products.price as product_price, quantity, image, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, description');
        $cart_builder->join('products', 'products.id = carts.product_id');
        $cart_builder->join('categories', 'categories.id = products.category_id', 'left');
        $cart_builder->join('brands', 'brands.id = products.brand_id', 'left'); 
        $cart_builder->join('product_images', 'product_images.product_id = carts.product_id');
        $cart_builder->where('product_images.status', 1);
        $cart_builder->where('carts.user_id', $sanitized_post['user_id']);
        $get_cart = $cart_builder->get();
        return $get_cart->getResult();
    }

    public function get_user_cart_header(array $posts): array {
        $sanitized_post = $this->sanitizing($posts);
        $cart_builder = $this->db->table('carts');
        $cart_builder->select('carts.id as cart_id, products.id as product_id, products.name as product_name, products.price as product_price, quantity, image');
        $cart_builder->join('products', 'products.id = carts.product_id');
        $cart_builder->join('product_images', 'product_images.product_id = carts.product_id');
        $cart_builder->where('product_images.status', 1);
        $cart_builder->where('carts.user_id', $sanitized_post['user_id']);
        $get_cart = $cart_builder->get();
        return $get_cart->getResult();
    }

    public function add_product_to_cart(array $posts, string $process = 'add'): bool {
        $sanitized_post = $this->sanitizing($posts);
        $cart_builder = $this->db->table('carts');

        $cart_builder->select('id, product_id, user_id, quantity');
        $cart_builder->where('product_id', $sanitized_post['product_id']);
        $cart_builder->where('user_id', $sanitized_post['user_id']);
        if(@$sanitized_post['cart_id']) {
            $cart_builder->where('carts.id', $sanitized_post['cart_id']);
        }
        $product_exist = $cart_builder->get();
        $cart_item = $product_exist->getResult();
        if(count($cart_item)) {
            if($process == 'add') {
                $cart_builder->set('quantity', $cart_item[0]->quantity + intval($sanitized_post['quantity']));
            } elseif($process == 'remove') {
                $cart_builder->set('quantity', $cart_item[0]->quantity - intval($sanitized_post['quantity']));
            }
            $cart_builder->where('product_id', $sanitized_post['product_id']);
            $cart_builder->where('user_id', $sanitized_post['user_id']);
            if(@$sanitized_post['cart_id']) {
                $cart_builder->where('carts.id', $sanitized_post['cart_id']);
            }
            $cart_builder->update();
        } else {
            $cart_builder->insert($sanitized_post);
        }        
        return $this->db->affectedRows() ? true : false;
    }

    public function remove_item_from_cart(array $posts): bool{
        $sanitized_post = $this->sanitizing($posts);
        $cart_builder = $this->db->table('carts');       
        $cart_builder->where("id", $sanitized_post["cart_id"]);
        $cart_builder->delete();
        return $this->db->affectedRows() ? true : false;
    }

    public function remove_all_items_from_cart(array $posts, $user_id): bool {
        $cart_builder = $this->db->table('carts');
        foreach($posts as $item) {
            $cart_builder->where('id',  $item->cart_id);
            $cart_builder->where('user_id',  $user_id);
            $cart_builder->delete();
        }
        return $this->db->affectedRows() ? true : false;
    }
}
