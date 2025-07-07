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
    <!-- Modern Hero Section: Split left (text/buttons) and right (image) -->
    <section class="hero hero-modern" id="hero">
        <div class="container hero-modern-flex">
            <div class="hero-modern-left animate-on-scroll">
                <h1 class="hero-modern-title">TASTYPH</h1>
                <p class="hero-modern-subtitle">Modern Filipino Kakanin, Delivered Fresh Daily.<br>Experience the authentic taste of our heritage, crafted with love and tradition.</p>
                <div class="hero-modern-buttons">
                    <a href="#order" class="btn-modern btn-modern-yellow">Order Now</a>
                    <a href="#menu" class="btn-modern btn-modern-purple">See Menu</a>
                </div>
            </div>
            <div class="hero-modern-right animate-on-scroll">
                <div class="hero-modern-img-wrap">
                    <img src="uploads/image/sapin.jpg" alt="Filipino Kakanin" class="hero-modern-img">
                    <span class="hero-modern-circle hero-modern-circle-yellow"></span>
                    <span class="hero-modern-circle hero-modern-circle-purple"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section (below hero) -->
    <section class="about-modern-section" id="about">
        <div class="container about-modern-flex">
            <div class="about-modern-img-wrap animate-on-scroll">
                <img src="uploads/image/kanin.jpg" alt="About TastyPH" class="about-modern-img">
            </div>
            <div class="about-modern-content animate-on-scroll">
                <h2 class="about-modern-title">
                    Where Tradition Meets <span class="about-modern-gradient">Modern Innovation</span>
                </h2>
                <p class="about-modern-desc">
                    Born from a deep love for Filipino heritage, Kakanin Kulture brings you the authentic flavors of traditional kakanin with a modern twist. Each layer tells a story, each bite connects you to generations of Filipino culinary artistry.<br><br>
                    Like the beautiful layers of sapin-sapin, our journey represents the harmony between past and present, creating delicious experiences that honor our roots while embracing contemporary tastes.
                </p>
                <div class="about-modern-stats">
                    <div class="about-modern-stat">
                        <span class="about-modern-stat-num about-modern-gradient">50+</span>
                        <span class="about-modern-stat-label">Traditional Recipes</span>
                    </div>
                    <div class="about-modern-stat">
                        <span class="about-modern-stat-num about-modern-gradient">1000+</span>
                        <span class="about-modern-stat-label">Happy Customers</span>
                    </div>
                    <div class="about-modern-stat">
                        <span class="about-modern-stat-num about-modern-gradient"><i class="fas fa-star"></i> 5</span>
                        <span class="about-modern-stat-label">Average Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section (below about) -->
    <section class="menu-modern-section" id="menu">
        <div class="container">
            <h2 class="menu-modern-title">
                Our Signature <span class="menu-modern-gradient">Kakanin</span> Collection
            </h2>
            <p class="menu-modern-subtitle">
                Each creation is carefully crafted using traditional methods and the finest ingredients, bringing you authentic Filipino flavors in every bite.
            </p>
            <div class="menu-modern-grid">
                <div class="menu-modern-card animate-on-scroll">
                    <img src="uploads/image/1.jpg" alt="Classic Sapin-Sapin" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Classic Sapin-Sapin</span>
                            <span class="menu-modern-card-price">₱150</span>
                        </div>
                        <div class="menu-modern-card-desc">Three-layered delight with ube, langka, and coconut flavors</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="menu-modern-card animate-on-scroll">
                        <img src="uploads/image/2.jpg" alt="Premium Puto" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Premium Puto</span>
                            <span class="menu-modern-card-price">₱120</span>
                        </div>
                        <div class="menu-modern-card-desc">Soft, steamed rice cakes topped with salted egg and cheese</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="menu-modern-card animate-on-scroll">
                    <img src="uploads/image/3.jpg" alt="Traditional Kutsinta" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Traditional Kutsinta</span>
                            <span class="menu-modern-card-price">₱100</span>
                        </div>
                        <div class="menu-modern-card-desc">Brown rice cakes with grated coconut and muscovado</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="menu-modern-card animate-on-scroll">
                    <img src="uploads/image/4.jpg" alt="Royal Suman" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Royal Suman</span>
                            <span class="menu-modern-card-price">₱130</span>
                        </div>
                        <div class="menu-modern-card-desc">Glutinous rice wrapped in banana leaves with latik</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="menu-modern-card animate-on-scroll">
                    <img src="uploads/image/5.jpg" alt="Ube Biko" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Ube Biko</span>
                            <span class="menu-modern-card-price">₱120</span>
                        </div>
                        <div class="menu-modern-card-desc">Purple sticky rice with coconut milk and brown sugar</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="menu-modern-card animate-on-scroll">
                        <img src="uploads/image/6.jpg" alt="Maja Blanca" class="menu-modern-img">
                    <div class="menu-modern-card-body">
                        <div class="menu-modern-card-header">
                            <span class="menu-modern-card-name">Maja Blanca</span>
                            <span class="menu-modern-card-price">₱110</span>
                        </div>
                        <div class="menu-modern-card-desc">Creamy corn pudding with cheese and coconut strips</div>
                        <button class="menu-modern-cart-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="menu-modern-footer">
                <a href="#menu" class="menu-modern-view-btn">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login to TastyPH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="api/auth/login.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="loginEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                                <button class="input-group-text toggle-password" type="button" tabindex="-1" data-target="#loginPassword"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="loginRemember">
                                <label class="form-check-label" for="loginRemember">Remember me</label>
                            </div>
                            <a href="#" class="small">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <span>Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Create Your TastyPH Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" action="api/auth/signup.php" method="POST">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="signupFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="signupFirstName" name="first_name" required>
                            </div>
                            <div class="col">
                                <label for="signupLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="signupLastName" name="last_name" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="signupEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="signupEmail" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="signupPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="signupPassword" name="password" required>
                                <button class="input-group-text toggle-password" type="button" tabindex="-1" data-target="#signupPassword"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="signupConfirmPassword" name="confirm_password" required>
                                <button class="input-group-text toggle-password" type="button" tabindex="-1" data-target="#signupConfirmPassword"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                    <div class="text-center mt-3">
                        <span>Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose TastyPH?</h2>
            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Authentic Filipino Food</h3>
                    <p>Experience the rich flavors of traditional Filipino cuisine</p>
                </div>
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Quick and reliable delivery to your doorstep</p>
                </div>
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality Assured</h3>
                    <p>Only the best restaurants and food quality</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Notification System -->
    <?php include_once 'includes/components/notification_modal.php'; ?>
    
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/index.js"></script>
    <script src="assets/js/notification_utils.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Password show/hide toggle
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('data-target'));
            if (input.type === 'password') {
                input.type = 'text';
                this.querySelector('i').classList.remove('fa-eye');
                this.querySelector('i').classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.querySelector('i').classList.remove('fa-eye-slash');
                this.querySelector('i').classList.add('fa-eye');
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
      const animatedEls = document.querySelectorAll('.animate-on-scroll');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if(entry.isIntersecting) {
            entry.target.classList.add('in-view');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15 });

      animatedEls.forEach(el => observer.observe(el));
    });
    </script>
    <?php include 'includes/footer.php'; ?>
    <style>
      html {
        scroll-behavior: smooth;
      }
    </style>
</body>

</html>