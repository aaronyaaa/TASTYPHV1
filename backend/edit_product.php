<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId      = $_POST['product_id'] ?? null;
    $productName    = $_POST['product_name'] ?? '';
    $slug           = $_POST['slug'] ?? '';
    $description    = $_POST['description'] ?? '';
    $price          = $_POST['price'] ?? 0;
    $discountPrice  = $_POST['discount_price'] ?? null;
    $stock          = $_POST['stock'] ?? 0;
    $quantityValue  = $_POST['quantity_value'] ?? 1;
    $unitType       = $_POST['unit_type'] ?? 'pcs';
    $isActive       = isset($_POST['is_active']) ? 1 : 0;
    $categoryId     = $_POST['category_id'] ?? null;

    // Optional image upload
    $imagePath = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $uploadDir = '../uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = str_replace('../', '', $targetFile);
        }
    }

    // Update query
    $sql = "UPDATE products SET 
                product_name = :product_name,
                slug = :slug,
                description = :description,
                price = :price,
                discount_price = :discount_price,
                stock = :stock,
                quantity_value = :quantity_value,
                unit_type = :unit_type,
                is_active = :is_active,
                updated_at = NOW()";

    if ($categoryId !== null && $categoryId !== '') {
        $sql .= ", category_id = :category_id";
    }

    if ($imagePath) {
        $sql .= ", image_url = :image_url";
    }

    $sql .= " WHERE product_id = :product_id";

    $stmt = $pdo->prepare($sql);

    $params = [
        ':product_name' => $productName,
        ':slug' => $slug,
        ':description' => $description,
        ':price' => $price,
        ':discount_price' => $discountPrice,
        ':stock' => $stock,
        ':quantity_value' => $quantityValue,
        ':unit_type' => $unitType,
        ':is_active' => $isActive,
        ':product_id' => $productId,
    ];

    if ($categoryId !== null && $categoryId !== '') {
        $params[':category_id'] = $categoryId;
    }

    if ($imagePath) {
        $params[':image_url'] = $imagePath;
    }

    $stmt->execute($params);

    $_SESSION['success'] = "Product updated successfully.";
    header("Location: ../seller/Store.php");
    exit;
}

http_response_code(405);
echo "Method not allowed.";
?>
