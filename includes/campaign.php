<?php
require_once("../database/session.php");
require_once("../database/db_connect.php");

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) die("Unauthorized");

$stmt = $pdo->prepare("SELECT seller_id FROM seller_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$seller) die("Seller profile not found.");

$errors = [];
$success = "";

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $bannerPath = '';

    if (empty($title) || empty($startDate) || empty($endDate)) {
        $errors[] = "All fields marked with * are required.";
    }

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/campaigns/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = uniqid('banner_', true) . '.' . pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
            $bannerPath = 'uploads/campaigns/' . $filename;
        } else {
            $errors[] = "Failed to upload banner image.";
        }
    } else {
        $errors[] = "Banner image is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO campaign_requests 
            (user_type, user_id, title, description, banner_image, start_date, end_date)
            VALUES ('seller', ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $description, $bannerPath, $startDate, $endDate]);
        $success = "Campaign submitted!";
    }
}

// Fetch active campaign (approved, not expired, started)
$today = date('Y-m-d');
$activeStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status = 'approved' AND start_date <= ? AND end_date >= ? ORDER BY start_date DESC LIMIT 1");
$activeStmt->execute([$userId, $today, $today]);
$activeCampaign = $activeStmt->fetch(PDO::FETCH_ASSOC);

// Date Range Filter
$range = $_GET['range'] ?? '7days';
switch ($range) {
    case 'now':    $dateCondition = "DATE(clicked_at) = CURDATE()"; break;
    case '2weeks': $dateCondition = "clicked_at >= CURDATE() - INTERVAL 13 DAY"; break;
    case 'month':  $dateCondition = "MONTH(clicked_at) = MONTH(CURDATE()) AND YEAR(clicked_at) = YEAR(CURDATE())"; break;
    case 'year':   $dateCondition = "YEAR(clicked_at) = YEAR(CURDATE())"; break;
    default:       $dateCondition = "clicked_at >= CURDATE() - INTERVAL 6 DAY"; break;
}

