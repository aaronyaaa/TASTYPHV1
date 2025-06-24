const panel = document.getElementById('userPanel');
const mainContent = document.getElementById('mainContent');
let currentlyOpenCampaign = null;

function closePanel() {
  panel.classList.remove('open');
  mainContent.classList.remove('shifted');
  currentlyOpenCampaign = null;
}

function filterCampaignByStatus(status) {
  document.querySelectorAll('.campaign-row').forEach(row => {
    const badge = row.querySelector('.status-badge');
    const currentStatus = badge.textContent.trim().toLowerCase();
    row.style.display = (status === 'all' || status === currentStatus) ? '' : 'none';
  });
  closePanel();
}

function fetchCampaignDetails(campaignId) {
  fetch(`/tastyphv1/Admin/api/get_campaign.php?id=${campaignId}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('user-details-content').innerHTML = `
        <div class="text-center mb-3">
          <img src="/tastyphv1/${data.banner_image}" class="img-fluid mb-2" style="max-width: 300px; border-radius: 10px;">
          <h5>${data.title}</h5>
          <span class="badge bg-${data.status === 'approved' ? 'success' : (data.status === 'rejected' ? 'danger' : 'secondary')}">${data.status ?? 'Pending'}</span>
        </div>
        <p><strong>Description:</strong> ${data.description || ''}</p>
        <p><strong>Start Date:</strong> ${data.start_date}</p>
        <p><strong>End Date:</strong> ${data.end_date}</p>
        <p><strong>User Type:</strong> ${data.user_type}</p>
        <p><strong>User ID:</strong> ${data.user_id}</p>
        <p><strong>Fee Paid:</strong> ₱${data.fee_paid}</p>
        <p><strong>Payment Status:</strong> ${data.payment_status}</p>
        <div class="d-flex gap-2 mt-3">
          <button class='btn btn-success btn-sm approve-btn' data-id='${data.campaign_id}'>Approve</button>
          <button class='btn btn-danger btn-sm reject-btn' data-id='${data.campaign_id}'>Reject</button>
        </div>
      `;
      attachCampaignActionEvents();
    });
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
  document.querySelectorAll('.approve-btn, .reject-btn').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const campaignId = button.dataset.id;
      const status = button.classList.contains('approve-btn') ? 'approved' : 'rejected';
      fetch('/tastyphv1/Admin/php_logic/update_campaign_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `campaignId=${campaignId}&status=${status}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          closePanel();
          location.reload();
        } else {
          alert(data.error || 'Failed to update status');
        }
      });
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
      filterCampaignByStatus('all');
    });
}

document.addEventListener('DOMContentLoaded', () => {
  loadCampaignTable();
}); 