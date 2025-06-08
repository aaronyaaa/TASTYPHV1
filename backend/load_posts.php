<?php
include_once("../database/session.php");
include_once("../database/db_connect.php");

$user_id = $_SESSION['userId'];
?>

<script>
    const userId = <?= json_encode($user_id) ?>;
    let hideTimeout;
</script>

<?php
$sql = "
    SELECT p.*, u.first_name, u.last_name, u.profile_pics 
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as $post) {
    $post_id = $post['id'];
    $poster_name = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
    $content = nl2br(htmlspecialchars($post['content']));
    $profile_pic = !empty($post['profile_pics']) ? "../" . $post['profile_pics'] : "../assets/images/default-profile.png";
    $created_at = date('F j, Y g:i a', strtotime($post['created_at']));

    echo "<div class='card fb-post-card shadow-sm mb-4' style='max-width: 500px; margin:auto;'>
            <div class='post-header'>
                <div class='d-flex align-items-center'>
                    <img src='{$profile_pic}' alt='Profile Picture' style='width: 40px; height: 40px; border-radius: 50%; object-fit: cover;'>
                    <div class='ms-2'>
                        <strong>{$poster_name}</strong><br>
                        <small class='text-muted'>{$created_at} · <i class='bi bi-globe'></i></small>
                    </div>
                </div>
            </div>
            <p class='card-text'>{$content}</p>";

    // Media
    $mediaStmt = $pdo->prepare("SELECT * FROM post_media WHERE post_id = ?");
    $mediaStmt->execute([$post_id]);
    $media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($media as $file) {
        $path = htmlspecialchars($file['file_path']);
        $webPath = '../' . ltrim($path, '/');
        if ($file['media_type'] === 'image') {
            echo "<img src='{$webPath}' class='post-img w-100' alt='Post Image'>";
        } elseif ($file['media_type'] === 'video') {
            echo "<video controls class='w-100'><source src='{$webPath}' type='video/mp4'></video>";
        }
    }

    // Reactions
    $reactionSummaryStmt = $pdo->prepare("SELECT reaction_type, COUNT(*) AS count FROM post_reactions WHERE post_id = ? GROUP BY reaction_type");
    $reactionSummaryStmt->execute([$post_id]);
    $reactionSummary = $reactionSummaryStmt->fetchAll(PDO::FETCH_ASSOC);

    $userReactStmt = $pdo->prepare("SELECT reaction_type FROM post_reactions WHERE post_id = ? AND user_id = ?");
    $userReactStmt->execute([$post_id, $user_id]);
    $userReaction = $userReactStmt->fetchColumn();

    $reactionIcon = '👍';
    $reactionLabel = 'Like';
    if ($userReaction) {
        $map = ['like' => '👍', 'love' => '❤️', 'haha' => '😂', 'wow' => '😲', 'sad' => '😢', 'angry' => '😠'];
        $reactionIcon = $map[strtolower($userReaction)] ?? '👍';
        $reactionLabel = ucfirst($userReaction);
    }
    $reactionClass = $userReaction ? 'liked-' . strtolower($userReaction) : '';
    $totalReacts = array_sum(array_column($reactionSummary, 'count'));
    $reactionString = '';
    foreach ($reactionSummary as $r) {
        $reactionString .= match ($r['reaction_type']) {
            'like' => '👍', 'love' => '❤️', 'haha' => '😂', 'wow' => '😲', 'sad' => '😢', 'angry' => '😠', default => ''
        };
    }

    echo "<div class='card-body d-flex justify-content-between align-items-center border-top border-bottom' style='font-size: 14px; color: #65676b;'>
            <span id='react-summary-{$post_id}'>{$reactionString} {$totalReacts}</span>
            <span class='me-3' style='cursor:pointer;' onclick='toggleCommentInput({$post_id})'><i class='bi bi-chat'></i> Comment</span>
          </div>";

    echo "<div class='action-buttons'>
            <div class='reaction-wrapper'>
                <button id='like-btn-{$post_id}' class='btn-like {$reactionClass}' data-post-id='{$post_id}' data-reaction='" . strtolower($userReaction) . "' onclick=\"react({$post_id})\">
                    {$reactionIcon} {$reactionLabel}
                </button>
                <div class='reaction-options'>
                    <span onclick=\"react({$post_id}, 'like')\">👍</span>
                    <span onclick=\"react({$post_id}, 'love')\">❤️</span>
                    <span onclick=\"react({$post_id}, 'haha')\">😂</span>
                    <span onclick=\"react({$post_id}, 'wow')\">😲</span>
                    <span onclick=\"react({$post_id}, 'sad')\">😢</span>
                    <span onclick=\"react({$post_id}, 'angry')\">😠</span>
                </div>
            </div>
            <button class='action-button' onclick='toggleCommentInput({$post_id})'><i class='bi bi-chat'></i> Comment</button>
            <button class='action-button'><i class='bi bi-share'></i> Share</button>
          </div>";

    // Comments and input box
    echo "<div class='comment-section' id='comments-{$post_id}'>
            <div class='comment-list' id='comment-list-{$post_id}'>";

    $commentStmt = $pdo->prepare("SELECT c.comment, u.first_name, u.last_name, u.profile_pics FROM post_comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC");
    $commentStmt->execute([$post_id]);
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as $c) {
        $name = htmlspecialchars($c['first_name'] . ' ' . $c['last_name']);
        $text = htmlspecialchars($c['comment']);
        $avatar = !empty($c['profile_pics']) ? "../" . $c['profile_pics'] : "../assets/images/default-profile.png";
        echo "<div class='comment'>
                <img src='{$avatar}' width='32' height='32' class='rounded-circle' style='object-fit:cover;'>
                <div class='comment-content'><strong>{$name}</strong> {$text}</div>
              </div>";
    }

    echo "<div class='comment-input-wrapper'>
            <img src='{$profile_pic}' width='32' height='32' class='rounded-circle' style='object-fit:cover;'>
            <input type='text' id='comment-text-{$post_id}' class='comment-input' placeholder='Write a comment...' onkeydown=\"handleEnter(event, {$post_id})\">
            <button class='btn btn-sm btn-primary' onclick='comment({$post_id})'><i class='bi bi-send'></i></button>
          </div>
        </div>
      </div>
    </div>";
}
?>

