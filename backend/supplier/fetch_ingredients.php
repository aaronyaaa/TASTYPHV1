<?php
require_once '../../database/db_connect.php';
session_start();

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

$sort = $_GET['sort'] ?? 'created_at';
$allowedSort = ['ingredient_name', 'price', 'created_at'];
$sort = in_array($sort, $allowedSort) ? $sort : 'created_at';

$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplier_id = $stmt->fetchColumn();
if (!$supplier_id) exit;

$stmt = $pdo->prepare("SELECT * FROM ingredients WHERE supplier_id = ? ORDER BY $sort ASC");
$stmt->execute([$supplier_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($ingredients)) {
    echo '<div class="text-muted text-center py-3">No ingredients found.</div>';
    exit;
}

$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_id'] ?? '';

// Build dynamic SQL
$sql = "SELECT * FROM ingredients WHERE supplier_id = ?";
$params = [$supplier_id];

if ($search !== '') {
    $sql .= " AND ingredient_name LIKE ?";
    $params[] = "%$search%";
}

if (!empty($categoryFilter)) {
    $sql .= " AND category_id = ?";
    $params[] = $categoryFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Sort Selector -->
<div class="d-flex justify-content-end align-items-center mb-3 px-2">
    <label class="me-2 mb-0 small fw-semibold text-muted" for="sortIngredients">Sort by:</label>
    <select id="sortIngredients" class="form-select form-select-sm w-auto">
        <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Latest</option>
        <option value="ingredient_name" <?= $sort === 'ingredient_name' ? 'selected' : '' ?>>Name</option>
        <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Price</option>
    </select>
</div>


<div class="row g-4">
    <?php foreach ($ingredients as $item): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 shadow-sm border-0">
                <!-- Image -->
                <img src="<?= !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../assets/images/default-category.png' ?>"
                    alt="<?= htmlspecialchars($item['ingredient_name']) ?>"
                    class="card-img-top"
                    style="height: 200px; object-fit: contain;">

                <div class="card-body d-flex flex-column">
                    <!-- Name + Description -->
                    <h5 class="card-title mb-1"><?= htmlspecialchars($item['ingredient_name']) ?></h5>
                    <p class="text-muted small mb-2"><?= mb_strimwidth(htmlspecialchars($item['description']), 0, 50, '...') ?></p>

                    <!-- Price + Stock -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-success fw-bold">₱<?= number_format($item['price'], 2) ?></span>
                        <span class="badge bg-success"><?= $item['stock'] ?> in stock</span>
                    </div>

                    <!-- Slug (optional) -->
                    <p class="mb-3 text-muted small">
                        <i class="fa fa-tag me-1"></i> <?= htmlspecialchars($item['slug']) ?>
                    </p>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-auto">
                        <div class="d-flex gap-2 justify-content-between">
                            <button class="btn btn-warning w-100" onclick="editIngredient(<?= $item['ingredient_id'] ?>)">
                                <i class="fa fa-pen me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger w-25" onclick="deleteIngredient(<?= $item['ingredient_id'] ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <div class="d-flex gap-2 mt-2">
                                <button class="btn btn-primary"
                                    onclick="openVariantModal(<?= $item['ingredient_id'] ?>)">
                                    <i class="fa fa-plus"></i> Add Variant
                                </button>

                            </div>
                            <button class="btn btn-info"
                                onclick="viewVariants(<?= $item['ingredient_id'] ?>, '<?= htmlspecialchars($item['ingredient_name'], ENT_QUOTES) ?>')">
                                <i class="fa fa-eye me-1"></i> View Variants
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>