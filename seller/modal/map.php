<!-- Map Modal for each order -->
<div class="modal fade" id="mapModal<?= $orderId ?>" tabindex="-1" aria-labelledby="mapModalLabel<?= $orderId ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel<?= $orderId ?>">Map - Order #<?= $orderId ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="map<?= $orderId ?>" style="height: 400px; border-radius: 8px;"></div>
      </div>
    </div>
  </div>
</div>
