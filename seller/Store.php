<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

// Fetch seller info
$userId = $_SESSION['userId'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Store fields
$storeName        = $store['business_name'] ?? 'My Store';
$storeStatus      = $store['store_status'] ?? 'inactive';
$storeRating      = $store['rating'] ?? 4.5;
$coverPhoto       = !empty($store['cover_photo']) ? "../" . $store['cover_photo'] : "../assets/images/default-cover.jpg";
$profileImage     = !empty($store['profile_pics']) ? "../" . $store['profile_pics'] : "../assets/images/default-profile.png";
$userId           = $store['user_id'] ?? 0;
$storeDescription = $store['description'] ?? '';
$isPublic         = !empty($store['is_public']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($storeName) ?> | Store Dashboard</title>
 <!-- HEAD -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="../assets/css/store_modal.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">
<link rel="stylesheet" href="../assets/css/tabs.css">




</head>
<body>
<?php include '../includes/nav/navbar_router.php'; ?>
<?php include('modal/header_modal.php'); ?>
<?php include '../seller/components/store_header.php'; ?>
<?php include '../seller/components/tabs.php'; ?>
  <?php include('modal/recipe_modal.php'); ?>
        <?php include '../includes/offcanvas.php'; ?>



<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/store_header.js"></script>
<script src="../assets/js/store_map.js"></script>



</body>
</html>
