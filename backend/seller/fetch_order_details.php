<?php
require_once '../../database/db_connect.php';
require_once '../../database/session.php';

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
<div class="order-details-sidebar-content">
  <div class="d-flex align-items-center mb-3">
    <img src="../<?= $order['profile_pics'] ?: 'assets/images/default-profile.png' ?>" class="rounded-circle me-3" style="width: 56px; height: 56px; object-fit: cover;">
    <div>
      <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></div>
      <div class="text-muted small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($order['email']) ?></div>
    </div>
  </div>
  <div class="mb-2">
    <span class="badge bg-<?= match(strtolower($order['status'])) {
      'pending' => 'warning',
      'processing' => 'primary',
      'shipped' => 'info text-dark',
      'delivered' => 'success',
      'cancelled' => 'danger',
      default => 'secondary'
    } ?>">
      <?= ucfirst($order['status']) ?>
    </span>
  </div>
  <div class="mb-2"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($order['contact_number']) ?></div>
  <div class="mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($order['full_address']) ?></div>
  <div class="mb-2"><i class="bi bi-cash me-1"></i><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></div>
  <div class="mb-2"><i class="bi bi-receipt me-1"></i>Total: <span class="fw-bold">₱<?= number_format($order['total_price'], 2) ?></span></div>
  <div class="mb-2"><i class="bi bi-calendar me-1"></i><?= htmlspecialchars($order['order_date']) ?></div>
  <hr>
  <div class="mb-3">
    <div class="fw-bold mb-2">Products</div>
    <?php foreach ($items as $item): ?>
      <div class="d-flex align-items-center mb-2">
        <img src="../<?= $item['image_url'] ?: 'assets/images/default-product.png' ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
        <div class="flex-grow-1">
          <div><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></div>
          <div class="text-muted small">Qty: <?= htmlspecialchars($item['quantity']) ?> × ₱<?= number_format($item['unit_price'], 2) ?></div>
        </div>
        <div class="fw-bold ms-2">₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <hr>
  <div class="mb-3">
    <div class="fw-bold mb-2">Notes</div>
    <div class="bg-light rounded p-2 small" style="min-height: 40px;">
      <?= htmlspecialchars($order['additional_notes'] ?? 'No notes.') ?>
    </div>
  </div>
  <hr>
  <div class="mb-3">
    <div class="fw-bold mb-2">Delivery Location</div>
    <?php if ($hasCoords): ?>
      <div id="orderMap" style="height: 220px; border-radius: 8px; border: 1px solid #eee;" data-lat="<?= $lat ?>" data-lng="<?= $lng ?>"></div>
    <?php else: ?>
      <div class="text-muted">No map location available.</div>
    <?php endif; ?>
  </div>
  <div class="d-grid mt-4">
    <a href="../chat.php?user=<?= $order['user_id'] ?>" class="btn btn-outline-primary"><i class="bi bi-chat-dots me-1"></i> Chat Buyer</a>
  </div>
</div>
<?php if ($hasCoords): ?>
  <div id="orderMap" 
       style="height: 220px; border-radius: 8px; border: 1px solid #eee;" 
       data-lat="<?= $lat ?>" 
       data-lng="<?= $lng ?>">
  </div>
<?php else: ?>
  <div class="text-muted">No map location available.</div>
<?php endif; ?>

