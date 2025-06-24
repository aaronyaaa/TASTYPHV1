<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../database/db_connect.php';

header('Content-Type: application/json');

$campaignId = intval($_GET['campaign_id'] ?? 0);
$mode = $_GET['mode'] ?? 'daily';
$selectedDate = $_GET['date'] ?? null;
$fromParam = $_GET['from'] ?? null;
$toParam = $_GET['to'] ?? null;

if ($campaignId <= 0) {
    echo json_encode(['labels' => [], 'values' => []]);
    exit;
}

// Get campaign info
$stmt = $pdo->prepare("SELECT start_date, end_date FROM campaign_requests WHERE campaign_id = ?");
$stmt->execute([$campaignId]);
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$campaign) {
    echo json_encode(['labels' => [], 'values' => []]);
    exit;
}

$startDate = $campaign['start_date'];
$endDate = $campaign['end_date'];
$today = date('Y-m-d');

// Calculate total views for the whole campaign
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM campaign_clicks WHERE campaign_id = ? AND DATE(clicked_at) BETWEEN ? AND ?");
$totalStmt->execute([$campaignId, $startDate, $endDate]);
$totalViews = (int)$totalStmt->fetchColumn();

if ($mode === 'hourly') {
    // Views per hour for a specific date (default: today)
    $date = $selectedDate ?: $today;
    $labels = [];
    $values = [];
    $reach_values = [];
    for ($h = 0; $h < 24; $h++) {
        $labels[] = sprintf('%02d:00', $h);
        $values[] = 0;
        $reach_values[] = 0;
    }
    $stmt = $pdo->prepare("SELECT HOUR(clicked_at) as hour, COUNT(*) as cnt FROM campaign_clicks WHERE campaign_id = ? AND DATE(clicked_at) = ? GROUP BY hour");
    $stmt->execute([$campaignId, $date]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $values[(int)$row['hour']] = (int)$row['cnt'];
    }
    $sum = array_sum($values);
    // Reach per hour (unique users per hour)
    $reachStmt = $pdo->prepare("SELECT HOUR(viewed_at) as hour, COUNT(DISTINCT user_id) as cnt FROM campaign_reach WHERE campaign_id = ? AND DATE(viewed_at) = ? GROUP BY hour");
    $reachStmt->execute([$campaignId, $date]);
    foreach ($reachStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $reach_values[(int)$row['hour']] = (int)$row['cnt'];
    }
    // Reach: unique users who saw the ad on this date
    $reachStmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM campaign_reach WHERE campaign_id = ? AND DATE(viewed_at) = ?");
    $reachStmt->execute([$campaignId, $date]);
    $reach = (int)$reachStmt->fetchColumn();
    // Clicks: unique users who clicked the ad on this date
    $clickStmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM campaign_clicks WHERE campaign_id = ? AND DATE(clicked_at) = ?");
    $clickStmt->execute([$campaignId, $date]);
    $clicks = (int)$clickStmt->fetchColumn();
    echo json_encode(['labels' => $labels, 'values' => $values, 'reach_values' => $reach_values, 'date' => $date, 'view_sum' => $sum, 'total_views' => $totalViews, 'reach' => $reach, 'clicks' => $clicks]);
    exit;
}

// Default: daily
$from = $fromParam ?: $startDate;
$to = $toParam ?: min($endDate, $today);
$labels = [];
$values = [];
$period = new DatePeriod(
    new DateTime($from),
    new DateInterval('P1D'),
    (new DateTime($to))->modify('+1 day')
);
foreach ($period as $date) {
    $labels[] = $date->format('Y-m-d');
    $values[] = 0;
}
$labelIndex = array_flip($labels);
$stmt = $pdo->prepare("SELECT DATE(clicked_at) as day, COUNT(*) as cnt FROM campaign_clicks WHERE campaign_id = ? AND DATE(clicked_at) BETWEEN ? AND ? GROUP BY day");
$stmt->execute([$campaignId, $from, $to]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if (isset($labelIndex[$row['day']])) {
        $values[$labelIndex[$row['day']]] = (int)$row['cnt'];
    }
}
// Calculate sum for selected range
$viewSum = array_sum($values);
// For daily mode, get reach and clicks for the selected range
$reach_values = array_fill(0, count($labels), 0);
$reachStmt = $pdo->prepare("SELECT DATE(viewed_at) as day, COUNT(DISTINCT user_id) as cnt FROM campaign_reach WHERE campaign_id = ? AND DATE(viewed_at) BETWEEN ? AND ? GROUP BY day");
$reachStmt->execute([$campaignId, $from, $to]);
foreach ($reachStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if (isset($labelIndex[$row['day']])) {
        $reach_values[$labelIndex[$row['day']]] = (int)$row['cnt'];
    }
}
$reach = array_sum($reach_values); // total unique users who saw the ad in the range
$clickStmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM campaign_clicks WHERE campaign_id = ? AND DATE(clicked_at) BETWEEN ? AND ?");
$clickStmt->execute([$campaignId, $from, $to]);
$clicks = (int)$clickStmt->fetchColumn();
echo json_encode(['labels' => $labels, 'values' => $values, 'reach_values' => $reach_values, 'from' => $from, 'to' => $to, 'view_sum' => $viewSum, 'total_views' => $totalViews, 'reach' => $reach, 'clicks' => $clicks]); 