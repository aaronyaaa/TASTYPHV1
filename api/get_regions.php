<?php
include_once("../database/db_connect.php");  // Your DB connection

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, name FROM regions ORDER BY name");
    $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($regions);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load regions']);
}
