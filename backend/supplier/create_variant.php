<?php
require_once '../../database/db_connect.php';
session_start();
header('Content-Type: application/json');

// Validate session
$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

// Get supplier_id
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();

if (!$supplier_id) {
  echo json_encode(['error' => 'Supplier not found']);
  exit;
}

// Get and validate inputs
$ingredient_id   = $_POST['ingredient_id'] ?? null;
$variant_name    = trim($_POST['variant_name'] ?? '');
$price           = $_POST['price'];
$discount_price  = isset($_POST['discount_price']) ? floatval($_POST['discount_price']) : null;
$stock           = $_POST['stock'];
$quantity_value  = $_POST['quantity_value'];
$unit_type       = $_POST['unit_type'] ?? '';
$is_active       = isset($_POST['is_active']) ? 1 : 0;

// Validate required fields without blocking valid 0 values
if (
  empty($ingredient_id) ||
  $variant_name === '' ||
  !is_numeric($price) ||
  !is_numeric($quantity_value) ||
  !is_numeric($stock) ||
  trim($unit_type) === ''
) {
  echo json_encode(['error' => 'All required fields must be filled.']);
  exit;
}

// Upload image if provided
$image_url = null;
if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $mime = mime_content_type($_FILES['image']['tmp_name']);
  
  if (!isset($allowed[$mime])) {
    echo json_encode(['error' => 'Only JPG, PNG, or WEBP allowed.']);
    exit;
  }

  $ext = $allowed[$mime];
  $filename = 'variant_' . $supplier_id . '_' . time() . '.' . $ext;
  $uploadDir = '../../uploads/variants/';
  $relativePath = 'uploads/variants/' . $filename;

 if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
  echo json_encode(['error' => 'Image upload failed.']);
  exit;
}

$image_url = $relativePath;

}

// Insert into database
try {
  $stmt = $pdo->prepare("
    INSERT INTO ingredient_variants (
      ingredient_id, supplier_id, variant_name, price, discount_price,
      stock, quantity_value, unit_type, image_url, is_active
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $ingredient_id,
    $supplier_id,
    $variant_name,
    floatval($price),
    $discount_price,
    intval($stock),
    floatval($quantity_value),
    $unit_type,
    $image_url,
    $is_active
  ]);

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
