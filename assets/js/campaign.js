document.addEventListener("DOMContentLoaded", async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const range = urlParams.get("range") || "7days";
  document.getElementById("rangeLabel").textContent = range;

  const res = await fetch(`campaign.php?range=${range}`);
  const data = await res.json();

  const chartOptions = {
    series: [{ name: "Total Clicks", data: data.data }],
    chart: { height: 350, type: "line", zoom: { enabled: false } },
    dataLabels: { enabled: false },
    stroke: { width: 4, curve: "smooth" },
    title: { text: "Ad Clicks Over Time", align: "left" },
    xaxis: { categories: data.labels },
    tooltip: { y: { formatter: (val) => val + " clicks" } },
    grid: { borderColor: "#f1f1f1" },
  };
  new ApexCharts(document.querySelector("#chart"), chartOptions).render();

  if (data.success) {
    document.getElementById(
      "campaignAlerts"
    ).innerHTML = `<div class="alert alert-success">${data.success}</div>`;
  } else if (data.errors.length > 0) {
    document.getElementById("campaignAlerts").innerHTML =
      `<div class="alert alert-danger"><ul>` +
      data.errors.map((err) => `<li>${err}</li>`).join("") +
      `</ul></div>`;
  }

  const campaign = data.activeCampaign;
  if (campaign) {
    document.getElementById("activeCampaignSection").innerHTML = `
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body text-center">
                    <h4 class="mb-3">Active Campaign</h4>
                    <img src="../${
                      campaign.banner_image
                    }" alt="Banner" class="campaign-banner mb-3" id="activeCampaignBanner" data-campaign-id="${
      campaign.campaign_id
    }">
                    <h5>${campaign.title}</h5>
                    <p class="mb-1 text-muted">From ${campaign.start_date} to ${
      campaign.end_date
    }</p>
                    ${
                      campaign.description
                        ? `<p>${campaign.description}</p>`
                        : ""
                    }
                </div>
            </div>
        `;
  } else {
    document.getElementById(
      "activeCampaignSection"
    ).innerHTML = `<div class="alert alert-info">No active campaign at the moment.</div>`;
  }

  document
    .getElementById("activeCampaignBanner")
    ?.addEventListener("click", () => {
      const campaignId = campaign.campaign_id;
      const startDate = campaign.start_date;
      const endDate = campaign.end_date;
      startRealTimeTracker(campaignId, startDate, endDate);
      const modal = new bootstrap.Modal(
        document.getElementById("viewTrackerModal")
      );
      modal.show();
    });
});
// Function to open the campaign details modal when clicking the "View Details" button
function viewCampaignDetails(campaignId) {
  fetch(`../backend/fetch_campaign_details.php?campaign_id=${campaignId}`)
    .then((response) => response.json())
    .then((data) => {
      // Update modal content with campaign details
      const modalBody = document.getElementById("campaignDetailsBody");
      modalBody.innerHTML = `
                <h5>${data.title}</h5>
                <p><strong>Description:</strong> ${
                  data.description || "N/A"
                }</p>
                <p><strong>Start Date:</strong> ${data.start_date}</p>
                <p><strong>End Date:</strong> ${data.end_date}</p>
                <p><strong>Status:</strong> ${data.status}</p>
                <p><strong>Total Clicks:</strong> ${data.total_clicks}</p>
                <p><strong>Total Reach:</strong> ${data.total_reach}</p>
                <h6>Click Data:</h6>
                <ul>
                    ${data.clicks
                      .map(
                        (click) => `
                        <li>${click.date}: ${click.count} clicks</li>
                    `
                      )
                      .join("")}
                </ul>
                <h6>Reach Data:</h6>
                <ul>
                    ${data.reach
                      .map(
                        (reach) => `
                        <li>${reach.date}: ${reach.count} reach</li>
                    `
                      )
                      .join("")}
                </ul>
            `;

      // Initialize and show the modal
      const modal = new bootstrap.Modal(
        document.getElementById("campaignDetailsModal")
      );
      modal.show();
    })
    .catch((error) => console.error("Error fetching campaign details:", error));
}

// Function to open the campaign view tracker modal
// Function to open the campaign view tracker modal
function openCampaignModal(element) {
  const campaignId = element.getAttribute("data-campaign-id");
  const startDate = element.getAttribute("data-start-date");
  const endDate = element.getAttribute("data-end-date");
  startRealTimeTracker(campaignId, startDate, endDate);
  const modal = new bootstrap.Modal(
    document.getElementById("viewTrackerModal")
  );
  modal.show();
}

// Real-Time Tracker functionality for campaign clicks and reach
let trackerChart = null;
let trackerInterval = null;

