<?php
include_once("../database/db_connect.php"); // loads $user array from DB and session
include_once("../database/session.php"); // Loads user into $_SESSION['user']

?>
<?php
$profilePic = $_SESSION['user']['profile_pics'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Profile - Shopee Style</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/user_navbar.css" />
  <link rel="stylesheet" href="../assets/css/map_modal.css" />
  <link rel="stylesheet" href="../assets/css/settings.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />

  <style>
    /* Set z-index for all modals */
    .modal {
      z-index: 100 !important;
    }
    .modal-backdrop {
      z-index: 99 !important;
    }
  </style>
</head>
<body>

<!-- Mobile Header with Back Arrow (visible only on mobile) -->
<div class="mobile-back-header d-lg-none">
  <a href="javascript:history.back()" class="back-link d-flex align-items-center text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
         class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
      <path fill-rule="evenodd" 
            d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
    </svg>
    <span class="fs-5 fw-semibold">Settings</span>
  </a>
</div>

<?php include '../includes/nav/navbar_router.php'; ?>
  <?php include '../modals/settings_modal.php'; ?>
  <?php include '../modals/edit_address_modal.php'; ?>
  <?php include '../includes/nav/chat.php'; ?>

  <div class="container py-5">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-lg-3 mb-4">
        <div class="list-group" id="settingsSidebar" role="tablist">

          <!-- My Account label (toggles collapse) -->
          <div class="sidebar-header" id="myAccountToggle" aria-expanded="true" aria-controls="accountCollapse">
            My Account
            <i class="fa-solid fa-chevron-down"></i>
          </div>

          <!-- Collapse with account tabs -->
          <div class="collapse show" id="accountCollapse" data-bs-parent="#settingsSidebar">
            <div class="list-group ms-3 mt-2" role="tablist" aria-orientation="vertical">
              <a href="#profile-pane" class="list-group-item list-group-item-action active" id="profile-tab" data-bs-toggle="tab" role="tab" aria-controls="profile-pane" aria-selected="true">Profile</a>
              <a href="#addresses-pane" class="list-group-item list-group-item-action" id="addresses-tab" data-bs-toggle="tab" role="tab" aria-controls="addresses-pane" aria-selected="false">Addresses</a>
              <a href="#banks-pane" class="list-group-item list-group-item-action" id="banks-tab" data-bs-toggle="tab" role="tab" aria-controls="banks-pane" aria-selected="false">Banks & Cards</a>
              <a href="#password-pane" class="list-group-item list-group-item-action" id="password-tab" data-bs-toggle="tab" role="tab" aria-controls="password-pane" aria-selected="false">Change Password</a>
            </div>
          </div>

          <!-- My Purchase tab -->
          <a href="#purchase-pane" class="list-group-item list-group-item-action" id="purchase-tab" data-bs-toggle="tab" role="tab" aria-controls="purchase-pane" aria-selected="false">
            My Purchase
          </a>
        </div>
      </nav>

        <div class="col-lg-9">
        <?php include '../includes/settings_tab_content.php'; ?>
        </div>
          <!-- My Purchase Tab -->
          <div class="tab-pane fade" id="purchase-pane" role="tabpanel" aria-labelledby="purchase-tab" tabindex="0">
            <h2>My Purchase</h2>
            <p>Purchase history and details here.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/settings.js"></script>
  <script src="../assets/js/map_handler.js"></script>
  <script src="../assets/js/addressModal.js"></script>
    <script src="../assets/js/settingsTabs.js"></script>
    <?php include '../includes/footer.php'; ?>

<script>
  // Preview uploaded image before form submit
  document.getElementById('profile-image-input').addEventListener('change', function(event) {
      const [file] = this.files;
      if (file) {
          const preview = document.getElementById('profileImagePreview');
          preview.src = URL.createObjectURL(file);
      }
  });
</script>

  <script>
    window.initialLatitude = <?php echo $user['latitude'] ?? 'null'; ?>;
    window.initialLongitude = <?php echo $user['longitude'] ?? 'null'; ?>;
  </script>


</body>
</html>
