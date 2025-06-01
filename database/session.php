<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit();
}

include_once __DIR__ . '/db_connect.php';

$userId = $_SESSION['userId'];

$sql = "SELECT
          id,
          first_name,
          middle_name,
          last_name,
          date_of_birth,
          full_address,
          contact_number,
          country_id,
          postal_code,
          streetname,
          email,
          usertype,
          profile_pics,
          latitude,
          longitude,
          gender,
          created_at,
          updated_at
        FROM users
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../index.php");
    exit();
}

// Sanitize user data
foreach ($user as $key => $value) {
    $user[$key] = htmlspecialchars($value);
}

// Save sanitized user data in session for reuse elsewhere
$_SESSION['user'] = $user;
?>
