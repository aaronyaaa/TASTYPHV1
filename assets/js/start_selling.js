// === Map Setup Function ===
function setupMap(mapId, latInput, lngInput, addrInput, previewId) {
  const map = L.map(mapId).setView([7.07, 125.6], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  let marker = null;

  const updateLocation = async (lat, lng) => {
    document.getElementById(latInput).value = lat;
    document.getElementById(lngInput).value = lng;

    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
    const data = await res.json();

    const addr = data.display_name || `Lat: ${lat}, Lng: ${lng}`;
    document.getElementById(addrInput).value = addr;

    const preview = document.getElementById(previewId);
    if (preview) {
      preview.style.display = 'block';
      preview.innerHTML = `<b>Address:</b> ${addr}<br><b>Coordinates:</b> ${lat}, ${lng}`;
    }
  };

  map.on('click', function (e) {
    const lat = e.latlng.lat.toFixed(7);
    const lng = e.latlng.lng.toFixed(7);

    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);

    updateLocation(lat, lng);
  });

  return {
    map,
    updateLocation,
    setMarker: (lat, lng) => {
      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng]).addTo(map);
      map.setView([lat, lng], 16);
    }
  };
}

// === Locate Me Button ===
function locateUser(type) {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser.');
    return;
  }

  navigator.geolocation.getCurrentPosition(position => {
    const lat = position.coords.latitude.toFixed(7);
    const lng = position.coords.longitude.toFixed(7);

    if (type === 'seller') {
      seller.setMarker(lat, lng);
      seller.updateLocation(lat, lng);
    } else if (type === 'supplier') {
      supplier.setMarker(lat, lng);
      supplier.updateLocation(lat, lng);
    }
  }, () => {
    alert('Unable to retrieve your location.');
  });
}

// === Image Preview Function ===
function previewImage(input, previewId) {
  const file = input.files[0];
  const preview = document.getElementById(previewId);

  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = '';
    preview.classList.add('d-none');
  }
}

// === Initialize Maps on DOM Ready ===
document.addEventListener('DOMContentLoaded', function () {
  window.seller = setupMap('sellerMap', 'sellerLat', 'sellerLng', 'storeAddress', 'sellerSelectedLocation');
  window.supplier = setupMap('supplierMap', 'supplierLat', 'supplierLng', 'supplierAddress', 'supplierSelectedLocation');
});
