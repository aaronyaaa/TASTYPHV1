<?php
require_once '../database/db_connect.php'; // PDO $pdo is assumed here
session_start();

$user_id = $_SESSION['userId'] ?? 1;

$title      = $_POST['title'] ?? '';
$servings   = $_POST['servings'] ?? null;
$prep_time  = $_POST['prep_time'] ?? '';
$cook_time  = $_POST['cook_time'] ?? '';
$notes      = $_POST['notes'] ?? '';

// Handle recipe image
$recipe_image = null;
if (!empty($_FILES['recipe_image']['name'])) {
    $imageName = time() . '_' . basename($_FILES['recipe_image']['name']);
    $targetDir = '../uploads/recipes/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFile = $targetDir . $imageName;
    if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $targetFile)) {
        $recipe_image = 'uploads/recipes/' . $imageName;
    }
}

// Insert into `recipes`
$sql = "INSERT INTO recipes (user_id, title, servings, prep_time, cook_time, notes, recipe_image, created_at, updated_at)
        VALUES (:user_id, :title, :servings, :prep_time, :cook_time, :notes, :recipe_image, NOW(), NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id,
    ':title' => $title,
    ':servings' => $servings,
    ':prep_time' => $prep_time,
    ':cook_time' => $cook_time,
    ':notes' => $notes,
    ':recipe_image' => $recipe_image
]);

$recipe_id = $pdo->lastInsertId();

// Insert Ingredients
if (!empty($_POST['ingredient_name'])) {
    $ingredient_sql = "INSERT INTO recipe_ingredients (recipe_id, ingredient_name, quantity_value, unit_type)
                       VALUES (:recipe_id, :ingredient_name, :quantity_value, :unit_type)";
    $ingredient_stmt = $pdo->prepare($ingredient_sql);

    foreach ($_POST['ingredient_name'] as $i => $name) {
        $name = trim($name);
        $quantity = $_POST['quantity_value'][$i] ?? 0;
        $unit = $_POST['unit_type'][$i] ?? '';

        if ($name && $quantity && $unit) {
            $ingredient_stmt->execute([
                ':recipe_id' => $recipe_id,
                ':ingredient_name' => $name,
                ':quantity_value' => $quantity,
                ':unit_type' => $unit
            ]);
        }
    }
}

// Insert Steps
if (!empty($_POST['steps'])) {
    $step_sql = "INSERT INTO recipe_steps (recipe_id, step_number, instruction)
                 VALUES (:recipe_id, :step_number, :instruction)";
    $step_stmt = $pdo->prepare($step_sql);

    foreach ($_POST['steps'] as $i => $instruction) {
        $instruction = trim($instruction);
        if ($instruction) {
            $step_stmt->execute([
                ':recipe_id' => $recipe_id,
                ':step_number' => $i + 1,
                ':instruction' => $instruction
            ]);
        }
    }
}

// Redirect
header("Location: ../seller/store.php?recipe_added=1");
exit;
