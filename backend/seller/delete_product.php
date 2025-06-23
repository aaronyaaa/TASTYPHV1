<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

header('Content-Type: application/json');

// Check if user is logged in
$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Validate product_id from POST
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit;
}

// Verify ownership (only allow deletion of seller's own product)
$sellerStmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$sellerStmt->execute([$userId]);
$sellerId = $sellerStmt->fetchColumn();

if (!$sellerId) {
    echo json_encode(['success' => false, 'message' => 'Seller not found.']);
    exit;
}

// Ensure the product belongs to this seller
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id = ? AND seller_id = ?");
$checkStmt->execute([$productId, $sellerId]);
if ($checkStmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found or unauthorized.']);
    exit;
}

// Delete the product
$deleteStmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
$deleted = $deleteStmt->execute([$productId]);

if ($deleted) {
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete product.']);
}
exit;