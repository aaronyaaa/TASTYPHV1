<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) die("Access denied.");

$defaultIngredientImage = "../assets/images/default-ingredient.jpg";

// Fetch inventory data
$sql = "SELECT 
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

$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ✅ HTML Display -->
<div class="tab-pane fade show active" id="inventory" role="tabpanel">
    <h2>Ingredients Inventory</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="../backend/move_to_kitchen.php">
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
                <?php if ($ingredients): ?>
                    <?php foreach ($ingredients as $row): ?>
                        <?php
                        $unitMeasure = $row['variant_quantity_value'] ?? $row['base_quantity_value'];
                        $stock = $row['inv_quantity'];
                        $storedQtyValue = $row['inv_quantity_value'];

                        // 💡 True Total Measurement: use stored quantity_value if it's > calculated
                        $calculatedQtyValue = $stock * $unitMeasure;
                        $totalMeasurement = max($storedQtyValue, $calculatedQtyValue);
                        ?>
                        <tr>
                            <td>
                                <img src="<?= !empty($row['image_url']) ? '../' . htmlspecialchars($row['image_url']) : $defaultIngredientImage ?>"
                                    class="img-thumbnail" style="width: 50px; height: 50px;">
                            </td>
                            <td><?= htmlspecialchars($row['ingredient_name']) ?></td>
                            <td><?= number_format($stock, 2) ?></td>
                            <td>
                                <span id="total_<?= $row['inventory_id'] ?>"><?= number_format($totalMeasurement, 2) ?></span>
                                <?= htmlspecialchars($row['unit_type']) ?>
                            </td>
                            <td>
                                <input type="number"
                                    name="move_qty[<?= $row['inventory_id'] ?>]"
                                    class="form-control move-input"
                                    step="0.01"
                                    min="0"
                                    max="<?= $totalMeasurement ?>"
                                    data-total="<?= $totalMeasurement ?>"
                                    data-target="remain_<?= $row['inventory_id'] ?>"
                                    placeholder="0">
                            </td>
                            <td>
                                <span id="remain_<?= $row['inventory_id'] ?>"><?= number_format($totalMeasurement, 2) ?></span>
                                <?= htmlspecialchars($row['unit_type']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No ingredients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">Move to Kitchen</button>
    </form>
</div>

<!-- ✅ JS: Update expected remaining -->
<script>
    document.querySelectorAll('.move-input').forEach(input => {
        input.addEventListener('input', function() {
            const total = parseFloat(this.dataset.total);
            const moveQty = parseFloat(this.value) || 0;
            const remaining = Math.max(total - moveQty, 0).toFixed(2);
            document.getElementById(this.dataset.target).textContent = remaining;
        });
    });
</script>