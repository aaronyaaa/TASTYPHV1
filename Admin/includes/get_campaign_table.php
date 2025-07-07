<?php 
require_once 'db_config.php';

// Get current date for comparison
$currentDate = date('Y-m-d');
?>
<table class="table table-hover">
  <thead>
    <tr>
      <th>Banner</th>
      <th>Title</th>
      <th>User Type</th>
      <th>Pricing</th>
      <th>Payment</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $pdo->prepare("
      SELECT cr.*, cp.duration_days, cp.price, cp.description as pricing_description,
             u.first_name, u.last_name, u.email
      FROM campaign_requests cr
      LEFT JOIN campaign_pricing cp ON cr.pricing_id = cp.pricing_id
      LEFT JOIN users u ON cr.user_id = u.id
      ORDER BY cr.created_at DESC
    ");
    $stmt->execute();
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $ongoingCount = 0;
    $expiredCount = 0;
    
    foreach ($campaigns as $c): 
      // Determine if campaign is ongoing or expired
      $isOngoing = false;
      $isExpired = false;
      
      if ($c['status'] === 'approved') {
        if ($c['start_date'] <= $currentDate && $c['end_date'] >= $currentDate) {
          $isOngoing = true;
          $ongoingCount++;
        } elseif ($c['end_date'] < $currentDate) {
          $isExpired = true;
          $expiredCount++;
        }
      }
    ?>
      <tr class="campaign-row" data-id="<?= $c['campaign_id'] ?>" 
          data-campaign='<?= json_encode($c) ?>'
          data-status="<?= $c['status'] ?>"
          data-ongoing="<?= $isOngoing ? '1' : '0' ?>"
          data-expired="<?= $isExpired ? '1' : '0' ?>">
        <td><img src="/tastyphv1/<?= htmlspecialchars($c['banner_image']) ?>" style="width: 80px; height: 40px; object-fit: cover; border-radius: 6px;"></td>
        <td>
          <strong><?= htmlspecialchars($c['title']) ?></strong>
          <br><small class="text-muted"><?= htmlspecialchars($c['description'] ?: 'No description') ?></small>
        </td>
        <td>
          <span class="badge bg-info"><?= ucfirst($c['user_type']) ?></span>
          <br><small><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></small>
        </td>
        <td>
          <?php if ($c['pricing_id']): ?>
            <div class="text-success">
              <strong>₱<?= number_format($c['price'], 2) ?></strong>
            </div>
            <small class="text-muted"><?= $c['duration_days'] ?> days</small>
            <br><small class="text-muted"><?= htmlspecialchars($c['pricing_description']) ?></small>
          <?php else: ?>
            <span class="text-muted">No pricing</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($c['payment_method']): ?>
            <span class="badge bg-<?= $c['payment_method'] === 'cash' ? 'success' : 'warning' ?>">
              <?= strtoupper($c['payment_method']) ?>
            </span>
            <br><small class="text-muted">₱<?= number_format($c['amount_spent'], 2) ?></small>
            <br><small class="text-muted"><?= ucfirst($c['payment_status']) ?></small>
          <?php else: ?>
            <span class="text-muted">No payment</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($isOngoing): ?>
            <span class="badge bg-success campaign-status-badge">
              <i class="fas fa-play-circle me-1"></i>Ongoing
            </span>
          <?php elseif ($isExpired): ?>
            <span class="badge bg-warning campaign-status-badge">
              <i class="fas fa-clock me-1"></i>Expired
            </span>
          <?php else: ?>
            <span class="badge <?= $c['status'] === 'approved' ? 'bg-success' : ($c['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary') ?> campaign-status-badge">
              <?= ucfirst($c['status'] ?? 'Pending') ?>
            </span>
          <?php endif; ?>
          <br><small class="text-muted">
            <?= date('M d, Y', strtotime($c['start_date'])) ?> - <?= date('M d, Y', strtotime($c['end_date'])) ?>
          </small>
        </td>

      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
// Update the counts for ongoing and expired campaigns
document.getElementById('ongoing-count').textContent = '<?= $ongoingCount ?>';
document.getElementById('expired-count').textContent = '<?= $expiredCount ?>';
</script> 