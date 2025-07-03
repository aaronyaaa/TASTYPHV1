<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerId = $stmt->fetchColumn();

// Optionally filter by status (for tabs)
$status = $_GET['status'] ?? 'pending';

$sql = "
SELECT o.*, u.first_name, u.last_name, u.profile_pics 
FROM orders o 
JOIN users u ON o.user_id = u.id 
WHERE o.seller_id = ?
";
$params = [$sellerId];
if ($status !== 'all') {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY o.order_date DESC LIMIT 20";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    echo "<p class='text-muted'>No orders found.</p>";
    return;
}
?>
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th>Customer</th>
        <th>Status</th>
        <th>Order Date</th>
        <th>Total</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (
        $orders as $order): ?>
        <tr class="order-row" data-order-id="<?= $order['order_id'] ?>" style="cursor:pointer;">
          <td>
                <div class="d-flex align-items-center gap-2">
                    <img src="../<?= $order['profile_pics'] ?: 'assets/images/default-profile.png' ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
              <span><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></span>
            </div>
          </td>
          <td>
                            <span class="badge <?= match ($order['status']) {
                                                    'pending' => 'bg-warning',
                                                    'delivered' => 'bg-success',
                                                    'processing', 'shipped' => 'bg-primary',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                } ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
          </td>
          <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
          <td>₱<?= number_format($order['total_price'], 2) ?></td>
          <td>
            <form method="post" action="../backend/seller/update_order_status.php" class="d-inline">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <?php if ($order['status'] === 'pending'): ?>
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" class="btn btn-sm btn-primary">Accept</button>
                    <?php elseif ($order['status'] === 'processing'): ?>
                        <input type="hidden" name="status" value="shipped">
                        <button type="submit" class="btn btn-sm btn-info">Ship</button>
                    <?php elseif ($order['status'] === 'shipped'): ?>
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-sm btn-success">Deliver</button>
                    <?php elseif ($order['status'] === 'delivered'): ?>
                        <button type="button" class="btn btn-sm btn-outline-success" disabled>Delivered</button>
                    <?php elseif ($order['status'] === 'cancelled'): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" disabled>Cancelled</button>
                    <?php endif; ?>
                    <?php if (!in_array($order['status'], ['delivered', 'cancelled'])): ?>
                        <input type="hidden" name="cancel" value="1">
                        <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-outline-danger">Cancel</button>
                    <?php endif; ?>
                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div> 