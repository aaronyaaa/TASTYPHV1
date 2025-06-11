<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) exit;

$stmt = $pdo->prepare("
  SELECT o.*, u.id AS user_id, u.first_name, u.last_name, u.email, u.contact_number, u.full_address, u.profile_pics, u.latitude, u.longitude
  FROM orders o 
  JOIN users u ON o.user_id = u.id 
  WHERE o.order_id = ?
");
$stmt->execute([$orderId]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<p>Order not found.</p>";
    exit;
}

$statusClass = match($data['status']) {
  'pending' => 'secondary',
  'processing' => 'primary',
  'shipped' => 'warning',
  'delivered' => 'success',
  'cancelled' => 'danger',
  default => 'light'
};
?>

<div class="card border-0">
  <div class="card-body text-center">
    <img src="../<?= $data['profile_pics'] ?? 'assets/images/default-profile.png' ?>" 
         class="rounded-circle shadow mb-3" 
         style="width: 100px; height: 100px; object-fit: cover;">
    <h5 class="fw-semibold mb-0"><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></h5>
    <p class="text-muted mb-1"><?= htmlspecialchars($data['email']) ?></p>
    <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($data['status']) ?></span>
  </div>

  <hr class="my-2">

  <div class="card-body small">
    <div class="mb-2 d-flex align-items-start">
      <i class="bi bi-telephone-fill me-2 text-primary"></i>
      <span><strong>Phone:</strong> <?= htmlspecialchars($data['contact_number']) ?></span>
    </div>
    <div class="mb-2 d-flex align-items-start">
      <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
      <span><strong>Address:</strong> <?= htmlspecialchars($data['full_address']) ?></span>
    </div>
    <div class="mb-2 d-flex align-items-start">
      <i class="bi bi-credit-card-fill me-2 text-dark"></i>
      <span><strong>Payment:</strong> <?= htmlspecialchars($data['payment_method']) ?></span>
    </div>
    <div class="mb-2 d-flex align-items-start">
      <i class="bi bi-box2-fill me-2 text-info"></i>
      <span><strong>Total:</strong> ₱<?= number_format($data['total_price'], 2) ?></span>
    </div>
    <div class="d-flex align-items-start">
      <i class="bi bi-clock-history me-2 text-secondary"></i>
      <span><strong>Date:</strong> <?= date('d-m-Y H:i', strtotime($data['order_date'])) ?></span>
    </div>
  </div>

  <div class="p-3">
    <div id="map"
         data-user-lat="<?= $data['latitude'] ?>"
         data-user-lng="<?= $data['longitude'] ?>"
         data-supplier-lat="<?= $_GET['supplier_lat'] ?? 0 ?>"
         data-supplier-lng="<?= $_GET['supplier_lng'] ?? 0 ?>"
         style="height: 240px; border-radius: 10px; border: 1px solid #dee2e6;">
    </div>
  </div>

  <!-- Chat Button -->
<div class="text-center mt-3">
  <button 
    class="btn btn-outline-primary btn-sm open-chat-from-sidebar w-100"
    data-user-id="<?= $data['user_id'] ?>"
    data-user-name="<?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?>">
    <i class="bi bi-chat-dots me-1"></i> Chat
  </button>
</div>
</div>
