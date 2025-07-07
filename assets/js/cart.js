document.addEventListener('DOMContentLoaded', function () {
  const selectAllCheckbox = document.getElementById('selectAll');
  const itemCheckboxes = document.querySelectorAll('.item-checkbox');
  const decreaseQuantityButtons = document.querySelectorAll('.decrease-quantity');
  const increaseQuantityButtons = document.querySelectorAll('.increase-quantity');
  const quantityInputs = document.querySelectorAll('.quantity-input');
  const orderSubtotalSpan = document.getElementById('order-subtotal');
  const orderTotalSpan = document.getElementById('order-total');
  const deleteSelectedButton = document.querySelector('.btn-danger');
  const paymentDetailsContainer = document.getElementById('payment-details');
  const checkoutBtn = document.getElementById('checkout-btn');
  let selectedPaymentMethod = null;

  function calculateCheckedTotal() {
    let total = 0;
    itemCheckboxes.forEach(checkbox => {
      if (!checkbox.checked) return;
      const cartId = checkbox.dataset.itemId;
      const quantityInput = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
      const unitPriceElement = checkbox.closest('.cart-item').querySelector('p[data-unit-price]');
      if (!unitPriceElement || !quantityInput) return;
      const unitPrice = parseFloat(unitPriceElement.dataset.unitPrice);
      const quantity = parseInt(quantityInput.value) || 1;
      total += unitPrice * quantity;
    });
    return total;
  }

function updateCartTotal() {
  let total = 0;
  itemCheckboxes.forEach(checkbox => {
    const cartId = checkbox.dataset.itemId;
    const quantityInput = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
    const unitPriceElement = checkbox.closest('.cart-item').querySelector('p[data-unit-price]');
    if (!unitPriceElement || !quantityInput) return;

    const unitPrice = parseFloat(unitPriceElement.dataset.unitPrice);
    const quantity = parseInt(quantityInput.value) || 1;
    const itemTotal = unitPrice * quantity;

    if (checkbox.checked) {
      total += itemTotal;
    }

    // Always update subtotal per item (visual)
    const itemSubtotalSpan = document.getElementById(`subtotal-${cartId}`);
    if (itemSubtotalSpan) {
      itemSubtotalSpan.textContent = itemTotal.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  });

  orderSubtotalSpan.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2 });
  orderTotalSpan.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2 });
}


  function syncQuantityToBackend(cartId, quantity) {
    fetch('../backend/update_cart_quantity.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ cart_id: cartId, quantity: quantity })
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          console.warn('❌ Update failed:', data.message);
        }
      })
      .catch(err => console.error('🚫 Update error:', err));
  }

  // Event bindings
  selectAllCheckbox.addEventListener('change', function () {
    itemCheckboxes.forEach(cb => cb.checked = this.checked);
    updateCartTotal();
  });

  itemCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      selectAllCheckbox.checked = Array.from(itemCheckboxes).every(cb => cb.checked);
      updateCartTotal();
    });
  });

  decreaseQuantityButtons.forEach(button => {
    button.addEventListener('click', function () {
      const cartId = this.dataset.cartId;
      const quantityInput = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
      let quantity = parseInt(quantityInput.value);
      if (quantity > 1) {
        quantityInput.value = quantity - 1;
        syncQuantityToBackend(cartId, quantity - 1);
        updateCartTotal();
      }
    });
  });

  increaseQuantityButtons.forEach(button => {
    button.addEventListener('click', function () {
      const cartId = this.dataset.cartId;
      const quantityInput = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
      const stock = parseInt(quantityInput.dataset.stock);
      let quantity = parseInt(quantityInput.value);
      if (quantity < stock) {
        quantityInput.value = quantity + 1;
        syncQuantityToBackend(cartId, quantity + 1);
        updateCartTotal();
      } else {
        alert('Cannot exceed available stock!');
      }
    });
  });

  quantityInputs.forEach(input => {
    input.addEventListener('change', function () {
      const cartId = this.dataset.cartId;
      const stock = parseInt(this.dataset.stock);
      let quantity = parseInt(this.value);
      if (isNaN(quantity) || quantity < 1) quantity = 1;
      else if (quantity > stock) {
        quantity = stock;
        alert('Quantity adjusted to available stock.');
      }
      this.value = quantity;
      syncQuantityToBackend(cartId, quantity);
      updateCartTotal();
    });
  });

  deleteSelectedButton.addEventListener('click', function () {
    const checkedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
    if (checkedItems.length === 0) {
      return alert('No items selected for deletion.');
    }

    if (!confirm('Are you sure you want to delete the selected item(s)?')) return;

    const cartIds = checkedItems.map(cb => cb.dataset.itemId);
    fetch('../backend/delete_cart_items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ cart_ids: cartIds })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          cartIds.forEach(id => {
            document.querySelector(`.item-checkbox[data-item-id="${id}"]`)?.closest('.cart-item')?.remove();
          });
          selectAllCheckbox.checked = false;
          updateCartTotal();
          alert('Selected item(s) deleted.');
        } else {
          alert(data.message || 'Failed to delete item(s).');
        }
      })
      .catch(err => {
        console.error('Delete failed:', err);
        alert('Error deleting item(s).');
      });
  });

  // Payment logic
  document.querySelectorAll('.payment-option').forEach(btn => {
    btn.addEventListener('click', () => {
      selectedPaymentMethod = btn.dataset.method;
      paymentDetailsContainer.innerHTML = '';

      if (selectedPaymentMethod === 'cash') {
        paymentDetailsContainer.innerHTML = `
          <label for="cashAmount">Enter Cash Amount:</label>
          <input type="number" id="cashAmount" class="form-control" min="0" step="0.01" placeholder="e.g. 500">
        `;
      } else if (selectedPaymentMethod === 'gcash') {
        paymentDetailsContainer.innerHTML = `
          <label for="gcashProof">Upload GCash Receipt:</label>
          <input type="file" id="gcashProof" class="form-control" accept="image/*">
        `;
      } else if (selectedPaymentMethod === 'card') {
        paymentDetailsContainer.innerHTML = `
          <label>Card Number</label>
          <input type="text" class="form-control mb-2" id="cardNumber" maxlength="16">
          <label>Expiration</label>
          <input type="month" class="form-control mb-2" id="cardExpiry">
          <label>CVV</label>
          <input type="text" class="form-control mb-2" id="cardCVV" maxlength="4">
        `;
      }
    });
  });
