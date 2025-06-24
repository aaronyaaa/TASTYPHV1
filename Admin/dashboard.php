<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Sidebar Styles -->
  <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/sidebar.css">
    <link rel="stylesheet" href="/tastyphv1/Admin/assets/css/dashboard_cards.css">

</head>

<body>
<div class="sidebar d-flex flex-column">
  <h4 class="mb-4 text-center">Admin</h4>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="../Admin/dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/applications.php" class="nav-link text-white"><i class="fas fa-users me-2"></i>Users</a></li>
    <li class="nav-item"><a href="/tastyphv1/Admin/includes/campaigns.php" class="nav-link text-white"><i class="fas fa-box-open me-2"></i>Campaigns</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="fas fa-chart-line me-2"></i>Reports</a></li>
  </ul>
  <hr>
  <div class="mt-auto">
    <a href="/tastyphv1/Admin/login/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>
</div>

<div class="main-content">
  <?php include 'includes/dashboard_cards.php'; ?>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('/tastyphv1/Admin/php_logic/fetch_user_doughnut.php')
  .then(res => res.json())
  .then(result => {
    const data = {
      labels: result.labels,
      datasets: [{
        label: 'User Types',
        data: result.counts,
        backgroundColor: [
          'rgb(255, 99, 132)',   // Red
          'rgb(54, 162, 235)',   // Blue
          'rgb(255, 205, 86)'    // Yellow
        ],
        hoverOffset: 10
      }]
    };

    const config = {
      type: 'doughnut',
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    };

    new Chart(document.getElementById('userDoughnutChart'), config);
  });
</script>



</body>
</html>
