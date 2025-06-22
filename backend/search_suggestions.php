<?php
require_once '../database/db_connect.php';

$q = $_GET['q'] ?? '';
if (!$q || strlen($q) < 2) {
  echo json_encode([]);
  exit;
}

$q = "%{$q}%";
$results = [];

// Union of top matches
$stmt = $pdo->prepare("
  (SELECT 'Ingredient' AS type, ingredient_name AS name FROM ingredients WHERE ingredient_name LIKE ? LIMIT 2)
  UNION
  (SELECT 'Product' AS type, CONCAT('Product ID: ', product_id) FROM products WHERE stock > 0 LIMIT 2)
  UNION
  (SELECT 'Store' AS type, business_name AS name FROM seller_applications WHERE business_name LIKE ? LIMIT 2)
");
$stmt->execute([$q, $q, $q]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