checkoutBtn.addEventListener('click', () => {
  if (!selectedPaymentMethod) return alert("Please select a payment method.");

  const checkedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
  const cartIds = checkedItems.map(cb => cb.dataset.itemId);
  const checkedTotal = calculateCheckedTotal();

  if (cartIds.length === 0) return alert("Please select items to checkout.");
  if (checkedTotal === 0) return alert("Your total is ₱0.00");

  let payload = {
    payment_method: selectedPaymentMethod,
    cart_ids: cartIds
  };

  if (selectedPaymentMethod === 'cash') {
    const cashInput = parseFloat(document.getElementById('cashAmount')?.value);
    if (!cashInput || cashInput < checkedTotal) {
      alert(`Cash must be at least ₱${checkedTotal.toFixed(2)}`);
      return;
    }
    payload.cash_amount = cashInput;

  } else if (selectedPaymentMethod === 'gcash') {
    const proof = document.getElementById('gcashProof')?.files[0];
    if (!proof) {
      alert("Please upload a GCash receipt.");
      return;
    }
    alert("✅ GCash upload not implemented in backend.");
    return;

  } else if (selectedPaymentMethod === 'card') {
    const cardNum = document.getElementById('cardNumber')?.value;
    const cardExp = document.getElementById('cardExpiry')?.value;
    const cardCVV = document.getElementById('cardCVV')?.value;
    if (!cardNum || !cardExp || !cardCVV) {
      alert("Please complete all card details.");
      return;
    }
    payload.card_number = cardNum;
    payload.card_expiry = cardExp;
    payload.card_cvv = cardCVV;
  }

  // POST to backend
  fetch('../backend/process_checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('✅ Checkout complete!');
        window.location.href = 'cart.php';
      } else {
        alert(`❌ ${data.message}`);
      }
    })
    .catch(err => {
      console.error('Checkout error:', err);
      alert('Something went wrong.');
    });
});


  updateCartTotal(); // Initial run
});
