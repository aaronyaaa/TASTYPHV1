<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    die("Unauthorized access");
}

// Helper: slug generator
function generateSlug($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

// Collect form inputs
$productName    = $_POST['product_name'] ?? '';
$slug           = $_POST['slug'] ?: generateSlug($productName);
$description    = $_POST['description'] ?? '';
$price          = $_POST['price'] ?? 0;
$discountPrice  = $_POST['discount_price'] ?? null;
$stock          = $_POST['stock'] ?? 0;
$quantityValue  = $_POST['quantity_value'] ?? 1;
$unitType       = $_POST['unit_type'] ?? 'pcs';
$notes          = "Cooked product via kitchen inventory.";
$categoryId     = null;
$imagePath      = null;

// ✅ Upload image
if (!empty($_FILES['image']['tmp_name'])) {
    $uploadDir = '../uploads/products/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = str_replace('../', '', $targetFile);
    }
}

// ✅ Validate seller
$sellerStmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$sellerStmt->execute([$userId]);
$seller_id = $sellerStmt->fetchColumn();
if (!$seller_id) {
    die("Seller account not found for this user.");
}

// ✅ Insert into products
$stmt = $pdo->prepare("INSERT INTO products (
    product_name, slug, description, image_url, seller_id, category_id,
    price, discount_price, stock, quantity_value, unit_type,
    is_active, rating, notes, created_by, updated_by
) VALUES (
    :product_name, :slug, :description, :image_url, :seller_id, :category_id,
    :price, :discount_price, :stock, :quantity_value, :unit_type,
    1, 0.00, :notes, :created_by, :updated_by
)");
$stmt->execute([
    'product_name'     => $productName,
    'slug'             => $slug,
    'description'      => $description,
    'image_url'        => $imagePath,
    'seller_id'        => $seller_id,
    'category_id'      => $categoryId,
    'price'            => $price,
    'discount_price'   => $discountPrice,
    'stock'            => $stock,
    'quantity_value'   => $quantityValue,
    'unit_type'        => $unitType,
    'notes'            => $notes,
    'created_by'       => $userId,
    'updated_by'       => $userId,
]);

$productId = $pdo->lastInsertId();

// ✅ Insert into cooking_history
$historyStmt = $pdo->prepare("INSERT INTO cooking_history (
    product_id, user_id, stock_created, notes
) VALUES (
    :product_id, :user_id, :stock_created, :notes
)");
$historyStmt->execute([
    'product_id'   => $productId,
    'user_id'      => $userId,
    'stock_created'=> $stock,
    'notes'        => $notes
]);

$historyId = $pdo->lastInsertId();

// ✅ Deduct and log ingredients
$ingredients = $_POST['ingredients'] ?? [];

foreach ($ingredients as $ingredient) {
    $ingredientId = $ingredient['ingredient_id'];
    $deductQty = floatval($ingredient['quantity']);
    $unit = $ingredient['unit'];

    // Fetch current stock & name
    $stmt = $pdo->prepare("SELECT quantity_value, ingredient_name FROM kitchen_inventory WHERE user_id = ? AND ingredient_id = ?");
    $stmt->execute([$userId, $ingredientId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $current = $row['quantity_value'];
        $ingredientName = $row['ingredient_name'];

        // Update inventory
        if ($current >= $deductQty) {
            $newQty = max(0, $current - $deductQty);
            $updateStmt = $pdo->prepare("UPDATE kitchen_inventory SET quantity_value = ?, updated_at = NOW() WHERE user_id = ? AND ingredient_id = ?");
            $updateStmt->execute([$newQty, $userId, $ingredientId]);
        }

        // Log cooking_history_ingredients
        $logStmt = $pdo->prepare("INSERT INTO cooking_history_ingredients (
            history_id, ingredient_id, ingredient_name, quantity_used, unit_type
        ) VALUES (
            :history_id, :ingredient_id, :ingredient_name, :quantity_used, :unit_type
        )");
        $logStmt->execute([
            'history_id'      => $historyId,
            'ingredient_id'   => $ingredientId,
            'ingredient_name' => $ingredientName,
            'quantity_used'   => $deductQty,
            'unit_type'       => $unit,
        ]);
    } else {
        $_SESSION['warning'] = "Ingredient ID $ingredientId not found in inventory.";
    }
}

// ✅ Success redirect
$_SESSION['success'] = "Product '$productName' cooked and logged successfully!";
header("Location: ../seller/Store.php");
exit;
