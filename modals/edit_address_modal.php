<!-- edit_address_modal.php -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <form id="editAddressForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="editAddressMap" style="height: 200px; width: 100%; border-radius: 8px;"></div>
          <div id="editAddressLoading" style="display:none;text-align:center;">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span style="font-size:0.9em;">Fetching address...</span>
          </div>
          <div class="mt-3" id="editAddressSummary"></div>
          
          <div class="form-group mt-3">
            <input type="text" class="form-control mb-2" name="address_line" id="edit_address_line" placeholder="Street, Building, House No." required />
            <input type="text" class="form-control mb-2" name="postal_code" id="edit_postal_code" placeholder="Postal Code" required />
          </div>

          <!-- Hidden inputs -->
          <input type="hidden" name="latitude" id="edit_latitude">
          <input type="hidden" name="longitude" id="edit_longitude">
          <input type="hidden" name="full_address" id="edit_full_address">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<link href="https://unpkg.com/leaflet/dist/leaflet.css" rel="stylesheet" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const editAddressModal = document.getElementById('editAddressModal');
  const editAddressForm = document.getElementById('editAddressForm');
  let editMap, editMarker;

  // Initialize map when modal is shown
  editAddressModal.addEventListener('shown.bs.modal', function() {
    if (!editMap) {
      const lat = <?php echo $user['latitude'] ?? '12.8797'; ?>;
      const lng = <?php echo $user['longitude'] ?? '121.7740'; ?>;
      
      editMap = L.map('editAddressMap').setView([lat, lng], 16);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(editMap);

      editMarker = L.marker([lat, lng], { draggable: true }).addTo(editMap);

      editMarker.on('dragend', function(e) {
        updateEditLocationFromMarker(e.target.getLatLng());
      });

      editMap.on('click', function(e) {
        editMarker.setLatLng(e.latlng);
        updateEditLocationFromMarker(e.latlng);
      });

      // Fill form with current address
      document.getElementById('edit_address_line').value = '<?php echo addslashes($user['streetname'] ?? ''); ?>';
      document.getElementById('edit_postal_code').value = '<?php echo addslashes($user['postal_code'] ?? ''); ?>';
      document.getElementById('edit_latitude').value = lat;
      document.getElementById('edit_longitude').value = lng;
      document.getElementById('edit_full_address').value = '<?php echo addslashes($user['full_address'] ?? ''); ?>';
    } else {
      editMap.invalidateSize();
    }
  });

  function updateEditLocationFromMarker(pos) {
    document.getElementById('editAddressLoading').style.display = 'block';
    
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`)
      .then(res => res.json())
      .then(data => {
        document.getElementById('edit_latitude').value = pos.lat;
        document.getElementById('edit_longitude').value = pos.lng;
        document.getElementById('edit_address_line').value = data.address.road || '';
        document.getElementById('edit_postal_code').value = data.address.postcode || '';
        document.getElementById('edit_full_address').value = data.display_name || '';

        document.getElementById('editAddressSummary').innerHTML = `
          <div><strong>Address:</strong> ${data.display_name}</div>
          <div><strong>Coordinates:</strong> ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}</div>
        `;
      })
      .catch(() => {
        document.getElementById('editAddressSummary').innerHTML = '<span class="text-danger">Failed to fetch address.</span>';
      })
      .finally(() => {
        document.getElementById('editAddressLoading').style.display = 'none';
      });
  }

  // Handle form submission
  editAddressForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
      const response = await fetch('../backend/update_address.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        alert('Address updated successfully');
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (error) {
      alert('Failed to update address: ' + error.message);
    }
  });
});
</script>