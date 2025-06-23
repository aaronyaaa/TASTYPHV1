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

$stmt = $pdo->query("SELECT * FROM seller_applications WHERE is_public = 1");
$allStores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM supplier_applications WHERE is_public = 1");
$allSuppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TastyPH Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-search/dist/leaflet-search.min.css" />
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/public_stores.css">
        <link rel="stylesheet" href="../assets/css/map_popup.css">



</head>

<body>
    <?php include '../includes/nav/navbar_router.php'; ?>
    <?php include '../includes/nav/chat.php'; ?>
        <?php include '../includes/offcanvas.php'; ?>


 <button id="toggleMapBtn" class="btn btn-outline-primary mb-3">
  <i class="fa fa-map-marker-alt"></i> View Map of Sellers & Suppliers
</button>

<div id="mapSection" style="display: none;">
  <h3>Map View of Sellers & Suppliers</h3>
  <div id="map" style="height: 600px;"></div>
</div>


    <section class="public-stores my-5 px-3">
        <h2 class="mb-4">Explore Local Stores in <?= ucfirst($matchedArea ?? 'your area') ?></h2>
        <?php if (count($nearbyStores) === 0): ?>
            <p class="text-muted">No public stores available in your area right now.</p>
        <?php endif; ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach (array_merge($nearbyStores, $otherStores) as $store): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="card-img-top" alt="Store Cover" style="height: 180px; object-fit: cover;">
                        <div class="card-body position-relative">
                            <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" alt="Profile" class="rounded-circle border border-2 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">
                            <div class="mt-4 ps-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($store['business_name']) ?></h5>
                                    <span class="badge <?= $store['store_status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($store['store_status']) ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted small"><?= htmlspecialchars($store['description']) ?></p>
                                <a href="seller_store.php?seller_id=<?= $store['seller_id'] ?>" class="btn btn-sm btn-primary mt-2">View Store</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="public-stores my-5 px-3">
        <h2 class="mb-4">Explore Local Suppliers in <?= ucfirst($matchedArea ?? 'your area') ?></h2>
        <?php if (count($nearbySuppliers) === 0): ?>
            <p class="text-muted">No public suppliers available in your area right now.</p>
        <?php endif; ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach (array_merge($nearbySuppliers, $otherSuppliers) as $store): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= !empty($store['cover_photo']) ? '../' . htmlspecialchars($store['cover_photo']) : '../assets/images/default-cover.jpg' ?>" class="card-img-top" alt="Supplier Cover" style="height: 180px; object-fit: cover;">
                        <div class="card-body position-relative">
                            <img src="<?= !empty($store['profile_pics']) ? '../' . htmlspecialchars($store['profile_pics']) : '../assets/images/default-profile.png' ?>" alt="Profile" class="rounded-circle border border-2 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover; position: absolute; top: -30px; left: 15px;">
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

    <script src="../assets/js/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://unpkg.com/leaflet-control-search/dist/leaflet-search.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
  document.getElementById('toggleMapBtn').addEventListener('click', () => {
    const section = document.getElementById('mapSection');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';

    if (section.style.display === 'block') {
      setTimeout(() => {
        map.invalidateSize(); // Make sure map renders correctly when shown
      }, 200);
    }
  });
</script>
 <script>
const map = L.map('map').setView([<?= json_encode($userLat ?? 13.41) ?>, <?= json_encode($userLng ?? 122.56) ?>], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 18,
}).addTo(map);

const markerLayer = L.layerGroup().addTo(map);

const violetIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

const greenIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

const blueIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

<?php foreach (array_merge($allStores, $allSuppliers) as $store): ?>
  L.marker(
    [<?= $store['latitude'] ?>, <?= $store['longitude'] ?>],
    {
      icon: <?= isset($store['seller_id']) ? 'violetIcon' : 'greenIcon' ?>,
      title: "<?= htmlspecialchars($store['business_name']) ?>"
    }
  ).addTo(markerLayer)
   .bindPopup(`
    <div class="map-popup">
        <img src="../<?= htmlspecialchars($store['cover_photo']) ?>" class="cover" />
        <div class="content">
            <div class="profile">
                <img src="../<?= htmlspecialchars($store['profile_pics']) ?>" />
                <div class="store-name"><?= htmlspecialchars($store['business_name']) ?></div>
            </div>
            <div class="description"><?= htmlspecialchars($store['description']) ?></div>
            <a href="<?= isset($store['seller_id']) ? 'seller_store.php?seller_id=' . $store['seller_id'] : 'supplier_store.php?supplier_id=' . $store['supplier_id'] ?>" class="btn btn-<?= isset($store['seller_id']) ? 'primary' : 'success' ?>">View <?= isset($store['seller_id']) ? 'Store' : 'Supplier' ?></a>
        </div>
    </div>
  `);
<?php endforeach; ?>

// 🔵 Add user marker
const userLat = <?= json_encode($userLat) ?>;
const userLng = <?= json_encode($userLng) ?>;
if (userLat && userLng) {
  L.marker([parseFloat(userLat), parseFloat(userLng)], { icon: blueIcon })
    .addTo(map)
    .bindPopup("📍 Your registered location")
    .openPopup();
}

// 🔍 Add search input inside the map (top right)
const searchBox = L.control({ position: 'topright' });
searchBox.onAdd = function () {
  const div = L.DomUtil.create('div', 'leaflet-bar bg-white p-2 shadow');
  div.innerHTML = `
    <div class="input-group input-group-sm">
      <input type="text" id="mapSearchInput" class="form-control" placeholder="Search store/supplier">
      <button class="btn btn-outline-primary" id="mapSearchBtn"><i class="fa fa-search"></i></button>
    </div>
  `;
  return div;
};
searchBox.addTo(map);

// 🔍 Search functionality
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('mapSearchInput');
  const btn = document.getElementById('mapSearchBtn');

  function runSearch() {
    const query = input.value.trim().toLowerCase();
    let found = false;

    markerLayer.eachLayer(marker => {
      if (marker.options.title && marker.options.title.toLowerCase().includes(query)) {
        map.setView(marker.getLatLng(), 15);
        marker.openPopup();
        found = true;
      }
    });

    if (!found) alert("No matching store/supplier found.");
  }

  btn.addEventListener('click', runSearch);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') runSearch();
  });
});
</script>
</body>

</html>