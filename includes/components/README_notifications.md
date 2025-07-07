# Bootstrap 5 Modal Notification System

A comprehensive notification system for your TastyPH application that provides both modal dialogs and toast notifications using Bootstrap 5.

## Features

- ✅ **5 Types of Modals**: Success, Warning, Error, Info, Confirmation
- ✅ **Toast Notifications**: Quick, non-intrusive notifications
- ✅ **Responsive Design**: Works on all device sizes
- ✅ **Customizable**: Full control over styling and behavior
- ✅ **Easy Integration**: Simple function calls
- ✅ **Authentication Ready**: Pre-built auth notification functions
- ✅ **Form Validation**: Built-in form error handling
- ✅ **Order Management**: Order and payment notifications
- ✅ **Product Management**: Cart and inventory notifications

## Quick Start

### 1. Include the Notification System

Add this to your PHP page header:

```php
<?php include_once 'includes/components/notification_modal.php'; ?>
```

### 2. Include the JavaScript Utilities

Add this to your page before the closing `</body>` tag:

```html
<script src="assets/js/notification_utils.js"></script>
```

### 3. Use the Notifications

```javascript
// Show a success modal
NotificationUtils.showSuccess('Success!', 'Your action was completed successfully.');

// Show a warning toast
NotificationUtils.showWarningToast('Please check your input.', 3000);

// Show authentication success
AuthNotifications.loginSuccess('John Doe');
```

## Modal Types

### 1. Success Modal
```javascript
NotificationUtils.showSuccess(
    'Success!', 
    'Your action has been completed successfully.',
    function() {
        // Callback when user clicks confirm
        console.log('Success confirmed');
    }
);
```

### 2. Warning Modal
```javascript
NotificationUtils.showWarning(
    'Warning!', 
    'This action cannot be undone. Are you sure?',
    function() {
        // Callback when user confirms
        console.log('Warning confirmed');
    }
);
```

### 3. Error Modal
```javascript
NotificationUtils.showError(
    'Error!', 
    'An error occurred while processing your request.',
    function() {
        // Callback when user clicks confirm
        console.log('Error acknowledged');
    }
);
```

### 4. Info Modal
```javascript
NotificationUtils.showInfo(
    'Information', 
    'This is an informational message.',
    function() {
        // Callback when user clicks confirm
        console.log('Info acknowledged');
    }
);
```

### 5. Confirmation Modal
```javascript
NotificationUtils.showConfirmation(
    'Confirm Action', 
    'Are you sure you want to perform this action?',
    function() {
        // Callback when user confirms
        console.log('Action confirmed');
    }
);
```

## Toast Notifications

### Success Toast
```javascript
NotificationUtils.showSuccessToast('Action completed successfully!', 3000);
```

### Warning Toast
```javascript
NotificationUtils.showWarningToast('Please check your input.', 4000);
```

### Error Toast
```javascript
NotificationUtils.showErrorToast('An error occurred.', 5000);
```

### Info Toast
```javascript
NotificationUtils.showInfoToast('New message received.', 3500);
```

## Authentication Notifications

### Login Success
```javascript
AuthNotifications.loginSuccess('John Doe');
```

### Login Error
```javascript
AuthNotifications.loginError('Invalid email or password.');
```

### Logout Confirmation
```javascript
AuthNotifications.logoutConfirmation();
```

### Password Reset
```javascript
AuthNotifications.passwordResetSent();
```

### Delete Account
```javascript
AuthNotifications.deleteAccountConfirmation();
```

## Form Notifications

### Validation Error
```javascript
FormNotifications.validationError('email', 'Please enter a valid email address.');
```

### Submission Success
```javascript
FormNotifications.submissionSuccess('Your profile has been updated successfully!');
```

### Submission Error
```javascript
FormNotifications.submissionError('Failed to save changes. Please try again.');
```

## Order Notifications

### Order Confirmation
```javascript
OrderNotifications.orderConfirmed('ORD-12345');
```

### Payment Success
```javascript
OrderNotifications.paymentSuccess('1,250.00');
```

