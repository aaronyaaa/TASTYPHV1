<!-- edit_address_modal.php -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editAddressForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="addressMap" style="height: 300px; width: 100%; border-radius: 8px;"></div>
          <div id="addressLoading" style="display:none;text-align:center;">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span style="font-size:0.9em;">Fetching address...</span>
          </div>
          <div class="mt-3" id="addressSummary"></div>
          <input type="hidden" name="latitude" id="latitude">
          <input type="hidden" name="longitude" id="longitude">
          <input type="hidden" name="postal_code" id="postal_code">
          <input type="hidden" name="address_line" id="address_line">
          <input type="hidden" name="full_address" id="full_address">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<link href="https://unpkg.com/leaflet/dist/leaflet.css" rel="stylesheet" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let map, marker, addressTimeout = null;

function setAddressLoading(loading) {
  document.getElementById('addressLoading').style.display = loading ? 'block' : 'none';
}

function reverseGeocodeAndFill(lat, lng) {
  setAddressLoading(true);
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;
      document.getElementById('postal_code').value = data.address.postcode || '';
      document.getElementById('address_line').value = data.address.road || '';
      document.getElementById('full_address').value = data.display_name || '';
      document.getElementById('addressSummary').innerHTML = `
        <div><strong>Address:</strong> ${data.display_name}</div>
        <div><strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
      `;
    })
    .catch(() => {
      document.getElementById('addressSummary').innerHTML = '<span class="text-danger">Failed to fetch address.</span>';
    })
    .finally(() => setAddressLoading(false));
}

function initEditMap(lat, lng) {
  if (map) {
    map.setView([lat, lng], 16);
    marker.setLatLng([lat, lng]);
    reverseGeocodeAndFill(lat, lng);
    return;
  }
  map = L.map('addressMap').setView([lat, lng], 16);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  marker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    reverseGeocodeAndFill(pos.lat, pos.lng);
  });
  map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    reverseGeocodeAndFill(e.latlng.lat, e.latlng.lng);
  });
  reverseGeocodeAndFill(lat, lng);
}

document.addEventListener('DOMContentLoaded', function() {
  $('#editAddressModal').on('shown.bs.modal', function() {
    // Use user's current address or default to Davao
    let lat = parseFloat(document.getElementById('latitude').value) || 7.1907;
    let lng = parseFloat(document.getElementById('longitude').value) || 125.4553;
    initEditMap(lat, lng);
  });

  document.getElementById('editAddressForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    setAddressLoading(true);
    const formData = new FormData(this);
    try {
      const response = await fetch('backend/update_address.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      if (result.success) {
        alert(result.message || 'Address saved successfully!');
        $('#editAddressModal').modal('hide');
        window.location.reload();
      } else {
        alert('Error: ' + (result.message || 'Unknown error'));
      }
    } catch (err) {
      alert('Failed to save address: ' + err.message);
    } finally {
      setAddressLoading(false);
    }
  });
});
</script>