<?php
require_once '../database/db_connect.php';
session_start();

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['usertype'] !== 'seller') {
    die("Unauthorized access");
}

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$sellerRow = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sellerRow) die("Seller profile not found.");
$sellerId = $sellerRow['seller_id'];

$stmt = $pdo->prepare("
  SELECT o.*, u.first_name, u.last_name, u.email, u.profile_pics, u.contact_number,
         u.full_address AS user_address, u.latitude, u.longitude
  FROM pre_order_list o
  JOIN users u ON o.user_id = u.id
  WHERE o.seller_id = ?
  ORDER BY o.request_date DESC
");
$stmt->execute([$sellerId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$statusGroups = ['pending' => [], 'approved' => [], 'declined' => [], 'delivered' => []];
foreach ($orders as $order) {
    $statusGroups[strtolower($order['status'])][] = $order;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tabbed Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="../assets/css/user_navbar.css">
  <link rel="stylesheet" href="../assets/css/order.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css"> <!-- Custom CSS for tracker -->

</head>
<body>
<?php include '../includes/nav/chat.php'; ?>

<?php include '../includes/nav/navbar_router.php'; ?>
<?php include('modal/reason_modal.php'); ?>
<!-- Sidebar Navigation -->
<?php include '../seller/components/sidebar.php'; ?>

  <?php include('modal/recipe_modal.php'); ?>


<nav id="sidebar">
  <div class="mt-4" id="sidebarContent">
    <p class="text-muted">Select an order to view details.</p>
  </div>  
</nav>
<!-- Main Wrapper -->
<div class="main-wrapper" id="mainWrapper" style="margin-left: 240px;">
  <div class="container py-4" id="mainContainer">
    <h2 class="mb-4">Orders</h2>

    <!-- Tabs -->
<ul class="nav nav-tabs justify-content-center" role="tablist">
      <?php foreach ($statusGroups as $status => $group): ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link<?= $status === 'pending' ? ' active' : '' ?>"
                  id="<?= $status ?>-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#tab-<?= $status ?>"
                  type="button" role="tab">
            <?= ucfirst($status) ?> (<?= count($group) ?>)
          </button>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3">
      <?php foreach ($statusGroups as $status => $group): ?>
        <div class="tab-pane fade<?= $status === 'pending' ? ' show active' : '' ?>"
             id="tab-<?= $status ?>"
             role="tabpanel">
          <?php if (empty($group)): ?>
            <p class="text-muted">No <?= $status ?> orders.</p>
          <?php else: ?>
            <div class="d-flex fw-bold border-bottom py-2 mb-2">
              <div class="flex-grow-1">Full Name</div>
              <div style="width: 180px;">Status</div>
              <div style="width: 180px;">Product</div>
              <div style="width: 120px;">Qty</div>
              <div style="width: 200px;">Date</div>
              <div style="width: 160px;">Time</div>
            </div>
            <?php foreach ($group as $order): ?>
              <div class="d-flex align-items-center py-2 px-2 bg-light mb-2 rounded clickable-row"
                   data-order='<?= htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8') ?>'>
                <div class="flex-grow-1 d-flex align-items-center">
                  <img src="../<?= $order['profile_pics'] ?: 'assets/images/default-profile.png' ?>"
                       class="rounded-circle me-2"
                       style="width: 40px; height: 40px; object-fit: cover;">
                  <span><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></span>
                </div>
                <div style="width: 180px;">
                  <span class="badge bg-<?= match(strtolower($status)) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'declined' => 'danger',
                    'delivered' => 'info',
                    default => 'secondary'
                  } ?>">
                    <?= ucfirst($status) ?>
                  </span>
                </div>
                <div style="width: 180px;"><?= htmlspecialchars($order['product_name']) ?></div>
                <div style="width: 120px;"><?= htmlspecialchars($order['quantity'] . ' ' . ($order['unit'] ?? '')) ?></div>
                <div style="width: 200px;"><?= htmlspecialchars($order['preferred_date']) ?></div>
                <div style="width: 160px;"><?= htmlspecialchars($order['preferred_time']) ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>


    <script src="../assets/js/order.js"></script>

<div class="modal fade" id="productListModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">📦 Select Products to Deliver</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">
          Choose which products to include and specify how many units to deliver.
        </p>
        <form id="productSelectionForm">
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Available Stock</th>
                  <th scope="col">Deliver Quantity</th>
                </tr>
              </thead>
              <tbody id="productListContainer">
                <!-- JavaScript will populate this -->
              </tbody>
            </table>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="confirmDelivery()">Confirm Delivery</button>
      </div>
    </div>
  </div>
</div>



<!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    
</body>
</html>
