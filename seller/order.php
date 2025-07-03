<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo "Access denied.";
    exit;
}

// Get seller info
$stmt = $pdo->prepare("SELECT seller_id, latitude AS seller_lat, longitude AS seller_lng FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);
$sellerId = $seller['seller_id'];

// Get counts for each status
$statusCountsStmt = $pdo->prepare("
    SELECT status, COUNT(*) AS count 
    FROM orders 
    WHERE seller_id = ? 
    GROUP BY status
");
$statusCountsStmt->execute([$sellerId]);
$statusCounts = array_column($statusCountsStmt->fetchAll(PDO::FETCH_ASSOC), 'count', 'status');

$allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$statusTotals = array_fill_keys($allStatuses, 0);
foreach ($statusCounts as $key => $val) {
    $statusTotals[$key] = $val;
}
$totalOrders = array_sum($statusTotals);

// Default status from GET or fallback
$currentStatus = $_GET['status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="../assets/css/user_navbar.css">
  <link rel="stylesheet" href="../assets/css/order_styles.css">
  <link rel="stylesheet" href="../assets/css/sidebar.css">
</head>
<body 
  data-seller-lat="<?= $seller['seller_lat'] ?? 0 ?>" 
  data-seller-lng="<?= $seller['seller_lng'] ?? 0 ?>" 
  data-initial-status="<?= $currentStatus ?>">

<?php include '../includes/nav/chat.php'; ?>
<?php include '../includes/nav/navbar_router.php'; ?>
<?php include('components/sidebar.php'); ?>

<div class="main-wrapper" id="mainWrapper" style="margin-left: 240px;">
  <div class="table-wrapper">
    <h2 class="mb-4">Orders</h2>
    <ul class="nav nav-tabs justify-content-center mb-4" id="orderTabs">
      <?php foreach (array_merge(['pending'], array_diff($allStatuses, ['pending']), ['all']) as $tabStatus): ?>
        <?php
          $label = ucfirst($tabStatus);
          $count = $tabStatus === 'all' ? $totalOrders : $statusTotals[$tabStatus];
          $badgeClass = match ($tabStatus) {
            'pending' => 'bg-warning',
            'processing' => 'bg-primary',
            'shipped' => 'bg-info text-dark',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
          };
        ?>
        <li class="nav-item">
          <a 
            class="nav-link<?= $currentStatus === $tabStatus ? ' active' : '' ?>" 
            data-status="<?= $tabStatus ?>" 
            href="?status=<?= $tabStatus ?>">
            <?= $label ?>
            <span class="badge <?= $badgeClass ?> rounded-pill"><?= $count ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="mt-4" id="orderTableWrapper"></div>
  </div>
  <!-- Right Sidebar for Order Details -->
  <nav id="orderDetailsPanel">
    <div class="mt-4" id="orderDetailsContent">
      <p class="text-muted">Select an order to view details.</p>
    </div>
  </nav>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/seller_order.js"></script>
</body>
</html> 