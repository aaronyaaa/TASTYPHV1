<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$ingredientId = !empty($_POST['ingredient_id']) ? (int) $_POST['ingredient_id'] : null;
$variantId = isset($_POST['variant_id']) && $_POST['variant_id'] !== '' ? (int) $_POST['variant_id'] : null;
$unitPrice = isset($_POST['unit_price']) ? (float) $_POST['unit_price'] : null;
$quantity = isset($_POST['quantity']) ? max(1, (int) $_POST['quantity']) : 1;
$status = 'active';

if (!$ingredientId || !$unitPrice || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid data']);
    exit;
}

$totalPrice = $unitPrice * $quantity;

try {
    // Check if item already exists
    $checkStmt = $pdo->prepare("
        SELECT cart_id, quantity 
        FROM cart 
        WHERE user_id = ? AND ingredient_id = ? AND (variant_id <=> ?) AND status = 'active'
    ");
    $checkStmt->execute([$userId, $ingredientId, $variantId]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $updateStmt = $pdo->prepare("
            UPDATE cart 
            SET quantity = ?, total_price = ? 
            WHERE cart_id = ?
        ");
        $updateStmt->execute([$newQty, $newQty * $unitPrice, $existing['cart_id']]);
    } else {
        $insertStmt = $pdo->prepare("
            INSERT INTO cart (user_id, product_id, ingredient_id, variant_id, unit_price, quantity, total_price, status)
            VALUES (?, NULL, ?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->execute([
            $userId,
            $ingredientId,
            $variantId, // this can be null
            $unitPrice,
            $quantity,
            $totalPrice,
            $status
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
