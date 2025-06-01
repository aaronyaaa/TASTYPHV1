    <?php
    require_once __DIR__ . '/../../database/db_connect.php';
    require_once __DIR__ . '/../../database/session.php';

    header('Content-Type: application/json');

    $currentUserId = $_SESSION['user']['id'] ?? null;
    if (!$currentUserId) {
    echo json_encode([]);
    exit;
    }

    try {
    $stmt = $pdo->prepare("
        SELECT 
        u.id,
        u.first_name,
        u.last_name,
        u.profile_pics,
        (
            SELECT COUNT(*) 
            FROM messages m 
            WHERE m.sender_id = u.id 
            AND m.receiver_id = :uid 
            AND m.is_read = 0
        ) AS unread_count
        FROM users u
        WHERE u.id IN (
        SELECT DISTINCT 
            CASE 
            WHEN sender_id = :uid THEN receiver_id
            ELSE sender_id
            END
        FROM messages
        WHERE sender_id = :uid OR receiver_id = :uid
        )
    ");
    $stmt->execute([':uid' => $currentUserId]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
    } catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch chat users', 'details' => $e->getMessage()]);
    }

