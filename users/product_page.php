<?php
// Fetch product details
require_once '../database/db_connect.php';
require_once '../database/session.php';

$product_id = $_GET['product_id'] ?? null;
if (!$product_id) {
    die('Product not found.');
}

$stmt = $pdo->prepare('
    SELECT 
        p.*, 
        s.business_name, s.profile_pics, s.cover_photo, 
        s.store_address, s.seller_id, 
        s.latitude, s.longitude,  -- ✅ ADD THESE
        s.user_id as seller_user_id 
    FROM products p 
    JOIN seller_applications s ON p.seller_id = s.seller_id 
    WHERE p.product_id = ?
');
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die('Product not found.');
}

// Optionally fetch variants if you have them
$variants = [];
// ... fetch variants logic if needed ...

// Optionally fetch user info
$user = $_SESSION['user'] ?? null;

// Calculate distance and ETA if possible
function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // km
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}
$distance = null;
$eta = null;
if (!empty($product['latitude']) && !empty($product['longitude']) && !empty($user['latitude']) && !empty($user['longitude'])) {
    $distance = haversineDistance($product['latitude'], $product['longitude'], $user['latitude'], $user['longitude']);
    $eta = round($distance * 1.5); // 1.5 min/km as a rough estimate
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_name']) ?> | Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <?php include '../includes/offcanvas.php'; ?>
    <div class="container py-4">
        <div class="row">
            <!-- Image Display -->
            <div class="col-md-6">
                <img
                    id="main-image"
                    src="../<?= htmlspecialchars($product['image_url']) ?>"
                    class="img-fluid mb-3 border main-preview"
                    alt="<?= htmlspecialchars($product['product_name']) ?>"
                    data-product-id="<?= htmlspecialchars($product['product_id']) ?>"
                    data-price="<?= htmlspecialchars($product['price']) ?>">
                <!-- Variant Thumbnails (if any) -->
                <!--
            <div class="row mt-5">
                <div class="col-12">
                    <h5>Available Variants</h5>
                    <div class="d-flex flex-wrap">
                        ...
                    </div>
                </div>
            </div>
            -->
                <div class="text-center mt-3">
                    <span class="me-2">Share:</span>
                    <a href="#" class="text-primary me-2"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-info me-2"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="text-danger me-2"><i class="fab fa-pinterest fa-2x"></i></a>
                    <a href="#" class="text-primary me-2"><i class="fab fa-facebook-messenger fa-2x"></i></a>
                </div>
            </div>
            <!-- Product Info Panel -->
            <div class="col-md-6">
                <h1 id="product-name" class="mb-3"><?= htmlspecialchars($product['product_name']) ?></h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="me-3">Rating: <?= number_format($product['rating'], 1) ?></span>
                    <span class="me-3">126 Sold</span>
                    <a href="#" class="text-decoration-none text-muted">Report</a>
                </div>
                <div class="bg-light p-3 mb-3 d-flex align-items-baseline">
                    <span id="product-price" class="text-danger fs-3 me-2" data-price="<?= htmlspecialchars($product['price']) ?>">₱<?= number_format($product['price'], 2) ?></span>
                    <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                        <span class="text-muted text-decoration-line-through">₱<?= number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
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
                            <p id="stock-display" class="mb-1"><?= htmlspecialchars($product['stock']) ?> units available</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="mb-1">Quantity Value</h6>
                            <p id="quantity-value-display" class="mb-1"><?= htmlspecialchars($product['quantity_value']) ?> <?= htmlspecialchars($product['unit_type']) ?></p>
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
                            Shipping from <strong><?= htmlspecialchars($product['store_address']) ?></strong><br>
                            <?php if (!empty($user['full_address'])): ?>
                                to <strong><?= htmlspecialchars($user['full_address']) ?></strong>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($product['latitude']) && !empty($product['longitude']) && !empty($user['latitude']) && !empty($user['longitude'])): ?>
                            <div id="map"></div>
                        <?php else: ?>
                            <div class="text-muted">📍 Location data not available for map.</div>
                        <?php endif; ?>
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
                <p id="full-description"><?= htmlspecialchars($product['description']) ?></p>
            </div>
        </div>
        <!-- Seller Info -->
        <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <img src="../<?= htmlspecialchars($product['profile_pics']) ?>" alt="Store Logo"
                    class="rounded-circle me-3"
                    style="width: 70px; height: 70px; object-fit: cover;">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($product['business_name']) ?></h5>
                    <div class="text-muted small">Store Address: <?= htmlspecialchars($product['store_address']) ?></div>
                </div>
            </div>
            <a href="seller_store.php?seller_id=<?= $product['seller_id'] ?>" class="btn btn-outline-primary">
                View Store
            </a>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.7.0/dist/leaflet.polylineDecorator.min.js"></script>
    <script src="https://unpkg.com/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.js"></script>
    <script src="../assets/js/seller_map.js"></script>
    <script>
        <?php if (!empty($product['latitude']) && !empty($product['longitude']) && !empty($user['latitude']) && !empty($user['longitude'])): ?>
            initSellerMap(
                <?= json_encode((float)$product['latitude']) ?>,
                <?= json_encode((float)$product['longitude']) ?>,
                <?= json_encode((float)$user['latitude']) ?>,
                <?= json_encode((float)$user['longitude']) ?>,
                <?= json_encode($product['store_address']) ?>,
                <?= json_encode($user['full_address']) ?>
            );
        <?php endif; ?>
    </script>
    <script src="../assets/js/seller_page.js"></script>
</body>

</html>