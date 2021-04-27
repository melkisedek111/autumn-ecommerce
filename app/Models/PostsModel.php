<?php

namespace App\Models;

use App\Helpers\ModelInterface;
use CodeIgniter\Model;

class PostsModel extends Model implements ModelInterface
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function sanitizing(array $array): array
    {
        return array_map(function ($post) {
            return filter_var($post, FILTER_SANITIZE_STRING);
        }, $array);
    }

    public function getNotes(): array {
        $selectMessageQuery = "SELECT * FROM notes ORDER BY created_at ASC";
        $query = $this->db->query($selectMessageQuery);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }
    public function getPosts(): array {
        $selectMessageQuery = "SELECT * FROM posts ORDER BY created_at ASC";
        $query = $this->db->query($selectMessageQuery);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }

    public function updateNoteTitle(array $posts): bool {
        $sanitizedPost = $this->sanitizing($posts);
        $getNoteQuery = "UPDATE notes SET title = ? WHERE note_id = ?";
        $this->db->query($getNoteQuery, [...array_values($posts)]);
        $response = $this->db->affectedRows();
        $this->db->close();
        if($response) {
            return true;
        } else {
            return false;
        }
    }

    public function updateNote(array $posts): bool {
        $sanitizedPost = $this->sanitizing($posts);
        $getNoteQuery = "UPDATE notes SET title = ?, note = ? WHERE note_id = ?";
        $this->db->query($getNoteQuery, [...array_values($posts)]);
        $response = $this->db->affectedRows();
        $this->db->close();
        if($response) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteNote(array $posts): bool {
        $sanitizedPost = $this->sanitizing($posts);
        $getNoteQuery = "DELETE FROM notes WHERE note_id = ?";
        $this->db->query($getNoteQuery, [...array_values($sanitizedPost)]);
        $response = $this->db->affectedRows();
        $this->db->close();
        if($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getNoteById(int $id): array {
        $posts = ['id' => $id];
        $sanitizedPost = $this->sanitizing($posts);
        $getPostQuery = "SELECT note_id, title, note FROM notes WHERE note_id = ? LIMIT 1";
        $query = $this->db->query($getPostQuery, [...array_values($sanitizedPost)]);
        $response = $this->db->affectedRows();
        $post = $query->getResult();
        $this->db->close();
        if($response) {
            return $post;
        } else {
            return [];
        }
    }

    public function createNewNote(array $post = []): array
    {
        $sanitizedPost = $this->sanitizing($post);
        $insertPostQuery = "INSERT INTO notes(title, note) VALUES('','')";
        $this->db->query($insertPostQuery);// spreading all the values from the sanitizedPost
        $response = $this->db->affectedRows();
        $getLastInsertId = $this->db->insertID();
        $this->db->close();
        if($response) {
            return $this->getNoteById($getLastInsertId);
        } else {
            return [];
        }
    }


}
