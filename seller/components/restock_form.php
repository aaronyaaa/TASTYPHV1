<?php
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    header("Location: ../login.php");
    exit;
}

// Fetch ingredients and variants
$ingredients = $pdo->query("SELECT ingredient_id, ingredient_name FROM ingredients ORDER BY ingredient_name")->fetchAll();
$variants = $pdo->query("SELECT variant_id, ingredient_id, variant_name FROM ingredient_variants")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
?>

<h2>Restock Ingredient</h2>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php elseif (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form action="../backend/restock_ingredient.php" method="POST">
    <label>Ingredient</label>
    <select name="ingredient_id" class="form-control" required>
        <option value="">Select Ingredient</option>
        <?php foreach ($ingredients as $ingredient): ?>
            <option value="<?= $ingredient['ingredient_id'] ?>"><?= htmlspecialchars($ingredient['ingredient_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Variant ID (optional)</label>
    <input type="number" name="variant_id" class="form-control" placeholder="e.g. 3">

    <label>Supplier ID</label>
    <input type="number" name="supplier_id" class="form-control" required>

    <label>Unit Type</label>
    <input type="text" name="unit_type" class="form-control" required placeholder="e.g. g, kg, pcs">

    <label>Stock Quantity</label>
    <input type="number" step="0.01" name="quantity" class="form-control" required>

    <label>Total Quantity Value (e.g. 3 packs * 1000g = 3000)</label>
    <input type="number" step="0.01" name="quantity_value" class="form-control" required>

    <button type="submit" class="btn btn-success mt-3">Restock</button>
</form>
