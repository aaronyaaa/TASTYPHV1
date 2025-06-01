<?php 
require_once 'db_config.php';

$type = $_GET['type'] ?? 'seller';

if (!in_array($type, ['seller', 'supplier', 'all'])) {
    http_response_code(400);
    echo "Invalid application type.";
    exit;
}

$tables = [];

if ($type === 'all') {
    $tables = [
        ['seller_applications', 'seller'],
        ['supplier_applications', 'supplier'],
    ];
} else {
    $tables[] = [$type === 'supplier' ? 'supplier_applications' : 'seller_applications', $type];
}
?>

<table class="table table-hover">
  <thead>
    <tr>
      <th>Full Name</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tables as [$table, $userType]):
      $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.middle_name, u.last_name, u.email, u.profile_pics, a.status
        FROM users u
        INNER JOIN {$table} a ON u.id = a.user_id
      ");
      $stmt->execute();
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($users as $u): ?>
        <tr class="user-row" data-id="<?= $u['id'] ?>" data-type="<?= $userType ?>">
          <td>
            <img src="/tastyphv1/<?= $u['profile_pics'] ?>" class="profile-img-sm me-2" alt="profile">
            <?= htmlspecialchars($u['first_name'] . ' ' . $u['middle_name'] . ' ' . $u['last_name']) ?>
          </td>
          <td>
            <span class="badge <?= $u['status'] === 'approved' ? 'bg-success' : ($u['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary') ?> status-badge">
              <?= ucfirst($u['status'] ?? 'Pending') ?>
            </span>
          </td>
          <td>
            <button class="btn btn-success btn-sm approve-btn" data-id="<?= $u['id'] ?>">Approve</button>
            <button class="btn btn-danger btn-sm reject-btn" data-id="<?= $u['id'] ?>">Reject</button>
          </td>
        </tr>
      <?php endforeach;
    endforeach; ?>
  </tbody>
</table>
