<?php

namespace App\Models;

use App\Helpers\ModelInterface;
use CodeIgniter\Model;

class TasksModel extends Model implements ModelInterface
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

    public function getTasks(): array {
        $getTasksQuery = "SELECT * FROM tasks";
        $query = $this->db->query($getTasksQuery);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }

    public function updateTask(array $posts): bool {
        $sanitizedPost = $this->sanitizing($posts);
        $updateTaskQuery = "UPDATE tasks SET name = ?, updated_at = CURRENT_TIMESTAMP() WHERE task_id = ?";
        $this->db->query($updateTaskQuery, [...array_values($sanitizedPost)]);
        $response = $this->db->affectedRows();
        $this->db->close();
        if($response) {
            return true;
        } else {
            return false;
        }
    }
    protected function checkCompletedTask(int $id):array {
        $getFilterLeadsQuery = "SELECT completed FROM tasks WHERE task_id = ? LIMIT 1";
        $query = $this->db->query($getFilterLeadsQuery, [$id]);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }
    public function completedTask(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $updateTaskQuery = "UPDATE tasks SET completed = IF(completed = 1, 0, 1), updated_at = CURRENT_TIMESTAMP() WHERE task_id = ?";
        $this->db->query($updateTaskQuery, [...array_values($sanitizedPost)]);
        $response = $this->db->affectedRows();
        $this->db->close();
        if($response) {
            return $this->checkCompletedTask($sanitizedPost['taskId'])[0];
        } else {
            return [];
        }
    }
    protected function selectTask(int $id):array {
        $getFilterLeadsQuery = "SELECT task_id, name, completed FROM tasks WHERE task_id = ? LIMIT 1";
        $query = $this->db->query($getFilterLeadsQuery, [$id]);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }
    public function addTask(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $addTaskQuery = "INSERT INTO tasks(name) VALUES(?)";
        $this->db->query($addTaskQuery, [...array_values($sanitizedPost)]);
        $response = $this->db->affectedRows();
        $getLastInsertId = $this->db->insertID();
        $this->db->close();
        if($response) {
            return $this->selectTask($getLastInsertId);
        } else {
            return [];
        }
    }



    
    public function getFilterLeads(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $getFilterLeadsQuery = "SELECT leads_id, first_name, last_name, registered_datetime, email FROM leads LIMIT ?, ?";
        $query = $this->db->query($getFilterLeadsQuery, [intval($sanitizedPost['pageNumber']), 10]);
        $row = $query->getResult();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }
    public function getLeadsByName(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $getFilterLeadsQuery = "SELECT leads_id, first_name, last_name, registered_datetime, email FROM leads WHERE CONCAT(leads.first_name,' ', leads.last_name) LIKE '%{$sanitizedPost['name']}%'";
        $query = $this->db->query($getFilterLeadsQuery);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    }
    public function getLeadsByDate(array $posts): array {
        $sanitizedPost = $this->sanitizing($posts);
        $getFilterLeadsQuery = "SELECT leads_id, first_name, last_name, registered_datetime, email FROM leads WHERE registered_datetime BETWEEN ? AND ?";
        $query = $this->db->query($getFilterLeadsQuery, [...array_values($sanitizedPost)]);
        $row = $query->getResultArray();
        $this->db->close();
        if ($row) {
            return $row;
        } else {
            return [];
        }
    } 
    public function getTotalRows(): array {
        $selectMessageQuery = "SELECT COUNT(*) as totalRows FROM leads";
        $query = $this->db->query($selectMessageQuery);
        $row = $query->getResult();
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
