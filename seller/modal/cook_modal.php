<?php
if (!isset($pdo)) require_once __DIR__ . '/../../database/db_connect.php';
if (!isset($kitchen)) {
  session_start();
  $userId = $_SESSION['userId'] ?? null;
  $stmt2 = $pdo->prepare("SELECT * FROM kitchen_inventory WHERE user_id = ?");
  $stmt2->execute([$userId]);
  $kitchen = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- First Modal: Cook New Product -->
<div class="modal fade" id="cookModal" tabindex="-1" aria-labelledby="cookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="cookProductForm" method="POST" action="../backend/cook_product.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="cookModalLabel"><i class="fas fa-utensils me-2"></i> Cook New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0"><i class="fas fa-bowl-food me-2"></i> Product Details</h6>

                </div>

                <!-- Product Fields -->
                <div class="row mb-2">
                    <div class="col">
                        <label>Product Name</label>
                        <input type="text" class="form-control" name="product_name" required>
                    </div>
                    <div class="col">
                        <label>Slug</label>
                        <input type="text" class="form-control" name="slug" placeholder="Auto-generated">
                    </div>
                </div>
                <div class="mb-2">
                    <label>Description</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
                <div class="mb-2">
                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label>Price</label>
                        <input type="number" name="price" step="0.01" class="form-control">
                    </div>
                    <div class="col">
                        <label>Discount Price</label>
                        <input type="number" name="discount_price" step="0.01" class="form-control">
                    </div>
                    <div class="col">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label>Quantity per Unit</label>
                        <input type="number" name="quantity_value" class="form-control" value="1">
                    </div>
                    <div class="col">
                        <label>Unit Type</label>
                        <select name="unit_type" class="form-control">
                            <option value="pcs">pcs</option>
                            <option value="tray">tray</option>
                            <option value="box">box</option>
                        </select>
                    </div>
                </div>

                <hr>
                <h6 class="text-muted"><i class="fas fa-list-ul me-2"> </i> Ingredients from Kitchen                 <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#recipeModal" data-bs-dismiss="modal">
                    <i class="fas fa-book me-1"></i> Choose From Recipes
                </button></h6>

                <div id="ingredientsWrapper">
                    <div class="row mb-2">
                        <div class="col">
                            <select name="ingredients[0][ingredient_id]" class="form-control">
                                <?php foreach ($kitchen as $item): ?>
                                    <option value="<?= $item['ingredient_id'] ?>"><?= htmlspecialchars($item['ingredient_name']) ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="col">
                            <input type="number" step="0.01" name="ingredients[0][quantity]" placeholder="Quantity" class="form-control">
                        </div>
                        <div class="col">
                            <input type="text" name="ingredients[0][unit]" class="form-control" placeholder="Unit (e.g. g, pcs)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addIngredientRow()">+ Add Ingredient</button>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-fire"></i> Cook Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Second Modal: Choose Recipe -->
<div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="recipeModalLabel"><i class="fas fa-book me-2"></i> Saved Recipes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="recipeAccordion">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($recipes as $i => $recipe):
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $i ?>">
                                <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>"
                                    aria-expanded="false" aria-controls="collapse<?= $i ?>">
                                    <?= htmlspecialchars($recipe['title']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $i ?>">
                                <div class="accordion-body">
                                    <p><strong>Prep Time:</strong> <?= htmlspecialchars($recipe['prep_time']) ?></p>
                                    <p><strong>Cook Time:</strong> <?= htmlspecialchars($recipe['cook_time']) ?></p>
                                    <p><strong>Servings:</strong> <?= htmlspecialchars($recipe['servings']) ?></p>
                                    <h6 class="fw-semibold">Ingredients:</h6>
                                    <ul class="list-group mb-2">
                                        <?php
                                        $i_stmt = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?");
                                        $i_stmt->execute([$recipe['recipe_id']]);
                                        foreach ($i_stmt->fetchAll() as $ing):
                                        ?>
                                            <li class="list-group-item"><?= htmlspecialchars($ing['quantity_value']) ?> <?= htmlspecialchars($ing['unit_type']) ?> — <?= htmlspecialchars($ing['ingredient_name']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <h6 class="fw-semibold">Steps:</h6>
                                    <ol class="small">
                                        <?php
                                        $s_stmt = $pdo->prepare("SELECT * FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number ASC");
                                        $s_stmt->execute([$recipe['recipe_id']]);
                                        foreach ($s_stmt->fetchAll() as $step):
                                        ?>
                                            <li><?= htmlspecialchars($step['instruction']) ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" onclick="backToCook()">Back to Cook</button>
            </div>
        </div>
    </div>
</div>
<script>
    function backToCook() {
        // Hide recipe modal first
        const recipeModal = bootstrap.Modal.getInstance(document.getElementById('recipeModal'));
        recipeModal.hide();

        // Wait for transition to complete, then show cook modal
        setTimeout(() => {
            const cookModal = new bootstrap.Modal(document.getElementById('cookModal'));
            cookModal.show();
        }, 500); // Adjust delay if your modal transition is faster/slower
    }
</script>