<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// 🔍 Get the seller_id from session user
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerId = $stmt->fetchColumn();

if (!$sellerId) {
    echo json_encode(['success' => false, 'error' => 'Seller not found.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $insert = $pdo->prepare("
        INSERT INTO product_inventory (product_id, seller_id, order_id, quantity, activity_type, notes)
        VALUES (?, ?, ?, ?, 'delivery', ?)
    ");

    $update = $pdo->prepare("
        UPDATE products
        SET stock = stock - ?
        WHERE product_id = ? AND seller_id = ?
    ");

    foreach ($items as $item) {
        $productId = (int) $item['product_id'];
        $quantity = (int) $item['quantity'];
        $notes = "Delivered to customer via order #{$orderId}";

        // Validate values
        if ($productId <= 0 || $quantity <= 0) {
            throw new Exception("Invalid product ID or quantity.");
        }

        // Insert delivery record
        $insert->execute([$productId, $sellerId, $orderId, $quantity, $notes]);

        // Update stock
        $update->execute([$quantity, $productId, $sellerId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
