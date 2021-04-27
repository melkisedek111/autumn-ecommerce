<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    protected function sanitizing(array $array): array
    {
        return array_map(function ($post) {
            return filter_var($post, FILTER_SANITIZE_STRING);
        }, $array);
    }

    public function selectMessageByUser(int $id): array
    {
        $selectMessageQuery = "SELECT message_id, to_user_id, from_user_id, message, first_name, last_name, tbl_messages.created_at FROM tbl_messages LEFT JOIN tbl_users ON tbl_messages.from_user_id = tbl_users.user_id WHERE to_user_id = ? ORDER BY tbl_messages.created_at DESC";
        $query = $this->db->query($selectMessageQuery, [filter_var($id, FILTER_SANITIZE_NUMBER_INT)]);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
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
