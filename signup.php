<?php
include 'db.php';

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function signup($username, $email, $password) {
        // Check if username or email already exists
        $stmt = $this->db->conn->prepare("SELECT id FROM students WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Username or email already exists.'];
        }
        $stmt->close();

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->conn->prepare("INSERT INTO students (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        $stmt->execute();
        $stmt->close();
        return ['success' => true];
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $student = new Student();
    $result = $student->signup($username, $email, $password);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
