<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$ingredientId = $_GET['ingredient_id'] ?? null;
if (!$ingredientId) die("Ingredient not found.");

// Fetch main ingredient
$stmt = $pdo->prepare("SELECT * FROM ingredients WHERE ingredient_id = ?");
$stmt->execute([$ingredientId]);
$ingredient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ingredient) die("Ingredient not found.");

// Fetch variants
$variantStmt = $pdo->prepare("SELECT * FROM ingredient_variants WHERE ingredient_id = ? AND is_active = 1");
$variantStmt->execute([$ingredientId]);
$variants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch supplier address
$supplierStmt = $pdo->prepare("SELECT * FROM supplier_applications WHERE supplier_id = ?");
$supplierStmt->execute([$ingredient['supplier_id']]);
$supplier = $supplierStmt->fetch(PDO::FETCH_ASSOC);

// Fetch logged-in user address
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['userId']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
