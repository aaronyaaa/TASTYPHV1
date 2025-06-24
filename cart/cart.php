<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['userId'] ?? null;
$cartGroupedByStore = [];

if ($userId) {
    $stmt = $pdo->prepare("
        SELECT 
            c.*, 
            i.ingredient_name, 
            i.image_url AS ingredient_image, 
            iv.variant_name, 
            i.stock AS ingredient_stock,
            p.product_name, 
            p.image_url AS product_image, 
            p.stock AS product_stock,
            COALESCE(sa.business_name, se.business_name) AS store_name,
            COALESCE(sa.supplier_id, se.seller_id) AS store_id
        FROM cart c
        LEFT JOIN ingredients i ON c.ingredient_id = i.ingredient_id
        LEFT JOIN ingredient_variants iv ON c.variant_id = iv.variant_id
        LEFT JOIN products p ON c.product_id = p.product_id
        LEFT JOIN supplier_applications sa ON i.supplier_id = sa.supplier_id
        LEFT JOIN seller_applications se ON p.seller_id = se.seller_id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $item) {
        $storeName = $item['store_name'] ?? 'Unknown Store';
        $cartGroupedByStore[$storeName][] = $item;
    }
}

$total = array_sum(array_column($cartItems, 'total_price'));

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>

<body class="bg-light">

    <?php include '../includes/nav/navbar_router.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card p-4 shadow-sm">
                    <h4>Your Cart</h4>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                            <label class="form-check-label" for="selectAll">SELECT ALL (<?= count($cartItems) ?> ITEM(S))</label>
                        </div>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt me-2"></i>Delete Selected</button>
                    </div>

                    <?php if (empty($cartGroupedByStore)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            Your cart is empty. Add some ingredients!
                        </div>
                    <?php else: ?>
                        <?php foreach ($cartGroupedByStore as $store => $items): ?>
                            <div class="mb-3 border-bottom pb-2">
                                <h5 class="text-primary"><?= htmlspecialchars($store) ?></h5>
                            </div>

                            <?php foreach ($items as $item): ?>
                                <div class="cart-item border rounded p-3 mb-3 bg-white d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-3 item-checkbox" checked data-item-id="<?= $item['cart_id'] ?>">
                                    <div class="me-3">
                                        <?php if (!empty($item['product_id'])): ?>
                                            <a href="../users/product_page.php?product_id=<?= $item['product_id'] ?>">
                                                <img src="../<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid" style="width: 70px; height: 70px; object-fit: cover;">
                                            </a>
                                        <?php else: ?>
                                            <a href="../users/ingredient_page.php?ingredient_id=<?= $item['ingredient_id'] ?>">
                                                <img src="../<?= htmlspecialchars($item['ingredient_image']) ?>" alt="<?= htmlspecialchars($item['ingredient_name']) ?>" class="img-fluid" style="width: 70px; height: 70px; object-fit: cover;">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?php if (!empty($item['product_id'])): ?>
                                            <h6 class="mb-1"><a href="../users/product_page.php?product_id=<?= $item['product_id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($item['product_name']) ?></a></h6>
                                        <?php else: ?>
                                            <h6 class="mb-1"><a href="../users/ingredient_page.php?ingredient_id=<?= $item['ingredient_id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($item['ingredient_name']) ?></a></h6>
                                        <?php endif; ?>
                                        <?php if (!empty($item['variant_name'])): ?>
                                            <small class="text-muted">Variant: <?= htmlspecialchars($item['variant_name']) ?></small><br>
                                        <?php endif; ?>
                                        <p class="mb-1" data-unit-price="<?= $item['unit_price'] ?>">Unit Price: ₱<?= number_format($item['unit_price'], 2) ?></p>
                                        <p class="mb-1">Available Stock: <?= !empty($item['product_id']) ? $item['product_stock'] : $item['ingredient_stock'] ?></p>
                                        <p class="fw-bold mb-0">Subtotal: ₱<span class="item-subtotal" id="subtotal-<?= $item['cart_id'] ?>"><?= number_format($item['total_price'], 2) ?></span></p>
                                    </div>
                                    <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                        <button class="btn btn-outline-secondary decrease-quantity" type="button" data-cart-id="<?= $item['cart_id'] ?>">-</button>
                                        <input type="text" class="form-control text-center quantity-input" value="<?= $item['quantity'] ?>" data-cart-id="<?= $item['cart_id'] ?>" data-stock="<?= !empty($item['product_id']) ? $item['product_stock'] : $item['ingredient_stock'] ?>">
                                        <button class="btn btn-outline-secondary increase-quantity" type="button" data-cart-id="<?= $item['cart_id'] ?>">+</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>


                </div>
            </div>

            <div class="col-md-4">
                <div class="card-checkout shadow-sm">
                    <h5>Order Summary</h5>
                    <p><strong>Location:</strong> <?= htmlspecialchars($user['full_address'] ?? 'N/A') ?></p>
                    <p><strong>Subtotal:</strong> ₱<span id="order-subtotal"><?= number_format($total, 2) ?></span></p>
                    <p><strong>Shipping Fee:</strong> ₱<span id="shipping-fee">0.00</span></p>
                    <input type="text" class="form-control mb-2" placeholder="Enter Voucher Code">
                    <button class="btn btn-primary w-100 mb-3">APPLY</button>
                    <hr>
                    <h5>Total: ₱<span id="order-total"><?= number_format($total, 2) ?></span></h5>
                    <h6 class="mt-3">Payment Method</h6>
                    <div class="d-flex justify-content-between mb-3">
                        <button class="btn btn-outline-danger flex-grow-1 me-2 payment-option" data-method="cash">Cash</button>
                        <button class="btn btn-outline-danger flex-grow-1 me-2 payment-option" data-method="gcash">GCash</button>
                        <button class="btn btn-outline-danger flex-grow-1 payment-option" data-method="card">Card Payment</button>
                    </div>

                    <!-- Dynamic payment inputs -->
                    <div id="payment-details" class="mb-3"></div>

                    <button id="checkout-btn" class="btn btn-success w-100 mt-3">Checkout</button>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/cart.js"></script>
</body>

</html>