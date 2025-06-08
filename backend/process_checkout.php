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
$method = $data['payment_method'] ?? null;
$cartIds = $data['cart_ids'] ?? [];

if (!$method || !is_array($cartIds) || count($cartIds) === 0) {
    echo json_encode(['success' => false, 'message' => 'Missing required data or no items selected']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Fetch cart items
    $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
    $params = array_merge([$userId], $cartIds);

    $stmt = $pdo->prepare("
        SELECT 
            c.*, 
            i.supplier_id,
            NULL AS seller_id  -- Default seller_id to NULL
        FROM cart c
        LEFT JOIN ingredients i ON c.ingredient_id = i.ingredient_id
        WHERE c.user_id = ? AND c.cart_id IN ($inQuery) AND c.status = 'active'
    ");
    $stmt->execute($params);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        echo json_encode(['success' => false, 'message' => 'No valid cart items found']);
        exit;
    }

    $total = array_sum(array_column($cartItems, 'total_price'));

    // Payment validations
    if ($method === 'cash') {
        $cash = (float) ($data['cash_amount'] ?? 0);
        if ($cash < $total) {
            echo json_encode(['success' => false, 'message' => 'Cash amount is less than total order']);
            exit;
        }
    }

    // Get first item as source for supplier
    $firstItem = $cartItems[0];
    $insertOrder = $pdo->prepare("
        INSERT INTO orders (user_id, supplier_id, payment_method, total_price)
        VALUES (?, ?, ?, ?)
    ");
    $insertOrder->execute([
        $userId,
        $firstItem['supplier_id'],
        $method,
        $total
    ]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    $insertItem = $pdo->prepare("
        INSERT INTO order_items 
        (order_id, ingredient_id, variant_id, quantity, unit_price, supplier_id, seller_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($cartItems as $item) {
        $insertItem->execute([
            $orderId,
            $item['ingredient_id'],
            $item['variant_id'] ?: null,
            $item['quantity'],
            $item['unit_price'],
            $item['supplier_id'],
            null // seller_id is not part of your schema (yet), set to null
        ]);
    }

    // Delete processed items
    $deleteStmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ?");
    foreach ($cartItems as $item) {
        $deleteStmt->execute([$item['cart_id']]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
