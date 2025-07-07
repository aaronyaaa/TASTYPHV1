<?php
session_start();
require_once '../includes/db_config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if campaign ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Campaign ID is required']);
    exit;
}

$campaign_id = $_GET['id'];

try {
    // Fetch campaign details with pricing and user information
    $stmt = $pdo->prepare("
        SELECT cr.*, cp.duration_days, cp.price, cp.description as pricing_description,
               u.first_name, u.last_name, u.email
        FROM campaign_requests cr
        LEFT JOIN campaign_pricing cp ON cr.pricing_id = cp.pricing_id
        LEFT JOIN users u ON cr.user_id = u.id
        WHERE cr.campaign_id = ?
    ");
    $stmt->execute([$campaign_id]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$campaign) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Campaign not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'campaign' => $campaign
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 