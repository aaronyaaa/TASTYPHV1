// Campaign Pricing Management
document.addEventListener('DOMContentLoaded', function() {
    // Load pricing options when modal opens
    const pricingModal = document.getElementById('pricingModal');
    if (pricingModal) {
        pricingModal.addEventListener('show.bs.modal', function() {
            loadPricingOptions();
        });
    }

    // Handle add pricing form submission
    const addPricingForm = document.getElementById('addPricingForm');
    if (addPricingForm) {
        addPricingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addPricingOption();
        });
    }
});

// Load pricing options from API
function loadPricingOptions() {
    fetch('/tastyphv1/Admin/api/get_campaign_pricing.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPricingOptions(data.pricing);
            } else {
                showAlert('Error loading pricing options', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading pricing options', 'danger');
        });
}

// Display pricing options in the list
function displayPricingOptions(pricingOptions) {
    const container = document.getElementById('pricingOptionsList');
    
    if (pricingOptions.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No pricing options found</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr><th>Duration</th><th>Price</th><th>Description</th><th>Actions</th></tr></thead>';
    html += '<tbody>';

    pricingOptions.forEach(option => {
        html += `
            <tr>
                <td><strong>${option.duration_days} days</strong></td>
                <td><span class="badge bg-success">₱${parseFloat(option.price).toFixed(2)}</span></td>
                <td>${option.description || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="editPricing(${option.pricing_id}, ${option.duration_days}, ${option.price}, '${option.description || ''}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletePricing(${option.pricing_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Add new pricing option
function addPricingOption() {
    const form = document.getElementById('addPricingForm');
    const formData = new FormData(form);
    
    const data = {
        duration_days: parseInt(formData.get('duration_days')),
        price: parseFloat(formData.get('price')),
        description: formData.get('description')
    };

    fetch('/tastyphv1/Admin/api/add_campaign_pricing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Pricing option added successfully', 'success');
            form.reset();
            loadPricingOptions();
        } else {
            showAlert(data.error || 'Error adding pricing option', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding pricing option', 'danger');
    });
}

// Edit pricing option
function editPricing(pricingId, durationDays, price, description) {
    document.getElementById('editPricingId').value = pricingId;
    document.getElementById('editDurationDays').value = durationDays;
    document.getElementById('editPrice').value = price;
    document.getElementById('editDescription').value = description;
    
    const editModal = new bootstrap.Modal(document.getElementById('editPricingModal'));
    editModal.show();
}

// Update pricing option
function updatePricing() {
    const pricingId = document.getElementById('editPricingId').value;
    const durationDays = document.getElementById('editDurationDays').value;
    const price = document.getElementById('editPrice').value;
    const description = document.getElementById('editDescription').value;

    const data = {
        pricing_id: parseInt(pricingId),
        duration_days: parseInt(durationDays),
        price: parseFloat(price),
        description: description
    };

    fetch('/tastyphv1/Admin/api/update_campaign_pricing.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Pricing option updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editPricingModal')).hide();
            loadPricingOptions();
        } else {
            showAlert(data.error || 'Error updating pricing option', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating pricing option', 'danger');
    });
}

// Delete pricing option
function deletePricing(pricingId) {
    if (!confirm('Are you sure you want to delete this pricing option?')) {
        return;
    }

    fetch('/tastyphv1/Admin/api/delete_campaign_pricing.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pricing_id: pricingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Pricing option deleted successfully', 'success');
            loadPricingOptions();
        } else {
            showAlert(data.error || 'Error deleting pricing option', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting pricing option', 'danger');
    });
}

// Show alert message
function showAlert(message, type) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at the top of the main content
    const mainContent = document.getElementById('mainContent');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
} 