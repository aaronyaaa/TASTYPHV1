<!-- edit_address_modal.php -->
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

          <!-- Map -->
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

<link href="https://unpkg.com/leaflet/dist/leaflet.css" rel="stylesheet" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let map, marker;

function setAddressLoading(loading) {
  const btn = document.getElementById('locateMeBtn');
  if(loading) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Locating...';
  } else {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use Current Location';
  }
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
      const selectedLocation = document.getElementById('selectedLocation');
      selectedLocation.style.display = 'block';
      selectedLocation.textContent = `Address: ${data.display_name}`;
    })
    .catch(() => {
      const selectedLocation = document.getElementById('selectedLocation');
      selectedLocation.style.display = 'block';
      selectedLocation.textContent = 'Failed to fetch address.';
    })
    .finally(() => setAddressLoading(false));
}

function initMap(lat = 7.1907, lng = 125.4553) {
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

document.addEventListener('DOMContentLoaded', () => {
  // Initialize map with default or saved coords
  const latVal = parseFloat(document.getElementById('latitude').value);
  const lngVal = parseFloat(document.getElementById('longitude').value);
  if (!isNaN(latVal) && !isNaN(lngVal)) {
    initMap(latVal, lngVal);
  } else {
    initMap();
  }

  // Handle locate me button click
  document.getElementById('locateMeBtn').addEventListener('click', () => {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    setAddressLoading(true);

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        if (map) {
          map.setView([lat, lng], 16);
          marker.setLatLng([lat, lng]);
          reverseGeocodeAndFill(lat, lng);
          map.invalidateSize(); // Ensure proper rendering of the map after resize
        } else {
          initMap(lat, lng);
        }

        setAddressLoading(false);
      },
      (error) => {
        alert('Unable to retrieve your location. ' + error.message);
        setAddressLoading(false);
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  });

  // Optional: form submit handler here if needed

});
</script>
