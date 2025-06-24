<?php
require_once __DIR__ . "/../database/db_connect.php";
require_once __DIR__ . "/../database/session.php";

$userLat = $_SESSION['user']['latitude'] ?? 13.41;
$userLng = $_SESSION['user']['longitude'] ?? 122.56;

// Fetch all public stores and suppliers
$allStores = $pdo->query("SELECT * FROM seller_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);
$allSuppliers = $pdo->query("SELECT * FROM supplier_applications WHERE is_public = 1")->fetchAll(PDO::FETCH_ASSOC);
?>
        <link rel="stylesheet" href="../assets/css/map_popup.css">

<!-- Fullscreen Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel">Map of Sellers & Suppliers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="map" style="width: 100%; height: 100vh;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Leaflet & Map Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-search/dist/leaflet-search.min.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet-control-search/dist/leaflet-search.min.js"></script>

<script>
  const map = L.map('map').setView([<?= json_encode($userLat) ?>, <?= json_encode($userLng) ?>], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

  const markerLayer = L.layerGroup().addTo(map);

  const icons = {
    seller: new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    }),
    supplier: new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    }),
    user: new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    })
  };

  <?php foreach (array_merge($allStores, $allSuppliers) as $store): ?>
    L.marker(
      [<?= $store['latitude'] ?>, <?= $store['longitude'] ?>], {
        icon: <?= isset($store['seller_id']) ? 'icons.seller' : 'icons.supplier' ?>,
        title: "<?= htmlspecialchars($store['business_name']) ?>"
      }
    ).addTo(markerLayer).bindPopup(`
      <div class="map-popup">
        <img src="../<?= htmlspecialchars($store['cover_photo']) ?>" class="cover" />
        <div class="content">
          <div class="profile">
            <img src="../<?= htmlspecialchars($store['profile_pics']) ?>" />
            <div class="store-name"><?= htmlspecialchars($store['business_name']) ?></div>
          </div>
          <div class="description"><?= htmlspecialchars($store['description']) ?></div>
          <a href="<?= isset($store['seller_id']) ? '../users/seller_store.php?seller_id=' . $store['seller_id'] : '../users/supplier_store.php?supplier_id=' . $store['supplier_id'] ?>" class="btn btn-sm btn-<?= isset($store['seller_id']) ? 'primary' : 'success' ?>">
            View <?= isset($store['seller_id']) ? 'Store' : 'Supplier' ?>
          </a>
        </div>
      </div>
    `);
  <?php endforeach; ?>

  if (<?= json_encode($userLat) ?> && <?= json_encode($userLng) ?>) {
    L.marker([<?= $userLat ?>, <?= $userLng ?>], { icon: icons.user })
      .addTo(map)
      .bindPopup("📍 Your registered location")
      .openPopup();
  }

  // Search Control
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

  document.getElementById('mapModal')?.addEventListener('shown.bs.modal', function () {
    setTimeout(() => map.invalidateSize(), 300);
  });
</script>
