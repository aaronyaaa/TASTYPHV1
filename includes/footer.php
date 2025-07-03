<?php /* Modern Footer for TastyPH */ ?>
<footer class="tastyph-footer footer-main bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container-xl">
        <div class="row gy-4">
            <div class="col-12 col-md-4">
                <h4 class="fw-bold mb-2 tastyph-footer-title" style="color: #ff8800;">TastyPH</h4>
                <p class="mb-0 text-light-50 tastyph-footer-desc">Your trusted marketplace for fresh, local, and artisan food products.</p>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="fw-bold mb-3 tastyph-footer-heading">Categories</h6>
                <ul class="list-unstyled tastyph-footer-list">
                    <li><a href="/users/home.php#products" class="text-light-50 text-decoration-none tastyph-footer-link">Homemade Kakanin</a></li>
                    <li><a href="/users/home.php#ingredients" class="text-light-50 text-decoration-none tastyph-footer-link">Fresh Ingredients</a></li>
                    <li><a href="/users/home.php#snacks" class="text-light-50 text-decoration-none tastyph-footer-link">Healthy Snacks</a></li>
                    <li><a href="/users/home.php#beverages" class="text-light-50 text-decoration-none tastyph-footer-link">Beverages</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="fw-bold mb-3 tastyph-footer-heading">Support</h6>
                <ul class="list-unstyled tastyph-footer-list">
                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">Help Center</a></li>
                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">Contact Us</a></li>
                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">Seller Guide</a></li>
                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">FAQ</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-3 tastyph-footer-heading">Connect</h6>
                <ul class="list-unstyled tastyph-footer-list">
                    <li>
                        <a href="https://web.facebook.com/profile.php?id=61565552490255" target="_blank" class="text-light-50 text-decoration-none tastyph-footer-link">
                            Facebook
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/aaronyaaa_/" target="_blank" class="text-light-50 text-decoration-none tastyph-footer-link">
                            Instagram
                        </a>
                    </li>

                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">Twitter</a></li>
                    <li><a href="#" class="text-light-50 text-decoration-none tastyph-footer-link">Newsletter</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4 tastyph-footer-divider">
        <div class="text-center small text-light-50 tastyph-footer-copyright">&copy; <?= date('Y') ?> TastyPH. All rights reserved.</div>
    </div>
</footer>
<style>
    .tastyph-footer {
        background: #232b32;
        font-family: 'Poppins', Arial, sans-serif;
        letter-spacing: 0.01em;
    }
    .tastyph-footer .tastyph-footer-title {
        color: #ff8800;
        font-size: 2.1rem;
        letter-spacing: 0.02em;
    }
    .tastyph-footer .tastyph-footer-desc {
        color: #bfc9d1;
        font-size: 1.08rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .tastyph-footer .tastyph-footer-heading {
        color: #ff8800;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        letter-spacing: 0.01em;
    }
    .tastyph-footer .tastyph-footer-list {
        padding-left: 0;
        margin-bottom: 0;
    }
    .tastyph-footer .tastyph-footer-link {
        color: #e0e0e0 !important;
        font-size: 1.01rem;
        display: inline-block;
        margin-bottom: 0.4rem;
        transition: color 0.18s, text-shadow 0.18s;
    }
    .tastyph-footer .tastyph-footer-link:hover {
        color: #ff8800 !important;
        text-shadow: 0 2px 8px rgba(255,136,0,0.12);
        text-decoration: underline;
    }
    .tastyph-footer .tastyph-footer-divider {
        border-color: #444 !important;
        opacity: 0.7;
    }
    .tastyph-footer .tastyph-footer-copyright {
        color: #bfc9d1 !important;
        font-size: 0.98rem;
        letter-spacing: 0.01em;
    }
    @media (max-width: 767px) {
        .tastyph-footer .tastyph-footer-title {
            font-size: 1.5rem;
        }
        .tastyph-footer .tastyph-footer-heading {
            font-size: 1rem;
        }
        .tastyph-footer .tastyph-footer-desc {
            font-size: 0.98rem;
        }
    }
</style>