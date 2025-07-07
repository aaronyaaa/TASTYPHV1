<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) die("Access denied.");

$defaultIngredientImage = "../assets/images/default-ingredient.jpg";

// Fetch ingredients inventory
$sql1 = "SELECT 
    ii.inventory_id,
    ii.quantity AS inv_quantity,
    ii.quantity_value AS inv_quantity_value,
    ii.unit_type,
    ii.supplier_id,
    ii.variant_id,
    ii.user_id,
    i.ingredient_id,
    i.ingredient_name,
    i.image_url,
    i.quantity_value AS base_quantity_value,
    iv.quantity_value AS variant_quantity_value
FROM ingredients_inventory ii
JOIN ingredients i ON ii.ingredient_id = i.ingredient_id
LEFT JOIN ingredient_variants iv ON ii.variant_id = iv.variant_id
WHERE ii.user_id = :userId";

$stmt1 = $pdo->prepare($sql1);
$stmt1->execute(['userId' => $userId]);
$ingredients = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Fetch kitchen inventory
$sql2 = "SELECT * FROM kitchen_inventory WHERE user_id = :userId";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute(['userId' => $userId]);
$kitchen = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
    <!-- Tabs Header -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ingredients-tab" data-bs-toggle="tab" data-bs-target="#ingredients" type="button" role="tab" aria-controls="ingredients" aria-selected="true">
                <i class="fas fa-boxes-stacked me-1"></i> Ingredients Inventory
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="kitchen-tab" data-bs-toggle="tab" data-bs-target="#kitchen" type="button" role="tab" aria-controls="kitchen" aria-selected="false">
                <i class="fas fa-kitchen-set me-1"></i> Kitchen Inventory
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="inventoryTabsContent">
        <!-- Ingredients Inventory Tab -->
        <div class="tab-pane fade show active" id="ingredients" role="tabpanel" aria-labelledby="ingredients-tab">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <i class="fas fa-boxes-stacked me-2 text-primary fs-4"></i>
                    <h5 class="mb-0 fw-bold">Ingredients Inventory</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="../backend/move_to_kitchen.php" id="moveToKitchenForm">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Stock</th>
                                        <th>Total Measurement</th>
                                        <th>Move Qty <i class="fas fa-arrow-right-to-bracket text-secondary" title="Move to Kitchen"></i></th>
                                        <th>Expected Remaining</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ingredients as $row):
                                        $unitMeasure = $row['variant_quantity_value'] ?? $row['base_quantity_value'];
                                        $stock = $row['inv_quantity'];
                                        $storedQtyValue = $row['inv_quantity_value'];
                                        $calculatedQtyValue = $stock * $unitMeasure;
                                        $totalMeasurement = max($storedQtyValue, $calculatedQtyValue);
                                    ?>
                                        <tr>
                                            <td>
                                                <img src="<?= !empty($row['image_url']) ? '../' . htmlspecialchars($row['image_url']) : $defaultIngredientImage ?>" class="rounded-circle shadow-sm border" style="width: 56px; height: 56px; object-fit: cover;" alt="Ingredient image">
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <?= htmlspecialchars($row['ingredient_name']) ?>
                                            </td>
                                            <td><span class="badge bg-info-subtle text-info fs-6"><?= number_format($stock, 2) ?></span></td>
                                            <td><span id="total_<?= $row['inventory_id'] ?>" class="fw-semibold text-primary"><?= number_format($totalMeasurement, 2) ?></span> <span class="text-muted small"><?= $row['unit_type'] ?></span></td>
                                            <td style="min-width:120px;">
                                                <input type="number" name="move_qty[<?= $row['inventory_id'] ?>]" class="form-control move-input" step="0.01" min="0" max="<?= $totalMeasurement ?>" data-total="<?= $totalMeasurement ?>" data-target="remain_<?= $row['inventory_id'] ?>" placeholder="0" title="Enter quantity to move">
                                            </td>
                                            <td><span id="remain_<?= $row['inventory_id'] ?>" class="fw-semibold text-success"><?= number_format($totalMeasurement, 2) ?></span> <span class="text-muted small"><?= $row['unit_type'] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-bold shadow-sm">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="moveSpinner" role="status" aria-hidden="true"></span>
                                <i class="fas fa-arrow-right-to-bracket me-1"></i> Move to Kitchen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Kitchen Inventory Tab -->
        <div class="tab-pane fade" id="kitchen" role="tabpanel" aria-labelledby="kitchen-tab">
            <?php include('kitchen_inventory.php'); ?>
        </div>
    </div>
</div>

<style>
    .table-striped > tbody > tr:nth-of-type(odd) {
        --bs-table-accent-bg: #f8fafc;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f9;
    }
    .move-input:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255,193,7,.15);
    }
    .card-header {
        border-bottom: 1px solid #f1f1f1;
    }
    .badge.bg-info-subtle {
        background: #e7f3fe;
        color: #0d6efd;
        font-weight: 600;
    }
    .fw-semibold { font-weight: 600; }
    .fw-bold { font-weight: 700; }
    .shadow-sm { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.05)!important; }
    .rounded-circle { border-radius: 50%!important; }
    @media (max-width: 576px) {
        .table-responsive { font-size: 0.95rem; }
        .card-header h5 { font-size: 1.1rem; }
    }
</style>

<script>
    // Update expected remaining on input
    document.querySelectorAll('.move-input').forEach(input => {
        input.addEventListener('input', function() {
            const total = parseFloat(this.dataset.total);
            const moveQty = parseFloat(this.value) || 0;
            const remaining = Math.max(total - moveQty, 0).toFixed(2);
            document.getElementById(this.dataset.target).textContent = remaining;
        });
    });

    // Show spinner on submit
    document.getElementById('moveToKitchenForm').addEventListener('submit', function(e) {
        document.getElementById('moveSpinner').classList.remove('d-none');
    });
</script>