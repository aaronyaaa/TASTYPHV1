document.addEventListener('DOMContentLoaded', function () {
    const authContainer = document.querySelector('.hero-auth-container');
    const authToggle = document.querySelector('.auth-toggle');
    const authTabs = document.querySelectorAll('.auth-tab');
    const switchForms = document.querySelectorAll('.switch-form');
    const authLinks = document.querySelectorAll('.auth-link');
  
    // Show auth container and switch tab (login/signup)
    function showAuthContainer(target) {
      authContainer.classList.remove('hidden');
      authContainer.classList.add('visible');
  
      // Activate tab buttons
      authTabs.forEach(tab => tab.classList.toggle('active', tab.dataset.tab === target));
      // Show corresponding form
      document.querySelectorAll('.auth-form').forEach(form => {
        form.classList.toggle('active', form.id === target + '-form');
      });
    }
  
    // Hide auth container
    function hideAuthContainer() {
      authContainer.classList.remove('visible');
      authContainer.classList.add('hidden');
    }
  
    // Open auth modal on "Get Started" button click
    authToggle.addEventListener('click', e => {
      e.preventDefault();
      showAuthContainer(authToggle.dataset.target);
    });
  
    // Switch tabs (login/signup) on tab button click
    authTabs.forEach(tab => {
      tab.addEventListener('click', e => {
        e.preventDefault();
        showAuthContainer(tab.dataset.tab);
      });
    });
  
    // Switch forms on links inside forms (e.g. "Don't have an account? Sign Up")
    switchForms.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        showAuthContainer(link.dataset.target);
      });
    });
  
    // Navbar auth links open auth modal on click
    authLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        showAuthContainer(link.dataset.target);
      });
    });
  
    // Close auth container when clicking outside of it or toggle button
    document.addEventListener('click', e => {
      if (
        authContainer.classList.contains('visible') &&
        !authContainer.contains(e.target) &&
        !authToggle.contains(e.target) &&
        !Array.from(authLinks).some(link => link.contains(e.target))
      ) {
        hideAuthContainer();
      }
    });
  
    // Password visibility toggle buttons
    document.querySelectorAll('.password-toggle').forEach(button => {
      button.addEventListener('click', function () {
        const input = this.previousElementSibling;
        const icon = this.querySelector('i');
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });
    });
  
  });
  

  