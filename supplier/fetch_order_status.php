<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

// Get supplier_id
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplierId = $stmt->fetchColumn();

// Get order counts for tabs
$statusCountsStmt = $pdo->prepare("
    SELECT status, COUNT(*) AS count 
    FROM orders 
    WHERE supplier_id = ? 
    GROUP BY status
");
$statusCountsStmt->execute([$supplierId]);
$statusCounts = array_column($statusCountsStmt->fetchAll(PDO::FETCH_ASSOC), 'count', 'status');

// Setup counts
$allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$statusTotals = array_fill_keys($allStatuses, 0);
foreach ($statusCounts as $key => $val) {
    $statusTotals[$key] = $val;
}
$totalOrders = array_sum($statusTotals);

// Determine active status
$status = $_GET['status'] ?? 'pending'; // Default: show pending

// Render tabs
?>


<?php
// Fetch orders for the selected tab
$sql = "
SELECT o.*, u.first_name, u.last_name, u.profile_pics 
FROM orders o 
JOIN users u ON o.user_id = u.id 
WHERE o.supplier_id = ?
";
$params = [$supplierId];
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

foreach ($orders as $order):
    $itemStmt = $pdo->prepare("
        SELECT oi.quantity, oi.unit_price, i.ingredient_name, i.image_url
        FROM order_items oi
        LEFT JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
        WHERE oi.order_id = ?
    ");
    $itemStmt->execute([$order['order_id']]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="../<?= $order['profile_pics'] ?: 'assets/images/default-profile.png' ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                    <div>
                        <h6 class="mb-0 mb-md-1 d-flex align-items-center gap-2">
                            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                            <span class="badge <?= match ($order['status']) {
                                                    'pending' => 'bg-warning',
                                                    'delivered' => 'bg-success',
                                                    'processing', 'shipped' => 'bg-primary',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                } ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </h6>
                        <small class="text-muted"><?= date('d M Y', strtotime($order['order_date'])) ?></small>
                    </div>
                </div>

                <form method="post" action="../backend/update_order_status.php" class="d-flex align-items-center gap-2">
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


            </div>

            <table class="table table-borderless">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50%;">Item</th>
                        <th style="width: 25%;">Quantity</th>
                        <th style="width: 25%;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="../<?= $item['image_url'] ?: 'assets/images/default-product.png' ?>" width="40" height="40" class="rounded shadow-sm" style="object-fit: cover;">
                                    <?= htmlspecialchars($item['ingredient_name']) ?>
                                </div>
                            </td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <strong>Total: ₱<?= number_format($order['total_price'], 2) ?></strong>
                <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?= $order['order_id'] ?>)">View Info</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>