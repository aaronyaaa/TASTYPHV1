const ctx = document.getElementById('userChart').getContext('2d');
let userChart;

function fetchChartData(userType = 'all', timeRange = 'month') {
  fetch(`/tastyphv1/Admin/php_logic/fetch_user_chart.php?usertype=${userType}&range=${timeRange}`)
    .then(res => res.json())
    .then(data => {
      const labels = data.labels;
      const counts = data.counts;

      if (userChart) userChart.destroy();
      userChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: `Registrations (${userType})`,
            data: counts,
            borderColor: '#7B4397',
            backgroundColor: 'rgba(123, 67, 151, 0.2)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    });
}

// Event listeners
document.getElementById('userTypeSelect').addEventListener('change', () => {
  const type = document.getElementById('userTypeSelect').value;
  const range = document.getElementById('timeRangeSelect').value;
  fetchChartData(type, range);
});

document.getElementById('timeRangeSelect').addEventListener('change', () => {
  const type = document.getElementById('userTypeSelect').value;
  const range = document.getElementById('timeRangeSelect').value;
  fetchChartData(type, range);
});

// Initial load
fetchChartData();
