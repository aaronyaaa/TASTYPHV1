<?php
require_once '../../../database/db_connect.php';
require_once '../../../database/session.php';

header('Content-Type: text/html');

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit('<div class="text-danger">Unauthorized</div>');

$ingredient_id = $_GET['ingredient_id'] ?? null;
if (!$ingredient_id) exit('<div class="text-muted">No variants available.</div>');

$stmt = $pdo->prepare("SELECT * FROM ingredient_variants WHERE ingredient_id = ? ORDER BY created_at DESC");
$stmt->execute([$ingredient_id]);
$variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($variants)) {
  echo '<div class="text-muted">No variants available for this ingredient.</div>';
  exit;
}

foreach ($variants as $v):
?>
  <div class="col-md-3 col-sm-6">
    <div class="card h-100 shadow-sm border-0">
      <img src="<?= !empty($v['image_url']) ? '../' . htmlspecialchars($v['image_url']) : '../assets/images/default-category.png' ?>"
           alt="<?= htmlspecialchars($v['variant_name']) ?>"
           class="card-img-top" style="height: 160px; object-fit: cover;">

      <div class="card-body d-flex flex-column">
        <h6 class="card-title"><?= htmlspecialchars($v['variant_name']) ?></h6>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-success fw-bold">₱<?= number_format($v['price'], 2) ?></span>
          <span class="badge bg-success"><?= $v['stock'] ?> in stock</span>
        </div>
        <p class="text-muted small mb-2"><i class="fa fa-box"></i> <?= $v['quantity_value'] . ' ' . $v['unit_type'] ?></p>
        <div class="d-flex gap-2 mt-auto">
          <button class="btn btn-warning w-100 btn-sm">
            <i class="fa fa-pen"></i> Edit
          </button>
          <button class="btn btn-danger btn-sm">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
