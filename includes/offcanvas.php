<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
$pagesWithHigherButton = ['supplier_store.php', 'home.php'];
$bottomValue = in_array($currentPage, $pagesWithHigherButton) ? '100px' : '20px';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Offcanvas Recipes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS for isolation -->
  <style>
    :root {
      --offcanvas-bg: #f9f9f9;
      --offcanvas-accent: #7B4397 ;
    }

    .recipe-canvas .offcanvas {
      background-color: var(--offcanvas-bg);
      z-index: 1051;
    }

    .recipe-canvas .offcanvas-header {
      background-color: var(--offcanvas-accent);
      color: white;
    }

    .recipe-canvas .offcanvas-title {
      font-weight: bold;
    }

    .recipe-canvas .btn-close {
      filter: invert(1);
    }

    .recipe-canvas .accordion-button {
      background-color: #fff;
      color: #000;
    }

    .recipe-canvas .accordion-button:not(.collapsed) {
      background-color: var(--offcanvas-accent);
      color: #fff;
    }

    .recipe-canvas .list-group-item {
      font-size: 0.95rem;
    }

    .recipe-canvas .floating-recipe-btn {
      position: fixed;
      bottom: <?= $bottomValue ?>;
      right: 25px;
      width: 60px;
      height: 60px;
      z-index: 1052;
    }
  </style>
</head>
<body class="recipe-canvas">

<!-- Floating Button -->
<button class="btn btn-primary rounded-circle shadow-lg floating-recipe-btn"
  type="button"
  data-bs-toggle="offcanvas"
  data-bs-target="#offcanvasRecipesRight"
  aria-controls="offcanvasRecipesRight">
  <i class="fas fa-utensils"></i>
</button>

<!-- Right Side Offcanvas -->
<div class="offcanvas offcanvas-end" id="offcanvasRecipesRight" tabindex="-1" aria-labelledby="offcanvasRecipesRightLabel" data-bs-scroll="true" data-bs-backdrop="false">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRecipesRightLabel">Your Recipes</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">

    <?php
    require_once __DIR__ . '/../database/db_connect.php';
    require_once __DIR__ . '/../database/session.php';

    $user_id = $_SESSION['userId'] ?? null;

    if (!$user_id) {
      echo "<div class='alert alert-danger'>User not logged in.</div>";
    } else {
      $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
      $stmt->execute([$user_id]);
      $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="accordion" id="recipeAccordion">
      <?php foreach ($recipes as $index => $recipe): ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
              <?= htmlspecialchars($recipe['title']) ?>
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>">
            <div class="accordion-body">
              <?php if (!empty($recipe['recipe_image'])): ?>
                <img src="../<?= htmlspecialchars($recipe['recipe_image']) ?>" class="img-fluid rounded mb-2" alt="Recipe Image" style="max-height: 160px; object-fit: cover;">
              <?php endif; ?>
              <p><strong>Prep Time:</strong> <?= htmlspecialchars($recipe['prep_time']) ?></p>
              <p><strong>Cook Time:</strong> <?= htmlspecialchars($recipe['cook_time']) ?></p>
              <p><strong>Servings:</strong> <?= htmlspecialchars($recipe['servings']) ?></p>
              <h6>Ingredients:</h6>
              <ul class="list-group mb-2">
                <?php
                $i_stmt = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?");
                $i_stmt->execute([$recipe['recipe_id']]);
                foreach ($i_stmt as $ing):
                ?>
                  <li class="list-group-item">
                    <?= htmlspecialchars($ing['quantity_value']) . ' ' . $ing['unit_type'] . ' — ' ?>
                    <a href="/tastyphv1/includes/search_page.php?q=<?= urlencode($ing['ingredient_name']) ?>" class="text-decoration-none">
                      <?= htmlspecialchars($ing['ingredient_name']) ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php } ?>
  </div>
</div>

<!-- Bootstrap JS -->
</body>
</html>
