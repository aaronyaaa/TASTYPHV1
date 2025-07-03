<?php require_once '../backend/logic_store.php';?>

<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$seller_id = $_GET['seller_id'] ?? ($store['seller_id'] ?? null);
$_GET['seller_id'] = $seller_id;
$user_id = $_SESSION['user']['id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? null;

if ($seller_id) {
    $stmt = $pdo->prepare("INSERT INTO store_visits (store_type, store_id, user_id, ip_address) VALUES ('seller', ?, ?, ?)");
    $stmt->execute([$seller_id, $user_id, $ip]);
}
require_once '../backend/logic_store.php';

$sellerId = $store['seller_id']; // or however you're identifying the store
$categories = [];

$stmt = $pdo->prepare("SELECT category_id, name FROM categories WHERE seller_id = ?");
$stmt->execute([$sellerId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($storeName) ?> - Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/supplier_store_header.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/supplier_main.css">
    <link rel="stylesheet" href="../assets/css/ingredient.css">
    <script>window.sellerId = "<?= htmlspecialchars($seller_id) ?>";</script>
</head>

<body>
    <?php include '../includes/nav/navbar_router.php'; ?>
    <?php include 'store/seller_header.php'; ?>
    <?php include '../includes/nav/chat.php'; ?>
    <?php include '../includes/offcanvas.php'; ?>
    <?php include 'store/seller_main.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="../assets/js/seller_store.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>
