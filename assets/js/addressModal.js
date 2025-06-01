// addressModal.js

document.addEventListener('DOMContentLoaded', () => {
  const profileForm = document.getElementById('profileForm');
  const formMessage = document.getElementById('formMessage');
  const postalCodeInput = document.querySelector('input[name="postal_code"]');
  const addAddressModal = document.getElementById('addAddressModal');

  // Elements expected in the scope
  const regionSelect = document.querySelector('select[name="region_code"]');
  const provinceSelect = document.querySelector('select[name="province_code"]');
  const citySelect = document.querySelector('select[name="city_code"]');
  const barangaySelect = document.querySelector('select[name="barangay_code"]');
  const addressLineInput = document.querySelector('input[name="address_line"]');

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
    postalCodeInput.addEventListener('input', () => {
      if (typeof window.updateMapFromAddress === 'function') {
        window.updateMapFromAddress();
      }
    });
  }

  if (addAddressModal) {
    addAddressModal.addEventListener('shown.bs.modal', () => {
      if (typeof window.initMap === 'function') {
        window.initMap();
        setTimeout(() => {
          if (window.map) window.map.invalidateSize();
          if (typeof window.updateMapFromAddress === 'function') {
            window.updateMapFromAddress();
          }
        }, 200);
      }
    });
  }
});


