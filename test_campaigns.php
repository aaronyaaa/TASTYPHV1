<?php
require_once 'database/db_connect.php';

try {
    // Check campaign_requests table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM campaign_requests");
    $stmt->execute();
    $campaignCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "Total campaign requests: " . $campaignCount . "\n";
    
    if ($campaignCount > 0) {
        // Get sample campaign data
        $stmt = $pdo->prepare("
            SELECT cr.*, cp.duration_days, cp.price, cp.description as pricing_description,
                   u.first_name, u.last_name, u.email
            FROM campaign_requests cr
            LEFT JOIN campaign_pricing cp ON cr.pricing_id = cp.pricing_id
            LEFT JOIN users u ON cr.user_id = u.id
            LIMIT 1
        ");
        $stmt->execute();
        $sample = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Sample campaign data:\n";
        echo "Title: " . ($sample['title'] ?? 'N/A') . "\n";
        echo "User: " . ($sample['first_name'] ?? 'N/A') . " " . ($sample['last_name'] ?? 'N/A') . "\n";
        echo "Pricing: ₱" . ($sample['price'] ?? '0.00') . " for " . ($sample['duration_days'] ?? '0') . " days\n";
        echo "Payment: " . ($sample['payment_method'] ?? 'N/A') . " - ₱" . ($sample['amount_spent'] ?? '0.00') . "\n";
        echo "Status: " . ($sample['status'] ?? 'N/A') . "\n";
    }
    
    // Check campaign_pricing table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM campaign_pricing");
    $stmt->execute();
    $pricingCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "Total pricing options: " . $pricingCount . "\n";
    
    // Check admin_logs table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM admin_logs");
    $stmt->execute();
    $logsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "Total admin logs: " . $logsCount . "\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?> 