<script>
function react(postId, reactionType = null) {
    const btn = $(`#like-btn-${postId}`);
    const hasReacted = btn.attr('class').includes('liked-');
    if (!reactionType) {
        reactionType = hasReacted ? btn.data('reaction') : 'like';
    }

    $.post('../backend/post_react.php', { postId, userId, reactionType }, function(response) {
        const emojiMap = {
            like: '👍', love: '❤️', haha: '😂', wow: '😲', sad: '😢', angry: '😠'
        };
        if (response.success) {
            if (response.removed) {
                btn.removeClass().addClass('btn-like').html('👍 Like').data('reaction', '');
            } else {
                const emoji = emojiMap[reactionType] || '👍';
                const label = reactionType.charAt(0).toUpperCase() + reactionType.slice(1);
                btn.removeClass().addClass(`btn-like liked-${reactionType}`).html(`${emoji} ${label}`).data('reaction', reactionType);
            }
            $(`#react-summary-${postId}`).html(`${response.summary} ${response.total}`);
        }
    }, 'json');
}

function comment(postId) {
    const input = document.getElementById(`comment-text-${postId}`);
    const text = input.value.trim();
    if (!text) return;

    $.post('../backend/post_comment.php', {
        postId, commentText: text, userId
    }, function(response) {
        if (response.success) {
            const newComment = `
                <div class='comment'>
                    <img src='${response.avatar}' width='32' height='32' class='rounded-circle' style='object-fit:cover;'>
                    <div class='comment-content'><strong>${response.name}</strong> ${text}</div>
                </div>`;
            $(`#comment-list-${postId}`).prepend(newComment);
            input.value = '';
        }
    }, 'json');
}

function handleEnter(event, postId) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        comment(postId);
    }
}

function toggleCommentInput(postId) {
    document.getElementById(`comment-text-${postId}`).focus();
}

function rebindPostScripts() {
    $('.reaction-wrapper').off('mouseenter mouseleave').hover(
        function () {
            $(this).find('.reaction-options').fadeIn(100);
        },
        function () {
            const $options = $(this).find('.reaction-options');
            hideTimeout = setTimeout(() => $options.fadeOut(100), 300);
        }
    );

    $('.reaction-options').off('mouseenter mouseleave').on('mouseenter', function () {
        clearTimeout(hideTimeout);
    }).on('mouseleave', function () {
        const $options = $(this);
        hideTimeout = setTimeout(() => $options.fadeOut(100), 300);
    });
}

$(document).ready(() => {
    rebindPostScripts(); // ✅ apply reaction hover when loading
});
</script>
