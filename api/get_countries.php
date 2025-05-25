<?php
header('Content-Type: application/json');
require_once '../database/db_connect.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, name FROM countries ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($countries);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error fetching countries: " . $e->getMessage()]);
}
?> 