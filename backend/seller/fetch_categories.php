<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

// Determine if user is a supplier or seller
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();

$seller_id = null;
if (!$supplier_id) {
  $stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
  $stmt->execute([$userId]);
  $seller_id = $stmt->fetchColumn();
}

if (!$supplier_id && !$seller_id) {
  echo '<div class="text-danger">Unauthorized access.</div>';
  exit;
}

// Fetch categories
if ($supplier_id) {
  $stmt = $pdo->prepare("SELECT * FROM categories WHERE supplier_id = ? ORDER BY created_at DESC");
  $stmt->execute([$supplier_id]);
} else {
  $stmt = $pdo->prepare("SELECT * FROM categories WHERE seller_id = ? ORDER BY created_at DESC");
  $stmt->execute([$seller_id]);
}

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($categories)) {
  echo '<div class="col-12 text-muted">No categories yet.</div>';
  exit;
}

foreach ($categories as $cat):
?>
  <div class="col-md-3 col-sm-6 text-center">
    <div class="d-flex flex-column align-items-center">
      <img src="<?= !empty($cat['image_url']) ? '../' . htmlspecialchars($cat['image_url']) : '../assets/images/default-category.png' ?>"
           alt="<?= htmlspecialchars($cat['name']) ?>"
           class="rounded-circle shadow mb-2 border"
           style="width: 140px; height: 140px; object-fit: cover;">
      <h6 class="fw-semibold mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
      <p class="text-muted small mb-1 px-2"><?= htmlspecialchars($cat['description']) ?></p>
      <span class="badge <?= $cat['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
        <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
      </span>
    </div>
  </div>
<?php endforeach; ?>
