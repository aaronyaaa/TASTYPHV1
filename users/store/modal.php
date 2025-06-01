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


<!-- Place Pre-order Modal -->
<div class="modal fade" id="placeOrderModal" tabindex="-1" aria-labelledby="placeOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-sm">
      <form id="preOrderForm" method="POST" action="../backend/preorder_submit.php">
        <div class="modal-header bg-light">
          <h5 class="modal-title" id="placeOrderLabel">Place a Pre-order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body small">
          <input type="hidden" name="seller_id" value="<?= $store['seller_id'] ?>">
          
          <div class="mb-2">
            <label for="product_name" class="form-label mb-1">Product Name</label>
            <input type="text" class="form-control form-control-sm" id="product_name" name="product_name" required>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label for="quantity" class="form-label mb-1">Quantity</label>
              <input type="number" class="form-control form-control-sm" id="quantity" name="quantity" min="1" required>
            </div>
            <div class="col-md-6">
              <label for="unit" class="form-label mb-1">Unit (e.g., pcs, kilos)</label>
              <input type="text" class="form-control form-control-sm" id="unit" name="unit">
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label for="preferred_date" class="form-label mb-1">Preferred Date</label>
              <input type="date" class="form-control form-control-sm" id="preferred_date" name="preferred_date">
            </div>
            <div class="col-md-6">
              <label for="preferred_time" class="form-label mb-1">Preferred Time</label>
              <input type="text" class="form-control form-control-sm" id="preferred_time" name="preferred_time" placeholder="e.g., 2:00 PM">
            </div>
          </div>

          <div class="mb-2">
            <label for="additional_notes" class="form-label mb-1">Additional Notes</label>
            <textarea class="form-control form-control-sm" id="additional_notes" name="additional_notes" rows="2"></textarea>
          </div>

          <div class="mb-2">
            <label for="full_address" class="form-label mb-1">Delivery Address</label>
            <textarea class="form-control form-control-sm" id="full_address" name="full_address" rows="2" required><?= htmlspecialchars($user['full_address'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="modal-footer bg-light py-2">
          <button type="submit" class="btn btn-sm btn-primary">Submit Order</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
