<?php
require '../includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match'); window.location.href='../index.php';</script>";
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email, usertype) VALUES (:username, :password, :email, 'admin')");
        $stmt->execute([
            'username' => $username,
            'password' => $hashed,
            'email' => $email
        ]);
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../index.php';</script>";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "<script>alert('Username already exists'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='../index.php';</script>";
        }
    }
}
?>
