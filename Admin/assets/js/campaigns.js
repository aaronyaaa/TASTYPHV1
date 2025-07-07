const panel = document.getElementById('userPanel');
const mainContent = document.getElementById('mainContent');
let currentlyOpenCampaign = null;
let currentFilter = 'all';

function closePanel() {
  panel.classList.remove('open');
  mainContent.classList.remove('shifted');
  currentlyOpenCampaign = null;
}

function filterCampaignByStatus(filter) {
  currentFilter = filter;
  const rows = document.querySelectorAll('.campaign-row');
  let visibleCount = 0;
  
  // Update filter button text
  const filterText = document.getElementById('currentFilterText');
  const filterMap = {
    'all': 'All Campaigns',
    'ongoing': 'Ongoing Campaigns',
    'expired': 'Expired Campaigns',
    'pending': 'Pending Campaigns',
    'approved': 'Approved Campaigns',
    'rejected': 'Rejected Campaigns'
  };
  filterText.textContent = filterMap[filter] || 'All Campaigns';
  
  // Update dropdown active state
  document.querySelectorAll('.dropdown-item').forEach(item => {
    item.classList.remove('active');
  });
  document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
  
  rows.forEach(row => {
    let shouldShow = false;
    
    switch (filter) {
      case 'all':
        shouldShow = true;
        break;
      case 'ongoing':
        shouldShow = row.dataset.ongoing === '1';
        break;
      case 'expired':
        shouldShow = row.dataset.expired === '1';
        break;
      case 'pending':
        shouldShow = row.dataset.status === 'pending';
        break;
      case 'approved':
        shouldShow = row.dataset.status === 'approved' && row.dataset.ongoing === '0' && row.dataset.expired === '0';
        break;
      case 'rejected':
        shouldShow = row.dataset.status === 'rejected';
        break;
      default:
        shouldShow = true;
    }
    
    if (shouldShow) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show message if no campaigns match filter
  const tableContainer = document.getElementById('campaignTable');
  let noResultsMsg = tableContainer.querySelector('.no-results-msg');
  
  if (visibleCount === 0) {
    if (!noResultsMsg) {
      noResultsMsg = document.createElement('div');
      noResultsMsg.className = 'no-results-msg text-center py-4';
      noResultsMsg.innerHTML = `
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No campaigns found</h5>
        <p class="text-muted">No campaigns match the selected filter.</p>
      `;
      tableContainer.appendChild(noResultsMsg);
    }
  } else if (noResultsMsg) {
    noResultsMsg.remove();
  }
}

function fetchCampaignDetails(campaignId) {
  fetch(`/tastyphv1/Admin/api/get_campaign.php?id=${campaignId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayCampaignDetails(data.campaign);
      } else {
        console.error('Error fetching campaign details:', data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

function displayCampaignDetails(campaign) {
  const content = document.getElementById('user-details-content');
  
  // Create enhanced campaign details with payment and pricing info
  let html = `
    <div class="campaign-details">
      <div class="text-center mb-3">
        <img src="/tastyphv1/${campaign.banner_image}" alt="Campaign Banner" class="img-fluid rounded" style="max-height: 150px; width: 100%; object-fit: cover;">
      </div>
      
      <h6 class="mb-2 fw-bold">${campaign.title}</h6>
      <p class="text-muted small mb-3">${campaign.description || 'No description provided'}</p>
      
      <div class="row mb-3">
        <div class="col-6">
          <small class="text-muted">User</small>
          <div class="fw-bold">${campaign.first_name} ${campaign.last_name}</div>
        </div>
        <div class="col-6">
          <small class="text-muted">Type</small>
          <div class="fw-bold">${campaign.user_type}</div>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-6">
          <small class="text-muted">Start Date</small>
          <div class="fw-bold">${formatDate(campaign.start_date)}</div>
        </div>
        <div class="col-6">
          <small class="text-muted">End Date</small>
          <div class="fw-bold">${formatDate(campaign.end_date)}</div>
        </div>
      </div>
      
      <hr>
      
      <h6 class="mb-3">Pricing Information</h6>
      <div class="pricing-info mb-3 p-3 bg-light rounded">
        <div class="row">
          <div class="col-6">
            <small class="text-muted">Price</small>
            <div class="fw-bold text-success">₱${parseFloat(campaign.price || 0).toFixed(2)}</div>
          </div>
          <div class="col-6">
            <small class="text-muted">Duration</small>
            <div class="fw-bold">${campaign.duration_days || 0} days</div>
          </div>
        </div>
        <div class="mt-2">
          <small class="text-muted">Plan</small>
          <div class="fw-bold">${campaign.pricing_description || 'No plan selected'}</div>
        </div>
      </div>
      
      <hr>
      
      <h6 class="mb-3">Payment Information</h6>
      <div class="payment-info mb-3 p-3 bg-light rounded">
        <div class="row">
          <div class="col-6">
            <small class="text-muted">Method</small>
            <div class="fw-bold">${campaign.payment_method ? campaign.payment_method.toUpperCase() : 'No payment'}</div>
          </div>
          <div class="col-6">
            <small class="text-muted">Amount</small>
            <div class="fw-bold text-success">₱${parseFloat(campaign.amount_spent || 0).toFixed(2)}</div>
          </div>
        </div>
        <div class="mt-2">
          <small class="text-muted">Status</small>
          <div class="fw-bold">${campaign.payment_status || 'Not paid'}</div>
        </div>
        ${campaign.payment_method === 'gcash' && campaign.receipt_image ? `
          <div class="mt-2">
            <small class="text-muted">Receipt</small>
            <div class="mt-1">
              <img src="/tastyphv1/${campaign.receipt_image}" alt="Payment Receipt" class="img-fluid rounded" style="max-height: 100px;">
            </div>
          </div>
        ` : ''}
      </div>
      
      <hr>
      
      <div class="d-grid gap-2">
        <button class="btn btn-success btn-sm" onclick="updateCampaignStatus(${campaign.campaign_id}, 'approved')">
          <i class="fas fa-check"></i> Approve Campaign
        </button>
        <button class="btn btn-danger btn-sm" onclick="updateCampaignStatus(${campaign.campaign_id}, 'rejected')">
          <i class="fas fa-times"></i> Reject Campaign
        </button>
      </div>
    </div>
  `;
  
  content.innerHTML = html;
}

function formatDate(dateString) {
  if (!dateString) return 'Not set';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function updateCampaignStatus(campaignId, status) {
  if (!confirm(`Are you sure you want to ${status} this campaign?`)) return;
  
  fetch('/tastyphv1/Admin/php_logic/update_campaign_status.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      campaign_id: campaignId,
      status: status
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showAlert(`Campaign ${status} successfully!`, 'success');
      loadCampaignTable();
      closePanel();
    } else {
      showAlert(`Error ${status} campaign: ${data.message}`, 'danger');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert(`Error ${status} campaign`, 'danger');
  });
}

function showAlert(message, type) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  const container = document.querySelector('.main-content');
  container.insertBefore(alertDiv, container.firstChild);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.remove();
    }
  }, 5000);
}

function attachCampaignRowEvents() {
  document.querySelectorAll('.campaign-row').forEach(row => {
    row.addEventListener('click', (e) => {
      e.preventDefault();
      if (e.target.closest('button')) return;
      const campaignId = row.dataset.id;
      if (currentlyOpenCampaign === campaignId) {
        closePanel();
      } else {
        currentlyOpenCampaign = campaignId;
        panel.classList.add('open');
        mainContent.classList.add('shifted');
        fetchCampaignDetails(campaignId);
      }
    });
  });
  attachCampaignActionEvents();
}

function attachCampaignActionEvents() {
  document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const campaignId = btn.dataset.id;
      updateCampaignStatus(campaignId, 'approved');
    });
  });

  document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const campaignId = btn.dataset.id;
      updateCampaignStatus(campaignId, 'rejected');
    });
  });
}

function loadCampaignTable() {
  fetch('/tastyphv1/Admin/includes/get_campaign_table.php')
    .then(res => res.text())
    .then(html => {
      document.getElementById('campaignTable').innerHTML = html;
      attachCampaignRowEvents();
      closePanel();
      // Reapply current filter after loading
      filterCampaignByStatus(currentFilter);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  loadCampaignTable();
  
  // Attach dropdown filter events
  document.querySelectorAll('.dropdown-item[data-filter]').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      const filter = item.dataset.filter;
      filterCampaignByStatus(filter);
    });
  });
}); 