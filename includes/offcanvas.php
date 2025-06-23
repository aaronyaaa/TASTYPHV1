<?php
  // Get current file name from URL
  $currentPage = basename($_SERVER['REQUEST_URI']);
  $pagesWithHigherButton = ['supplier_store.php', 'home.php'];
  $bottomValue = in_array($currentPage, $pagesWithHigherButton) ? '100px' : '20px';
?>


<!-- Floating Button to trigger Offcanvas -->
<button class="btn btn-primary rounded-circle shadow-lg position-fixed"
  style="bottom: <?= $bottomValue ?>; right: 25px; width: 60px; height: 60px; z-index: 1051;"
  type="button"
  data-bs-toggle="offcanvas"
  data-bs-target="#offcanvasRecipesRight"
  aria-controls="offcanvasRecipesRight">
  <i class="fas fa-utensils"></i>
</button>

<!-- Right Side Offcanvas -->
<div class="offcanvas offcanvas-end"
  tabindex="-1"
  id="offcanvasRecipesRight"
  aria-labelledby="offcanvasRecipesRightLabel"
  data-bs-scroll="true"
  data-bs-backdrop="false">
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
      return;
    }

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
                  <li class="list-group-item p-2">
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
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const floatingBtn = document.querySelector('[data-bs-target="#offcanvasRecipesRight"]');
    const path = window.location.pathname;

    if (path.includes('supplier_store.php') || path.includes('home.php')) {
      floatingBtn.style.bottom = '100px';
    } else {
      floatingBtn.style.bottom = '20px';
    }
  });
</script>

