<?php
session_start();
require_once '../includes/db_config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Validate required fields
if (!isset($input['campaign_id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$campaign_id = $input['campaign_id'];
$status = $input['status'];

// Validate status
$allowed_statuses = ['pending', 'approved', 'rejected'];
if (!in_array($status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Check if campaign exists
    $stmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE campaign_id = ?");
    $stmt->execute([$campaign_id]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$campaign) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Campaign not found']);
        exit;
    }
    
    // Update campaign status (removed updated_at column)
    $stmt = $pdo->prepare("UPDATE campaign_requests SET status = ? WHERE campaign_id = ?");
    $result = $stmt->execute([$status, $campaign_id]);
    
    if ($result) {
        // Log the action (only if admin_logs table exists)
        try {
            $admin_id = $_SESSION['admin_id'];
            $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
            $details = json_encode([
                'campaign_id' => $campaign_id,
                'old_status' => $campaign['status'],
                'new_status' => $status,
                'campaign_title' => $campaign['title']
            ]);
            $stmt->execute([$admin_id, 'campaign_status_update', $details]);
        } catch (PDOException $e) {
            // If admin_logs table doesn't exist, continue without logging
            // This prevents the entire operation from failing
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Campaign status updated successfully',
            'campaign_id' => $campaign_id,
            'new_status' => $status
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update campaign status']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 