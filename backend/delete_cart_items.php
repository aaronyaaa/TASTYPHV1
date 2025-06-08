<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$cartIds = $input['cart_ids'] ?? [];

if (!is_array($cartIds) || empty($cartIds)) {
    echo json_encode(['success' => false, 'message' => 'No cart items provided']);
    exit;
}

// Prepare statement for security
$inClause = implode(',', array_fill(0, count($cartIds), '?'));
$params = array_merge([$userId], $cartIds);

try {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND cart_id IN ($inClause)");
    $stmt->execute($params);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
