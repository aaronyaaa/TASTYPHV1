<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$seller_id = $_GET['seller_id'] ?? null;

// Fetch store details (public access)
$stmt = $pdo->prepare("SELECT business_name FROM seller_applications WHERE seller_id = ?");
$stmt->execute([$seller_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);
$storeName = $store['business_name'] ?? 'Seller';

// Handle optional filters
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_id'] ?? '';

// Build dynamic SQL to fetch products with category names
$sql = "
    SELECT 
        p.product_id,
        p.seller_id,
        p.category_id,
        p.product_name,
        p.slug,
        p.description,
        p.image_url,
        p.price,
        p.discount_price,
        p.stock,
        p.quantity_value,
        p.unit_type,
        p.is_active,
        p.rating,
        p.created_at,
        p.updated_at,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.seller_id = ?
";

$params = [$seller_id];

if ($search !== '') {
    $sql .= " AND p.product_name LIKE ?";
    $params[] = "%$search%";
}

if (!empty($categoryFilter)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $categoryFilter;
}

$sql .= " ORDER BY c.name ASC, p.product_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group products by category
$productsByCategory = [];
foreach ($products as $item) {
    $categoryName = $item['category_name'] ?? 'Uncategorized';
    $productsByCategory[$categoryName][] = $item;
}

if (empty($products)) {
    echo '<div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-box-open fa-3x text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">No Products Available</h5>
            <p class="text-muted small">' .
        (!empty($search) ? 'No products match your search criteria.' : (!empty($categoryFilter) ? 'This category is currently empty.' :
            'This seller has not added any products yet.')) .
        '</p>
          </div>';
    exit;
}
?>
<!-- HTML Output -->
<div id="productSection">
    <?php foreach ($productsByCategory as $categoryName => $items): ?>
        <h4 class="mt-4 mb-3"><?= htmlspecialchars($categoryName) ?></h4>
        <div class="ingredient-grid">
            <?php foreach ($items as $item): ?>
                <div>
                    <a href="../users/product_page.php?product_id=<?= $item['product_id'] ?>" class="text-decoration-none text-dark">
                        <div class="ingredient-card-1 hover-shadow">
                            <div class="position-relative">
                                <img src="<?= !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../assets/images/default-category.png' ?>"
                                    alt="<?= htmlspecialchars($item['product_name']) ?>"
                                    class="ingredient-image-1 w-100">
                                <?php if ($item['is_active'] == 0): ?>
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-secondary">Out of Stock</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="ingredient-title"><?= htmlspecialchars($item['product_name']) ?></h5>

                                <div class="small text-warning mb-1">
                                    <?php
                                    $rating = (float)($item['rating'] ?? 0);
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                    for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star"></i>';
                                    if ($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                                    for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '<i class="far fa-star"></i>';
                                    ?>
                                    <span class="text-muted ms-2">(<?= number_format($rating, 1) ?>)</span>
                                </div>

                                <p class="ingredient-description"><?= htmlspecialchars($item['description']) ?></p>

                                <div class="text-muted mb-2">
                                    <small>
                                        Stock: <?= (int)$item['stock'] ?> <?= htmlspecialchars($item['unit_type']) ?>
                                        (<?= number_format($item['quantity_value'], 2) ?> <?= htmlspecialchars($item['unit_type']) ?> per unit)
                                    </small>
                                </div>

                                <div class="price-cart-container">
                                    <?php if ($item['discount_price'] && $item['discount_price'] < $item['price']): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="price-tag fw-bold text-danger">₱<?= number_format($item['discount_price'], 2) ?></span>
                                            <span class="price-tag text-muted text-decoration-line-through small">₱<?= number_format($item['price'], 2) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="price-tag fw-bold">₱<?= number_format($item['price'], 2) ?></span>
                                    <?php endif; ?>
                                    <button
                                        class="add-to-cart-btn-1"
                                        title="Add to Cart"
                                        data-product-id="<?= $item['product_id'] ?>"
                                        data-price="<?= $item['discount_price'] && $item['discount_price'] < $item['price'] ? $item['discount_price'] : $item['price'] ?>"
                                        onclick="addToCart(this); event.stopPropagation();">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div> 