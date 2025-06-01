<?php
require_once '../../database/db_connect.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$orderId = $data['pre_order_id'] ?? null;
$status = $data['status'] ?? null;
$reason = trim($data['reason'] ?? '');

if (!$orderId || !$status) {
  echo json_encode(['error' => 'Invalid request']);
  exit;
}

try {
  if ($status === 'declined') {
    $stmt = $pdo->prepare("UPDATE pre_order_list SET status = ?, decline_reason = ?, updated_at = NOW() WHERE pre_order_id = ?");
    $stmt->execute(['declined', $reason, $orderId]);
  } else {
    $stmt = $pdo->prepare("UPDATE pre_order_list SET status = ?, updated_at = NOW() WHERE pre_order_id = ?");
    $stmt->execute([$status, $orderId]);
  }

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
