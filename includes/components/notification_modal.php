<?php
/**
 * Bootstrap 5 Modal Notification System
 * Supports: Success, Warning, Error, Info, Confirmation modals
 * Usage: Include this file and call showNotificationModal() function
 */

// Prevent direct access
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}
?>

<!-- Bootstrap 5 Modal Notification System -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" id="notificationModalHeader">
                <h5 class="modal-title" id="notificationModalLabel">
                    <i class="modal-icon" id="notificationModalIcon"></i>
                    <span id="notificationModalTitle">Notification</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" id="notificationModalBody">
                <div class="notification-content">
                    <p id="notificationModalMessage" class="mb-0"></p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer" id="notificationModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="notificationModalCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="notificationModalConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="successToastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Warning Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="warningToast" class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="warningToastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Error Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-times-circle me-2"></i>
                <span id="errorToastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Info Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="infoToast" class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-info-circle me-2"></i>
                <span id="infoToastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
/* Modal Notification Styles */
#notificationModal .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

#notificationModal .modal-header {
    border-bottom: none;
    padding: 1.5rem 1.5rem 0.5rem 1.5rem;
}

#notificationModal .modal-body {
    padding: 1rem 1.5rem;
}

#notificationModal .modal-footer {
    border-top: none;
    padding: 0.5rem 1.5rem 1.5rem 1.5rem;
}

/* Modal Type Styles */
#notificationModal.success-modal .modal-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 15px 15px 0 0;
}

#notificationModal.warning-modal .modal-header {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    border-radius: 15px 15px 0 0;
}

#notificationModal.error-modal .modal-header {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
    border-radius: 15px 15px 0 0;
}

#notificationModal.info-modal .modal-header {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    color: white;
    border-radius: 15px 15px 0 0;
}

#notificationModal.confirmation-modal .modal-header {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
    border-radius: 15px 15px 0 0;
}

/* Modal Icon Styles */
.modal-icon {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

/* Button Styles */
#notificationModal .btn {
    border-radius: 8px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

#notificationModal .btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

#notificationModal .btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
}

#notificationModal .btn-secondary {
    background: linear-gradient(135deg, #6c757d, #545b62);
    border: none;
}

#notificationModal .btn-secondary:hover {
    background: linear-gradient(135deg, #545b62, #3d4449);
    transform: translateY(-1px);
}

/* Toast Styles */
.toast {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.toast .toast-body {
    font-weight: 500;
}

/* Animation for modals */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Responsive adjustments */
@media (max-width: 576px) {
    #notificationModal .modal-dialog {
        margin: 0.5rem;
    }
    
    #notificationModal .modal-content {
        border-radius: 10px;
    }
    
    #notificationModal .modal-header {
        border-radius: 10px 10px 0 0;
    }
}
</style>

<script>
/**
 * Bootstrap 5 Modal Notification System
 * Provides easy-to-use functions for showing different types of notifications
 */

class NotificationModal {
    constructor() {
        this.modal = document.getElementById('notificationModal');
        this.bootstrapModal = new bootstrap.Modal(this.modal);
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Handle confirmation button clicks
        document.getElementById('notificationModalConfirmBtn').addEventListener('click', () => {
            if (this.onConfirm) {
                this.onConfirm();
            }
            this.hide();
        });

        // Handle modal hidden event
        this.modal.addEventListener('hidden.bs.modal', () => {
            this.resetModal();
        });
    }

    resetModal() {
        // Reset modal to default state
        this.modal.className = 'modal fade';
        document.getElementById('notificationModalTitle').textContent = 'Notification';
        document.getElementById('notificationModalMessage').textContent = '';
        document.getElementById('notificationModalIcon').className = 'modal-icon fas fa-info-circle';
        this.onConfirm = null;
    }

