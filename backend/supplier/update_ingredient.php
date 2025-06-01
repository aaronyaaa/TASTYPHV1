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

$ingredient_id    = $_POST['ingredient_id'] ?? null;
$name             = trim($_POST['ingredient_name'] ?? '');
$description      = trim($_POST['description'] ?? '');
$price            = floatval($_POST['price'] ?? 0);
$stock            = intval($_POST['stock'] ?? 0);
$quantity_value   = floatval($_POST['quantity_value'] ?? 0);
$unit_type        = $_POST['unit_type'] ?? '';

if (!$ingredient_id || !$name || !$price || !$quantity_value || !$unit_type) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing required fields.']);
  exit;
}

// Validate supplier ownership
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();

if (!$supplier_id) {
  http_response_code(403);
  echo json_encode(['error' => 'Access denied.']);
  exit;
}

// Check if ingredient exists and belongs to supplier
$stmt = $pdo->prepare("SELECT image_url FROM ingredients WHERE ingredient_id = ? AND supplier_id = ?");
$stmt->execute([$ingredient_id, $supplier_id]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
  http_response_code(404);
  echo json_encode(['error' => 'Ingredient not found.']);
  exit;
}

$image_url = $current['image_url'];

// Handle optional image update
if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $mime = mime_content_type($_FILES['image']['tmp_name']);

  if (!isset($allowed[$mime])) {
    echo json_encode(['error' => 'Only JPG, PNG, WEBP allowed.']);
    exit;
  }

  $ext = $allowed[$mime];
  $filename = 'ingredient_' . $supplier_id . '_' . time() . '.' . $ext;
  $uploadDir = '../../uploads/ingredients/';
  $relativePath = 'uploads/ingredients/' . $filename;

  if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
  if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
    echo json_encode(['error' => 'Image upload failed.']);
    exit;
  }

  $image_url = $relativePath;
}

// Update record
$stmt = $pdo->prepare("
  UPDATE ingredients
  SET ingredient_name = ?, description = ?, price = ?, stock = ?, 
      quantity_value = ?, unit_type = ?, image_url = ?, updated_at = NOW()
  WHERE ingredient_id = ? AND supplier_id = ?
");

try {
  $stmt->execute([
    $name,
    $description,
    $price,
    $stock,
    $quantity_value,
    $unit_type,
    $image_url,
    $ingredient_id,
    $supplier_id
  ]);

  // Instead of returning JSON:
  // echo json_encode(['success' => true]);

  // Just silently exit
header('Location: ../../supplier/Store.php');
exit;
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
}

