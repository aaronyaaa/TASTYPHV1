document.addEventListener("DOMContentLoaded", function () {
  const mainImage = document.getElementById("main-image");
  const ingredientName = document.getElementById("ingredient-name");
  const ingredientPrice = document.getElementById("ingredient-price");
  const fullDescription = document.getElementById("full-description");
  const variantImages = document.querySelectorAll(".variant-image");
  const quantityInput = document.getElementById("quantity-input");
  const increaseBtn = document.getElementById("increase-quantity");
  const decreaseBtn = document.getElementById("decrease-quantity");
  const stockDisplay = document.getElementById("stock-display");
  const quantityValueDisplay = document.getElementById(
    "quantity-value-display"
  );

  let currentStock = 99; // Default fallback

  // Utility: Extract stock from display
  const getMaxStock = () => currentStock;

  // Variant selection
  if (variantImages.length > 0) {
    variantImages[0].classList.add("active");
    currentStock = parseInt(variantImages[0].dataset.stock || "99");
  }

  variantImages.forEach((image) => {
    image.addEventListener("click", function () {
      variantImages.forEach((img) => img.classList.remove("active"));
      this.classList.add("active");

      mainImage.src = this.dataset.image;
      ingredientName.textContent = this.dataset.name;
      ingredientPrice.textContent =
        "₱" +
        parseFloat(this.dataset.price).toLocaleString("en-US", {
          minimumFractionDigits: 2,
        });
      fullDescription.textContent = this.dataset.description;

      currentStock = parseInt(this.dataset.stock || "99");

      if (stockDisplay) {
        stockDisplay.textContent = `${currentStock} units available`;
      }

      if (quantityValueDisplay) {
        quantityValueDisplay.textContent = `${this.dataset.quantity} ${this.dataset.unit}`;
      }
    });
  });

  // Quantity control
  if (increaseBtn && decreaseBtn && quantityInput) {
    increaseBtn.addEventListener("click", () => {
      let current = parseInt(quantityInput.value) || 1;
      if (current < getMaxStock()) {
        quantityInput.value = current + 1;
      }
    });

    decreaseBtn.addEventListener("click", () => {
      let current = parseInt(quantityInput.value) || 1;
      if (current > 1) {
        quantityInput.value = current - 1;
      }
    });
  }

  document
    .getElementById("add-to-cart-btn")
    ?.addEventListener("click", function () {
      const addToCartBtn = this;
      const spinner = document.getElementById("cart-loading-spinner");

      const activeVariant = document.querySelector(".variant-image.active");
      const ingredientId =
        activeVariant?.dataset.ingredientId ||
        document.querySelector("#main-image").dataset.ingredientId;
      const variantId = activeVariant?.dataset.variantId || "";
      const price = parseFloat(
        activeVariant?.dataset.price ||
          document.querySelector("#ingredient-price").dataset.price
      );
      const quantity =
        parseInt(document.getElementById("quantity-input").value) || 1;

      if (quantity > getMaxStock()) {
        alert(`Only ${getMaxStock()} units available.`);
        return;
      }

      spinner.style.display = "inline-block";
      addToCartBtn.disabled = true;

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
            setTimeout(() => {
              window.location.reload(); // Refreshes current page
            }, 300);
          } else {
            alert(data.message || "Failed to add to cart.");
          }
        })

        .catch((err) => {
          console.error("Add to cart error:", err);
          alert("An error occurred while adding to cart.");
        })
        .finally(() => {
          spinner.style.display = "none";
          addToCartBtn.disabled = false;
        });
    });
});
