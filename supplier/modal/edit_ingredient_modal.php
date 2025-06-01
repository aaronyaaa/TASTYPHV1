<!-- Edit Ingredient Modal -->
<div class="modal fade" id="editIngredientModal" tabindex="-1" aria-labelledby="editIngredientLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">
      <form id="editIngredientForm" action="../backend/supplier/update_ingredient.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editIngredientLabel"><i class="fa-solid fa-pen me-2"></i> Edit Ingredient</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="ingredient_id" id="edit_ingredient_id">
          
          <div class="mb-2">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="ingredient_name" id="edit_ingredient_name" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Price (₱)</label>
              <input type="number" class="form-control" name="price" id="edit_price" step="0.01" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Stock</label>
              <input type="number" class="form-control" name="stock" id="edit_stock" min="0">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Quantity Value</label>
              <input type="number" class="form-control" name="quantity_value" id="edit_quantity_value" step="0.01">
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Unit Type</label>
              <select class="form-select" name="unit_type" id="edit_unit_type">
                <option value="g">Grams</option>
                <option value="kg">Kilograms</option>
                <option value="ml">Milliliters</option>
                <option value="l">Liters</option>
                <option value="pcs">Pieces</option>
                <option value="pack">Pack</option>
                <option value="bottle">Bottle</option>
                <option value="can">Can</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" class="form-control" name="image">
            <img id="edit_image_preview" class="mt-2 img-thumbnail" style="max-height: 120px;" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning"><i class="fa fa-save me-1"></i> Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
