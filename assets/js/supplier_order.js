document.addEventListener("DOMContentLoaded", () => {
  const initialStatus = document.body.getAttribute("data-initial-status") || "pending";
  let currentlySelectedOrderId = null;

  const mainWrapper = document.querySelector('.main-wrapper');
  const sidebar = document.getElementById("orderDetailsPanel"); // ✅ match HTML
  const sidebarContent = document.getElementById("orderDetailsContent"); // ✅ match HTML

  function loadOrders(status) {
    fetch(`fetch_order_status.php?status=${status}`)
      .then(res => res.text())
      .then(html => {
        document.getElementById("orderTableWrapper").innerHTML = html;
        currentlySelectedOrderId = null;

        // Ensure sidebar is closed after loading
        if (sidebar && mainWrapper) {
          sidebar.classList.remove("active");
          mainWrapper.classList.remove("sidebar-opened");
        }
      });
  }

  loadOrders(initialStatus);

  document.querySelectorAll("#orderTabs .nav-link").forEach(tab => {
    tab.addEventListener("click", function (e) {
      e.preventDefault();
      const status = this.getAttribute("data-status");

      document.querySelectorAll("#orderTabs .nav-link").forEach(t => t.classList.remove("active"));
      this.classList.add("active");

      history.replaceState(null, '', `?status=${status}`);
      loadOrders(status);
    });
  });

  window.viewOrderDetails = function(orderId) {
    if (!sidebar || !sidebarContent) return;

    if (currentlySelectedOrderId === orderId) {
      sidebar.classList.remove("active");
      mainWrapper.classList.remove("sidebar-opened");
      sidebarContent.innerHTML = `<p class="text-muted">Select an order to view details.</p>`;
      currentlySelectedOrderId = null;
      return;
    }

    currentlySelectedOrderId = orderId;

    const supplierLat = document.body.getAttribute("data-supplier-lat");
    const supplierLng = document.body.getAttribute("data-supplier-lng");

    fetch(`fetch_order_details.php?order_id=${orderId}&supplier_lat=${supplierLat}&supplier_lng=${supplierLng}`)
      .then(res => res.text())
      .then(html => {
        sidebarContent.innerHTML = html;
        sidebar.classList.add("active");
        mainWrapper.classList.add("sidebar-opened");

        const mapContainer = document.getElementById("map");
        if (mapContainer) {
          const supLat = parseFloat(mapContainer.getAttribute('data-supplier-lat'));
          const supLng = parseFloat(mapContainer.getAttribute('data-supplier-lng'));
          const usrLat = parseFloat(mapContainer.getAttribute('data-user-lat'));
          const usrLng = parseFloat(mapContainer.getAttribute('data-user-lng'));

          if (!isNaN(supLat) && !isNaN(supLng) && !isNaN(usrLat) && !isNaN(usrLng)) {
            const map = L.map('map').setView([usrLat, usrLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([supLat, supLng]).addTo(map).bindPopup("Supplier");
            L.marker([usrLat, usrLng]).addTo(map).bindPopup("Customer").openPopup();

            L.polyline([[supLat, supLng], [usrLat, usrLng]], { color: 'purple' }).addTo(map);
          } else {
            mapContainer.innerHTML = "<p class='text-danger'>Invalid coordinates for map</p>";
          }
        }
      });
  };
});
document.addEventListener('click', function (e) {
  const chatBtn = e.target.closest('.open-chat-from-sidebar');
  if (!chatBtn) return;

  e.preventDefault();

  const userId = chatBtn.dataset.userId;
  const userName = chatBtn.dataset.userName;

  // Set hidden inputs
  const receiverIdInput = document.getElementById('receiverId');
  const receiverNameInput = document.getElementById('receiverName');
  if (receiverIdInput && receiverNameInput) {
    receiverIdInput.value = userId;
    receiverNameInput.value = userName;
  }

  // Show chat panel
  const chatBox = document.getElementById('chatBox');
  if (chatBox) {
    chatBox.style.display = 'flex';
    chatBox.classList.remove('hide');
    chatBox.classList.add('show');
  }

  // Optional: trigger openChatWithUser if available
  if (typeof openChatWithUser === 'function') {
    openChatWithUser(userId, userName);
  }
});
