<?php
session_start();
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_supplier'])) {
    $userId = $_POST['userId'];
    $businessName = trim($_POST['business_name']);
    $description = trim($_POST['description']);
    $latitude = $_POST['latitude'] ?: null;
    $longitude = $_POST['longitude'] ?: null;
    $fullAddress = trim($_POST['full_address']);
    $storeAddress = $fullAddress; // alias for clarity

    // Check for existing pending/approved supplier application
    $checkStmt = $pdo->prepare("SELECT status FROM supplier_applications WHERE user_id = :user_id ORDER BY application_date DESC LIMIT 1");
    $checkStmt->execute(['user_id' => $userId]);
    $existing = $checkStmt->fetch();

    if ($existing && in_array($existing['status'], ['pending', 'approved'])) {
        $_SESSION['error'] = "You already have a pending or approved supplier application.";
        header("Location: ../users/home.php");
        exit();
    }

    // Uploads directory
    $uploadDir = '../uploads/licenses/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    // Handle business license upload
    $businessLicenseName = null;
    if (!empty($_FILES['business_license']['name'])) {
        $ext = pathinfo($_FILES['business_license']['name'], PATHINFO_EXTENSION);
        $businessLicenseName = 'license_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['business_license']['tmp_name'], $uploadDir . $businessLicenseName);
    }

    // Insert into supplier_applications
    $stmt = $pdo->prepare("INSERT INTO supplier_applications (
        user_id, business_name, description, store_address, latitude, longitude, full_address,
        business_license, status
    ) VALUES (
        :user_id, :business_name, :description, :store_address, :latitude, :longitude, :full_address,
        :business_license, 'pending'
    )");

    try {
        $stmt->execute([
            'user_id' => $userId,
            'business_name' => $businessName,
            'description' => $description,
            'store_address' => $storeAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'full_address' => $fullAddress,
            'business_license' => $businessLicenseName
        ]);

        // Optional: create a notification
        $notifStmt = $pdo->prepare("INSERT INTO notifications (sender_id, receiver_id, message, is_read, created_at)
            VALUES (:sender_id, :receiver_id, :message, 0, NOW())");

        $notifStmt->execute([
            'sender_id' => null,
            'receiver_id' => $userId,
            'message' => 'Your supplier application was submitted successfully.'
        ]);

        $_SESSION['success'] = "Your supplier application was submitted successfully!";
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