// Fetch Click Data
$clickStmt = $pdo->prepare("
    SELECT DATE(clicked_at) AS click_date, COUNT(*) AS total_clicks
    FROM campaign_clicks
    WHERE campaign_id IN (
        SELECT campaign_id FROM campaign_requests 
        WHERE user_type = 'seller' AND user_id = (
            SELECT user_id FROM seller_applications WHERE seller_id = ?
        )
    )
    AND $dateCondition
    GROUP BY DATE(clicked_at)
");
$clickStmt->execute([$seller['seller_id']]);
$clickData = $clickStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Labels and Data
$labels = [];
$data = [];
if ($range === 'now') {
    $labels[] = date('M d');
    $data[] = (int)($clickData[date('Y-m-d')] ?? 0);
} elseif ($range === 'month') {
    for ($i = 1; $i <= date('t'); $i++) {
        $d = date('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
        $labels[] = date('M d', strtotime($d));
        $data[] = (int)($clickData[$d] ?? 0);
    }
} elseif ($range === 'year') {
    for ($i = 1; $i <= 12; $i++) {
        $m = date('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01';
        $labels[] = date('M', strtotime($m));
        $monthClicks = 0;
        foreach ($clickData as $date => $count) {
            if (strpos($date, date('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)) === 0) {
                $monthClicks += $count;
            }
        }
        $data[] = $monthClicks;
    }
} else {
    $days = $range === '2weeks' ? 13 : 6;
    for ($i = $days; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('M d', strtotime($d));
        $data[] = (int)($clickData[$d] ?? 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campaign Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .campaign-banner {
            max-width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            cursor: pointer;
            transition: box-shadow 0.2s;
        }
        .campaign-banner:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .apexcharts-tooltip {
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
<?php include '../includes/nav/navbar_router.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Campaign Dashboard</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#campaignModal">
            <i class="fa fa-paper-plane me-1"></i> Send Campaign
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if ($activeCampaign): ?>
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body text-center">
                <h4 class="mb-3">Active Campaign</h4>
                <img src="../<?= htmlspecialchars($activeCampaign['banner_image']) ?>" alt="Active Campaign Banner" class="campaign-banner mb-3" id="activeCampaignBanner" data-campaign-id="<?= $activeCampaign['campaign_id'] ?>">
                <h5><?= htmlspecialchars($activeCampaign['title']) ?></h5>
                <p class="mb-1 text-muted">From <?= htmlspecialchars($activeCampaign['start_date']) ?> to <?= htmlspecialchars($activeCampaign['end_date']) ?></p>
                <?php if (!empty($activeCampaign['description'])): ?>
                    <p><?= htmlspecialchars($activeCampaign['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No active campaign at the moment.</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h5 class="mb-0">Campaign Clicks</h5>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa fa-calendar-alt me-1"></i> <?= ucfirst($range) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="?range=now"><i class="fa fa-clock me-2"></i>Today</a></li>
                <li><a class="dropdown-item" href="?range=7days"><i class="fa fa-calendar-week me-2"></i>Last 7 Days</a></li>
                <li><a class="dropdown-item" href="?range=2weeks"><i class="fa fa-calendar me-2"></i>Last 2 Weeks</a></li>
                <li><a class="dropdown-item" href="?range=month"><i class="fa fa-calendar-alt me-2"></i>This Month</a></li>
                <li><a class="dropdown-item" href="?range=year"><i class="fa fa-calendar-check me-2"></i>This Year</a></li>
            </ul>
        </div>
    </div>

    <div id="chart"></div>
</div>

<!-- Modal: Real-Time View Tracker -->
<div class="modal fade" id="viewTrackerModal" tabindex="-1" aria-labelledby="viewTrackerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewTrackerModalLabel">Campaign View Tracker</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="fw-semibold">View Mode:</span>
                <select id="viewMode" class="form-select form-select-sm d-inline-block w-auto ms-2">
                    <option value="daily">Daily</option>
                    <option value="hourly">Hourly</option>
                </select>
            </div>
            <div id="datePickerContainer"></div>
            <div id="viewTrackerDateRange" class="text-muted small"></div>
        </div>
        <div id="viewTrackerChart" style="height: 350px;"></div>
        <div class="mt-4 p-3 bg-light rounded border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div><strong>View:</strong> <span id="viewSum">0</span></div>
            <div><strong>Total View:</strong> <span id="totalView">0</span></div>
            <div><strong>Reach:</strong> <span id="reach">0</span></div>
            <div><strong>Clicks:</strong> <span id="clicks">0</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-labelledby="campaignModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="campaignModalLabel">Submit New Campaign</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
              <label class="form-label">Campaign Title *</label>
              <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Start Date *</label>
              <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">End Date *</label>
              <input type="date" name="end_date" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Banner Image (1200x400) *</label>
              <input type="file" name="banner_image" class="form-control" accept="image/*" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Description (optional)</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit Campaign</button>
      </div>
    </form>
  </div>
</div>

<script>
    const options = {
        series: [{ name: 'Total Clicks', data: <?= json_encode($data) ?> }],
        chart: { height: 350, type: 'line', zoom: { enabled: false } },
        dataLabels: { enabled: false },
        stroke: { width: 4, curve: 'smooth' },
        title: { text: 'Ad Clicks Over Time', align: 'left' },
        xaxis: { categories: <?= json_encode($labels) ?> },
        tooltip: {
            y: { formatter: val => val + " clicks" }
        },
        grid: { borderColor: '#f1f1f1' }
    };
    new ApexCharts(document.querySelector("#chart"), options).render();

let trackerChart = null;
let trackerInterval = null;

function createDatePicker(startDate, endDate, mode, onChange) {
    const container = document.getElementById('datePickerContainer');
    container.innerHTML = '';
    if (mode === 'hourly') {
        const input = document.createElement('input');
        input.type = 'date';
        input.className = 'form-control form-control-sm d-inline-block w-auto ms-2';
        input.min = startDate;
        input.max = endDate;
        input.value = new Date().toISOString().slice(0,10).localeCompare(startDate) < 0 ? startDate : (new Date().toISOString().slice(0,10).localeCompare(endDate) > 0 ? endDate : new Date().toISOString().slice(0,10));
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
        from.onchange = () => { if (from.value > to.value) to.value = from.value; onChange(from.value, to.value); };
        to.onchange = () => { if (to.value < from.value) from.value = to.value; onChange(from.value, to.value); };
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
        series: [
            { name: 'Clicks', data: data.values, color: '#008FFB' },
            { name: 'Reach', data: data.reach_values, color: '#00E396' }
        ],
        chart: { type: 'line', height: 350, animations: { enabled: true } },
        xaxis: { categories: data.labels, title: { text: mode === 'daily' ? 'Date' : 'Hour' } },
        dataLabels: { enabled: false },
        stroke: { width: 4, curve: 'smooth' },
        tooltip: { y: { formatter: val => val + ' users' } },
        title: { text: 'Campaign Views', align: 'left' },
        grid: { borderColor: '#f1f1f1' },
        legend: { show: true, position: 'top', horizontalAlign: 'right' }
    };
    if (trackerChart) trackerChart.destroy();
    trackerChart = new ApexCharts(document.querySelector("#viewTrackerChart"), options);
    trackerChart.render();
    if (mode === 'hourly') {
        document.getElementById('viewTrackerDateRange').textContent = `For ${data.date}`;
    } else {
        document.getElementById('viewTrackerDateRange').textContent = `From ${data.from} to ${data.to}`;
    }
    document.getElementById('viewSum').textContent = data.view_sum ?? 0;
    document.getElementById('totalView').textContent = data.total_views ?? 0;
    document.getElementById('reach').textContent = data.reach ?? 0;
    document.getElementById('clicks').textContent = data.clicks ?? 0;
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
        if (mode === 'hourly') { date = a; } else { from = a; to = b; }
        update();
    }
    picker = createDatePicker(startDate, endDate, mode, onPickerChange);
    if (mode === 'hourly') { date = picker.value; }
    else { from = picker[0].value; to = picker[1].value; }
    update();
    if (trackerInterval) clearInterval(trackerInterval);
    trackerInterval = setInterval(update, 5000);
    document.getElementById('viewMode').onchange = function() {
        mode = this.value;
        picker = createDatePicker(startDate, endDate, mode, onPickerChange);
        if (mode === 'hourly') { date = picker.value; from = to = null; }
        else { from = picker[0].value; to = picker[1].value; date = null; }
        update();
    };
}

<?php if ($activeCampaign): ?>
// Show modal and start tracker on banner click
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('activeCampaignBanner').addEventListener('click', function() {
            const campaignId = this.getAttribute('data-campaign-id');
            const startDate = "<?= $activeCampaign['start_date'] ?>";
            const endDate = "<?= $activeCampaign['end_date'] ?>";
            startRealTimeTracker(campaignId, startDate, endDate);
            const modal = new bootstrap.Modal(document.getElementById('viewTrackerModal'));
            modal.show();
        });
    });
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
