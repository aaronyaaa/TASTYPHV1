<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$cartId = isset($data['cart_id']) ? (int) $data['cart_id'] : 0;
$newQuantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;

if ($cartId <= 0 || $newQuantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // Check if cart item exists and belongs to this user
    $stmt = $pdo->prepare("SELECT unit_price FROM cart WHERE cart_id = ? AND user_id = ? AND status = 'active'");
    $stmt->execute([$cartId, $userId]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        exit;
    }

    $unitPrice = $cartItem['unit_price'];
    $newTotal = $unitPrice * $newQuantity;

    // Perform the update
    $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ?, total_price = ? WHERE cart_id = ?");
    $updateStmt->execute([$newQuantity, $newTotal, $cartId]);

    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
