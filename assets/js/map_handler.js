let map, marker;
let selectedLocation = null;

function initMap() {
  if (map) {
    map.invalidateSize();
    return;
  }

  const defaultLat = 12.8797;
  const defaultLng = 121.7740;
  map = L.map('addressMap').setView([defaultLat, defaultLng], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

  marker.on('dragend', e => {
    updateLocationFromMarker(e.target.getLatLng());
  });

  map.on('click', e => {
    marker.setLatLng(e.latlng);
    updateLocationFromMarker(e.latlng);
  });
}

function updateLocationFromMarker(pos) {
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`)
    .then(res => res.json())
    .then(data => {
      selectedLocation = {
        lat: pos.lat,
        lng: pos.lng,
        address: data.display_name,
        details: data.address
      };

      document.getElementById('latitude').value = pos.lat;
      document.getElementById('longitude').value = pos.lng;
      document.getElementById('address_line').value = data.address.road || '';
      document.getElementById('postal_code').value = data.address.postcode || '';
      document.getElementById('full_address').value = data.display_name || '';

      document.getElementById('selectedLocation').style.display = 'block';
      document.getElementById('selectedLocation').innerHTML = `
        <b>Address:</b> ${data.display_name}<br>
        <b>Coordinates:</b> ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}
      `;
    })
    .catch(() => alert('Failed to fetch address'));
}

async function fillPSGCCodes(address) {
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
  $('#addAddressModal').on('shown.bs.modal', () => {
    initMap();
  });

  document.getElementById('locateMeBtn').addEventListener('click', () => {
    if (!navigator.geolocation) {
      alert('Geolocation not supported');
      return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
      const coords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      map.setView(coords, 16);
      marker.setLatLng(coords);
      updateLocationFromMarker(coords);
    }, () => alert('Could not get your location'));
  });

  document.getElementById('addAddressForm').addEventListener('submit', async e => {
    e.preventDefault();
    if (!selectedLocation) {
      alert('Please select a location on the map.');
      return;
    }

    const formData = new FormData(e.target);

    try {
      const res = await fetch('../backend/update_address.php', {
        method: 'POST',
        body: formData
      });

      const json = await res.json();

      if (json.success) {
        alert(json.message);
        $('#addAddressModal').modal('hide');
        window.location.reload();
      } else {
        alert('Error: ' + (json.message || 'Unknown error'));
      }
    } catch (err) {
      alert('Failed to save address: ' + err.message);
    }
  });
});
