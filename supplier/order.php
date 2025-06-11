<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo "Access denied.";
    exit;
}

$stmt = $pdo->prepare("SELECT supplier_id, latitude AS supplier_lat, longitude AS supplier_lng FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);
$supplierId = $supplier['supplier_id'];

// Get counts for each status
$statusCountsStmt = $pdo->prepare("
    SELECT status, COUNT(*) AS count 
    FROM orders 
    WHERE supplier_id = ? 
    GROUP BY status
");
$statusCountsStmt->execute([$supplierId]);
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
  <title>Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="../assets/css/user_navbar.css">
  <link rel="stylesheet" href="../assets/css/order_styles.css">
      <link rel="stylesheet" href="../assets/css/supplier_dashboard.css">
    
</head>

<body 
  data-supplier-lat="<?= $supplier['supplier_lat'] ?? 0 ?>" 
  data-supplier-lng="<?= $supplier['supplier_lng'] ?? 0 ?>" 
  data-initial-status="<?= $currentStatus ?>">

<?php include '../includes/nav/navbar_router.php'; ?>

<!-- Left Navigation -->
<div class="sidebar">
  <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
  <a href="order.php"><i class="bi bi-box-seam me-2"></i>Orders</a>
  <a href="#"><i class="bi bi-cash-coin me-2"></i>Payments</a>
  <a href="#"><i class="bi bi-gear me-2"></i>Settings</a>
  <a href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>
<?php include '../includes/nav/chat.php'; ?>

<!-- Main Content + Right Panel -->
<div class="main-wrapper" id="mainWrapper">
  <div class="table-wrapper">
    <ul class="nav nav-tabs mb-4" id="orderTabs">
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

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/supplier_order.js"></script>

</body>
</html>
