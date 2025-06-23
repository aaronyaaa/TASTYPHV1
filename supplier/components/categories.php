<div class="tab-pane fade" id="category" role="tabpanel">
    <lass="container text-start" style="max-width: 900px;">
        <!-- Header & Add Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fa-solid fa-tags me-2 text-success"></i> Ingredient Categories</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fa-solid fa-plus me-1"></i> New Category
            </button>
        </div>

        <?php include('modal/category_modal.php'); ?>
        <!-- Category Grid -->
        <div class="row g-1" id="categoryList">

        </div>
</div>
</div>