    show(options = {}) {
        const {
            type = 'info',
            title = 'Notification',
            message = '',
            showCancel = true,
            showConfirm = true,
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            onConfirm = null,
            onCancel = null
        } = options;

        // Set modal type and styling
        this.modal.className = `modal fade ${type}-modal`;
        
        // Set icon based on type
        const iconMap = {
            success: 'fas fa-check-circle text-success',
            warning: 'fas fa-exclamation-triangle text-warning',
            error: 'fas fa-times-circle text-danger',
            info: 'fas fa-info-circle text-info',
            confirmation: 'fas fa-question-circle text-secondary'
        };
        
        document.getElementById('notificationModalIcon').className = `modal-icon ${iconMap[type] || iconMap.info}`;
        document.getElementById('notificationModalTitle').textContent = title;
        document.getElementById('notificationModalMessage').textContent = message;
        
        // Show/hide buttons
        const cancelBtn = document.getElementById('notificationModalCancelBtn');
        const confirmBtn = document.getElementById('notificationModalConfirmBtn');
        
        cancelBtn.style.display = showCancel ? 'block' : 'none';
        confirmBtn.style.display = showConfirm ? 'block' : 'none';
        
        if (showCancel) {
            cancelBtn.textContent = cancelText;
            cancelBtn.onclick = onCancel;
        }
        
        if (showConfirm) {
            confirmBtn.textContent = confirmText;
            this.onConfirm = onConfirm;
        }
        
        this.bootstrapModal.show();
    }

    hide() {
        this.bootstrapModal.hide();
    }

    // Convenience methods for different notification types
    success(options) {
        this.show({ ...options, type: 'success' });
    }

    warning(options) {
        this.show({ ...options, type: 'warning' });
    }

    error(options) {
        this.show({ ...options, type: 'error' });
    }

    info(options) {
        this.show({ ...options, type: 'info' });
    }

    confirmation(options) {
        this.show({ ...options, type: 'confirmation' });
    }
}

class ToastNotification {
    constructor() {
        this.toasts = {
            success: new bootstrap.Toast(document.getElementById('successToast')),
            warning: new bootstrap.Toast(document.getElementById('warningToast')),
            error: new bootstrap.Toast(document.getElementById('errorToast')),
            info: new bootstrap.Toast(document.getElementById('infoToast'))
        };
    }

    show(type, message, duration = 5000) {
        const toast = this.toasts[type];
        if (toast) {
            document.getElementById(`${type}ToastMessage`).textContent = message;
            toast.show();
            
            // Auto hide after duration
            setTimeout(() => {
                toast.hide();
            }, duration);
        }
    }

    success(message, duration) {
        this.show('success', message, duration);
    }

    warning(message, duration) {
        this.show('warning', message, duration);
    }

    error(message, duration) {
        this.show('error', message, duration);
    }

    info(message, duration) {
        this.show('info', message, duration);
    }
}

// Initialize notification systems
let notificationModal;
let toastNotification;

document.addEventListener('DOMContentLoaded', function() {
    notificationModal = new NotificationModal();
    toastNotification = new ToastNotification();
});

// Global functions for easy access
function showNotificationModal(options) {
    if (notificationModal) {
        notificationModal.show(options);
    }
}

function showSuccessModal(title, message, onConfirm) {
    if (notificationModal) {
        notificationModal.success({ title, message, onConfirm });
    }
}

function showWarningModal(title, message, onConfirm) {
    if (notificationModal) {
        notificationModal.warning({ title, message, onConfirm });
    }
}

function showErrorModal(title, message, onConfirm) {
    if (notificationModal) {
        notificationModal.error({ title, message, onConfirm });
    }
}

function showInfoModal(title, message, onConfirm) {
    if (notificationModal) {
        notificationModal.info({ title, message, onConfirm });
    }
}

function showConfirmationModal(title, message, onConfirm) {
    if (notificationModal) {
        notificationModal.confirmation({ title, message, onConfirm });
    }
}

function showSuccessToast(message, duration = 5000) {
    if (toastNotification) {
        toastNotification.success(message, duration);
    }
}

function showWarningToast(message, duration = 5000) {
    if (toastNotification) {
        toastNotification.warning(message, duration);
    }
}

function showErrorToast(message, duration = 5000) {
    if (toastNotification) {
        toastNotification.error(message, duration);
    }
}

function showInfoToast(message, duration = 5000) {
    if (toastNotification) {
        toastNotification.info(message, duration);
    }
}
</script> 