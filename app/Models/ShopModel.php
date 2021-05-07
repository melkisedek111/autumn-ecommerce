<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopModel extends Model
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
        $products_builder->select('products.id as product_id, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, name, description, image, stock_quantity, stock_sold, stock_status');
        $products_builder->join('categories', 'categories.id = products.category_id', 'left');
        $products_builder->join('brands', 'brands.id = products.brand_id', 'left');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->join('stocks', 'stocks.product_id = products.id', 'left');
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
