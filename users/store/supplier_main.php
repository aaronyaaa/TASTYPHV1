<div class="container py-4">

    <!-- Fixed Horizontal Scrollable Category Bar -->


    <div class="container py-4">
        <!-- Search and category filter -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Search ingredients...">
            </div>
        </div>

        <!-- Ingredient & Variant Section Wrapper -->
        <div id="ingredientList">
            <div id="ingredientSection">
                <div class="row g-4" id="ingredientGrid">
                    <?php
                    $_GET['supplier_id'] = $supplier_id;
                    include '../backend/supplier/store/fetch_ingredients.php';
                    ?>

                </div>
            </div>

            <div id="variantSection" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 id="variantTitle">Variants of Ingredient</h4>
                    <button class="btn btn-secondary btn-sm" onclick="backToIngredients()">← Back to All Ingredients</button>
                </div>
                <div class="row g-4" id="variantList"></div>
            </div>
        </div>
    </div>