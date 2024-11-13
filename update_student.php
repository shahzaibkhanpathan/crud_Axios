<?php
include 'db.php';

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateStudent($id, $username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->conn->prepare("UPDATE students SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $hashedPassword, $id);
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Update failed.'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $student = new Student();
    $result = $student->updateStudent($id, $username, $email, $password);
    header('Content-Type: application/json');
    echo json_encode($result);
}
?>
