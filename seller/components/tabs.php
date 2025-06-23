<div class="d-flex">
  <!-- Sidebar Navigation -->
  <div class="nav flex-column nav-pills me-3" id="quickTabs" role="tablist" style="width: 250px;">
    <!-- 🥇 Products -->
    <li class="nav-item" role="presentation">
      <button class="nav-link active tab-btn tab-products" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
        <i class="fa-solid fa-cubes me-1"></i> Products
      </button>
    </li>

    <!-- 🥈 Inventory -->
    <li class="nav-item" role="presentation">
      <button class="nav-link tab-btn tab-inventory" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
        <i class="fa-solid fa-box-archive me-1"></i> Ingredients Inventory
      </button>
    </li>

    <!-- 🥉 Recipes -->
    <li class="nav-item" role="presentation">
      <button class="nav-link tab-btn tab-recipes" id="recipes-tab" data-bs-toggle="tab" data-bs-target="#recipes" type="button" role="tab">
        <i class="fa-solid fa-book me-1"></i> Recipes
      </button>
    </li>

    <!-- 🏷️ Category -->
    <li class="nav-item" role="presentation">
      <button class="nav-link tab-btn tab-category" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab">
        <i class="fa-solid fa-tags me-1"></i> Add Category
      </button>
    </li>

    <!-- 🕒 Hours -->
    <li class="nav-item" role="presentation">
      <button class="nav-link tab-btn tab-hours" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours" type="button" role="tab">
        <i class="fa-solid fa-clock me-1"></i> Business Hours
      </button>
    </li>
  </div>

  <!-- Tab Content -->
  <div class="tab-content px-3" style="flex-grow: 1;">
    <!-- 🥇 Products Tab -->
    <div class="tab-pane fade show active" id="products" role="tabpanel">
      <?php include('products.php'); ?>
    </div>

    <!-- 🥈 Inventory Tab -->
    <div class="tab-pane fade" id="inventory" role="tabpanel">
      <?php include('inventory.php'); ?>
    </div>

    <!-- 🥉 Recipes Tab -->
    <div class="tab-pane fade" id="recipes" role="tabpanel">
      <?php include('recipes.php'); ?>
    </div>

    <!-- 🏷️ Add Category Tab -->
    <div class="tab-pane fade" id="category" role="tabpanel">
      <?php include('categories.php'); ?>
    </div>

    <!-- 🕒 Business Hours Tab -->
    <div class="tab-pane fade" id="hours" role="tabpanel">
      <p class="lead">Business hours content goes here.</p>
    </div>
  </div>
</div>
