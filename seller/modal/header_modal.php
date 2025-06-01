<div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="editStoreModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content shadow-sm border-0 rounded-3">
      <form id="storeEditForm">
        <div class="modal-header py-2 px-3 bg-light">
          <h6 class="modal-title" id="editStoreModalLabel">Edit Store Info</h6>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-3 py-2 small">
          <div class="mb-2">
            <label for="businessName" class="form-label mb-1">Business Name</label>
            <input type="text" class="form-control form-control-sm" id="businessName" name="business_name"
              value="<?= htmlspecialchars($storeName) ?>" required>
          </div>
          <div class="mb-2">
            <label for="businessDescription" class="form-label mb-1">Description</label>
            <textarea class="form-control form-control-sm" id="businessDescription" name="description" rows="2"><?= htmlspecialchars($storeDescription) ?></textarea>
          </div>
          <div class="mb-2">
            <label for="storeAddress" class="form-label mb-1">Store Address</label>
            <input type="text" class="form-control form-control-sm" id="storeAddress" name="store_address"
              value="<?= htmlspecialchars($store['store_address'] ?? '') ?>">
          </div>
          <div class="mb-2">
            <label for="fullAddress" class="form-label mb-1">Full Address</label>
            <textarea class="form-control form-control-sm" id="fullAddress" name="full_address" rows="2"><?= htmlspecialchars($store['full_address'] ?? '') ?></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label mb-1">Store Location</label>
            <div id="map" class="rounded border" style="height: 200px;"></div>
            <div class="row g-2 mt-2">
              <div class="col">
                <label for="latitude" class="form-label mb-1">Lat</label>
                <input type="text" id="latitude" name="latitude" class="form-control form-control-sm"
                  value="<?= $store['latitude'] ?? '' ?>" required>
              </div>
              <div class="col">
                <label for="longitude" class="form-label mb-1">Lng</label>
                <input type="text" id="longitude" name="longitude" class="form-control form-control-sm"
                  value="<?= $store['longitude'] ?? '' ?>" required>
              </div>
            </div>
            <small class="text-muted">Drag or input to set store location.</small>
          </div>
        </div>
        <div class="modal-footer py-2 px-3 bg-light">
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
