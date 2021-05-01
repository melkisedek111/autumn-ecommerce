<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
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

    public function add_product(array $posts, array $images): int
    {
        unset($posts['setMainImageIndex']);
        $sanitizedPost = $this->sanitizing($posts);
        $insertProductDetailsQuery = $this->db->prepare(function ($db) {
            return $db->table('tbl_products')
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
        $insertProductImagesBuilder = $this->db->table('tbl_product_images');
        $resultInsertProductImages = $insertProductImagesBuilder->insertBatch($imagesWithMainSet);
        return $resultInsertProductImages;
    }

    
    public function add_category(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $categoryBuilder = $this->db->table('tbl_categories');
        $categoryBuilder->select('category_id, category_name');
        $checkCategoryNameQuery = $categoryBuilder->getWhere($sanitizedPost);
        if(count($checkCategoryNameQuery->getResult())) {
            return ['category_exists' => true];
        }
        $insertCategoryQuery = $this->db->prepare(function ($db) {
            return $db->table('tbl_categories')
              ->insert([
                   'category_name' => '1',
              ]);
        });
        $result = $insertCategoryQuery->execute($sanitizedPost['category_name']);
        $lastId = empty($result->error) ? $this->db->insertID() : 0;
        $getInsertedCategoryQuery = $categoryBuilder->getWhere(['category_id' => $lastId], 1);
        return empty($result->error) ? $getInsertedCategoryQuery->getResult() : [];
    }

    public function add_brand(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $brandBuilder = $this->db->table('tbl_brands');
        $brandBuilder->select('brand_id, brand_name');
        $query = $brandBuilder->getWhere($sanitizedPost);
        if(count($query->getResult())) {
            return ['brand_exists' => true];
        }
        $insertBrandQuery = $this->db->prepare(function ($db) {
            return $db->table('tbl_brands')
              ->insert([
                   'brand_name' => '1',
              ]);
        });
        $result = $insertBrandQuery->execute($sanitizedPost['brand_name']);
        $lastId = empty($result->error) ? $this->db->insertID() : 0;
        $getInsertedBrandQuery = $brandBuilder->getWhere(['brand_id' => $lastId], 1);
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
        $categoryBrandBuilder->where("{$name}_id", $sanitizedPost["{$name}_id"]);
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
        $categoryBrandBuilder->where("{$name}_id", $sanitizedPost["{$name}_id"]);
        $categoryBrandBuilder->delete();
        return $this->db->affectedRows();
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

    public function login_user(array $posts)
    {
        unset($posts['login']);
        $sanitizedPost = $this->sanitizing($posts);
        $builder = $this->db->table('tbl_users');
        $builder->select('user_id, first_name, last_name, email, encrypted_password, salt, image, user_type');
        $query = $builder->getWhere(['email' => $sanitizedPost['email']]);
        $user = $query->getRow();
        $login_encrypted_password = md5("{$sanitizedPost['password']}{$user->salt}");
        if ($login_encrypted_password === $user->encrypted_password) {
            return $user;
        } else {
            return [];
        }
    }
}
