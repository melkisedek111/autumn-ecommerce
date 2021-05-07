<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
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

    public function get_products(int $id = null): array {
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, name, description, stock_quantity, stock_sold, stock_status, image');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->join('stocks', 'stocks.product_id = products.id', 'left');
        $products_builder->where('product_images.status', 1);
        $products_builder->orderBy('products.created_at', 'DESC');
        if($id) {
            $products_builder->where('products.id', $id);
        }
        $products = $products_builder->get();
        return $products->getResult();
    }

    public function get_product(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, name, description');
        $products_builder->join('categories', 'categories.id = products.category_id', 'left');
        $products_builder->join('brands', 'brands.id = products.brand_id', 'left');
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


    public function delete_product(array $posts): array {
        unset($posts['indicator']);
        $sanitizedPost = $this->sanitizing($posts);
        $product_image_builder = $this->db->table('product_images');
        $product_image_builder->select('image');
        $product_image_builder->where('product_id', $sanitizedPost['product_id']);
        $images = $product_image_builder->get();
        $productImages = $images->getResult();
       
        $products_builder = $this->db->table('products');
        $products_builder->where("id", $sanitizedPost["product_id"]);
        $products_builder->delete();
        
        if($productImages && $this->db->affectedRows()) {
            return $productImages;
        } else {
            return [];
        }
    }


    public function update_product(array $posts, array $images = [], array $imageToBeDeleted): array {
        unset($posts['imageToBeDeleted']);
        $sanitizedPost = $this->sanitizing($posts);
        if($imageToBeDeleted) {
            $imageId = [];
            foreach($imageToBeDeleted as $image) {
                $imageId[] = $image->id;
            }
            $sql = "DELETE FROM product_images WHERE id IN ?";
            $this->db->query($sql, [$imageId]);
            $this->db->affectedRows();
        }
        if($images || @$sanitizedPost['setMainImageIndex'] != null) {
            if(@$sanitizedPost['setMainImageIndex'] != null) {
                $sql = "UPDATE product_images SET status = 0 WHERE product_id = ?";
                $this->db->query($sql, [$sanitizedPost['product_id']]);
                $this->db->affectedRows();
            }
            foreach ($images as $image) {
                $imagesWithMainSet[] = ['product_id' => $sanitizedPost['product_id'], 'image' => $image['image'], 'status' => $image['status']];
            }
            $update_product_images_builder = $this->db->table('product_images');
            $update_product_images_builder->insertBatch($imagesWithMainSet);
        } else {
            $sql = "UPDATE product_images SET status = IF(id = ?, 1, 0) WHERE product_id = ?";
            $this->db->query($sql, [$sanitizedPost['previousImageSetMain'], $sanitizedPost['product_id']]);
            $this->db->affectedRows();
        }
        $update_products_builder = $this->db->table('products');
        $data = [
            'category_id' => $sanitizedPost['category_id'],
            'brand_id' => $sanitizedPost['brand_id'],
            'name' => $sanitizedPost['name'],
            'description' => $sanitizedPost['description'],
            'price' => $sanitizedPost['price'],
            'updated_at' => date("Y-m-d H:i:s"),
        ];
        $update_products_builder->where('id', $sanitizedPost['product_id']);
        $update_products_builder->update($data);
        if($this->db->affectedRows()) {
            return $this->get_products($sanitizedPost['product_id']);
        } else {
            return [];
        }
    }

    public function add_product(array $posts, array $images): array
    {
        unset($posts['setMainImageIndex']);
        $sanitizedPost = $this->sanitizing($posts);
        $insertProductDetailsQuery = $this->db->prepare(function ($db) {
            return $db->table('products')
              ->insert([
                   'category_id' => '1',
                   'brand_id' => '2',
                   'name' => '3',
                   'description' => '4',
                   'price' => '5',
              ]);
        });
        $result = $insertProductDetailsQuery->execute($sanitizedPost['category_id'], $sanitizedPost['brand_id'], $sanitizedPost['name'], $sanitizedPost['description'], $sanitizedPost['price']);
        $lastId = empty($result->error) ? $this->db->insertID() : 0;
        $imagesWithMainSet = [];
        foreach ($images as $image) {
            $imagesWithMainSet[] = ['product_id' => $lastId, 'image' => $image['image'], 'status' => $image['status']];
        }
        $insert_product_images_builder = $this->db->table('product_images');
        $insert_product_images_builder->insertBatch($imagesWithMainSet);
        $lastInsertedProduct = $this->get_products($lastId);
        return $lastInsertedProduct;
    }

    
    public function add_category(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $category_builder = $this->db->table('categories');
        $category_builder->select('id as category_id, category_name');
        $checkCategoryNameQuery = $category_builder->getWhere($sanitizedPost);
        if(count($checkCategoryNameQuery->getResult())) {
            return ['category_exists' => true];
        }
        $insertCategoryQuery = $this->db->prepare(function ($db) {
            return $db->table('categories')
              ->insert([
                   'category_name' => '1',
              ]);
        });
        $result = $insertCategoryQuery->execute($sanitizedPost['category_name']);
        $lastId = empty($result->error) ? $this->db->insertID() : 0;
        $getInsertedCategoryQuery = $category_builder->getWhere(['id' => $lastId], 1);
        return empty($result->error) ? $getInsertedCategoryQuery->getResult() : [];
    }

    public function add_brand(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $brand_builder = $this->db->table('brands');
        $brand_builder->select('id as brand_id, brand_name');
        $query = $brand_builder->getWhere($sanitizedPost);
        if(count($query->getResult())) {
            return ['brand_exists' => true];
        }
        $insertBrandQuery = $this->db->prepare(function ($db) {
            return $db->table('brands')
              ->insert([
                   'brand_name' => '1',
              ]);
        });
        $result = $insertBrandQuery->execute($sanitizedPost['brand_name']);
        $lastId = empty($result->error) ? $this->db->insertID() : 0;
        $getInsertedBrandQuery = $brand_builder->getWhere(['id' => $lastId], 1);
        return empty($result->error) ? $getInsertedBrandQuery->getResult() : [];
    }

    public function get_categories_brand(string $table, string $query, string $orderBy = ''): array {
        $category_brand_builder = $this->db->table($table);
        $category_brand_builder->select($query);
        $category_brand_builder->orderBy($orderBy);
        $query = $category_brand_builder->get();
        return $query->getResult();
    }


    public function update_category_brand(string $table, array $posts, string $name): array {
        $sanitizedPost = $this->sanitizing($posts);
        $category_brand_builder = $this->db->table($table);
        $checkResult = $category_brand_builder->getWhere(["{$name}_name" => $sanitizedPost["{$name}_name"]]);
        if(count($checkResult->getResult())) {
            return ["{$name}_exists" => true];
        }
        $category_brand_builder->set("{$name}_name", $sanitizedPost["{$name}_name"]);
        $category_brand_builder->where("id", $sanitizedPost["{$name}_id"]);
        $result = $category_brand_builder->update();
        if($result) {
            return ["{$name}_name" => $sanitizedPost["{$name}_name"]];
        } else {
            return [];
        }
    }

    public function delete_category_brand(string $table, array $posts, string $name) {
        $sanitizedPost = $this->sanitizing($posts);
        $category_brand_builder = $this->db->table($table);
        $category_brand_builder->where("id", $sanitizedPost["{$name}_id"]);
        $category_brand_builder->delete();
        return $this->db->affectedRows();
    }

    public function get_total_product_rows() {
        $products_builder = $this->db->table('products');
        $products_builder->select('COUNT(*) as product_rows');
        $query = $products_builder->get();
        return $query->getRow();
    }

    public function get_filter_products(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $getFilterLeadsQuery = "SELECT products.id as product_id, name, description, image FROM products INNER JOIN product_images ON product_images.product_id = products.id WHERE product_images.status = 1 ORDER BY products.created_at DESC LIMIT ?, ?";
        $query = $this->db->query($getFilterLeadsQuery, [intval($sanitizedPost['page_number']), 5]);
        $row = $query->getResult();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }

    public function get_latest_product(): array {
        $products_builder = $this->db->table('products');
        $products_builder = $this->db->table('products');
        $products_builder->select('products.id as product_id, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, name, description, image');
        $products_builder->join('categories', 'categories.id = products.category_id', 'left');
        $products_builder->join('brands', 'brands.id = products.brand_id', 'left');
        $products_builder->join('product_images', 'product_images.product_id = products.id');
        $products_builder->where('product_images.status', 1);
        $products_builder->limit(6);
        $products_builder->orderBy('products.created_at', 'DESC');
        $product = $products_builder->get();
        return $product->getResult();
    }

}
