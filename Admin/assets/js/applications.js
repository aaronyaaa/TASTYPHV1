const panel = document.getElementById('userPanel');
const mainContent = document.getElementById('mainContent');
let currentlyOpenUser = null;

function showMap(id, lat, lng) {
  if (!lat || !lng) return;
  const map = L.map(id).setView([lat, lng], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  L.marker([lat, lng]).addTo(map);
}

function closePanel() {
  panel.classList.remove('open');
  mainContent.classList.remove('shifted');
  currentlyOpenUser = null;
}

function viewPermit(url) {
  const img = document.getElementById('permitViewerImg');
  img.src = url;
  const modal = new bootstrap.Modal(document.getElementById('permitModal'));
  modal.show();
}

function filterByStatus(status) {
  document.querySelectorAll('.user-row').forEach(row => {
    const badge = row.querySelector('.status-badge');
    const currentStatus = badge.textContent.trim().toLowerCase();
    row.style.display = (status === 'all' || status === currentStatus) ? '' : 'none';
  });
  closePanel();
}

function fetchUserDetails(userId, type = 'seller') {
  const url = `/tastyphv1/Admin/api/get_user.php?id=${userId}&type=${type}`;
  fetch(url)
    .then(res => res.json())
    .then(data => {
      document.getElementById('user-details-content').innerHTML = `
        <div class="text-center mb-3">
          <img src="/tastyphv1/${data.profile_pics}" class="profile-img-lg mb-2">
          <h5>${data.first_name} ${data.middle_name} ${data.last_name}</h5>
          <span class="badge bg-${data.status === 'approved' ? 'success' : (data.status === 'rejected' ? 'danger' : 'secondary')}">${data.status ?? 'Pending'}</span>
        </div>
        <p><strong>Email:</strong> ${data.email}</p>
        <p><strong>Contact:</strong> ${data.contact_number}</p>
        <p><strong>Birthdate:</strong> ${data.date_of_birth}</p>
        <p><strong>Address:</strong> ${data.full_address}, ${data.streetname}, ${data.postal_code}</p>
        <div id="userMap"></div>
        <hr>
        <h6>${type === 'supplier' ? 'Supplier' : 'Business'} Info</h6>
        <p><strong>Business Name:</strong> ${data.business_name}</p>
        <p><strong>Description:</strong> ${data.description}</p>
        <p><strong>Store Address:</strong> ${data.store_address}</p>
        <p><strong>Application Date:</strong> ${data.application_date}</p>
        <div id="businessMap"></div>
        <div class="d-flex gap-2 mt-3">
          ${data.business_license ? `<button class='btn btn-outline-primary btn-sm' onclick="viewPermit('/tastyphv1/uploads/licenses/${data.business_license}')">Business License</button>` : ''}
          ${data.business_permit ? `<button class='btn btn-outline-primary btn-sm' onclick="viewPermit('/tastyphv1/uploads/permits/${data.business_permit}')">Business Permit</button>` : ''}
          ${data.health_permit ? `<button class='btn btn-outline-primary btn-sm' onclick="viewPermit('/tastyphv1/uploads/permits/${data.health_permit}')">Health Permit</button>` : ''}
        </div>
      `;
      setTimeout(() => {
        showMap('userMap', data.latitude, data.longitude);
        showMap('businessMap', data.sa_latitude || data.latitude, data.sa_longitude || data.longitude);
      }, 200);
    });
}

function attachUserRowEvents() {
  document.querySelectorAll('.user-row').forEach(row => {
    row.addEventListener('click', (e) => {
      e.preventDefault();
      if (e.target.closest('button')) return;

      const userId = row.dataset.id;
      const type = row.dataset.type || 'seller';

      if (currentlyOpenUser === userId) {
        closePanel();
      } else {
        currentlyOpenUser = userId;
        panel.classList.add('open');
        mainContent.classList.add('shifted');
        fetchUserDetails(userId, type);
      }
    });
  });

 document.querySelectorAll('.approve-btn, .reject-btn').forEach(button => {
  button.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();

    const userId = button.dataset.id;
    const status = button.classList.contains('approve-btn') ? 'approved' : 'rejected';
    
    // Determine type (defaults to 'seller' if not specified)
    const type = button.closest('.user-row')?.dataset.type || 'seller';

    // Choose the correct PHP endpoint
    const endpoint = type === 'supplier'
      ? '/tastyphv1/Admin/php_logic/update_supplier_application_status.php'
      : '/tastyphv1/Admin/php_logic/update_application_status.php';

    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `userId=${userId}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        closePanel();
        location.reload();
      } else {
        alert(data.error || 'Failed to update status');
      }
    });
  });
});

}

function loadApplicationTable(type) {
  fetch(`/tastyphv1/Admin/includes/get_application_table.php?type=${type}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('applicationTable').innerHTML = html;
      attachUserRowEvents();
      closePanel();
      filterByStatus('all');
    });
}

function updateStatCards(type) {
  fetch(`/tastyphv1/Admin/api/get_status_counts.php?type=${type}`)
    .then(res => res.json())
    .then(data => {
      const counts = data.counts;
      const total = data.total;

      document.querySelector('[data-role="total-count"]').textContent = total;
      document.querySelector('[data-role="pending-count"]').textContent = counts.pending;
      document.querySelector('[data-role="approved-count"]').textContent = counts.approved;
      document.querySelector('[data-role="rejected-count"]').textContent = counts.rejected;

      document.querySelectorAll('.card-footer').forEach(footer => {
        footer.textContent = data.type === 'all' ? 'Seller + Supplier' : `${data.type.charAt(0).toUpperCase() + data.type.slice(1)} only`;
      });
    });
}

document.getElementById('applicationType').addEventListener('change', function () {
  const type = this.value;
  loadApplicationTable(type);
  updateStatCards(type);
});

document.addEventListener('DOMContentLoaded', () => {
  const type = document.getElementById('applicationType').value;
  loadApplicationTable(type);
  updateStatCards(type);
});
