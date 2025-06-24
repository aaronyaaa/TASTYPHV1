<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$campaignId = intval($_GET['campaign_id'] ?? 0);
if ($campaignId <= 0) {
    die("Invalid campaign.");
}

$userId = $_SESSION['user']['id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// Check if campaign exists first
$stmt = $pdo->prepare("SELECT user_type, user_id FROM campaign_requests WHERE campaign_id = ?");
$stmt->execute([$campaignId]);
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campaign) {
    die("Campaign not found.");
}

// Insert click tracking
try {
    $stmt = $pdo->prepare("INSERT INTO campaign_clicks (campaign_id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->execute([$campaignId, $userId, $ip, $userAgent]);
} catch (PDOException $e) {
    error_log("Error logging campaign click: " . $e->getMessage());
}

// Determine redirect URL
$storeUrl = "#";
if ($campaign['user_type'] === 'seller') {
    $stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
    $stmt->execute([$campaign['user_id']]);
    $seller = $stmt->fetch();
    if ($seller) {
        $storeUrl = "../users/seller_store.php?seller_id=" . $seller['seller_id'];
    }
} elseif ($campaign['user_type'] === 'supplier') {
    $stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
    $stmt->execute([$campaign['user_id']]);
    $supplier = $stmt->fetch();
    if ($supplier) {
        $storeUrl = "../users/supplier_store.php?supplier_id=" . $supplier['supplier_id'];
    }
}

header("Location: $storeUrl");
exit;
