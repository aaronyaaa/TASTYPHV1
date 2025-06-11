<?php
include_once("../database/db_connect.php"); // loads $user array from DB and session
include_once("../database/session.php"); // Loads user into $_SESSION['user']

// Ensure user is logged in
if (!isset($user['id'])) {
  header("Location: ../auth/login.php");
  exit;
}

// Optional: fetch latest from DB in case session is outdated
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $user['id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback to session if DB fails
if (!$currentUser) {
  $currentUser = $user;
}


// User fields (from `users` table)
$fullName         = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
$accountStatus    = 'active'; // Optional static or dynamic field
$coverPhoto       = !empty($currentUser['cover_photo']) ? "../" . $currentUser['cover_photo'] : "../assets/images/default-cover.jpg";
$profileImage     = !empty($currentUser['profile_pics']) ? "../" . $currentUser['profile_pics'] : "../assets/images/default-profile.png";
$userId           = $currentUser['id'] ?? 0;
$bio              = $currentUser['full_address'] ?? 'No address available';
$fullName   = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
$userStatus = $currentUser['status'] ?? 'offline';
$isPublic   = !empty($currentUser['is_public']);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/post_style.css">


</head>

<body class="bg-light">

  <?php include '../includes/nav/navbar_router.php'; ?>
  <?php include '../users/components/user_header.php'; ?>
    <?php include '../users/components/timeline.php'; ?>

  <?php include '../modals/user_modal.php'; ?>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="../assets/js/user.js"></script>
  <script src="../assets/js/user_photo.js"></script>

  <script>
    window.initialLatitude = <?php echo $user['latitude'] ?? 'null'; ?>;
    window.initialLongitude = <?php echo $user['longitude'] ?? 'null'; ?>;
  </script>
</body>

</html>