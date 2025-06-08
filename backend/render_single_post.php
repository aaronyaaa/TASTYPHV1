<?php
function render_post($post_id, $viewer_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.*, u.first_name, u.last_name, u.profile_pics 
                           FROM posts p JOIN users u ON p.user_id = u.id 
                           WHERE p.id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) return;

    $poster_name = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
    $content = nl2br(htmlspecialchars($post['content']));
    $profile_pic = !empty($post['profile_pics']) ? "../" . $post['profile_pics'] : "../assets/images/default-profile.png";
    $created_at = date('F j, Y g:i a', strtotime($post['created_at']));

    echo "<div class='card shadow-sm mb-4' style='max-width: 500px; margin:auto;'>
            <div class='card-body'>
                <div class='d-flex align-items-center mb-2'>
                    <img src='{$profile_pic}' width='40' height='40' class='rounded-circle me-2'>
                    <div>
                        <strong>{$poster_name}</strong><br>
                        <small class='text-muted'>{$created_at}</small>
                    </div>
                </div>
                <p>{$content}</p>
            </div>
        </div>";
}
