<?php require_once '../backend/logic_supplier_store.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($storeName) ?> - Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/supplier_store_header.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/supplier_main.css">
    <link rel="stylesheet" href="../assets/css/ingredient.css">
    <!-- Chat CSS should be loaded last to prevent style conflicts -->
</head>

<body>
    <?php include '../includes/nav/navbar_router.php'; ?>
    <?php include 'store/supplier_header.php'; ?>

    <?php include '../includes/nav/chat.php'; ?>

    <?php include 'store/supplier_main.php'; ?>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS and CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="../assets/js/supplier_store.js"></script>


</body>

</html>