function createDatePicker(startDate, endDate, mode, onChange) {
  const container = document.getElementById("datePickerContainer");
  container.innerHTML = "";
  if (mode === "hourly") {
    const input = document.createElement("input");
    input.type = "date";
    input.className = "form-control form-control-sm d-inline-block w-auto ms-2";
    input.min = startDate;
    input.max = endDate;
    input.value =
      new Date().toISOString().slice(0, 10).localeCompare(startDate) < 0
        ? startDate
        : new Date().toISOString().slice(0, 10).localeCompare(endDate) > 0
        ? endDate
        : new Date().toISOString().slice(0, 10);
    input.onchange = () => onChange(input.value);
    container.appendChild(input);
    return input;
  } else if (mode === "daily") {
    const from = document.createElement("input");
    from.type = "date";
    from.className = "form-control form-control-sm d-inline-block w-auto ms-2";
    from.min = startDate;
    from.max = endDate;
    from.value = startDate;
    const to = document.createElement("input");
    to.type = "date";
    to.className = "form-control form-control-sm d-inline-block w-auto ms-2";
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
    container.appendChild(document.createTextNode("From: "));
    container.appendChild(from);
    container.appendChild(document.createTextNode(" To: "));
    container.appendChild(to);
    return [from, to];
  }
}

function fetchViewTrackerData(
  campaignId,
  mode = "daily",
  date = null,
  from = null,
  to = null
) {
  let url = `../backend/fetch_campaign_views.php?campaign_id=${campaignId}&mode=${mode}`;
  if (mode === "hourly" && date) url += `&date=${date}`;
  if (mode === "daily" && from && to) url += `&from=${from}&to=${to}`;
  return fetch(url).then((res) => res.json());
}

function renderViewTrackerChart(data, mode, startDate, endDate) {
  const options = {
    series: [
      {
        name: "Clicks",
        data: data.values,
        color: "#008FFB",
      },
      {
        name: "Reach",
        data: data.reach_values,
        color: "#00E396",
      },
    ],
    chart: {
      type: "line",
      height: 350,
      animations: {
        enabled: true,
      },
    },
    xaxis: {
      categories: data.labels,
      title: {
        text: mode === "daily" ? "Date" : "Hour",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      width: 4,
      curve: "smooth",
    },
    tooltip: {
      y: {
        formatter: (val) => val + " users",
      },
    },
    title: {
      text: "Campaign Views",
      align: "left",
    },
    grid: {
      borderColor: "#f1f1f1",
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "right",
    },
  };

  if (trackerChart) trackerChart.destroy();
  trackerChart = new ApexCharts(
    document.querySelector("#viewTrackerChart"),
    options
  );
  trackerChart.render();

  // Update info text
  if (mode === "hourly") {
    document.getElementById(
      "viewTrackerDateRange"
    ).textContent = `For ${data.date}`;
  } else {
    document.getElementById(
      "viewTrackerDateRange"
    ).textContent = `From ${data.from} to ${data.to}`;
  }

  // Set text values
  document.getElementById("viewSum").textContent = data.view_sum ?? 0;
  document.getElementById("totalView").textContent = data.total_views ?? 0;
  document.getElementById("reach").textContent = data.reach ?? 0;
  document.getElementById("clicks").textContent = data.clicks ?? 0;
  document.getElementById("totalClicks").textContent = data.total_clicks ?? 0;

  // Calculate percentages safely
  const totalViews = data.total_views || 1; // prevent divide-by-zero
  const viewPercent = Math.min(100, (data.view_sum / totalViews) * 100);
  const reachPercent = Math.min(100, (data.reach / totalViews) * 100);
  const clicksPercent = Math.min(100, (data.clicks / totalViews) * 100);
  const totalClicksPercent = Math.min(
    100,
    (data.total_clicks / totalViews) * 100
  );

  // Update progress bars
  document.getElementById("viewProgress").style.width = `${viewPercent}%`;
  document.getElementById("totalViewProgress").style.width = `100%`;
  document.getElementById("reachProgress").style.width = `${reachPercent}%`;
  document.getElementById("clicksProgress").style.width = `${clicksPercent}%`;
  document.getElementById(
    "totalClicksProgress"
  ).style.width = `${totalClicksPercent}%`;
}

function startRealTimeTracker(campaignId, startDate, endDate) {
  let mode = document.getElementById("viewMode").value;
  let date = null,
    from = null,
    to = null;
  let picker = null;

  function update() {
    fetchViewTrackerData(campaignId, mode, date, from, to).then((data) => {
      renderViewTrackerChart(data, mode, startDate, endDate);
    });
  }

  function onPickerChange(a, b) {
    if (mode === "hourly") {
      date = a;
    } else {
      from = a;
      to = b;
    }
    update();
  }
  picker = createDatePicker(startDate, endDate, mode, onPickerChange);
  if (mode === "hourly") {
    date = picker.value;
  } else {
    from = picker[0].value;
    to = picker[1].value;
  }
  update();
  if (trackerInterval) clearInterval(trackerInterval);
  trackerInterval = setInterval(update, 5000);
  document.getElementById("viewMode").onchange = function () {
    mode = this.value;
    picker = createDatePicker(startDate, endDate, mode, onPickerChange);
    if (mode === "hourly") {
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
