<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once '../database/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];

// Sanitize inputs
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
$postal_code = trim($_POST['postal_code'] ?? '');
$address_line = trim($_POST['address_line'] ?? '');
$full_address = trim($_POST['full_address'] ?? '');

if ($latitude === null || $longitude === null || !$full_address) {
    echo json_encode(['success' => false, 'message' => 'Latitude, longitude, and full address are required']);
    exit;
}

try {
    // You may need to add full_address column in users table as TEXT if not yet added.
    $sql = "UPDATE users SET
            latitude = :latitude,
            longitude = :longitude,
            postal_code = :postal_code,
            streetname = :address_line,
            full_address = :full_address
            WHERE id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':postal_code' => $postal_code,
        ':address_line' => $address_line,
        ':full_address' => $full_address,
        ':user_id' => $user_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Address saved successfully']);
} catch (Exception $e) {
    error_log("Address update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save address: ' . $e->getMessage()]);
}
