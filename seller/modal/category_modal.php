<!-- Modal: Create Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-sm border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="categoryModalLabel">
          <i class="fa-solid fa-tags me-2"></i> Add New Category
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="categoryForm" action="../backend/seller/create_category.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row g-4">
            <!-- Left: Inputs -->
            <div class="col-md-8">
              <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Category Name</label>
                <input type="text" class="form-control shadow-sm" id="name" name="name" required placeholder="e.g. Rice ckae">
              </div>

              <div class="mb-3">
                <label for="slug" class="form-label fw-semibold">Slug</label>
                <input type="text" class="form-control shadow-sm" id="slug" name="slug" placeholder="e.g. rice cake">
                <div class="form-text">Use lowercase letters and dashes (e.g. glutinous-rice)</div>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control shadow-sm" id="description" name="description" rows="3" placeholder="Short description..."></textarea>
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                  Make this category public and active
                </label>
              </div>
            </div>

            <!-- Right: Image Upload & Preview -->
            <div class="col-md-4 text-center">
              <label for="image" class="form-label fw-semibold">Category Image</label>
              <input class="form-control mb-2 shadow-sm" type="file" id="image" name="image" accept="image/*">
              <div class="form-text">JPG, PNG, WEBP — Max: 1MB</div>
              <img id="imagePreview" src="../assets/images/default-category.png" alt="Preview" class="img-thumbnail mt-3 d-none" style="max-height: 180px; object-fit: cover;">
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-success px-4">
            <i class="fa-solid fa-plus me-2"></i> Add Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
