<?php 
require_once 'db_config.php';
?>
<table class="table table-hover">
  <thead>
    <tr>
      <th>Banner</th>
      <th>Title</th>
      <th>User Type</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM campaign_requests ORDER BY created_at DESC");
    $stmt->execute();
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($campaigns as $c): ?>
      <tr class="campaign-row" data-id="<?= $c['campaign_id'] ?>">
        <td><img src="/tastyphv1/<?= htmlspecialchars($c['banner_image']) ?>" style="width: 80px; height: 40px; object-fit: cover; border-radius: 6px;"></td>
        <td><?= htmlspecialchars($c['title']) ?></td>
        <td><?= htmlspecialchars($c['user_type']) ?></td>
        <td>
          <span class="badge <?= $c['status'] === 'approved' ? 'bg-success' : ($c['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary') ?> status-badge">
            <?= ucfirst($c['status'] ?? 'Pending') ?>
          </span>
        </td>
        <td>
          <button class="btn btn-success btn-sm approve-btn" data-id="<?= $c['campaign_id'] ?>">Approve</button>
          <button class="btn btn-danger btn-sm reject-btn" data-id="<?= $c['campaign_id'] ?>">Reject</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table> 