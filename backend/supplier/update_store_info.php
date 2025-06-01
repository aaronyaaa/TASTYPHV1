<?php
require_once '../../database/db_connect.php';
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$businessName   = $_POST['business_name'] ?? '';
$description    = $_POST['description'] ?? '';
$latitude       = is_numeric($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude      = is_numeric($_POST['longitude']) ? $_POST['longitude'] : null;
$storeAddress   = $_POST['store_address'] ?? '';
$fullAddress    = $_POST['full_address'] ?? '';

if (!$businessName || $latitude === null || $longitude === null) {
  echo json_encode(['error' => 'Missing required fields.']);
  exit;
}

try {
  $stmt = $pdo->prepare("
    UPDATE supplier_applications 
    SET business_name = ?, description = ?, latitude = ?, longitude = ?, store_address = ?, full_address = ?
    WHERE user_id = ?
  ");
  $stmt->execute([
    $businessName,
    $description,
    $latitude,
    $longitude,
    $storeAddress,
    $fullAddress,
    $userId
  ]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'DB error: ' . $e->getMessage()]);
}
