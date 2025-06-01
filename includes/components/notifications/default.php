<?php
// includes/components/notifications/default.php

/**
 * Expected:
 * $notif (assoc array): [
 *   'message' => string,
 *   'icon' => string (e.g., 'fa-bell'),
 *   'created_at' => datetime string
 * ]
 * timeAgo() should be available in global scope or use a fallback
 */

$icon = htmlspecialchars($notif['icon'] ?? 'fa-bell');
$message = htmlspecialchars($notif['message'] ?? 'You have a new notification.');
$created = isset($notif['created_at']) ? date('F j, Y g:i A', strtotime($notif['created_at'])) : '';
?>

<li class="mb-2 px-1">
  <div class="d-flex align-items-start gap-2">
    <i class="fa-solid <?= $icon ?> text-warning mt-1"></i>
    <div class="small text-white">
      <div class="fw-semibold"><?= $message ?></div>
      <div class="text-white-50 small"><?= $created ?></div>
    </div>
  </div>
</li>
