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

$receiverId = $_GET['seller_id'] ?? null;
$receiver = null;

if ($receiverId) {
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.last_name
        FROM seller_applications sa
        JOIN users u ON sa.user_id = u.id
        WHERE sa.seller_id = ?
    ");
    $stmt->execute([$receiverId]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->prepare("
  SELECT u.id, u.first_name, u.last_name,
         COUNT(m.message_id) AS unread_count
  FROM users u
  LEFT JOIN messages m 
    ON m.sender_id = u.id 
    AND m.receiver_id = ? 
    AND m.is_read = 0
  WHERE u.id != ?
  GROUP BY u.id
");
$stmt->execute([$currentUserId, $currentUserId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/chat.css">

<input type="hidden" id="currentUserId" value="<?= htmlspecialchars($currentUserId) ?>">
<input type="hidden" id="receiverId" value="<?= $receiver['id'] ?? '' ?>">
<input type="hidden" id="receiverName" value="<?= htmlspecialchars(($receiver['first_name'] ?? '') . ' ' . ($receiver['last_name'] ?? '')) ?>">
<input type="hidden" id="globalUnreadCount" value="<?= (int)$unreadCount ?>">

<!-- Chat Toggle Button -->
<div id="chatToggle" class="position-fixed bottom-0 end-0 m-3">
  <button class="btn shadow border rounded-pill px-3 py-2 d-flex align-items-center chat-toggle-btn position-relative">
    <i class="fas fa-comment-dots me-2"></i>
    <strong>Chat</strong>
<?php if ($unreadCount > 0): ?>
  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    <?= $unreadCount ?>
  </span>
<?php endif; ?>

  </button>
</div>

<!-- Chat Box -->
<div id="chatBox" class="card chat-slide-up position-fixed bottom-0 end-0 m-3" style="display: none;">
  <div class="chat-header">
    <span><strong>Chat</strong></span>
    <button id="closeChat" class="btn btn-sm btn-light">&times;</button>
  </div>

  <div class="card-body p-0 d-flex chat-body">
    <!-- Sidebar User List -->
<!-- Sidebar User List -->
<div class="chat-user-list">
  <input type="text" class="form-control form-control-sm mb-2" placeholder="Search name" id="userSearch">
  <ul id="userList" class="user-list list-unstyled mb-0">
    <?php foreach ($users as $user): ?>
      <li class="user-item" data-userid="<?= $user['id'] ?>">
        <?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?>
        <?php if ($user['unread_count'] > 0): ?>
          <span id="userBadge-<?= $user['id'] ?>" class="badge bg-danger ms-1">•</span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>


    <!-- Thread -->
    <div class="flex-fill d-flex flex-column">
      <div class="chat-thread flex-grow-1 p-2 bg-light" id="chatThread">
        <div class="text-muted small text-center mt-3">Select a user to start chatting</div>
      </div>
      <div class="chat-input">
        <input type="text" id="chatMessageInput" placeholder="Type a message" class="form-control form-control-sm me-2" disabled>
        <button id="chatSendBtn" class="btn btn-sm btn-primary" disabled>Send</button>
      </div>
    </div>
  </div>
</div>



<script src="../assets/js/chat.js"></script>
