document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("shopInfoModal");
  const lat = parseFloat(modal?.dataset.lat || 0);
  const lng = parseFloat(modal?.dataset.lng || 0);
  const storeName = modal?.dataset.name || "Store";
  let mapInitialized = false;

  if (modal) {
    modal.addEventListener("shown.bs.modal", () => {
      if (!mapInitialized) {
        const map = L.map("shopMap").setView([lat, lng], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(storeName).openPopup();
        setTimeout(() => map.invalidateSize(), 200);
        mapInitialized = true;
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const categoryBar = document.getElementById("categoryBar");
  const leftBtn = document.getElementById("scrollLeftBtn");
  const rightBtn = document.getElementById("scrollRightBtn");
  const ingredientList = document.getElementById("ingredientList");
  const variantList = document.getElementById("variantList");
  const ingredientSection = document.getElementById("ingredientSection");
  const variantSection = document.getElementById("variantSection");
  const variantTitle = document.getElementById("variantTitle");
  const searchInput = document.getElementById("searchInput");

  if (!ingredientList) {
    console.error("ingredientList element not found.");
    return;
  }

  // Search functionality
  let searchTimeout;
  searchInput?.addEventListener("input", (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const searchTerm = e.target.value.trim();
      fetchIngredientsByCategory("", searchTerm);
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
      fetchIngredientsByCategory(categoryId);
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

  function fetchIngredientsByCategory(categoryId = "") {
    const url = `../backend/supplier/store/fetch_ingredients.php?category_id=${categoryId}`;
    fetch(url)
      .then((res) => res.text())
      .then((html) => {
        // Check if the response contains the 'No ingredients found' message
        if (html.includes('No ingredients found')) {
          ingredientList.innerHTML = html; // Show the "no results" message
          variantSection.classList.add("d-none");
          ingredientSection.classList.remove("d-none");
          console.log('No ingredients found for this category, not scrolling.');
          return;
        }

        // If we have a category ID, scroll to that category's section
        if (categoryId) {
          requestAnimationFrame(() => {
            // Find the category heading that matches the selected category
            const categoryHeadings = document.querySelectorAll('#ingredientSection h4');
            const targetHeading = Array.from(categoryHeadings).find(heading =>
              heading.textContent.trim() === document.querySelector(`.category-tab[data-id="${categoryId}"]`)?.textContent.trim()
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
        console.error("Fetch ingredients failed:", err);
      });
  }

  window.viewVariants = function (ingredientId, name) {
    variantTitle.textContent = `Variants of "${name}"`;
    fetch(
      `../backend/supplier/store/fetch_variants.php?ingredient_id=${ingredientId}`
    )
      .then((res) => res.text())
      .then((html) => {
        variantList.innerHTML = html;
        ingredientSection.classList.add("d-none");
        variantSection.classList.remove("d-none");
      })
      .catch((err) => {
        console.error("Fetch variants failed:", err);
      });
  };

  window.backToIngredients = function () {
    ingredientSection.classList.remove("d-none");
    variantSection.classList.add("d-none");
  };

  // Initial load
  fetchIngredientsByCategory();

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

function toggleIngredientStatus(ingredientId) {
  fetch('../backend/supplier/toggle_ingredient_settings.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'toggle_active',
      ingredient_id: ingredientId
    })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        console.log('Ingredient updated');
        // Optionally refresh ingredient list
      } else {
        alert(data.error);
      }
    })
    .catch(err => {
      console.error('Error:', err);
    });
}

function addToCart(button) {
  const ingredientId = button.dataset.ingredientId;
  const unitPrice = parseFloat(button.dataset.price);
  const quantity = 1; // default quantity

  button.disabled = true;
  button.innerHTML = '<span class="spinner-border spinner-border-sm text-light"></span>';

  fetch('../backend/add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      ingredient_id: ingredientId,
      variant_id: '',
      unit_price: unitPrice,
      quantity: quantity,
    }),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        alert(data.message || "❌ Failed to add.");
        return; // 🚫 Stop here, don't reload
      }

      // ✅ Only reload if add to cart succeeded
      setTimeout(() => {
        location.reload();
      }, 300);
    })
    .finally(() => {
      button.disabled = false;
      button.innerHTML = '<i class="fas fa-shopping-cart"></i>';
    });
}


