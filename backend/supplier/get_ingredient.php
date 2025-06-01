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

// Validate ID param
$ingredient_id = $_GET['id'] ?? null;
if (!$ingredient_id || !is_numeric($ingredient_id)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid ingredient ID.']);
  exit;
}

// Confirm supplier ownership
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();

if (!$supplier_id) {
  http_response_code(403);
  echo json_encode(['error' => 'Access denied.']);
  exit;
}

// Fetch the ingredient owned by the supplier
$stmt = $pdo->prepare("
  SELECT ingredient_id, ingredient_name, description, price, stock, quantity_value,
         unit_type, image_url
  FROM ingredients
  WHERE ingredient_id = ? AND supplier_id = ?
");
$stmt->execute([$ingredient_id, $supplier_id]);
$ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ingredient) {
  http_response_code(404);
  echo json_encode(['error' => 'Ingredient not found.']);
  exit;
}

echo json_encode($ingredient);
