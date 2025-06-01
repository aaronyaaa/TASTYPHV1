<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$senderId = $_SESSION['user']['id'];
$receiverId = $data['receiver_id'] ?? null;
$messageText = trim($data['message_text'] ?? '');
$imageUrl = $data['image_url'] ?? null;
$replyTo = $data['reply_to'] ?? null;

if (!$receiverId || $senderId == $receiverId || (!$messageText && !$imageUrl)) {
  echo json_encode(['success' => false, 'error' => 'Invalid message data.']);
  exit;
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO messages 
    (sender_id, receiver_id, message_text, image_url, reply_to, pinned, is_read, sent_at)
    VALUES (?, ?, ?, ?, ?, 0, 0, NOW())
  ");
  $stmt->execute([
    $senderId,
    $receiverId,
    $messageText ?: null,
    $imageUrl ?: null,
    $replyTo ?: null
  ]);

  echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'error' => 'Database error', 'details' => $e->getMessage()]);
}
