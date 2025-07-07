/**
 * Bootstrap 5 Modal Notification Utilities
 * Easy-to-use functions for showing notifications in your application
 */

// Notification utility functions
const NotificationUtils = {
    /**
     * Show a success modal
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback function when confirmed
     */
    showSuccess: function(title, message, onConfirm = null) {
        if (typeof showSuccessModal === 'function') {
            showSuccessModal(title, message, onConfirm);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show a warning modal
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback function when confirmed
     */
    showWarning: function(title, message, onConfirm = null) {
        if (typeof showWarningModal === 'function') {
            showWarningModal(title, message, onConfirm);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show an error modal
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback function when confirmed
     */
    showError: function(title, message, onConfirm = null) {
        if (typeof showErrorModal === 'function') {
            showErrorModal(title, message, onConfirm);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show an info modal
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback function when confirmed
     */
    showInfo: function(title, message, onConfirm = null) {
        if (typeof showInfoModal === 'function') {
            showInfoModal(title, message, onConfirm);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show a confirmation modal
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback function when confirmed
     */
    showConfirmation: function(title, message, onConfirm = null) {
        if (typeof showConfirmationModal === 'function') {
            showConfirmationModal(title, message, onConfirm);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show a custom modal with full options
     * @param {object} options - Modal options
     */
    showCustom: function(options = {}) {
        if (typeof showNotificationModal === 'function') {
            showNotificationModal(options);
        } else {
            console.warn('Notification modal system not loaded');
        }
    },

    /**
     * Show a success toast
     * @param {string} message - Toast message
     * @param {number} duration - Duration in milliseconds
     */
    showSuccessToast: function(message, duration = 5000) {
        if (typeof showSuccessToast === 'function') {
            showSuccessToast(message, duration);
        } else {
            console.warn('Toast notification system not loaded');
        }
    },

    /**
     * Show a warning toast
     * @param {string} message - Toast message
     * @param {number} duration - Duration in milliseconds
     */
    showWarningToast: function(message, duration = 5000) {
        if (typeof showWarningToast === 'function') {
            showWarningToast(message, duration);
        } else {
            console.warn('Toast notification system not loaded');
        }
    },

    /**
     * Show an error toast
     * @param {string} message - Toast message
     * @param {number} duration - Duration in milliseconds
     */
    showErrorToast: function(message, duration = 5000) {
        if (typeof showErrorToast === 'function') {
            showErrorToast(message, duration);
        } else {
            console.warn('Toast notification system not loaded');
        }
    },

    /**
     * Show an info toast
     * @param {string} message - Toast message
     * @param {number} duration - Duration in milliseconds
     */
    showInfoToast: function(message, duration = 5000) {
        if (typeof showInfoToast === 'function') {
            showInfoToast(message, duration);
        } else {
            console.warn('Toast notification system not loaded');
        }
    }
};

// Authentication-specific notification functions
const AuthNotifications = {
    /**
     * Show login success notification
     * @param {string} username - User's name
     */
    loginSuccess: function(username = 'User') {
        NotificationUtils.showSuccess(
            'Welcome Back!',
            `Hello ${username}! You have successfully logged in to your account. Redirecting to dashboard...`,
            function() {
                // Redirect to dashboard or home page
                window.location.href = 'dashboard.php';
            }
        );
    },

    /**
     * Show login error notification
     * @param {string} errorMessage - Error message
     */
    loginError: function(errorMessage = 'Invalid email or password. Please check your credentials and try again.') {
        NotificationUtils.showError(
            'Login Failed',
            errorMessage,
            function() {
                // Focus on email field or clear form
                const emailField = document.getElementById('loginEmail');
                if (emailField) {
                    emailField.focus();
                }
            }
        );
    },

    /**
     * Show logout confirmation
     */
    logoutConfirmation: function() {
        NotificationUtils.showConfirmation(
            'Confirm Logout',
            'Are you sure you want to logout? Any unsaved changes will be lost.',
            function() {
                // Perform logout
                window.location.href = 'logout.php';
            }
        );
    },

    /**
     * Show password reset notification
     */
    passwordResetSent: function() {
        NotificationUtils.showInfo(
            'Password Reset',
            'A password reset link has been sent to your email address. Please check your inbox and spam folder.',
            function() {
                NotificationUtils.showInfoToast('Check your email for reset instructions.');
            }
        );
    },

    /**
     * Show account deletion confirmation
     */
    deleteAccountConfirmation: function() {
        NotificationUtils.showWarning(
            'Delete Account',
            'This action will permanently delete your account and all associated data. This cannot be undone. Are you absolutely sure?',
            function() {
                // Perform account deletion
                NotificationUtils.showErrorToast('Account deleted permanently.');
            }
        );
    }
};

// Form validation notifications
const FormNotifications = {
    /**
     * Show form validation error
     * @param {string} fieldName - Name of the field with error
     * @param {string} message - Error message
     */
    validationError: function(fieldName, message) {
        NotificationUtils.showError(
            'Validation Error',
            `${fieldName}: ${message}`,
            function() {
                // Focus on the field with error
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.focus();
                }
            }
        );
    },

    /**
     * Show form submission success
     * @param {string} message - Success message
     */
    submissionSuccess: function(message = 'Form submitted successfully!') {
        NotificationUtils.showSuccess(
            'Success!',
            message,
            function() {
                NotificationUtils.showSuccessToast('Data saved successfully!');
            }
        );
    },

    /**
     * Show form submission error
     * @param {string} message - Error message
     */
    submissionError: function(message = 'An error occurred while submitting the form. Please try again.') {
        NotificationUtils.showError(
            'Submission Error',
            message
        );
    }
};

// Order and payment notifications
const OrderNotifications = {
    /**
     * Show order confirmation
     * @param {string} orderId - Order ID
     */
    orderConfirmed: function(orderId) {
        NotificationUtils.showSuccess(
            'Order Confirmed!',
            `Your order #${orderId} has been confirmed and is being prepared. You will receive updates on your order status.`,
            function() {
                NotificationUtils.showSuccessToast('Order confirmed successfully!');
            }
        );
    },

    /**
     * Show payment success
     * @param {string} amount - Payment amount
     */
    paymentSuccess: function(amount) {
        NotificationUtils.showSuccess(
            'Payment Successful!',
            `Your payment of ₱${amount} has been processed successfully. Thank you for your order!`,
            function() {
                NotificationUtils.showSuccessToast('Payment processed successfully!');
            }
        );
    },

    /**
     * Show payment error
     * @param {string} message - Error message
     */
    paymentError: function(message = 'Payment processing failed. Please try again or contact support.') {
        NotificationUtils.showError(
            'Payment Failed',
            message
        );
    },

    /**
     * Show order cancellation confirmation
     * @param {string} orderId - Order ID
     */
    cancelOrderConfirmation: function(orderId) {
        NotificationUtils.showConfirmation(
            'Cancel Order',
            `Are you sure you want to cancel order #${orderId}? This action cannot be undone.`,
            function() {
                // Perform order cancellation
                NotificationUtils.showSuccessToast('Order cancelled successfully!');
            }
        );
    }
};

// Product and inventory notifications
const ProductNotifications = {
    /**
     * Show product added to cart
     * @param {string} productName - Product name
     */
    addedToCart: function(productName) {
        NotificationUtils.showSuccessToast(`${productName} added to cart!`, 3000);
    },

    /**
     * Show product removed from cart
     * @param {string} productName - Product name
     */
    removedFromCart: function(productName) {
        NotificationUtils.showInfoToast(`${productName} removed from cart.`, 3000);
    },

    /**
     * Show out of stock notification
     * @param {string} productName - Product name
     */
    outOfStock: function(productName) {
        NotificationUtils.showWarning(
            'Out of Stock',
            `${productName} is currently out of stock. We will notify you when it becomes available again.`,
            function() {
                NotificationUtils.showInfoToast('You will be notified when the product is back in stock.');
            }
        );
    },

    /**
     * Show inventory update
     * @param {string} productName - Product name
     * @param {number} quantity - New quantity
     */
    inventoryUpdated: function(productName, quantity) {
        NotificationUtils.showSuccess(
            'Inventory Updated',
            `${productName} inventory has been updated. Current stock: ${quantity} units.`,
            function() {
                NotificationUtils.showSuccessToast('Inventory updated successfully!');
            }
        );
    }
};

// Make functions globally available
window.NotificationUtils = NotificationUtils;
window.AuthNotifications = AuthNotifications;
window.FormNotifications = FormNotifications;
window.OrderNotifications = OrderNotifications;
window.ProductNotifications = ProductNotifications;

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notification utilities loaded successfully');
}); 