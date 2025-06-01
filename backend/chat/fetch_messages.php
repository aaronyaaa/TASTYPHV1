<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

header('Content-Type: application/json');

// ✅ Check user is logged in
$currentUserId = $_SESSION['user']['id'] ?? null;
$receiverId = $_GET['receiver_id'] ?? null;

if (!$currentUserId || !$receiverId) {
  echo json_encode(['error' => 'Missing sender or receiver ID.']);
  exit;
}

try {
  $stmt = $pdo->prepare("
    SELECT 
      message_id,
      sender_id,
      receiver_id,
      message_text,
      image_url,
      reply_to,
      pinned,
      is_read,
      sent_at
    FROM messages
    WHERE (
        (sender_id = :sender AND receiver_id = :receiver)
        OR 
        (sender_id = :receiver AND receiver_id = :sender)
      )
      AND deleted_at IS NULL
    ORDER BY sent_at ASC
  ");

  $stmt->execute([
    ':sender' => $currentUserId,
    ':receiver' => $receiverId
  ]);

  $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($messages);

} catch (Exception $e) {
  echo json_encode(['error' => 'Unable to fetch messages', 'details' => $e->getMessage()]);
}