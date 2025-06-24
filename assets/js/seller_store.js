window.sellerId = window.sellerId || null;

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

  const categoryBar = document.getElementById("categoryBar");
  const leftBtn = document.getElementById("scrollLeftBtn");
  const rightBtn = document.getElementById("scrollRightBtn");
  const productList = document.getElementById("productList");
  const variantList = document.getElementById("variantList");
  const productSection = document.getElementById("productSection");
  const variantSection = document.getElementById("variantSection");
  const variantTitle = document.getElementById("variantTitle");
  const searchInput = document.getElementById("searchInput");

  if (!productList) {
    console.error("productList element not found.");
    return;
  }

  // Search functionality
  let searchTimeout;
  searchInput?.addEventListener("input", (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const searchTerm = e.target.value.trim();
      fetchProductsByCategory("", searchTerm);
    }, 300);
  });

  // Category click event
  document.querySelectorAll(".category-tab").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".category-tab")
        .forEach((el) => el.classList.remove("active"));
      btn.classList.add("active");

      const categoryId = btn.dataset.id;
      fetchProductsByCategory(categoryId);
    });
  });

  // Scroll buttons
  leftBtn?.addEventListener("click", () => {
    categoryBar.scrollBy({ left: -150, behavior: "smooth" });
  });

  rightBtn?.addEventListener("click", () => {
    categoryBar.scrollBy({ left: 150, behavior: "smooth" });
  });

  // Drag to scroll
  let isDown = false,
    startX,
    scrollLeft;
  categoryBar?.addEventListener("mousedown", (e) => {
    isDown = true;
    startX = e.pageX - categoryBar.offsetLeft;
    scrollLeft = categoryBar.scrollLeft;
    categoryBar.classList.add("dragging");
  });

  categoryBar?.addEventListener("mouseleave", () => {
    isDown = false;
    categoryBar.classList.remove("dragging");
  });

  categoryBar?.addEventListener("mouseup", () => {
    isDown = false;
    categoryBar.classList.remove("dragging");
  });

  categoryBar?.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - categoryBar.offsetLeft;
    const walk = (x - startX) * 2;
    categoryBar.scrollLeft = scrollLeft - walk;
  });

  function fetchProductsByCategory(categoryId = "", search = "") {
    const url = `../backend/seller/fetch_products.php?seller_id=${window.sellerId || ''}&category_id=${categoryId}&search=${encodeURIComponent(search)}`;
    fetch(url)
      .then((res) => res.text())
      .then((html) => {
        if (html.includes('No Products Available')) {
          productList.innerHTML = html;
          variantSection.classList.add("d-none");
          productSection.classList.remove("d-none");
          return;
        }
        productSection.innerHTML = html;
        variantSection.classList.add("d-none");
        productSection.classList.remove("d-none");
        // If we have a category ID, scroll to that category's section
        if (categoryId) {
          requestAnimationFrame(() => {
            const categoryHeadings = document.querySelectorAll('#productSection h4');
            const targetHeading = Array.from(categoryHeadings).find(heading =>
              heading.textContent.trim() === document.querySelector(`.category-tab[data-id=\"${categoryId}\"]`)?.textContent.trim()
            );
            if (targetHeading) {
              const categoryBar = document.getElementById('categoryBarWrapper');
              const categoryBarHeight = categoryBar ? categoryBar.offsetHeight : 0;
              const offset = categoryBarHeight + 20;
              const rect = targetHeading.getBoundingClientRect();
              const scrollPosition = window.scrollY + rect.top - offset;
              window.scrollTo({
                top: scrollPosition,
                behavior: "smooth"
              });
            }
          });
        }
      })
      .catch((err) => {
        console.error("Fetch products failed:", err);
      });
  }

  window.viewVariants = function (productId, name) {
    variantTitle.textContent = `Variants of "${name}"`;
    fetch(
      `../backend/seller/fetch_variants.php?product_id=${productId}`
    )
      .then((res) => res.text())
      .then((html) => {
        variantList.innerHTML = html;
        productSection.classList.add("d-none");
        variantSection.classList.remove("d-none");
      })
      .catch((err) => {
        console.error("Fetch variants failed:", err);
      });
  };

  window.backToProducts = function () {
    productSection.classList.remove("d-none");
    variantSection.classList.add("d-none");
  };

  // Initial load
  fetchProductsByCategory();

  // Manual Sticky Header Logic
  const categoryBarWrapper = document.getElementById('categoryBarWrapper');
  const headerTop = categoryBarWrapper?.getBoundingClientRect().top + window.scrollY;
  let isSticky = false;

  function checkSticky() {
    if (!categoryBarWrapper) return;
    if (window.scrollY >= headerTop && !isSticky) {
      categoryBarWrapper.style.position = 'fixed';
      categoryBarWrapper.style.top = '0';
      categoryBarWrapper.style.width = '100%';
      categoryBarWrapper.style.zIndex = '1000';
      isSticky = true;
    } else if (window.scrollY < headerTop && isSticky) {
      categoryBarWrapper.style.position = 'static';
      categoryBarWrapper.style.top = '';
      categoryBarWrapper.style.width = '';
      categoryBarWrapper.style.zIndex = '';
      isSticky = false;
    }
  }

  window.addEventListener('scroll', checkSticky);
  checkSticky();

  window.addEventListener('resize', () => {
    headerTop = categoryBarWrapper?.getBoundingClientRect().top + window.scrollY;
    checkSticky();
  });
});
