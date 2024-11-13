<?php
include 'db.php';

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getStudents() {
        $result = $this->db->conn->query("SELECT id, username, email FROM students");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

$student = new Student();
header('Content-Type: application/json');
echo json_encode($student->getStudents());
?>
