<?php
header('Content-Type: application/json');

include_once '../database/db_connect.php'; // Assumes you have a DB connection file

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$user_id = $_SESSION['userId'];

// Collect and sanitize input
define('REQUIRED_FIELDS', ['first_name', 'last_name', 'gender', 'contact_number']);
$fields = [];
foreach (REQUIRED_FIELDS as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
        exit;
    }
    $fields[$field] = htmlspecialchars(trim($_POST[$field]));
}
$fields['middle_name'] = isset($_POST['middle_name']) ? htmlspecialchars(trim($_POST['middle_name'])) : '';

// Validate gender
$valid_genders = ['Male', 'Female', 'Other'];
if (!in_array($fields['gender'], $valid_genders)) {
    echo json_encode(['success' => false, 'message' => 'Invalid gender.']);
    exit;
}

// Update user in database
$sql = "UPDATE users SET first_name=?, middle_name=?, last_name=?, gender=?, contact_number=?, updated_at=NOW() WHERE id=?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
$stmt->bind_param(
    'sssssi',
    $fields['first_name'],
    $fields['middle_name'],
    $fields['last_name'],
    $fields['gender'],
    $fields['contact_number'],
    $user_id
);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
}
$stmt->close();
$conn->close(); 