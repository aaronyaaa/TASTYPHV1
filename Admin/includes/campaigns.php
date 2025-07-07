<?php
require_once 'db_config.php';

// Get campaign status counts
function getCampaignStatusCounts($pdo) {
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM campaign_requests GROUP BY status");
    $stmt->execute();
    $counts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $counts[$row['status']] = $row['count'];
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
    .filter-dropdown {
      position: relative;
      display: inline-block;
    }
    .filter-dropdown .dropdown-menu {
      min-width: 200px;
    }
    .filter-dropdown .dropdown-item {
      padding: 0.5rem 1rem;
      cursor: pointer;
    }
    .filter-dropdown .dropdown-item:hover {
      background-color: #f8f9fa;
    }
    .filter-dropdown .dropdown-item.active {
      background-color: #0d6efd;
      color: white;
    }
    .campaign-status-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
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
    <div class="d-flex gap-2">
      <!-- Campaign Status Filter Dropdown -->
      <div class="filter-dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-filter me-2"></i>
          <span id="currentFilterText">All Campaigns</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
          <li><a class="dropdown-item active" href="#" data-filter="all">
            <i class="fas fa-list me-2"></i>All Campaigns
            <span class="badge bg-secondary float-end"><?= $total ?></span>
          </a></li>
          <li><a class="dropdown-item" href="#" data-filter="ongoing">
            <i class="fas fa-play-circle me-2"></i>Ongoing Campaigns
            <span class="badge bg-success float-end" id="ongoing-count">0</span>
          </a></li>
          <li><a class="dropdown-item" href="#" data-filter="expired">
            <i class="fas fa-clock me-2"></i>Expired Campaigns
            <span class="badge bg-warning float-end" id="expired-count">0</span>
          </a></li>
          <li><a class="dropdown-item" href="#" data-filter="pending">
            <i class="fas fa-hourglass-half me-2"></i>Pending Campaigns
            <span class="badge bg-secondary float-end"><?= $statusCounts['pending'] ?? 0 ?></span>
          </a></li>
          <li><a class="dropdown-item" href="#" data-filter="approved">
            <i class="fas fa-check-circle me-2"></i>Approved Campaigns
            <span class="badge bg-success float-end"><?= $statusCounts['approved'] ?? 0 ?></span>
          </a></li>
          <li><a class="dropdown-item" href="#" data-filter="rejected">
            <i class="fas fa-times-circle me-2"></i>Rejected Campaigns
            <span class="badge bg-danger float-end"><?= $statusCounts['rejected'] ?? 0 ?></span>
          </a></li>
        </ul>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pricingModal">
        <i class="fas fa-cog me-2"></i>Manage Pricing
      </button>
    </div>
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
            <h5 class="card-title mb-0" data-role="pending-count"><?= $statusCounts['pending'] ?? 0 ?></h5>
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
            <h5 class="card-title mb-0" data-role="approved-count"><?= $statusCounts['approved'] ?? 0 ?></h5>
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
            <h5 class="card-title mb-0" data-role="rejected-count"><?= $statusCounts['rejected'] ?? 0 ?></h5>
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

<!-- Pricing Management Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-labelledby="pricingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pricingModalLabel">Campaign Pricing Management</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add New Pricing Form -->
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0">Add New Pricing Option</h6>
          </div>
          <div class="card-body">
            <form id="addPricingForm">
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">Duration (Days)</label>
                  <input type="number" class="form-control" id="durationDays" name="duration_days" min="1" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Price (₱)</label>
                  <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Description</label>
                  <input type="text" class="form-control" id="description" name="description" placeholder="e.g., 3 Days Campaign">
                </div>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-plus me-2"></i>Add Pricing Option
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Current Pricing Options -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Current Pricing Options</h6>
          </div>
          <div class="card-body">
            <div id="pricingOptionsList">
              <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Pricing Modal -->
<div class="modal fade" id="editPricingModal" tabindex="-1" aria-labelledby="editPricingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPricingModalLabel">Edit Pricing Option</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editPricingForm">
          <input type="hidden" id="editPricingId" name="pricing_id">
          <div class="mb-3">
            <label class="form-label">Duration (Days)</label>
            <input type="number" class="form-control" id="editDurationDays" name="duration_days" min="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Price (₱)</label>
            <input type="number" class="form-control" id="editPrice" name="price" min="0" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" id="editDescription" name="description">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="updatePricing()">Update Pricing</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/tastyphv1/Admin/assets/js/campaigns.js"></script>
<script src="/tastyphv1/Admin/assets/js/pricing.js"></script>
</body>
</html>
