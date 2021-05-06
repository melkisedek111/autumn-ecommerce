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
        if($sanitized_post['cart_id'] != null) {
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
            if($sanitized_post['cart_id'] != null) {
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









    public function get_items_per_categories_brands(string $table, string $name): array {
        $category_builder = $this->db->table($table);
        $category_builder->select("{$table}.id as {$name}_id, {$name}_name, COUNT(products.id) as items_per_{$name}");
        $category_builder->join('products', "{$table}.id = products.{$name}_id", 'left');
        $category_builder->groupBy("{$name}_name");
        $item_per_category = $category_builder->get();
        $products_builder = $this->db->table('products');
        $products_builder->select('COUNT(*) as total_products');
        $total_products = $products_builder->get();
        return ["item_per_{$name}" => $item_per_category->getResult(), 'total_products' => $total_products->getRow()];
    }

    public function check_indicator() {

    }

    public function get_items(array $posts = [], string $name = '', $offset = 0): array {
        $sanitized_post = $this->sanitizing($posts);
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, name, description, price, image');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->orderBy('products.created_at', 'DESC');

        if($name != '') {
            $products_builder->limit(9, $offset);
            $products_builder->where(["{$name}_id" => $sanitized_post['id']]);

            $check_row_products = $this->db->table('products');
            $check_row_products->select('COUNT(*) as total_rows');
            $check_row_products->where(["{$name}_id" => $sanitized_post['id']]);
            $query = $check_row_products->get();
        } else {
            $products_builder->limit(9, $offset);
            
            $check_row_products = $this->db->table('products');
            $check_row_products->select('COUNT(*) as total_rows');
            $query = $check_row_products->get();
            
        }
        $products = $products_builder->get();
        return ['products' => $products->getResult(), 'total_rows' => $query->getRow()->total_rows];

    }

    public function get_product_by_name(array $posts): array {
        $sanitized_post = $this->sanitizing($posts);
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, name, description, price, image');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->like('name', $sanitized_post['name']);
        $products_builder->orderBy('products.created_at', 'DESC');
        $products = $products_builder->get();
        return ['products' => $products->getResult()];
    }

    public function get_product_by_price(array $posts): array {
        $sanitized_post = $this->sanitizing($posts);
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, name, description, price, image');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->where('price >=', $sanitized_post['min_price']);
        $products_builder->where('price <=', $sanitized_post['max_price']);
        $products_builder->orderBy('products.created_at', 'DESC');
        $products = $products_builder->get();
        return ['products' => $products->getResult()];
    }

    public function get_min_max_price(): array {
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, name, description, price, image');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->selectMax('price', 'maxPrice');
        $products_builder->selectMin('price', 'minPrice');
        $products_builder->orderBy('products.created_at', 'DESC');
        $products = $products_builder->get();
        return ['prices' => $products->getRow()];
    }

    public function get_product_by_id(array $posts) {
        $sanitizedPost = $this->sanitizing($posts);
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, name, description, image');
        $products_builder->join('categories', 'categories.id = products.category_id', 'left');
        $products_builder->join('brands', 'brands.id = products.brand_id', 'left');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->where('products.id', $sanitizedPost['product_id']);
        $product = $products_builder->get();
        $productDetails = $product->getRow();
        
        $product_image_builder = $this->db->table('product_images');
        $product_image_builder->select('id as image_id, product_id, image, status');
        $product_image_builder->where('product_id', $sanitizedPost['product_id']);
        $images = $product_image_builder->get();
        $productImages = $images->getResult();
        return ['details' => $productDetails, 'images' => $productImages];
    }








}
