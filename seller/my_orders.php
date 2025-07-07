<?php
require_once '../database/db_connect.php';
require_once '../database/session.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) exit('Unauthorized.');

$stmt = $pdo->prepare("
  SELECT o.order_id, o.status, o.order_date, o.total_price,
         oi.quantity, oi.unit_price,
         i.ingredient_name, i.image_url AS ingredient_image,
         p.product_name, p.image_url AS product_image,
         COALESCE(sa.business_name, sa2.business_name) AS store_name,
         u.latitude AS user_latitude, u.longitude AS user_longitude,
         sa.latitude AS supplier_latitude, sa.longitude AS supplier_longitude
  FROM orders o
  JOIN order_items oi ON o.order_id = oi.order_id
  LEFT JOIN ingredients i ON oi.ingredient_id = i.ingredient_id
  LEFT JOIN products p ON oi.product_id = p.product_id
  LEFT JOIN supplier_applications sa ON o.supplier_id = sa.supplier_id
  LEFT JOIN seller_applications sa2 ON o.seller_id = sa2.seller_id
  JOIN users u ON o.user_id = u.id
  WHERE o.user_id = ?
  ORDER BY o.order_date DESC
");

$stmt->execute([$userId]);
$rawItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orderGroups = [];
foreach ($rawItems as $item) {
    $orderGroups[$item['order_id']]['meta'] = [
        'order_date' => $item['order_date'],
        'status' => $item['status'],
        'total_price' => $item['total_price'],
        'latitude' => $item['user_latitude'],
        'longitude' => $item['user_longitude'],
        'supplier_latitude' => $item['supplier_latitude'],
        'supplier_longitude' => $item['supplier_longitude']
    ];
    $orderGroups[$item['order_id']]['items'][] = $item;
}

$steps = ['pending', 'processing', 'shipped', 'delivered'];
$statusLabels = [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'shipped' => 'Shipped',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled'
];

// Fetch pre-orders for this user
$preOrders = [];
$preOrderStmt = $pdo->prepare("
    SELECT pol.*, sa.business_name AS seller_name
    FROM pre_order_list pol
    LEFT JOIN seller_applications sa ON pol.seller_id = sa.seller_id
    WHERE pol.user_id = ?
    ORDER BY pol.request_date DESC
");
$preOrderStmt->execute([$userId]);
$preOrders = $preOrderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML content starts -->
<!-- inside the foreach loop after each card -->
<?php foreach ($orderGroups as $orderId => $data): ?>
    <?php if ($data['meta']['status'] === 'delivered'): ?>
        <?php
        $receiptStmt = $pdo->prepare("SELECT r.*, u.first_name, u.last_name, sa.business_name FROM receipts r
                                    JOIN users u ON r.user_id = u.id
                                    LEFT JOIN supplier_applications sa ON r.supplier_id = sa.supplier_id
                                    WHERE r.order_id = ?");
        $receiptStmt->execute([$orderId]);
        $receipt = $receiptStmt->fetch(PDO::FETCH_ASSOC);

        if ($receipt):
            $itemsStmt = $pdo->prepare("SELECT * FROM receipt_item WHERE receipt_id = ?");
            $itemsStmt->execute([$receipt['receipt_id']]);
            $receiptItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <div class="modal fade" id="receiptModal<?= $orderId ?>" tabindex="-1" aria-labelledby="receiptModalLabel<?= $orderId ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="receiptModalLabel<?= $orderId ?>">Receipt - Order #<?= $orderId ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="receipt-content">
                                <div class="receipt-header">
                                    <h5><?= htmlspecialchars($receipt['business_name']) ?></h5>
                                    <small>Payment Date: <?= date('d M Y H:i', strtotime($receipt['payment_date'])) ?></small>
                                </div>
                                <div class="receipt-details">
                                    <strong>Customer:</strong> <?= htmlspecialchars($receipt['first_name'] . ' ' . $receipt['last_name']) ?><br>
                                    <strong>Payment Method:</strong> <?= ucfirst($receipt['payment_method']) ?>
                                </div>

                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($receiptItems as $ri): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ri['description']) ?></td>
                                                <td><?= $ri['quantity'] ?></td>
                                                <td>₱<?= number_format($ri['unit_price'], 2) ?></td>
                                                <td>₱<?= number_format(($ri['unit_price'] - $ri['discount']) * $ri['quantity'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <div class="text-end">
                                    <p class="mb-1">Subtotal: ₱<?= number_format($receipt['subtotal'], 2) ?></p>
                                    <p class="mb-1">Tax: ₱<?= number_format($receipt['tax_amount'], 2) ?></p>
                                    <p class="mb-1">Discount: ₱<?= number_format($receipt['discount'], 2) ?></p>
                                    <h5>Total Paid: ₱<?= number_format($receipt['total_paid'], 2) ?></h5>
                                    <p class="mb-0">Amount Paid: ₱<?= number_format($receipt['amount_paid'], 2) ?></p>
                                    <p class="mb-0">Change: ₱<?= number_format($receipt['change_given'], 2) ?></p>
                                </div>

                                <div class="footer-note">
                                    Authorized by: <?= htmlspecialchars($receipt['authorized_by'] ?? 'System') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/seller_order.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/receipt.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>

    <?php include '../includes/nav/navbar_router.php'; ?>
    <?php include '../seller/components/sidebar.php'; ?>

    <div class="main-content" style="margin-left: 240px;">
        <h3 class="mb-4">My Orders</h3>

        <!-- Pre-Orders Section -->
        <h4 class="mb-3">Pre-Orders</h4>
        <?php if (empty($preOrders)): ?>
            <div class="alert alert-info">You have no pre-orders yet.</div>
        <?php else: ?>
            <?php foreach ($preOrders as $pre): ?>
                <div class="card mb-4 shadow-sm border border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-warning text-dark me-2">Pre-Order</span>
                                <strong><?= htmlspecialchars($pre['product_name']) ?></strong>
                                <span class="text-muted small">from <?= htmlspecialchars($pre['seller_name'] ?? 'Unknown Seller') ?></span>
                            </div>
                            <span class="badge <?=
                                match($pre['status']) {
                                    'pending' => 'bg-secondary',
                                    'approved' => 'bg-info text-dark',
                                    'declined' => 'bg-danger',
                                    'delivered' => 'bg-success',
                                    default => 'bg-dark'
                                }
                            ?>">
                                <?= ucfirst($pre['status']) ?>
                            </span>
                        </div>
                        <table class="table table-borderless align-middle mb-0">
                            <tr>
                                <th>Quantity</th>
                                <td><?= $pre['quantity'] ?> <?= htmlspecialchars($pre['unit']) ?></td>
                                <th>Preferred Date</th>
                                <td><?= htmlspecialchars($pre['preferred_date']) ?> <?= htmlspecialchars($pre['preferred_time']) ?></td>
                            </tr>
                            <tr>
                                <th>Requested</th>
                                <td><?= date('d M Y', strtotime($pre['request_date'])) ?></td>
                                <th>Order Status</th>
                                <td><?= ucfirst($pre['status']) ?></td>
                            </tr>
                            <?php if (!empty($pre['additional_notes'])): ?>
                            <tr>
                                <th>Notes</th>
                                <td colspan="3"><?= htmlspecialchars($pre['additional_notes']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($pre['full_address'])): ?>
                            <tr>
                                <th>Delivery Address</th>
                                <td colspan="3"><?= htmlspecialchars($pre['full_address']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($orderGroups)): ?>
            <div class="alert alert-info">You have no orders yet.</div>
        <?php else: ?>
            <?php foreach ($orderGroups as $orderId => $data): ?>
                <?php
                $status = $data['meta']['status'];
                $isCancelled = $status === 'cancelled';
                $progressIndex = array_search($status, $steps);
                if ($isCancelled && $progressIndex === false) $progressIndex = 0;
                $progressWidth = ($progressIndex / (count($steps) - 1)) * 100;
                $progressColor = $isCancelled ? 'bg-danger' : 'bg-primary';

                $statusClass = match ($status) {
                    'pending' => 'bg-secondary',
                    'processing' => 'bg-warning text-dark',
                    'shipped' => 'bg-info text-dark',
                    'delivered' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    default => 'bg-dark'
                };
                $statusIcon = match ($status) {
                    'pending' => 'bi-hourglass-split',
                    'processing' => 'bi-gear',
                    'shipped' => 'bi-truck',
                    'delivered' => 'bi-check-circle',
                    'cancelled' => 'bi-x-circle',
                    default => 'bi-question-circle'
                };
                ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-muted small">
                                <strong><?= htmlspecialchars($data['items'][0]['store_name'] ?? 'Order') ?></strong> — Ordered on <?= date('d M Y', strtotime($data['meta']['order_date'])) ?>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#mapModal<?= $orderId ?>">
                                    <i class="bi bi-geo-alt-fill me-1"></i> View Map
                                </button>
                                <span class="badge <?= $statusClass ?> px-3 py-2 fw-medium">
                                    <i class="bi <?= $statusIcon ?> me-1"></i> <?= $statusLabels[$status] ?>
                                </span>
                            </div>
                        </div>

                        <div class="progress mt-3 mb-2" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated <?= $progressColor ?>" role="progressbar"
                                style="width: <?= $progressWidth ?>%" aria-valuenow="<?= $progressWidth ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="d-flex justify-content-between text-muted small mt-2 px-1">
                            <?php foreach ($steps as $label): ?>
                                <div class="text-center" style="flex: 1;"><?= $statusLabels[$label] ?></div>
                            <?php endforeach; ?>
                        </div>

                        <table class="table table-borderless align-middle mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['items'] as $item): ?>
                                    <tr>
                                        <td class="d-flex align-items-center gap-2">
                                            <?php
                                            $name = $item['product_name'] ?: $item['ingredient_name'];
                                            $img = $item['product_image'] ?: $item['ingredient_image'] ?: 'assets/images/default-product.png';
                                            ?>
                                            <img src="../<?= $img ?>" width="40" height="40" class="rounded shadow-sm" style="object-fit: cover;">
                                            <?= htmlspecialchars($name) ?>

                                        </td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                                        <td>₱<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">₱<?= number_format($data['meta']['total_price'], 2) ?></td>
                                </tr>
                                <?php if ($status === 'delivered'): ?>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Receipt:</td>
                                        <td>
                                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#receiptModal<?= $orderId ?>">View Receipt</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>


                <!-- Map Modal -->
                <div class="modal fade" id="mapModal<?= $orderId ?>" tabindex="-1" aria-labelledby="mapModalLabel<?= $orderId ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mapModalLabel<?= $orderId ?>">Delivery Map — Order #<?= $orderId ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div id="map<?= $orderId ?>" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const scooterIcon = L.icon({
            iconUrl: '../uploads/icon/scooter.png',
            iconSize: [60, 60],
            iconAnchor: [22, 22]
        });

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return (R * c).toFixed(2);
        }

        const orderMapData = <?= json_encode($orderGroups) ?>;

        Object.entries(orderMapData).forEach(([orderId, data]) => {
            const modal = document.getElementById(`mapModal${orderId}`);
            modal.addEventListener('shown.bs.modal', () => {
                const container = document.getElementById(`map${orderId}`);
                if (!container.dataset.initialized) {
                    const uLat = parseFloat(data.meta.latitude);
                    const uLng = parseFloat(data.meta.longitude);
                    const sLat = parseFloat(data.meta.supplier_latitude);
                    const sLng = parseFloat(data.meta.supplier_longitude);

                    const status = data.meta.status;

                    const map = L.map(container).setView([uLat, uLng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([uLat, uLng]).addTo(map).bindPopup("Your Location");
                    L.marker([sLat, sLng]).addTo(map).bindPopup("Supplier Location");

                    const line = L.polyline([
                        [sLat, sLng],
                        [uLat, uLng]
                    ], {
                        color: 'blue',
                        weight: 3,
                        dashArray: '6, 6'
                    }).addTo(map);

                    const distance = calculateDistance(sLat, sLng, uLat, uLng);
                    L.popup()
                        .setLatLng(line.getCenter())
                        .setContent(`<strong>Distance:</strong> ${distance} km`)
                        .openOn(map);

                    if (status === 'processing') {
                        L.marker([sLat, sLng], {
                            icon: scooterIcon
                        }).addTo(map).bindPopup("On Standby");
                    } else if (status === 'shipped') {
                        const midLat = (sLat + uLat) / 2;
                        const midLng = (sLng + uLng) / 2;
                        L.marker([midLat, midLng], {
                            icon: scooterIcon
                        }).addTo(map).bindPopup("On the way");
                    } else if (status === 'delivered') {
                        L.marker([uLat, uLng], {
                            icon: scooterIcon
                        }).addTo(map).bindPopup("Delivered");
                    }

                    map.fitBounds(line.getBounds(), {
                        padding: [40, 40]
                    });
                    container.dataset.initialized = 'true';
                }
            });
        });
    </script>
</body>

</html>