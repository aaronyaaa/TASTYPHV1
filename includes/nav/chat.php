<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$currentUserId = $_SESSION['user']['id'] ?? null;
$unreadCount = 0;

if ($currentUserId) {
  $stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT sender_id) as unread_count
    FROM messages
    WHERE receiver_id = ? AND is_read = 0
  ");
  $stmt->execute([$currentUserId]);
  $unreadCount = $stmt->fetchColumn();
}

$receiver = null;
$receiverId = $_GET['seller_id'] ?? $_GET['supplier_id'] ?? null;

if ($receiverId) {
  if (isset($_GET['seller_id'])) {
    $stmt = $pdo->prepare("
      SELECT u.id AS user_id, u.first_name, u.last_name
      FROM seller_applications sa
      JOIN users u ON sa.user_id = u.id
      WHERE sa.seller_id = ?
    ");
  } else {
    $stmt = $pdo->prepare("
      SELECT u.id AS user_id, u.first_name, u.last_name
      FROM supplier_applications sa
      JOIN users u ON sa.user_id = u.id
      WHERE sa.supplier_id = ?
    ");
  }
  $stmt->execute([$receiverId]);
  $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
}

$userType = $_SESSION['user']['usertype'] ?? null;
$targetType = ($userType === 'supplier') ? 'buyer' : 'supplier';

$stmt = $pdo->prepare("
  SELECT u.id, u.first_name, u.last_name,
         (
           SELECT COUNT(*) FROM messages m
           WHERE m.sender_id = u.id AND m.receiver_id = :currentId AND m.is_read = 0
         ) AS unread_count
  FROM users u
  WHERE u.id != :currentId
    AND u.usertype = :targetType
  ORDER BY u.first_name ASC
");
$stmt->execute([
  ':currentId' => $currentUserId,
  ':targetType' => $targetType
]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$receiverJson = !empty($receiver) ? json_encode($receiver) : '{}';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chat Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/chat.css">
</head>
<body>

<!-- Hidden context inputs -->
<input type="hidden" id="currentUserId" value="<?= htmlspecialchars($currentUserId) ?>">
<input type="hidden" id="receiverId" value="<?= $receiver['user_id'] ?? '' ?>">
<input type="hidden" id="receiverName" value="<?= htmlspecialchars(($receiver['first_name'] ?? '') . ' ' . ($receiver['last_name'] ?? '')) ?>">
<input type="hidden" id="globalUnreadCount" value="<?= (int)$unreadCount ?>">
<input type="hidden" id="debugReceiver" value="<?= htmlspecialchars($receiverJson) ?>">

<!-- Floating Chat Button -->
<div id="chatToggle" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1060;">
  <button type="button" class="btn btn-primary rounded-circle shadow-lg p-3 position-relative chat-toggle-btn" title="Open Chat">
    <i class="fas fa-comment-dots fa-lg"></i>
    <?php if ($unreadCount > 0): ?>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount ?></span>
    <?php endif; ?>
  </button>
</div>

<!-- Chat Panel -->
<div id="chatBox" class="card chat-slide-up position-fixed bottom-0 end-0 m-4 shadow-lg" style="width: 650px; height: 600px; display: none; z-index: 1061;">
  <!-- Header -->
  <div class="chat-header d-flex justify-content-between align-items-center px-3 py-2 bg-primary text-white">
    <strong>Messages</strong>
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm btn-light d-lg-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#chatSidebar" aria-expanded="true" aria-controls="chatSidebar">
        <i class="fas fa-users"></i>
      </button>
      <button id="closeChat" class="btn btn-sm btn-light" aria-label="Close chat">&times;</button>
    </div>
  </div>

  <!-- Body -->
  <div class="row flex-grow-1 h-100 w-100 g-0">
    <!-- Sidebar -->
    <div id="chatSidebar" class="col-12 col-md-4 col-lg-3 collapse show border-end bg-light p-2" style="overflow-y: auto;">
      <input type="text" id="userSearch" class="form-control form-control-sm mb-3" placeholder="Search users...">
      <ul id="userList" class="list-unstyled mb-0">
        <?php foreach ($users as $user): ?>
          <li class="user-item px-2 py-1 rounded mb-1 open-chat-btn"
              data-user-id="<?= $user['id'] ?>"
              data-user-name="<?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?>"
              style="cursor: pointer;">
            <?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?>
            <?php if ($user['unread_count'] > 0): ?>
              <span id="userBadge-<?= $user['id'] ?>" class="badge bg-danger ms-1">•</span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Chat Main -->
    <div id="chatMain" class="col bg-white d-flex flex-column">
      <div id="chatThread" class="chat-thread flex-grow-1 p-3 overflow-auto">
        <div class="text-muted small text-center mt-3">Select a user to start chatting</div>
      </div>
      <div class="chat-input border-top p-2 bg-light d-flex">
        <input type="text" id="chatMessageInput" class="form-control form-control-sm me-2" placeholder="Type a message...">
        <button id="chatSendBtn" class="btn btn-sm btn-primary">Send</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap & JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/chat.js"></script>
</body>
</html>
