<?php
require_once '../../database/db_connect.php';
require_once '../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

$orderId = $_POST['order_id'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (!$orderId || !$newStatus) {
    header("Location: ../../seller/order.php?error=invalid");
    exit;
}

// Validate seller
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerId = $stmt->fetchColumn();

if (!$sellerId) {
    header("Location: ../../seller/order.php?error=no_seller");
    exit;
}

$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($newStatus, $validStatuses)) {
    header("Location: ../../seller/order.php?error=invalid_status");
    exit;
}

// Update order status for this seller
$update = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ? AND seller_id = ?");
$update->execute([$newStatus, $orderId, $sellerId]);

header("Location: ../../seller/order.php?success=1");
exit; 