document.addEventListener('DOMContentLoaded', () => {
  const navLinks = document.querySelectorAll('.mobile-bottom-nav .nav-link');
  const currentPath = window.location.pathname;

  navLinks.forEach(link => {
    // Extract href path only (without domain)
    const linkPath = new URL(link.href).pathname;

    // Compare current path with link href path
    if (linkPath === currentPath) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
});
document.addEventListener("DOMContentLoaded", function() {
  const searchBar = document.querySelector('nav.navbar.fixed-top.d-lg-none');
  if (!searchBar) return;

  // List of partial URL paths where search bar should be hidden on mobile
  const hideOnPaths = [
    '/users/settings.php',
    '/cart/cart.php',
    '/includes/chat.php',
    '/notifications.php',
    // add more if needed
  ];

  const currentPath = window.location.pathname;

  // Check if currentPath includes any of the hideOnPaths
  const shouldHide = hideOnPaths.some(path => currentPath.includes(path));

  if (shouldHide) {
    searchBar.style.display = 'none';
  } else {
    searchBar.style.display = 'flex'; // or '' to reset
  }
});
