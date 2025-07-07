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
$activeStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status = 'approved' AND start_date <= ? AND end_date >= ? ORDER BY start_date DESC LIMIT 4");
$activeStmt->execute([$userId, $today, $today]);
$activeCampaigns = $activeStmt->fetchAll(PDO::FETCH_ASSOC);


// Date Range Filter
$range = $_GET['range'] ?? '7days';
switch ($range) {
    case 'now':
        $dateCondition = "DATE(clicked_at) = CURDATE()";
        break;
    case '2weeks':
        $dateCondition = "clicked_at >= CURDATE() - INTERVAL 13 DAY";
        break;
    case 'month':
        $dateCondition = "MONTH(clicked_at) = MONTH(CURDATE()) AND YEAR(clicked_at) = YEAR(CURDATE())";
        break;
    case 'year':
        $dateCondition = "YEAR(clicked_at) = YEAR(CURDATE())";
        break;
    default:
        $dateCondition = "clicked_at >= CURDATE() - INTERVAL 6 DAY";
        break;
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

// Fetch historical campaigns (approved, rejected, expired)
$historicalStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status IN ('approved', 'rejected', 'expired') AND end_date < ? ORDER BY end_date DESC");
$historicalStmt->execute([$userId, $today]);
$historicalCampaigns = $historicalStmt->fetchAll(PDO::FETCH_ASSOC);


$filter = $_GET['filter'] ?? 'current';

switch ($filter) {
    case 'previous':
        // Fetch previous campaigns (expired or approved but ended)
        $campaignStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status IN ('approved', 'expired') AND end_date < ? ORDER BY end_date DESC");
        $campaignStmt->execute([$userId, $today]);
        $campaigns = $campaignStmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'pending':
        // Fetch campaigns that are still pending (not approved yet)
        $campaignStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status = 'pending' ORDER BY created_at DESC");
        $campaignStmt->execute([$userId]);
        $campaigns = $campaignStmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'current':
    default:
        // Fetch current campaigns (approved, not expired, started) AND upcoming campaigns (approved, not started yet)
        $campaignStmt = $pdo->prepare("SELECT * FROM campaign_requests WHERE user_type = 'seller' AND user_id = ? AND status = 'approved' AND end_date >= ? ORDER BY start_date ASC");
        $campaignStmt->execute([$userId, $today]);
        $campaigns = $campaignStmt->fetchAll(PDO::FETCH_ASSOC);
        break;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Campaign Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/campaign_form.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="../assets/js/campaign.js"></script>
    <style>
        .campaign-banner {
            max-width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
            cursor: pointer;
            transition: box-shadow 0.2s;
        }

        .campaign-banner:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        }

        .apexcharts-tooltip {
            z-index: 9999 !important;
        }

        /* Campaign Form Styles */
        .pricing-option {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .pricing-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pricing-option.border-primary {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05) !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .payment-section {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        #selectedPricing {
            border-left: 4px solid #0d6efd;
        }

        .form-control:read-only {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-xl {
                max-width: 95%;
                margin: 1rem;
            }
        }

        /* Countdown Timer Styles */
        .time-status {
            margin: 0.5rem 0;
        }

        .time-status .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .time-status .badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .time-status .badge.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }

        .time-status .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }

        .countdown-text {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .campaign-card {
            transition: all 0.3s ease;
        }

        .campaign-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .campaign-card:hover .time-status .badge {
            transform: scale(1.05);
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

        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <h5 class="mb-0">Campaigns</h5>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-filter me-1"></i> Filter Campaigns
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="?filter=current"><i class="fa fa-check-circle me-2"></i> Current Campaigns</a></li>
                    <li><a class="dropdown-item" href="?filter=previous"><i class="fa fa-clock me-2"></i> Previous Campaigns</a></li>
                    <li><a class="dropdown-item" href="?filter=pending"><i class="fa fa-hourglass me-2"></i> Pending Campaigns</a></li>
                </ul>
            </div>
        </div>

        <!-- Display Campaigns -->
        <?php if (!empty($campaigns)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
                <?php foreach ($campaigns as $campaign): 
                    // Calculate time remaining or time until start
                    $now = new DateTime();
                    $startDate = new DateTime($campaign['start_date']);
                    $endDate = new DateTime($campaign['end_date']);
                    $endDate->setTime(23, 59, 59); // Set to end of day
                    
                    $timeStatus = '';
                    $timeClass = '';
                    $isRunning = false;
                    $isStartingSoon = false;
                    
                    if ($campaign['status'] === 'approved') {
                        if ($now >= $startDate && $now <= $endDate) {
                            // Campaign is running
                            $isRunning = true;
                            $timeRemaining = $now->diff($endDate);
                            
                            if ($timeRemaining->days > 0) {
                                $timeStatus = $timeRemaining->days . ' day' . ($timeRemaining->days > 1 ? 's' : '') . ' left';
                            } elseif ($timeRemaining->h > 0) {
                                $timeStatus = $timeRemaining->h . ' hour' . ($timeRemaining->h > 1 ? 's' : '') . ' left';
                            } elseif ($timeRemaining->i > 0) {
                                $timeStatus = $timeRemaining->i . ' minute' . ($timeRemaining->i > 1 ? 's' : '') . ' left';
                            } else {
                                $timeStatus = $timeRemaining->s . ' second' . ($timeRemaining->s > 1 ? 's' : '') . ' left';
                            }
                            $timeClass = 'text-success';
                        } elseif ($now < $startDate) {
                            // Campaign hasn't started yet
                            $timeUntilStart = $now->diff($startDate);
                            
                            if ($timeUntilStart->days > 0) {
                                $timeStatus = 'Starts in ' . $timeUntilStart->days . ' day' . ($timeUntilStart->days > 1 ? 's' : '');
                            } elseif ($timeUntilStart->h > 0) {
                                $timeStatus = 'Starts in ' . $timeUntilStart->h . ' hour' . ($timeUntilStart->h > 1 ? 's' : '');
                            } elseif ($timeUntilStart->i > 0) {
                                $timeStatus = 'Starts in ' . $timeUntilStart->i . ' minute' . ($timeUntilStart->i > 1 ? 's' : '');
                            } else {
                                $timeStatus = 'Starts in ' . $timeUntilStart->s . ' second' . ($timeUntilStart->s > 1 ? 's' : '');
                            }
                            $timeClass = 'text-warning';
                            $isStartingSoon = true;
                        } else {
                            // Campaign has ended
                            $timeStatus = 'Ended';
                            $timeClass = 'text-muted';
                        }
                    }
                ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 text-center campaign-card" 
                             data-campaign-id="<?= $campaign['campaign_id'] ?>"
                             data-start-date="<?= $campaign['start_date'] ?>"
                             data-end-date="<?= $campaign['end_date'] ?>"
                             data-status="<?= $campaign['status'] ?>">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    <?= htmlspecialchars($campaign['title']) ?>
                                    <span class="badge <?= $campaign['status'] === 'expired' ? 'bg-danger' : ($campaign['status'] === 'rejected' ? 'bg-warning' : 'bg-success') ?> ms-2"><?= ucfirst($campaign['status']) ?></span>
                                </h5>
                                <img src="../<?= htmlspecialchars($campaign['banner_image']) ?>" alt="Campaign Banner" class="campaign-banner mb-3" data-campaign-id="<?= $campaign['campaign_id'] ?>" onclick="openCampaignModal(this)">
                                
                                <!-- Time Status -->
                                <?php if ($campaign['status'] === 'approved' && $timeStatus): ?>
                                    <div class="time-status mb-2">
                                        <span class="badge <?= $isRunning ? 'bg-success' : ($isStartingSoon ? 'bg-warning' : 'bg-secondary') ?>">
                                            <i class="fas <?= $isRunning ? 'fa-play' : ($isStartingSoon ? 'fa-clock' : 'fa-stop') ?> me-1"></i>
                                            <span class="countdown-text"><?= $timeStatus ?></span>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="text-muted mb-1">
                                    From <?= htmlspecialchars($campaign['start_date']) ?> to <?= htmlspecialchars($campaign['end_date']) ?>
                                </p>
                                <?php if (!empty($campaign['description'])): ?>
                                    <p class="small"><?= htmlspecialchars($campaign['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No campaigns found.</div>
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
                    <div class="mt-4 p-3 bg-light rounded border">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>View</strong>
                                <span id="viewSum">0</span>
                            </div>
                            <div class="progress">
                                <div id="viewProgress" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Total View</strong>
                                <span id="totalView">0</span>
                            </div>
                            <div class="progress">
                                <div id="totalViewProgress" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Reach</strong>
                                <span id="reach">0</span>
                            </div>
                            <div class="progress">
                                <div id="reachProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Clicks</strong>
                                <span id="clicks">0</span>
                            </div>
                            <div class="progress">
                                <div id="clicksProgress" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Clicks</strong>
                                <span id="totalClicks">0</span>
                            </div>
                            <div class="progress">
                                <div id="totalClicksProgress" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade campaign-form-modal" id="campaignModal" tabindex="-1" aria-labelledby="campaignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="campaignForm" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="campaignModalLabel">Submit New Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Campaign Details -->
                            <div class="mb-3">
                                <label class="form-label">Campaign Title *</label>
                                <input type="text" name="title" id="campaignTitle" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date *</label>
                                        <input type="date" name="start_date" id="startDate" class="form-control" required min="<?= date('Y-m-d') ?>">
                                        <small class="text-muted">Choose when you want your campaign to start</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">End Date *</label>
                                        <input type="date" name="end_date" id="endDate" class="form-control" required readonly>
                                        <small class="text-muted">Automatically calculated based on duration</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Banner Image (1200x400) *</label>
                                <input type="file" name="banner_image" id="bannerImage" class="form-control" accept="image/*" required>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description (optional)</label>
                                <textarea name="description" id="campaignDescription" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Pricing Options -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Campaign Pricing</h6>
                                </div>
                                <div class="card-body">
                                    <div id="pricingOptions" class="mb-3">
                                        <div class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <small class="text-muted">Loading pricing options...</small>
                                        </div>
                                    </div>
                                    
                                    <div id="selectedPricing" class="alert alert-info" style="display: none;">
                                        <h6 class="mb-2">Selected Plan</h6>
                                        <div id="pricingDetails"></div>
                                        <div id="dateRangeInfo" class="mt-2 small text-muted" style="display: none;">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Campaign will run from <span id="displayStartDate"></span> to <span id="displayEndDate"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Section -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Payment Method</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method *</label>
                                        <select id="paymentMethod" class="form-select" required>
                                            <option value="">Select payment method</option>
                                            <option value="cash">Cash</option>
                                            <option value="gcash">GCash</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Cash Payment -->
                                    <div id="cashPayment" class="payment-section" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Cash Amount *</label>
                                            <input type="number" id="cashAmount" class="form-control" min="0" step="0.01" placeholder="Enter amount">
                                        </div>
                                        <div id="cashChange" class="alert alert-success" style="display: none;">
                                            <strong>Change:</strong> ₱<span id="changeAmount">0.00</span>
                                        </div>
                                    </div>
                                    
                                    <!-- GCash Payment -->
                                    <div id="gcashPayment" class="payment-section" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">GCash Receipt *</label>
                                            <input type="file" id="gcashReceipt" class="form-control" accept="image/*">
                                            <small class="text-muted">Upload screenshot of your payment receipt</small>
                                        </div>
                                        <div id="receiptPreview" class="mt-2" style="display: none;">
                                            <img id="receiptImg" src="" alt="Receipt" style="max-width: 100%; max-height: 150px; border-radius: 6px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitCampaign">
                        <span class="spinner-border spinner-border-sm me-2" role="status" style="display: none;"></span>
                        Submit Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize chart with PHP data
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = <?= json_encode($data) ?>;
            const chartLabels = <?= json_encode($labels) ?>;
            initializeChart(chartData, chartLabels);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>