<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$user_id = $_SESSION['userId'] ?? null;

if (!$user_id) {
  echo "<div class='alert alert-danger'>User not logged in.</div>";
  return;
}

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
/* Card Enhancements */
.card {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
}

.card-text {
  font-size: 0.9rem;
  color: #555;
}

/* Modal Enhancements */
.modal-header {
  border-bottom: none;
}

.modal-title {
  font-weight: 600;
}

.modal-body h6 {
  font-size: 1rem;
  font-weight: 600;
  margin-top: 1rem;
  color: #333;
}

.modal-body ul,
.modal-body ol {
  padding-left: 1rem;
}

.modal-body li {
  font-size: 0.95rem;
  margin-bottom: 0.4rem;
}

/* Icons & Layout */
i.fas {
  color: #555;
}

.list-group-item {
  border: none;
  padding-left: 0;
  font-size: 0.95rem;
}

/* Responsive Image */
.card-img-top,
.modal-body img {
  border-radius: 8px;
}

/* Button Styling */
.btn-primary,
.btn-success {
  border-radius: 8px;
  font-weight: 500;
}

.btn-outline-primary {
  border-radius: 6px;
}

/* ===== Card UI ===== */
.card.recipe-card {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
}

.card.recipe-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.recipe-card img {
  height: 200px;
  object-fit: cover;
}

.recipe-card .card-title {
  font-size: 1.2rem;
  font-weight: 600;
}

.recipe-card .card-text {
  font-size: 0.9rem;
  color: #666;
}

.recipe-card .btn-sm {
  font-size: 0.85rem;
}

/* ===== Modal UI ===== */
.modal-content {
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.modal-header {
  border: none;
  padding: 1.2rem 1.5rem;
}

.modal-header .modal-title {
  font-weight: 600;
  font-size: 1.2rem;
}

.modal-body img {
  border-radius: 10px;
  width: 100%;
  height: auto;
  object-fit: cover;
  margin-bottom: 1rem;
}

.modal-body h6 {
  font-weight: 600;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
  color: #333;
}

.modal-body ul.list-group {
  border-radius: 8px;
}

.modal-body .list-group-item {
  font-size: 0.95rem;
  padding: 0.6rem 1rem;
  border: none;
  border-bottom: 1px solid #eee;
}

.modal-body ol {
  padding-left: 1.25rem;
}

.modal-body li {
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

.recipe-notes {
  background-color: #f8f9fa;
  border-left: 4px solid #0d6efd;
  padding: 1rem;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #444;
}

/* ===== Info Icons Row ===== */
.recipe-info-row {
  font-size: 0.9rem;
  color: #555;
  border-bottom: 1px solid #eee;
  padding: 0.75rem 0;
}

.recipe-info-row .col {
  border-right: 1px solid #eee;
}

.recipe-info-row .col:last-child {
  border-right: none;
}

/* ===== Button Tweaks ===== */
.btn {
  border-radius: 8px !important;
  font-weight: 500;
}

.btn-close {
  box-shadow: none;
}

/* Responsive fix for modal image */
@media (max-width: 768px) {
  .modal-body img {
    max-height: 250px;
  }
}

</style>

</head>

<body>

      <?php include('modal/edit_recipe_moda.php'); ?>

    <div class="container mt-5">
        <!-- Add Recipe Button -->
        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addRecipeModal">
            <i class="fas fa-plus-circle me-1"></i> Add New Recipe
        </button>
    </div>

    <div class="container">
        <div class="row">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <?php if (!empty($recipe['recipe_image'])): ?>
                            <img src="../<?= htmlspecialchars($recipe['recipe_image']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
                            <p class="card-text text-muted mb-1"><i class="fas fa-users me-1"></i> <?= htmlspecialchars($recipe['servings'] ?? 'N/A') ?> servings</p>
                            <p class="card-text text-muted"><i class="fas fa-clock me-1"></i> <?= htmlspecialchars($recipe['prep_time']) ?> prep • <?= htmlspecialchars($recipe['cook_time']) ?> cook</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewRecipeModal<?= $recipe['recipe_id'] ?>">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <!-- Edit and Delete Buttons -->
                            <button class="btn btn-warning btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#editRecipeModal<?= $recipe['recipe_id'] ?>">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="delete_recipe_id" value="<?= $recipe['recipe_id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Are you sure you want to delete this recipe?');">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="viewRecipeModal<?= $recipe['recipe_id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-book me-2"></i><?= htmlspecialchars($recipe['title']) ?></h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <?php if (!empty($recipe['recipe_image'])): ?>
                                    <img src="../<?= htmlspecialchars($recipe['recipe_image']) ?>" class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;">
                                <?php endif; ?>

                                <div class="row text-center mb-3">
                                    <div class="col">
                                        <i class="fas fa-users"></i>
                                        <div><small>Servings</small></div>
                                        <strong><?= htmlspecialchars($recipe['servings'] ?? 'N/A') ?></strong>
                                    </div>
                                    <div class="col">
                                        <i class="fas fa-clock"></i>
                                        <div><small>Prep Time</small></div>
                                        <strong><?= htmlspecialchars($recipe['prep_time']) ?></strong>
                                    </div>
                                    <div class="col">
                                        <i class="fas fa-fire"></i>
                                        <div><small>Cook Time</small></div>
                                        <strong><?= htmlspecialchars($recipe['cook_time']) ?></strong>
                                    </div>
                                </div>

                                <!-- Ingredients -->
                                <h6><i class="fas fa-list-ul me-1"></i>Ingredients</h6>
                                <ul class="list-group mb-3">
                                    <?php
                                    $i_stmt = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?");
                                    $i_stmt->execute([$recipe['recipe_id']]);
                                    foreach ($i_stmt as $ing):
                                    ?>
                                        <li class="list-group-item">
                                            <?= htmlspecialchars($ing['quantity_value']) . ' ' . $ing['unit_type'] . ' — ' . htmlspecialchars($ing['ingredient_name']) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <!-- Steps -->
                                <h6><i class="fas fa-clipboard-list me-1"></i>Steps</h6>
                                <ol class="mb-3">
                                    <?php
                                    $s_stmt = $pdo->prepare("SELECT * FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number ASC");
                                    $s_stmt->execute([$recipe['recipe_id']]);
                                    foreach ($s_stmt as $step):
                                    ?>
                                        <li class="mb-2"><?= htmlspecialchars($step['instruction']) ?></li>
                                    <?php endforeach; ?>
                                </ol>

                                <?php if (!empty($recipe['notes'])): ?>
                                    <h6><i class="fas fa-sticky-note me-1"></i>Notes</h6>
                                    <p><?= nl2br(htmlspecialchars($recipe['notes'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>