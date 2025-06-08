<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['userId'];
$data = json_decode(file_get_contents("php://input"), true);

$type = $data['type'] ?? '';
$value = $data['value'] ?? '';

try {
    if ($type === 'status' && in_array($value, ['online', 'offline'])) {
        $stmt = $pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$value, $userId]);
        echo json_encode(['status' => 'success']);
    } elseif ($type === 'visibility' && in_array($value, [0, 1], true)) {
        $stmt = $pdo->prepare("UPDATE users SET is_public = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$value, $userId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
