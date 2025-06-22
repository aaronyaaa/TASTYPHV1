<?php require_once '../backend/logic_store.php';?>
<?php include 'store/seller_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($storeName) ?> - Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/store_header.css">
  <link rel="stylesheet" href="../assets/css/user_navbar.css">

</head>
<body>
<?php include '../includes/nav/navbar_router.php'; ?>
<?php include '../includes/nav/chat.php'; ?>


<?php include 'store/modal.php'; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS and CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="../assets/js/seller_store.js"></script>


</body>
</html>
