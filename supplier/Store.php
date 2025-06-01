<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

// Fetch supplier info and supplier_id
$stmt = $pdo->prepare("SELECT * FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store || empty($store['supplier_id'])) {
  echo json_encode(['error' => 'You are not registered as a supplier.']);
  exit;
}

$supplier_id = $store['supplier_id'];

// Store fields
$storeName        = $store['business_name'] ?? 'My Supply Store';
$storeStatus      = $store['store_status'] ?? 'inactive';
$storeRating      = $store['rating'] ?? 4.5; // Optional
$coverPhoto       = !empty($store['cover_photo']) ? "../" . $store['cover_photo'] : "../assets/images/default-cover.jpg";
$profileImage     = !empty($store['profile_pics']) ? "../" . $store['profile_pics'] : "../assets/images/default-profile.png";
$storeDescription = $store['description'] ?? '';
$isPublic         = !empty($store['is_public']);

$supplierCategories = [];

if ($userId) {
  $stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
  $stmt->execute([$userId]);
  $supplier_id = $stmt->fetchColumn();

  if ($supplier_id) {
    $stmt = $pdo->prepare("SELECT category_id, name FROM categories WHERE supplier_id = ? AND is_active = 1 ORDER BY created_at DESC");
    $stmt->execute([$supplier_id]);
    $supplierCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($storeName) ?> | Store Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="../assets/css/store_modal.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">
<link rel="stylesheet" href="../assets/css/tabs.css">
<link rel="stylesheet" href="../assets/css/categories.css">
<link rel="stylesheet" href="../assets/css/ingredient.css">




</head>
<body>
<?php include '../includes/nav/navbar_router.php'; ?>
<?php include('modal/header_modal.php'); ?>
<?php include '../supplier/components/store_header.php'; ?>
<?php include '../supplier/components/tabs.php'; ?>
<?php include '../includes/nav/chat.php'; ?>
<img src="<?= !empty($v['image_url']) && file_exists('../' . $v['image_url']) ? '../' . htmlspecialchars($v['image_url']) : '../assets/images/default-category.png' ?>"

<?php include('../supplier/modal/variant_modal.php'); ?>
<?php include('../supplier/modal/edit_ingredient_modal.php'); ?>


<script src="../assets/js/catergory.js"></script>
<script src="../assets/js/ingredient.js"></script>

<script src="../assets/js/supplier_header.js"></script>
<script src="../assets/js/supplier_map.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
