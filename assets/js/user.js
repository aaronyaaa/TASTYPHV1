
let map, marker;

function setAddressLoading(loading) {
  const btn = document.getElementById('locateMeBtn');
  btn.disabled = loading;
  btn.innerHTML = loading
    ? '<span class="spinner-border spinner-border-sm"></span> Locating...'
    : '<i class="bi bi-geo-alt-fill"></i> Use Current Location';
}

function reverseGeocodeAndFill(lat, lng) {
  setAddressLoading(true);
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
    headers: {
      'Accept-Language': 'en',
      'User-Agent': 'TastyPH-UserProfileEditor/1.0 (your@email.com)' // change to real email
    }
  })
    .then(res => res.json())
    .then(data => {
      const address = data.address || {};
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;

      document.getElementById('postal_code').value = address.postcode || '';
      document.getElementById('address_line').value =
        [address.house_number, address.road, address.neighbourhood].filter(Boolean).join(', ') || '';

      document.getElementById('full_address').value = data.display_name || '';
      const selectedLocation = document.getElementById('selectedLocation');
      selectedLocation.style.display = 'block';
      selectedLocation.textContent = `Address: ${data.display_name}`;
    })
    .catch(() => {
      const selectedLocation = document.getElementById('selectedLocation');
      selectedLocation.style.display = 'block';
      selectedLocation.textContent = '❌ Failed to fetch address from coordinates.';
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
  marker.on('dragend', function (e) {
    const pos = e.target.getLatLng();
    reverseGeocodeAndFill(pos.lat, pos.lng);
  });

  map.on('click', function (e) {
    marker.setLatLng(e.latlng);
    reverseGeocodeAndFill(e.latlng.lat, e.latlng.lng);
  });

  reverseGeocodeAndFill(lat, lng);
}

document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('editUserModal');

  modal.addEventListener('shown.bs.modal', () => {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    if (!isNaN(lat) && !isNaN(lng)) {
      initMap(lat, lng);
    } else {
      initMap();
    }
    setTimeout(() => map?.invalidateSize(), 200); // Ensure correct rendering
  });

  // 👇 FIX: Invalidate map when switching to the tab
  const locationTab = document.querySelector('#location-tab');
  locationTab.addEventListener('shown.bs.tab', () => {
    setTimeout(() => {
      if (map) {
        map.invalidateSize();
      }
    }, 300);
  });

  document.getElementById('locateMeBtn').addEventListener('click', () => {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }
    setAddressLoading(true);
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        initMap(latitude, longitude);
        setAddressLoading(false);
      },
      (error) => {
        alert('Unable to retrieve your location. ' + error.message);
        setAddressLoading(false);
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
  });
});


document.getElementById('editUserForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('../backend/user_profile_update.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert('✅ Location updated successfully.');
        location.reload();
      } else {
        alert('❌ ' + (data.message || 'Failed to save.'));
        console.error(data);
      }
    })
    .catch(err => {
      alert('❌ Request failed.');
      console.error(err);
    });
});

document.getElementById('toggleUserStatus').addEventListener('change', function () {
  const isOnline = this.checked;
  const newStatus = isOnline ? 'online' : 'offline';

  fetch('../backend/update_user_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type: 'status', value: newStatus })
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        // Update status text
        const label = document.querySelector('label[for="toggleUserStatus"]');
        if (label) label.textContent = `User Status (${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)})`;

        // Update badge
        const badge = document.querySelector('.badge');
        if (badge) {
          badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
          badge.classList.remove('bg-success', 'bg-secondary');
          badge.classList.add(isOnline ? 'bg-success' : 'bg-secondary');
        }

        // Update dot
        let dot = document.querySelector('.status-dot');
        if (!dot && isOnline) {
          const nameHeading = document.querySelector('h2.fw-bold');
          dot = document.createElement('span');
          dot.className = 'status-dot p-2 bg-success border border-white rounded-circle';
          dot.style.width = '12px';
          dot.style.height = '12px';
          nameHeading.appendChild(dot);
        } else if (dot && !isOnline) {
          dot.remove();
        }
      }
    })
    .catch(err => console.error('Status update failed:', err));
});

