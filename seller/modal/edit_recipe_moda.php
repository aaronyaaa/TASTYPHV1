<?php foreach ($recipes as $recipe): ?>

  <style>
  .modal-dialog-scrollable .modal-content {
    max-height: 90vh; /* Ensure it's not too tall */
    overflow: hidden;
  }
  .modal-dialog-scrollable .modal-body {
    overflow-y: auto;
    max-height: calc(90vh - 150px); /* Adjust to leave space for header/footer */
  }
</style>

<!-- Edit Recipe Modal -->
<div class="modal fade" id="editRecipeModal<?= $recipe['recipe_id'] ?>" tabindex="-1">
  <div class="modal-dialog modal-xl  modal-dialog-scrollable">
    <div class="modal-content">
      <form action="../backend/update_recipe.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?= $recipe['recipe_id'] ?>">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit Recipe</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- Recipe Details -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Recipe Title</label>
              <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($recipe['title']) ?>" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Servings</label>
              <input type="number" name="servings" class="form-control" value="<?= $recipe['servings'] ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Prep Time</label>
              <input type="text" name="prep_time" class="form-control" value="<?= htmlspecialchars($recipe['prep_time']) ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Cook Time</label>
              <input type="text" name="cook_time" class="form-control" value="<?= htmlspecialchars($recipe['cook_time']) ?>">
            </div>
          </div>

          <!-- Ingredients -->
          <h6 class="mt-4">Ingredients</h6>
          <div id="edit-ingredient-list-<?= $recipe['recipe_id'] ?>">
            <?php
            $i_stmt = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?");
            $i_stmt->execute([$recipe['recipe_id']]);
            foreach ($i_stmt as $index => $ing):
            ?>
            <div class="row g-2 align-items-end ingredient-row mb-2">
              <div class="col-md-4">
                <label>Ingredient Name</label>
                <input type="text" name="ingredient_name[]" class="form-control" value="<?= htmlspecialchars($ing['ingredient_name']) ?>" required>
              </div>
              <div class="col-md-3">
                <label>Quantity</label>
                <input type="number" name="quantity_value[]" class="form-control" step="0.01" value="<?= $ing['quantity_value'] ?>" required>
              </div>
              <div class="col-md-3">
                <label>Unit</label>
                <select name="unit_type[]" class="form-select">
                  <?php
                  $units = ['g','kg','ml','l','pcs','pack','bottle','can'];
                  foreach ($units as $unit) {
                    $selected = $ing['unit_type'] === $unit ? 'selected' : '';
                    echo "<option value='$unit' $selected>$unit</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm mb-3 add-ingredient-btn" data-target="#edit-ingredient-list-<?= $recipe['recipe_id'] ?>">+ Add Ingredient</button>

          <!-- Directions -->
          <h6>Steps</h6>
          <div id="edit-directions-list-<?= $recipe['recipe_id'] ?>">
            <?php
            $s_stmt = $pdo->prepare("SELECT * FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number ASC");
            $s_stmt->execute([$recipe['recipe_id']]);
            foreach ($s_stmt as $step):
            ?>
            <div class="mb-2 step-row">
              <textarea name="steps[]" class="form-control mb-2" rows="2" required><?= htmlspecialchars($step['instruction']) ?></textarea>
              <button type="button" class="btn btn-sm btn-danger remove-step">Remove</button>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm mb-3 add-step-btn" data-target="#edit-directions-list-<?= $recipe['recipe_id'] ?>">+ Add Step</button>

          <!-- Notes and Image -->
          <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($recipe['notes']) ?></textarea>
          </div>

          <div class="mb-3">
            <label>Update Recipe Image (optional)</label>
            <input type="file" name="recipe_image" class="form-control" accept="image/*">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save me-2"></i> Update Recipe
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Scripts (put at the end of your page) -->
<script>
document.querySelectorAll('.add-ingredient-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const target = document.querySelector(this.getAttribute('data-target'));
    const clone = target.querySelector('.ingredient-row').cloneNode(true);
    clone.querySelectorAll('input').forEach(input => input.value = '');
    target.appendChild(clone);
  });
});

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('remove-ingredient')) {
    const rows = e.target.closest('.ingredient-row').parentElement.querySelectorAll('.ingredient-row');
    if (rows.length > 1) e.target.closest('.ingredient-row').remove();
  }
});

document.querySelectorAll('.add-step-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const target = document.querySelector(this.getAttribute('data-target'));
    const container = document.createElement('div');
    container.classList.add('mb-2', 'step-row');
    container.innerHTML = `
      <textarea name="steps[]" class="form-control mb-2" rows="2" placeholder="Step instruction..." required></textarea>
      <button type="button" class="btn btn-sm btn-danger remove-step">Remove</button>
    `;
    target.appendChild(container);
  });
});

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('remove-step')) {
    const rows = e.target.closest('.step-row').parentElement.querySelectorAll('.step-row');
    if (rows.length > 1) e.target.closest('.step-row').remove();
  }
});
</script>
