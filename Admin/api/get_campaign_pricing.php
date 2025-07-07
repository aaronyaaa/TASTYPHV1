<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM campaign_pricing WHERE is_active = 1 ORDER BY duration_days ASC");
    $pricing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'pricing' => $pricing
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch pricing data'
    ]);
} 