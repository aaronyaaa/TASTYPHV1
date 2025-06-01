// Make map and marker globally accessible
window.map = null;
window.marker = null;
let selectedLocation = null;

// Expose functions to window object
window.initMap = function(containerId = 'addressMap', initialLat = 12.8797, initialLng = 121.7740, zoomLevel = 6) {
  if (window.map) {
    window.map.invalidateSize();
    return;
  }

  window.map = L.map(containerId).setView([initialLat, initialLng], zoomLevel);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(window.map);

  window.marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(window.map);

  window.marker.on('dragend', e => {
    window.updateLocationFromMarker(e.target.getLatLng());
  });

  window.map.on('click', e => {
    window.marker.setLatLng(e.latlng);
    window.updateLocationFromMarker(e.latlng);
  });

  return { map: window.map, marker: window.marker };
};

window.updateLocationFromMarker = function(pos) {
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`)
    .then(res => res.json())
    .then(data => {
      selectedLocation = {
        lat: pos.lat,
        lng: pos.lng,
        address: data.display_name,
        details: data.address
      };

      const fields = {
        'latitude': pos.lat,
        'longitude': pos.lng,
        'address_line': data.address.road || '',
        'postal_code': data.address.postcode || '',
        'full_address': data.display_name || ''
      };

      Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
      });

      const selectedLocationEl = document.getElementById('selectedLocation');
      if (selectedLocationEl) {
        selectedLocationEl.style.display = 'block';
        selectedLocationEl.innerHTML = `
          <b>Address:</b> ${data.display_name}<br>
          <b>Coordinates:</b> ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}
        `;
      }
    })
    .catch(() => alert('Failed to fetch address'));
};

window.updateMapFromAddress = async function() {
  const elements = {
    region: document.querySelector('select[name="region_code"]'),
    province: document.querySelector('select[name="province_code"]'),
    city: document.querySelector('select[name="city_code"]'),
    barangay: document.querySelector('select[name="barangay_code"]'),
    address: document.querySelector('input[name="address_line"]'),
    postal: document.querySelector('input[name="postal_code"]')
  };

  if (!Object.values(elements).every(el => el) || !window.map || !window.marker) return;

  const values = {
    region: elements.region.options[elements.region.selectedIndex]?.text || '',
    province: elements.province.options[elements.province.selectedIndex]?.text || '',
    city: elements.city.options[elements.city.selectedIndex]?.text || '',
    barangay: elements.barangay.options[elements.barangay.selectedIndex]?.text || '',
    street: elements.address.value.trim(),
    postal: elements.postal.value.trim()
  };

  if (!Object.values(values).every(val => val)) return;

  const fullAddress = `${values.street}, ${values.barangay}, ${values.city}, ${values.province}, ${values.region}, ${values.postal}, Philippines`;

  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}`);
    const data = await response.json();

    if (data && data.length > 0) {
      const lat = parseFloat(data[0].lat);
      const lon = parseFloat(data[0].lon);
      window.map.setView([lat, lon], 16);
      window.marker.setLatLng([lat, lon]);
    }
  } catch (error) {
    console.error('Geocoding error:', error);
  }
};

async function updatePSGCCodes(address) {
  const [regions, provinces, cities, barangays] = await Promise.all([
    fetchJSON('https://psgc.gitlab.io/api/regions/'),
    fetchJSON('https://psgc.gitlab.io/api/provinces/'),
    fetchJSON('https://psgc.gitlab.io/api/cities-municipalities/'),
    fetchJSON('https://psgc.gitlab.io/api/barangays/')
  ]);

  let city = cities.find(c => c.name.toLowerCase() === (address.city || address.town || '').toLowerCase());
  if (!city) city = cities.find(c => (address.city || address.town || '').toLowerCase().includes(c.name.toLowerCase()));
  document.getElementById('city_code').value = city ? city.code : '';

  let province = provinces.find(p =>
    p.code === (city ? city.provinceCode : '') ||
    p.name.toLowerCase() === (address.state || '').toLowerCase()
  );
  document.getElementById('province_code').value = province ? province.code : '';

  let region = regions.find(r =>
    r.code === (province ? province.regionCode : '') ||
    r.name.toLowerCase() === (address.region || address.state || '').toLowerCase()
  );
  document.getElementById('region_code').value = region ? region.code : '';

  let barangay = barangays.find(b =>
    b.name.toLowerCase() === (address.suburb || address.neighbourhood || '').toLowerCase() &&
    b.cityCode === (city ? city.code : '')
  );
  document.getElementById('barangay_code').value = barangay ? barangay.code : '';
}

async function fetchJSON(url) {
  const response = await fetch(url);
  if (!response.ok) throw new Error(`Failed to fetch ${url}`);
  return await response.json();
}

document.addEventListener('DOMContentLoaded', () => {
  const addAddressModal = document.getElementById('addAddressModal');
  if (addAddressModal) {
    addAddressModal.addEventListener('shown.bs.modal', () => {
      initMap();
      setTimeout(() => {
        if (window.map) window.map.invalidateSize();
        updateMapFromAddress();
      }, 200);
    });
  }

  const locateMeBtn = document.getElementById('locateMeBtn');
  if (locateMeBtn) {
    locateMeBtn.addEventListener('click', () => {
      if (!navigator.geolocation) {
        alert('Geolocation not supported');
        return;
      }
      navigator.geolocation.getCurrentPosition(pos => {
        const coords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        window.map.setView(coords, 16);
        window.marker.setLatLng(coords);
        window.updateLocationFromMarker(coords);
      }, () => alert('Could not get your location'));
    });
  }

  const addAddressForm = document.getElementById('addAddressForm');
  if (addAddressForm) {
    addAddressForm.addEventListener('submit', async e => {
      e.preventDefault();
      
      // Debug form data
      console.log('Form submission started');
      console.log('Selected location:', selectedLocation);

      if (!selectedLocation) {
        alert('Please select a location on the map.');
        return;
      }

      const formData = new FormData(e.target);
      
      // Ensure required fields are set
      formData.set('latitude', selectedLocation.lat);
      formData.set('longitude', selectedLocation.lng);
      formData.set('full_address', selectedLocation.address);

      // Debug form data
      console.log('Form data being sent:', Object.fromEntries(formData));

      try {
        const res = await fetch('../backend/update_address.php', {
          method: 'POST',
          body: formData
        });

        const json = await res.json();
        console.log('Server response:', json);

        if (json.success) {
          alert(json.message);
          $('#addAddressModal').modal('hide');
          window.location.reload();
        } else {
          alert('Error: ' + (json.message || 'Unknown error'));
          if (json.debug) {
            console.error('Debug info:', json.debug);
          }
        }
      } catch (err) {
        console.error('Submission error:', err);
        alert('Failed to save address: ' + err.message);
      }
    });
  }

  const addressFields = [
    'region_code',
    'province_code',
    'city_code',
    'barangay_code',
    'postal_code'
  ];

  addressFields.forEach(fieldName => {
    const element = document.querySelector(`select[name="${fieldName}"], input[name="${fieldName}"]`);
    if (element) {
      element.addEventListener('change', updateMapFromAddress);
    }
  });

  const addressLineInput = document.querySelector('input[name="address_line"]');
  if (addressLineInput) {
    addressLineInput.addEventListener('input', updateMapFromAddress);
  }
});
