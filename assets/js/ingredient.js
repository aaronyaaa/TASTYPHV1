document.addEventListener('DOMContentLoaded', () => {
  const createForm = document.getElementById('ingredientForm');
  const editForm = document.getElementById('editIngredientForm');
  const imageInput = document.getElementById('image');
  const preview = document.getElementById('imagePreview');
  const sortSelect = document.getElementById('sortIngredients');

  // Live Preview for Create
  if (imageInput && preview) {
    imageInput.addEventListener('change', () => {
      const file = imageInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Submit New Ingredient
  if (createForm) {
    createForm.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(createForm);

      fetch(createForm.action, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('ingredientModal'));
        if (modal) modal.hide();
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        location.reload(); // Reload page
      })
      .catch(err => {
        console.error(err);
        alert('Error while saving the ingredient.');
      });
    });
  }

  // Submit Edit Form
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(editForm);

      fetch(editForm.action, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editIngredientModal'));
          if (modal) modal.hide();
          document.body.classList.remove('modal-open');
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
          location.reload(); // Force page reload after edit
        } else {
          alert(data.error || 'Update failed.');
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error updating ingredient.');
      });
    });
  }

  // Fetch Ingredient List (for sorting/filter)
  window.fetchIngredients = function (sort = 'created_at') {
    fetch(`../backend/supplier/fetch_ingredients.php?sort=${sort}`)
      .then(res => res.text())
      .then(html => {
        const list = document.getElementById('ingredientList');
        if (list) list.innerHTML = html;
      })
      .catch(err => console.error('Failed to fetch ingredients:', err));
  };

  fetchIngredients(); // Initial load

  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      fetchIngredients(sortSelect.value);
    });
  }

  // Assign editIngredient to global scope
  window.editIngredient = function (id) {
    fetch(`../backend/supplier/get_ingredient.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        document.getElementById('edit_ingredient_id').value = data.ingredient_id;
        document.getElementById('edit_ingredient_name').value = data.ingredient_name;
        document.getElementById('edit_description').value = data.description;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_stock').value = data.stock;
        document.getElementById('edit_quantity_value').value = data.quantity_value;
        document.getElementById('edit_unit_type').value = data.unit_type;
        document.getElementById('edit_image_preview').src = data.image_url
          ? '../' + data.image_url
          : '../assets/images/default-category.png';

        const modal = new bootstrap.Modal(document.getElementById('editIngredientModal'));
        modal.show();
      })
      .catch(err => {
        console.error(err);
        alert('Failed to load ingredient data.');
      });
  };

  // Optional: Delete Ingredient
  window.deleteIngredient = function (id) {
    if (confirm('Are you sure you want to delete this ingredient?')) {
      fetch(`../backend/supplier/delete_ingredient.php?id=${id}`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            fetchIngredients();
          } else {
            alert(data.error || 'Delete failed.');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Failed to delete ingredient.');
        });
    }
  };
});


function fetchFilteredIngredients() {
  const search = document.getElementById('ingredientSearch')?.value || '';
  const categoryId = document.getElementById('categoryFilter')?.value || '';

  const url = `../backend/supplier/fetch_ingredients.php?search=${encodeURIComponent(search)}&category_id=${categoryId}`;

  fetch(url)
    .then(res => res.text())
    .then(html => {
      document.getElementById('ingredientList').innerHTML = html;
    })
    .catch(err => console.error('Fetch error:', err));
}

// Trigger on input or select change
document.getElementById('ingredientSearch')?.addEventListener('input', fetchFilteredIngredients);
document.getElementById('categoryFilter')?.addEventListener('change', fetchFilteredIngredients);

// Optionally fetch immediately on load
fetchFilteredIngredients();


document.addEventListener('DOMContentLoaded', function () {
  const variantForm = document.getElementById('variantForm');
  const variantModal = document.getElementById('variantModal');
  const variantIngredientId = document.getElementById('variantIngredientId');

  window.openVariantModal = function (ingredientId) {
    variantIngredientId.value = ingredientId;
    const modal = new bootstrap.Modal(variantModal);
    modal.show();
  };

  variantForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(variantForm);

    fetch(variantForm.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(variantModal)?.hide();
        variantForm.reset();
        // Optionally reload variants
        alert('Variant added!');
      } else {
        alert(data.error || 'Failed to save variant.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('An error occurred.');
    });
  });
});
function openVariantModal(ingredientId) {
  const input = document.getElementById('variantIngredientId');
  const modalEl = document.getElementById('variantModal');
  if (!input || !modalEl) {
    alert('Variant modal not found!');
    return;
  }

  input.value = ingredientId;
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
}


function viewVariants(ingredientId, name) {
  document.getElementById('ingredientSection').classList.add('d-none');
  document.getElementById('variantSection').classList.remove('d-none');
  document.getElementById('variantTitle').innerText = `Variants of "${name}"`;

  fetch(`../backend/supplier/fetch_variants.php?ingredient_id=${ingredientId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('variantList').innerHTML = html;
    })
    .catch(err => {
      console.error(err);
      alert('Failed to load variants.');
    });
}

function backToIngredients() {
  document.getElementById('variantSection').classList.add('d-none');
  document.getElementById('ingredientSection').classList.remove('d-none');
  document.getElementById('variantList').innerHTML = ''; // Optional: clear the variant view
}
