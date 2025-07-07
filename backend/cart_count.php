<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;
$count = 0;

if ($userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$userId]);
    $count = (int) $stmt->fetchColumn();
}

echo json_encode(['count' => $count]);
