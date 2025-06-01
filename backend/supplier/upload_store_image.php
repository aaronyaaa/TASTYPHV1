<?php
require_once '../../database/db_connect.php';
session_start();

header('Content-Type: application/json');

// Begin output buffering to prevent any unwanted output
ob_start();

try {
    $userId = $_SESSION['userId'] ?? null;
    $image = $_FILES['image'] ?? null;
    $type = $_POST['type'] ?? 'cover';

    if (!$userId || !$image || $image['error'] !== 0) {
        throw new Exception('Unauthorized or invalid upload');
    }

    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $image['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedMime)) {
        throw new Exception('Invalid image type. JPG, PNG, or WEBP only.');
    }

    $ext = match ($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'jpg'
    };

    $prefix = ($type === 'profile') ? 'profile' : 'cover';
    $filename = $prefix . '_' . $userId . '_' . time() . '.' . $ext;

    $uploadDir = __DIR__ . '/../../uploads/supplier/';
    $relativePath = 'uploads/supplier/' . $filename;
    $fullPath = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($image['tmp_name'], $fullPath)) {
        throw new Exception('Failed to save image to server');
    }

    $column = ($type === 'profile') ? 'profile_pics' : 'cover_photo';
    $stmt = $pdo->prepare("UPDATE supplier_applications SET $column = ? WHERE user_id = ?");
    $stmt->execute([$relativePath, $userId]);

    // Clear buffer and return success
    ob_end_clean();
    echo json_encode(['success' => true, 'path' => $relativePath]);
    exit;

} catch (Exception $e) {
    error_log('Upload error: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
