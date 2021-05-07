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
        $sanitizedPost = $this->sanitizing($email);
        $builder = $this->db->table('users');
        $builder->select('email');
        $query = $builder->getWhere(['email' => $sanitizedPost['email']]);
        return $query->getResult();
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
        $result = $pQuery->execute($sanitizedPost['email'], $encrypted_password, $sanitizedPost['first_name'], $sanitizedPost['last_name'], $salt);
        return empty($result->error) ? true : false;
    }

    public function login_user(array $posts) {
        unset($posts['login']);
        $sanitizedPost = $this->sanitizing($posts);
        $builder = $this->db->table('users');
        $builder->select('users.id as user_id, first_name, last_name, email, encrypted_password, salt, image, user_type');
        $query = $builder->getWhere(['email' => $sanitizedPost['email']]);
        $user = $query->getRow();
        $login_encrypted_password = md5("{$sanitizedPost['password']}{$user->salt}");
        if($login_encrypted_password === $user->encrypted_password) {
            return $user;
        } else {
            return [];
        }
    }

    public function set_user_address(array $posts, string $user_id) {
        unset($posts['set_address']);
        $sanitizedPost = $this->sanitizing($posts);
        $sanitizedPost['isShipping'] = 1;
        $sanitizedPost['isBilling'] = 0;
        $sanitizedPost['user_id'] = $user_id;
        $address_builder = $this->db->table('addresses');
        $address_builder->insert($sanitizedPost);
        return $this->db->affectedRows();
    }

    public function check_user_address(array $posts) {
        $sanitizedPost = $this->sanitizing($posts);
        $address_builder = $this->db->table('addresses');
        $address_builder->select('COUNT(*) as address_count');
        $address_builder->where('user_id', $sanitizedPost['user_id']);
        $address_builder->where('isShipping', 1);
        $address_count = $address_builder->get();
        return $address_count->getRow();
    }

    public function get_user_address(array $posts) {
        $sanitizedPost = $this->sanitizing($posts);
        $address_builder = $this->db->table('addresses');
        $address_builder->select('*');
        $address_builder->where('user_id', $sanitizedPost['user_id']);
        $address_builder->where('isShipping', 1);
        $address_count = $address_builder->get();
        return $address_count->getRow();
    }


}
