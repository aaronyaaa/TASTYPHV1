<?php
include_once("../database/db_connect.php");

$postId = $_POST['postId'];
$userId = $_POST['userId'];
$commentText = trim($_POST['commentText']);

$insert = $pdo->prepare("INSERT INTO post_comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$success = $insert->execute([$postId, $userId, $commentText]);

if ($success) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_pics FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $avatar = !empty($user['profile_pics']) ? "../" . $user['profile_pics'] : "../assets/images/default-profile.png";
    echo json_encode([
        'success' => true,
        'name' => htmlspecialchars($user['first_name'] . ' ' . $user['last_name']),
        'avatar' => $avatar
    ]);
} else {
    echo json_encode(['success' => false]);
}
