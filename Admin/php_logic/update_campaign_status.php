<?php
require_once '../includes/db_config.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
$campaignId = $_POST['campaignId'] ?? null;
$status = $_POST['status'] ?? null;
if (!$campaignId || !in_array($status, ['approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}
try {
    $stmt = $pdo->prepare("UPDATE campaign_requests SET status = ?, admin_feedback = NULL WHERE campaign_id = ?");
    $stmt->execute([$status, $campaignId]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update status']);
} 