<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    die("Unauthorized access");
}

// Get seller_id for the logged-in user
$sellerStmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$sellerStmt->execute([$userId]);
$seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    die("Seller not found for the current user.");
}

$sellerId = $seller['seller_id'];

// Fetch products by seller
$stmt = $pdo->prepare("SELECT 
    product_id, product_name, slug, description, image_url, price, discount_price, 
    stock, quantity_value, unit_type, is_active, rating, notes, 
    created_at, updated_at, category_id 

    FROM products 
    WHERE seller_id = ? 
    ORDER BY updated_at DESC
");
$stmt->execute([$sellerId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for this seller
$catStmt = $pdo->prepare("SELECT category_id, name FROM categories WHERE seller_id = ?");
$catStmt->execute([$sellerId]);
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-borderless th,
        .table-borderless td {
            border: none !important;
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .description {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-box-open me-2"></i>Your Cooked Products</h2>

        <?php if (!empty($products)): ?>
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Stock</th>
                            <th>Qty/Unit</th>
                            <th>Active</th>
                            <th>Rating</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($products as $row): ?>
                            <?php
                            $imgSrc = !empty($row['image_url']) ? "../" . ltrim($row['image_url'], '/') : "../assets/images/default-category.png";
                            ?>

                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" class="product-img" alt="Image">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['product_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['slug']) ?></small><br>
                                    <span class="description"><?= htmlspecialchars($row['description']) ?></span>
                                </td>
                                <td>₱<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['discount_price'] ? '₱' . number_format($row['discount_price'], 2) : '—' ?></td>
                                <td><?= $row['stock'] ?></td>
                                <td><?= $row['quantity_value'] . ' ' . htmlspecialchars($row['unit_type']) ?></td>
                                <td><?= $row['is_active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                                <td><?= number_format($row['rating'], 2) ?></td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td><?= date('M d, Y', strtotime($row['updated_at'])) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="<?= $row['product_id'] ?>"
                                            data-name="<?= htmlspecialchars($row['product_name']) ?>"
                                            data-slug="<?= htmlspecialchars($row['slug']) ?>"
                                            data-desc="<?= htmlspecialchars($row['description']) ?>"
                                            data-price="<?= $row['price'] ?>"
                                            data-discount="<?= $row['discount_price'] ?>"
                                            data-stock="<?= $row['stock'] ?>"
                                            data-qty="<?= $row['quantity_value'] ?>"
                                            data-unit="<?= $row['unit_type'] ?>"
                                            data-active="<?= $row['is_active'] ?>"
                                            data-category="<?= $row['category_id'] ?>"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>


                                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $row['product_id'] ?>)">
                                            Delete
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No products found.</div>
        <?php endif; ?>
    </div>
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form action="../backend/edit_product.php" method="POST" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="editProductId">
                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="editCategory" class="form-control">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="product_name" id="editProductName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" id="editSlug" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" id="editPrice" class="form-control">
                        </div>
                        <div class="col">
                            <label>Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" id="editDiscount" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Stock</label>
                            <input type="number" name="stock" id="editStock" class="form-control">
                        </div>
                        <div class="col">
                            <label>Quantity/Unit</label>
                            <input type="number" name="quantity_value" id="editQuantity" step="0.01" class="form-control">
                        </div>
                        <div class="col">
                            <label>Unit Type</label>
                            <select name="unit_type" id="editUnitType" class="form-control">
                                <option value="pcs">pcs</option>
                                <option value="tray">tray</option>
                                <option value="box">box</option>
                                <option value="g">g</option>
                                <option value="ml">ml</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive">
                            <label class="form-check-label" for="editIsActive">Active Status</label>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label>Product Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('editProductId').value = button.getAttribute('data-id');
                document.getElementById('editProductName').value = button.getAttribute('data-name');
                document.getElementById('editSlug').value = button.getAttribute('data-slug');
                document.getElementById('editDescription').value = button.getAttribute('data-desc');
                document.getElementById('editPrice').value = button.getAttribute('data-price');
                document.getElementById('editDiscount').value = button.getAttribute('data-discount');
                document.getElementById('editStock').value = button.getAttribute('data-stock');
                document.getElementById('editQuantity').value = button.getAttribute('data-qty');
                document.getElementById('editUnitType').value = button.getAttribute('data-unit');
                document.getElementById('editIsActive').checked = button.getAttribute('data-active') === '1';
                document.getElementById('editCategory').value = button.getAttribute('data-category') || '';
            });
        });
    </script>

    <script>
        function deleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            fetch('../backend/seller/delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${encodeURIComponent(productId)}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // 🔄 Refresh the current page
                    }
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                    alert('Something went wrong. Try again.');
                });
        }
    </script>


</body>

</html>