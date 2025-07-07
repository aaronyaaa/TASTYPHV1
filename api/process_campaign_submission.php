<?php
require_once("../database/db_connect.php");
require_once("../database/session.php");

header('Content-Type: application/json');

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Handle form data (multipart/form-data)
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $pricingId = $_POST['pricing_id'] ?? null;
    $durationDays = $_POST['duration_days'] ?? null;
    $paymentMethod = $_POST['payment_method'] ?? '';
    
    // Handle banner image upload
    $bannerImage = '';
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/campaigns/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = uniqid('banner_', true) . '.' . pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
            $bannerImage = 'uploads/campaigns/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload banner image']);
            exit;
        }
    }
    
    // Handle payment data
    $paymentData = [];
    if ($paymentMethod === 'cash') {
        $paymentData['cash_amount'] = floatval($_POST['payment_data']['cash_amount'] ?? 0);
    } elseif ($paymentMethod === 'gcash') {
        if (isset($_FILES['payment_data']['receipt_image']) && $_FILES['payment_data']['receipt_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/receipts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = uniqid('receipt_', true) . '.' . pathinfo($_FILES['payment_data']['receipt_image']['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['payment_data']['receipt_image']['tmp_name'], $targetPath)) {
                $paymentData['receipt_image'] = 'uploads/receipts/' . $filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload receipt image']);
                exit;
            }
        }
    }

    // Validation
    if (empty($title) || empty($startDate) || empty($endDate) || !$pricingId || !$durationDays || empty($paymentMethod)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }

    // Get pricing information
    $stmt = $pdo->prepare("SELECT * FROM campaign_pricing WHERE pricing_id = ? AND is_active = 1");
    $stmt->execute([$pricingId]);
    $pricing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pricing) {
        echo json_encode(['success' => false, 'message' => 'Invalid pricing option']);
        exit;
    }

    $pdo->beginTransaction();

    // Insert campaign request
    $stmt = $pdo->prepare("INSERT INTO campaign_requests 
        (user_type, user_id, pricing_id, duration_days, title, description, banner_image, start_date, end_date, amount_spent, payment_method, payment_status)
        VALUES ('seller', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid')");
    
    $stmt->execute([
        $userId,
        $pricingId,
        $durationDays,
        $title, 
        $description, 
        $bannerImage, 
        $startDate, 
        $endDate, 
        $pricing['price'],
        $paymentMethod
    ]);

    $campaignId = $pdo->lastInsertId();

    // Handle payment based on method
    if ($paymentMethod === 'cash') {
        $cashAmount = $paymentData['cash_amount'] ?? 0;
        if ($cashAmount < $pricing['price']) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Cash amount is less than required payment']);
            exit;
        }
        
        // Update payment status to paid
        $stmt = $pdo->prepare("UPDATE campaign_requests SET payment_status = 'paid', paid_at = NOW() WHERE campaign_id = ?");
        $stmt->execute([$campaignId]);
        
    } elseif ($paymentMethod === 'gcash') {
        // For GCash, we expect a receipt image
        if (empty($paymentData['receipt_image'])) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'GCash receipt image is required']);
            exit;
        }
        
        // Store receipt image path in the database
        $stmt = $pdo->prepare("UPDATE campaign_requests SET payment_status = 'pending', payment_method = 'gcash' WHERE campaign_id = ?");
        $stmt->execute([$campaignId]);
        
        // You might want to store the receipt image path in a separate table
        // For now, we'll just mark as pending payment
    }

    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Campaign submitted successfully!',
        'campaign_id' => $campaignId
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} 