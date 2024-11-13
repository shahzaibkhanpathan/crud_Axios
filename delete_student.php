<?php
include 'db.php';

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function deleteStudent($id) {
        $stmt = $this->db->conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Deletion failed.'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';

    $student = new Student();
    $result = $student->deleteStudent($id);
    header('Content-Type: application/json');
    echo json_encode($result);
}
?>
