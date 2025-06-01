<?php
include_once("../database/session.php");
include_once("../database/db_connect.php"); // Provides $pdo PDO connection

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated.']);
    exit();
}

$userId = $_SESSION['userId'];

// Collect all possible fields from POST (trim to clean)
$fieldsToUpdate = [];
$params = [];

// Check each field, add to update if present and not empty
if (isset($_POST['first_name']) && $_POST['first_name'] !== '') {
    $fieldsToUpdate[] = "first_name = ?";
    $params[] = trim($_POST['first_name']);
}

if (isset($_POST['middle_name'])) {
    $fieldsToUpdate[] = "middle_name = ?";
    $params[] = trim($_POST['middle_name']);
}

if (isset($_POST['last_name']) && $_POST['last_name'] !== '') {
    $fieldsToUpdate[] = "last_name = ?";
    $params[] = trim($_POST['last_name']);
}

if (isset($_POST['contact_number']) && $_POST['contact_number'] !== '') {
    $fieldsToUpdate[] = "contact_number = ?";
    $params[] = trim($_POST['contact_number']);
}

if (isset($_POST['date_of_birth']) && $_POST['date_of_birth'] !== '') {
    $fieldsToUpdate[] = "date_of_birth = ?";
    $params[] = $_POST['date_of_birth'];
}

if (isset($_POST['gender']) && $_POST['gender'] !== '') {
    $fieldsToUpdate[] = "gender = ?";
    $params[] = $_POST['gender'];
}

// Handle profile image upload if any
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileTmp = $_FILES['profile_image']['tmp_name'];
    $fileType = mime_content_type($fileTmp);

    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image type']);
        exit();
    }

    $uploadDir = '../uploads/profile_pics/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileExt = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExt;
    $destination = $uploadDir . $fileName;

    if (!move_uploaded_file($fileTmp, $destination)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
        exit();
    }

    $profilePicPath = 'uploads/profile_pics/' . $fileName;
    $fieldsToUpdate[] = "profile_pics = ?";
    $params[] = $profilePicPath;
}

// If no fields to update, return error
if (empty($fieldsToUpdate)) {
    http_response_code(400);
    echo json_encode(['error' => 'No data to update']);
    exit();
}

// Build the SQL dynamically
$sql = "UPDATE users SET " . implode(", ", $fieldsToUpdate) . " WHERE id = ?";
$params[] = $userId;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => 'Profile updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
