<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ingredient Categories</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>


    <!-- Tabs Nav -->

    <!-- Tabs Content -->
    <div class="tab-content mt-3" id="myTabContent">
      <div class="tab-pane fade show active" id="category" role="tabpanel" aria-labelledby="category-tab">
        <div class="container text-start" style="max-width: 900px;">
          
          <!-- Header & Add Button -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
              <i class="fa-solid fa-tags me-2 text-success"></i> Ingredient Categories
            </h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
              <i class="fa-solid fa-plus me-1"></i> New Category
            </button>
          </div>

          <!-- Category Modal (Create) -->
          <?php include('modal/category_modal.php'); ?>

          <!-- Category Grid -->
          <div class="row g-3" id="categoryList">
            <?php include('../backend/seller/fetch_categories.php'); ?>
          </div>
        </div>
      </div>
    </div>



  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
