<?php
include_once("../database/db_connect.php");
include_once("../database/session.php");

$originalSearch = trim($_GET['q'] ?? '');
$search = strtolower($originalSearch);

// Basic plural logic
$altSearch = $search;
if (substr($search, -1) === 's') {
  $altSearch = rtrim($search, 's');
} elseif (!str_ends_with($search, 's')) {
  $altSearch .= 's';
}

$like = "%$search%";
$altLike = "%$altSearch%";
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 18;
$offset = ($page - 1) * $limit;

$results = [];
$total = 0;
$totalPages = 1;

if ($search) {
  $countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM (
      SELECT ingredient_id FROM ingredients WHERE ingredient_name LIKE ? OR ingredient_name LIKE ?
      UNION
      SELECT seller_id FROM seller_applications WHERE business_name LIKE ? OR business_name LIKE ?
    ) AS total
  ");
  $countStmt->execute([$like, $altLike, $like, $altLike]);
  $total = $countStmt->fetchColumn();
  $totalPages = ceil($total / $limit);

  $stmt = $pdo->prepare("(
      SELECT 'Ingredient' AS type, ingredient_id, ingredient_name AS name, image_url, price, NULL AS seller_id, supplier_id
      FROM ingredients 
      WHERE ingredient_name LIKE ? OR ingredient_name LIKE ?
      LIMIT $offset, $limit
    ) UNION (
      SELECT 'Store' AS type, NULL AS ingredient_id, business_name AS name, profile_pics AS image_url, NULL AS price, seller_id, NULL AS supplier_id
      FROM seller_applications 
      WHERE business_name LIKE ? OR business_name LIKE ?
      LIMIT $offset, $limit
    )
  ");
  $stmt->execute([$like, $altLike, $like, $altLike]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom Card Styles -->
  <style>

    .ingredient-card-1 {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 8px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .ingredient-card-1:hover {
      transform: translateY(-3px);
    }

    .ingredient-card-1 img {
      width: 100%;
      height: 140px;
      object-fit: contain;
      background-color: #fafafa;
      border-bottom: 1px solid #eee;
    }

    .ingredient-card-1 .card-body {
      padding: 0.8rem 1rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex-grow: 1;
    }

    .ingredient-title {
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.2rem;
      color: #333;
    }

    .ingredient-description {
      font-size: 0.75rem;
      color: #888;
      margin-bottom: 0.4rem;
    }

    .price-tag {
      font-weight: 700;
      font-size: 0.85rem;
      color: #d63031;
    }
  </style>
</head>

<body>
  <?php include '../includes/nav/navbar_router.php'; ?>
  <?php include 'offcanvas.php'; ?>

  <div class="container mt-5 pt-4">
    <?php if ($search): ?>
      <h4 class="mb-4">Results for: "<?= htmlspecialchars($originalSearch) ?>"</h4>

      <?php if (!empty($results)): ?>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
          <?php foreach ($results as $item): ?>
            <?php
              $url = '#';
              if ($item['type'] === 'Ingredient' && $item['ingredient_id']) {
                $url = "../users/ingredient_page.php?ingredient_id=" . $item['ingredient_id'];
              } elseif ($item['type'] === 'Store' && $item['seller_id']) {
                $url = "seller_store.php?seller_id=" . $item['seller_id'];
              }
            ?>
            <div class="col">
              <a href="<?= $url ?>" class="text-decoration-none text-dark">
                <div class="ingredient-card-1">
                  <img src="<?= !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../assets/images/default-category.png' ?>"
                       alt="<?= htmlspecialchars($item['name']) ?>"
                       class="ingredient-image-1">

                  <div class="card-body">
                    <h5 class="ingredient-title"><?= htmlspecialchars($item['name']) ?></h5>
                    <p class="ingredient-description"><?= $item['type'] ?></p>
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
        <nav class="mt-4" aria-label="Search pagination">
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
        <p class="text-muted">No results found for "<?= htmlspecialchars($originalSearch) ?>"</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
