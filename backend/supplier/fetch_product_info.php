<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

header('Content-Type: application/json');

// Check session user
$userId = $_SESSION['userId'] ?? null;
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if (!$userId || $productId <= 0) {
    echo json_encode([]);
    exit;
}

// Get seller_id from current user
$sellerStmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$sellerStmt->execute([$userId]);
$sellerId = $sellerStmt->fetchColumn();

if (!$sellerId) {
    echo json_encode([]);
    exit;
}

// Fetch product only if it belongs to the seller
$stmt = $pdo->prepare("
    SELECT product_name, slug, description, price, discount_price, stock, quantity_value, unit_type 
    FROM products 
    WHERE product_id = ? AND seller_id = ? 
    LIMIT 1
");
$stmt->execute([$productId, $sellerId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Output JSON
echo json_encode($product ?: []);
