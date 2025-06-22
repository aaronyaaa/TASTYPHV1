<?php
include_once("../database/db_connect.php"); // loads $user array from DB and session
include_once("../database/session.php"); // Loads user into $_SESSION['user']
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Start Selling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


    <link rel="stylesheet" href="../assets/css/user_navbar.css" />
    <link rel="stylesheet" href="../assets/css/start_selling.css">


</head>

<body>

    <?php include '../includes/nav/user_navbar.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4 text-center">Start Selling on TastyPH</h2>

        <div class="row">
            <!-- Sidebar Tabs -->
            <div class="col-md-3 mb-4">
                <div class="list-group" id="sellTab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="seller-tab" data-bs-toggle="list" href="#seller-form" role="tab">
                        <i class="fa-solid fa-store me-2"></i>Apply as Seller
                    </a>
                    <a class="list-group-item list-group-item-action" id="supplier-tab" data-bs-toggle="list" href="#supplier-form" role="tab">
                        <i class="fa-solid fa-truck me-2"></i>Apply as Supplier
                    </a>
                </div>
            </div>

            <!-- Form Panels -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Seller Form -->
                    <div class="tab-pane fade show active" id="seller-form" role="tabpanel">
                        <?php include '../includes/forms/apply_seller_form.php'; ?>
                    </div>

                    <!-- Supplier Form -->
                    <div class="tab-pane fade" id="supplier-form" role="tabpanel">
                        <?php include '../includes/forms/apply_supplier_form.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Leaflet Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="../assets/js/start_selling.js"></script>


</body>

</html>