    <?php
    include_once("../database/db_connect.php");

    $postId = $_POST['postId'];
    $userId = $_POST['userId'];
    $reactionType = $_POST['reactionType'];

    if (!$postId || !$userId || !$reactionType) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }

    // Check if user already reacted
    $stmt = $pdo->prepare("SELECT id, reaction_type FROM post_reactions WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    $existing = $stmt->fetch();

    $wasRemoved = false;

    if ($existing) {
        if (strtolower($existing['reaction_type']) === strtolower($reactionType)) {
            // Toggle off (unlike)
            $delete = $pdo->prepare("DELETE FROM post_reactions WHERE id = ?");
            $success = $delete->execute([$existing['id']]);
            $wasRemoved = true;
        } else {
            // Update to new reaction
            $update = $pdo->prepare("UPDATE post_reactions SET reaction_type = ?, updated_at = NOW() WHERE id = ?");
            $success = $update->execute([$reactionType, $existing['id']]);
        }
    } else {
        // Insert new reaction
        $insert = $pdo->prepare("INSERT INTO post_reactions (post_id, user_id, reaction_type, created_at) VALUES (?, ?, ?, NOW())");
        $success = $insert->execute([$postId, $userId, $reactionType]);
    }

    // Fetch updated summary
    $summaryStmt = $pdo->prepare("SELECT reaction_type, COUNT(*) as count FROM post_reactions WHERE post_id = ? GROUP BY reaction_type");
    $summaryStmt->execute([$postId]);
    $summary = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);

    $emojiMap = ['like' => '👍', 'love' => '❤️', 'haha' => '😂', 'wow' => '😲', 'sad' => '😢', 'angry' => '😠'];
    $total = 0;
    $summaryString = '';

    foreach ($summary as $r) {
        $emoji = $emojiMap[strtolower($r['reaction_type'])] ?? '👍';
        $summaryString .= $emoji . ' ';
        $total += $r['count'];
    }

    echo json_encode([
        'success' => $success,
        'removed' => $wasRemoved,
        'summary' => trim($summaryString),
        'total' => $total
    ]);
