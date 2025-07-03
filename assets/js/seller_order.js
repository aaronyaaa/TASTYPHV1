// assets/js/seller_order.js
// Handles AJAX for seller order management page (like supplier_order.js)

document.addEventListener("DOMContentLoaded", function () {
  const orderTableWrapper = document.getElementById("orderTableWrapper");
  const orderDetailsContent = document.getElementById("orderDetailsContent");
  const orderTabs = document.getElementById("orderTabs");
  const mainWrapper = document.getElementById("mainWrapper");
  const initialStatus =
    document.body.getAttribute("data-initial-status") || "pending";
  let sidebarOpen = false;
  let activeOrderId = null;

  function loadOrders(status) {
    fetch(`fetch_orders.php?status=${encodeURIComponent(status)}`)
      .then((res) => res.text())
      .then((html) => {
        orderTableWrapper.innerHTML = html;
        attachOrderRowEvents();
        // If sidebar is open, clear details
        if (sidebarOpen) {
          orderDetailsContent.innerHTML =
            '<p class="text-muted">Select an order to view details.</p>';
          document
            .querySelectorAll(".order-row")
            .forEach((r) => r.classList.remove("selected"));
          sidebarOpen = false;
          activeOrderId = null;
          mainWrapper.classList.remove("sidebar-opened");
          document.getElementById("orderDetailsPanel").style.transform =
            "translateX(100%)";
        }
      });
  }

  function loadOrderDetails(orderId, row) {
    fetch(`fetch_order_details.php?order_id=${orderId}`)
      .then((res) => res.text())
      .then((html) => {
        orderDetailsContent.innerHTML = html;
        openOrderSidebar();
        document
          .querySelectorAll(".order-row")
          .forEach((r) => r.classList.remove("selected"));
        if (row) row.classList.add("selected");

        // Leaflet Map Logic
        setTimeout(() => {
          const mapDiv = document.getElementById("orderMap");
          if (mapDiv && !mapDiv.dataset.initialized) {
            const lat = parseFloat(mapDiv.dataset.lat);
            const lng = parseFloat(mapDiv.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng)) {
              if (window.orderMapInstance) {
                window.orderMapInstance.remove();
              }
              const map = L.map(mapDiv).setView([lat, lng], 15);
              window.orderMapInstance = map;

              L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                {
                  maxZoom: 19,
                  attribution: "© OpenStreetMap",
                }
              ).addTo(map);

              L.marker([lat, lng]).addTo(map);

              mapDiv.dataset.initialized = "true";

              setTimeout(() => {
                map.invalidateSize();
              }, 100);
            }
          }
        }, 350); // ensure sidebar is visible
      });
  }

  function attachOrderRowEvents() {
    document.querySelectorAll(".order-row").forEach((row) => {
      row.addEventListener("click", function (e) {
        if (e.target.closest("button,form")) return;
        const orderId = this.getAttribute("data-order-id");
        if (sidebarOpen && activeOrderId === orderId) {
          // Toggle: close sidebar if clicking the same row
          closeOrderSidebar();
          return;
        }
        activeOrderId = orderId;
        loadOrderDetails(orderId, this);
      });
    });
  }

  // Tab click
  orderTabs.querySelectorAll(".nav-link").forEach((tab) => {
    tab.addEventListener("click", function (e) {
      e.preventDefault();
      const status = this.getAttribute("data-status");
      loadOrders(status);
      orderTabs
        .querySelectorAll(".nav-link")
        .forEach((t) => t.classList.remove("active"));
      this.classList.add("active");
      closeOrderSidebar();
    });
  });

  // Initial load
  loadOrders(initialStatus);

  // Sidebar open/close logic
  function openOrderSidebar() {
    if (!sidebarOpen) {
      document.getElementById("orderDetailsPanel").style.transform =
        "translateX(0)";
      mainWrapper.classList.add("sidebar-opened");
      sidebarOpen = true;
    }
  }
  function closeOrderSidebar() {
    document.getElementById("orderDetailsPanel").style.transform =
      "translateX(100%)";
    mainWrapper.classList.remove("sidebar-opened");
    sidebarOpen = false;
    activeOrderId = null;
    document
      .querySelectorAll(".order-row")
      .forEach((r) => r.classList.remove("selected"));
    orderDetailsContent.innerHTML =
      '<p class="text-muted">Select an order to view details.</p>';
  }

  // Add close button if not present
  if (
    !document.getElementById("orderSidebarCloseBtn") &&
    document.getElementById("orderDetailsPanel")
  ) {
    const closeBtn = document.createElement("button");
    closeBtn.id = "orderSidebarCloseBtn";
    closeBtn.className =
      "btn btn-light btn-sm position-absolute top-0 end-0 m-2";
    closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
    closeBtn.onclick = closeOrderSidebar;
    document.getElementById("orderDetailsPanel").prepend(closeBtn);
  }

  // Click outside sidebar closes it (desktop only)
  document.addEventListener("mousedown", function (e) {
    const sidebar = document.getElementById("orderDetailsPanel");
    if (
      sidebarOpen &&
      !sidebar.contains(e.target) &&
      !e.target.closest(".order-row")
    ) {
      closeOrderSidebar();
    }
  });

  // Responsive: on resize, adjust sidebar/mainWrapper
  function handleResize() {
    if (window.innerWidth <= 992) {
      // On mobile, overlay sidebar, no shift
      mainWrapper.classList.remove("sidebar-opened");
      document.getElementById("orderDetailsPanel").style.width = "100vw";
    } else {
      document.getElementById("orderDetailsPanel").style.width = "500px";
      if (sidebarOpen) mainWrapper.classList.add("sidebar-opened");
    }
  }
  window.addEventListener("resize", handleResize);
  handleResize();

  closeOrderSidebar();
  console.log("[DEBUG] seller_order.js loaded");
});

window.viewOrderDetails = function (orderId) {
  const orderDetailsContent = document.getElementById("orderDetailsContent");
  fetch(`fetch_order_details.php?order_id=${orderId}`)
    .then((res) => res.text())
    .then((html) => {
      orderDetailsContent.innerHTML = html;
      openOrderSidebar();
      if (document.getElementById("orderSidebarOverlay")) {
        document.getElementById("orderSidebarOverlay").style.display = "block";
      }
      console.log("[DEBUG] viewOrderDetails called for orderId:", orderId);
    });
};
// Hide sidebar and overlay on page load

