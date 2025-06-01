<?php
require_once '../../database/db_connect.php';
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
  if ($action === 'toggle_status') {
    $stmt = $pdo->prepare("SELECT store_status FROM seller_applications WHERE user_id = ?");
    $stmt->execute([$userId]);
    $current = $stmt->fetchColumn();
    $new = $current === 'active' ? 'inactive' : 'active';

    $update = $pdo->prepare("UPDATE seller_applications SET store_status = ? WHERE user_id = ?");
    $update->execute([$new, $userId]);
    echo json_encode(['success' => true]);
  }
  elseif ($action === 'toggle_visibility') {
    $stmt = $pdo->prepare("SELECT is_public FROM seller_applications WHERE user_id = ?");
    $stmt->execute([$userId]);
    $current = (int)$stmt->fetchColumn();
    $new = $current ? 0 : 1;

    $update = $pdo->prepare("UPDATE seller_applications SET is_public = ? WHERE user_id = ?");
    $update->execute([$new, $userId]);
    echo json_encode(['success' => true]);
  }
  else {
    throw new Exception('Invalid action');
  }
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['error' => $e->getMessage()]);
}
