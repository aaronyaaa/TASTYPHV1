<?php
include_once("../database/db_connect.php");

header('Content-Type: application/json');

$city_id = isset($_GET['city_id']) ? (int)$_GET['city_id'] : 0;
if ($city_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name FROM barangays WHERE city_id = ? ORDER BY name");
    $stmt->execute([$city_id]);
    $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($barangays);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
