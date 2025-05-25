<?php
include_once("../database/db_connect.php");

header('Content-Type: application/json');

$region_id = isset($_GET['region_id']) ? (int)$_GET['region_id'] : 0;
if ($region_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name FROM provinces WHERE region_id = ? ORDER BY name");
    $stmt->execute([$region_id]);
    $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($provinces);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
