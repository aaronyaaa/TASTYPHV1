<?php
include("../database/session.php");

$first_name = $_SESSION['user']['first_name'] ?? 'User';
$profile_pics = $_SESSION['user']['profile_pics'] ?? '';
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

require_once '../database/db_connect.php';

// Get unread notification count for this user
$notifCount = 0;
$notifications = [];
if (isset($_SESSION['userId'])) {
  $userId = $_SESSION['userId'];

  // Count unread notifications
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE receiver_id = ? AND is_read = 0");
  $stmt->execute([$userId]);
  $notifCount = $stmt->fetchColumn();

  // Fetch latest notifications
  $stmt = $pdo->prepare("SELECT * FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC LIMIT 6");
  $stmt->execute([$userId]);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="../assets/css/notifications.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg kakanin-navbar shadow-lg py-2 fixed-top" style="background: linear-gradient(90deg, #006466 70%, #9ffcdf 100%); z-index: 1050;">
  <div class="container-fluid px-3">
    <!-- Logo + Brand as Home -->
    <a class="navbar-brand d-flex align-items-center gap-2 text-warning fw-bold fs-4" href="../users/home.php">
      <img src="../assets/images/logo.png" alt="TASTYPH Logo" style="height: 44px; border-radius: 50%; border: 2px solid #FDEB71;">
      <span><i class="fa-solid fa-cookie-bite"></i> TASTYPH</span>
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <form class="d-flex flex-grow-1 mx-4 position-relative" role="search" action="../includes/search_page.php" method="GET" autocomplete="off">
        <input id="searchInput" class="form-control me-2 rounded-pill" type="search" name="q" placeholder="Search ingredients, products, recipes, stores..." aria-label="Search">
        <button class="btn btn-warning rounded-pill" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

        <!-- Autocomplete Dropdown -->
        <ul class="list-group position-absolute w-100 shadow-sm z-3" id="autocompleteList" style="top: 100%; left: 0; display: none;"></ul>
      </form>


      <ul class="navbar-nav align-items-center gap-3">
        <!-- Chat -->


        <!-- Cart -->
        <li class="nav-item position-relative">
          <a class="nav-link text-white" href="../cart/cart.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php if ($cartCount > 0): ?>
              <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size: 0.75rem;">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown">
          <a class="nav-link text-white" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-bell"></i>
            <?php if ($notifCount > 0): ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size: 0.75rem;">
                <?= $notifCount ?>
              </span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-2 shadow" aria-labelledby="notifDropdown" style="width: 320px; background-color: #7B4397;">
            <li class="fw-bold text-white text-uppercase py-2 px-2 user-select-none" style="pointer-events: none;">Notifications</li>
            <hr class="my-1 text-white-50">
            <?php if (!empty($notifications)): ?>
              <?php foreach ($notifications as $notif): ?>
                <?php
                $type = $notif['type'] ?? 'default';
                $templatePath = "../includes/components/notifications/{$type}.php";
                if (!file_exists($templatePath)) {
                  $templatePath = "../includes/components/notifications/default.php";
                }
                include $templatePath;
                ?>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="text-center text-white-50 small py-2 user-select-none" style="pointer-events: none;">No notifications yet</li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- Profile -->
        <li class="nav-item dropdown position-relative">
          <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (!empty($profile_pics)) : ?>
              <img src="../<?= htmlspecialchars($profile_pics) ?>" class="rounded-circle shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
            <?php else : ?>
              <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-user text-white"></i>
              </div>
            <?php endif; ?>
            <span><?= htmlspecialchars($first_name) ?></span>
          </a>

          <div class="dropdown-menu dropdown-menu-end user-dropdown p-3" aria-labelledby="userDropdown">
            <div class="d-flex align-items-center gap-3 border-bottom border-light pb-3 mb-3">
              <?php if (!empty($profile_pics)) : ?>
                <img src="../<?= htmlspecialchars($profile_pics) ?>" class="rounded-circle border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
              <?php else : ?>
                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                  <i class="fa-solid fa-user text-white fs-4"></i>
                </div>
              <?php endif; ?>
              <div>
                <h6 class="mb-0 text-white"><?= htmlspecialchars($first_name) ?></h6>
                <small class="text-white-50">User Account</small>
              </div>
            </div>

            <a href="../users/user_profile.php" class="dropdown-item"><i class="fa-solid fa-gear"></i> My Account</a>
            <a href="../includes/start_selling.php" class="dropdown-item"><i class="fa-solid fa-briefcase"></i> Start Selling</a>
            <div class="dropdown-divider"></div>
            <a href="../api/auth/logout.php" class="dropdown-item"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
          </div>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap + Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user_navbar.js"></script>