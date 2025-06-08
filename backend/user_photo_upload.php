<?php
session_start();
include_once("../database/db_connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userId'];
$type = $_POST['type'] ?? ''; // 'profile' or 'cover'

if (!in_array($type, ['profile', 'cover'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid upload type']);
    exit;
}

$inputName = ($type === 'profile') ? 'profile_photo' : 'cover_photo';
$targetDir = ($type === 'profile') ? '../uploads/users/profile/' : '../uploads/users/cover/';

// Check file
if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    exit;
}

$fileTmp  = $_FILES[$inputName]['tmp_name'];
$ext      = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
$newName  = $type . '_' . $userId . '_' . time() . '.' . $ext;
$savePath = $targetDir . $newName;

if (!move_uploaded_file($fileTmp, $savePath)) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to save file']);
    exit;
}

// Save to DB
$dbPath = 'uploads/users/' . $type . '/' . $newName;
$column = ($type === 'profile') ? 'profile_pics' : 'cover_photo';

$stmt = $pdo->prepare("UPDATE users SET $column = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$dbPath, $userId]);

echo json_encode(['status' => 'success', 'path' => $dbPath]);
