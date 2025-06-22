document.addEventListener("DOMContentLoaded", () => {
  const categoryForm = document.getElementById("categoryForm");
  const imageInput = document.getElementById("image");
  const imagePreview = document.getElementById("imagePreview");
  const submitBtn = categoryForm?.querySelector('button[type="submit"]');

  // Image preview
  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        imagePreview.src = e.target.result;
        imagePreview.classList.remove("d-none");
      };
      reader.readAsDataURL(file);
    });
  }

  // Form submission
  if (categoryForm) {
    categoryForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(categoryForm);

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
      }

      fetch(categoryForm.action, {
        method: "POST",
        body: formData,
      })
        .then((res) => res.text())
        .then((text) => {
          try {
            const data = JSON.parse(text);
            if (data.success) {
              // Close modal
              const modalEl = document.getElementById("categoryModal");
              if (modalEl) {
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (!modal) modal = new bootstrap.Modal(modalEl);
                modal.hide();
              }

              // Cleanup
              document.body.classList.remove("modal-open");
              document
                .querySelectorAll(".modal-backdrop")
                .forEach((el) => el.remove());

              categoryForm.reset();
              if (imagePreview) {
                imagePreview.src = "../assets/images/default-category.png";
                imagePreview.classList.add("d-none");
              }

              fetchCategoryList();
            } else {
              alert(data.error || "Failed to add category.");
            }
          } catch (err) {
            console.error("Invalid JSON returned:", text);
            alert("Server returned an error:\n\n" + text);
          }
        })
        .catch((err) => {
          console.error("Request failed:", err);
          alert("An unexpected error occurred.");
        })
        .finally(() => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML =
              '<i class="fa-solid fa-plus me-2"></i> Add Category';
          }
        });
    });
  }

  // Initial fetch
  fetchCategoryList();
});

// Refresh category list
function fetchCategoryList() {
  fetch("../backend/supplier/fetch_categories.php")
    .then((res) => res.text())
    .then((html) => {
      const list = document.getElementById("categoryList");
      if (list) list.innerHTML = html;
    })
    .catch((err) => {
      console.error("Failed to fetch updated categories:", err);
    });
}
