<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$pricingId = $data['pricing_id'] ?? null;

if (!$pricingId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Pricing ID is required']);
    exit();
}

try {
    // Soft delete by setting is_active to 0
    $stmt = $pdo->prepare("UPDATE campaign_pricing SET is_active = 0 WHERE pricing_id = ?");
    $stmt->execute([$pricingId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Pricing option deleted successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Pricing option not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete pricing option']);
} 