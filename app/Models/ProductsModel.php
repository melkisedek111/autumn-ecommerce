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
        $productsBuilder = $this->db->table('products');
        $productsBuilder->select('products.id as product_id, name, description, stock_quantity, stock_sold, stock_status, image');
        $productsBuilder->join('product_images', 'product_images.product_id = products.id');
        $productsBuilder->join('stocks', 'stocks.product_id = products.id', 'left');
        $productsBuilder->where('product_images.status', 1);
        $productsBuilder->orderBy('products.created_at', 'DESC');
        if($id) {
            $productsBuilder->where('products.id', $id);
        }
        $products = $productsBuilder->get();
        return $products->getResult();
    }

    public function get_product(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $productsBuilder = $this->db->table('products');
        $productsBuilder->select('products.id as product_id, products.category_id as category_id, category_name, products.brand_id as brand_id, brand_name, price, name, description');
        $productsBuilder->join('categories', 'categories.id = products.category_id', 'left');
        $productsBuilder->join('brands', 'brands.id = products.brand_id', 'left');
        $productsBuilder->where('products.id', $sanitizedPost['product_id']);
        $product = $productsBuilder->get();
        $productDetails = $product->getRow();
        
        $productImageBuilder = $this->db->table('product_images');
        $productImageBuilder->select('id as image_id, product_id, image, status');
        $productImageBuilder->where('product_id', $sanitizedPost['product_id']);
        $images = $productImageBuilder->get();
        $productImages = $images->getResult();
        return ['details' => $productDetails, 'images' => $productImages];
    }


    public function delete_product(array $posts): array {
        unset($posts['indicator']);
        $sanitizedPost = $this->sanitizing($posts);
        $productImageBuilder = $this->db->table('product_images');
        $productImageBuilder->select('image');
        $productImageBuilder->where('product_id', $sanitizedPost['product_id']);
        $images = $productImageBuilder->get();
        $productImages = $images->getResult();
       
        $productBuilder = $this->db->table('products');
        $productBuilder->where("id", $sanitizedPost["product_id"]);
        $productBuilder->delete();
        
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
            $updateProductImagesBuilder = $this->db->table('product_images');
            $updateProductImagesBuilder->insertBatch($imagesWithMainSet);
        } else {
            $sql = "UPDATE product_images SET status = IF(id = ?, 1, 0) WHERE product_id = ?";
            $this->db->query($sql, [$sanitizedPost['previousImageSetMain'], $sanitizedPost['product_id']]);
            $this->db->affectedRows();
        }
        $updateProductBuilder = $this->db->table('products');
        $data = [
            'category_id' => $sanitizedPost['category_id'],
            'brand_id' => $sanitizedPost['brand_id'],
            'name' => $sanitizedPost['name'],
            'description' => $sanitizedPost['description'],
            'price' => $sanitizedPost['price'],
            'updated_at' => date("Y-m-d H:i:s"),
        ];
        $updateProductBuilder->where('id', $sanitizedPost['product_id']);
        $updateProductBuilder->update($data);
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
        $insertProductImagesBuilder = $this->db->table('product_images');
        $insertProductImagesBuilder->insertBatch($imagesWithMainSet);
        $lastInsertedProduct = $this->get_products($lastId);
        return $lastInsertedProduct;
    }

    
    public function add_category(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $categoryBuilder = $this->db->table('categories');
        $categoryBuilder->select('id as category_id, category_name');
        $checkCategoryNameQuery = $categoryBuilder->getWhere($sanitizedPost);
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
        $getInsertedCategoryQuery = $categoryBuilder->getWhere(['id' => $lastId], 1);
        return empty($result->error) ? $getInsertedCategoryQuery->getResult() : [];
    }

    public function add_brand(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $brandBuilder = $this->db->table('brands');
        $brandBuilder->select('id as brand_id, brand_name');
        $query = $brandBuilder->getWhere($sanitizedPost);
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
        $getInsertedBrandQuery = $brandBuilder->getWhere(['id' => $lastId], 1);
        return empty($result->error) ? $getInsertedBrandQuery->getResult() : [];
    }

    public function get_categories_brand(string $table, string $query, string $orderBy = ''): array {
        $categoryBrandBuilder = $this->db->table($table);
        $categoryBrandBuilder->select($query);
        $categoryBrandBuilder->orderBy($orderBy);
        $query = $categoryBrandBuilder->get();
        return $query->getResult();
    }


    public function update_category_brand(string $table, array $posts, string $name): array {
        $sanitizedPost = $this->sanitizing($posts);
        $categoryBrandBuilder = $this->db->table($table);
        $checkResult = $categoryBrandBuilder->getWhere(["{$name}_name" => $sanitizedPost["{$name}_name"]]);
        if(count($checkResult->getResult())) {
            return ["{$name}_exists" => true];
        }
        $categoryBrandBuilder->set("{$name}_name", $sanitizedPost["{$name}_name"]);
        $categoryBrandBuilder->where("id", $sanitizedPost["{$name}_id"]);
        $result = $categoryBrandBuilder->update();
        if($result) {
            return ["{$name}_name" => $sanitizedPost["{$name}_name"]];
        } else {
            return [];
        }
    }

    public function delete_category_brand(string $table, array $posts, string $name) {
        $sanitizedPost = $this->sanitizing($posts);
        $categoryBrandBuilder = $this->db->table($table);
        $categoryBrandBuilder->where("id", $sanitizedPost["{$name}_id"]);
        $categoryBrandBuilder->delete();
        return $this->db->affectedRows();
    }

    public function get_total_product_rows() {
        $productBuilder = $this->db->table('products');
        $productBuilder->select('COUNT(*) as product_rows');
        $query = $productBuilder->get();
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

    public function insert_user(array $posts):bool
    {
        // Prepare the Query
        unset($posts['register']);
        unset($posts['confirm_password']);
        $sanitizedPost = $this->sanitizing($posts);
        $salt = bin2hex(openssl_random_pseudo_bytes(22)); // hashing for password
        $encrypted_password = md5($sanitizedPost['password']. '' .$salt);
        $pQuery = $this->db->prepare(function ($db) {
            return $db->table('tbl_users')
              ->insert([
                   'email' => '1',
                   'encrypted_password' => '2',
                   'first_name' => '3',
                   'last_name' => '4',
                   'salt' => '5',
              ]);
        });
        // Run the Query
        $result = $pQuery->execute($sanitizedPost['email'], $encrypted_password, $sanitizedPost['first_name'], $sanitizedPost['last_name'], $salt);
        return empty($result->error) ? true : false;
    }

}
