

<div class="mb-4">
  <label class="form-label fw-bold">Filter by time range:</label>
  <select id="cardTimeRange" class="form-select w-auto d-inline-block">
    <option value="day">Today</option>
    <option value="week">This Week</option>
    <option value="month" selected>This Month</option>
    <option value="year">This Year</option>
  </select>
</div>

<!-- This is where the card HTML will be inserted -->
<div id="statCardsContainer"></div>

<script>
function loadCards(range = 'month') {
  fetch(`/tastyphv1/Admin/php_logic/fetch_user_counts.php?range=${range}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('statCardsContainer').innerHTML = html;
    });
}

document.getElementById('cardTimeRange').addEventListener('change', function () {
  loadCards(this.value);
});

loadCards(); // Load initial cards
</script>


<!-- Donut Chart Card -->
<div class="card mt-5" style="max-width: 420px;">
  <div class="card-body">
    <h5 class="card-title text-center">User Distribution</h5>
    <canvas id="userDoughnutChart" style="max-width: 300px; margin: auto;"></canvas>

    <!-- Breakdown below chart -->
    <div class="d-flex justify-content-around mt-4 text-center">
      <div>
        <span class="dot" style="background-color: rgb(255, 99, 132);"></span>
        <p class="fw-semibold mb-1">Users</p>
        <span class="text-primary">+<?= $counts['users'] ?? 0 ?></span>
      </div>
      <div>
        <span class="dot" style="background-color: rgb(255, 205, 86);"></span>
        <p class="fw-semibold mb-1">Sellers</p>
        <span class="text-warning">+<?= $counts['sellers'] ?? 0 ?></span>
      </div>
      <div>
        <span class="dot" style="background-color: rgb(54, 162, 235);"></span>
        <p class="fw-semibold mb-1">Suppliers</p>
        <span class="text-info">+<?= $counts['suppliers'] ?? 0 ?></span>
      </div>
    </div>
  </div>
</div>
