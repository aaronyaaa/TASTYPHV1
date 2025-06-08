function updateCartBadge() {
  const badge = document.getElementById("cart-badge");
  const loader = document.getElementById("cart-loader");

  if (loader) loader.style.display = "inline-block";
  if (badge) badge.style.display = "none";

  fetch("../backend/cart_count.php")
    .then(res => res.json())
    .then(data => {
      if (loader) loader.style.display = "none";

      if (badge) {
        if (data.count > 0) {
          badge.textContent = data.count;
          badge.style.display = "inline-block";
        } else {
          badge.style.display = "none";
        }
      }
    })
    .catch(err => {
      console.error("Cart badge update failed:", err);
      if (loader) loader.style.display = "none";
    });
}

// Call on load
document.addEventListener("DOMContentLoaded", updateCartBadge);




function refreshCartSidebar() {
  return fetch("../users/cart/cart_offcanvas.php")
    .then((res) => res.text())
    .then((html) => {
      const container = document.getElementById("offcanvasWithBothOptions");
      const newContent = new DOMParser().parseFromString(html, "text/html")
        .querySelector(".offcanvas-body");
      const existingBody = container?.querySelector(".offcanvas-body");
      if (existingBody && newContent) {
        existingBody.innerHTML = newContent.innerHTML;
      }
    });
}

document.getElementById("add-to-cart-btn")?.addEventListener("click", function () {
  const activeVariant = document.querySelector(".variant-image.active");
  const ingredientId =
    activeVariant?.dataset.ingredientId ||
    document.querySelector("#main-image").dataset.ingredientId;
  const variantId = activeVariant?.dataset.variantId || "";
  const price = parseFloat(
    activeVariant?.dataset.price ||
    document.querySelector("#ingredient-price").dataset.price
  );
  const quantity = parseInt(document.getElementById("quantity-input").value) || 1;

  fetch("../backend/add_to_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      ingredient_id: ingredientId,
      variant_id: variantId,
      unit_price: price,
      quantity: quantity,
    }),
  })
    .then((res) => res.json())
   .then((data) => {
  if (data.success) {
    updateCartBadge(); // ✅ updates badge count in real-time

    refreshCartSidebar().then(() => {
      const cartCanvas = new bootstrap.Offcanvas("#offcanvasWithBothOptions");
      cartCanvas.show();
    });
  } else {
    alert(data.message || "Failed to add to cart.");
  }
})

    .catch((err) => {
      console.error("Add to cart error:", err);
      alert("An error occurred while adding to cart.");
    });
});
