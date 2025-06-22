<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['restock'])) {
    foreach ($_POST['restock'] as $ingredientId => $restockQty) {
        $ingredientId = intval($ingredientId);
        $restockQty = floatval($restockQty);
        if ($restockQty <= 0) continue;

        // Fetch base and variant unit
        $stmt = $pdo->prepare("
            SELECT 
                i.ingredient_id,
                i.ingredient_name,
                i.quantity_value AS base_quantity_value,
                iv.variant_id,
                iv.quantity_value AS variant_quantity_value,
                iv.unit_type,
                iv.supplier_id
            FROM ingredients i
            LEFT JOIN ingredient_variants iv ON iv.ingredient_id = i.ingredient_id
            WHERE i.ingredient_id = ? LIMIT 1
        ");
        $stmt->execute([$ingredientId]);
        $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ingredient) continue;

        $unitMeasure = $ingredient['variant_quantity_value'] ?? $ingredient['base_quantity_value'];
        $unitType = $ingredient['unit_type'] ?? 'unit';
        $variantId = $ingredient['variant_id'];
        $supplierId = $ingredient['supplier_id'] ?? 1;

        $quantityValue = $restockQty * $unitMeasure;

        // Try to find existing row
        $existing = $pdo->prepare("
            SELECT inventory_id, quantity, quantity_value FROM ingredients_inventory
            WHERE ingredient_id = ? AND variant_id <=> ? AND supplier_id = ? AND user_id = ?
            LIMIT 1
        ");
        $existing->execute([$ingredientId, $variantId, $supplierId, $userId]);
        $row = $existing->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Update existing
            $newQty = $row['quantity'] + $restockQty;
            $newQtyValue = $row['quantity_value'] + $quantityValue;

            $pdo->prepare("UPDATE ingredients_inventory
                SET quantity = ?, quantity_value = ?, updated_at = NOW()
                WHERE inventory_id = ?")
                ->execute([$newQty, $newQtyValue, $row['inventory_id']]);
        } else {
            // Insert new
            $pdo->prepare("INSERT INTO ingredients_inventory
                (ingredient_id, ingredient_name, quantity, quantity_value, unit_type, supplier_id, variant_id, user_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())")
                ->execute([
                    $ingredientId,
                    $ingredient['ingredient_name'],
                    $restockQty,
                    $quantityValue,
                    $unitType,
                    $supplierId,
                    $variantId,
                    $userId
                ]);
        }
    }

    $_SESSION['success'] = "Ingredient(s) restocked successfully.";
    header("Location: ../seller/Store.php");
    exit;
}
