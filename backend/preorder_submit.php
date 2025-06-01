<?php
require_once '../database/db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
  header("Location: ../users/login.php");
  exit;
}

$userId = $_SESSION['user']['id'];
$userAddress = $_POST['full_address'] ?? '';

// Required fields
$sellerId      = $_POST['seller_id'] ?? null;
$productName   = trim($_POST['product_name'] ?? '');
$quantity      = (int)($_POST['quantity'] ?? 0);
$unit          = trim($_POST['unit'] ?? '');
$preferredDate = $_POST['preferred_date'] ?? null;
$preferredTime = $_POST['preferred_time'] ?? null;
$notes         = trim($_POST['additional_notes'] ?? '');

// Validate inputs
if (!$sellerId || !$productName || $quantity <= 0 || empty($userAddress)) {
  header("Location: ../users/home.php?error=missing_fields");
  exit;
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO pre_order_list (
      user_id, seller_id, product_name, quantity, unit,
      preferred_date, preferred_time, additional_notes, full_address
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $userId, $sellerId, $productName, $quantity, $unit,
    $preferredDate, $preferredTime, $notes, $userAddress
  ]);

  // Redirect back to home or a confirmation page
  header("Location: ../users/home.php?success=preorder_submitted");
  exit;

} catch (PDOException $e) {
  error_log('Pre-order DB error: ' . $e->getMessage());
  header("Location: ../users/home.php?error=db");
  exit;
}
