<?php
include_once("../database/db_connect.php");

header('Content-Type: application/json');

$province_id = isset($_GET['province_id']) ? (int)$_GET['province_id'] : 0;
if ($province_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name FROM cities WHERE province_id = ? ORDER BY name");
    $stmt->execute([$province_id]);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cities);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
