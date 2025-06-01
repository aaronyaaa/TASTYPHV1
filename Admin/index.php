<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Access</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="welcome-bg">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="login-card text-center">
      <h2 id="formTitle">Admin Login</h2>

      <!-- LOGIN FORM -->
      <form id="loginForm" action="login/login.php" method="POST">
        <div class="mb-3 text-start">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100 mb-3">Login</button>
        <p class="form-switch" onclick="toggleForms()">Don't have an account? Register here</p>
      </form>

      <!-- REGISTER FORM -->
      <form id="registerForm" class="d-none" action="register/register.php" method="POST">
        <div class="mb-3 text-start">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100 mb-3">Register</button>
        <p class="form-switch" onclick="toggleForms()">Already have an account? Login here</p>
      </form>
    </div>
  </div>

  <script>
    function toggleForms() {
      const login = document.getElementById('loginForm');
      const register = document.getElementById('registerForm');
      const title = document.getElementById('formTitle');

      login.classList.toggle('d-none');
      register.classList.toggle('d-none');

      title.innerText = login.classList.contains('d-none') ? 'Admin Sign Up' : 'Admin Login';
    }
  </script>
</body>
</html>
