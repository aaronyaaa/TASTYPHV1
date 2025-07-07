<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

// Get user info and match area
$userLat = $user['latitude'] ?? null;
$userLng = $user['longitude'] ?? null;
$userAddress = strtolower($user['full_address'] ?? '');
$locationKeywords = ['toril', 'mintal', 'gensan', 'talomo', 'matina', 'bajada', 'ma-a', 'bunawan'];
$matchedArea = null;
foreach ($locationKeywords as $keyword) {
  if (strpos($userAddress, $keyword) !== false) {
    $matchedArea = $keyword;
    break;
  }
}

// Stores & Suppliers (public only)
$allStores = $pdo->query("SELECT * FROM seller_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);
$allSuppliers = $pdo->query("SELECT * FROM supplier_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);

// Prioritize stores by area
function prioritizeNearby($items, $area) {
  $nearby = $others = [];
  foreach ($items as $item) {
    if ($area && strpos(strtolower($item['full_address']), $area) !== false) {
      $nearby[] = $item;
    } else {
      $others[] = $item;
    }
  }
  return [$nearby, $others];
}
[$nearbyStores, $otherStores] = prioritizeNearby($allStores, $matchedArea);
[$nearbySuppliers, $otherSuppliers] = prioritizeNearby($allSuppliers, $matchedArea);

$products = $pdo->query("
  SELECT p.*,
    (
      SELECT COALESCE(SUM(oi.quantity), 0)
      FROM order_items oi
      JOIN orders o ON o.order_id = oi.order_id
      WHERE oi.product_id = p.product_id AND o.status = 'delivered'
    ) +
    (
      SELECT COALESCE(SUM(pi.quantity), 0)
      FROM product_inventory pi
      WHERE pi.product_id = p.product_id AND pi.activity_type = 'delivery'
    ) +
    (
      SELECT COALESCE(SUM(pol.quantity), 0)
      FROM pre_order_list pol
      WHERE LOWER(pol.product_name) = LOWER(p.product_name) AND pol.status = 'delivered'
    ) AS total_sold
  FROM products p
  WHERE p.is_active = 1
  ORDER BY p.created_at DESC
  LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);

// Latest Ingredients (no pre-order integration assumed)
$ingredients = $pdo->query("
  SELECT i.*, 
    (
      SELECT COALESCE(SUM(oi.quantity), 0)
      FROM order_items oi
      JOIN orders o ON o.order_id = oi.order_id AND o.status = 'delivered'
      WHERE oi.ingredient_id = i.ingredient_id
    ) AS total_sold
  FROM ingredients i
  WHERE i.is_active = 1
  ORDER BY i.created_at DESC
  LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);

// Name lookup maps
$supplierMap = $pdo->query("SELECT supplier_id, business_name FROM supplier_applications")->fetchAll(PDO::FETCH_KEY_PAIR);
$sellerMap = $pdo->query("SELECT seller_id, business_name FROM seller_applications")->fetchAll(PDO::FETCH_KEY_PAIR);

// Campaigns
$campaigns = $pdo->query("
  SELECT * FROM campaign_requests 
  WHERE status = 'approved' 
    AND start_date <= CURDATE() 
    AND end_date >= CURDATE()
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Track campaign reach
foreach ($campaigns as $camp) {
  if (!empty($user['id'])) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO campaign_reach (campaign_id, user_id, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$camp['campaign_id'], $user['id'], $_SERVER['REMOTE_ADDR']]);
  }
}

// Top Selling Products (orders + pre_orders)
$topProducts = $pdo->query("
  SELECT 
    p.product_id, p.product_name, p.image_url, p.price, p.seller_id,
    (
      SELECT COALESCE(SUM(oi.quantity), 0)
      FROM order_items oi
      JOIN orders o ON o.order_id = oi.order_id
      WHERE oi.product_id = p.product_id AND o.status = 'delivered'
    ) +
    (
      SELECT COALESCE(SUM(pi.quantity), 0)
      FROM product_inventory pi
      WHERE pi.product_id = p.product_id AND pi.activity_type = 'delivery'
    ) +
    (
      SELECT COALESCE(SUM(pol.quantity), 0)
      FROM pre_order_list pol
      WHERE LOWER(pol.product_name) = LOWER(p.product_name) AND pol.status = 'delivered'
    ) AS total_sold
  FROM products p
  WHERE p.is_active = 1
  GROUP BY p.product_id
  HAVING total_sold > 0
  ORDER BY total_sold DESC
")->fetchAll(PDO::FETCH_ASSOC);

$topProducts = array_filter($topProducts, fn($p) => (int)$p['total_sold'] > 0);
$topProducts = array_values($topProducts);

// Top Ingredients
$topIngredients = $pdo->query("
  SELECT i.ingredient_id, i.ingredient_name, i.image_url, i.price, i.supplier_id,
         COALESCE(SUM(oi.quantity), 0) AS total_sold
  FROM ingredients i
  LEFT JOIN order_items oi ON oi.ingredient_id = i.ingredient_id
  LEFT JOIN orders o ON o.order_id = oi.order_id AND o.status = 'delivered'
  GROUP BY i.ingredient_id
  ORDER BY total_sold DESC
")->fetchAll(PDO::FETCH_ASSOC);

$topIngredients = array_filter($topIngredients, fn($i) => (int)$i['total_sold'] > 0);
$topIngredients = array_values($topIngredients);

// Add haversineDistance function for distance calculation
function haversineDistance(
  $lat1, $lon1, $lat2, $lon2, $earthRadius = 6371
) {
  if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) return null;
  $lat1 = deg2rad($lat1);
  $lon1 = deg2rad($lon1);
  $lat2 = deg2rad($lat2);
  $lon2 = deg2rad($lon2);
  $dlat = $lat2 - $lat1;
  $dlon = $lon2 - $lon1;
  $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
  $c = 2 * atan2(sqrt($a), sqrt(1-$a));
  return $earthRadius * $c;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TastyPH Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/public_stores.css">
  <link rel="stylesheet" href="../assets/css/marketplace.css">
  <link rel="stylesheet" href="../assets/css/campaigns.css">
  <link rel="stylesheet" href="../assets/css/best.css">

</head>

<body>
  <?php include '../includes/nav/navbar_router.php'; ?>
  <?php include '../includes/nav/chat.php'; ?>
  <?php include '../includes/offcanvas.php'; ?>
  <?php include '../modals/map_modal.php'; ?>

  <!-- Carousel with modern container and shadow -->
  <?php if (!empty($campaigns)): ?>
    <div class="container-xl px-0 mb-5">
      <div class="overflow-hidden rounded-4 shadow" style="margin-left: auto; margin-right: auto;">
        <div id="campaignCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500" data-bs-pause="false">
          <div class="carousel-inner">
            <?php foreach ($campaigns as $index => $camp): ?>
              <?php
              $storeLink = '#';
              $exploreLabel = 'Explore';
              if ($camp['user_type'] === 'seller') {
                $stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
                $stmt->execute([$camp['user_id']]);
                $seller = $stmt->fetch();
                if ($seller) {
                  $storeLink = "seller_store.php?seller_id=" . $seller['seller_id'];
                  $exploreLabel = 'Visit Store';
                }
              } elseif ($camp['user_type'] === 'supplier') {
                $stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
                $stmt->execute([$camp['user_id']]);
                $supplier = $stmt->fetch();
                if ($supplier) {
                  $storeLink = "supplier_store.php?supplier_id=" . $supplier['supplier_id'];
                  $exploreLabel = 'Visit Supplier';
                }
              }
              ?>
              <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> position-relative">
                <img src="../<?= htmlspecialchars($camp['banner_image']) ?>"
                  class="d-block mx-auto img-fluid w-100"
                  style="max-height: 420px; object-fit: cover; object-position: center; width: 100vw;"
                  alt="<?= htmlspecialchars($camp['title']) ?>">
                <!-- Overlay -->
                <div class="carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                  <div class="text-center text-white px-3 px-md-5">
                    <h2 class="fw-bold mb-2" style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                      <?= htmlspecialchars($camp['title']) ?>
                    </h2>
                    <?php if (!empty($camp['description'])): ?>
                      <p class="lead mb-4" style="text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                        <?= htmlspecialchars($camp['description']) ?>
                      </p>
                    <?php endif; ?>
                    <a href="<?= $storeLink ?>" class="btn btn-warning btn-lg px-5 fw-bold shadow explore-btn">
                      <?= $exploreLabel ?>
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#campaignCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#campaignCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Add after navbar_router.php -->
  <!-- Floating Map Button -->
  <button class="btn btn-primary rounded-circle shadow position-fixed"
    style="bottom: 175px; right: 30px; width: 60px; height: 60px; z-index: 1050;"
    data-bs-toggle="modal" data-bs-target="#mapModal"
    title="View Map">
    <i class="fa fa-map-marker-alt"></i>
  </button>

  <!-- Main content container for margin and spacing -->
  <div class="container-xl px-3 px-md-4">
    <section class="public-stores my-5 px-3">
      <h2 class="mb-4">🔥 Best Sellers</h2>

      <!-- Top Selling Products -->
      <h4 class="mb-3">Top Selling Products</h4>
      <div class="best-sale-scroll">
        <?php foreach ($topProducts as $index => $product): ?>
          <?php
          $rankClass = match ($index) {
            0 => 'top-rank-1',
            1 => 'top-rank-2',
            2 => 'top-rank-3',
            default => ''
          };
          ?>
          <div class="best-product-card <?= $rankClass ?>">
            <a href="../users/product_page.php?product_id=<?= $product['product_id'] ?>" class="text-decoration-none text-dark">
              <div class="card shadow-sm border-0 position-relative">
                <?php if ($index < 3): ?>
                  <div class="circle-rank-badge rank-<?= $index + 1 ?>">
                    <?= $index + 1 ?>
                  </div>
                <?php endif; ?>
                <div class="image-wrapper">
                  <img src="<?= !empty($product['image_url']) ? '../' . htmlspecialchars($product['image_url']) : '../assets/images/default-category.png' ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                </div>
                <div class="card-body">
                  <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($product['product_name']) ?></h6>
                  <small class="text-muted d-block mb-1">By <?= htmlspecialchars($sellerMap[$product['seller_id']] ?? 'Unknown Seller') ?></small>
                  <span class="text-success fw-bold">₱<?= number_format($product['price'], 2) ?></span><br>
                  <small class="text-muted">Sold: <?= $product['total_sold'] ?></small>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Top Selling Ingredients -->
      <h4 class="mb-3 mt-4">Top Selling Ingredients</h4>
      <div class="best-sale-scroll">
        <?php foreach ($topIngredients as $index => $ingredient): ?>
          <?php
          $rankClass = match ($index) {
            0 => 'top-rank-1',
            1 => 'top-rank-2',
            2 => 'top-rank-3',
            default => ''
          };
          ?>
          <div class="best-ingredient-card <?= $rankClass ?>">
            <a href="../users/ingredient_page.php?ingredient_id=<?= $ingredient['ingredient_id'] ?>" class="text-decoration-none text-dark">
              <div class="card shadow-sm border-0 position-relative">
                <?php if ($index < 3): ?>
                  <div class="circle-rank-badge rank-<?= $index + 1 ?>">
                    <?= $index + 1 ?>
                  </div>
                <?php endif; ?>
                <div class="image-wrapper">
                  <img src="<?= !empty($ingredient['image_url']) ? '../' . htmlspecialchars($ingredient['image_url']) : '../assets/images/default-category.png' ?>" alt="<?= htmlspecialchars($ingredient['ingredient_name']) ?>">
                </div>
                <div class="card-body">
                  <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($ingredient['ingredient_name']) ?></h6>
                  <small class="text-muted d-block mb-1">From <?= htmlspecialchars($supplierMap[$ingredient['supplier_id']] ?? 'Unknown Supplier') ?></small>
                  <span class="text-success fw-bold">₱<?= number_format($ingredient['price'], 2) ?></span><br>
                  <small class="text-muted">Sold: <?= $ingredient['total_sold'] ?></small>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>


    <!-- Marketplace – Homemade Products -->
    <section class="public-stores my-5 px-3">
      <h2 class="mb-4">Marketplace – Homemade Products</h2>
      <?php if (empty($products)): ?>
        <p class="text-muted">No products available.</p>
      <?php endif; ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
        <?php foreach ($products as $product): ?>
          <div class="col">
            <a href="../users/product_page.php?product_id=<?= $product['product_id'] ?>" class="text-decoration-none text-dark">
              <div class="ingredient-card-1">
                <img src="<?= !empty($product['image_url']) ? '../' . htmlspecialchars($product['image_url']) : '../assets/images/default-category.png' ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="ingredient-image-1">
                <div class="card-body">
                  <h5 class="ingredient-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                  <p class="ingredient-description">Product by <?= htmlspecialchars($sellerMap[$product['seller_id']] ?? 'Unknown Seller') ?></p>
                  <p class="price-tag mb-0">₱<?= number_format($product['price'], 2) ?></p>
                  <small class="text-muted">Sold: <?= $product['total_sold'] ?? 0 ?></small>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Public Stores -->
    <section class="public-stores my-5 px-3">
      <h2 class="mb-4">Explore Local Stores in <?= ucfirst($matchedArea ?? 'your area') ?></h2>
      <?php if (count($nearbyStores) === 0): ?>
        <p class="text-muted">No public stores available in your area right now.</p>
      <?php endif; ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach (array_merge($nearbyStores, $otherStores) as $store): ?>
          <?php
          $sellerId = $store['seller_id'];
          $productCount = $pdo->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerId AND is_active = 1")->fetchColumn();
          $avgRating = $pdo->query("SELECT AVG(rating) FROM products WHERE seller_id = $sellerId AND is_active = 1")->fetchColumn();
          $avgRating = $avgRating ? number_format($avgRating, 1) : '0.0';
          $distance = null;
          if ($userLat && $userLng && $store['latitude'] && $store['longitude']) {
              $distance = haversineDistance($userLat, $userLng, $store['latitude'], $store['longitude']);
          }
          ?>
          <div class="col">
            <div class="store-card shadow-sm rounded-4 bg-white position-relative h-100 p-0">
              <div class="store-cover position-relative rounded-top-4" style="height: 140px; overflow: hidden;">
                <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="w-100 h-100 object-fit-cover" alt="cover">
                <?php if ($distance !== null): ?>
                  <span class="position-absolute top-0 end-0 m-2 badge bg-light text-dark shadow-sm fs-6"><i class="fa fa-location-arrow me-1"></i><?= number_format($distance, 1) ?> km</span>
                <?php endif; ?>
              </div>
              <div class="store-profile position-absolute" style="top: 90px; left: 24px;">
                <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" class="rounded-circle border border-3 border-white shadow" style="width: 80px; height: 80px; object-fit: cover;">
              </div>
              <div class="card-body pt-5 mt-2 px-3 pb-3">
                <div class="d-flex align-items-center mb-1">
                  <h5 class="fw-bold mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                  <?php if ($store['store_status'] === 'active' && $store['is_public']): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-2"><i class="fa fa-check-circle"></i> Verified</span>
                  <?php endif; ?>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span class="text-warning me-1"><i class="fa fa-star"></i></span>
                  <span class="fw-semibold"><?= $avgRating ?></span>
                  <span class="text-muted ms-2">(<?= $productCount ?> products)</span>
                </div>
                <div class="mb-2 text-muted small"><i class="fa fa-map-marker-alt me-1"></i><?= htmlspecialchars($store['full_address']) ?></div>
                <a href="seller_store.php?seller_id=<?= $store['seller_id'] ?>" class="btn btn-visit-store w-100 fw-bold rounded-pill mt-2">Visit Store</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Marketplace – Ingredients -->
    <section class="public-stores my-5 px-3">
      <h2 class="mb-4">Marketplace – Ingredients</h2>
      <?php if (empty($ingredients)): ?>
        <p class="text-muted">No ingredients available.</p>
      <?php endif; ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
        <?php foreach ($ingredients as $ingredient): ?>
          <div class="col">
            <a href="../users/ingredient_page.php?ingredient_id=<?= $ingredient['ingredient_id'] ?>" class="text-decoration-none text-dark">
              <div class="ingredient-card-1">
                <img src="<?= !empty($ingredient['image_url']) ? '../' . htmlspecialchars($ingredient['image_url']) : '../assets/images/default-category.png' ?>" alt="<?= htmlspecialchars($ingredient['ingredient_name']) ?>" class="ingredient-image-1">
                <div class="card-body">
                  <h5 class="ingredient-title"><?= htmlspecialchars($ingredient['ingredient_name']) ?></h5>
                  <p class="ingredient-description">Ingredient from <?= htmlspecialchars($supplierMap[$ingredient['supplier_id']] ?? 'Unknown Supplier') ?></p>
                  <p class="price-tag mb-0">₱<?= number_format($ingredient['price'], 2) ?></p>
                  <small class="text-muted">Sold: <?= $ingredient['total_sold'] ?? 0 ?></small>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>




    <!-- Public Suppliers -->
    <section class="public-stores my-5 px-3">
      <h2 class="mb-4">Explore Local Suppliers in <?= ucfirst($matchedArea ?? 'your area') ?></h2>
      <?php if (count($nearbySuppliers) === 0): ?>
        <p class="text-muted">No public suppliers available in your area right now.</p>
      <?php endif; ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach (array_merge($nearbySuppliers, $otherSuppliers) as $store): ?>
          <?php
          $supplierId = $store['supplier_id'];
          $productCount = $pdo->query("SELECT COUNT(*) FROM ingredients WHERE supplier_id = $supplierId AND is_active = 1")->fetchColumn();
          $avgRating = $pdo->query("SELECT AVG(rating) FROM ingredients WHERE supplier_id = $supplierId AND is_active = 1")->fetchColumn();
          $avgRating = $avgRating ? number_format($avgRating, 1) : '0.0';
          $distance = null;
          if ($userLat && $userLng && $store['latitude'] && $store['longitude']) {
              $distance = haversineDistance($userLat, $userLng, $store['latitude'], $store['longitude']);
          }
          ?>
          <div class="col">
            <div class="store-card shadow-sm rounded-4 bg-white position-relative h-100 p-0">
              <div class="store-cover position-relative rounded-top-4" style="height: 140px; overflow: hidden;">
                <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="w-100 h-100 object-fit-cover" alt="cover">
                <?php if ($distance !== null): ?>
                  <span class="position-absolute top-0 end-0 m-2 badge bg-light text-dark shadow-sm fs-6"><i class="fa fa-location-arrow me-1"></i><?= number_format($distance, 1) ?> km</span>
                <?php endif; ?>
              </div>
              <div class="store-profile position-absolute" style="top: 90px; left: 24px;">
                <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" class="rounded-circle border border-3 border-white shadow" style="width: 80px; height: 80px; object-fit: cover;">
              </div>
              <div class="card-body pt-5 mt-2 px-3 pb-3">
                <div class="d-flex align-items-center mb-1">
                  <h5 class="fw-bold mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                  <?php if ($store['store_status'] === 'active' && $store['is_public']): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-2"><i class="fa fa-check-circle"></i> Verified</span>
                  <?php endif; ?>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span class="text-warning me-1"><i class="fa fa-star"></i></span>
                  <span class="fw-semibold"><?= $avgRating ?></span>
                  <span class="text-muted ms-2">(<?= $productCount ?> products)</span>
                </div>
                <div class="mb-2 text-muted small"><i class="fa fa-map-marker-alt me-1"></i><?= htmlspecialchars($store['full_address']) ?></div>
                <a href="supplier_store.php?supplier_id=<?= $store['supplier_id'] ?>" class="btn btn-visit-supplier w-100 fw-bold rounded-pill mt-2">Visit Supplier</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div> <!-- end main container -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="../assets/js/dashboard.js"></script>
  <?php include '../includes/footer.php'; ?>
</body>

</html>