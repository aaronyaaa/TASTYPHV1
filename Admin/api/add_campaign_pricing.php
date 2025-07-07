<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$durationDays = $data['duration_days'] ?? null;
$price = $data['price'] ?? null;
$description = $data['description'] ?? null;

if (!$durationDays || !$price) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Duration and price are required']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO campaign_pricing (duration_days, price, description) VALUES (?, ?, ?)");
    $stmt->execute([$durationDays, $price, $description]);
    
    $pricingId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing option added successfully',
        'pricing_id' => $pricingId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to add pricing option']);
} 