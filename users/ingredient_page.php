<?php require_once '../backend/ingredient_page_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ingredient['ingredient_name']) ?> | Ingredient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/ingredient_page.css">
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include '../includes/nav/navbar_router.php'; ?>

    <div class="container py-4">
        <div class="row">
            <!-- Image Display -->
            <div class="col-md-6">
                <img
                    id="main-image"
                    src="../<?= htmlspecialchars($ingredient['image_url']) ?>"
                    class="img-fluid mb-3 border main-preview"
                    alt="<?= htmlspecialchars($ingredient['ingredient_name']) ?>"
                    data-ingredient-id="<?= htmlspecialchars($ingredient['ingredient_id']) ?>"
                    data-price="<?= htmlspecialchars($ingredient['price']) ?>">

                <!-- Variant Thumbnails -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5>Available Variants</h5>
                        <div class="d-flex flex-wrap">
                            <img src="../<?= htmlspecialchars($ingredient['image_url']) ?>"
                                class="img-thumbnail m-1 variant-image"
                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                data-name="<?= htmlspecialchars($ingredient['ingredient_name']) ?>"
                                data-price="<?= htmlspecialchars($ingredient['price']) ?>"
                                data-stock="<?= htmlspecialchars($ingredient['stock']) ?>"
                                data-unit="<?= htmlspecialchars($ingredient['unit_type']) ?>"
                                data-description="<?= htmlspecialchars($ingredient['description']) ?>"
                                data-quantity="<?= htmlspecialchars($ingredient['quantity_value']) ?>"
                                data-image="../<?= htmlspecialchars($ingredient['image_url']) ?>"
                                data-ingredient-id="<?= htmlspecialchars($ingredient['ingredient_id']) ?>"
                                data-variant-id=""
                                alt="Main Ingredient">


                            <?php foreach ($variants as $variant): ?>
                                <img src="../<?= htmlspecialchars($variant['image_url']) ?>"
                                    class="img-thumbnail m-1 variant-image"
                                    style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                    data-name="<?= htmlspecialchars($variant['variant_name']) ?>"
                                    data-price="<?= htmlspecialchars($variant['price']) ?>"
                                    data-stock="<?= htmlspecialchars($variant['stock']) ?>"
                                    data-unit="<?= htmlspecialchars($variant['unit_type']) ?>"
                                    data-description="<?= htmlspecialchars($variant['description'] ?? $ingredient['description']) ?>"
                                    data-quantity="<?= htmlspecialchars($variant['quantity_value']) ?>"
                                    data-image="../<?= htmlspecialchars($variant['image_url']) ?>"
                                    data-ingredient-id="<?= htmlspecialchars($variant['ingredient_id']) ?>"
                                    data-variant-id="<?= htmlspecialchars($variant['variant_id']) ?>"
                                    alt="<?= htmlspecialchars($variant['variant_name']) ?>">
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <span class="me-2">Share:</span>
                    <a href="#" class="text-primary me-2"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-info me-2"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="text-danger me-2"><i class="fab fa-pinterest fa-2x"></i></a>
                    <a href="#" class="text-primary me-2"><i class="fab fa-facebook-messenger fa-2x"></i></a>
                </div>
            </div>

            <!-- Ingredient Info Panel -->
            <div class="col-md-6">
                <h1 id="ingredient-name" class="mb-3"><?= htmlspecialchars($ingredient['ingredient_name']) ?></h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="me-3">4.8 Ratings</span>
                    <span class="me-3">126 Sold</span>
                    <a href="#" class="text-decoration-none text-muted">Report</a>
                </div>

                <div class="bg-light p-3 mb-3 d-flex align-items-baseline">
                    <span id="ingredient-price" class="text-danger fs-3 me-2">₱<?= number_format($ingredient['price'], 2) ?></span>
                    <span class="text-muted text-decoration-line-through">₱<?= number_format($ingredient['price'] * 1.2, 2) ?></span>
                </div>

                <!-- Shop Vouchers -->
                <div class="mb-3">
                    <h6 class="d-inline me-3">Shop Vouchers</h6>
                    <span class="badge bg-warning text-dark me-1">₱11 OFF</span>
                    <span class="badge bg-warning text-dark me-1">₱50 OFF</span>
                    <span class="badge bg-warning text-dark me-1">₱66 OFF</span>
                    <a href="#" class="text-decoration-none">Show All <i class="fas fa-chevron-down"></i></a>
                </div>

                <!-- Shipping Fee -->
                <div class="mb-3 d-flex align-items-center">
                    <h6 class="me-3">Shipping Fee</h6>
                    <span class="me-3">₱0 - ₱25</span>
                    <a href="#" class="text-decoration-none">Change <i class="fas fa-chevron-down"></i></a>
                </div>

                <!-- Shopping Guarantee -->
                <div class="mb-3 d-flex align-items-center">
                    <h6 class="me-3">Shopping Guarantee</h6>
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span>Free & Easy Returns - Merchandise Protection</span>
                </div>

                <!-- Stock and Quantity Info -->
                <div class="mb-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="mb-1">Stock</h6>
                            <p id="stock-display" class="mb-1"><?= htmlspecialchars($ingredient['stock']) ?> units available</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="mb-1">Quantity Value</h6>
                            <p id="quantity-value-display" class="mb-1"><?= htmlspecialchars($ingredient['quantity_value']) ?> <?= htmlspecialchars($ingredient['unit_type']) ?></p>
                        </div>
                    </div>
                </div>



                <!-- Quantity Selector -->
                <div class="mb-3">
                    <h6 class="mb-2">Quantity</h6>
                    <div class="input-group input-group-sm w-50">
                        <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">-</button>
                        <input type="text" class="form-control text-center" value="1" id="quantity-input">
                        <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                    </div>

                </div>

                <!-- Shipping Information -->
                <div class="mb-3 d-flex align-items-start">
                    <i class="fas fa-truck text-muted me-2 mt-1"></i>
                    <div>
                        <p class="mb-1">
                            Shipping from <strong><?= htmlspecialchars($supplier['store_address']) ?></strong><br>
                            to <strong><?= htmlspecialchars($user['full_address']) ?></strong>
                        </p>
                        <div id="map"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex">
                    <button id="add-to-cart-btn" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        <span id="cart-loading-spinner" class="spinner-border spinner-border-sm ms-2 text-light" style="display: none;"></span>

                    </button>
                </div>
            </div>
        </div>
        <hr class="my-4">

        <!-- Product Description -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-3">Product Description</h2>
                <p id="full-description"><?= htmlspecialchars($ingredient['description']) ?></p>


            </div>
        </div>
        <!-- Supplier Info -->
        <?php if (!empty($storeInfo)): ?>
            <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <img src="../<?= htmlspecialchars($storeInfo['profile_pics']) ?>" alt="Store Logo"
                        class="rounded-circle me-3"
                        style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                        <h5 class="mb-1"><?= htmlspecialchars($storeInfo['business_name']) ?></h5>
                        <div class="text-muted small">Joined <?= $storeInfo['joined_ago'] ?></div>
                    </div>
                </div>

                <div class="text-end me-3">
                    <div><strong><?= $storeInfo['product_count'] ?></strong> Products</div>
                    <div><strong>0</strong> Ratings</div> <!-- Replace with real rating if available -->
                </div>

                <a href="supplier_store.php?supplier_id=<?= $storeInfo['supplier_id'] ?>" class="btn btn-outline-primary">
                    View Store
                </a>
            </div>
        <?php endif; ?>


    </div>

    <footer class="bg-light py-4 mt-4">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> TASTYPHV1. All rights reserved.</p>
        </div>
    </footer>

    <script src="../assets/js/ingredient_page.js"></script>
    <script src="../assets/js/ingredient_map.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.7.0/dist/leaflet.polylineDecorator.min.js"></script>
    <script src="https://unpkg.com/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.js"></script>
    <script src="../assets/js/ingredient_map.js"></script>
    <script>
        initIngredientMap(
            <?= json_encode((float)$supplier['latitude']) ?>,
            <?= json_encode((float)$supplier['longitude']) ?>,
            <?= json_encode((float)$user['latitude']) ?>,
            <?= json_encode((float)$user['longitude']) ?>,
            <?= json_encode($supplier['store_address']) ?>,
            <?= json_encode($user['full_address']) ?>
        );
    </script>





</body>

</html>