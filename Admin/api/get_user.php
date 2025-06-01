<?php
require_once '../includes/db_config.php';

$id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? 'seller';

if ($type === 'supplier') {
    $stmt = $pdo->prepare("SELECT 
        u.*, 
        sa.business_name, sa.description, sa.store_address, sa.business_license, sa.application_date, sa.status,
        sa.latitude AS sa_latitude, sa.longitude AS sa_longitude
      FROM users u
      INNER JOIN supplier_applications sa ON u.id = sa.user_id
      WHERE u.id = ?");
} else {
    $stmt = $pdo->prepare("SELECT 
        u.*, 
        sa.business_name, sa.description, sa.store_address,
        sa.business_permit, sa.health_permit, sa.application_date, sa.status,
        sa.latitude AS sa_latitude, sa.longitude AS sa_longitude
      FROM users u
      INNER JOIN seller_applications sa ON u.id = sa.user_id
      WHERE u.id = ?");
}

$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($user);
