<?php
require_once 'db_config.php';

$type = $_GET['type'] ?? 'all';

function getStatusCounts($pdo, $table) {
    $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
    $stmt = $pdo->query("SELECT status, COUNT(*) AS count FROM {$table} GROUP BY status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = strtolower($row['status']);
        if (isset($counts[$status])) {
            $counts[$status] += (int) $row['count'];
        }
    }
    return $counts;
}

$sellerCounts = getStatusCounts($pdo, 'seller_applications');
$supplierCounts = getStatusCounts($pdo, 'supplier_applications');

if ($type === 'seller') {
    $statusCounts = $sellerCounts;
    $footerLabel = 'Seller only';
} elseif ($type === 'supplier') {
    $statusCounts = $supplierCounts;
    $footerLabel = 'Supplier only';
} else {
    $statusCounts = [
        'pending' => $sellerCounts['pending'] + $supplierCounts['pending'],
        'approved' => $sellerCounts['approved'] + $supplierCounts['approved'],
        'rejected' => $sellerCounts['rejected'] + $supplierCounts['rejected'],
    ];
    $footerLabel = 'Seller + Supplier';
}
$total = array_sum($statusCounts);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/applications.css">
  <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/right_sidebar.css">
  <style>
    #userMap, #businessMap {
      height: 200px;
      border-radius: 8px;
    }
    .user-details-panel {
      overflow-y: auto;
    }
    .cursor-pointer {
      cursor: pointer;
    }
    .main-content {
      margin-left: 250px;
      padding: 2rem;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column position-fixed">
  <h4 class="mb-4 text-center">Admin</h4>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="/tastyphv1/Admin/dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/applications.php" class="nav-link text-white"><i class="fas fa-users me-2"></i>Users</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/campaigns.php" class="nav-link text-white"><i class="fas fa-box-open me-2"></i>Campaigns</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="fas fa-chart-line me-2"></i>Reports</a></li>
  </ul>
  <hr>
  <div class="mt-auto">
    <a href="/tastyphv1/Admin/login/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
  <div class="mb-4 d-flex justify-content-between align-items-center">
    <h4>Applications</h4>
    <div class="form-group">
      <label for="applicationType" class="form-label">Application Type</label>
<select id="applicationType" class="form-select form-select-sm w-auto">
  <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All</option>
  <option value="seller" <?= $type === 'seller' ? 'selected' : '' ?>>Seller</option>
  <option value="supplier" <?= $type === 'supplier' ? 'selected' : '' ?>>Supplier</option>
</select>

    </div>
  </div>

<!-- In your HTML where the cards are: -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-primary cursor-pointer" onclick="filterByStatus('all')">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0" data-role="total-count"><?= $total ?></h5>
          <small>All Applications</small>
        </div>
        <i class="fas fa-layer-group fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-primary-subtle"><?= $footerLabel ?></div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-secondary cursor-pointer" onclick="filterByStatus('pending')">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0" data-role="pending-count"><?= $statusCounts['pending'] ?></h5>
          <small>Pending Applications</small>
        </div>
        <i class="fas fa-hourglass-half fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-secondary-subtle"><?= $footerLabel ?></div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-success cursor-pointer" onclick="filterByStatus('approved')">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0" data-role="approved-count"><?= $statusCounts['approved'] ?></h5>
          <small>Approved Applications</small>
        </div>
        <i class="fas fa-check-circle fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-success-subtle"><?= $footerLabel ?></div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-danger cursor-pointer" onclick="filterByStatus('rejected')">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0" data-role="rejected-count"><?= $statusCounts['rejected'] ?></h5>
          <small>Rejected Applications</small>
        </div>
        <i class="fas fa-times-circle fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-danger-subtle"><?= $footerLabel ?></div>
    </div>
  </div>
</div>



  <div id="applicationTable"></div>
</div>

<div class="user-details-panel" id="userPanel">
  <div class="p-3 position-relative">
    <span class="btn-close close-btn" onclick="closePanel()"></span>
    <div id="user-details-content">
      <p class="text-muted">Select a user to view details.</p>
    </div>
  </div>
</div>

<div class="modal fade" id="permitModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Permit Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <img id="permitViewerImg" src="" alt="Permit Preview">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="/tastyphv1/Admin/assets/js/applications.js"></script>
</body>
</html>
