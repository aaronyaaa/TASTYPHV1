<div class="container-fluid">
  <div class="row min-vh-100">
    <!-- Sidebar -->
    <div class="col-md-3 border-end bg-white shadow-sm py-4 px-3">
      <h5 class="fw-bold text-dark mb-4">⚡ Quick Actions</h5>

      <ul class="nav nav-pills flex-column gap-2" id="quickTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active d-flex align-items-center gap-2" id="ingredients-tab" data-bs-toggle="tab" data-bs-target="#ingredients" type="button" role="tab">
            <i class="fa-solid fa-carrot text-success"></i> <span>Ingredients</span>
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-2" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
            <i class="fa-solid fa-box-archive text-primary"></i> <span>Inventory</span>
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-2" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab">
            <i class="fa-solid fa-tags text-warning"></i> <span>Categories</span>
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-2" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours" type="button" role="tab">
            <i class="fa-solid fa-clock text-secondary"></i> <span>Business Hours</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 py-4 px-4 bg-light">
      <div class="tab-content rounded shadow-sm bg-white p-4" id="quickTabsContent" style="min-height: 80vh;">
        <div class="tab-pane fade show active" id="ingredients" role="tabpanel">
          <?php include('ingredients.php'); ?>
        </div>



          <?php include('categories.php'); ?>


        <div class="tab-pane fade" id="hours" role="tabpanel">
          <p class="lead text-muted">Business hours content goes here.</p>
        </div>
      </div>
    </div>
  </div>
</div>
