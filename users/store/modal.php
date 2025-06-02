<div class="modal fade" id="shopInfoModal"
     data-lat="<?= $lat ?>"
     data-lng="<?= $lng ?>"
     data-name="<?= htmlspecialchars($store['business_name']) ?>"
     tabindex="-1" aria-labelledby="shopInfoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-3 shadow-sm border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="shopInfoLabel">Store Location & Info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body small">
        <div class="mb-2">
          <strong>Store Address:</strong><br>
          <?= htmlspecialchars($store['store_address'] ?? 'Not available') ?>
        </div>
        <div class="mb-2">
          <strong>Full Address:</strong><br>
          <?= htmlspecialchars($store['full_address'] ?? 'Not available') ?>
        </div>
        <div class="mb-2">
          <strong>Location Map:</strong>
          <div id="shopMap" class="rounded border mt-2" style="height: 300px;"></div>
        </div>
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

