<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

$user_id = $_SESSION['userId'];
$content = $_POST['content'] ?? '';
$audience = $_POST['audience'] ?? 'public';
$created_at = date("Y-m-d H:i:s");

// Insert post
$stmt = $pdo->prepare("INSERT INTO posts (user_id, content, audience, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $content, $audience, $created_at, $created_at]);
$post_id = $pdo->lastInsertId();

// Handle uploads
foreach ($_FILES['media_files']['tmp_name'] as $index => $tmpPath) {
    if ($tmpPath) {
        $fileName = basename($_FILES['media_files']['name'][$index]);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $type = in_array($ext, ['mp4', 'mov', 'avi']) ? 'video' : 'image';
        $subDir = $type === 'video' ? '../uploads/posts/video/' : '../uploads/posts/picture/';
        if (!is_dir($subDir)) {
            mkdir($subDir, 0777, true);
        }

        $targetPath = $subDir . uniqid() . "_" . $fileName;
        move_uploaded_file($tmpPath, $targetPath);
        $relativePath = str_replace('../', '', $targetPath);

        $stmt = $pdo->prepare("INSERT INTO post_media (post_id, file_path, media_type) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $relativePath, $type]);
    }
}

// Load and render the new post
include("render_single_post.php");
render_post($post_id, $user_id);
