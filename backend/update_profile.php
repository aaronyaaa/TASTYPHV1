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

$firstName = trim($_POST['first_name'] ?? '');
$middleName = trim($_POST['middle_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$contactNumber = trim($_POST['contact_number'] ?? '');
$dateOfBirth = $_POST['date_of_birth'] ?? '';
$gender = $_POST['gender'] ?? '';

if (!$firstName || !$lastName || !$contactNumber || !$dateOfBirth || !$gender) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$profilePicPath = null;
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
}

try {
    // Build SQL and params
    $sql = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, contact_number = ?, date_of_birth = ?, gender = ?";
    $params = [$firstName, $middleName, $lastName, $contactNumber, $dateOfBirth, $gender];

    if ($profilePicPath !== null) {
        $sql .= ", profile_pics = ?";
        $params[] = $profilePicPath;
    }

    $sql .= " WHERE id = ?";
    $params[] = $userId;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => 'Profile updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
