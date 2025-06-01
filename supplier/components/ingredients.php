<div class="tab-pane fade show active" id="ingredients" role="tabpanel">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fa-solid fa-leaf me-2 text-success"></i> My Ingredients</h4>

            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ingredientModal">
                <i class="fa-solid fa-plus me-1"></i> Add Ingredient
            </button>
        </div>

        <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <input type="text" id="ingredientSearch" class="form-control w-100 w-md-50" placeholder="Search ingredients...">

            <select id="categoryFilter" class="form-select w-100 w-md-25">
                <option value="">All Categories</option>
                <?php foreach ($supplierCategories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php include('modal/ingredient_modal.php'); ?>
<!-- Ingredient Grid -->
<div id="ingredientSection">
  <div class="row g-4" id="ingredientList"></div>
</div>

<!-- Variant Grid (Hidden by default) -->
<div id="variantSection" class="d-none">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 id="variantTitle">Variants</h4>
    <button class="btn btn-secondary" onclick="backToIngredients()">
      <i class="fa fa-arrow-left me-1"></i> Back to Ingredients
    </button>
  </div>
  <div class="row g-4" id="variantList"></div>
</div>

        <div class="row g-3" id="ingredientList">
        </div>
    </div>
</div>