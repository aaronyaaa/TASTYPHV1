<?php
require_once '../includes/db_config.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

function getStatusCounts($pdo, $table) {
    $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
    $query = "SELECT status, COUNT(*) AS count FROM {$table} GROUP BY status";
    $stmt = $pdo->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = strtolower($row['status']);
        if (isset($counts[$status])) {
            $counts[$status] += (int) $row['count'];
        }
    }

    return $counts;
}

// Get individual counts
$sellerCounts = getStatusCounts($pdo, 'seller_applications');
$supplierCounts = getStatusCounts($pdo, 'supplier_applications');

// Determine result based on type
switch ($type) {
    case 'seller':
        $result = $sellerCounts;
        break;
    case 'supplier':
        $result = $supplierCounts;
        break;
    case 'all':
    default:
        $result = [
            'pending' => $sellerCounts['pending'] + $supplierCounts['pending'],
            'approved' => $sellerCounts['approved'] + $supplierCounts['approved'],
            'rejected' => $sellerCounts['rejected'] + $supplierCounts['rejected'],
        ];
        break;
}

$total = array_sum($result);

// Output JSON
echo json_encode([
    'success' => true,
    'type' => $type,
    'counts' => $result,
    'total' => $total
]);
