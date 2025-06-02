<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$currentUserId = $_SESSION['userId'] ?? null;

$supplierId = $_GET['supplier_id'] ?? null;
if (!$supplierId) {
    die("Invalid supplier store link.");
}

// Fetch supplier store details (public access)
$stmt = $pdo->prepare("SELECT * FROM supplier_applications WHERE supplier_id = ?");
$stmt->execute([$supplierId]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store) {
    die("Supplier store not found.");
}

$storeName        = $store['business_name'] ?? 'My Supplier Store';
$storeStatus      = $store['store_status'] ?? 'inactive';
$storeRating      = $store['rating'] ?? 4.5;
$storeDescription = $store['description'] ?? '';
$coverPhoto       = !empty($store['cover_photo']) ? "../" . $store['cover_photo'] : "../assets/images/default-cover.jpg";
$profileImage     = !empty($store['profile_pics']) ? "../" . $store['profile_pics'] : "../assets/images/default-profile.png";
$lat              = $store['latitude'] ?? 0;
$lng              = $store['longitude'] ?? 0;

// Get supplier's user_id and full name
$userId = null;
$userFullName = '';
$isOwnStore = false;

$stmt = $pdo->prepare("
    SELECT sa.user_id, u.first_name, u.last_name
    FROM supplier_applications sa
    JOIN users u ON sa.user_id = u.id
    WHERE sa.supplier_id = ?
");
$stmt->execute([$supplierId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $userId = $row['user_id'];
    $userFullName = $row['first_name'] . ' ' . $row['last_name'];
    $isOwnStore = ($currentUserId && $currentUserId == $userId);
}

// Keep the supplier ID from the URL for public viewing
$supplier_id = $supplierId;

// Fetch categories (public access)
$stmt = $pdo->prepare("SELECT category_id, name FROM categories WHERE supplier_id = ? ORDER BY name");
$stmt->execute([$supplier_id]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch ingredients (public access)
$sql = "SELECT * FROM ingredients WHERE supplier_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$supplier_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

