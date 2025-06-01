<?php
require_once '../includes/db_config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_POST['userId'] ?? null;
$status = $_POST['status'] ?? null;

if (!$userId || !in_array($status, ['approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Update supplier application status
    $stmt = $pdo->prepare("
        UPDATE supplier_applications
        SET status = ?, reviewed_by = ?, reviewed_at = NOW()
        WHERE user_id = ?
    ");
    $stmt->execute([$status, $_SESSION['admin_id'], $userId]);

    // Update the user's usertype based on approval or rejection
    $newUsertype = ($status === 'approved') ? 'supplier' : 'user';

    $stmt2 = $pdo->prepare("
        UPDATE users
        SET usertype = ?
        WHERE id = ?
    ");
    $stmt2->execute([$newUsertype, $userId]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
}
