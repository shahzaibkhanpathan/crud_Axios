<?php
include 'db.php';

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function signin($username, $password) {
        $stmt = $this->db->conn->prepare("SELECT password FROM students WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($password, $hashedPassword)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $student = new Student();
    $result = $student->signin($username, $password);
    echo json_encode($result);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="scripts/main.js" defer></script>
</head>
<body>
    <h1>Sign In</h1>
    <form id="signinForm">
        <input type="text" id="signinUsername" name="username" placeholder="Username" required>
        <input type="password" id="signinPassword" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>
</body>
</html>
