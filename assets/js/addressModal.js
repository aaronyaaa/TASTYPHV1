// addressModal.js

document.addEventListener('DOMContentLoaded', () => {
  const profileForm = document.getElementById('profileForm');
  const formMessage = document.getElementById('formMessage');
  const postalCodeInput = document.querySelector('input[name="postal_code"]');
  const addAddressModal = document.getElementById('addAddressModal');

  // Elements expected in the scope (make sure these are defined globally or passed somehow)
  // Example selectors for selects and inputs used in updateMapFromAddress()
  // Adjust these selectors to match your actual form elements
  const regionSelect = document.querySelector('select[name="region_code"]');
  const provinceSelect = document.querySelector('select[name="province_code"]');
  const citySelect = document.querySelector('select[name="city_code"]');
  const barangaySelect = document.querySelector('select[name="barangay_code"]');
  const addressLineInput = document.querySelector('input[name="address_line"]');

  let map;
  let marker;

  if (profileForm && formMessage) {
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      formMessage.textContent = '';
      formMessage.style.color = '';

      try {
        const formData = new FormData(profileForm);
        const response = await fetch('../backend/settings_logic.php', {
          method: 'POST',
          body: formData,
        });
        const data = await response.json();

        if (data.success) {
          formMessage.style.color = 'green';
          formMessage.textContent = data.message;
        } else {
          formMessage.style.color = 'red';
          formMessage.textContent = data.message;
        }
      } catch (error) {
        formMessage.style.color = 'red';
        formMessage.textContent = 'An error occurred. Please try again.';
      }
    });
  }

  if (postalCodeInput) {
    postalCodeInput.addEventListener('input', updateMapFromAddress);
  }

  if (addAddressModal) {
    addAddressModal.addEventListener('shown.bs.modal', () => {
      initMap();
      setTimeout(() => {
        if (map) map.invalidateSize();
        updateMapFromAddress();
      }, 200);
    });
  }

  function initMap() {
    // You can replace these with dynamic values or defaults
    // If you need PHP variables, pass them to JS through data attributes or inline scripts safely.
    const lat = window.initialLatitude || 12.8797;  // fallback default
    const lng = window.initialLongitude || 121.7740;
    const zoomLevel = 13;

    if (!map) {
      map = L.map('addressMap').setView([lat, lng], zoomLevel);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map);

      marker = L.marker([lat, lng]).addTo(map);

      if (lat && lng) {
        marker.bindPopup("Saved location").openPopup();
      }
    }
  }

  async function updateMapFromAddress() {
    if (!regionSelect || !provinceSelect || !citySelect || !barangaySelect || !addressLineInput || !postalCodeInput || !map || !marker) return;

    const regionText = regionSelect.options[regionSelect.selectedIndex]?.text || '';
    const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
    const cityText = citySelect.options[citySelect.selectedIndex]?.text || '';
    const barangayText = barangaySelect.options[barangaySelect.selectedIndex]?.text || '';
    const street = addressLineInput.value.trim();
    const postal = postalCodeInput.value.trim();

    if (!regionText || !provinceText || !cityText || !barangayText || !street || !postal) return;

    const fullAddress = `${street}, ${barangayText}, ${cityText}, ${provinceText}, ${regionText}, ${postal}, Philippines`;

    try {
      const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}`);
      const data = await response.json();

      if (data && data.length > 0) {
        const lat = parseFloat(data[0].lat);
        const lon = parseFloat(data[0].lon);

        map.setView([lat, lon], 16);
        marker.setLatLng([lat, lon]);
      }
    } catch (error) {
      console.error('Geocoding error:', error);
    }
  }
});


