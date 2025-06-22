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

$name        = trim($_POST['name'] ?? '');
$slug        = trim($_POST['slug'] ?? '');
$description = trim($_POST['description'] ?? '');
$is_active   = isset($_POST['is_active']) ? 1 : 0;

if (!$name) {
  echo json_encode(['error' => 'Category name is required.']);
  exit;
}

if (!$slug) {
  // Auto-generate slug if not provided
  $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
  $slug = trim($slug, '-');
}

// Get the supplier_id using userId
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();

if (!$supplier_id) {
  http_response_code(400);
  echo json_encode(['error' => 'Supplier not found.']);
  exit;
}

// Check slug uniqueness per supplier
$stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ? AND supplier_id = ?");
$stmt->execute([$slug, $supplier_id]);
if ($stmt->fetchColumn() > 0) {
  echo json_encode(['error' => 'This category slug already exists under your account.']);
  exit;
}

// Handle image upload
$image_url = null;
if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
  $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $mimeType = mime_content_type($_FILES['image']['tmp_name']);

  if (!isset($allowedTypes[$mimeType])) {
    echo json_encode(['error' => 'Invalid image type. Only JPG, PNG, or WEBP allowed.']);
    exit;
  }

  $ext = $allowedTypes[$mimeType];
  $filename = 'category_' . $userId . '_' . time() . '.' . $ext;
  $uploadDir = '../../uploads/categories/';
  $relativePath = 'uploads/categories/' . $filename;

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $fullPath = $uploadDir . $filename;

  if (!move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
    echo json_encode(['error' => 'Failed to save image.']);
    exit;
  }

  $image_url = $relativePath;
}

// Insert category
$stmt = $pdo->prepare("
  INSERT INTO categories (supplier_id, name, slug, description, image_url, is_active)
  VALUES (?, ?, ?, ?, ?, ?)
");

try {
  $stmt->execute([$supplier_id, $name, $slug, $description, $image_url, $is_active]);
  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;
