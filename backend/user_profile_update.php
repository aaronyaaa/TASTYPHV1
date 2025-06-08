<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized - no user ID']);
    exit;
}

$userId = $_SESSION['userId'];

// Profile fields
$firstName    = trim($_POST['first_name'] ?? '');
$middleName   = trim($_POST['middle_name'] ?? '');
$lastName     = trim($_POST['last_name'] ?? '');
$dateOfBirth  = trim($_POST['date_of_birth'] ?? '');
$contactNum   = trim($_POST['contact_number'] ?? '');
$email        = trim($_POST['email'] ?? '');
$gender       = trim($_POST['gender'] ?? '');

// Location fields
$addressLine  = trim($_POST['address_line'] ?? '');
$postalCode   = trim($_POST['postal_code'] ?? '');
$latitude     = $_POST['latitude'] ?? null;
$longitude    = $_POST['longitude'] ?? null;
$fullAddress  = trim($_POST['full_address'] ?? '');

// Fallback if coords are missing
if (empty($latitude) || empty($longitude)) {
    $latitude = 13.41;
    $longitude = 122.56;
    $fullAddress = 'Philippines';
}

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            first_name = ?, 
            middle_name = ?, 
            last_name = ?, 
            date_of_birth = ?, 
            contact_number = ?, 
            email = ?, 
            gender = ?, 
            streetname = ?, 
            postal_code = ?, 
            latitude = ?, 
            longitude = ?, 
            full_address = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([
        $firstName, $middleName, $lastName,
        $dateOfBirth, $contactNum, $email, $gender,
        $addressLine, $postalCode, $latitude, $longitude, $fullAddress,
        $userId
    ]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
