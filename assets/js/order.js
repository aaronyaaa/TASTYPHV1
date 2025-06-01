const sidebar = document.getElementById('sidebar');
const mainWrapper = document.getElementById('mainWrapper');
const sidebarContent = document.getElementById('sidebarContent');
const mainContainer = document.getElementById('mainContainer');
let sidebarOpen = false;

function toggleSidebar(force = null) {
  const shouldOpen = force !== null ? force : !sidebarOpen;
  sidebar.classList.toggle('open', shouldOpen);
  mainWrapper?.classList.toggle('shifted', shouldOpen);
  sidebarOpen = shouldOpen;
}

function renderMap(lat, lng) {
  const mapContainer = document.getElementById('map');
  if (!mapContainer || mapContainer.dataset.initialized === 'true') return;

  mapContainer.dataset.initialized = 'true';
  const map = L.map(mapContainer).setView([lat, lng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  L.marker([lat, lng]).addTo(map);
}

function generateSidebarContent(data) {
  const statusClass = {
    approved: 'success',
    declined: 'danger',
    delivered: 'info',
    pending: 'secondary'
  }[data.status] || 'secondary';

  let actions = '';
  if (data.status === 'pending') {
    actions = `
      <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm" onclick="handleStatusUpdate(${data.pre_order_id}, 'approved')">Approve</button>
        <button class="btn btn-danger btn-sm" onclick="handleStatusUpdate(${data.pre_order_id}, 'declined')">Decline</button>
      </div>`;
  } else if (data.status === 'approved') {
    actions = `
      <button class="btn btn-warning btn-sm" onclick="handleStatusUpdate(${data.pre_order_id}, 'delivered')">Mark as Delivered</button>`;
  }

  const chatBtn = `
    <button 
      class="btn btn-outline-primary btn-sm mt-2 open-chat-from-sidebar" 
      data-user-id="${data.user_id}" 
      data-user-name="${data.first_name} ${data.last_name}">
      <i class="fa-solid fa-message me-1"></i> Chat
    </button>
  `;

  return `
    <div class="text-center">
      <img src="../${data.profile_pics || 'assets/images/default-profile.png'}"
           class="rounded-circle shadow mb-3"
           style="width: 100px; height: 100px; object-fit: cover;">
      <h5 class="fw-semibold text-dark mb-0">${data.first_name} ${data.last_name}</h5>
      <p class="text-muted small mb-2">${data.email}</p>
      <span class="badge bg-${statusClass} mb-3">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>
      <hr class="border-light my-3">
      <div class="text-start small px-1">
        <p class="mb-1"><strong>📞 Phone:</strong> ${data.contact_number}</p>
        <p class="mb-1"><strong>📍 Address:</strong> ${data.user_address}</p>
        <p class="mb-1"><strong>🛒 Product:</strong> ${data.product_name}</p>
        <p class="mb-1"><strong>🔢 Quantity:</strong> ${data.quantity} ${data.unit || ''}</p>
        <p class="mb-1"><strong>📅 Preferred Date:</strong> ${data.preferred_date}</p>
        <p class="mb-1"><strong>⏰ Preferred Time:</strong> ${data.preferred_time}</p>
        <p class="mb-2"><strong>📝 Notes:</strong>
          <div style="max-height: 120px; overflow-y: auto; white-space: pre-wrap; background-color: #f8f9fa; border-radius: 6px; padding: 6px;">
            ${data.additional_notes || 'None'}
          </div>
        </p>
        ${data.status === 'declined' ? `
        <p class="mb-2"><strong>❌ Decline Reason:</strong>
          <div style="max-height: 100px; overflow-y: auto; background-color: #ffe5e5; border-left: 4px solid #dc3545; border-radius: 6px; padding: 6px;">
            ${data.decline_reason || 'No reason provided.'}
          </div>
        </p>` : ''}
        <label class="fw-bold mb-1 d-block">🗺️ Location Map:</label>
        <div id="map" class="rounded shadow-sm border" style="height: 200px;"></div>
        <hr class="border-light my-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          ${actions}
          ${chatBtn}
        </div>
      </div>
    </div>
  `;
}

function bindOrderRowClicks() {
  document.querySelectorAll('.clickable-row').forEach(row => {
    row.removeEventListener('click', row.__clickHandler);

    const clickHandler = () => {
      const data = JSON.parse(row.dataset.order);
      const currentId = sidebarContent.dataset.active;
      const clickedId = String(data.pre_order_id);

      if (sidebarOpen && currentId === clickedId) {
        toggleSidebar(false);
        sidebarContent.dataset.active = '';
        return;
      }

      sidebarContent.innerHTML = generateSidebarContent(data);
      sidebarContent.dataset.active = clickedId;
      toggleSidebar(true);
      setTimeout(() => renderMap(data.latitude, data.longitude), 250);
    };

    row.__clickHandler = clickHandler;
    row.addEventListener('click', clickHandler);
  });
}

document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
  tab.addEventListener('shown.bs.tab', bindOrderRowClicks);
});

bindOrderRowClicks();

mainContainer.addEventListener('click', (e) => {
  if (sidebarOpen && !e.target.closest('.clickable-row') && !e.target.closest('#sidebar')) {
    toggleSidebar(false);
    sidebarContent.dataset.active = '';
  }
});

function handleStatusUpdate(orderId, newStatus) {
  if (newStatus === 'declined') {
    document.getElementById('declineOrderId').value = orderId;
    const modal = new bootstrap.Modal(document.getElementById('declineModal'));
    modal.show();
    return;
  }

  if (!confirm(`Are you sure you want to mark this as ${newStatus}?`)) return;
  updateOrderStatus(orderId, newStatus);
}

function updateOrderStatus(orderId, newStatus, declineReason = '') {
  fetch('../backend/seller/update_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ pre_order_id: orderId, status: newStatus, reason: declineReason })
  })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        location.reload();
      } else {
        alert(response.error || 'Something went wrong.');
      }
    });
}

document.getElementById('declineForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const orderId = document.getElementById('declineOrderId').value;
  const reason = document.getElementById('declineReason').value.trim();
  if (!reason) return alert('Please enter a reason.');
  updateOrderStatus(orderId, 'declined', reason);
});

// ✅ Enable chat open from sidebar
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.open-chat-from-sidebar');
  if (btn) {
    e.preventDefault();
    const userId = btn.dataset.userId;
    const userName = btn.dataset.userName;

    if (typeof openChatWithUser === 'function') {
      openChatWithUser(userId, userName);

      const chatBox = document.getElementById('chatBox');
      if (chatBox && chatBox.style.display === 'none') {
        chatBox.style.display = 'flex';
        chatBox.classList.remove('hide');
        chatBox.classList.add('show');
      }
    } else {
      console.warn('openChatWithUser not defined.');
    }
  }
});
