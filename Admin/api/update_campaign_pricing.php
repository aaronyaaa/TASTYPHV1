<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$pricingId = $data['pricing_id'] ?? null;
$durationDays = $data['duration_days'] ?? null;
$price = $data['price'] ?? null;
$description = $data['description'] ?? null;

if (!$pricingId || !$durationDays || !$price) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Pricing ID, duration and price are required']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE campaign_pricing SET duration_days = ?, price = ?, description = ? WHERE pricing_id = ?");
    $stmt->execute([$durationDays, $price, $description, $pricingId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Pricing option updated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Pricing option not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update pricing option']);
} 