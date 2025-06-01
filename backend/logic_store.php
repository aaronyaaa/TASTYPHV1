<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$currentUserId = $_SESSION['user']['id'] ?? null;

$sellerId = $_GET['seller_id'] ?? null;
if (!$sellerId) {
    die("Invalid store link.");
}

// Fetch store details
$stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE seller_id = ? AND is_public = 1");
$stmt->execute([$sellerId]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store) {
    die("Store not found or is private.");
}

$storeName        = $store['business_name'] ?? 'My Store';
$storeStatus      = $store['store_status'] ?? 'inactive';
$storeRating      = $store['rating'] ?? 4.5;
$storeDescription = $store['description'] ?? '';
$coverPhoto       = !empty($store['cover_photo']) ? "../" . $store['cover_photo'] : "../assets/images/default-cover.jpg";
$profileImage     = !empty($store['profile_pics']) ? "../" . $store['profile_pics'] : "../assets/images/default-profile.png";
$lat              = $store['latitude'] ?? 0;
$lng              = $store['longitude'] ?? 0;

// Get seller's user_id and real name
$userId = null;
$userFullName = '';
$isOwnStore = false;

$stmt = $pdo->prepare("
    SELECT sa.user_id, u.first_name, u.last_name
    FROM seller_applications sa
    JOIN users u ON sa.user_id = u.id
    WHERE sa.seller_id = ?
");
$stmt->execute([$sellerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $userId = $row['user_id'];
    $userFullName = $row['first_name'] . ' ' . $row['last_name'];
    $isOwnStore = ($currentUserId && $currentUserId == $userId);
}
?>