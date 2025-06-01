
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
