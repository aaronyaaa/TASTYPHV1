<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1); error_reporting(E_ALL); // DEV only

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;
$items = $data['products'] ?? [];

$userId = $_SESSION['user']['id'] ?? null;

if (!$orderId || !$userId || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Missing required data.']);
    exit;
}

// 🔍 Lookup seller_id using user ID
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerId = $stmt->fetchColumn();

if (!$sellerId) {
    echo json_encode(['success' => false, 'error' => 'Seller not found.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO product_inventory (product_id, seller_id, order_id, quantity, activity_type, notes)
        VALUES (?, ?, ?, ?, 'delivery', ?)
    ");

    foreach ($items as $item) {
        $productId = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $notes = 'Delivered to customer via order #' . $orderId;

        $stmt->execute([$productId, $sellerId, $orderId, $quantity, $notes]);

        // Decrease stock
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND seller_id = ?");
        $updateStock->execute([$quantity, $productId, $sellerId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
