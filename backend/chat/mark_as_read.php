<?php
require_once __DIR__ . '/../../database/db_connect.php';
session_start();

$currentUserId = $_SESSION['user']['id'] ?? null;
$senderId = $_POST['senderId'] ?? null;

if ($currentUserId && $senderId) {
    $stmt = $pdo->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ");
    $stmt->execute([$senderId, $currentUserId]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
