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

// Get supplier_id
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();
if (!$supplier_id) {
  echo json_encode(['error' => 'Supplier not found.']);
  exit;
}

// Collect inputs
$name            = trim($_POST['ingredient_name'] ?? '');
$slug            = trim($_POST['slug'] ?? '');
$description     = trim($_POST['description'] ?? '');
$price           = floatval($_POST['price'] ?? 0);
$quantity_value  = floatval($_POST['quantity_value'] ?? 0);
$unit_type       = $_POST['unit_type'] ?? '';
$category_id     = $_POST['category_id'] ?? null;
$stock           = intval($_POST['stock'] ?? 0);

// Validation
if (!$name || !$price || !$quantity_value || !$unit_type) {
  echo json_encode(['error' => 'Please fill in all required fields.']);
  exit;
}

if (!$slug) {
  $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
  $slug = trim($slug, '-');
}

// Handle image upload
$image_url = null;
if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $mime = mime_content_type($_FILES['image']['tmp_name']);

  if (!isset($allowed[$mime])) {
    echo json_encode(['error' => 'Only JPG, PNG, WEBP images allowed.']);
    exit;
  }

  $ext = $allowed[$mime];
  $filename = 'ingredient_' . $supplier_id . '_' . time() . '.' . $ext;
  $dir = '../../uploads/ingredients/';
  $relativePath = 'uploads/ingredients/' . $filename;

  if (!is_dir($dir)) mkdir($dir, 0755, true);
  if (!move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename)) {
    echo json_encode(['error' => 'Failed to upload image.']);
    exit;
  }

  $image_url = $relativePath;
}

// Insert ingredient
try {
  $stmt = $pdo->prepare("
    INSERT INTO ingredients (
      supplier_id, category_id, ingredient_name, slug, description,
      price, stock, quantity_value, unit_type, image_url
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([
    $supplier_id,
    $category_id ?: null,
    $name,
    $slug,
    $description,
    $price,
    $stock,
    $quantity_value,
    $unit_type,
    $image_url
  ]);

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'DB Error: ' . $e->getMessage()]);
}
