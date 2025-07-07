document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault(); 
    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Logging in...';
    submitBtn.disabled = true;
    
    fetch("api/auth/login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Show success notification
            AuthNotifications.loginSuccess(data.username || 'User');
        } else {
            // Show error notification
            AuthNotifications.loginError(data.message || "Invalid credentials");
        }
    })
    .catch(error => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Show error notification
        NotificationUtils.showError('Login Error', 'Network error. Please check your connection and try again.');
    });
});

document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault(); 
    const formData = new FormData(this);
    
    // Validate passwords match
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        FormNotifications.validationError('confirm_password', 'Passwords do not match.');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating Account...';
    submitBtn.disabled = true;
    
    fetch("api/auth/signup.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Show success notification
            NotificationUtils.showSuccess(
                'Account Created!', 
                'Your account has been created successfully. Welcome to TastyPH!',
                function() {
                    // Close signup modal and redirect
                    const signupModal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
                    if (signupModal) {
                        signupModal.hide();
                    }
                    window.location.href = '/tastyphv1/index.php';
                }
            );
        } else {
            // Show error notification
            NotificationUtils.showError('Signup Error', data.message || "Error during sign up");
        }
    })
    .catch(error => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Show error notification
        NotificationUtils.showError('Signup Error', 'Network error. Please check your connection and try again.');
    });
});

// Add to cart functionality with notifications
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.menu-modern-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.menu-modern-card');
            const productName = card.querySelector('.menu-modern-card-name').textContent;
            const productPrice = card.querySelector('.menu-modern-card-price').textContent;
            
            // Show success toast
            ProductNotifications.addedToCart(productName);
            
            // You can add actual cart functionality here
            console.log(`Added ${productName} (${productPrice}) to cart`);
        });
    });
});
