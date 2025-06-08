<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

header('Content-Type: application/json');

// Ensure logged in
if (!isset($_SESSION['user']['id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Authenticated sender
$senderId = $_SESSION['user']['id'];

// Extract & sanitize inputs
$receiverId   = isset($data['receiver_id']) ? (int) $data['receiver_id'] : null;
$messageText  = trim($data['message_text'] ?? '');
$imageUrl     = $data['image_url'] ?? null;
$replyTo      = $data['reply_to'] ?? null;

// Validate required data
if (!$receiverId || $senderId === $receiverId || (empty($messageText) && empty($imageUrl))) {
  echo json_encode(['success' => false, 'error' => 'Invalid message data.']);
  exit;
}

// Verify receiver exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
$stmt->execute([$receiverId]);
if ($stmt->fetchColumn() == 0) {
  echo json_encode(['success' => false, 'error' => 'Receiver not found.']);
  exit;
}

// Optional: validate reply_to message exists and belongs to sender or receiver
if (!empty($replyTo)) {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE message_id = ?");
  $stmt->execute([$replyTo]);
  if ($stmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'error' => 'Reply reference not found.']);
    exit;
  }
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO messages (
      sender_id, receiver_id, message_text, image_url, reply_to, pinned, is_read, sent_at
    )
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
  echo json_encode([
    'success' => false,
    'error' => 'Database error',
    'details' => $e->getMessage()
  ]);
}
