<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

header('Content-Type: application/json');

$user_id = $_SESSION['userId'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$q = strtolower(trim($_GET['q'] ?? ''));
if (!$q) {
    echo json_encode(['error' => 'No query provided']);
    exit;
}

$phrases_to_remove = [
    'i want to cook', 'how to make', 'can i cook', 'show me',
    'i have', 'what can i cook with', 'i want to make', 'i want', 'make'
];
foreach ($phrases_to_remove as $phrase) {
    $q = str_ireplace($phrase, '', $q);
}
$q = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $q));

// Get all recipes
$stmt = $pdo->query("SELECT * FROM kakanin_dataset");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fuzzy match
$bestMatch = null;
$lowestDistance = PHP_INT_MAX;
foreach ($recipes as $recipe) {
    $title = strtolower($recipe['recipe_title']);
    $distance = levenshtein($q, $title);
    if ($distance < $lowestDistance) {
        $lowestDistance = $distance;
        $bestMatch = $recipe;
    }
}

if (!$bestMatch || $lowestDistance > 6) {
    echo json_encode(['error' => 'No close match found']);
    exit;
}

// Load ingredients from normalized table
$ingredientQuery = $pdo->prepare("SELECT * FROM kakanin_ingredients WHERE recipe_id = ?");
$ingredientQuery->execute([$bestMatch['id']]);
$ingredients = $ingredientQuery->fetchAll(PDO::FETCH_ASSOC);

$final_ingredients = [];
$missing = [];
$you_need_to_buy = [];

foreach ($ingredients as $row) {
    $name = trim($row['ingredient_name']);
    $qty = floatval($row['quantity_value']);
    $unit = trim($row['unit_type']);
    $display = "$name — $qty $unit";
    $final_ingredients[] = $display;

    $found = false;

    // Check kitchen_inventory
    $check1 = $pdo->prepare("SELECT COUNT(*) FROM kitchen_inventory WHERE LOWER(ingredient_name) = ? AND user_id = ?");
    $check1->execute([strtolower($name), $user_id]);
    if ($check1->fetchColumn()) {
        $found = true;
    }

    // Check ingredients_inventory if not found
    if (!$found) {
        $check2 = $pdo->prepare("SELECT COUNT(*) FROM ingredients_inventory WHERE LOWER(ingredient_name) = ? AND user_id = ?");
        $check2->execute([strtolower($name), $user_id]);
        if ($check2->fetchColumn()) {
            $found = true;
        }
    }

    if (!$found) {
        $missing[] = $display;
        $you_need_to_buy[] = $name;
    }
}

echo json_encode([
    'recipe_title' => $bestMatch['recipe_title'],
    'ingredients' => $final_ingredients,
    'steps' => $bestMatch['steps'],
    'servings' => $bestMatch['servings'],
    'prep_time' => $bestMatch['prep_time'],
    'cook_time' => $bestMatch['cook_time'],
    'notes' => $bestMatch['notes'],
    'missing' => $missing,
    'you_need_to_buy' => $you_need_to_buy,
    'match_score' => $lowestDistance
]);
exit;
