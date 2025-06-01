<!-- Modal: Add Variant -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content shadow-sm border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="variantModalLabel"><i class="fa-solid fa-layer-group me-2"></i> Add Variant</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="variantForm" action="../backend/supplier/create_variant.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="ingredient_id" id="variantIngredientId">

          <div class="mb-3">
            <label for="variant_name" class="form-label fw-semibold">Variant Name</label>
            <input type="text" class="form-control" name="variant_name" id="variant_name" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="price" class="form-label fw-semibold">Price (₱)</label>
              <input type="number" step="0.01" class="form-control" name="price" id="price" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="discount_price" class="form-label fw-semibold">Discount Price (₱)</label>
              <input type="number" step="0.01" class="form-control" name="discount_price" id="discount_price">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="stock" class="form-label fw-semibold">Stock</label>
              <input type="number" class="form-control" name="stock" id="stock" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="quantity_value" class="form-label fw-semibold">Measurement Value</label>
              <input type="number" step="0.01" class="form-control" name="quantity_value" id="quantity_value" required placeholder="e.g. 500">
            </div>
          </div>

          <div class="mb-3">
            <label for="unit_type" class="form-label fw-semibold">Unit Type</label>
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

          <div class="mb-3">
            <label for="variant_image" class="form-label fw-semibold">Variant Image</label>
            <input class="form-control" type="file" name="image" id="variant_image" accept="image/*">
            <div class="form-text">JPG, PNG, WEBP • Max 1MB</div>
            <img id="variantImagePreview" src="../assets/images/default-category.png" alt="Preview" class="img-thumbnail d-none mt-2" style="max-height: 140px;">
          </div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Mark as Active</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-plus me-1"></i> Save Variant
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('variant_image')?.addEventListener('change', function () {
  const file = this.files[0];
  const preview = document.getElementById('variantImagePreview');
  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  }
});

function openVariantModal(ingredientId) {
  document.getElementById('variantIngredientId').value = ingredientId;
  const modal = new bootstrap.Modal(document.getElementById('variantModal'));
  modal.show();
}
</script>
