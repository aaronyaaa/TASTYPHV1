<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) exit;

// Get seller id for security
$userId = $_SESSION['userId'] ?? null;
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerId = $stmt->fetchColumn();

// Fetch order and user info (including lat/lng)
$stmt = $pdo->prepare("
  SELECT o.*, u.id AS user_id, u.first_name, u.last_name, u.email, u.contact_number, u.full_address, u.profile_pics, u.latitude, u.longitude
  FROM orders o
  JOIN users u ON o.user_id = u.id
  WHERE o.order_id = ? AND o.seller_id = ?
");
$stmt->execute([$orderId, $sellerId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) exit('Order not found.');

// Fetch order items (products)
$stmt = $pdo->prepare("
  SELECT oi.*, p.product_name, p.image_url
  FROM order_items oi
  LEFT JOIN products p ON oi.product_id = p.product_id
  WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare map coordinates
$lat = $order['latitude'] ?? null;
$lng = $order['longitude'] ?? null;
$hasCoords = $lat && $lng;

?>
<div class="order-details-sidebar-content p-0" style="height:100%;display:flex;flex-direction:column;">
  <div class="card shadow-sm border-0 rounded-4 mb-3" style="overflow:hidden;">
    <div class="card-body pb-0">
  <div class="d-flex align-items-center mb-3">
        <img src="../<?= $order['profile_pics'] ?: 'assets/images/default-profile.png' ?>" class="rounded-circle me-3 border border-2" style="width: 64px; height: 64px; object-fit: cover;">
    <div>
          <div class="fw-bold fs-4 mb-1 text-dark"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></div>
      <div class="text-muted small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($order['email']) ?></div>
    </div>
  </div>
      <div class="mb-3">
        <span class="badge px-3 py-2 fs-6 rounded-pill bg-<?= match(strtolower($order['status'])) {
          'pending' => 'warning text-dark',
      'processing' => 'primary',
      'shipped' => 'info text-dark',
      'delivered' => 'success',
      'cancelled' => 'danger',
      default => 'secondary'
        } ?> shadow-sm">
      <?= ucfirst($order['status']) ?>
    </span>
  </div>
      <ul class="list-unstyled mb-4">
        <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><span class="fw-semibold text-dark">Phone:</span> <span class="text-muted ms-1"><?= htmlspecialchars($order['contact_number']) ?></span></li>
        <li class="mb-2"><i class="bi bi-geo-alt me-2 text-danger"></i><span class="fw-semibold text-dark">Address:</span> <span class="text-muted ms-1"><?= htmlspecialchars($order['full_address']) ?></span></li>
        <li><i class="bi bi-calendar me-2 text-secondary"></i><span class="fw-semibold text-dark">Date:</span> <span class="text-muted ms-1"><?= htmlspecialchars($order['order_date']) ?></span></li>
      </ul>
      <div class="mb-4">
        <div class="fw-bold mb-2 fs-5 text-dark">Products</div>
        <div class="vstack gap-2">
    <?php foreach ($items as $item): ?>
            <div class="d-flex align-items-center bg-light rounded-3 px-2 py-2 shadow-sm">
              <img src="../<?= $item['image_url'] ?: 'assets/images/default-product.png' ?>" class="rounded-3 border me-3" style="width: 48px; height: 48px; object-fit: cover;">
        <div class="flex-grow-1">
                <div class="fw-semibold text-dark small"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></div>
          <div class="text-muted small">Qty: <?= htmlspecialchars($item['quantity']) ?> × ₱<?= number_format($item['unit_price'], 2) ?></div>
        </div>
              <div class="fw-bold text-primary ms-2">₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
      </div>
      <ul class="list-unstyled mb-4">
        <li class="mb-2"><i class="bi bi-cash me-2 text-success"></i><span class="fw-semibold text-dark">Payment:</span> <span class="text-muted ms-1"><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></span></li>
        <li><i class="bi bi-receipt me-2 text-info"></i><span class="fw-semibold text-dark">Total:</span> <span class="fw-bold text-dark ms-1">₱<?= number_format($order['total_price'], 2) ?></span></li>
      </ul>
      <div class="mb-4">
        <div class="fw-bold mb-2 fs-5 text-dark">Notes</div>
        <div class="bg-light rounded-3 p-3 small border shadow-sm" style="min-height: 40px;">
      <?= htmlspecialchars($order['additional_notes'] ?? 'No notes.') ?>
    </div>
  </div>
      <div class="mb-4">
        <div class="fw-bold mb-2 fs-5 text-dark">Delivery Location</div>
    <?php if ($hasCoords): ?>
          <div id="orderMap" style="height: 220px; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 2px 8px rgba(0,0,0,0.06);" data-lat="<?= $lat ?>" data-lng="<?= $lng ?>"></div>
    <?php else: ?>
      <div class="text-muted">No map location available.</div>
    <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="d-grid mt-4">
    <a href="../chat.php?user=<?= $order['user_id'] ?>" class="btn btn-outline-primary"><i class="bi bi-chat-dots me-1"></i> Chat Buyer</a>
  </div>
</div>


