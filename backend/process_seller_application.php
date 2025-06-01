<?php
session_start();
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_seller'])) {
    $userId = $_POST['userId'];
    $businessName = trim($_POST['business_name']);
    $description = trim($_POST['description']);
    $storeAddress = trim($_POST['store_address']);
    $latitude = $_POST['latitude'] ?: null;
    $longitude = $_POST['longitude'] ?: null;

    // Check if user already has a pending or approved application
    $checkStmt = $pdo->prepare("SELECT status FROM seller_applications WHERE user_id = :user_id ORDER BY application_date DESC LIMIT 1");
    $checkStmt->execute(['user_id' => $userId]);
    $existing = $checkStmt->fetch();

    if ($existing && in_array($existing['status'], ['pending', 'approved'])) {
        $_SESSION['error'] = "You already have an active or pending seller application.";
        header("Location: ../users/home.php");
        exit();
    }

    // Create upload directory if not exists
    $uploadDir = '../uploads/permits/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    // Handle file uploads
    $businessPermitName = null;
    if (!empty($_FILES['business_permit']['name'])) {
        $ext = pathinfo($_FILES['business_permit']['name'], PATHINFO_EXTENSION);
        $businessPermitName = 'permit_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['business_permit']['tmp_name'], $uploadDir . $businessPermitName);
    }

    $healthPermitName = null;
    if (!empty($_FILES['health_permit']['name'])) {
        $ext = pathinfo($_FILES['health_permit']['name'], PATHINFO_EXTENSION);
        $healthPermitName = 'health_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['health_permit']['tmp_name'], $uploadDir . $healthPermitName);
    }

    // Insert seller application
    $stmt = $pdo->prepare("INSERT INTO seller_applications (user_id, business_name, description, store_address, latitude, longitude, full_address, business_permit, health_permit, status)
        VALUES (:user_id, :business_name, :description, :store_address, :latitude, :longitude, :full_address, :business_permit, :health_permit, 'pending')");

    try {
        $stmt->execute([
            'user_id' => $userId,
            'business_name' => $businessName,
            'description' => $description,
            'store_address' => $storeAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'full_address' => $storeAddress,
            'business_permit' => $businessPermitName,
            'health_permit' => $healthPermitName
        ]);

        // Insert notification using sender_id and receiver_id (sender = system/admin, receiver = applicant)
        $notifStmt = $pdo->prepare("INSERT INTO notifications (sender_id, receiver_id, message, is_read, created_at)
            VALUES (:sender_id, :receiver_id, :message, 0, NOW())");

        $notifStmt->execute([
            'sender_id' => null, // or 0 if you want to denote 'system'
            'receiver_id' => $userId,
            'message' => 'Your seller application was submitted successfully.'
        ]);

        $_SESSION['success'] = "Your seller application was submitted successfully!";
        header("Location: ../users/home.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Application failed: " . $e->getMessage();
        header("Location: ../users/home.php");
        exit();
    }

} else {
    header("Location: ../users/home.php");
    exit();
}
