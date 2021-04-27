<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function selectAllUsers(): array
    {
        $selectAllUsersQuery = "SELECT user_id, first_name, last_name, email, DATE_FORMAT(created_at, '%M %D %Y %H:%i %p') as created_at, user_level FROM tbl_users;";
        $result = $this->db->query($selectAllUsersQuery);
        $users = $result->getResult();
        $this->db->close();
        return $users;
    }

    public function checkIfUserIsAdmin(int $id): bool {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $selectUserAdminQuery = "SELECT user_level FROM tbl_users WHERE user_id = ? LIMIT 1;";
        $result = $this->db->query($selectUserAdminQuery, [$id]);
        $user = $result->getRow();
        if($user->user_level == 9) {
            return true;
        } else {
            return false;
        }
    }


}
