<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg fixed-top" style="background: rgba(123, 67, 151, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(123, 67, 151, 0.15);">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="index.php" style="font-size:1.5rem; border-radius:12px; padding:0.5rem 1rem; transition: all 0.3s ease;">
      <i class="fas fa-utensils" style="font-size:1.8rem; color:#FDEB71;"></i>
      TastyPH
    </a>

    <!-- Mobile toggle button -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>

    <!-- Desktop navbar links -->
    <div class="collapse navbar-collapse justify-content-end d-none d-lg-flex">
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item">
          <a href="#hero" class="nav-link text-white<?php echo $current_page === 'index.php' ? ' active' : ''; ?>" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-home"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a href="#about" class="nav-link text-white" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-info-circle"></i> About
          </a>
        </li>
        <li class="nav-item">
          <a href="#menu" class="nav-link text-white" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-utensils"></i> Menu
          </a>
        </li>
        <li class="nav-item">
          <a href="#contact" class="nav-link text-white" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-envelope"></i> Contact
          </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a href="profile.php" class="nav-link text-white <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
              <i class="fas fa-user"></i> Profile
            </a>
          </li>
          <li class="nav-item">
            <a href="api/auth/logout.php" class="nav-link text-white" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#loginModal" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#signupModal" style="font-weight:500; font-size:1rem; border-radius:12px; padding:0.8rem 1.2rem; display:flex; align-items:center; gap:0.5rem;">
              <i class="fas fa-user-plus"></i> Sign Up
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Offcanvas sidebar for mobile -->
<div class="offcanvas offcanvas-start bg-primary text-white" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 250px;">
  <div class="offcanvas-header border-bottom border-white">
    <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="#hero" class="nav-link text-white px-3 py-2<?php echo $current_page === 'index.php' ? ' active' : ''; ?>">
          <i class="fas fa-home me-2"></i> Home
        </a>
      </li>
      <li class="nav-item">
        <a href="#about" class="nav-link text-white px-3 py-2">
          <i class="fas fa-info-circle me-2"></i> About
        </a>
      </li>
      <li class="nav-item">
        <a href="#menu" class="nav-link text-white px-3 py-2">
          <i class="fas fa-utensils me-2"></i> Menu
        </a>
      </li>
      <li class="nav-item">
        <a href="#contact" class="nav-link text-white px-3 py-2">
          <i class="fas fa-envelope me-2"></i> Contact
        </a>
      </li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a href="profile.php" class="nav-link text-white px-3 py-2 <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user me-2"></i> Profile
          </a>
        </li>
        <li class="nav-item">
          <a href="api/auth/logout.php" class="nav-link text-white px-3 py-2">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
          </a>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <a href="#" class="nav-link text-white px-3 py-2" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fas fa-sign-in-alt me-2"></i> Login
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link text-white px-3 py-2" data-bs-toggle="modal" data-bs-target="#signupModal">
            <i class="fas fa-user-plus me-2"></i> Sign Up
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</div>