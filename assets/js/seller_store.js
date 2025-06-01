document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('shopInfoModal');
  const lat = parseFloat(modal?.dataset.lat || 0);
  const lng = parseFloat(modal?.dataset.lng || 0);
  const storeName = modal?.dataset.name || 'Store';
  let mapInitialized = false;

  if (modal) {
    modal.addEventListener('shown.bs.modal', () => {
      if (!mapInitialized) {
        const map = L.map('shopMap').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(storeName).openPopup();
        setTimeout(() => map.invalidateSize(), 200);
        mapInitialized = true;
      }
    });
  }

  // ✅ Fix for null error
  const preorderForm = document.getElementById('preorderForm');
  if (preorderForm) {
    preorderForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const res = await fetch('../backend/preorder_submit.php', {
          method: 'POST',
          body: formData
        });

        const result = await res.json();

        if (result.success) {
          alert(result.message);
          window.location.href = window.location.href;
        } else {
          alert(result.error || 'Failed to submit order.');
        }
      } catch (err) {
        alert('An error occurred. Please try again.');
        console.error(err);
      }
    });
  }
});
