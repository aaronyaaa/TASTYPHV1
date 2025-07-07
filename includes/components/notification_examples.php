<?php
/**
 * Bootstrap 5 Modal Notification System - Usage Examples
 * This file demonstrates how to use the notification modal system
 */

// Include the notification modal component
include_once 'notification_modal.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Modal Examples</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Bootstrap 5 Modal Notification Examples</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Modal Notifications</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success mb-2 w-100" onclick="showSuccessExample()">
                            <i class="fas fa-check-circle me-2"></i>Success Modal
                        </button>
                        
                        <button class="btn btn-warning mb-2 w-100" onclick="showWarningExample()">
                            <i class="fas fa-exclamation-triangle me-2"></i>Warning Modal
                        </button>
                        
                        <button class="btn btn-danger mb-2 w-100" onclick="showErrorExample()">
                            <i class="fas fa-times-circle me-2"></i>Error Modal
                        </button>
                        
                        <button class="btn btn-info mb-2 w-100" onclick="showInfoExample()">
                            <i class="fas fa-info-circle me-2"></i>Info Modal
                        </button>
                        
                        <button class="btn btn-secondary mb-2 w-100" onclick="showConfirmationExample()">
                            <i class="fas fa-question-circle me-2"></i>Confirmation Modal
                        </button>
                        
                        <button class="btn btn-primary mb-2 w-100" onclick="showCustomExample()">
                            <i class="fas fa-cog me-2"></i>Custom Modal
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Toast Notifications</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success mb-2 w-100" onclick="showSuccessToast()">
                            <i class="fas fa-check-circle me-2"></i>Success Toast
                        </button>
                        
                        <button class="btn btn-warning mb-2 w-100" onclick="showWarningToast()">
                            <i class="fas fa-exclamation-triangle me-2"></i>Warning Toast
                        </button>
                        
                        <button class="btn btn-danger mb-2 w-100" onclick="showErrorToast()">
                            <i class="fas fa-times-circle me-2"></i>Error Toast
                        </button>
                        
                        <button class="btn btn-info mb-2 w-100" onclick="showInfoToast()">
                            <i class="fas fa-info-circle me-2"></i>Info Toast
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Authentication Examples</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success mb-2 me-2" onclick="showLoginSuccess()">
                            Login Success
                        </button>
                        
                        <button class="btn btn-danger mb-2 me-2" onclick="showLoginError()">
                            Login Error
                        </button>
                        
                        <button class="btn btn-warning mb-2 me-2" onclick="showLogoutConfirmation()">
                            Logout Confirmation
                        </button>
                        
                        <button class="btn btn-info mb-2 me-2" onclick="showPasswordReset()">
                            Password Reset
                        </button>
                        
                        <button class="btn btn-secondary mb-2 me-2" onclick="showDeleteConfirmation()">
                            Delete Confirmation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Modal Examples
        function showSuccessExample() {
            showSuccessModal(
                'Success!', 
                'Your action has been completed successfully. All changes have been saved.',
                function() {
                    console.log('Success modal confirmed');
                    showSuccessToast('Action completed successfully!');
                }
            );
        }
        
        function showWarningExample() {
            showWarningModal(
                'Warning!', 
                'This action cannot be undone. Are you sure you want to proceed?',
                function() {
                    console.log('Warning modal confirmed');
                    showWarningToast('Proceeding with action...');
                }
            );
        }
        
        function showErrorExample() {
            showErrorModal(
                'Error!', 
                'An error occurred while processing your request. Please try again.',
                function() {
                    console.log('Error modal confirmed');
                    showErrorToast('Please try again later.');
                }
            );
        }
        
        function showInfoExample() {
            showInfoModal(
                'Information', 
                'This is an informational message. You can use this to provide helpful tips or updates.',
                function() {
                    console.log('Info modal confirmed');
                    showInfoToast('Thank you for your attention!');
                }
            );
        }
        
        function showConfirmationExample() {
            showConfirmationModal(
                'Confirm Action', 
                'Are you sure you want to perform this action? This will affect your account settings.',
                function() {
                    console.log('Confirmation modal confirmed');
                    showSuccessToast('Action confirmed and executed!');
                }
            );
        }
        
        function showCustomExample() {
            showNotificationModal({
                type: 'success',
                title: 'Custom Modal',
                message: 'This is a custom modal with specific options. You can customize buttons, text, and behavior.',
                showCancel: true,
                showConfirm: true,
                confirmText: 'Proceed',
                cancelText: 'Go Back',
                onConfirm: function() {
                    console.log('Custom modal confirmed');
                    showSuccessToast('Custom action completed!');
                },
                onCancel: function() {
                    console.log('Custom modal cancelled');
                    showInfoToast('Action cancelled.');
                }
            });
        }
        
        // Toast Examples
        function showSuccessToast() {
            showSuccessToast('This is a success toast notification!', 3000);
        }
        
        function showWarningToast() {
            showWarningToast('This is a warning toast notification!', 4000);
        }
        
        function showErrorToast() {
            showErrorToast('This is an error toast notification!', 5000);
        }
        
        function showInfoToast() {
            showInfoToast('This is an info toast notification!', 3500);
        }
        
        // Authentication Examples
        function showLoginSuccess() {
            showSuccessModal(
                'Welcome Back!', 
                'You have successfully logged in to your account. Redirecting to dashboard...',
                function() {
                    console.log('Login success confirmed');
                    showSuccessToast('Welcome to TastyPH!');
                }
            );
        }
        
        function showLoginError() {
            showErrorModal(
                'Login Failed', 
                'Invalid email or password. Please check your credentials and try again.',
                function() {
                    console.log('Login error confirmed');
                    showErrorToast('Please check your credentials.');
                }
            );
        }
        
        function showLogoutConfirmation() {
            showConfirmationModal(
                'Confirm Logout', 
                'Are you sure you want to logout? Any unsaved changes will be lost.',
                function() {
                    console.log('Logout confirmed');
                    showSuccessToast('You have been logged out successfully.');
                }
            );
        }
        
        function showPasswordReset() {
            showInfoModal(
                'Password Reset', 
                'A password reset link has been sent to your email address. Please check your inbox.',
                function() {
                    console.log('Password reset info confirmed');
                    showInfoToast('Check your email for reset instructions.');
                }
            );
        }
        
        function showDeleteConfirmation() {
            showWarningModal(
                'Delete Account', 
                'This action will permanently delete your account and all associated data. This cannot be undone. Are you absolutely sure?',
                function() {
                    console.log('Delete confirmed');
                    showErrorToast('Account deleted permanently.');
                }
            );
        }
    </script>
</body>
</html> 