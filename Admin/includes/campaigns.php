<?php
require_once 'db_config.php';

// Get campaign status counts
function getCampaignStatusCounts($pdo) {
    $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
    $stmt = $pdo->query("SELECT status, COUNT(*) AS count FROM campaign_requests GROUP BY status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = strtolower($row['status']);
        if (isset($counts[$status])) {
            $counts[$status] += (int) $row['count'];
        }
    }
    return $counts;
}

$statusCounts = getCampaignStatusCounts($pdo);
$total = array_sum($statusCounts);
$footerLabel = 'All Campaigns';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campaign Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/applications.css">
  <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/right_sidebar.css">
  <style>
    .main-content { margin-left: 250px; padding: 2rem; }
  </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar d-flex flex-column position-fixed">
  <h4 class="mb-4 text-center">Admin</h4>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="/tastyphv1/Admin/dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/applications.php" class="nav-link text-white"><i class="fas fa-users me-2"></i>Users</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/campaigns.php" class="nav-link text-white active"><i class="fas fa-box-open me-2"></i>Campaigns</a></li>
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
    <h4>Campaign Requests</h4>
  </div>
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card border-0 shadow-sm text-white bg-primary cursor-pointer" onclick="filterCampaignByStatus('all')">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0" data-role="total-count"><?= $total ?></h5>
            <small>All Campaigns</small>
          </div>
          <i class="fas fa-layer-group fa-2x"></i>
        </div>
        <div class="card-footer text-black-50 small bg-primary-subtle"><?= $footerLabel ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card border-0 shadow-sm text-white bg-secondary cursor-pointer" onclick="filterCampaignByStatus('pending')">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0" data-role="pending-count"><?= $statusCounts['pending'] ?></h5>
            <small>Pending Campaigns</small>
          </div>
          <i class="fas fa-hourglass-half fa-2x"></i>
        </div>
        <div class="card-footer text-black-50 small bg-secondary-subtle"><?= $footerLabel ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card border-0 shadow-sm text-white bg-success cursor-pointer" onclick="filterCampaignByStatus('approved')">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0" data-role="approved-count"><?= $statusCounts['approved'] ?></h5>
            <small>Approved Campaigns</small>
          </div>
          <i class="fas fa-check-circle fa-2x"></i>
        </div>
        <div class="card-footer text-black-50 small bg-success-subtle"><?= $footerLabel ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card border-0 shadow-sm text-white bg-danger cursor-pointer" onclick="filterCampaignByStatus('rejected')">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0" data-role="rejected-count"><?= $statusCounts['rejected'] ?></h5>
            <small>Rejected Campaigns</small>
          </div>
          <i class="fas fa-times-circle fa-2x"></i>
        </div>
        <div class="card-footer text-black-50 small bg-danger-subtle"><?= $footerLabel ?></div>
      </div>
    </div>
  </div>
  <div id="campaignTable"></div>
</div>
<div class="user-details-panel" id="userPanel">
  <div class="p-3 position-relative">
    <span class="btn-close close-btn" onclick="closePanel()"></span>
    <div id="user-details-content">
      <p class="text-muted">Select a campaign to view details.</p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/tastyphv1/Admin/assets/js/campaigns.js"></script>
</body>
</html>
