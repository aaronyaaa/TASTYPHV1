<?php
require_once '../includes/db_config.php';
header('Content-Type: application/json');
function getStatusCounts($pdo) {
    $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
    $query = "SELECT status, COUNT(*) AS count FROM campaign_requests GROUP BY status";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = strtolower($row['status']);
        if (isset($counts[$status])) {
            $counts[$status] += (int) $row['count'];
        }
    }
    return $counts;
}
$result = getStatusCounts($pdo);
$total = array_sum($result);
echo json_encode([
    'success' => true,
    'counts' => $result,
    'total' => $total
]); 