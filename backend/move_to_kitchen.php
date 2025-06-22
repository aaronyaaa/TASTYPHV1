<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['move_qty'])) {
    foreach ($_POST['move_qty'] as $inventoryId => $qtyToMove) {
        $inventoryId = intval($inventoryId);
        $qtyToMove = floatval($qtyToMove);
        if ($qtyToMove <= 0) continue;

        $stmt = $pdo->prepare("
            SELECT 
                ii.inventory_id,
                ii.quantity AS inv_quantity,
                ii.quantity_value AS inv_quantity_value,
                ii.unit_type,
                ii.supplier_id,
                ii.variant_id,
                ii.user_id,
                i.ingredient_id,
                i.ingredient_name,
                i.quantity_value AS base_quantity_value,
                iv.quantity_value AS variant_quantity_value
            FROM ingredients_inventory ii
            JOIN ingredients i ON ii.ingredient_id = i.ingredient_id
            LEFT JOIN ingredient_variants iv ON ii.variant_id = iv.variant_id
            WHERE ii.inventory_id = ? AND ii.user_id = ?
        ");
        $stmt->execute([$inventoryId, $userId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $unitMeasure = $item['variant_quantity_value'] ?? $item['base_quantity_value'];
            $stock = $item['inv_quantity'];
            $storedQtyValue = $item['inv_quantity_value'];

            // ✅ Use the greater of stored or calculated quantity value
            $calculatedQtyValue = $stock * $unitMeasure;
            $qtyValue = max($storedQtyValue, $calculatedQtyValue);

            if ($qtyValue <= 0 || $unitMeasure <= 0 || $qtyValue < $qtyToMove) {
                continue;
            }

            $remainingQtyValue = $qtyValue - $qtyToMove;
            $newStock = floor($remainingQtyValue / $unitMeasure);

            // ✅ Update ingredients_inventory
            $pdo->prepare("UPDATE ingredients_inventory 
                SET quantity = ?, quantity_value = ?, updated_at = NOW() 
                WHERE inventory_id = ?")
                ->execute([$newStock, $remainingQtyValue, $inventoryId]);

            // ✅ Delete row if fully depleted
            if ($newStock <= 0 && $remainingQtyValue <= 0) {
                $pdo->prepare("DELETE FROM ingredients_inventory WHERE inventory_id = ?")
                    ->execute([$inventoryId]);
            }

            // ✅ Update or insert into kitchen_inventory
            $existingKitchen = $pdo->prepare("
                SELECT kitchen_inventory_id, quantity, quantity_value 
                FROM kitchen_inventory 
                WHERE ingredient_id = ? AND variant_id <=> ? AND supplier_id = ? AND user_id = ? AND unit_type = ?
                LIMIT 1
            ");
            $existingKitchen->execute([
                $item['ingredient_id'],
                $item['variant_id'],
                $item['supplier_id'],
                $userId,
                $item['unit_type']
            ]);
            $existing = $existingKitchen->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQty = $existing['quantity'] + ($qtyToMove / $unitMeasure);
                $newQtyValue = $existing['quantity_value'] + $qtyToMove;

                $pdo->prepare("UPDATE kitchen_inventory 
                    SET quantity = ?, quantity_value = ?, updated_at = NOW() 
                    WHERE kitchen_inventory_id = ?")
                    ->execute([$newQty, $newQtyValue, $existing['kitchen_inventory_id']]);
            } else {
                $pdo->prepare("INSERT INTO kitchen_inventory 
                    (ingredient_id, ingredient_name, quantity, quantity_value, unit_type, supplier_id, variant_id, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())")
                    ->execute([
                        $item['ingredient_id'],
                        $item['ingredient_name'],
                        $qtyToMove / $unitMeasure,
                        $qtyToMove,
                        $item['unit_type'],
                        $item['supplier_id'],
                        $item['variant_id'],
                        $userId
                    ]);
            }
        }
    }

    $_SESSION['success'] = "Ingredient(s) moved and updated in kitchen inventory.";
    header("Location: ../seller/Store.php");
    exit;
}
