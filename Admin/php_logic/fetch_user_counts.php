<?php
require_once __DIR__ . '/../includes/db_config.php';
$range = $_GET['range'] ?? 'month';

switch ($range) {
    case 'day':  $where = "DATE(created_at) = CURDATE()"; break;
    case 'week': $where = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"; break;
    case 'year': $where = "YEAR(created_at) = YEAR(CURDATE())"; break;
    default:     $where = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"; break;
}

$stmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(usertype = 'user') AS users,
        SUM(usertype = 'seller') AS sellers,
        SUM(usertype = 'supplier') AS suppliers
    FROM users
    WHERE $where
");
$stmt->execute();
$counts = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="row g-4">
  <!-- USERS -->
  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-primary">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0"><?= $counts['users'] ?? 0 ?></h5>
          <small>Users</small>
        </div>
        <i class="fas fa-user fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-primary-subtle">
        <?= $counts['users'] ?? 0 ?> new users <?= $range ?>
      </div>
    </div>
  </div>

  <!-- SELLERS -->
  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-warning">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0"><?= $counts['sellers'] ?? 0 ?></h5>
          <small>Sellers</small>
        </div>
        <i class="fas fa-store fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-warning-subtle">
        <?= $counts['sellers'] ?? 0 ?> new sellers <?= $range ?>
      </div>
    </div>
  </div>

  <!-- SUPPLIERS -->
  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-info">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0"><?= $counts['suppliers'] ?? 0 ?></h5>
          <small>Suppliers</small>
        </div>
        <i class="fas fa-truck fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-info-subtle">
        <?= $counts['suppliers'] ?? 0 ?> new suppliers <?= $range ?>
      </div>
    </div>
  </div>

  <!-- TOTAL -->
  <div class="col-md-3">
    <div class="card stat-card border-0 shadow-sm text-white bg-dark">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0"><?= $counts['total'] ?? 0 ?></h5>
          <small>Total Users</small>
        </div>
        <i class="fas fa-users fa-2x"></i>
      </div>
      <div class="card-footer text-black-50 small bg-secondary-subtle">
        Total signups <?= $range ?>
      </div>
    </div>
  </div>
</div>
