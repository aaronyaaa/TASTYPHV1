
document.addEventListener("DOMContentLoaded", function () {
  const notifDropdown = document.getElementById("notifDropdown");

  notifDropdown.addEventListener("show.bs.dropdown", function () {
    // Call backend to mark all notifications as read
    fetch("../backend/mark_all_read.php", {
      method: "POST",
      credentials: "same-origin"
    }).then(response => {
      if (response.ok) {
        // Hide the badge
        const badge = notifDropdown.querySelector(".badge");
        if (badge) {
          badge.style.display = "none";
        }
      }
    });
  });
});
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('searchInput');
  const list = document.getElementById('autocompleteList');

  searchInput.addEventListener('input', function () {
    const query = searchInput.value.trim();
    if (query.length < 2) {
      list.style.display = 'none';
      list.innerHTML = '';
      return;
    }

    fetch(`../backend/search_suggestions.php?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        list.innerHTML = '';
        if (data.length === 0) {
          list.style.display = 'none';
          return;
        }

        data.forEach(item => {
          const li = document.createElement('li');
          li.className = 'list-group-item list-group-item-action';
          li.textContent = `${item.type}: ${item.name}`;
          li.addEventListener('click', () => {
            searchInput.value = item.name;
            list.innerHTML = '';
            list.style.display = 'none';
          });
          list.appendChild(li);
        });

        list.style.display = 'block';
      });
  });

  document.addEventListener('click', function (e) {
    if (!searchInput.contains(e.target) && !list.contains(e.target)) {
      list.style.display = 'none';
    }
  });
});
