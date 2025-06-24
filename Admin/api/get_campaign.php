<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE campaign_id = ?");
$stmt->execute([$id]);
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($campaign); 