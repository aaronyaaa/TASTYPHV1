<!-- Add Manual Recipe Modal -->
<div class="modal fade" id="addRecipeModal" tabindex="-1" aria-labelledby="addRecipeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form action="../backend/save_manual_recipe.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="addRecipeModalLabel">
            <i class="fas fa-utensils me-2"></i> Add New Recipe (Manual Ingredients)
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Recipe Details -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Recipe Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Servings</label>
              <input type="number" name="servings" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Prep Time</label>
              <input type="text" name="prep_time" class="form-control" placeholder="e.g. 30 mins">
            </div>
            <div class="col-md-2">
              <label class="form-label">Cook Time</label>
              <input type="text" name="cook_time" class="form-control" placeholder="e.g. 1 hour">
            </div>
          </div>

          <!-- Manual Ingredients Section -->
          <h6 class="mt-4">Ingredients (Manual Entry)</h6>
          <div id="ingredient-list">
            <div class="row g-2 align-items-end ingredient-row mb-2">
              <div class="col-md-4">
                <label>Ingredient Name</label>
                <input type="text" name="ingredient_name[]" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label>Quantity</label>
                <input type="number" name="quantity_value[]" class="form-control" step="0.01" required>
              </div>
              <div class="col-md-3">
                <label>Unit</label>
                <select name="unit_type[]" class="form-select">
                  <option value="g">g</option>
                  <option value="kg">kg</option>
                  <option value="ml">ml</option>
                  <option value="l">l</option>
                  <option value="pcs">pcs</option>
                  <option value="pack">pack</option>
                  <option value="bottle">bottle</option>
                  <option value="can">can</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-ingredient">+ Add Ingredient</button>

          <!-- Directions Section -->
          <h6>Directions</h6>
          <div id="directions-list">
            <div class="mb-2 step-row">
              <textarea name="steps[]" class="form-control mb-2" rows="2" placeholder="Step instruction..." required></textarea>
              <button type="button" class="btn btn-sm btn-danger remove-step">Remove</button>
            </div>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm" id="add-step">+ Add Step</button>

          <!-- Notes and Image -->
          <div class="mt-4 mb-3">
            <label>Notes (optional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Add any extra tips or notes"></textarea>
          </div>

          <div class="mb-3">
            <label>Recipe Image</label>
            <input type="file" name="recipe_image" class="form-control" accept="image/*">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Recipe
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script>
document.getElementById('add-ingredient').addEventListener('click', function () {
  const clone = document.querySelector('.ingredient-row').cloneNode(true);
  clone.querySelectorAll('input').forEach(input => input.value = '');
  document.getElementById('ingredient-list').appendChild(clone);
});

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('remove-ingredient')) {
    const rows = document.querySelectorAll('.ingredient-row');
    if (rows.length > 1) e.target.closest('.ingredient-row').remove();
  }
});

document.getElementById('add-step').addEventListener('click', function () {
  const stepContainer = document.createElement('div');
  stepContainer.classList.add('mb-2', 'step-row');
  stepContainer.innerHTML = `
    <textarea name="steps[]" class="form-control mb-2" rows="2" placeholder="Step instruction..." required></textarea>
    <button type="button" class="btn btn-sm btn-danger remove-step">Remove</button>
  `;
  document.getElementById('directions-list').appendChild(stepContainer);
});

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('remove-step')) {
    const steps = document.querySelectorAll('.step-row');
    if (steps.length > 1) e.target.closest('.step-row').remove();
  }
});
</script>
