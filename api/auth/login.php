<?php
session_start();
include_once('../../database/db_connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid password."]);
    exit;
}

// Login successful - set session variables
$_SESSION['userId'] = $user['id'];
$_SESSION['firstName'] = $user['first_name'];
$_SESSION['middleName'] = $user['middle_name'] ?? null;
$_SESSION['lastName'] = $user['last_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['usertype'] = $user['usertype'];
$_SESSION['profilePics'] = $user['profile_pics'] ?? null;

// Send success response with redirect URL depending on usertype
$redirectUrl = '/user/home.php';
if ($user['usertype'] === 'supplier') {
    $redirectUrl = '/includes/supplier_dashboard.php';
} elseif ($user['usertype'] === 'seller') {
    $redirectUrl = '/users/homepage.php';
}

echo json_encode(["success" => true, "redirect" => $redirectUrl]);
exit;
