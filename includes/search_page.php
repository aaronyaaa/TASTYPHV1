<?php
include_once("../database/db_connect.php");
include_once("../database/session.php");

$search = $_GET['q'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$results = [];
$total = 0;
$totalPages = 1;

if ($search) {
    $like = "%$search%";

    // Count total
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM ingredients WHERE ingredient_name LIKE ?");
    $countStmt->execute([$like]);
    $total = $countStmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    // Full Results
    $stmt = $pdo->prepare("(
        SELECT 'Ingredient' AS type, ingredient_id, ingredient_name AS name, image_url, price, NULL AS seller_id, supplier_id
        FROM ingredients WHERE ingredient_name LIKE ? LIMIT $offset, $limit
    ) UNION (
        SELECT 'Store' AS type, NULL AS ingredient_id, business_name AS name, profile_pics AS image_url, NULL AS price, seller_id, NULL AS supplier_id
        FROM seller_applications WHERE business_name LIKE ? LIMIT $offset, $limit
    )");
    $stmt->execute([$like, $like]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/ingredient.css">
  <link rel="stylesheet" href="../assets/css/user_navbar.css">
  <style>
    .ingredient-card-1 {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s;
      cursor: pointer;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .ingredient-card-1:hover {
      transform: scale(1.02);
    }

    .ingredient-image-1 {
      height: 180px;
      object-fit: cover;
      width: 100%;
    }

    .card-body {
      padding: 1rem;
      flex-grow: 1;
    }

    .ingredient-title {
      font-size: 1.1rem;
      font-weight: bold;
      margin-bottom: 0.3rem;
    }

    .ingredient-description {
      font-size: 0.9rem;
      color: #555;
    }

    .price-tag {
      font-weight: bold;
      font-size: 1rem;
      color: #d63031;
    }

    .pagination {
      margin-top: 2rem;
    }
  </style>
</head>
<body>
<?php include '../includes/nav/navbar_router.php'; ?>

<div class="container mt-5 pt-4">
  <?php if ($search): ?>
    <?php if (!empty($results)): ?>
      <div class="row g-4">
        <?php foreach ($results as $item): ?>
          <?php
            $url = '#';
            if ($item['type'] === 'Ingredient' && $item['ingredient_id']) {
              $url = "../users/ingredient_page.php?ingredient_id=" . $item['ingredient_id'];
            }
          ?>
          <div class="col-md-4 col-sm-6">
            <a href="<?= $url ?>" class="text-decoration-none text-dark">
              <div class="ingredient-card-1">
                <img src="<?= !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../assets/images/default-category.png' ?>"
                     alt="<?= htmlspecialchars($item['name']) ?>"
                     class="ingredient-image-1">

                <div class="card-body">
                  <h5 class="ingredient-title"><?= htmlspecialchars($item['name']) ?></h5>
                  <p class="ingredient-description"><?= htmlspecialchars($item['type']) ?></p>
                  <?php if (!empty($item['price'])): ?>
                    <p class="price-tag mb-0">₱<?= number_format($item['price'], 2) ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <nav aria-label="Search result pages" class="mt-4">
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

    <?php else: ?>
      <p class="text-muted">No results found for "<?= htmlspecialchars($search) ?>"</p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
