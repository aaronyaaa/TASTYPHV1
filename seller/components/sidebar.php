<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="sidebar text-white vh-100 position-fixed">
    <div class="p-3">
        <h5 class="text-white mb-4">Seller Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="orders.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? ' active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="my_orders.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'my_orders.php' ? ' active' : '' ?>">
                    <i class="bi bi-box-seam me-2"></i> My Orders
                </a>
            </li>
        </ul>
    </div>
</div>
