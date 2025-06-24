<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

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

$allStores = $pdo->query("SELECT * FROM seller_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);
$allSuppliers = $pdo->query("SELECT * FROM supplier_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);

function prioritizeNearby($items, $area)
{
  $nearby = [];
  $others = [];
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

$ingredients = $pdo->query("SELECT * FROM ingredients WHERE is_active = 1 ORDER BY created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

// Name Maps
$supplierMap = $pdo->query("SELECT supplier_id, business_name FROM supplier_applications")->fetchAll(PDO::FETCH_KEY_PAIR);
$sellerMap = $pdo->query("SELECT seller_id, business_name FROM seller_applications")->fetchAll(PDO::FETCH_KEY_PAIR);

$campaigns = $pdo->query("
  SELECT * FROM campaign_requests 
  WHERE status = 'approved' 
    AND start_date <= CURDATE() 
    AND end_date >= CURDATE()
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($campaigns as $camp) {
  // Only log for logged-in users
  if (!empty($user['id'])) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO campaign_reach (campaign_id, user_id, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$camp['campaign_id'], $user['id'], $_SERVER['REMOTE_ADDR']]);
  }
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

</head>

<body>
  <?php include '../includes/nav/navbar_router.php'; ?>
  <?php include '../includes/nav/chat.php'; ?>
  <?php include '../includes/offcanvas.php'; ?>
  <?php include '../modals/map_modal.php'; ?>



  <?php if (!empty($campaigns)): ?>
    <div id="campaignCarousel" class="carousel slide mb-5"
      data-bs-ride="carousel"
      data-bs-interval="3000"
      data-bs-pause="false">
      <div class="carousel-inner">
        <?php foreach ($campaigns as $index => $camp): ?>
          <?php
          $storeLink = '#';
          if ($camp['user_type'] === 'seller') {
            $stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
            $stmt->execute([$camp['user_id']]);
            $seller = $stmt->fetch();
            if ($seller) {
              $storeLink = "seller_store.php?seller_id=" . $seller['seller_id'];
            }
          } elseif ($camp['user_type'] === 'supplier') {
            $stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
            $stmt->execute([$camp['user_id']]);
            $supplier = $stmt->fetch();
            if ($supplier) {
              $storeLink = "supplier_store.php?supplier_id=" . $supplier['supplier_id'];
            }
          }
          ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <a href="../backend/track_campaign_click.php?campaign_id=<?= $camp['campaign_id'] ?>">
              <img src="../<?= htmlspecialchars($camp['banner_image']) ?>"
                class="d-block mx-auto img-fluid rounded shadow"
                style="max-width: 1000px; max-height: 500px; width: 100%; object-fit: cover; object-position: center;"
                alt="<?= htmlspecialchars($camp['title']) ?>">
            </a>

            <?php if (!empty($camp['title'])): ?>
              <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded px-3 py-2">
                <h5><?= htmlspecialchars($camp['title']) ?></h5>
                <?php if (!empty($camp['description'])): ?>
                  <p><?= htmlspecialchars($camp['description']) ?></p>
                <?php endif; ?>
              </div>
            <?php endif; ?>
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
  <?php endif; ?>

  <!-- Add after navbar_router.php -->
  <!-- Floating Map Button -->
  <button class="btn btn-primary rounded-circle shadow position-fixed"
    style="bottom: 175px; right: 30px; width: 60px; height: 60px; z-index: 1050;"
    data-bs-toggle="modal" data-bs-target="#mapModal"
    title="View Map">
    <i class="fa fa-map-marker-alt"></i>
  </button>







  <!-- Marketplace – Products -->
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
        <div class="col">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
            <div class="card-body position-relative">
              <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" class="rounded-circle border border-2 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">
              <div class="mt-4 ps-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <h5 class="card-title mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                  <span class="badge <?= $store['store_status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= ucfirst($store['store_status']) ?>
                  </span>
                </div>
                <p class="card-text text-muted small"><?= htmlspecialchars($store['description']) ?></p>
                <a href="seller_store.php?seller_id=<?= $store['seller_id'] ?>" class="btn btn-sm btn-success mt-2">View Store</a>
              </div>
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
        <div class="col">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
            <div class="card-body position-relative">
              <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" class="rounded-circle border border-2 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">
              <div class="mt-4 ps-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <h5 class="card-title mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                  <span class="badge <?= $store['store_status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= ucfirst($store['store_status']) ?>
                  </span>
                </div>
                <p class="card-text text-muted small"><?= htmlspecialchars($store['description']) ?></p>
                <a href="supplier_store.php?supplier_id=<?= $store['supplier_id'] ?>" class="btn btn-sm btn-success mt-2">View Supplier</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>

</html>