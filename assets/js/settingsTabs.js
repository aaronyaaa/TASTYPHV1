
document.addEventListener("DOMContentLoaded", () => {
  const accountCollapse = document.getElementById('accountCollapse');
  const toggleIcon = document.querySelector('.sidebar-header i');
  const myAccountToggle = document.getElementById('myAccountToggle');

  const profileTab = document.getElementById('profile-tab');
  const purchaseTab = document.getElementById('purchase-tab');
  const accountTabs = document.querySelectorAll('#profile-tab, #addresses-tab, #banks-tab, #password-tab');

  // Toggle chevron icon on collapse show/hide
  accountCollapse.addEventListener('show.bs.collapse', () => {
    toggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-down');
  });
  accountCollapse.addEventListener('hide.bs.collapse', () => {
    toggleIcon.classList.replace('fa-chevron-down', 'fa-chevron-right');
  });

  // Toggle collapse & activate Profile tab when clicking "My Account"
  myAccountToggle.addEventListener('click', () => {
    const collapseInstance = bootstrap.Collapse.getOrCreateInstance(accountCollapse);
    if (accountCollapse.classList.contains('show')) {
      collapseInstance.hide();
    } else {
      collapseInstance.show();
      activateTab(profileTab);
    }
  });

  // Helper to activate a tab and deactivate others
  function activateTab(tab) {
    // Deactivate all tabs & content
    accountTabs.forEach(t => {
      t.classList.remove('active');
      t.setAttribute('aria-selected', 'false');
      document.querySelector(t.getAttribute('href')).classList.remove('show', 'active');
    });
    purchaseTab.classList.remove('active');
    purchaseTab.setAttribute('aria-selected', 'false');
    document.querySelector(purchaseTab.getAttribute('href')).classList.remove('show', 'active');

    // Activate clicked tab and corresponding pane
    tab.classList.add('active');
    tab.setAttribute('aria-selected', 'true');
    document.querySelector(tab.getAttribute('href')).classList.add('show', 'active');
  }

  // Click handlers for tabs
  accountTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      e.preventDefault();
      // Show collapse and activate tab
      bootstrap.Collapse.getOrCreateInstance(accountCollapse).show();
      activateTab(tab);
    });
  });

  purchaseTab.addEventListener('click', (e) => {
    e.preventDefault();
    // Hide collapse and activate purchase tab
    bootstrap.Collapse.getOrCreateInstance(accountCollapse).hide();
    activateTab(purchaseTab);
  });

  // Initialize default state on page load
  bootstrap.Collapse.getOrCreateInstance(accountCollapse).show();
  activateTab(profileTab);
});

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('profile-image-input');
  const previewImg = document.querySelector('img[alt="Profile Image"]');

  input.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(evt) {
        previewImg.src = evt.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
});
document.getElementById('profileForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const messageSpan = document.getElementById('formMessage');
  messageSpan.textContent = '';
  messageSpan.className = '';

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: formData,
      credentials: 'include' // include cookies/session
    });

    const result = await response.json();

    if (response.ok && result.success) {
      messageSpan.textContent = result.success;
      messageSpan.className = 'text-success ms-3';

      // Optionally, update the profile image preview:
      if (formData.get('profile_image')?.size > 0) {
        const reader = new FileReader();
        reader.onload = e => {
          document.querySelector('#profile-pane img').src = e.target.result;
        };
        reader.readAsDataURL(formData.get('profile_image'));
      }
    } else {
      messageSpan.textContent = result.error || 'An error occurred while updating.';
      messageSpan.className = 'text-danger ms-3';
    }
  } catch (error) {
    console.error('AJAX error:', error);
    messageSpan.textContent = 'Network error, please try again.';
    messageSpan.className = 'text-danger ms-3';
  }
});


