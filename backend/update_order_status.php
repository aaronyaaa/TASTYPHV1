<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

$orderId = $_POST['order_id'] ?? null;
$newStatus = $_POST['status'] ?? null;
$authorizedBy = $_SESSION['username'] ?? 'System'; // Optional: replace with actual user identifier

if (!$orderId || !$newStatus) {
    header("Location: ../supplier/order.php?error=invalid");
    exit;
}

// Validate supplier
$stmt = $pdo->prepare("SELECT supplier_id FROM supplier_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$supplierId = $stmt->fetchColumn();

if (!$supplierId) {
    header("Location: ../supplier/order.php?error=no_supplier");
    exit;
}

$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if (!in_array($newStatus, $validStatuses)) {
    header("Location: ../supplier/order.php?error=invalid_status");
    exit;
}

// Update order status
$update = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ? AND supplier_id = ?");
$update->execute([$newStatus, $orderId, $supplierId]);

// Auto-generate receipt if delivered and not already existing
if ($newStatus === 'delivered') {
    // Check if receipt already exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM receipts WHERE order_id = ?");
    $check->execute([$orderId]);

    if ($check->fetchColumn() == 0) {
        // Fetch order data
        $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND supplier_id = ?");
        $orderStmt->execute([$orderId, $supplierId]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Insert into receipts
            $receiptStmt = $pdo->prepare("
                INSERT INTO receipts 
                    (order_id, user_id, supplier_id, subtotal, total_paid, amount_paid, authorized_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $receiptStmt->execute([ 
                $order['order_id'],
                $order['user_id'],
                $order['supplier_id'],
                $order['total_price'],
                $order['total_price'],
                $order['total_price'], // Assume full payment
                $authorizedBy
            ]);
            $receiptId = $pdo->lastInsertId();

            // Insert items into receipt_item
            $itemStmt = $pdo->prepare("
                SELECT oi.quantity, oi.unit_price, i.ingredient_id, i.ingredient_name
                FROM order_items oi
                JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
                WHERE oi.order_id = ?
            ");
            $itemStmt->execute([$order['order_id']]);
            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $receiptItemStmt = $pdo->prepare("
                    INSERT INTO receipt_item 
                        (receipt_id, ingredient_id, description, quantity, unit_price, discount, taxed)
                    VALUES (?, ?, ?, ?, ?, 0.00, 0)
                ");
                $receiptItemStmt->execute([ 
                    $receiptId,
                    $item['ingredient_id'],
                    $item['ingredient_name'],
                    $item['quantity'],
                    $item['unit_price']
                ]);
            }
        }
    }
}

// Handle Ingredients and Variants Separately in ingredients_inventory
if ($newStatus === 'delivered') {
    // Fetch order items
    $itemStmt = $pdo->prepare("
        SELECT oi.ingredient_id, oi.variant_id, oi.quantity, i.ingredient_name, i.quantity_value AS ingredient_quantity_value, i.unit_type AS ingredient_unit_type, i.supplier_id, oi.unit_price, iv.quantity_value AS variant_quantity_value, iv.unit_type AS variant_unit_type
        FROM order_items oi
        JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
        LEFT JOIN ingredient_variants iv ON oi.variant_id = iv.variant_id  -- Join to get variant quantity_value and unit_type
        WHERE oi.order_id = ?
    ");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        // Handle main ingredients
        if (!$item['variant_id']) {
            // Check if ingredient already exists in inventory
            $invCheck = $pdo->prepare("SELECT inventory_id, quantity, quantity_value, unit_type FROM ingredients_inventory WHERE user_id = ? AND ingredient_id = ?");
            $invCheck->execute([$order['user_id'], $item['ingredient_id']]);
            $existing = $invCheck->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update ingredient inventory if it exists
                $newQty = $existing['quantity'] + $item['quantity'];
                $invUpdate = $pdo->prepare("UPDATE ingredients_inventory SET quantity = ?, updated_at = NOW() WHERE inventory_id = ?");
                $invUpdate->execute([$newQty, $existing['inventory_id']]);
            } else {
                // Insert new ingredient inventory if it doesn't exist
                $invInsert = $pdo->prepare("
                    INSERT INTO ingredients_inventory 
                        (ingredient_id, ingredient_name, quantity, quantity_value, unit_type, supplier_id, variant_id, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $invInsert->execute([
                    $item['ingredient_id'],
                    $item['ingredient_name'],
                    $item['quantity'],
                    $item['ingredient_quantity_value'],  // Use the ingredient's quantity_value
                    $item['ingredient_unit_type'],       // Use the ingredient's unit_type
                    $item['supplier_id'],
                    $item['variant_id'], // NULL for non-variant ingredients
                    $order['user_id']
                ]);
            }
        } else {
            // Handle variants separately
            $invCheckVariant = $pdo->prepare("SELECT inventory_id, quantity, quantity_value, unit_type FROM ingredients_inventory WHERE user_id = ? AND variant_id = ?");
            $invCheckVariant->execute([$order['user_id'], $item['variant_id']]);
            $existingVariant = $invCheckVariant->fetch(PDO::FETCH_ASSOC);

            if ($existingVariant) {
                // Update variant inventory if it exists
                $newQtyVariant = $existingVariant['quantity'] + $item['quantity'];
                $invUpdateVariant = $pdo->prepare("UPDATE ingredients_inventory SET quantity = ?, updated_at = NOW() WHERE inventory_id = ?");
                $invUpdateVariant->execute([$newQtyVariant, $existingVariant['inventory_id']]);
            } else {
                // Insert new variant inventory if it doesn't exist
                $invInsertVariant = $pdo->prepare("
                    INSERT INTO ingredients_inventory 
                        (ingredient_id, ingredient_name, quantity, quantity_value, unit_type, supplier_id, variant_id, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $invInsertVariant->execute([
                    $item['ingredient_id'],
                    $item['ingredient_name'],
                    $item['quantity'],
                    $item['variant_quantity_value'],  // Use the variant's quantity_value
                    $item['variant_unit_type'],       // Use the variant's unit_type
                    $item['supplier_id'],
                    $item['variant_id'], // Specific variant ID
                    $order['user_id']
                ]);
            }
        }
    }
}

header("Location: ../supplier/order.php?success=1");
exit;
?>
