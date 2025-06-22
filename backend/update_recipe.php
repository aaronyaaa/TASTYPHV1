<?php
require_once '../database/db_connect.php';
session_start();

$user_id = $_SESSION['userId'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$recipe_id     = $_POST['recipe_id'] ?? null;
$title         = trim($_POST['title'] ?? '');
$servings      = $_POST['servings'] ?? null;
$prep_time     = trim($_POST['prep_time'] ?? '');
$cook_time     = trim($_POST['cook_time'] ?? '');
$notes         = trim($_POST['notes'] ?? '');

if (!$recipe_id || !$title) {
    echo "Missing recipe ID or title.";
    exit;
}

// Handle image upload if provided
$image_path = null;
if (!empty($_FILES['recipe_image']['tmp_name']) && $_FILES['recipe_image']['error'] === 0) {
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeType = mime_content_type($_FILES['recipe_image']['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        echo "Invalid image format.";
        exit;
    }

    $ext = $allowedTypes[$mimeType];
    $filename = 'recipe_' . $user_id . '_' . time() . '.' . $ext;
    $uploadDir = '../../uploads/recipes/';
    $relativePath = 'uploads/recipes/' . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fullPath = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['recipe_image']['tmp_name'], $fullPath)) {
        echo "Failed to save image.";
        exit;
    }

    $image_path = $relativePath;
}

// Update base recipe info
$update = $pdo->prepare("
    UPDATE recipes
    SET title = ?, servings = ?, prep_time = ?, cook_time = ?, notes = ?" . ($image_path ? ", recipe_image = ?" : "") . "
    WHERE recipe_id = ? AND user_id = ?
");

$params = [$title, $servings, $prep_time, $cook_time, $notes];
if ($image_path) $params[] = $image_path;
$params[] = $recipe_id;
$params[] = $user_id;

$update->execute($params);

// Delete existing ingredients and steps
$pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$recipe_id]);
$pdo->prepare("DELETE FROM recipe_steps WHERE recipe_id = ?")->execute([$recipe_id]);

// Re-insert ingredients
if (!empty($_POST['ingredient_name'])) {
    foreach ($_POST['ingredient_name'] as $i => $name) {
        $quantity = $_POST['quantity_value'][$i] ?? 0;
        $unit = $_POST['unit_type'][$i] ?? '';
        if (trim($name) !== '') {
            $pdo->prepare("
                INSERT INTO recipe_ingredients (recipe_id, ingredient_name, quantity_value, unit_type)
                VALUES (?, ?, ?, ?)
            ")->execute([$recipe_id, trim($name), floatval($quantity), trim($unit)]);
        }
    }
}

// Re-insert steps
if (!empty($_POST['steps'])) {
    foreach ($_POST['steps'] as $i => $instruction) {
        if (trim($instruction) !== '') {
            $pdo->prepare("
                INSERT INTO recipe_steps (recipe_id, step_number, instruction)
                VALUES (?, ?, ?)
            ")->execute([$recipe_id, $i + 1, trim($instruction)]);
        }
    }
}

header("Location: ../seller/Store.php");
exit;
