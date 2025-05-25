<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Extract user info safely
$first_name = htmlspecialchars($_SESSION['user']['first_name'] ?? 'User');
$profilePics = $_SESSION['user']['profile_pics'] ?? '';
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!-- Desktop Navbar -->
<nav class="navbar navbar-expand-lg kakanin-navbar shadow-lg py-2 d-none d-lg-flex" style="background: linear-gradient(90deg, #7B4397 70%, #FDEB71 100%); border-radius: 0 0 24px 24px;">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center gap-2" href="#" style="color: #FDEB71; font-weight: bold; font-size: 1.7rem;">
      <img src="../assets/images/logo.png" alt="TASTYPH Logo" class="logo" style="height: 44px; border-radius: 50%; box-shadow: 0 2px 8px #8E44AD55; background: #FFF8E7; border: 2px solid #FDEB71;">
      <span><i class="fa-solid fa-cookie-bite"></i> TASTYPH</span>
    </a>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item">
          <a class="nav-link active d-flex align-items-center gap-1" aria-current="page" href="../users/home.php">
            <i class="fa-solid fa-house"></i> Home
          </a>
        </li>
      </ul>

      <!-- Search bar replacing Products/About/Contact -->
      <form class="d-flex mx-4 flex-grow-1" role="search" action="../includes/search_page.php" method="GET">
        <input class="form-control me-2" type="search" name="query" placeholder="Search for products, stores, categories..." aria-label="Search" style="border-radius: 20px;">
        <button class="btn btn-warning" type="submit" style="border-radius: 20px;">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </form>

      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-1" href="../includes/chat.php">
            <i class="fa-solid fa-comments"></i>
          </a>
        </li>
        <li class="nav-item position-relative">
          <a class="nav-link d-flex align-items-center gap-1" href="../cart/cart.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php if ($cartCount > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark shadow-sm" style="font-size: 0.8rem;">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item position-relative">
          <a class="nav-link d-flex align-items-center gap-1" href="notifications.php">
            <i class="fa-solid fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.7rem;">3</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="navbarDropdownMenuLink" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (!empty($profilePics)) : ?>
              <img src="../uploads/<?php echo htmlspecialchars($profilePics); ?>" alt="User Photo"
                class="rounded-circle bg-secondary shadow-sm"
                style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #FDEB71;">
            <?php else : ?>
              <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center shadow-sm"
                style="width: 32px; height: 32px; overflow: hidden; border: 2px solid #FDEB71;">
                <i class="fa-solid fa-user text-light fs-5"></i>
              </div>
            <?php endif; ?>
            <span><?php echo $first_name; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow-lg" aria-labelledby="navbarDropdownMenuLink" style="margin-top: 0.25rem;">
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="../users/settings.php"><i class="fa-solid fa-cog"></i> My Account</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#applySupplierModal"><i class="fa-solid fa-hand-holding-heart"></i> Apply as Supplier</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#applySellerModal"><i class="fa-solid fa-store"></i> Apply as Seller</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="../api/auth/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Mobile Search Bar (fixed top) -->
<nav class="navbar navbar-expand-lg kakanin-navbar shadow-lg py-2 d-lg-none fixed-top" style="background: #7B4397; z-index: 1080;">
  <div class="container-fluid px-3">

    <form class="d-flex flex-grow-1 ms-3" role="search" action="../includes/search_page.php" method="GET">
      <input class="form-control me-2" type="search" name="query" placeholder="Search for products, stores, categories..." aria-label="Search" style="border-radius: 20px;">
      <button class="btn btn-warning" type="submit" style="border-radius: 20px;">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
  </div>
</nav>

<!-- Mobile Bottom Navbar -->
<nav class="navbar fixed-bottom d-lg-none mobile-bottom-nav">
  <div class="container-fluid justify-content-around px-0">
    <a href="../users/home.php" class="nav-link d-flex flex-column align-items-center justify-content-center">
      <i class="fa-solid fa-house"></i>
      <small>Home</small>
    </a>
    <a href="../includes/chat.php" class="nav-link d-flex flex-column align-items-center justify-content-center">
      <i class="fa-solid fa-comments"></i>
      <small>Chat</small>
    </a>
    <a href="../cart/cart.php" class="nav-link position-relative d-flex flex-column align-items-center justify-content-center">
      <i class="fa-solid fa-cart-shopping"></i>
      <span class="badge rounded-pill">3</span>
      <small>Cart</small>
    </a>
    <a href="../users/settings.php" class="nav-link d-flex flex-column align-items-center justify-content-center">
      <i class="fa-solid fa-user"></i>
      <small>Settings</small>
    </a>
  </div>
</nav>


        <script src="../assets/js/user_navbar.js"></script>
