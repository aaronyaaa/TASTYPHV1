document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const range = urlParams.get('range') || '7days';
    document.getElementById('rangeLabel').textContent = range;

    const res = await fetch(`campaign.php?range=${range}`);
    const data = await res.json();

    const chartOptions = {
        series: [{ name: 'Total Clicks', data: data.data }],
        chart: { height: 350, type: 'line', zoom: { enabled: false } },
        dataLabels: { enabled: false },
        stroke: { width: 4, curve: 'smooth' },
        title: { text: 'Ad Clicks Over Time', align: 'left' },
        xaxis: { categories: data.labels },
        tooltip: { y: { formatter: val => val + " clicks" } },
        grid: { borderColor: '#f1f1f1' }
    };
    new ApexCharts(document.querySelector("#chart"), chartOptions).render();

    if (data.success) {
        document.getElementById('campaignAlerts').innerHTML = `<div class="alert alert-success">${data.success}</div>`;
    } else if (data.errors.length > 0) {
        document.getElementById('campaignAlerts').innerHTML =
            `<div class="alert alert-danger"><ul>` +
            data.errors.map(err => `<li>${err}</li>`).join('') +
            `</ul></div>`;
    }

    const campaign = data.activeCampaign;
    if (campaign) {
        document.getElementById('activeCampaignSection').innerHTML = `
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body text-center">
                    <h4 class="mb-3">Active Campaign</h4>
                    <img src="../${campaign.banner_image}" alt="Banner" class="campaign-banner mb-3" id="activeCampaignBanner" data-campaign-id="${campaign.campaign_id}">
                    <h5>${campaign.title}</h5>
                    <p class="mb-1 text-muted">From ${campaign.start_date} to ${campaign.end_date}</p>
                    ${campaign.description ? `<p>${campaign.description}</p>` : ''}
                </div>
            </div>
        `;
    } else {
        document.getElementById('activeCampaignSection').innerHTML = `<div class="alert alert-info">No active campaign at the moment.</div>`;
    }

    document.getElementById('activeCampaignBanner')?.addEventListener('click', () => {
        const campaignId = campaign.campaign_id;
        const startDate = campaign.start_date;
        const endDate = campaign.end_date;
        startRealTimeTracker(campaignId, startDate, endDate);
        const modal = new bootstrap.Modal(document.getElementById('viewTrackerModal'));
        modal.show();
    });
});
