<?php

namespace App\Models;
use CodeIgniter\Model;
class StudentModel extends Model {
    
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
	}

    public function select_student_by_id($id) {
		$sql = "SELECT  student_id, first_name, last_name, email FROM students WHERE student_id = ?";
		$query = $this->db->query($sql, [$id]);
		$row = $query->getRowArray();
		return $row;
	}

    public function select_student_by_one_column($column, $value) {
		$sql = "SELECT * FROM students WHERE $column = ? LIMIT 1";
		$query = $this->db->query($sql, [$value]);
		$row = $query->getRowArray();
		return $row;
	}

	public function add_student($data, $getLastId = false){
		$sql = "INSERT INTO students(first_name, last_name, email, password, salt) VALUES(?,?,?,?,?);";
		$this->db->query($sql, [$data['first_name'], $data['last_name'], $data['email'], $data['password'], $data['salt']]);
		$res = $this->db->affectedRows();
        $lastId = $this->db->insertID();
		if($res) {
            if($getLastId) return $lastId;
			return true;
		} else {
			return false;
		}
	}
}