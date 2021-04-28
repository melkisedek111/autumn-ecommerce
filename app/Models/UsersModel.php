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
        $builder = $this->db->table('tbl_users');
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



    public function selectCommentsByMessage(int $id)
    {
        $selectMessageQuery = "SELECT comment_id, comment, from_user_id, first_name, last_name, tbl_comments.created_at FROM tbl_comments LEFT JOIN tbl_users ON tbl_comments.from_user_id = tbl_users.user_id WHERE message_id = ? ORDER BY tbl_comments.created_at DESC";
        $query = $this->db->query($selectMessageQuery, [filter_var($id, FILTER_SANITIZE_NUMBER_INT)]);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }

    public function createNewComment(array $post, $fromUserId)
    {
        unset($post['to_user_id']);
        $post['from_user_id'] = $fromUserId;
        $sanitizedPost = $this->sanitizing($post);
        $insertMessageQuery = "INSERT INTO tbl_comments(comment,message_id,from_user_id) VALUES(?,?,?);";
        $this->db->query($insertMessageQuery, [...array_values($sanitizedPost)]);// spreading all the values from the sanitizedPost
        $result = $this->db->affectedRows();
        return $result ? true : false;
    }



    public function createNewPost(array $post, $fromUserId): bool
    {
        $post['from_user_id'] = $fromUserId;
        $sanitizedPost = $this->sanitizing($post);
        $insertMessageQuery = "INSERT INTO tbl_messages(message,to_user_id,from_user_id) VALUES(?,?,?);";
        $this->db->query($insertMessageQuery, [...array_values($sanitizedPost)]);// spreading all the values from the sanitizedPost
        $result = $this->db->affectedRows();
        return $result ? true : false;
    }

    public function deletePostMessage(array $post, $fromUserId): bool
    {
        $post['to_user_id'] = $post['other_id'];
        unset($post['other_id']);
        $post['from_user_id'] = $fromUserId;
        $sanitizedPost = $this->sanitizing($post);
        $deleteMessageQuery = "DELETE FROM tbl_messages WHERE message_id = ? AND to_user_id = ? AND from_user_id = ?;";
        $this->db->query($deleteMessageQuery, [...array_values($sanitizedPost)]);// spreading all the values from the sanitizedPost
        $result = $this->db->affectedRows();
        return $result ? true : false;
    }

    public function deleteMessageComment(array $post, $fromUserId): bool
    {
        $post['message_id'] = explode("|", $post['other_id'])[0];
        unset($post['other_id']);
        $post['from_user_id'] = $fromUserId;
        $sanitizedPost = $this->sanitizing($post);
        $deleteMessageQuery = "DELETE FROM tbl_comments WHERE comment_id = ? AND message_id = ? AND from_user_id = ?;";
        $this->db->query($deleteMessageQuery, [...array_values($sanitizedPost)]);// spreading all the values from the sanitizedPost
        $result = $this->db->affectedRows();
        return $result ? true : false;
    }
}
