<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

// Get supplier ID
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplierId = $stmt->fetchColumn();

if (!$supplierId) {
    echo '<p>No supplier info found.</p>';
    exit;
}

// Get today's orders with joined ingredient & user data
$stmt = $pdo->prepare("
    SELECT 
        oi.order_id,
        oi.ingredient_id,
        i.ingredient_name,
        i.image_url,
        o.order_date,
        o.payment_method,
        u.first_name,
        u.last_name,
        u.full_address
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
    JOIN users u ON o.user_id = u.id
    WHERE o.supplier_id = ? AND DATE(o.order_date) = CURDATE()
    ORDER BY o.order_date DESC
    LIMIT 10
");
$stmt->execute([$supplierId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    echo '<p class="text-muted">No orders today.</p>';
    return;
}
?>

<div class="today-orders-list">
<?php foreach ($orders as $order): ?>
    <div class="today-order-item">
        <div class="today-order-image">
            <img src="<?= !empty($order['image_url']) ? '../' . $order['image_url'] : '../assets/images/default-food.jpg' ?>" alt="<?= htmlspecialchars($order['ingredient_name']) ?>">
            <div class="today-order-tooltip">
                <small>Customer Name</small>
                <span><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></span>

                <small>Location</small>
                <span><?= htmlspecialchars($order['full_address'] ?: 'N/A') ?></span>

                <small>Payment</small>
                <span><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
        </div>
        <div class="today-order-details">
            <strong><?= htmlspecialchars($order['ingredient_name']) ?></strong>
            <small><?= date('h:i A', strtotime($order['order_date'])) ?></small>
        </div>
    </div>
<?php endforeach; ?>
</div>

