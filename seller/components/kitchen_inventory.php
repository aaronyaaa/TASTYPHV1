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
        .card-cook {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.08)!important;
            margin-bottom: 2rem;
        }
        .card-cook .card-header {
            border-radius: 1rem 1rem 0 0;
            background: linear-gradient(90deg, #fffbe6 60%, #fff3cd 100%);
            border-bottom: 1px solid #ffe082;
        }
        .ingredient-row {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.5rem 0.25rem;
            margin-bottom: 0.5rem;
            border: 1px solid #f1f1f1;
        }
        .ingredient-row .form-control {
            border-radius: 0.5rem;
        }
        .add-ingredient-btn {
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .add-ingredient-btn i {
            color: #ffc107;
        }
        .cook-submit-btn {
            font-weight: 700;
            border-radius: 0.5rem;
            padding-left: 2rem;
            padding-right: 2rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .cook-submit-btn .spinner-border {
            margin-right: 0.5rem;
        }
        .card-cook .card-body label {
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .card-cook .card-header h5 { font-size: 1.1rem; }
            .ingredient-row { font-size: 0.97rem; }
        }
        .card-inventory {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.08)!important;
            margin-bottom: 2rem;
        }
        .card-inventory .card-header {
            border-radius: 1rem 1rem 0 0;
            background: linear-gradient(90deg, #e3f2fd 60%, #bbdefb 100%);
            border-bottom: 1px solid #90caf9;
            display: flex;
            align-items: center;
        }
        .card-inventory .card-header i {
            color: #1976d2;
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            --bs-table-accent-bg: #f8fafc;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f9;
        }
        .table th {
            font-weight: 700;
            font-size: 1.08rem;
        }
        .badge-qty {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
            font-size: 1rem;
        }
        .ingredient-icon {
            color: #ff9800;
            margin-right: 0.4rem;
        }
        .search-bar {
            max-width: 300px;
            margin-bottom: 1rem;
        }
        @media (max-width: 576px) {
            .card-inventory .card-header h5 { font-size: 1.1rem; }
            .table th, .table td { font-size: 0.97rem; }
        }
        /* Modal overlay enhancement */
        .modal-backdrop.show {
            opacity: 0.85 !important;
            background: #222 !important;
        }
        .modal-fullscreen, .modal-fullscreen .modal-dialog, .modal-fullscreen .modal-content.cook-modal-content {
            width: 100vw !important;
            max-width: 100vw !important;
            height: 100vh !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .cook-modal-content {
            background: radial-gradient(circle at 50% 40%, #fffde7 60%, #ffe082 100%) !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .cook-gif-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .cook-gif-glow {
            border-radius: 50%;
        }
        .floating-foods {
            position: absolute;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: 1;
        }
        .food-icon {
            position: absolute;
            font-size: 2.2rem;
            opacity: 0.7;
            filter: drop-shadow(0 2px 6px #ffe082);
            animation: floatFood 5s linear infinite;
        }
        .food-egg { left: 10%; top: 10%; animation-delay: 0s; }
        .food-bread { left: 80%; top: 18%; animation-delay: 1.2s; }
        .food-cheese { left: 20%; top: 70%; animation-delay: 2.1s; }
        .food-carrot { left: 70%; top: 75%; animation-delay: 2.8s; }
        .food-pepper { left: 50%; top: 5%; animation-delay: 3.5s; }
        @keyframes floatFood {
            0% { transform: translateY(0) scale(1); opacity: 0.7; }
            50% { transform: translateY(-18px) scale(1.12); opacity: 1; }
            100% { transform: translateY(0) scale(1); opacity: 0.7; }
        }
        .progress-food-icon {
            position: absolute;
            left: 0; top: 100%;
            font-size: 1.7rem;
            z-index: 2;
            transform: translateY(-30px);
            transition: left 0.2s linear;
            pointer-events: none;
        }
        .cook-bounce {
            animation: cookBounce 1.2s infinite alternate;
        }
        @keyframes cookBounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px) scale(1.05); }
        }
        @media (max-width: 576px) {
            .cook-gif-glow { width: 120px !important; height: 120px !important; }
            .modal-content { padding: 1rem !important; }
            .food-icon { font-size: 1.3rem; }
        }
        .animate__fadeInScale {
            animation: fadeInScale 0.7s cubic-bezier(0.23, 1, 0.32, 1);
        }
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.7); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <!-- Enhanced Inventory Table -->
        <div class="card card-inventory">
            <div class="card-header">
                <i class="fas fa-kitchen-set"></i>
                <h5 class="mb-0 fw-bold text-primary">Kitchen Inventory</h5>
            </div>
            <div class="card-body">
                <input type="text" class="form-control search-bar" id="kitchenSearch" placeholder="Search ingredient...">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody id="kitchenTableBody">
                            <?php foreach ($kitchen as $item): ?>
                                <tr>
                                    <td><i class="fas fa-carrot ingredient-icon"></i><?= htmlspecialchars($item['ingredient_name']) ?></td>
                                    <td><span class="badge rounded-pill bg-success"><?= number_format($item['quantity_value'], 2) ?></span></td>
                                    <td><?= htmlspecialchars($item['unit_type']) ?></td>
                                    <td><span class="text-muted small"><?= htmlspecialchars($item['updated_at']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cooking Form -->
        <div class="card card-cook">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="fas fa-fire me-2 fs-4 text-danger"></i>
                <h5 class="mb-0">Cook New Product</h5>
            </div>
            <div class="card-body">
                <form id="cookProductForm" method="POST" action="../backend/cook_product.php" enctype="multipart/form-data">
                    <h6 class="mb-3 fw-bold text-secondary"><i class="fas fa-carrot me-1"></i> Ingredients</h6>
                    <div id="ingredientsWrapper" class="mb-2"></div>
                    <button type="button" class="btn btn-outline-warning btn-sm mb-3 add-ingredient-btn" onclick="addIngredientRow()">
                        <i class="fas fa-plus-circle me-1"></i> Add Ingredient
                    </button>
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
                        <button type="submit" name="cookBtn" value="normal" class="btn btn-warning cook-submit-btn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="cookSpinner" role="status" aria-hidden="true"></span>
                            <i class="fas fa-fire me-1"></i> Cook Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cooking Modal -->
    <div class="modal fade" id="cookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen m-0 p-0" style="width:100vw;max-width:100vw;height:100vh;">
            <div class="modal-content cook-modal-content d-flex flex-column justify-content-center align-items-center text-center p-0 position-relative animate__fadeInScale" style="width:100vw;height:100vh;min-height:100vh;background: radial-gradient(circle at 50% 40%, #fffde7 60%, #ffe082 100%) !important;overflow:hidden;">
                <div class="floating-foods"></div>
                <div class="cook-gif-wrapper mb-3" style="z-index:2;">
                    <img src="../uploads/GIF/Cooking.gif" alt="Cooking animation" class="cook-gif-glow" style="width: 220px; height: 220px;">
                    <span id="progressFoodIcon" class="progress-food-icon">🍳</span>
                </div>
                <p class="fs-4 fw-bold text-warning mt-2 mb-1 cook-bounce" style="text-shadow: 0 2px 8px #ffe082, 0 1px 2px #fffbe6;">Cooking your product...</p>
                <p class="text-muted mb-3" id="cookTip">Please wait while we cook your delicious creation!</p>
                <div class="progress mx-auto" style="height: 12px; max-width: 220px; background: #fffbe6; position: relative;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="cookProgressBar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ingredient Row Template -->
    <template id="ingredientRowTemplate">
        <div class="row mb-2 ingredient-row align-items-center">
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
    <script src="https://unpkg.com/@lottiefiles/dotlottie-web@latest/dist/dotlottie.min.js"></script>
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
                    document.getElementById('cookSpinner').classList.remove('d-none');
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

        // Fun cooking tips/quotes
        const cookTips = [
            "A watched pot never boils, but a happy chef always smiles!",
            "Did you know? The secret ingredient is always love.",
            "Cooking is like magic, but tastier!",
            "Great food takes time. Thanks for your patience!",
            "Stirring up something delicious just for you!",
            "Every recipe is a story. Yours is about to begin!",
            "Good things come to those who bake!",
            "Let the aroma of success fill your kitchen!",
            "Cooking is an art, and you're the masterpiece!",
            "Yum in progress..."
        ];
        // Animate progress bar and food icon
        function animateCookProgressBar() {
            var bar = document.getElementById('cookProgressBar');
            var icon = document.getElementById('progressFoodIcon');
            if (!bar || !icon) return;
            bar.style.width = '0%';
            let progress = 0;
            let interval = setInterval(function() {
                progress += 2 + Math.random() * 3;
                if (progress >= 100) progress = 100;
                bar.style.width = progress + '%';
                // Move the food icon along the bar
                icon.style.left = `calc(${progress}% - 18px)`;
                if (progress >= 100) clearInterval(interval);
            }, 120);
        }
        // Show random tip
        function showRandomTip() {
            var tip = cookTips[Math.floor(Math.random() * cookTips.length)];
            document.getElementById('cookTip').textContent = tip;
        }
        // Hook into modal show
        document.addEventListener('DOMContentLoaded', function() {
            var cookingModal = document.getElementById('cookingModal');
            if (cookingModal) {
                cookingModal.addEventListener('show.bs.modal', function () {
                    animateCookProgressBar();
                    showRandomTip();
                });
            }
        });

        // Re-insert floating food icons dynamically for fullscreen
        document.addEventListener('DOMContentLoaded', function() {
            var foods = [
                '<span class="food-icon food-egg">🍳</span>',
                '<span class="food-icon food-bread">🍞</span>',
                '<span class="food-icon food-cheese">🧀</span>',
                '<span class="food-icon food-carrot">🥕</span>',
                '<span class="food-icon food-pepper">🌶️</span>'
            ];
            var floating = document.querySelector('.floating-foods');
            if (floating) floating.innerHTML = foods.join('');
        });
    </script>

</body>

</html>