### Payment Error
```javascript
OrderNotifications.paymentError('Payment processing failed. Please try again.');
```

### Cancel Order
```javascript
OrderNotifications.cancelOrderConfirmation('ORD-12345');
```

## Product Notifications

### Added to Cart
```javascript
ProductNotifications.addedToCart('Sapin-Sapin');
```

### Removed from Cart
```javascript
ProductNotifications.removedFromCart('Sapin-Sapin');
```

### Out of Stock
```javascript
ProductNotifications.outOfStock('Premium Puto');
```

### Inventory Updated
```javascript
ProductNotifications.inventoryUpdated('Sapin-Sapin', 25);
```

## Custom Modals

For more control, use the custom modal function:

```javascript
NotificationUtils.showCustom({
    type: 'success',           // success, warning, error, info, confirmation
    title: 'Custom Title',
    message: 'Custom message here',
    showCancel: true,          // Show cancel button
    showConfirm: true,         // Show confirm button
    confirmText: 'Proceed',    // Custom confirm button text
    cancelText: 'Go Back',     // Custom cancel button text
    onConfirm: function() {    // Confirm callback
        console.log('Confirmed');
    },
    onCancel: function() {     // Cancel callback
        console.log('Cancelled');
    }
});
```

## Integration Examples

### 1. Login Form Handler
```javascript
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simulate login process
    fetch('api/auth/login.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            AuthNotifications.loginSuccess(data.username);
        } else {
            AuthNotifications.loginError(data.message);
        }
    })
    .catch(error => {
        NotificationUtils.showError('Login Error', 'Network error. Please try again.');
    });
});
```

### 2. Add to Cart Handler
```javascript
function addToCart(productId, productName) {
    fetch('backend/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            ProductNotifications.addedToCart(productName);
            updateCartCount(data.cart_count);
        } else {
            NotificationUtils.showError('Cart Error', data.message);
        }
    })
    .catch(error => {
        NotificationUtils.showError('Network Error', 'Failed to add item to cart.');
    });
}
```

### 3. Form Validation
```javascript
function validateForm(form) {
    const email = form.querySelector('[name="email"]').value;
    const password = form.querySelector('[name="password"]').value;
    
    if (!email) {
        FormNotifications.validationError('email', 'Email is required.');
        return false;
    }
    
    if (!password) {
        FormNotifications.validationError('password', 'Password is required.');
        return false;
    }
    
    if (password.length < 6) {
        FormNotifications.validationError('password', 'Password must be at least 6 characters.');
        return false;
    }
    
    return true;
}
```

## Styling Customization

The notification system uses CSS classes that you can customize:

### Modal Colors
- `.success-modal` - Green gradient
- `.warning-modal` - Orange gradient  
- `.error-modal` - Red gradient
- `.info-modal` - Blue gradient
- `.confirmation-modal` - Gray gradient

### Toast Colors
- `.bg-success` - Green background
- `.bg-warning` - Orange background
- `.bg-danger` - Red background
- `.bg-info` - Blue background

## Browser Compatibility

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers

## Dependencies

- Bootstrap 5.3.2+
- Font Awesome 6.0+
- Modern browser with ES6 support

## File Structure

```
includes/components/
├── notification_modal.php          # Main notification system
├── notification_examples.php       # Usage examples
└── README_notifications.md        # This documentation

assets/js/
└── notification_utils.js          # JavaScript utilities
```

## Troubleshooting

### Modal not showing?
1. Check if Bootstrap JS is loaded
2. Verify the modal HTML is included in the page
3. Check browser console for JavaScript errors

### Toast not appearing?
1. Ensure toast containers are in the DOM
2. Check z-index conflicts
3. Verify Bootstrap Toast component is available

### Styling issues?
1. Check if Bootstrap CSS is loaded
2. Verify Font Awesome is included
3. Check for CSS conflicts with existing styles

## Support

For issues or questions:
1. Check the browser console for errors
2. Verify all dependencies are loaded
3. Test with the example file first
4. Check the notification system is properly included

## License

This notification system is part of the TastyPH project and follows the same licensing terms. 