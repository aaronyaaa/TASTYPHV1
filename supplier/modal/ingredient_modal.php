<div class="modal fade" id="ingredientModal" tabindex="-1" aria-labelledby="ingredientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-sm border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="ingredientModalLabel">
          <i class="fa-solid fa-plus me-2"></i> Add Ingredient
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="ingredientForm" action="../backend/supplier/create_ingredient.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-8">
              <div class="mb-3">
                <label for="ingredient_name" class="form-label fw-semibold">Ingredient Name</label>
                <input type="text" class="form-control" name="ingredient_name" id="ingredient_name" required>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Describe the ingredient..."></textarea>
              </div>

              <div class="mb-3">
                <label for="category_id" class="form-label fw-semibold">Category</label>
                <select class="form-select" name="category_id" id="category_id" required>
                  <option value="">Select a category</option>
                  <?php foreach ($supplierCategories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="price" class="form-label fw-semibold">Price (₱)</label>
                  <input type="number" step="0.01" class="form-control" name="price" id="price" required>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="stock" class="form-label fw-semibold">Stock</label>
                  <input type="number" class="form-control" name="stock" id="stock" min="0" placeholder="e.g. 50">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="quantity_value" class="form-label fw-semibold">Measurement Value</label>
                  <input type="number" step="0.01" class="form-control" name="quantity_value" id="quantity_value" required placeholder="e.g. 500">
                  <div class="form-text">Amount per unit (e.g. 500 ml, 1.5 kg)</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="unit_type" class="form-label fw-semibold">Unit</label>
                  <select class="form-select" name="unit_type" id="unit_type" required>
                    <option value="">Select unit</option>
                    <option value="g">Grams (g)</option>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="ml">Milliliters (ml)</option>
                    <option value="l">Liters (L)</option>
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="pack">Pack</option>
                    <option value="bottle">Bottle</option>
                    <option value="can">Can</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Right Column: Image Upload -->
            <div class="col-md-4 text-center">
              <label for="image" class="form-label fw-semibold">Ingredient Image</label>
              <input class="form-control mb-2" type="file" name="image" id="image" accept="image/*">
              <div class="form-text">Max 1MB • JPG / PNG / WEBP</div>
              <img id="imagePreview" src="../assets/images/default-category.png" alt="Preview" class="img-thumbnail mt-3 d-none" style="max-height: 160px;">
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-success px-4">
            <i class="fa-solid fa-plus me-1"></i> Save Ingredient
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
