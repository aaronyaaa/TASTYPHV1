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
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-boxes-stacked me-2 text-primary"></i> Ingredients Inventory</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="../backend/move_to_kitchen.php">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Total Measurement</th>
                                    <th>Move Qty</th>
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
                                        <td><img src="<?= !empty($row['image_url']) ? '../' . htmlspecialchars($row['image_url']) : $defaultIngredientImage ?>" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                                        <td><?= htmlspecialchars($row['ingredient_name']) ?></td>
                                        <td><?= number_format($stock, 2) ?></td>
                                        <td><span id="total_<?= $row['inventory_id'] ?>"><?= number_format($totalMeasurement, 2) ?></span> <?= $row['unit_type'] ?></td>
                                        <td>
                                            <input type="number" name="move_qty[<?= $row['inventory_id'] ?>]" class="form-control move-input" step="0.01" min="0" max="<?= $totalMeasurement ?>" data-total="<?= $totalMeasurement ?>" data-target="remain_<?= $row['inventory_id'] ?>" placeholder="0">
                                        </td>
                                        <td><span id="remain_<?= $row['inventory_id'] ?>"><?= number_format($totalMeasurement, 2) ?></span> <?= $row['unit_type'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary mt-2">
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


<script>
    function addIngredientRow() {
        const index = document.querySelectorAll('#ingredientsWrapper .row').length;
        const wrapper = document.createElement('div');
        wrapper.className = 'row mb-2';
        wrapper.innerHTML = `
    <div class="col">
      <select name="ingredients[${index}][ingredient_id]" class="form-control">
        <?php foreach ($kitchen as $item): ?>
          <option value="<?= $item['ingredient_id'] ?>"><?= htmlspecialchars($item['ingredient_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col">
      <input type="number" step="0.01" name="ingredients[${index}][quantity]" class="form-control" placeholder="Quantity">
    </div>
    <div class="col">
      <input type="text" name="ingredients[${index}][unit]" class="form-control" placeholder="Unit (e.g. g, pcs)">
    </div>`;
        document.getElementById('ingredientsWrapper').appendChild(wrapper);
    }

    document.querySelectorAll('.move-input').forEach(input => {
        input.addEventListener('input', function() {
            const total = parseFloat(this.dataset.total);
            const moveQty = parseFloat(this.value) || 0;
            const remaining = Math.max(total - moveQty, 0).toFixed(2);
            document.getElementById(this.dataset.target).textContent = remaining;
        });
    });
</script>