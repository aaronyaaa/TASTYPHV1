// Campaign Management JavaScript
// Handles countdown timers, form submissions, pricing selection, and real-time updates

// Global variables
let selectedPricing = null;
let bannerImageFile = null;
let receiptImageFile = null;
let countdownIntervals = [];
let trackerChart = null;
let trackerInterval = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCountdowns();
    initializeEventListeners();
});

// Initialize all event listeners
function initializeEventListeners() {
    // Campaign modal events
    const campaignModal = document.getElementById('campaignModal');
    if (campaignModal) {
        campaignModal.addEventListener('show.bs.modal', function() {
            loadPricingOptions();
            resetForm();
        });
    }

    // Form submission
    const campaignForm = document.getElementById('campaignForm');
    if (campaignForm) {
        campaignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCampaign();
        });
    }

    // Start date change
    const startDateInput = document.getElementById('startDate');
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showAlert('Start date cannot be in the past', 'warning');
                this.value = today.toISOString().split('T')[0];
            }
            
            if (selectedPricing) {
                updateEndDate();
            }
        });
    }

    // Payment method change
    const paymentMethodSelect = document.getElementById('paymentMethod');
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            const method = this.value;
            document.querySelectorAll('.payment-section').forEach(section => {
                section.style.display = 'none';
            });

            if (method === 'cash') {
                document.getElementById('cashPayment').style.display = 'block';
            } else if (method === 'gcash') {
                document.getElementById('gcashPayment').style.display = 'block';
            }
        });
    }

    // Cash amount input
    const cashAmountInput = document.getElementById('cashAmount');
    if (cashAmountInput) {
        cashAmountInput.addEventListener('input', function() {
            if (selectedPricing) {
                const cashAmount = parseFloat(this.value) || 0;
                const requiredAmount = selectedPricing.price;
                const change = cashAmount - requiredAmount;

                const changeDiv = document.getElementById('cashChange');
                const changeAmount = document.getElementById('changeAmount');

                if (cashAmount >= requiredAmount) {
                    changeDiv.style.display = 'block';
                    changeAmount.textContent = change.toFixed(2);
                    changeDiv.className = 'alert alert-success';
                } else {
                    changeDiv.style.display = 'none';
                }
            }
        });
    }

    // Banner image preview
    const bannerImageInput = document.getElementById('bannerImage');
    if (bannerImageInput) {
        bannerImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                bannerImageFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // GCash receipt preview
    const gcashReceiptInput = document.getElementById('gcashReceipt');
    if (gcashReceiptInput) {
        gcashReceiptInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                receiptImageFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('receiptImg').src = e.target.result;
                    document.getElementById('receiptPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Initialize countdown timers
function initializeCountdowns() {
    // Clear existing intervals
    countdownIntervals.forEach(interval => clearInterval(interval));
    countdownIntervals = [];

    // Get all campaign cards
    const campaignCards = document.querySelectorAll('.campaign-card');
    
    campaignCards.forEach(card => {
        const status = card.dataset.status;
        const startDate = new Date(card.dataset.startDate);
        const endDate = new Date(card.dataset.endDate);
        const countdownElement = card.querySelector('.countdown-text');
        
        if (status === 'approved' && countdownElement) {
            // Update countdown every second
            const interval = setInterval(() => {
                updateCountdown(card, startDate, endDate, countdownElement);
            }, 1000);
            
            countdownIntervals.push(interval);
            
            // Initial update
            updateCountdown(card, startDate, endDate, countdownElement);
        }
    });
}

// Update countdown for a specific campaign card
function updateCountdown(card, startDate, endDate, countdownElement) {
    const now = new Date();
    const badge = card.querySelector('.time-status .badge');
    const icon = card.querySelector('.time-status .fas');
    
    let timeStatus = '';
    let badgeClass = '';
    let iconClass = '';
    
    if (now >= startDate && now <= endDate) {
        // Campaign is running
        const timeRemaining = endDate - now;
        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
        
        if (days > 0) {
            timeStatus = `${days} day${days > 1 ? 's' : ''} left`;
        } else if (hours > 0) {
            timeStatus = `${hours} hour${hours > 1 ? 's' : ''} left`;
        } else if (minutes > 0) {
            timeStatus = `${minutes} minute${minutes > 1 ? 's' : ''} left`;
        } else {
            timeStatus = `${seconds} second${seconds > 1 ? 's' : ''} left`;
        }
        
        badgeClass = 'bg-success';
        iconClass = 'fa-play';
        
    } else if (now < startDate) {
        // Campaign hasn't started yet
        const timeUntilStart = startDate - now;
        const days = Math.floor(timeUntilStart / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeUntilStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeUntilStart % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeUntilStart % (1000 * 60)) / 1000);
        
        if (days > 0) {
            timeStatus = `Starts in ${days} day${days > 1 ? 's' : ''}`;
        } else if (hours > 0) {
            timeStatus = `Starts in ${hours} hour${hours > 1 ? 's' : ''}`;
        } else if (minutes > 0) {
            timeStatus = `Starts in ${minutes} minute${minutes > 1 ? 's' : ''}`;
        } else {
            timeStatus = `Starts in ${seconds} second${seconds > 1 ? 's' : ''}`;
        }
        
        badgeClass = 'bg-warning';
        iconClass = 'fa-clock';
        
    } else {
        // Campaign has ended
        timeStatus = 'Ended';
        badgeClass = 'bg-secondary';
        iconClass = 'fa-stop';
    }
    
    // Update the display
    countdownElement.textContent = timeStatus;
    badge.className = `badge ${badgeClass}`;
    icon.className = `fas ${iconClass} me-1`;
    
    // If campaign has ended, stop the countdown
    if (now > endDate) {
        clearInterval(countdownIntervals[countdownIntervals.length - 1]);
    }
}

// Load pricing options from API
function loadPricingOptions() {
    fetch('../api/get_campaign_pricing.php')
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

// Display pricing options
function displayPricingOptions(pricingOptions) {
    const container = document.getElementById('pricingOptions');
    
    if (pricingOptions.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No pricing options available</div>';
        return;
    }

    let html = '';
    pricingOptions.forEach(option => {
        html += `
            <div class="pricing-option card mb-2 cursor-pointer" onclick="selectPricing(${option.pricing_id}, ${option.duration_days}, ${option.price}, '${option.description || ''}')">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${option.duration_days} Days</h6>
                            <small class="text-muted">${option.description || 'Campaign duration'}</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-primary">₱${parseFloat(option.price).toFixed(2)}</h5>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Select pricing option
function selectPricing(pricingId, durationDays, price, description) {
    selectedPricing = { pricingId, durationDays, price, description };
    
    // Update selected pricing display
    document.getElementById('selectedPricing').style.display = 'block';
    document.getElementById('pricingDetails').innerHTML = `
        <div class="d-flex justify-content-between">
            <span>Duration:</span>
            <strong>${durationDays} days</strong>
        </div>
        <div class="d-flex justify-content-between">
            <span>Price:</span>
            <strong class="text-primary">₱${parseFloat(price).toFixed(2)}</strong>
        </div>
        <div class="d-flex justify-content-between">
            <span>Description:</span>
            <small>${description || 'N/A'}</small>
        </div>
    `;

    // Set default start date to today if not already set
    const startDateInput = document.getElementById('startDate');
    if (!startDateInput.value) {
        const today = new Date();
        startDateInput.value = today.toISOString().split('T')[0];
    }

    // Calculate end date based on selected start date and duration
    updateEndDate();

    // Update pricing option styling
    document.querySelectorAll('.pricing-option').forEach(option => {
        option.classList.remove('border-primary', 'bg-primary-subtle');
    });
    event.currentTarget.classList.add('border-primary', 'bg-primary-subtle');
}

// Update end date based on start date and duration
function updateEndDate() {
    if (selectedPricing && document.getElementById('startDate').value) {
        const startDate = new Date(document.getElementById('startDate').value);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + selectedPricing.durationDays - 1);
        
        document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
        
        // Update display information
        document.getElementById('displayStartDate').textContent = startDate.toLocaleDateString();
        document.getElementById('displayEndDate').textContent = endDate.toLocaleDateString();
        document.getElementById('dateRangeInfo').style.display = 'block';
    }
}

// Submit campaign
function submitCampaign() {
    if (!selectedPricing) {
        showAlert('Please select a pricing option', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('title', document.getElementById('campaignTitle').value);
    formData.append('description', document.getElementById('campaignDescription').value);
    formData.append('start_date', document.getElementById('startDate').value);
    formData.append('end_date', document.getElementById('endDate').value);
    formData.append('pricing_id', selectedPricing.pricingId);
    formData.append('duration_days', selectedPricing.durationDays);
    formData.append('payment_method', document.getElementById('paymentMethod').value);

    // Add banner image
    if (bannerImageFile) {
        formData.append('banner_image', bannerImageFile);
    }

    // Add payment data
    const paymentMethod = document.getElementById('paymentMethod').value;
    if (paymentMethod === 'cash') {
        const cashAmount = document.getElementById('cashAmount').value;
        formData.append('payment_data[cash_amount]', cashAmount);
    } else if (paymentMethod === 'gcash') {
        if (receiptImageFile) {
            formData.append('payment_data[receipt_image]', receiptImageFile);
        }
    }

    // Show loading state
    const submitBtn = document.getElementById('submitCampaign');
    const spinner = submitBtn.querySelector('.spinner-border');
    submitBtn.disabled = true;
    spinner.style.display = 'inline-block';

    // Submit to API
    fetch('../api/process_campaign_submission.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('campaignModal')).hide();
            // Reload page to show new campaign
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error submitting campaign', 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        spinner.style.display = 'none';
    });
}

// Reset form
function resetForm() {
    document.getElementById('campaignForm').reset();
    document.getElementById('selectedPricing').style.display = 'none';
    document.getElementById('dateRangeInfo').style.display = 'none';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('receiptPreview').style.display = 'none';
    document.getElementById('cashChange').style.display = 'none';
    document.querySelectorAll('.payment-section').forEach(section => {
        section.style.display = 'none';
    });
    document.querySelectorAll('.pricing-option').forEach(option => {
        option.classList.remove('border-primary', 'bg-primary-subtle');
    });
    
    // Reset start date to today
    const today = new Date();
    document.getElementById('startDate').value = today.toISOString().split('T')[0];
    document.getElementById('endDate').value = '';
    
    selectedPricing = null;
    bannerImageFile = null;
    receiptImageFile = null;
}

// Show alert
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Campaign modal functions
function openCampaignModal(element) {
    const campaignId = element.getAttribute('data-campaign-id');
    const startDate = element.getAttribute('data-start-date');
    const endDate = element.getAttribute('data-end-date');

    startRealTimeTracker(campaignId, startDate, endDate);
    const modal = new bootstrap.Modal(document.getElementById('viewTrackerModal'));
    modal.show();
}

// Real-time tracker functions
function createDatePicker(startDate, endDate, mode, onChange) {
    const container = document.getElementById('datePickerContainer');
    container.innerHTML = '';

    if (mode === 'hourly') {
        const input = document.createElement('input');
        input.type = 'date';
        input.className = 'form-control form-control-sm d-inline-block w-auto ms-2';
        input.min = startDate;
        input.max = endDate;
        input.value = new Date().toISOString().slice(0, 10).localeCompare(startDate) < 0 ? startDate : (new Date().toISOString().slice(0, 10).localeCompare(endDate) > 0 ? endDate : new Date().toISOString().slice(0, 10));
        input.onchange = () => onChange(input.value);
        container.appendChild(input);
        return input;
    } else if (mode === 'daily') {
        const from = document.createElement('input');
        from.type = 'date';
        from.className = 'form-control form-control-sm d-inline-block w-auto ms-2';
        from.min = startDate;
        from.max = endDate;
        from.value = startDate;

        const to = document.createElement('input');
        to.type = 'date';
        to.className = 'form-control form-control-sm d-inline-block w-auto ms-2';
        to.min = startDate;
        to.max = endDate;
        to.value = endDate;

        from.onchange = () => {
            if (from.value > to.value) to.value = from.value;
            onChange(from.value, to.value);
        };
        to.onchange = () => {
            if (to.value < from.value) from.value = to.value;
            onChange(from.value, to.value);
        };

        container.appendChild(document.createTextNode('From: '));
        container.appendChild(from);
        container.appendChild(document.createTextNode(' To: '));
        container.appendChild(to);
        return [from, to];
    }
}

function fetchViewTrackerData(campaignId, mode = 'daily', date = null, from = null, to = null) {
    let url = `../backend/fetch_campaign_views.php?campaign_id=${campaignId}&mode=${mode}`;
    if (mode === 'hourly' && date) url += `&date=${date}`;
    if (mode === 'daily' && from && to) url += `&from=${from}&to=${to}`;
    return fetch(url).then(res => res.json());
}

function renderViewTrackerChart(data, mode, startDate, endDate) {
    const options = {
        series: [{
                name: 'Clicks',
                data: data.values,
                color: '#008FFB'
            },
            {
                name: 'Reach',
                data: data.reach_values,
                color: '#00E396'
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            animations: {
                enabled: true
            }
        },
        xaxis: {
            categories: data.labels,
            title: {
                text: mode === 'daily' ? 'Date' : 'Hour'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 4,
            curve: 'smooth'
        },
        tooltip: {
            y: {
                formatter: val => val + ' users'
            }
        },
        title: {
            text: 'Campaign Views',
            align: 'left'
        },
        grid: {
            borderColor: '#f1f1f1'
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    if (trackerChart) trackerChart.destroy();
    trackerChart = new ApexCharts(document.querySelector("#viewTrackerChart"), options);
    trackerChart.render();

    // Update info text
    if (mode === 'hourly') {
        document.getElementById('viewTrackerDateRange').textContent = `For ${data.date}`;
    } else {
        document.getElementById('viewTrackerDateRange').textContent = `From ${data.from} to ${data.to}`;
    }

    // Set text values
    document.getElementById('viewSum').textContent = data.view_sum ?? 0;
    document.getElementById('totalView').textContent = data.total_views ?? 0;
    document.getElementById('reach').textContent = data.reach ?? 0;
    document.getElementById('clicks').textContent = data.clicks ?? 0;
    document.getElementById('totalClicks').textContent = data.total_clicks ?? 0;

    // Calculate percentages safely
    const totalViews = data.total_views || 1;
    const viewPercent = Math.min(100, (data.view_sum / totalViews) * 100);
    const reachPercent = Math.min(100, (data.reach / totalViews) * 100);
    const clicksPercent = Math.min(100, (data.clicks / totalViews) * 100);
    const totalClicksPercent = Math.min(100, (data.total_clicks / totalViews) * 100);

    // Update progress bars
    document.getElementById('viewProgress').style.width = `${viewPercent}%`;
    document.getElementById('totalViewProgress').style.width = `100%`;
    document.getElementById('reachProgress').style.width = `${reachPercent}%`;
    document.getElementById('clicksProgress').style.width = `${clicksPercent}%`;
    document.getElementById('totalClicksProgress').style.width = `${totalClicksPercent}%`;
}

function startRealTimeTracker(campaignId, startDate, endDate) {
    let mode = document.getElementById('viewMode').value;
    let date = null, from = null, to = null;
    let picker = null;

    function update() {
        fetchViewTrackerData(campaignId, mode, date, from, to).then(data => {
            renderViewTrackerChart(data, mode, startDate, endDate);
        });
    }

    function onPickerChange(a, b) {
        if (mode === 'hourly') {
            date = a;
        } else {
            from = a;
            to = b;
        }
        update();
    }
    
    picker = createDatePicker(startDate, endDate, mode, onPickerChange);
    if (mode === 'hourly') {
        date = picker.value;
    } else {
        from = picker[0].value;
        to = picker[1].value;
    }
    update();
    if (trackerInterval) clearInterval(trackerInterval);
    trackerInterval = setInterval(update, 5000);
    
    document.getElementById('viewMode').onchange = function() {
        mode = this.value;
        picker = createDatePicker(startDate, endDate, mode, onPickerChange);
        if (mode === 'hourly') {
            date = picker.value;
            from = to = null;
        } else {
            from = picker[0].value;
            to = picker[1].value;
            date = null;
        }
        update();
    };
}

// Export functions for global access
window.openCampaignModal = openCampaignModal;
window.selectPricing = selectPricing;
window.updateEndDate = updateEndDate;
window.submitCampaign = submitCampaign;
window.resetForm = resetForm;
window.showAlert = showAlert;
window.initializeCountdowns = initializeCountdowns;

// Initialize chart if data is available
function initializeChart(data, labels) {
    if (data && labels) {
        const options = {
            series: [{
                name: 'Total Clicks',
                data: data
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 4,
                curve: 'smooth'
            },
            title: {
                text: 'Ad Clicks Over Time',
                align: 'left'
            },
            xaxis: {
                categories: labels
            },
            tooltip: {
                y: {
                    formatter: val => val + " clicks"
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        new ApexCharts(document.querySelector("#chart"), options).render();
    }
}

// Export chart initialization
window.initializeChart = initializeChart;
