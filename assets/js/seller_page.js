document.addEventListener('DOMContentLoaded', function () {
  const decreaseBtn = document.getElementById('decrease-quantity');
  const increaseBtn = document.getElementById('increase-quantity');
  const quantityInput = document.getElementById('quantity-input');
  const stockDisplay = document.getElementById('stock-display');
  const addToCartBtn = document.getElementById('add-to-cart-btn');
  const spinner = document.getElementById('cart-loading-spinner');

  let maxStock = 99;
  if (stockDisplay) {
    const match = stockDisplay.textContent.match(/(\d+)/);
    if (match) maxStock = parseInt(match[1]);
  }

  if (increaseBtn && decreaseBtn && quantityInput) {
    increaseBtn.addEventListener('click', () => {
      let current = parseInt(quantityInput.value) || 1;
      if (current < maxStock) quantityInput.value = current + 1;
    });
    decreaseBtn.addEventListener('click', () => {
      let current = parseInt(quantityInput.value) || 1;
      if (current > 1) quantityInput.value = current - 1;
    });
  }

  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function () {
      const productId = document.getElementById('main-image').dataset.productId;
      const price = parseFloat(document.getElementById('product-price').dataset.price || 0);
      const quantity = parseInt(quantityInput.value) || 1;
      if (quantity > maxStock) {
        alert(`Only ${maxStock} units available.`);
        return;
      }
      spinner.style.display = 'inline-block';
      addToCartBtn.disabled = true;
      fetch('../backend/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          product_id: productId,
          unit_price: price,
          quantity: quantity
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            setTimeout(() => {
              window.location.reload();
            }, 300);
          } else {
            alert(data.message || 'Failed to add to cart.');
          }
        })
        .catch(err => {
          console.error('Add to cart error:', err);
          alert('An error occurred while adding to cart.');
        })
        .finally(() => {
          spinner.style.display = 'none';
          addToCartBtn.disabled = false;
        });
    });
  }
}); 