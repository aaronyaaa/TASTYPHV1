<?php
require_once __DIR__ . '/../includes/db_config.php';

$stmt = $pdo->query("
  SELECT usertype, COUNT(*) as count
  FROM users
  WHERE usertype IN ('user', 'seller', 'supplier')
  GROUP BY usertype
");

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];

foreach ($results as $row) {
    $labels[] = ucfirst($row['usertype']);
    $data[] = (int)$row['count'];
}

echo json_encode([
    'labels' => $labels,
    'counts' => $data
]);
