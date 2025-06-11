<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo "Access denied.";
    exit;
}

$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplierId = $stmt->fetchColumn();

if (!$supplierId) {
    echo "Access denied.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/supplier_dashboard.css">
    <link rel="stylesheet" href="../assets/css/table_styles.css">


</head>
<body>
    <?php include '../includes/nav/navbar_router.php'; ?>

    <div class="sidebar">
        <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="order.php"><i class="bi bi-box-seam me-2"></i>Orders</a>
        <a href="#"><i class="bi bi-cash-coin me-2"></i>Payments</a>
        <a href="#"><i class="bi bi-gear me-2"></i>Settings</a>
        <a href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

<div class="main-content">
    <div class="row g-4 mt-3">
        <div class="col-md-8">
            <div class="card order-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order List</h5>
                    <span class="text-muted" style="font-size: 0.9rem;">Real-time</span>
                </div>
                <div class="card-body" id="order-list-container">
                    <!-- Orders will be loaded here via JS -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card order-card">
                <div class="card-header">
                    <h5>Today's Orders</h5>
                </div>
                <div class="card-body" id="today-orders-container">
                    <!-- Today's orders loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function fetchOrders() {
    fetch('fetch_orders.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('order-list-container').innerHTML = html;
        });

    fetch('fetch_today_orders.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('today-orders-container').innerHTML = html;
        });
}

setInterval(fetchOrders, 5000); // Refresh every 5s
window.onload = fetchOrders;
</script>
</body>
</html>
