<?php
header('Content-Type: application/json');

require_once '../database/db_connect.php'; // ✅ FIXED: Make sure this path is correct

if (!isset($_GET['seller_id'])) {
    echo json_encode(["error" => "Missing seller_id"]);
    exit;
}

$sellerId = intval($_GET['seller_id']);

try {
    $query = $pdo->prepare("SELECT product_id, product_name, stock FROM products WHERE seller_id = ? AND is_active = 1");
    $query->execute([$sellerId]);
    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
