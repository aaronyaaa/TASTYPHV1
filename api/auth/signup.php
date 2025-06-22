<?php
include_once('../../database/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    header('Content-Type: application/json');

    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$first_name, $last_name, $email, $password])) {
            echo json_encode(['success' => true, 'message' => 'Sign up successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register user.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
