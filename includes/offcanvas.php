<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
$bottomValue = in_array($currentPage, ['supplier_store.php', 'home.php']) ? '100px' : '20px';
require_once __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../database/session.php';
$user_id = $_SESSION['userId'] ?? null;
?>

<div class="tastyph-offcanvas">
  <!-- Unified Floating Button -->
  <button class="btn tastyph-offcanvas-fab rounded-circle shadow-lg position-fixed"
    style="bottom: <?= $bottomValue ?>; right: 25px; width: 60px; height: 60px; z-index: 1052;"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#offcanvasCombo"
    aria-controls="offcanvasCombo">
    <i class="fas fa-book-open"></i>
  </button>

  <!-- Combined Offcanvas with Tabs -->
  <div class="offcanvas offcanvas-end tastyph-offcanvas-panel" tabindex="-1" id="offcanvasCombo" aria-labelledby="offcanvasComboLabel" data-bs-scroll="true" data-bs-backdrop="false">
    <div class="offcanvas-header tastyph-offcanvas-header">
      <h5 class="offcanvas-title tastyph-offcanvas-title" id="offcanvasComboLabel">Recipe Tools</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body tastyph-offcanvas-body">
      <ul class="nav nav-tabs mb-3 tastyph-offcanvas-tabs" id="recipeTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active tastyph-offcanvas-tab" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab">My Recipes</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link tastyph-offcanvas-tab" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai" type="button" role="tab">AI Assistant</button>
        </li>
      </ul>
      <div class="tab-content tastyph-offcanvas-tab-content" id="recipeTabContent">
        <!-- My Recipes Tab -->
        <div class="tab-pane fade show active tastyph-offcanvas-tabpane" id="saved" role="tabpanel">
          <?php
          if (!$user_id) {
            echo "<div class='alert alert-danger'>User not logged in.</div>";
          } else {
            $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>
            <div class="accordion tastyph-offcanvas-accordion" id="recipeAccordion">
              <?php foreach ($recipes as $index => $recipe): ?>
                <div class="accordion-item tastyph-offcanvas-accordion-item">
                  <h2 class="accordion-header tastyph-offcanvas-accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button collapsed tastyph-offcanvas-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
                      <?= htmlspecialchars($recipe['title']) ?>
                    </button>
                  </h2>
                  <div id="collapse<?= $index ?>" class="accordion-collapse collapse tastyph-offcanvas-accordion-collapse">
                    <div class="accordion-body tastyph-offcanvas-accordion-body">
                      <?php if (!empty($recipe['recipe_image'])): ?>
                        <img src="../<?= htmlspecialchars($recipe['recipe_image']) ?>" class="img-fluid rounded mb-2 tastyph-offcanvas-img" style="max-height: 160px; object-fit: cover;">
                      <?php endif; ?>
                      <p><strong>Prep Time:</strong> <?= htmlspecialchars($recipe['prep_time']) ?></p>
                      <p><strong>Cook Time:</strong> <?= htmlspecialchars($recipe['cook_time']) ?></p>
                      <p><strong>Servings:</strong> <?= htmlspecialchars($recipe['servings']) ?></p>
                      <h6>Ingredients:</h6>
                      <ul class="list-group mb-2 tastyph-offcanvas-ingredient-list">
                        <?php
                        $i_stmt = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?");
                        $i_stmt->execute([$recipe['recipe_id']]);
                        foreach ($i_stmt as $ing):
                        ?>
                          <li class="list-group-item p-2 tastyph-offcanvas-ingredient-item">
                            <?= htmlspecialchars($ing['quantity_value']) . ' ' . $ing['unit_type'] . ' — ' ?>
                            <a href="/tastyphv1/includes/search_page.php?q=<?= urlencode($ing['ingredient_name']) ?>" class="text-decoration-none tastyph-offcanvas-ingredient-link">
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

        <!-- AI Assistant Tab -->
        <div class="tab-pane fade tastyph-offcanvas-tabpane" id="ai" role="tabpanel">
          <?php if (!$user_id): ?>
            <div class='alert alert-danger'>User not logged in.</div>
          <?php else: ?>
            <form id="mlForm" class="mb-3 tastyph-offcanvas-mlform">
              <label for="mlInput" class="form-label tastyph-offcanvas-mllabel">Tell me what you want to cook 👩‍🍳</label>
              <input type="text" class="form-control tastyph-offcanvas-mlinput" id="mlInput" placeholder="e.g. I want to cook turon">
              <button type="submit" class="btn tastyph-offcanvas-mlbtn mt-2 w-100">
                <i class="fas fa-brain me-1"></i> Get AI Suggestion
              </button>
            </form>
            <div id="mlResults" style="display:none;">
              <div class="ai-response-card tastyph-offcanvas-airesponse">
                <h5 id="recipeTitle" class="fw-bold tastyph-offcanvas-aititle">AI Recipe</h5>
                <p><strong>🕒 Prep:</strong> <span id="prepTime">N/A</span> &nbsp;
                  <strong>🔥 Cook:</strong> <span id="cookTime">N/A</span> &nbsp;
                  <strong>🍽️ Serves:</strong> <span id="servings">N/A</span>
                </p>
                <h6 class="mt-3 tastyph-offcanvas-aisub">📋 Ingredients</h6>
                <ul id="ingredientList" class="list-group mb-3 ingredients-section tastyph-offcanvas-ailingredients"></ul>
                <div id="missingSection" class="alert alert-warning tastyph-offcanvas-aimissing" style="display:none;"></div>
                <h6 class="mt-3 tastyph-offcanvas-aisub">🧑‍🍳 Steps</h6>
                <ul id="stepsText" class="steps-text tastyph-offcanvas-aisteps"></ul>
                <div id="notesSection" class="note-section tastyph-offcanvas-ainotes"></div>
                <div class="mt-3 d-flex gap-2 tastyph-offcanvas-aibtns">
                  <button class="btn btn-outline-secondary btn-sm tastyph-offcanvas-copybtn" onclick="copyRecipe()"><i class="fas fa-copy me-1"></i>Copy</button>
                  <button class="btn btn-outline-primary btn-sm tastyph-offcanvas-savebtn"><i class="fas fa-save me-1"></i>Save to My Recipes</button>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
:root {
  --tastyph-violet: #7B4397;
  --tastyph-violet-light: #b084cc;
  --tastyph-white: #fff;
  --tastyph-gray: #f6f6fa;
  --tastyph-dark: #232b32;
}
.tastyph-offcanvas-fab {
  background: var(--tastyph-violet);
  color: #fff;
  border: none;
  box-shadow: 0 6px 24px rgba(123,67,151,0.18), 0 1.5px 6px rgba(0,0,0,0.10);
  font-size: 1.6rem;
  transition: background 0.18s, box-shadow 0.18s, color 0.18s;
}
.tastyph-offcanvas-fab:hover, .tastyph-offcanvas-fab:focus {
  background: var(--tastyph-violet-light);
  color: var(--tastyph-dark);
  box-shadow: 0 8px 32px rgba(123,67,151,0.22), 0 2px 8px rgba(0,0,0,0.13);
}
.tastyph-offcanvas-panel {
  border-top-left-radius: 22px;
  border-bottom-left-radius: 22px;
  box-shadow: -8px 0 32px rgba(123,67,151,0.10);
  min-width: 370px;
  max-width: 95vw;
  background: var(--tastyph-white);
}
.tastyph-offcanvas-header {
  border-top-left-radius: 22px;
  border-bottom: 1px solid var(--tastyph-violet-light);
  background: var(--tastyph-violet);
  color: #fff;
}
.tastyph-offcanvas-title {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: 0.01em;
}
.tastyph-offcanvas-body {
  background: var(--tastyph-white);
  color: #232b32;
  border-bottom-left-radius: 22px;
  min-height: 60vh;
}
.tastyph-offcanvas-tabs .nav-link {
  color: var(--tastyph-violet);
  font-weight: 600;
  border: none;
  background: none;
  border-radius: 0;
  font-size: 1.08rem;
  margin-right: 0.5rem;
  transition: color 0.18s, border-bottom 0.18s;
}
.tastyph-offcanvas-tabs .nav-link.active, .tastyph-offcanvas-tabs .nav-link:focus {
  color: var(--tastyph-white);
  background: var(--tastyph-violet);
  border-bottom: 3px solid var(--tastyph-violet-light);
}
.tastyph-offcanvas-accordion-item {
  border-radius: 12px;
  margin-bottom: 1rem;
  box-shadow: 0 2px 10px rgba(123,67,151,0.07);
  background: var(--tastyph-white);
  border: 1px solid #eee;
}
.tastyph-offcanvas-accordion-btn {
  background: var(--tastyph-white);
  color: var(--tastyph-violet);
  font-weight: 600;
  border-radius: 12px 12px 0 0;
  font-size: 1.08rem;
  transition: background 0.18s, color 0.18s;
}
.tastyph-offcanvas-accordion-btn:not(.collapsed) {
  background: var(--tastyph-violet);
  color: #fff;
}
.tastyph-offcanvas-accordion-body {
  background: var(--tastyph-white);
  color: #232b32;
  border-radius: 0 0 12px 12px;
}
.tastyph-offcanvas-img {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(123,67,151,0.10);
}
.tastyph-offcanvas-ingredient-link {
  color: var(--tastyph-violet);
  font-weight: 500;
}
.tastyph-offcanvas-ingredient-link:hover {
  color: var(--tastyph-violet-light);
  text-decoration: underline;
}
.tastyph-offcanvas-mlform {
  background: var(--tastyph-gray);
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 2px 8px rgba(123,67,151,0.07);
}
.tastyph-offcanvas-mlinput {
  background: var(--tastyph-white);
  color: #232b32;
  border: 1px solid var(--tastyph-violet-light);
  border-radius: 8px;
}
.tastyph-offcanvas-mlinput:focus {
  border-color: var(--tastyph-violet);
  background: var(--tastyph-white);
  color: #232b32;
}
.tastyph-offcanvas-mlbtn {
  background: var(--tastyph-violet);
  color: #fff;
  font-weight: 700;
  border-radius: 2rem;
  border: none;
  transition: background 0.18s, color 0.18s;
}
.tastyph-offcanvas-mlbtn:hover, .tastyph-offcanvas-mlbtn:focus {
  background: var(--tastyph-violet-light);
  color: #232b32;
}
.tastyph-offcanvas-airesponse {
  background: var(--tastyph-gray);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(123,67,151,0.07);
  padding: 1.2rem;
  color: #232b32;
}
.tastyph-offcanvas-aititle {
  color: var(--tastyph-violet);
  font-size: 1.2rem;
  font-weight: 700;
}
.tastyph-offcanvas-aisub {
  color: var(--tastyph-violet-light);
  font-size: 1.05rem;
  font-weight: 600;
}
.tastyph-offcanvas-copybtn, .tastyph-offcanvas-savebtn {
  border-radius: 2rem;
  font-weight: 600;
  font-size: 0.98rem;
}
.tastyph-offcanvas-copybtn {
  border-color: var(--tastyph-violet);
  color: var(--tastyph-violet);
}
.tastyph-offcanvas-copybtn:hover {
  background: var(--tastyph-violet);
  color: #fff;
}
.tastyph-offcanvas-savebtn {
  border-color: var(--tastyph-violet-light);
  color: var(--tastyph-violet-light);
}
.tastyph-offcanvas-savebtn:hover {
  background: var(--tastyph-violet-light);
  color: #232b32;
}
@media (max-width: 600px) {
  .tastyph-offcanvas-panel {
    min-width: 90vw;
    border-radius: 18px 0 0 18px;
  }
}
</style>

<script>
  document.getElementById('mlForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const input = document.getElementById('mlInput').value.trim();
    if (!input) return;

    document.getElementById('mlResults').style.display = 'block';
    document.getElementById('ingredientList').innerHTML = '';
    document.getElementById('stepsText').innerHTML = '';
    document.getElementById('notesSection').innerHTML = '';

    const stepsText = document.getElementById('stepsText');
    const ingredientsList = document.getElementById('ingredientList');
    const notesSection = document.getElementById('notesSection');

    stepsText.innerHTML = '<li>Loading...</li>';

    const response = await fetch('/tastyphv1/ml/suggest.php?q=' + encodeURIComponent(input));
    const data = await response.json();

    if (data && !data.error && data.steps) {
      document.getElementById('recipeTitle').innerText = data.recipe_title || 'AI Recipe';

      const fullText = data.steps.replace(/\*|•/g, '\n•');
      const ingredientsMatch = fullText.match(/ingredients[:\n]*([\s\S]*?)instructions[:\n]|steps[:\n]/i);
      const stepsMatch = fullText.match(/(instructions|steps)[:\n]*([\s\S]*)/i);
      const notesMatch = fullText.match(/notes[:\n]*([\s\S]*)/i);

      const ingredientsText = ingredientsMatch ? ingredientsMatch[1].trim() : '';
      const stepsRaw = stepsMatch ? stepsMatch[2].trim() : fullText;
      const notesText = notesMatch ? notesMatch[1].trim() : '';

      ingredientsText.split(/\r?\n/).forEach(line => {
        if (line.trim()) {
          const li = document.createElement('li');
          li.className = 'list-group-item';
          li.innerHTML = `<i class='fas fa-leaf text-success me-1'></i>` + line.trim();
          ingredientsList.appendChild(li);
        }
      });

      stepsText.innerHTML = '';
      let stepBuffer = '';
      let index = 0;
      const typeInterval = setInterval(() => {
        if (index >= stepsRaw.length) {
          if (stepBuffer.trim()) {
            const li = document.createElement('li');
            li.innerText = stepBuffer.trim();
            stepsText.appendChild(li);
          }
          clearInterval(typeInterval);
          return;
        }
        const char = stepsRaw.charAt(index);
        stepBuffer += char;
        if (char === '\n' || char === '.' || index === stepsRaw.length - 1) {
          if (stepBuffer.trim().length > 4) {
            const li = document.createElement('li');
            li.innerText = stepBuffer.trim();
            stepsText.appendChild(li);
            stepBuffer = '';
          }
        }
        index++;
      }, 15);

      if (notesText) {
        notesSection.innerHTML = `<strong>📝 Notes:</strong><br>${notesText.replace(/\n/g, '<br>')}`;
      }
    } else {
      document.getElementById('stepsText').innerHTML = '<li>⚠️ ' + (data.error || 'No reply from AI') + '</li>';
    }
  });

  function copyRecipe() {
    const ingredients = [...document.querySelectorAll('#ingredientList li')].map(li => li.innerText).join('\n');
    const steps = [...document.querySelectorAll('#stepsText li')].map(li => li.innerText).join('\n');
    const text = 'Ingredients:\n' + ingredients + '\n\nSteps:\n' + steps;
    navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'));
  }
</script>