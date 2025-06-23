<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/../../database/session.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) exit;

// Fetch seller_id
$sellerStmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$sellerStmt->execute([$userId]);
$seller_id = $sellerStmt->fetchColumn();
if (!$seller_id) exit;

// Kitchen Inventory
$stmt = $pdo->prepare("SELECT * FROM kitchen_inventory WHERE user_id = ?");
$stmt->execute([$userId]);
$kitchen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kitchen Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #cookingModal .modal-content {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        #cookingModal .modal-dialog {
            max-width: 400px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <!-- Inventory Table -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5>Kitchen Inventory</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kitchen as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['ingredient_name']) ?></td>
                                <td><?= number_format($item['quantity_value'], 2) ?></td>
                                <td><?= htmlspecialchars($item['unit_type']) ?></td>
                                <td><?= htmlspecialchars($item['updated_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cooking Form -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5>Cook New Product</h5>
            </div>
            <div class="card-body">
                <form id="cookProductForm" method="POST" action="../backend/cook_product.php" enctype="multipart/form-data">

                    <h6>Ingredients</h6>
                    <div id="ingredientsWrapper"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" onclick="addIngredientRow()">+ Add Ingredient</button>
                    <hr>
                    <div class="row mb-2">
                        <div class="col"><label>Product Name</label><input type="text" name="product_name" class="form-control" required></div>
                        <div class="col"><label>Slug</label><input type="text" name="slug" class="form-control"></div>
                    </div>
                    <div class="mb-2">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="row mb-2">
                        <div class="col"><label>Price</label><input type="number" step="0.01" name="price" class="form-control"></div>
                        <div class="col"><label>Discount Price</label><input type="number" step="0.01" name="discount_price" class="form-control"></div>
                        <div class="col"><label>Stock</label><input type="number" name="stock" class="form-control"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col"><label>Quantity per Unit</label><input type="number" name="quantity_value" value="1" class="form-control"></div>
                        <div class="col">
                            <label>Unit Type</label>
                            <select name="unit_type" class="form-control">
                                <option value="pcs">pcs</option>
                                <option value="tray">tray</option>
                                <option value="box">box</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" name="cookBtn" value="normal" class="btn btn-warning">
                            <i class="fas fa-fire me-1"></i> Cook Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cooking Modal -->
    <div class="modal fade" id="cookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light border-0 text-center p-4">
                <img src="../uploads/GIF/Cooking.gif" alt="Cooking animation" style="width: 300px; height: 300px;">
                <p class="fs-5 fw-bold text-warning mt-3 mb-0">Cooking your product...</p>
            </div>
        </div>
    </div>

    <!-- Ingredient Row Template -->
    <template id="ingredientRowTemplate">
        <div class="row mb-2 ingredient-row">
            <div class="col">
                <select class="form-control ingredient-select" onchange="setUnitType(this)">
                    <?php foreach ($kitchen as $item): ?>
                        <option value="<?= $item['ingredient_id'] ?>" data-unit-type="<?= htmlspecialchars($item['unit_type']) ?>">
                            <?= htmlspecialchars($item['ingredient_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col"><input type="number" step="0.01" class="form-control ingredient-qty" placeholder="Quantity"></div>
            <div class="col"><input type="text" class="form-control ingredient-unit" placeholder="Unit (e.g. g, pcs)"></div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('cookProductForm');
            let clickedButton = null;

            // Track submit button click
            document.querySelectorAll('#cookProductForm button[type="submit"]').forEach(btn => {
                btn.addEventListener('click', () => clickedButton = btn);
            });

            // Form validation and submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const productName = form.querySelector('[name="product_name"]').value.trim();
                const wrapper = document.getElementById('ingredientsWrapper');
                const ingredientRows = wrapper.querySelectorAll('.ingredient-row');

                if (!productName) {
                    alert("Please enter a product name.");
                    return;
                }


                let valid = true;

                ingredientRows.forEach(row => {
                    const qtyInput = row.querySelector('.ingredient-qty');
                    const qty = qtyInput ? qtyInput.value.trim() : '';
                    if (!qty || isNaN(qty) || parseFloat(qty) <= 0) {
                        valid = false;
                    }
                });

                if (!valid) {
                    alert("Each ingredient must have a valid quantity greater than 0.");
                    return;
                }

                // Show modal if cooking normally
                if (clickedButton && clickedButton.name === 'cookBtn' && clickedButton.value === 'normal') {
                    const modal = new bootstrap.Modal(document.getElementById('cookingModal'));
                    modal.show();
                }

                const delay = (clickedButton && clickedButton.value === 'normal') ? 5000 : 0;
                setTimeout(() => {
                    form.submit();
                }, delay);
            });
        });

        // Add new ingredient row
        function addIngredientRow() {
            const index = document.querySelectorAll('.ingredient-row').length;
            const template = document.getElementById('ingredientRowTemplate').content.cloneNode(true);
            const select = template.querySelector('.ingredient-select');
            const qty = template.querySelector('.ingredient-qty');
            const unit = template.querySelector('.ingredient-unit');

            select.name = `ingredients[${index}][ingredient_id]`;
            qty.name = `ingredients[${index}][quantity]`;
            unit.name = `ingredients[${index}][unit]`;

            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption) {
                unit.value = selectedOption.getAttribute('data-unit-type') || '';
            }

            document.getElementById('ingredientsWrapper').appendChild(template);
        }

        // Auto-fill unit type on ingredient select change
        function setUnitType(selectEl) {
            const unitInput = selectEl.closest('.ingredient-row').querySelector('.ingredient-unit');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            unitInput.value = selectedOption.getAttribute('data-unit-type') || '';
        }
    </script>

</body>

</html>