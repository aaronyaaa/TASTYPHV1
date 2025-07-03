<?php
include_once('includes/nav/navbar.php');
include_once('database/db_connect.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TastyPH - Filipino Food Delivery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Hero Section with Auth -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-text">
                <h1>TastyPH</h1>
                <p class="tagline">Savor the Flavors of Filipino Cuisine</p>
                <div class="hero-buttons">
                    <a href="#menu" class="btn-primary">Explore Menu</a>
                    <button class="btn-secondary auth-toggle" data-target="login">Get Started</button>
                </div>
            </div>
            
            <!-- Auth Container in Hero -->
            <div class="hero-auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Login</button>
                    <button class="auth-tab" data-tab="signup">Sign Up</button>
                </div>

                <!-- Login Form -->
                <div class="auth-form active" id="login-form">
                    <h2>Welcome Back!</h2>
                    <p class="form-subtitle">Login to your account</p>
                    <form id="loginForm" action="api/auth/login.php" method="POST">
                        <div class="form-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group password-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember">
                                <span>Remember me</span>
                            </label>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn-submit">Login</button>
                    </form>
                    <div class="form-footer">
                        <p>Don't have an account? <a href="#" class="switch-form" data-target="signup">Sign Up</a></p>
                    </div>
                </div>

                <!-- Signup Form -->
                <div class="auth-form" id="signup-form">
                    <h2>Create Account</h2>
                    <p class="form-subtitle">Join our food-loving community</p>
                    <form id="signupForm" action="api/auth/signup.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="first_name" placeholder="First Name" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="last_name" placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group password-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-group password-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn-submit">Create Account</button>
                    </form>
                    <div class="form-footer">
                        <p>Already have an account? <a href="#" class="switch-form" data-target="login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-pattern"></div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose TastyPH?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Authentic Filipino Food</h3>
                    <p>Experience the rich flavors of traditional Filipino cuisine</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Quick and reliable delivery to your doorstep</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality Assured</h3>
                    <p>Only the best restaurants and food quality</p>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/auth.js"></script>
    <script src="assets/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>
