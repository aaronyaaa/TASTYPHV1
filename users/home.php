<?php
include_once("../database/session.php"); // loads $user array from DB and session
include_once("../database/db_connect.php");

// Fetch public and active stores
$stmt = $pdo->prepare("SELECT seller_id, business_name, description, cover_photo, profile_pics, store_status FROM seller_applications WHERE is_public = 1");
$stmt->execute();
$publicStores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT supplier_id, business_name, description, cover_photo, profile_pics, store_status 
    FROM supplier_applications 
    WHERE is_public = 1
");
$stmt->execute();
$publicSuppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TastyPH Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/public_stores.css">

</head>

<body>
    <?php include '../includes/nav/navbar_router.php'; ?>
        <?php include '../includes/nav/chat.php'; ?>



    <section class="public-stores my-5 px-3">
        <h2 class="mb-4">Explore Local Stores</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach ($publicStores as $store): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <!-- Cover Photo -->
                        <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>"
                            class="card-img-top" alt="Store Cover" style="height: 180px; object-fit: cover;">

                        <!-- Store Info -->
                        <div class="card-body position-relative">
                            <!-- Profile Pic -->
                            <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>"
                                alt="Profile" class="rounded-circle border border-2 border-white shadow-sm"
                                style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">

                            <div class="mt-4 ps-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                                    <span class="badge <?= $store['store_status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($store['store_status']) ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted small"><?= htmlspecialchars($store['description']) ?></p>
                                <a href="seller_store.php?seller_id=<?= $store['seller_id'] ?>" class="btn btn-sm btn-primary mt-2">
                                    View Store
                                </a>

                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (count($publicStores) === 0): ?>
                <p class="text-muted">No public stores available right now.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="public-stores my-5 px-3">
    <h2 class="mb-4">Explore Local Suppliers</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach ($publicSuppliers as $store): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <!-- Cover Photo -->
                    <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>"
                        class="card-img-top" alt="Supplier Cover" style="height: 180px; object-fit: cover;">

                    <!-- Supplier Info -->
                    <div class="card-body position-relative">
                        <!-- Profile Pic -->
                        <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>"
                            alt="Profile" class="rounded-circle border border-2 border-white shadow-sm"
                            style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">

                        <div class="mt-4 ps-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                                <span class="badge <?= $store['store_status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($store['store_status']) ?>
                                </span>
                            </div>
                            <p class="card-text text-muted small"><?= htmlspecialchars($store['description']) ?></p>
                            <a href="supplier_store.php?supplier_id=<?= $store['supplier_id'] ?>" class="btn btn-sm btn-success mt-2">
                                View Supplier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (count($publicSuppliers) === 0): ?>
            <p class="text-muted">No public suppliers available right now.</p>
        <?php endif; ?>
    </div>
</section>




    <script src="../assets/js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>