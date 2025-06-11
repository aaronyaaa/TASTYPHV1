<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplierId = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT o.*, u.first_name, u.last_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.supplier_id = ? ORDER BY o.order_date DESC LIMIT 10");
$stmt->execute([$supplierId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    echo '<div class="alert alert-warning">No orders available.</div>';
    return;
}
?>
<table class="table-custom">
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Customer</th>
      <th>Total</th>
      <th>Status</th>
    </tr>
  </thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= $order['first_name'] . ' ' . $order['last_name'] ?></td>
            <td>₱<?= number_format($order['total_price'], 2) ?></td>
            <td><span class="badge bg-<?= $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'delivered' ? 'success' : 'primary') ?>">
                <?= ucfirst($order['status']) ?>
            </span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
