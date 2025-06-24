<div class="container py-4">
    <!-- Fixed Horizontal Scrollable Category Bar -->
    <div class="container py-4">
        <!-- Search and category filter -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
            </div>
        </div>
        <!-- Product & Variant Section Wrapper -->
        <div id="productList">
            <div id="productSection">
                <div class="row g-4" id="productGrid">
                    <?php
                    $_GET['seller_id'] = $seller_id;
                    include '../backend/seller/fetch_products.php';
                    ?>
                </div>
            </div>
            <div id="variantSection" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 id="variantTitle">Variants of Product</h4>
                    <button class="btn btn-secondary btn-sm" onclick="backToProducts()">← Back to All Products</button>
                </div>
                <div class="row g-4" id="variantList"></div>
            </div>
        </div>
    </div>
</div> 