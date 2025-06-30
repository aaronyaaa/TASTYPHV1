<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
$pagesWithHigherButton = ['supplier_store.php', 'home.php'];
$bottomValue = in_array($currentPage, $pagesWithHigherButton) ? '100px' : '20px';

require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';

$user_id = $_SESSION['userId'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Recipe Assistant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --offcanvas-bg: #f9f9f9;
      --offcanvas-accent: #7B4397;
    }
    .recipe-canvas .offcanvas {
      background-color: var(--offcanvas-bg);
      z-index: 1051;
    }
    .recipe-canvas .offcanvas-header {
      background-color: var(--offcanvas-accent);
      color: white;
    }
    .recipe-canvas .floating-recipe-btn {
      position: fixed;
      bottom: <?= $bottomValue ?>;
      right: 25px;
      width: 60px;
      height: 60px;
      z-index: 1052;
    }
    .ai-response-card {
      background: #fff;
      border-left: 4px solid var(--offcanvas-accent);
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="recipe-canvas">

<!-- Floating Button -->
<button class="btn btn-primary rounded-circle shadow-lg floating-recipe-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMLRecipes" aria-controls="offcanvasMLRecipes">
  <i class="fas fa-robot"></i>
</button>

<!-- Offcanvas ML Suggestion Panel -->
<div class="offcanvas offcanvas-end" id="offcanvasMLRecipes" tabindex="-1" aria-labelledby="offcanvasMLRecipesLabel" data-bs-scroll="true" data-bs-backdrop="false">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasMLRecipesLabel">🤖 AI Recipe Assistant</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <?php if (!$user_id): ?>
      <div class='alert alert-danger'>User not logged in.</div>
    <?php else: ?>
      <form id="mlForm" class="mb-3">
        <label for="mlInput" class="form-label">Tell me what you want to cook 👩‍🍳</label>
        <input type="text" class="form-control" id="mlInput" placeholder="e.g. I want to cook turon">
        <button type="submit" class="btn btn-success mt-2 w-100">
          <i class="fas fa-brain me-1"></i> Get AI Suggestion
        </button>
      </form>

      <div id="mlResults" style="display:none;">
        <div class="ai-response-card">
          <h5 id="recipeTitle" class="fw-bold"></h5>
          <p><strong>🕒 Prep:</strong> <span id="prepTime"></span> &nbsp;
             <strong>🔥 Cook:</strong> <span id="cookTime"></span> &nbsp;
             <strong>🍽️ Serves:</strong> <span id="servings"></span></p>

          <h6 class="mt-3">📋 Ingredients</h6>
          <ul id="ingredientList" class="list-group mb-3"></ul>

          <div id="missingSection" class="alert alert-warning" style="display:none;"></div>

          <h6 class="mt-3">🧑‍🍳 Steps</h6>
          <p id="stepsText"></p>
        </div>
      </div>

      <script>
        document.getElementById('mlForm').addEventListener('submit', async function(e) {
          e.preventDefault();
          const input = document.getElementById('mlInput').value.toLowerCase();
          const response = await fetch('/tastyphv1/ml/suggest.php?q=' + encodeURIComponent(input));
          const data = await response.json();

          if (data && !data.error) {
            document.getElementById('mlResults').style.display = 'block';
            document.getElementById('recipeTitle').innerText = data.recipe_title;
            document.getElementById('prepTime').innerText = data.prep_time;
            document.getElementById('cookTime').innerText = data.cook_time;
            document.getElementById('servings').innerText = data.servings;
            document.getElementById('stepsText').innerText = data.steps;

            const list = document.getElementById('ingredientList');
            list.innerHTML = '';
            data.ingredients.forEach(i => {
              const li = document.createElement('li');
              li.className = 'list-group-item';
              li.innerHTML = `<i class='fas fa-leaf text-success me-1'></i>` + i;
              list.appendChild(li);
            });

            const missing = data.missing;
            const missingSection = document.getElementById('missingSection');
            if (missing && missing.length) {
              missingSection.style.display = 'block';
              missingSection.innerHTML = `<strong>🛒 You need to buy:</strong> ` + missing.join(', ');
            } else {
              missingSection.style.display = 'none';
            }
          }
        });
      </script>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
