<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

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
  $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
  $slug = trim($slug, '-');
}

// Get seller_id from session user
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$seller_id = $stmt->fetchColumn();

if (!$seller_id) {
  http_response_code(400);
  echo json_encode(['error' => 'Seller not found.']);
  exit;
}

// Enforce unique slug for seller
$stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ? AND seller_id = ?");
$stmt->execute([$slug, $seller_id]);
if ($stmt->fetchColumn() > 0) {
  echo json_encode(['error' => 'This category slug already exists under your account.']);
  exit;
}

// Image upload handler
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
  $uploadDir = __DIR__ . '/../../uploads/categories/';
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

// Insert into categories table
$stmt = $pdo->prepare("
  INSERT INTO categories (seller_id, name, slug, description, image_url, is_active)
  VALUES (?, ?, ?, ?, ?, ?)
");

try {
  $stmt->execute([$seller_id, $name, $slug, $description, $image_url, $is_active]);
  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;
