<?php
require_once("../database/db_connect.php");

if (isset($_GET['campaign_id'])) {
    $campaignId = $_GET['campaign_id'];

    // Fetch Campaign Details
    $stmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE campaign_id = ?");
    $stmt->execute([$campaignId]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Click Data
    $clickStmt = $pdo->prepare("SELECT DATE(clicked_at) AS date, COUNT(*) AS count FROM campaign_clicks WHERE campaign_id = ? GROUP BY DATE(clicked_at)");
    $clickStmt->execute([$campaignId]);
    $clicks = $clickStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Reach Data
    $reachStmt = $pdo->prepare("SELECT DATE(viewed_at) AS date, COUNT(*) AS count FROM campaign_reach WHERE campaign_id = ? GROUP BY DATE(viewed_at)");
    $reachStmt->execute([$campaignId]);
    $reach = $reachStmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare response data
    $response = [
        'title' => $campaign['title'],
        'description' => $campaign['description'],
        'start_date' => $campaign['start_date'],
        'end_date' => $campaign['end_date'],
        'status' => $campaign['status'],
        'total_clicks' => count($clicks),
        'total_reach' => count($reach),
        'clicks' => $clicks,
        'reach' => $reach
    ];

    echo json_encode($response);
}
