<?php
include_once("../database/session.php"); // loads $user array from DB and session

// Now you have access to $user with all info safely sanitized
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TastyPH Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
</head>
<body>
    <?php include '../includes/user_navbar.php'; ?>
    <main class="dashboard-main">
        <section class="dashboard-hero">
            <div class="hero-content">
                <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p class="hero-tagline">Craving for kakanin? Discover and order your favorite Filipino delicacies, delivered fresh to your door.</p>
            </div>
            <div class="hero-image">
                <img src="../assets/images/sapin-sapin-hero.png" alt="Sapin-sapin" />
            </div>
        </section>
        <section class="featured-kakanin">
            <h2>Featured Kakanin</h2>
            <div class="kakanin-carousel">
                <!-- Carousel items (sample static, can be dynamic later) -->
                <div class="kakanin-card">
                    <img src="../assets/images/biko.jpg" alt="Biko">
                    <div class="kakanin-info">
                        <h3>Biko</h3>
                        <p>Sweet sticky rice cake with coconut caramel.</p>
                    </div>
                </div>
                <div class="kakanin-card">
                    <img src="../assets/images/pichi-pichi.jpg" alt="Pichi-Pichi">
                    <div class="kakanin-info">
                        <h3>Pichi-Pichi</h3>
                        <p>Chewy cassava treat rolled in coconut.</p>
                    </div>
                </div>
                <div class="kakanin-card">
                    <img src="../assets/images/sapin-sapin.jpg" alt="Sapin-Sapin">
                    <div class="kakanin-info">
                        <h3>Sapin-Sapin</h3>
                        <p>Colorful layered rice cake, a Filipino classic.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="recent-orders">
            <h2>Recent Orders</h2>
            <div class="orders-list">
                <!-- Sample order card -->
                <div class="order-card">
                    <div class="order-info">
                        <h3>Order #12345</h3>
                        <p>Biko, Sapin-Sapin</p>
                        <span class="order-status delivered">Delivered</span>
                    </div>
                    <div class="order-date">April 25, 2024</div>
                </div>
                <!-- More order cards can be added dynamically -->
            </div>
        </section>
    </main>
    <script src="../assets/js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html> 