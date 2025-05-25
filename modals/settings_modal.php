<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="addAddressForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body d-flex flex-column gap-3">
        <div class="form-section w-100">
          <input type="text" class="form-control mb-2" name="address_line" id="address_line" placeholder="Street, Building, House No." required />
          <input type="text" class="form-control mb-2" name="postal_code" id="postal_code" placeholder="Postal Code" required />

          <!-- Hidden inputs -->
          <input type="hidden" name="region_code" id="region_code" />
          <input type="hidden" name="province_code" id="province_code" />
          <input type="hidden" name="city_code" id="city_code" />
          <input type="hidden" name="barangay_code" id="barangay_code" />
          <input type="hidden" name="latitude" id="latitude" />
          <input type="hidden" name="longitude" id="longitude" />
          <input type="hidden" name="full_address" id="full_address" />

          <button type="button" id="locateMeBtn" class="btn btn-primary w-100 mb-2">
            <i class="bi bi-geo-alt-fill"></i> Use Current Location
          </button>

          <div id="selectedLocation" class="selected-location" style="display:none;"></div>

          <!-- Map moved here -->
          <div id="addressMap" style="height: 300px; border: 1px solid #ccc; border-radius: 8px;"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger btn-sm">Save Location</button>
      </div>
    </form>
  </div>
</div>
