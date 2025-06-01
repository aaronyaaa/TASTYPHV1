let map;
let marker;

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("editStoreModal");

  modal.addEventListener("shown.bs.modal", () => {
    const latInput = document.getElementById("latitude");
    const lngInput = document.getElementById("longitude");
    const storeAddressInput = document.getElementById("storeAddress");
    const fullAddressInput = document.getElementById("fullAddress");

    const lat = parseFloat(latInput.value) || -6.1754;
    const lng = parseFloat(lngInput.value) || 106.8272;

    // Geocode address to lat/lng
    async function geocodeAddress(address) {
      const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;
      try {
        const response = await fetch(url);
        const results = await response.json();
        if (results.length > 0) {
          return {
            lat: parseFloat(results[0].lat),
            lng: parseFloat(results[0].lon),
          };
        }
      } catch (err) {
        console.error("Geocoding error:", err);
      }
      return null;
    }

    // Reverse geocode lat/lng to full and short address
    async function reverseGeocode(lat, lng) {
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
      try {
        const response = await fetch(url);
        const result = await response.json();
        if (result && result.address) {
          const address = result.address;
          const full = result.display_name;
          const street = address.road || address.pedestrian || address.footway || '';
          const city = address.city || address.town || address.village || address.hamlet || '';
          const short = [street, city].filter(Boolean).join(', ');
          return { full, short };
        }
      } catch (err) {
        console.error("Reverse geocoding error:", err);
      }
      return { full: '', short: '' };
    }

    // Debounce helper
    function debounce(func, delay) {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
      };
    }

    // Update marker on map
    function updateMarker(lat, lng) {
      const parsedLat = parseFloat(lat);
      const parsedLng = parseFloat(lng);
      if (!isNaN(parsedLat) && !isNaN(parsedLng)) {
        marker.setLatLng([parsedLat, parsedLng]);
        map.setView([parsedLat, parsedLng]);
      }
    }

    // Handle address input
    const handleAddressChange = debounce(async () => {
      const address = fullAddressInput.value || storeAddressInput.value;
      if (!address.trim()) return;

      const coords = await geocodeAddress(address);
      if (coords) {
        latInput.value = coords.lat.toFixed(7);
        lngInput.value = coords.lng.toFixed(7);
        updateMarker(coords.lat, coords.lng);
      }
    }, 800);

    storeAddressInput?.addEventListener("input", handleAddressChange);
    fullAddressInput?.addEventListener("input", handleAddressChange);

    if (!map) {
      map = L.map("map").setView([lat, lng], 13);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);

      marker = L.marker([lat, lng], { draggable: true }).addTo(map);

      marker.on("dragend", async (e) => {
        const pos = e.target.getLatLng();
        latInput.value = pos.lat.toFixed(7);
        lngInput.value = pos.lng.toFixed(7);

        const { full, short } = await reverseGeocode(pos.lat, pos.lng);
        if (full) {
          fullAddressInput.value = full;
          storeAddressInput.value = full; // Keep both the same
        }
      });

      latInput.addEventListener("change", async () => {
        updateMarker(latInput.value, lngInput.value);
        const { full } = await reverseGeocode(latInput.value, lngInput.value);
        if (full) {
          fullAddressInput.value = full;
          storeAddressInput.value = full;
        }
      });

      lngInput.addEventListener("change", async () => {
        updateMarker(latInput.value, lngInput.value);
        const { full } = await reverseGeocode(latInput.value, lngInput.value);
        if (full) {
          fullAddressInput.value = full;
          storeAddressInput.value = full;
        }
      });
    }

    setTimeout(() => map.invalidateSize(), 200);
  });

  // Form submit
  document.getElementById("storeEditForm")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const res = await fetch("../backend/seller/update_store_info.php", {
        method: "POST",
        body: formData,
      });
      const result = await res.json();

      if (result.success) {
        alert("Store info updated!");
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById("editStoreModal"));
        modalInstance.hide();
        location.reload();
      } else {
        alert(result.error || "Failed to update store.");
      }
    } catch (err) {
      console.error(err);
      alert("An error occurred while saving.");
    }
  });
});
