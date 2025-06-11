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
// Auto-generate receipt and update inventory only if status is delivered
if ($newStatus === 'delivered') {
    // Check if receipt already exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM receipts WHERE order_id = ?");
    $check->execute([$orderId]);

    // Fetch order once
    $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND supplier_id = ?");
    $orderStmt->execute([$orderId, $supplierId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if ($order && $check->fetchColumn() == 0) {
        // Insert receipt
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
            $order['total_price'],
            $authorizedBy
        ]);
        $receiptId = $pdo->lastInsertId();

        // Insert receipt items
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

    // Insert or update ingredients_inventory
    if ($order) {
        $itemStmt = $pdo->prepare("
            SELECT oi.ingredient_id, oi.quantity, i.ingredient_name, i.quantity_value, i.unit_type, i.supplier_id, oi.variant_id
            FROM order_items oi
            JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
            WHERE oi.order_id = ?
        ");
        $itemStmt->execute([$orderId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $invCheck = $pdo->prepare("SELECT inventory_id, quantity FROM ingredients_inventory WHERE user_id = ? AND ingredient_id = ?");
            $invCheck->execute([$order['user_id'], $item['ingredient_id']]);
            $existing = $invCheck->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQty = $existing['quantity'] + $item['quantity'];
                $invUpdate = $pdo->prepare("UPDATE ingredients_inventory SET quantity = ?, updated_at = NOW() WHERE inventory_id = ?");
                $invUpdate->execute([$newQty, $existing['inventory_id']]);
            } else {
                $invInsert = $pdo->prepare("
                    INSERT INTO ingredients_inventory 
                        (ingredient_id, ingredient_name, quantity, quantity_value, unit_type, supplier_id, variant_id, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $invInsert->execute([
                    $item['ingredient_id'],
                    $item['ingredient_name'],
                    $item['quantity'],
                    $item['quantity_value'],
                    $item['unit_type'],
                    $item['supplier_id'],
                    $item['variant_id'],
                    $order['user_id']
                ]);
            }
        }
    }
}



header("Location: ../supplier/order.php?success=1");
exit;
